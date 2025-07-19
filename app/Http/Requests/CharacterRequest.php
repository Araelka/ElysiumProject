<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CharacterRequest extends FormRequest
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
            'firstName' => [
                'required',
                'string'
            ],
            
            'secondName' => [
                'required',
                'string'
            ],

            'age' => [
                'required',
                'integer',
                'min:14',
                'max:120'
            ],

            'gender' => [
                'required',
                'string'
            ],

            'nationality' => [
                'required',
                'string'
            ],

            'residentialAddress' => [
                'required',
                'string'
            ],

            'activity' => [
                'required',
                'string'
            ],

            'personality' => [
                'required'
            ]
        ];
    }

    public function messages(){
        return [
            'firstName.required' => 'Поле "Имя" обязательно для заполнения',
            'secondName.required' => 'Поле "Фамилия" обязательно для заполнения',
            'age.required' => 'Поле "Возраст" обязательно для заполнения',
            'age.integer' => 'Возраст должен быть числом',
            'age.min' => 'Возраст должен быть больше 14',
            'age.max' => 'Возраст должен быть меньше 120',
            'gender.required' => 'Поле "Пол" обязательно для заполнения',
            'nationality.required' => 'Поле "Национальность" обязательно для заполнения',
            'residentialAddress.required' => 'Поле "Адрес проживания" обязательно для заполнения',
            'activity.required' => 'Поле "Род деятельности" обязательно для заполнения',
            'personality.required' => 'Поле "Характер" обязательно для заполнения'
        ];
    }
}
