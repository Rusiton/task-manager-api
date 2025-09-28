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
                'columnToken' => ['required', 'exists:columns,token'],
                'assignedTo' => ['nullable', 'exists:users,token'],
                'name' => ['required', 'string'],
                'description' => ['nullable'],
                'position' => ['required', 'integer'],
                'dueDate' => ['nullable', 'date'],
            ];
        }
        else {
            return [
                'columnToken' => ['sometimes', 'required', 'exists:columns,token'],
                'assignedTo' => ['sometimes', 'nullable', 'exists:users,token'],
                'name' => ['sometimes', 'required', 'string'],
                'description' => ['sometimes', 'nullable'],
                'position' => ['sometimes', 'required', 'integer'],
                'dueDate' => ['sometimes', 'nullable', 'date'],
            ];
        }
    }
}
