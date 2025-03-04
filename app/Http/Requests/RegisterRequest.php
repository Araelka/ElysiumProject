<?php

namespace App\Http\Requests;

use App\Rules\NoSpaces;
use Illuminate\Foundation\Http\FormRequest;
use function PHPUnit\Framework\returnArgument;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()  {
        $this->merge([
            'login' => mb_strtolower($this->input('login'))
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => [
                'required', 
                'string', 
                'max:255', 
                'unique:users',
                'regex: /^[a-zA-Z0-9_\-\ ]+$/',
                new NoSpaces('Поле "Логин" не должно содержать пробелов')
                ],

            'email' => [
                'required', 
                'string', 
                'email',
                'max:255', 
                'unique:users'],

            'password' => [
                'required', 
                'string', 
                'min:6', 
                'confirmed']
        ];
    }

    public function messages() {
        return [
            'login.required' => 'Поле "Логин" обязательно для заполнения',
            'login.no_spaces' => 'Логин не должен содержать пробелов',
            'login.regex' => 'Логин может содержать только английские буквы, цифры, подчёркивания (_) и дефисы (-)',
            'login.unique' => 'Этот логин уже используется',
            'email.required' => 'Поле "Email" обязательно для заполнения',
            'email.unique' => 'Этот email уже используется',
            'password.required' => 'Поле "Пароль" обязательно для заполнения',
            'password.min' => 'Парольдолжен содержать миниум 6 символов',
            'password.confirmed' => 'Пароли не совпадают',
        ];
    }
}
