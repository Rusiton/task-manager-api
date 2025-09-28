<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCommentRequest extends FormRequest
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
        return [
            'commentableType' => ['required', 'string', Rule::in(['Column', 'Task'])],
            'commentableId' => ['required', 'integer'],
            'userToken' => ['required', 'exists:users,token'],
            'content' => ['required', 'string'],
        ];
    }



    protected function prepareForValidation()
    {
        return $this->merge([
            'commentableType' => $this->type,
            'commentableId' => $this->id,
        ]);
    }
}
