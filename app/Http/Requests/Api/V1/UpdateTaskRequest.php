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
                'column_id' => ['required', 'integer', 'exists:columns,id'],
                'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
                'name' => ['required', 'string'],
                'description' => ['nullable'],
                'position' => ['required', 'integer'],
                'due_date' => ['required', 'date'],
            ];
        }
        else {
            return [
                'column_id' => ['sometimes', 'required', 'integer', 'exists:columns,id'],
                'assigned_to' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
                'name' => ['sometimes', 'required', 'string'],
                'description' => ['sometimes', 'nullable'],
                'position' => ['sometimes', 'nullable', 'integer'],
                'due_date' => ['sometimes', 'required', 'date'],
            ];
        }
    }



    protected function prepareForValidation()
    {
        return $this->merge([
            'column_id' => $this->columnId,
            'assigned_to' => $this->assignedTo,
            'due_date' => $this->dueDate,
        ]);
    }
}
