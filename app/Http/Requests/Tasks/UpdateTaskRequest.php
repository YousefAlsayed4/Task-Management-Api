<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('status')) {
            $this->merge([
                '__forbidden_status' => true
            ]);
        }

        if ($this->has('dependencies')) {
            $this->merge([
                '__forbidden_dependencies' => true
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:191| unique:tasks,title',
            'description' => 'sometimes|string|nullable',
            'assigned_user_id' => 'sometimes|exists:users,id',
            'due_date' => 'sometimes|date|after_or_equal:today',

            '__forbidden_status' => 'prohibited',
            '__forbidden_dependencies' => 'prohibited',
        ];
    }

    public function messages(): array
    {
        return [
            '__forbidden_status.prohibited' =>
                'Status cannot be updated from this endpoint',

            '__forbidden_dependencies.prohibited' =>
                'Dependencies cannot be updated from this endpoint',
        ];
    }
}
