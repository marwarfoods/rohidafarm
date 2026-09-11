<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        // On localhost/local development environment, allow bypass if Cloudflare domain doesn't match
        if (app()->environment('local') && in_array(request()->getHost(), ['127.0.0.1', 'localhost', '::1'])) {
            return true;
        }

        if (empty($token)) {
            Log::warning('Turnstile verification: Empty token received from ' . request()->ip() . ' on ' . request()->path());
            return false;
        }

        $secretKey = Setting::get('turnstile_secret_key');
        if (empty($secretKey)) {
            return true;
        }

        try {
            $payload = [
                'secret' => $secretKey,
                'response' => $token,
            ];

            // Only pass remoteip if it is a valid public IP (avoid internal/proxy IPs that cause Cloudflare mismatch)
            $clientIp = $ip ?: request()->ip();
            if ($clientIp && filter_var($clientIp, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                $payload['remoteip'] = $clientIp;
            }

            $response = Http::asForm()->timeout(6)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', $payload);

            if ($response->serverError()) {
                Log::warning('Cloudflare Turnstile server error (' . $response->status() . '). Failing open to not block user.');
                return true;
            }

            $result = $response->json();
            $success = !empty($result['success']) && $result['success'] === true;

            if (!$success) {
                $errorCodes = $result['error-codes'] ?? [];
                Log::warning('Turnstile verification failed', [
                    'errors' => $errorCodes,
                    'path' => request()->path(),
                    'ip' => request()->ip(),
                ]);
            }

            return $success;
        } catch (\Exception $e) {
            Log::error('Turnstile verification exception: ' . $e->getMessage());
            // Fail open on network timeouts/exceptions so legitimate users are never locked out
            return true;
        }
    }
}
