<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShiprocketCheckoutOrder;
use App\Models\ShiprocketCheckoutVariant;
use App\Models\TrackOrder;
use App\Models\User;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Turns a Shiprocket Checkout order into a Laravel order.
 *
 * The order webhook carries no signature in the official docs, so the payload is never
 * trusted: the order is always re-fetched from the HMAC-authenticated Order Details API.
 */
class ShiprocketCheckoutOrderProcessor
{
    use LogsActivity;

    public function __construct(private ShiprocketCheckoutService $checkout)
    {
    }

    /**
     * Verify a checkout order with Shiprocket and create the Laravel order if it succeeded.
     * Safe to call repeatedly (webhook retries, return redirect, reconcile job).
     */
    public function sync(string $checkoutOrderId, string $trigger = 'webhook'): ShiprocketCheckoutOrder
    {
        $record = ShiprocketCheckoutOrder::firstOrCreate(
            ['checkout_order_id' => $checkoutOrderId],
            ['source' => 'webhook', 'status' => 'CREATED']
        );

        if ($record->order_id) {
            return $record; // already processed — duplicate webhook / redirect
        }

        $res = $this->checkout->orderDetails($checkoutOrderId);
        if (!$res['ok'] || !is_array($res['result'])) {
            $record->update(['last_error' => Str::limit('Order Details failed: ' . ($res['error'] ?? 'empty result'), 490)]);
            $this->checkout->recordError("Order {$checkoutOrderId}: " . ($res['error'] ?? 'Order Details returned no result'));

            return $record;
        }

        $details = $res['result'];
        if ((string) ($details['order_id'] ?? '') !== $checkoutOrderId) {
            $record->update(['last_error' => 'Order Details returned a different order_id.']);

            return $record;
        }

        $record->update([
            'status' => strtoupper((string) ($details['status'] ?? 'CREATED')),
            'fastrr_order_id' => $details['fastrr_order_id'] ?? null,
            'platform_order_id' => $details['platform_order_id'] ?? null,
            'payment_type' => $details['payment_type'] ?? null,
            'payment_status' => $details['payment_status'] ?? null,
            'total_amount_payable' => isset($details['total_amount_payable']) ? round((float) $details['total_amount_payable'], 2) : null,
            'details' => $details,
            'verified_at' => now(),
            'last_error' => null,
        ]);

        if ($record->status !== 'SUCCESS') {
            return $record;
        }

        $order = null;
        try {
            $order = $this->createOrder($record->id);
        } catch (\Throwable $e) {
            Log::error('Shiprocket Checkout order creation failed', ['checkout_order_id' => $checkoutOrderId, 'error' => $e->getMessage()]);
            $record->update(['last_error' => Str::limit('Order creation failed: ' . $e->getMessage(), 490)]);
            $this->checkout->recordError("Order {$checkoutOrderId}: " . $e->getMessage());
        }

        if ($order) {
            $this->afterOrderCreated($order, $trigger);
        }

        return $record->fresh();
    }

