<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:191| unique:tasks,title',
            'description' => 'nullable|string',
            'assigned_user_id' => 'nullable|exists:users,id',
            'due_date' => 'sometimes|date|after_or_equal:today',
            'status' => 'required|in:pending,completed,canceled',
            'dependencies' => 'array',
            'dependencies.*' => 'exists:tasks,id',
        ];
    }
}
