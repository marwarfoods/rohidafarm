<?php

namespace App\Rules;

use App\Services\RecaptchaService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Recaptcha implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!RecaptchaService::isEnabled()) {
            return;
        }

        if (!RecaptchaService::verify($value)) {
            $fail('Security verification failed (Google reCAPTCHA). Please try submitting again.');
        }
    }
}
