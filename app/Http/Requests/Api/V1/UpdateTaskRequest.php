<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $method = request()->method();

        if ($method == 'PUT') {
            return [
                'column_token' => ['required', 'exists:columns,token'],
                'assigned_to' => ['nullable', 'exists:users,token'],
                'name' => ['required', 'string'],
                'description' => ['nullable'],
                'position' => ['required', 'integer'],
                'due_date' => ['nullable', 'date'],
            ];
        }
        else {
            return [
                'column_token' => ['sometimes', 'required', 'exists:columns,token'],
                'assigned_to' => ['sometimes', 'nullable', 'exists:users,token'],
                'name' => ['sometimes', 'required', 'string'],
                'description' => ['sometimes', 'nullable'],
                'position' => ['sometimes', 'required', 'integer'],
                'due_date' => ['sometimes', 'nullable', 'date'],
            ];
        }
    }

    protected function prepareForValidation()
    {
        if ($this->columnToken) {
            $this->merge([
                'column_token' => $this->columnToken
            ]);
        }

        if ($this->assignedTo) {
            $this->merge([
                'assigned_to' => $this->assignedTo
            ]);
        }

        if ($this->dueDate) {
            $this->merge([
                'due_date' => $this->dueDate
            ]);
        }
    }
}
