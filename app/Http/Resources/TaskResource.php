<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'due_date' => $this->due_date,
            'assigned_user' => $this->assignedUser?->name,
            'dependencies' => $this->dependencies->map(function ($dependency) {
                return [
                    'id' => $dependency->id,
                    'title' => $dependency->title,
                    'status' => $dependency->status,
                ];
            }),
        ];
    }
}