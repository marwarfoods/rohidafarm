<?php

namespace App\Rules;

use App\Services\TurnstileService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Turnstile implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!TurnstileService::isEnabled()) {
            return;
        }

        if (!TurnstileService::verify($value)) {
            $fail('Security verification failed (Cloudflare Turnstile). Please try submitting again.');
        }
    }
}
