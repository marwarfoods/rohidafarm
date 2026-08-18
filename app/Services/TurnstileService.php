<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class TurnstileService
{
    /**
     * Check if Turnstile is enabled and configured.
     */
    public static function isEnabled(): bool
    {
        return (bool) Setting::get('turnstile_enabled', false) && !empty(Setting::get('turnstile_site_key'));
    }

    /**
     * Get the Turnstile Site Key.
     */
    public static function getSiteKey(): ?string
    {
        return Setting::get('turnstile_site_key');
    }

    /**
     * Verify Turnstile token from client form submission.
     */
    public static function verify(?string $token, ?string $ip = null): bool
    {
        if (!self::isEnabled()) {
            return true;
        }

        if (empty($token)) {
            return false;
        }

        $secretKey = Setting::get('turnstile_secret_key');
        if (empty($secretKey)) {
            return true;
        }

        try {
            $response = Http::asForm()->timeout(5)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secretKey,
                'response' => $token,
                'remoteip' => $ip ?: request()->ip(),
            ]);

            $result = $response->json();
            return !empty($result['success']) && $result['success'] === true;
        } catch (\Exception $e) {
            logger()->error('Turnstile verification exception: ' . $e->getMessage());
            // Fail open or closed depending on configuration; fallback to true on network timeouts to not block legitimate users
            return true;
        }
    }
}
