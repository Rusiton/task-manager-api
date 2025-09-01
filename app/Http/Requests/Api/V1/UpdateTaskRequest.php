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
                'columnId' => ['required', 'integer', 'exists:columns,id'],
                'assignedTo' => ['required', 'integer', 'exists:users,id'],
                'name' => ['required', 'string'],
                'description' => ['nullable'],
                'position' => ['required', 'integer'],
                'dueDate' => ['required', 'date'],
            ];
        }
        else {
            return [
                'columnId' => ['sometimes', 'required', 'integer', 'exists:columns,id'],
                'assignedTo' => ['sometimes', 'required', 'integer', 'exists:users,id'],
                'name' => ['sometimes', 'required', 'string'],
                'description' => ['sometimes', 'nullable'],
                'position' => ['sometimes', 'required', 'integer'],
                'dueDate' => ['sometimes', 'required', 'date'],
            ];
        }
    }
}
