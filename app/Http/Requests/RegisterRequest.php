<?php

namespace App\Http\Requests;

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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed']
        ];
    }

    public function messages() {
        return [
            'login.required' => 'Поле "Логин" обязательно для заполнения',
            'email.required' => 'Поле "Email" обязательно для заполнения',
            'password.required' => 'Поле "Пароль" обязательно для заполнения',
            'login.unique' => 'Этот логин уже используется',
            'email.unique' => 'Этот email уже используется',
            'password.min' => 'Парольдолжен содержать миниум 6 символов',
            'password.confirmed' => 'Пароли не совпадают'
        ];
    }
}
