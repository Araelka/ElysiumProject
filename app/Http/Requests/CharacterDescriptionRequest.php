<?php

namespace App\Http\Requests;

use App\Rules\MaxCharacters;
use Illuminate\Foundation\Http\FormRequest;

class CharacterDescriptionRequest extends FormRequest
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
            'biography' => [
                'required', 
                'string',
                new MaxCharacters(10000)
            ],

            'description' => [
                'required', 
                'string',
                new MaxCharacters(5000)
            ],

            'headcounts' => [
                'nullable',
                'string',
                new MaxCharacters(1000)
                ]
        ];
    }

    public function messages(){
        return [
            'biography.required' => 'Поле "Биография" обязательно для заполнения',
            'description.required' => 'Поле "Внешность" обязательно для заполнения'
        ];
    }
}
