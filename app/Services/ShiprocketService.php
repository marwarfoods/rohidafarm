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
     * Parse arbitrary weight string/number into kilograms (kg).
     * Handles: '1L', '1 L', '2L', '5 L', '500ml', '1 kg', '250 gm', '500g', '500 mg', numeric values.
     */
    public function parseWeightToKg(mixed $rawWeight): float
    {
        if (empty($rawWeight)) {
            return 0.0;
        }

        $str = strtolower(trim((string) $rawWeight));

        // 1. Kilograms (e.g. "1kg", "1.5 kg", "2 kgs", "kilogram")
        if (preg_match('/([\d.]+)\s*(?:kg|kgs|kilo|kilogram)/i', $str, $m)) {
            return (float) $m[1];
        }

        // 2. Litres (for liquid products like ghee, oil, honey: 1L ≈ 1 kg)
        // e.g. "1l", "1 l", "2.5 litre", "500 ltr", "liter"
        if (preg_match('/([\d.]+)\s*(?:litre|liter|ltr|lt|l)\b/i', $str, $m)) {
            return (float) $m[1];
        }

        // 3. Millilitres (e.g. "500ml", "250 ml", "1000ml")
        if (preg_match('/([\d.]+)\s*(?:ml|millilitre|milliliter)\b/i', $str, $m)) {
            $val = (float) $m[1];
            return $val > 0 ? $val / 1000 : 0.0;
        }

        // 4. Grams (e.g. "500gm", "500 g", "250 gm", "200 grams")
        if (preg_match('/([\d.]+)\s*(?:grams?|gms?|gm|g)\b/i', $str, $m)) {
            $val = (float) $m[1];
            return $val > 0 ? $val / 1000 : 0.0;
        }

        // 5. Milligrams (e.g. "500 mg" — sometimes used as typo for gm in DB)
        if (preg_match('/([\d.]+)\s*(?:mg|milligram)\b/i', $str, $m)) {
            $val = (float) $m[1];
            // If user typed 500 MG for turmeric/chilli, they almost certainly meant 500 grams (0.5kg)
            if ($val >= 50) {
                return $val / 1000;
            }
            return $val > 0 ? $val / 1000000 : 0.0;
        }

        // 6. Plain numeric or unknown string with number
        if (preg_match('/([\d.]+)/', $str, $m)) {
            $val = (float) $m[1];
            // If >= 50, standard Indian e-commerce practice assumes grams (e.g. 100, 250, 500)
            if ($val >= 50) {
                return $val / 1000;
            }
            // If < 50, assumed already in kg (e.g. 0.5, 1, 2, 5)
            return $val;
        }

        return 0.0;
    }

    /**
     * Create a Shiprocket order for a store order and persist a Shipment row.
     */
    public function createShipment(Order $order): Shipment
    {
        $token = $this->getToken();

        // Ensure relations are freshly loaded
        $order->load(['items.product', 'items.variant', 'user']);

        $pickupLocation = Setting::get('shiprocket_pickup_location', 'Primary');
        $channelId = Setting::get('shiprocket_channel_id') ?: null;
        $defaultWeight = (float) Setting::get('shiprocket_default_weight', 0.5) ?: 0.5;
        $dimL = (float) Setting::get('shiprocket_pkg_length', 15) ?: 15.0;
        $dimB = (float) Setting::get('shiprocket_pkg_breadth', 12) ?: 12.0;
        $dimH = (float) Setting::get('shiprocket_pkg_height', 8) ?: 8.0;

        $isCod = strtolower((string) $order->payment_method) === 'cod';

        $items = [];
        $totalWeight = 0.0;
        foreach ($order->items as $item) {
            $unitPrice = (float) ($item->price ?? 0);
            $qty = (int) ($item->quantity ?? 1);

            // Resolve item raw weight from variant, variant name, product weight, or product name
            $rawWeight = null;
            if ($item->variant && !empty($item->variant->weight)) {
                $rawWeight = $item->variant->weight;
            } elseif (!empty($item->variant_name)) {
                $rawWeight = $item->variant_name;
            } elseif ($item->product && !empty($item->product->weight)) {
                $rawWeight = $item->product->weight;
            } elseif ($item->product && !empty($item->product->name)) {
                $rawWeight = $item->product->name;
            } elseif (!empty($item->product_name)) {
                $rawWeight = $item->product_name;
            }

            $parsedKg = $this->parseWeightToKg($rawWeight);
            $weightKg = $parsedKg > 0 ? $parsedKg : $defaultWeight;
            $totalWeight += $weightKg * $qty;

            $itemName = $item->variant_name 
                ?: ($item->variant?->name 
                ?: ($item->product_name ?: ($item->product?->name ?? 'Item')));

            $itemSku = $item->variant?->sku 
                ?: ($item->product?->sku ?? ('SKU-' . $item->id));

            $items[] = [
                'name' => mb_substr($itemName, 0, 100),
                'sku' => mb_substr($itemSku, 0, 50),
                'units' => $qty,
                'selling_price' => round($unitPrice, 2),
            ];
        }

        // Guarantee weight is strictly positive and meets minimum courier slab (at least 0.5 kg)
        $finalWeight = max(round($totalWeight, 2), $defaultWeight, 0.5);

        [$firstName, $lastName] = $this->splitName($order->shipping_name);

        // Sanitize phone number for Shiprocket (10 digits)
        $phone = preg_replace('/[^\d]/', '', (string) $order->shipping_phone);
        if (strlen($phone) > 10 && str_starts_with($phone, '91')) {
            $phone = substr($phone, 2);
        } elseif (strlen($phone) > 10 && str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }
        if (strlen($phone) < 10) {
            $phone = str_pad($phone, 10, '0', STR_PAD_RIGHT);
        }

        $address1 = trim((string) $order->shipping_address_line1);
        if (strlen($address1) < 10 && !empty($order->shipping_address_line2)) {
            $address1 .= ', ' . trim($order->shipping_address_line2);
        }

        $payload = [
            'order_id' => $order->order_number,
            'order_date' => $order->created_at->format('Y-m-d H:i'),
            'pickup_location' => $pickupLocation,
            'billing_customer_name' => $firstName ?: 'Customer',
            'billing_last_name' => $lastName ?: '.',
            'billing_address' => $address1 ?: 'Address Line',
            'billing_address_2' => $order->shipping_address_line2 ?? '',
            'billing_city' => $order->shipping_city ?: 'City',
            'billing_pincode' => $order->shipping_postal_code,
            'billing_state' => $order->shipping_state ?: 'State',
            'billing_country' => 'India',
            'billing_email' => $order->user?->email ?: Setting::get('site_email', 'orders@example.com'),
            'billing_phone' => $phone,
            'shipping_is_billing' => true,
            'order_items' => $items,
            'payment_method' => $isCod ? 'COD' : 'Prepaid',
            'sub_total' => round((float) $order->subtotal, 2),
            'length' => $dimL,
            'breadth' => $dimB,
            'height' => $dimH,
            'weight' => $finalWeight,
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
