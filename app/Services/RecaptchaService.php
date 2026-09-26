<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaService
{
    public const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Check if Google reCAPTCHA v3 is enabled and configured.
     */
    public static function isEnabled(): bool
    {
        return (bool) Setting::get('recaptcha_enabled', false) && !empty(Setting::get('recaptcha_site_key'));
    }

    /**
     * Get the reCAPTCHA Site Key.
     */
    public static function getSiteKey(): ?string
    {
        return Setting::get('recaptcha_site_key');
    }

    /**
     * Minimum score (0.0 bot – 1.0 human) a submission needs to pass.
     */
    public static function minScore(): float
    {
        $score = (float) Setting::get('recaptcha_min_score', 0.5);

        return ($score > 0 && $score <= 1) ? $score : 0.5;
    }

    /**
     * Verify a reCAPTCHA v3 token from a client form submission.
     */
    public static function verify(?string $token, ?string $ip = null): bool
    {
        if (!self::isEnabled()) {
            return true;
        }

        // On localhost the site key's domain won't match, so allow bypass in local development
        if (app()->environment('local') && in_array(request()->getHost(), ['127.0.0.1', 'localhost', '::1'])) {
            return true;
        }

        if (empty($token)) {
            Log::warning('reCAPTCHA verification: Empty token received from ' . request()->ip() . ' on ' . request()->path());
            return false;
        }

        $secretKey = Setting::get('recaptcha_secret_key');
        if (empty($secretKey)) {
            return true;
        }

        try {
            $payload = [
                'secret' => $secretKey,
                'response' => $token,
            ];

            // Only pass remoteip if it is a valid public IP (avoid internal/proxy IPs)
            $clientIp = $ip ?: request()->ip();
            if ($clientIp && filter_var($clientIp, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                $payload['remoteip'] = $clientIp;
            }

            $response = Http::asForm()->timeout(6)->post(self::VERIFY_URL, $payload);

            if ($response->serverError()) {
                Log::warning('Google reCAPTCHA server error (' . $response->status() . '). Failing open to not block user.');
                return true;
            }

            $result = $response->json();
            $score = (float) ($result['score'] ?? 0);
            $success = !empty($result['success']) && $score >= self::minScore();

            if (!$success) {
                Log::warning('reCAPTCHA verification failed', [
                    'errors' => $result['error-codes'] ?? [],
                    'score' => $result['score'] ?? null,
                    'action' => $result['action'] ?? null,
                    'path' => request()->path(),
                    'ip' => request()->ip(),
                ]);
            }

            return $success;
        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification exception: ' . $e->getMessage());
            // Fail open on network timeouts/exceptions so legitimate users are never locked out
            return true;
        }
    }
}
