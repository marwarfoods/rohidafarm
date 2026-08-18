<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'regex:/^[0-9]{10}$/', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'cf-turnstile-response' => [new \App\Rules\Turnstile()],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'The phone number must be exactly 10 digits.',
            'phone.unique' => 'This phone number is already registered.',
            'email.unique' => 'This email address is already registered.',
        ];
    }
}
