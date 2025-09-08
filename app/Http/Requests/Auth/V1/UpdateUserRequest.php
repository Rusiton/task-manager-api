<?php

namespace App\Http\Requests\Auth\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $http_method = request()->method();

        if ($http_method === 'PUT') {
            return [
                'name' => ['required', 'unique:users'],
                'email' => ['required', 'email', Rule::unique('users')->ignore($this->user->id, 'id')],
                'password' => ['required'],
            ];
        } 
        else {
            return [
                'name' => ['sometimes', 'required', 'unique:users'],
                'email' => ['sometimes', 'required', 'email', Rule::unique('users')->ignore($this->user->id, 'id')],
                'password' => ['sometimes', 'required'],
            ];
        }

    }
}
