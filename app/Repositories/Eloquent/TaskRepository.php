<?php

namespace App\Repositories\Eloquent;

use App\Models\Task;
use App\Repositories\TaskRepositoryInterface;
use Illuminate\Support\Facades\Gate;

class TaskRepository implements TaskRepositoryInterface
{
    public function create(array $data)
    {
        $task = Task::create($data);
        if (!empty($data['dependencies'])) {
            $task->dependencies()->sync($data['dependencies']);
        }

        return $task;
    }

    public function list(array $filters)
    {
        return Task::query()
            ->status($filters['status'] ?? null)
            ->assignedTo($filters['assigned_user_id'] ?? null)
            ->dueBetween($filters['due_from'] ?? null, $filters['due_to'] ?? null)
            ->with(['assignedUser', 'dependencies'])
            ->paginate(10);
    }

   public function find(Task $task)
    {
        return $task->load(['assignedUser', 'dependencies']);
    }

    public function update(Task $task, array $data)
    {
        $task = Task::findOrFail($task->id);

        $task->update($data);

        if (isset($data['dependencies'])) {
            $task->dependencies()->sync($data['dependencies']);
        }

        return $task;
    }

    public function addDependency(Task $task, array $dependsOnIds)
    {
        $task = Task::findOrFail($task->id);

        foreach ($dependsOnIds as $id) {
            $task->addDependency($id);
        }

        return $task;
    }

    public function assign(Task $task, int $userId)
    {
        $task = Task::findOrFail($task->id);
        $task->assigned_user_id = $userId;
        $task->save();

        return $task;
    }
}