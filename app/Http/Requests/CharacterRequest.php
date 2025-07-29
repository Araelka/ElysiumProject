<?php

namespace App\Http\Requests;

use App\Rules\MaxCharacters;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Storage;


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

            'height' => [
                'required',
                'integer',
                'min: 50',
                'max: 250'
            ],

            'weight' => [
                'required',
                'integer',
                'min:10',
                'max:300'
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
                'required',
                new MaxCharacters(5000)
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:png,jpeg,jpg,webp',
                'max:4056'
            ],

            'image' => [
                'nullable', 
                'image', 
                'mimes:png,jpeg,jpg,webp', 
                'max:2048']
        ];
    }



    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator) {
        if ($this->hasFile('image')) {
            $tempPath = $this->file('image')->store('temp', 'public');
            session(['temp_photo_path' => $tempPath]);
        }    

        throw new HttpResponseException(
            redirect()->back()
            ->withErrors($validator)
            ->withInput()
        );
    }

    public function messages(){
        return [
            'firstName.required' => 'Поле "Имя" обязательно для заполнения',
            'secondName.required' => 'Поле "Фамилия" обязательно для заполнения',
            'age.required' => 'Поле "Возраст" обязательно для заполнения',
            'age.integer' => 'Возраст должен быть числом',
            'age.min' => 'Возраст должен быть больше 14 лет',
            'age.max' => 'Возраст должен быть меньше 120 лет',
            'height.required' => 'Поле "Рост" обязательно для заполнения',
            'height.integer' => 'Рост должен быть числом',
            'height.min' => 'Рост должен быть больше 50 см.',
            'height.max' => 'Рост должен быть меньше 250 см.',
            'weight.required' => 'Поле "Вес" обязательно для заполнения',
            'weight.integer' => 'Вес должен быть числом',
            'weight.min' => 'Вес должен быть больше 10 кг.',
            'weight.max' => 'Вес должен быть меньше 300 кг.',
            'gender.required' => 'Поле "Пол" обязательно для заполнения',
            'nationality.required' => 'Поле "Национальность" обязательно для заполнения',
            'residentialAddress.required' => 'Поле "Адрес проживания" обязательно для заполнения',
            'activity.required' => 'Поле "Род деятельности" обязательно для заполнения',
            'personality.required' => 'Поле "Характер" обязательно для заполнения',
            'personality.max' => 'Превышен лимит символом для характера. Максимальное количество символов - :max'
        ];
    }
}
