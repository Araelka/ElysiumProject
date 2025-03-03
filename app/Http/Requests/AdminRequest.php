<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminRequest extends FormRequest
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
        $id = $this->route('id');
        
        return [
            'email' => ['required', 
            'string', 
            'email', 
            Rule::unique('users', 'email')->ignore($this->id), 
            'max:255'
            ],
            'role_id' => ['required', 
            'integer', 
            'exists:roles,id'
            ]
        ];
    }

    public function messages() {
        return [
            'email.required' => 'Поле "Email" обязательно для заполнения',
            'email.unique' => 'Этот email уже используется',
            'role_id.required' => 'Поле "Роль" обязательно для заполнения'
        ];
    }
}
