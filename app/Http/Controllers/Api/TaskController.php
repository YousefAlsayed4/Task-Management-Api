<?php

namespace App\Http\Controllers\Api;

use App\Enums\Http;
use App\Enums\TaskStatus;
use App\Helpers\APIResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\StoreTaskRequest;
use App\Http\Requests\Tasks\UpdateTaskRequest;
use App\Http\Requests\Tasks\UpdateTaskStatusRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Repositories\TaskRepositoryInterface;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
        public function __construct(private TaskRepositoryInterface $taskRepo) {}
        
        public function index()
        {
        try{
            $user = auth()->user();
            $filters = request()->all();
            if ($user->role?->name === 'user') {
                $filters['assigned_user_id'] = $user->id;
            }
            $tasks = $this->taskRepo->list($filters);
            if ($tasks->isEmpty()) {
                return new APIResponse(
                    status: 'success',
                    code: Http::OK,
                    message: 'No tasks found',
                    body: []
                );
            }
            return new APIResponse(
                status: 'success',
                code: Http::OK,
                message: 'Tasks retrieved successfully',
                body: TaskResource::collection($tasks)
            );
        }
       catch (\Illuminate\Auth\Access\AuthorizationException $e) {
        return new APIResponse('fail', Http::FORBIDDEN, 'Not allowed', [], ['auth'=>['Not allowed']]);
        } catch (\Throwable $e) {
            return new APIResponse('fail', Http::SERVER_ERROR, 'Server error', [], ['server'=>[$e->getMessage()]]);
        }
    }

    public function store(StoreTaskRequest $request){
    try {
        Gate::authorize('create', Task::class);

        $data = $request->validated();
        $task = $this->taskRepo->create($data);

        return new APIResponse(
            status: 'success',
            code: Http::CREATED,
            message: 'Task created successfully',
            body: new TaskResource($task)
        );
    } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
        return new APIResponse('fail', Http::FORBIDDEN, 'Not allowed', [], ['auth'=>['Not allowed']]);
    } catch (\Throwable $e) {
        return new APIResponse('fail', Http::SERVER_ERROR, 'Server error', [], ['server'=>[$e->getMessage()]]);
    }
    }

    public function show(Task $task){
      try{  
        Gate::authorize('showTask', $task);
        $task = $this->taskRepo->find($task);
        if(!$task){
            return new APIResponse(
                status: 'fail',
                code: Http::NOT_FOUND,
                message: 'Task not found',
                errors: ['task' => ['Task not found']]
            );
        }
        return new APIResponse(
            status: 'success',
            code: Http::OK,
            message: 'Task retrieved successfully',
            body: new TaskResource($task)
        );
    }
    catch (\Illuminate\Auth\Access\AuthorizationException $e) {
        return new APIResponse('fail', Http::FORBIDDEN, 'Not allowed', [], ['auth'=>['Not allowed']]);
      } catch (\Throwable $e) {
        return new APIResponse('fail', Http::SERVER_ERROR, 'Server error', [], ['server'=>[$e->getMessage()]]);
      }
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        try {

            Gate::authorize('update', $task);
            $data = $request->validated();
            $task = $this->taskRepo->update($task, $data);

            return new APIResponse(
                status: 'success',
                code: Http::OK,
                message: 'Task updated successfully',
                body: new TaskResource($task)
            );
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return new APIResponse(
                'fail',
                Http::FORBIDDEN,
                'Not allowed',
                [],
                ['auth' => ['Not allowed']]
            );
        }
    }

    public function addDependency(Task $task){
        $dependsOnIds = request()->input('depends_on_id', []);
        $this->taskRepo->addDependency($task, $dependsOnIds);
        return new APIResponse(
            status: 'success',
            code: Http::OK,
            message: 'Dependency added successfully',
            body: TaskResource::make($task)
        );
    }

    public function updateStatus(UpdateTaskStatusRequest $request, Task $task)
    {
        try {
            Gate::authorize('updateStatus', $task);

            $task->changeStatus(
                TaskStatus::from($request->status)
            );

            return new APIResponse(
                status: 'success',
                code: Http::OK,
                message: 'Task status updated successfully',
                body: new TaskResource($task->fresh())
            );

        } catch (\DomainException $e) {
            return new APIResponse(
                'fail',
                Http::BAD_REQUEST,
                $e->getMessage(),
                [],
                ['status' => [$e->getMessage()]]

            );
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return new APIResponse(
                'fail',
                Http::FORBIDDEN,
                'Not allowed',
                [],
                ['auth' => ['Not allowed']]
            );
        }
    }

    public function assign(Task $task)
    {
        try {
            Gate::authorize('assign', $task);
            $task->update([
                'assigned_user_id' => request()->input('assigned_user_id')
            ]);

            return new APIResponse(
                status: 'success',
                code: Http::OK,
                message: 'Task assigned successfully',
                body: new TaskResource($task)
            );

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return new APIResponse('fail', Http::FORBIDDEN, 'Not allowed' , [], ['auth'=>['Not allowed']]);
        }
    }

}