    /**
     * Create the Laravel order inside a transaction with a row lock (no duplicates).
     * Returns null when another request already created it.
     */
    private function createOrder(int $recordId): ?Order
    {
        return DB::transaction(function () use ($recordId) {
            $record = ShiprocketCheckoutOrder::whereKey($recordId)->lockForUpdate()->first();
            if (!$record || $record->order_id) {
                return null;
            }

            $d = $record->details;
            $lines = $this->resolveLines($d['cart_data']['items'] ?? []);
            if (!$lines) {
                throw new \RuntimeException('None of the ordered variant_ids exist in the local catalog mapping.');
            }

            $shipping = $d['shipping_address'] ?? [];
            $user = $this->resolveUser($record, $d, $shipping);
            $isCod = strtoupper((string) ($d['payment_type'] ?? '')) === 'CASH_ON_DELIVERY';
            $paid = !$isCod && strcasecmp((string) ($d['payment_status'] ?? ''), 'Success') === 0;

            $itemsSubtotal = collect($lines)->sum(fn ($l) => $l['price'] * $l['quantity']);
            $subtotal = isset($d['subtotal_price']) ? (float) $d['subtotal_price'] : $itemsSubtotal;
            $total = isset($d['total_amount_payable']) ? round((float) $d['total_amount_payable'], 2) : $subtotal;

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'RF-' . strtoupper(Str::random(4)) . '-' . time(),
                'status' => 'pending',
                'subtotal' => round($subtotal, 2),
                'tax' => 0,
                'shipping_charges' => round((float) ($d['shipping_charges'] ?? 0) + (float) ($d['cod_charges'] ?? 0), 2),
                'coupon_code' => !empty($d['coupon_codes']) ? Str::limit(implode(',', (array) $d['coupon_codes']), 250, '') : null,
                'discount_amount' => round((float) ($d['total_discount'] ?? 0), 2),
                'total' => max(0, $total),
                'payment_method' => $isCod ? 'cod' : 'shiprocket_checkout',
                'payment_status' => $paid ? 'paid' : 'pending',
                'shipping_name' => trim(($shipping['first_name'] ?? '') . ' ' . ($shipping['last_name'] ?? '')) ?: ($user->name ?: 'Customer'),
                'shipping_phone' => (string) ($shipping['phone'] ?? $d['phone'] ?? $user->phone ?? ''),
                'shipping_address_line1' => (string) ($shipping['line1'] ?? ''),
                'shipping_address_line2' => trim(implode(', ', array_filter([$shipping['line2'] ?? null, $shipping['landmark'] ?? null]))) ?: null,
                'shipping_city' => (string) ($shipping['city'] ?? ''),
                'shipping_state' => (string) ($shipping['state'] ?? ''),
                'shipping_postal_code' => (string) ($shipping['pincode'] ?? ''),
                'shipping_country' => (string) ($shipping['country'] ?? 'India'),
                'estimated_delivery' => !empty($d['edd']) ? \Illuminate\Support\Carbon::parse($d['edd']) : now()->addDays(5),
                'cod_due_amount' => $isCod ? max(0, $total) : 0,
            ]);

            foreach ($lines as $line) {
                $this->deductStock($line);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $line['product']->id,
                    'variant_id' => $line['variant']?->id,
                    'product_name' => $line['product']->name,
                    'variant_name' => $line['variant']?->name,
                    'quantity' => $line['quantity'],
                    'price' => $line['price'],
                    'total' => round($line['price'] * $line['quantity'], 2),
                ]);
            }

            foreach ((array) ($d['payments'] ?? []) as $p) {
                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => Str::limit('shiprocket_checkout:' . strtolower(($p['gateway'] ?? '') . '/' . ($p['payment_method'] ?? '')), 250, ''),
                    'transaction_id' => $p['pg_transaction_id'] ?? $p['txn_id'] ?? null,
                    'amount' => round((float) ($p['amount_received'] ?? $p['amount'] ?? 0), 2),
                    'status' => match (strtolower((string) ($p['payment_status'] ?? ''))) {
                        'success' => 'successful',
                        'failed' => 'failed',
                        default => 'pending',
                    },
                    'payload' => $p,
                ]);
            }

            TrackOrder::create([
                'order_id' => $order->id,
                'status' => 'order_placed',
                'description' => 'Order placed via Shiprocket Checkout (' . ($isCod ? 'Cash on Delivery' : 'Prepaid') . ').',
                'location' => 'System',
            ]);

            $record->update(['order_id' => $order->id, 'user_id' => $user->id, 'processed_at' => now()]);

            self::logActivity('checkout_shiprocket', "Shiprocket Checkout order {$order->order_number} (Fastrr {$record->checkout_order_id}) total ₹{$order->total}", ['order_id' => $order->id]);

            return $order;
        });
    }

    /**
     * Push to existing Shiprocket Shipping + notifications (outside the DB transaction).
     */
    private function afterOrderCreated(Order $order, string $trigger): void
    {
        // Stock changed via query builder (no model events) — push new quantities to Shiprocket Checkout.
        foreach ($order->items()->pluck('product_id')->unique() as $productId) {
            ShiprocketCheckoutCatalogSync::product((int) $productId);
        }

        if ($this->checkout->shouldPushToShipping()) {
            $shipping = app(ShiprocketService::class);
            if ($shipping->isConfigured()) {
                try {
                    $shipping->createShipment($order);
                } catch (\Throwable $e) {
                    logger()->error('Shiprocket shipment for Shiprocket Checkout order failed: ' . $e->getMessage());
                    $order->update(['shipment_status' => 'Shipment Failed - Needs Manual Booking']);
                }
            }
        }

        try {
            if ($order->user?->email && !str_ends_with($order->user->email, '.invalid')) {
                Mail::to($order->user->email)->send(new \App\Mail\OrderPlacedMail($order));
            }
        } catch (\Throwable $e) {
            logger()->error('Failed to send OrderPlacedMail: ' . $e->getMessage());
        }

        try {
            $adminEmails = User::where('role', 'admin')->pluck('email')->toArray();
            $primary = \App\Models\Setting::get('contact_email_1') ?: \App\Models\Setting::get('contact_email');
            if ($primary && !in_array($primary, $adminEmails)) {
                $adminEmails[] = $primary;
            }
            if ($adminEmails) {
                Mail::to($adminEmails)->send(new \App\Mail\AdminNewOrderMail($order));
            }
        } catch (\Throwable $e) {
            logger()->error('Failed to send AdminNewOrderMail: ' . $e->getMessage());
        }
    }

    /**
     * Map Fastrr variant_ids back to local products/variants.
     */
    private function resolveLines(array $items): array
    {
        $lines = [];
        foreach ($items as $item) {
            $map = ShiprocketCheckoutVariant::with(['product', 'variant'])->find((int) ($item['variant_id'] ?? 0));
            $qty = max(1, (int) ($item['quantity'] ?? 1));
            if (!$map || !$map->product) {
                Log::warning('Shiprocket Checkout: unknown variant_id in order', ['variant_id' => $item['variant_id'] ?? null]);
                continue;
            }
            $variant = $map->product_variant_id ? $map->variant : null;
            $price = $variant && (float) $variant->sale_price > 0 ? (float) $variant->sale_price : (float) $map->product->sale_price;
            $lines[] = ['product' => $map->product, 'variant' => $variant, 'quantity' => $qty, 'price' => $price];
        }

        return $lines;
    }

    /**
     * The customer has already paid/confirmed on Shiprocket, so never reject the order for stock —
     * deduct what is available and flag oversells in the log.
     */
    private function deductStock(array $line): void
    {
        $model = $line['variant'] ?: $line['product'];
        $table = $line['variant'] ? (new ProductVariant)->getTable() : (new Product)->getTable();
        $current = (int) DB::table($table)->where('id', $model->id)->lockForUpdate()->value('stock');

        if ($current < $line['quantity']) {
            Log::warning('Shiprocket Checkout oversell', ['table' => $table, 'id' => $model->id, 'stock' => $current, 'qty' => $line['quantity']]);
        }
        $model->newQuery()->whereKey($model->id)->update(['stock' => max(0, $current - $line['quantity'])]);
    }

    private function resolveUser(ShiprocketCheckoutOrder $record, array $d, array $shipping): User
    {
        if ($record->user_id && ($user = User::find($record->user_id))) {
            return $user;
        }

        $email = strtolower(trim((string) ($d['email'] ?? $shipping['email'] ?? '')));
        $phone = preg_replace('/\D/', '', (string) ($d['phone'] ?? $shipping['phone'] ?? ''));
        $phone = strlen($phone) > 10 ? substr($phone, -10) : $phone;

        $user = null;
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $user = User::withTrashed()->where('email', $email)->first();
        }
        if (!$user && $phone) {
            $user = User::withTrashed()->where('phone', $phone)->orderBy('id')->first();
        }
        if ($user) {
            if ($user->trashed()) {
                $user->restore();
            }

            return $user;
        }

        $validEmail = filter_var($email, FILTER_VALIDATE_EMAIL);
        $rawPassword = Str::random(12);
        $user = User::create([
            'name' => trim(($shipping['first_name'] ?? '') . ' ' . ($shipping['last_name'] ?? '')) ?: 'Customer',
            // users.email is required; without an email use a non-deliverable placeholder.
            'email' => $validEmail ? $email : 'fastrr-' . ($phone ?: Str::random(8)) . '@checkout.invalid',
            'phone' => $phone ?: null,
            'password' => bcrypt($rawPassword),
            'role' => 'customer',
            'wallet_balance' => 0.00,
        ]);

        if ($validEmail) {
            try {
                Mail::to($user->email)->send(new \App\Mail\NewAccountMail($user, $rawPassword));
            } catch (\Throwable $e) {
                logger()->error('Failed to send NewAccountMail: ' . $e->getMessage());
            }
        }

        return $user;
    }
}
