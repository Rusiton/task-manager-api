<?php

namespace App\Http\Requests\Auth\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserSettingsRequest extends FormRequest
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
                'theme' => ['required', Rule::in(['light', 'dark'])],
            ];
        } 
        else {
            return [
                'theme' => ['sometimes', 'required', Rule::in(['light', 'dark'])],
            ];
        }
    }
}
