<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Shiprocket Checkout (Fastrr one-click checkout) API client.
 *
 * Separate from ShiprocketService (shipping): different host, credentials and auth.
 * Endpoints/auth follow https://documenter.getpostman.com/view/25617008/2sB34bL3ig:
 *   X-Api-Key: <api key>
 *   X-Api-HMAC-SHA256: base64(hmac_sha256(<exact request body>, <secret key>))
 */
class ShiprocketCheckoutService
{
    public const ENCRYPTED_KEYS = ['shiprocket_checkout_api_key', 'shiprocket_checkout_secret_key'];

    public function isEnabled(): bool
    {
        return filter_var(Setting::get('shiprocket_checkout_enabled', 'false'), FILTER_VALIDATE_BOOLEAN);
    }

    public function hasCredentials(): bool
    {
        return $this->apiKey() !== '' && $this->secretKey() !== '';
    }

    /**
     * Enabled + credentials present — the only state in which checkout may launch.
     */
    public function isActive(): bool
    {
        return $this->isEnabled() && $this->hasCredentials();
    }

    public function isActiveFor(string $placement): bool
    {
        if (!$this->isActive()) {
            return false;
        }
        $key = $placement === 'buy_now' ? 'shiprocket_checkout_on_buy_now' : 'shiprocket_checkout_on_cart';

        return filter_var(Setting::get($key, 'true'), FILTER_VALIDATE_BOOLEAN);
    }

    public function shouldPushToShipping(): bool
    {
        return filter_var(Setting::get('shiprocket_checkout_push_to_shipping', 'true'), FILTER_VALIDATE_BOOLEAN);
    }

    public function environment(): string
    {
        $env = config('services.shiprocket_checkout.environment') ?: Setting::get('shiprocket_checkout_environment', 'production');

        return array_key_exists($env, config('services.shiprocket_checkout.environments')) ? $env : 'production';
    }

    public function baseUrl(): string
    {
        $url = config('services.shiprocket_checkout.base_url')
            ?: Setting::get('shiprocket_checkout_base_url')
            ?: config("services.shiprocket_checkout.environments.{$this->environment()}.base_url");

        return rtrim((string) $url, '/');
    }

    public function scriptUrl(): string
    {
        return config("services.shiprocket_checkout.environments.{$this->environment()}.script");
    }

    public function styleUrl(): string
    {
        return config("services.shiprocket_checkout.environments.{$this->environment()}.style");
    }

    public function credentialSource(): string
    {
        if (config('services.shiprocket_checkout.api_key') && config('services.shiprocket_checkout.secret_key')) {
            return 'env';
        }

        return $this->hasCredentials() ? 'settings' : 'none';
    }

    public function apiKey(): string
    {
        return trim((string) (config('services.shiprocket_checkout.api_key') ?: $this->decryptSetting('shiprocket_checkout_api_key')));
    }

    /** Never expose this value outside server-side signing. */
    private function secretKey(): string
    {
        return (string) (config('services.shiprocket_checkout.secret_key') ?: $this->decryptSetting('shiprocket_checkout_secret_key'));
    }

    /**
     * Masked API key for display, e.g. "H3E8••••rr7o".
     */
    public function maskedApiKey(): ?string
    {
        $key = $this->apiKey();
        if ($key === '') {
            return null;
        }

        return strlen($key) <= 8 ? str_repeat('•', strlen($key)) : substr($key, 0, 4) . '••••' . substr($key, -4);
    }

    /**
     * HMAC SHA256 of the exact body, Base64 encoded.
     */
    public function hmac(string $body, ?string $secret = null): string
    {
        return base64_encode(hash_hmac('sha256', $body, $secret ?? $this->secretKey(), true));
    }

    /**
     * ISO-8601 UTC timestamp as used in the documented request bodies.
     */
    public static function timestamp(): string
    {
        return now()->utc()->format('Y-m-d\TH:i:s.v\Z');
    }

    /**
     * Signed POST to the Shiprocket Checkout API.
     *
     * @return array{ok: bool, status: int, data: mixed, error: ?string}
     */
    public function post(string $path, array $payload, ?string $apiKey = null, ?string $secret = null): array
    {
        $apiKey = $apiKey ?? $this->apiKey();
        $secret = $secret ?? $this->secretKey();

        if ($apiKey === '' || $secret === '') {
            return ['ok' => false, 'status' => 0, 'data' => null, 'error' => 'API Key / Secret Key are not configured.'];
        }

        // Sign exactly the bytes that are sent.
        $body = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION);

