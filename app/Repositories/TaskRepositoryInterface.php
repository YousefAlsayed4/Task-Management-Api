<?php

namespace App\Repositories;

use App\Models\Task;

interface TaskRepositoryInterface
{
    public function create(array $data);
    public function list(array $filters);
    public function find(Task $task);
    public function update(Task $task, array $data);
    public function addDependency(Task $task, array $dependsOnIds);
    public function assign(Task $task, int $userId);
}
