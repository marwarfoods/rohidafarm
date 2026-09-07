<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use App\Models\Shipment;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShiprocketService
{
    private const BASE = 'https://apiv2.shiprocket.in/v1/external';

    /**
     * Whether Shiprocket is enabled and has credentials.
     */
    public function isConfigured(): bool
    {
        return filter_var(Setting::get('shiprocket_enabled', 'false'), FILTER_VALIDATE_BOOLEAN)
            && Setting::get('shiprocket_email')
            && Setting::get('shiprocket_password');
    }

    /**
     * Get a valid API token (cached), authenticating if needed.
     */
    public function getToken(?string $email = null, ?string $password = null): string
    {
        $email = $email ?: Setting::get('shiprocket_email');
        $password = $password ?: Setting::get('shiprocket_password');

        if (!$email || !$password) {
            throw new \Exception('Shiprocket email and password are not set in Settings.');
        }

        $cacheKey = 'shiprocket_token_' . md5($email);

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($email, $password) {
            $res = Http::acceptJson()->post(self::BASE . '/auth/login', [
                'email' => $email,
                'password' => $password,
            ]);

            if (!$res->successful() || empty($res->json('token'))) {
                Log::error('Shiprocket auth failed', ['body' => $res->body()]);
                throw new \Exception($res->json('message') ?: 'Shiprocket login failed. Check the email and password.');
            }

            return $res->json('token');
        });
    }

    /**
     * Forget the cached token (used after a credentials change or auth error).
     */
    public function forgetToken(): void
    {
        $email = Setting::get('shiprocket_email');
        if ($email) {
            Cache::forget('shiprocket_token_' . md5($email));
        }
    }

    /**
     * Verify credentials + pickup location. Returns a human-readable summary.
     */
    public function testConnection(?string $email = null, ?string $password = null): array
    {
        $this->forgetToken();
        $token = $this->getToken($email, $password);

        $pickupRes = Http::withToken($token)->acceptJson()
            ->get(self::BASE . '/settings/company/pickup');

        $locations = collect($pickupRes->json('data.shipping_address') ?? [])
            ->pluck('pickup_location')
            ->filter()
            ->values();

        $configured = Setting::get('shiprocket_pickup_location');
        $pickupOk = !$configured || $locations->isEmpty() || $locations->contains($configured);

        return [
            'token_ok' => true,
            'pickup_locations' => $locations->all(),
            'configured_pickup' => $configured,
            'pickup_ok' => $pickupOk,
        ];
    }

    /**
     * Create a Shiprocket order for a store order and persist a Shipment row.
     */
    public function createShipment(Order $order): Shipment
    {
        $token = $this->getToken();

        $pickupLocation = Setting::get('shiprocket_pickup_location', 'Primary');
        $channelId = Setting::get('shiprocket_channel_id') ?: null;
        $defaultWeight = (float) Setting::get('shiprocket_default_weight', 0.5);
        $dimL = (float) Setting::get('shiprocket_pkg_length', 15);
        $dimB = (float) Setting::get('shiprocket_pkg_breadth', 12);
        $dimH = (float) Setting::get('shiprocket_pkg_height', 8);

        $isCod = strtolower((string) $order->payment_method) === 'cod';

        $items = [];
        $totalWeight = 0.0;
        foreach ($order->items as $item) {
            $unitPrice = $item->price ?? 0;
            $weightKg = $item->product && $item->product->weight
                ? ((float) $item->product->weight / 1000)
                : $defaultWeight;
            $totalWeight += $weightKg * $item->quantity;

            $items[] = [
                'name' => $item->product_name ?: ($item->product->name ?? 'Item'),
                'sku' => $item->product->sku ?? ('SKU-' . $item->id),
                'units' => (int) $item->quantity,
                'selling_price' => round((float) $unitPrice, 2),
            ];
        }

        [$firstName, $lastName] = $this->splitName($order->shipping_name);

        $payload = [
            'order_id' => $order->order_number,
            'order_date' => $order->created_at->format('Y-m-d H:i'),
            'pickup_location' => $pickupLocation,
            'billing_customer_name' => $firstName,
            'billing_last_name' => $lastName,
            'billing_address' => $order->shipping_address_line1,
            'billing_address_2' => $order->shipping_address_line2 ?? '',
            'billing_city' => $order->shipping_city,
            'billing_pincode' => $order->shipping_postal_code,
            'billing_state' => $order->shipping_state,
            'billing_country' => 'India',
            'billing_email' => $order->user?->email ?: Setting::get('site_email', 'orders@example.com'),
            'billing_phone' => $order->shipping_phone,
            'shipping_is_billing' => true,
            'order_items' => $items,
            'payment_method' => $isCod ? 'COD' : 'Prepaid',
            'sub_total' => round((float) $order->subtotal, 2),
            'length' => $dimL,
            'breadth' => $dimB,
            'height' => $dimH,
            'weight' => $totalWeight > 0 ? round($totalWeight, 2) : $defaultWeight,
        ];

        if ($channelId) {
            $payload['channel_id'] = $channelId;
        }

        $res = Http::withToken($token)->acceptJson()
            ->post(self::BASE . '/orders/create/adhoc', $payload);

        // A stale cached token → re-auth once
        if ($res->status() === 401) {
            $this->forgetToken();
            $token = $this->getToken();
            $res = Http::withToken($token)->acceptJson()
                ->post(self::BASE . '/orders/create/adhoc', $payload);
        }

        $data = $res->json();

        if (!$res->successful() || empty($data['order_id'])) {
            Log::error('Shiprocket order create failed', ['order_id' => $order->id, 'response' => $data]);
            $msg = $data['message'] ?? 'Shiprocket order creation failed.';
            if (!empty($data['errors'])) {
                $msg .= ' ' . collect($data['errors'])->flatten()->implode(' ');
            }
            throw new \Exception($msg);
        }

        $awb = $data['awb_code'] ?? null;

        $shipment = Shipment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'delhivery_shipment_id' => (string) ($data['shipment_id'] ?? ''),
                'delhivery_order_id' => (string) $data['order_id'],
                'awb_code' => $awb,
                'courier_name' => 'Shiprocket',
                'status' => $data['status'] ?? 'Order Created',
                'response_payload' => $data,
            ]
        );

        $order->update(array_filter([
            'shipment_status' => 'Order Created in Shiprocket',
            'tracking_carrier' => 'Shiprocket',
            'tracking_number' => $awb,
            'tracking_url' => $awb ? "https://shiprocket.co/tracking/{$awb}" : null,
        ]));

        Log::info('Shiprocket order created', ['order_id' => $order->id, 'sr_order_id' => $data['order_id']]);

        return $shipment;
    }

    private function splitName(?string $name): array
    {
        $name = trim((string) $name);
        if ($name === '') {
            return ['Customer', '.'];
        }
        $parts = preg_split('/\s+/', $name, 2);
        return [$parts[0], $parts[1] ?? '.'];
    }
}
