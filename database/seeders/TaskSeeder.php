<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Enums\TaskStatus;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereHas('role', fn ($q) => $q->where('name', 'user'))->get();

        // 1️⃣ Define tasks without dependencies
        $tasksData = [
            [
                'title' => 'Prepare project documentation',
                'description' => 'Create API documentation',
                'status' => TaskStatus::PENDING->value,
                'days' => 3,
            ],
            [
                'title' => 'Fix authentication bug',
                'description' => 'Resolve token expiration issue',
                'status' => TaskStatus::PENDING->value,
                'days' => 5,
            ],
            [
                'title' => 'Implement task dependencies',
                'description' => 'Add dependency feature',
                'status' => TaskStatus::COMPLETED->value,
                'days' => 1,
            ],
        ];

        $tasks = [];

        // 2️⃣ Create tasks
        foreach ($tasksData as $index => $data) {
            $tasks[] = Task::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'assigned_user_id' => $users[$index % $users->count()]->id,
                'due_date' => now()->addDays($data['days']),
                'status' => $data['status'],
            ]);
        }

        $tasks[1]->addDependency($tasks[0]->id);
        $tasks[2]->addDependency($tasks[0]->id);
        $tasks[2]->addDependency($tasks[1]->id);
    }
}
