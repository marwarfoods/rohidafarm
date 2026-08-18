<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login' => ['required', 'string'], // email or phone
            'password' => ['required', 'string'],
            'cf-turnstile-response' => [new \App\Rules\Turnstile()],
        ];
    }
}
