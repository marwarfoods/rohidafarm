<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Shipment;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Shiprocket Engage integration.
 *
 * Shiprocket does not publish a separate Engage API — Engage (WhatsApp order
 * confirmation, COD confirmation, address verification, etc.) runs inside the
 * Shiprocket account on the orders that reach it, and is authenticated with the
 * same API user as the external API (https://apidocs.shiprocket.in). This module
 * is kept separate from ShiprocketService so shipping is never affected.
 */
class ShiprocketEngageService
{
    /** Shiprocket tokens are valid for 240 hours; refresh a day early. */
    private const TOKEN_TTL_HOURS = 216;

    public function baseUrl(): string
    {
        return rtrim(config('services.shiprocket_engage.base_url') ?: 'https://apiv2.shiprocket.in/v1/external', '/');
    }

    public function isEnabled(): bool
    {
        return filter_var(Setting::get('shiprocket_engage_enabled', 'false'), FILTER_VALIDATE_BOOLEAN);
    }

    public function isConfigured(): bool
    {
        $creds = $this->credentials();

        return $this->isEnabled() && $creds['email'] && $creds['password'];
    }

    /**
     * Resolve credentials. Priority: .env → Engage settings → Shipping settings.
     *
     * @return array{email: ?string, password: ?string, source: string}
     */
    public function credentials(): array
    {
        $envEmail = config('services.shiprocket_engage.email');
        $envPassword = config('services.shiprocket_engage.password');
        if ($envEmail && $envPassword) {
            return ['email' => trim($envEmail), 'password' => $envPassword, 'source' => 'env'];
        }

        $email = trim((string) Setting::get('shiprocket_engage_email', ''));
        $password = $this->storedPassword();
        if ($email && $password) {
            return ['email' => $email, 'password' => $password, 'source' => 'engage_settings'];
        }

        $shipEmail = trim((string) Setting::get('shiprocket_email', ''));
        $shipPassword = Setting::get('shiprocket_password');
        if ($shipEmail && $shipPassword) {
            return ['email' => $shipEmail, 'password' => $shipPassword, 'source' => 'shipping_settings'];
        }

        return ['email' => null, 'password' => null, 'source' => 'none'];
    }

    public static function sourceLabel(string $source): string
    {
        return match ($source) {
            'env' => '.env (SHIPROCKET_ENGAGE_EMAIL / PASSWORD)',
            'engage_settings' => 'Shiprocket Engage settings',
            'shipping_settings' => 'Shipping tab Shiprocket credentials',
            'form' => 'Values entered in the form',
            default => 'Not configured',
        };
    }

    /**
     * Decrypt the Engage password saved from the admin panel.
     */
    public function storedPassword(): ?string
    {
        $encrypted = Setting::get('shiprocket_engage_password');
        if (!$encrypted) {
            return null;
        }

        try {
            return Crypt::decryptString($encrypted);
        } catch (\Throwable $e) {
            // APP_KEY changed or value tampered with — treat as not set.
            Log::warning('Shiprocket Engage password could not be decrypted; re-enter it in settings.');
            return null;
        }
    }

    /**
     * Get a cached API token for Engage calls (separate cache from shipping).
     */
    public function getToken(bool $fresh = false): string
    {
        $creds = $this->credentials();
        if (!$creds['email'] || !$creds['password']) {
            throw new \RuntimeException('Shiprocket Engage credentials are not configured.');
        }

        $cacheKey = 'shiprocket_engage_token_' . md5($creds['email']);
        if ($fresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, now()->addHours(self::TOKEN_TTL_HOURS), function () use ($creds) {
            return $this->login($creds['email'], $creds['password'])['token'];
        });
    }

    /**
     * Authenticated request builder for future Engage features.
     */
    public function api(): PendingRequest
    {
        return $this->client()->withToken($this->getToken());
    }

    /**
     * Run all connection checks. Pass $email/$password to test unsaved form values.
     *
     * @return array{ok: bool, source: string, checks: array<int, array{label: string, status: string, detail: string}>}
     */
    public function testConnection(?string $email = null, ?string $password = null): array
    {
        $creds = $this->credentials();
        if ($email && $password) {
            $creds = ['email' => trim($email), 'password' => $password, 'source' => 'form'];
        }

        $checks = [];
        $add = function (string $label, string $status, string $detail) use (&$checks) {
            $checks[] = compact('label', 'status', 'detail');
        };

        if (!$creds['email'] || !$creds['password']) {
            $add('Credentials', 'fail', 'No Shiprocket API user email/password found in .env, Engage settings or Shipping settings.');
            return ['ok' => false, 'source' => 'none', 'checks' => $checks];
        }
        $add('Credentials', 'pass', 'Using: ' . self::sourceLabel($creds['source']) . ' — ' . $creds['email']);

        // 1. Authentication — proves the credentials and API connectivity.
        try {
            $login = $this->login($creds['email'], $creds['password']);
        } catch (\Throwable $e) {
            $add('API authentication', 'fail', $e->getMessage());
            return ['ok' => false, 'source' => $creds['source'], 'checks' => $checks];
        }

        $token = $login['token'];
        $who = trim(($login['first_name'] ?? '') . ' ' . ($login['last_name'] ?? ''));
        $add('API authentication', 'pass', 'Token issued' . ($who ? " for {$who}" : '') . (!empty($login['company_id']) ? " (Company ID: {$login['company_id']})" : '') . '.');

        // Cache this token when testing the credentials the app will actually use.
        if ($creds['source'] !== 'form') {
            Cache::put('shiprocket_engage_token_' . md5($creds['email']), $token, now()->addHours(self::TOKEN_TTL_HOURS));
        }

        $client = $this->client()->withToken($token);

        // 2. Account access — Engage charges are debited from the Shiprocket wallet.
        try {
            $res = $client->get('/account/details/wallet-balance');
            if ($res->successful()) {
                $balance = (float) $res->json('data.balance_amount', 0);
                $add('Account / wallet access', $balance > 0 ? 'pass' : 'warn',
                    'Wallet balance: ₹' . number_format($balance, 2) . ($balance > 0 ? '' : ' — recharge the wallet, Engage messages are billed from it.'));
            } else {
                $add('Account / wallet access', 'warn', $this->errorMessage($res->status(), $res->json(), 'Could not read wallet balance.'));
            }
        } catch (\Throwable $e) {
            $add('Account / wallet access', 'warn', $this->friendlyException($e));
        }

        // 3. Channels — Engage works on orders that arrive through these channels.
        try {
            $res = $client->get('/channels');
            if ($res->successful()) {
                $channels = collect($res->json('data') ?? []);
                $names = $channels->map(fn ($c) => ($c['name'] ?? '?') . ' #' . ($c['id'] ?? '?') . ' (' . ($c['status'] ?? '?') . ')')->implode(', ');
                $configured = Setting::get('shiprocket_channel_id');
                if ($configured && !$channels->contains(fn ($c) => (string) ($c['id'] ?? '') === (string) $configured)) {
                    $add('Channels', 'warn', "Channel ID {$configured} (Shipping tab) was not found. Available: " . ($names ?: 'none'));
                } else {
                    $add('Channels', 'pass', $names ? "Found: {$names}" : 'Connected, but no channels returned.');
                }
            } else {
                $add('Channels', 'warn', $this->errorMessage($res->status(), $res->json(), 'Could not list channels.'));
            }
        } catch (\Throwable $e) {
            $add('Channels', 'warn', $this->friendlyException($e));
        }

        // 4. Engage status of the most recent order this site pushed to Shiprocket.
        $shipment = Shipment::whereNotNull('delhivery_order_id')->where('delhivery_order_id', '!=', '')->latest('id')->first();
        if (!$shipment) {
            $add('Engage status on orders', 'info', 'No order has been pushed to Shiprocket yet. Place a test order to see its Engage status.');
        } else {
            try {
                $res = $client->get('/orders/show/' . $shipment->delhivery_order_id);
                if ($res->successful()) {
                    $engage = $res->json('data.engage');
                    $active = is_array($engage)
                        ? !empty($engage)
                        : ($engage !== null && $engage !== '' && strtoupper((string) $engage) !== 'NA');
                    $add('Engage status on orders', $active ? 'pass' : 'info',
                        "Latest Shiprocket order #{$shipment->delhivery_order_id}: engage = " . (is_array($engage) ? json_encode($engage) : var_export($engage, true))
                        . ($active ? '' : ' — Engage is not active on this order yet. Activate it in the Shiprocket panel (see Setup Guide).'));
                } else {
                    $add('Engage status on orders', 'info', $this->errorMessage($res->status(), $res->json(), 'Could not read the latest order.'));
                }
            } catch (\Throwable $e) {
                $add('Engage status on orders', 'info', $this->friendlyException($e));
            }
        }

        return ['ok' => true, 'source' => $creds['source'], 'checks' => $checks];
    }

    /**
     * POST /auth/login and return the response data (token, company_id, ...).
     */
    private function login(string $email, string $password): array
    {
        try {
            $res = $this->client()->post('/auth/login', ['email' => $email, 'password' => $password]);
        } catch (ConnectionException $e) {
            throw new \RuntimeException($this->friendlyException($e), 0, $e);
        }

        if (!$res->successful() || empty($res->json('token'))) {
            Log::warning('Shiprocket Engage auth failed', ['status' => $res->status(), 'body' => $res->json() ?? $res->body()]);
            throw new \RuntimeException($this->errorMessage($res->status(), $res->json(), 'Login failed.'));
        }

        return $res->json();
    }

    private function client(): PendingRequest
    {
        $verify = true;
        if (app()->environment('local')) {
            $verify = filter_var(config('services.shiprocket_engage.verify_ssl', true), FILTER_VALIDATE_BOOLEAN);
        }

        return Http::baseUrl($this->baseUrl())
            ->acceptJson()
            ->asJson()
            ->timeout(config('services.shiprocket_engage.timeout', 20) ?: 20)
            ->withOptions(['verify' => $verify]);
    }

    private function errorMessage(int $status, $json, string $fallback): string
    {
        $apiMessage = is_array($json) ? ($json['message'] ?? null) : null;
        if (is_array($apiMessage)) {
            $apiMessage = json_encode($apiMessage);
        }

        $hint = match (true) {
            $status === 401 || $status === 403 => 'Wrong API user email/password, or the API user is disabled. Use the API user from Shiprocket → Settings → API, not your panel login.',
            $status === 429 => 'Too many requests — wait a minute and try again.',
            $status >= 500 => 'Shiprocket server error — try again later.',
            default => $fallback,
        };

        return "HTTP {$status}: " . ($apiMessage ? "{$apiMessage}. " : '') . $hint;
    }

    private function friendlyException(\Throwable $e): string
    {
        $msg = $e->getMessage();

        if (str_contains($msg, 'cURL error 60') || str_contains($msg, 'SSL certificate')) {
            return 'SSL certificate error (cURL 60). On localhost set curl.cainfo in php.ini to a cacert.pem file, or set SHIPROCKET_ENGAGE_VERIFY_SSL=false in .env (local only).';
        }
        if (str_contains($msg, 'cURL error 6') || str_contains($msg, 'Could not resolve host')) {
            return 'Cannot reach apiv2.shiprocket.in — check the server internet connection / DNS / firewall.';
        }
        if (str_contains($msg, 'cURL error 28') || str_contains($msg, 'timed out')) {
            return 'Request to Shiprocket timed out — check connectivity or raise SHIPROCKET_ENGAGE_TIMEOUT.';
        }

        return $msg;
    }
}