        try {
            $res = Http::withHeaders([
                    'X-Api-Key' => $apiKey,
                    'X-Api-HMAC-SHA256' => $this->hmac($body, $secret),
                ])
                ->acceptJson()
                ->timeout(config('services.shiprocket_checkout.timeout', 15) ?: 15)
                ->withBody($body, 'application/json')
                ->post($this->baseUrl() . '/' . ltrim($path, '/'));
        } catch (ConnectionException $e) {
            return ['ok' => false, 'status' => 0, 'data' => null, 'error' => $this->friendlyConnectionError($e->getMessage())];
        }

        $json = $res->json();
        $ok = $res->successful() && (!is_array($json) || !array_key_exists('ok', $json) || $json['ok'] === true);

        if (!$ok) {
            $error = $this->errorMessage($res->status(), $json);
            // Log status + message only — never headers, keys or HMACs.
            Log::warning('Shiprocket Checkout API error', ['path' => $path, 'status' => $res->status(), 'error' => $error]);

            return ['ok' => false, 'status' => $res->status(), 'data' => $json, 'error' => $error];
        }

        return ['ok' => true, 'status' => $res->status(), 'data' => $json, 'error' => null];
    }

    /**
     * Access Token / Checkout — POST /api/v1/access-token/checkout.
     *
     * @param array<int, array{variant_id: string, quantity: int}> $items
     * @return array{ok: bool, token: ?string, order_id: ?string, expires_at: ?string, error: ?string}
     */
    public function createAccessToken(array $items, string $redirectUrl, array $customAttributes = []): array
    {
        $cartData = ['items' => array_values($items)];
        if ($customAttributes) {
            $cartData['custom_attributes'] = $customAttributes;
        }
        $cartData['mobile_app'] = false;

        $res = $this->post('/api/v1/access-token/checkout', [
            'cart_data' => $cartData,
            'redirect_url' => $redirectUrl,
            'timestamp' => self::timestamp(),
        ]);

        $token = $res['ok'] ? data_get($res['data'], 'result.token') : null;
        if ($res['ok'] && !$token) {
            $res['error'] = 'Shiprocket Checkout returned no token.';
        }

        return [
            'ok' => (bool) $token,
            'token' => $token,
            'order_id' => $token ? (string) data_get($res['data'], 'result.data.order_id') : null,
            'expires_at' => $token ? data_get($res['data'], 'result.expires_at') : null,
            'error' => $token ? null : $res['error'],
        ];
    }

    /**
     * Order / Details — POST /api/v1/custom-platform-order/details.
     */
    public function orderDetails(string $orderId): array
    {
        $res = $this->post('/api/v1/custom-platform-order/details', [
            'order_id' => $orderId,
            'timestamp' => self::timestamp(),
        ]);

        return $res + ['result' => $res['ok'] ? data_get($res['data'], 'result') : null];
    }

    /**
     * Test the credentials with the read-only Order List API
     * (POST /api/v1/custom-platform-order/details/list) and record the outcome.
     *
     * @return array{ok: bool, message: string, details: array<int, string>}
     */
    public function testConnection(?string $apiKey = null, ?string $secret = null): array
    {
        $details = [
            'Environment: ' . ucfirst($this->environment()),
            'Base URL: ' . $this->baseUrl(),
        ];

        $res = $this->post('/api/v1/custom-platform-order/details/list', [
            'startDate' => now()->utc()->subDay()->format('Y-m-d\TH:i:s\Z'),
            'endDate' => now()->utc()->format('Y-m-d\TH:i:s\Z'),
            'timestamp' => self::timestamp(),
            'limit' => 1,
            'page' => 0,
        ], $apiKey, $secret);

        if ($res['ok']) {
            $total = data_get($res['data'], 'result.total');
            $details[] = 'API Key accepted and HMAC signature verified.';
            if ($total !== null) {
                $details[] = "Orders in last 24h: {$total}";
            }
        }

        $this->recordTest($res['ok'], $res['error']);

        return [
            'ok' => $res['ok'],
            'message' => $res['ok'] ? '✓ Shiprocket Checkout connection successful.' : '✗ Connection failed. ' . $res['error'],
            'details' => $details,
        ];
    }

    /**
     * Product Webhook — POST /wh/v1/custom/product (real-time catalog sync).
     */
    public function sendProductWebhook(array $product): array
    {
        return $this->post('/wh/v1/custom/product', $product);
    }

    /**
     * Collection Webhook — POST /wh/v1/custom/collection.
     */
    public function sendCollectionWebhook(array $collection): array
    {
        return $this->post('/wh/v1/custom/collection', $collection);
    }

    /**
     * Secret path segment for the order webhook URL (generated once, stored in settings).
     */
    public function webhookToken(): string
    {
        $token = (string) Setting::get('shiprocket_checkout_webhook_token', '');
        if ($token === '') {
            $token = Str::random(40);
            Setting::set('shiprocket_checkout_webhook_token', $token, 'string', 'shiprocket_checkout', 'Secret path segment of the Shiprocket Checkout order webhook URL');
        }

        return $token;
    }

    public function webhookUrl(): string
    {
        return route('shiprocket-checkout.webhook.order', ['token' => $this->webhookToken()]);
    }

    public function isValidWebhookToken(string $token): bool
    {
        $expected = (string) Setting::get('shiprocket_checkout_webhook_token', '');

        return $expected !== '' && hash_equals($expected, $token);
    }

    /**
     * Endpoints to register in the Shiprocket Checkout dashboard, built from APP_URL.
     */
    public function customEndpoints(): array
    {
        return [
            'Fetch Products' => route('shiprocket-checkout.catalog.products') . '?page=1&limit=100',
            'Fetch Products by Collection' => route('shiprocket-checkout.catalog.collection-products') . '?collection_id={collection_id}&page=1&limit=100',
            'Fetch Collections' => route('shiprocket-checkout.catalog.collections') . '?page=1&limit=100',
            'Order Webhook' => $this->webhookUrl(),
            'Redirect URL (sent automatically)' => route('shiprocket-checkout.return'),
        ];
    }

    public function recordTest(bool $ok, ?string $error): void
    {
        Setting::set('shiprocket_checkout_last_test_at', now()->toDateTimeString(), 'string', 'shiprocket_checkout', 'Last Shiprocket Checkout connection test');
        Setting::set('shiprocket_checkout_last_test_ok', $ok ? 'true' : 'false', 'boolean', 'shiprocket_checkout', 'Last test result');
        if (!$ok) {
            $this->recordError($error);
        }
    }

    public function recordError(?string $error): void
    {
        Setting::set('shiprocket_checkout_last_error', $error ? '[' . now()->toDateTimeString() . '] ' . Str::limit($error, 400) : null, 'string', 'shiprocket_checkout', 'Last Shiprocket Checkout error');
    }

    /**
     * Record a timestamp setting at most once every 5 minutes (catalog fetches can be frequent).
     */
    public function touch(string $key): void
    {
        $last = Setting::get($key);
        if (!$last || now()->diffInMinutes(\Illuminate\Support\Carbon::parse($last)) >= 5) {
            Setting::set($key, now()->toDateTimeString(), 'string', 'shiprocket_checkout', null);
        }
    }

    private function decryptSetting(string $key): string
    {
        $encrypted = Setting::get($key);
        if (!$encrypted) {
            return '';
        }

        try {
            return Crypt::decryptString($encrypted);
        } catch (\Throwable $e) {
            Log::warning("Shiprocket Checkout: could not decrypt {$key}; re-enter it in settings.");
            return '';
        }
    }

    private function errorMessage(int $status, $json): string
    {
        $apiError = null;
        if (is_array($json)) {
            $err = $json['error'] ?? $json['message'] ?? $json['errorCode'] ?? null;
            $apiError = is_array($err) ? ($err['message'] ?? $err['msg'] ?? json_encode($err)) : $err;
        }

        $hint = match (true) {
            $status === 511 => 'Invalid X-Api-Key or HMAC signature — check the API Key, Secret Key and Environment.',
            $status === 401 || $status === 403 => 'Unauthorized — check the API Key, Secret Key and Environment (staging keys do not work on production).',
            $status === 404 => 'Endpoint not found — check the Base URL / Environment.',
            $status === 429 => 'Rate limited — try again in a minute.',
            $status >= 500 => 'Shiprocket Checkout server error — try again later.',
            default => 'Request rejected.',
        };

        return "HTTP {$status}: " . ($apiError ? Str::limit((string) $apiError, 200) . ' — ' : '') . $hint;
    }

    private function friendlyConnectionError(string $message): string
    {
        return match (true) {
            str_contains($message, 'cURL error 60') => 'SSL certificate error (cURL 60) — configure curl.cainfo in php.ini.',
            str_contains($message, 'cURL error 6') => 'Could not resolve the Shiprocket Checkout host — check internet/DNS.',
            str_contains($message, 'cURL error 28') => 'Connection timed out — check connectivity or raise SHIPROCKET_CHECKOUT_TIMEOUT.',
            default => 'Could not connect to Shiprocket Checkout.',
        };
    }
}
