<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateColumnRequest extends FormRequest
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
                'name' => ['required', 'string'],
                'position' => ['required', 'integer'],
            ];
        }
        else {
            return [
                'name' => ['sometimes', 'required', 'string'],
                'position' => ['sometimes', 'required', 'integer'],
            ];
        }
    }
}
