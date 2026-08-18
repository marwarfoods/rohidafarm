<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\TrackOrder;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    use LogsActivity;

    protected $cartService;
    protected $delhiveryService;

    public function __construct(CartService $cartService, DelhiveryService $delhiveryService)
    {
        $this->cartService = $cartService;
        $this->delhiveryService = $delhiveryService;
    }

    /**
     * Create an order from current cart items.
     */
    public function createFromCart(array $shippingData, string $paymentMethod): Order
    {
        $totals = $this->cartService->getTotals($shippingData['state'] ?? null, $paymentMethod);
        $items = $this->cartService->getItems();

        if ($items->isEmpty()) {
            throw new \Exception("Cannot place order with an empty cart.");
        }

        return DB::transaction(function () use ($shippingData, $paymentMethod, $totals, $items) {
            $user = Auth::user();
            
            // 1. Deduct wallet balance if wallet payment chosen
            if ($paymentMethod === 'wallet') {
                if ($user->wallet_balance < $totals['total']) {
                    throw new \Exception("Insufficient wallet balance.");
                }
                $user->decrement('wallet_balance', $totals['total']);
                
                // Record wallet transaction
                $user->transactions()->create([
                    'amount' => $totals['total'],
                    'type' => 'debit',
                    'description' => 'Payment for order check-out.',
                ]);
            }

            // 2. Generate unique order number
            $orderNumber = 'RF-' . strtoupper(Str::random(4)) . '-' . time();

            // Add payment adjustment to shipping (if charge) or discount (if discount) to avoid migration
            $shippingCharges = $totals['shipping'];
            $discountAmount = $totals['discount'];
            if (($totals['payment_adj'] ?? 0) > 0) {
                $shippingCharges += $totals['payment_adj'];
            } elseif (($totals['payment_adj'] ?? 0) < 0) {
                $discountAmount += abs($totals['payment_adj']);
            }

            // 3. Create the order
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => $orderNumber,
                'status' => 'pending',
                'subtotal' => $totals['subtotal'],
                'tax' => $totals['tax'],
                'shipping_charges' => $shippingCharges,
                'coupon_code' => $totals['coupon'] ? $totals['coupon']->code : null,
                'discount_amount' => $discountAmount,
                'total' => $totals['total'],
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentMethod === 'wallet' ? 'paid' : 'pending',
                'shipping_name' => $shippingData['name'],
                'shipping_phone' => $shippingData['phone'],
                'shipping_address_line1' => $shippingData['address_line1'],
                'shipping_address_line2' => $shippingData['address_line2'] ?? null,
                'shipping_city' => $shippingData['city'],
                'shipping_state' => $shippingData['state'],
                'shipping_postal_code' => $shippingData['postal_code'],
                'shipping_country' => $shippingData['country'] ?? 'India',
                'estimated_delivery' => now()->addDays(5),
                'advance_amount' => $totals['cod_advance'] ?? 0,
                'cod_due_amount' => $totals['cod_due'] ?? 0,
            ]);

            // 4. Create order items & deduct stock
            foreach ($items as $item) {
                // Determine item stock and decrement
                if ($item->variant_id) {
                    $variant = ProductVariant::findOrFail($item->variant_id);
                    if ($variant->stock < $item->quantity) {
                        throw new \Exception("Product variant {$variant->name} is out of stock.");
                    }
                    $variant->decrement('stock', $item->quantity);
                } else {
                    $product = Product::findOrFail($item->product_id);
                    if ($product->stock < $item->quantity) {
                        throw new \Exception("Product {$product->name} is out of stock.");
                    }
                    $product->decrement('stock', $item->quantity);
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'product_name' => $item->product->name,
                    'variant_name' => $item->variant ? $item->variant->name : null,
                    'quantity' => $item->quantity,
                    'price' => $item->variant ? $item->variant->sale_price : $item->product->sale_price,
                    'total' => $item->subtotal,
                ]);
            }

            // 5. Increment coupon usage count if applied
            if ($totals['coupon']) {
                $totals['coupon']->increment('usage_count');
            }

            // 6. Create initial tracking entry
            TrackOrder::create([
                'order_id' => $order->id,
                'status' => 'order_placed',
                'description' => 'Order placed successfully. Awaiting payment authorization/processing.',
                'location' => 'System'
            ]);

            // 7. Trigger Delhivery shipment creation
            try {
                $this->delhiveryService->createShipment($order);
            } catch (\Exception $e) {
                logger()->error('Delhivery shipment creation failed: ' . $e->getMessage());
            }

            // 8. Log activity
            self::logActivity('checkout', "Placed order {$orderNumber} with total ₹{$totals['total']}", ['order_id' => $order->id]);

            return $order;
        });
    }

    /**
     * Create an order directly bypassing the cart.
     */
    public function createDirectOrder(Product $product, ?ProductVariant $variant, int $quantity, array $shippingData, string $paymentMethod): Order
    {
        $price = $variant ? $variant->sale_price : $product->sale_price;
        $subtotal = $price * $quantity;
        
        $shippingThreshold = $product->free_shipping_threshold ?? \App\Models\Setting::get('free_shipping_threshold', 499);
        $shippingChargeSetting = \App\Models\Setting::get('shipping_charges', 50);
        
        $shippingCharges = ($subtotal >= $shippingThreshold) ? 0 : $shippingChargeSetting;
        $tax = 0; // Simplified
        $total = $subtotal + $shippingCharges + $tax;

        return DB::transaction(function () use ($shippingData, $paymentMethod, $subtotal, $shippingCharges, $tax, $total, $product, $variant, $quantity, $price) {
            $user = Auth::user();

            // 2. Generate unique order number
            $orderNumber = 'RF-' . strtoupper(Str::random(4)) . '-' . time();

            // 3. Create the order
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => $orderNumber,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_charges' => $shippingCharges,
                'coupon_code' => null,
                'discount_amount' => 0,
                'total' => $total,
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending',
                'shipping_name' => $shippingData['name'],
                'shipping_phone' => $shippingData['phone'],
                'shipping_address_line1' => $shippingData['address_line1'],
                'shipping_address_line2' => $shippingData['address_line2'] ?? null,
                'shipping_city' => $shippingData['city'],
                'shipping_state' => $shippingData['state'],
                'shipping_postal_code' => $shippingData['postal_code'],
                'shipping_country' => $shippingData['country'] ?? 'India',
                'estimated_delivery' => now()->addDays(5),
            ]);

            // 4. Create order items & deduct stock
            if ($variant) {
                if ($variant->stock < $quantity) {
                    throw new \Exception("Product variant {$variant->name} is out of stock.");
                }
                $variant->decrement('stock', $quantity);
            } else {
                if ($product->stock < $quantity) {
                    throw new \Exception("Product {$product->name} is out of stock.");
                }
                $product->decrement('stock', $quantity);
            }

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'variant_id' => $variant ? $variant->id : null,
                'product_name' => $product->name,
                'variant_name' => $variant ? $variant->name : null,
                'quantity' => $quantity,
                'price' => $price,
                'total' => $subtotal,
            ]);

            // 6. Create initial tracking entry
            TrackOrder::create([
                'order_id' => $order->id,
                'status' => 'order_placed',
                'description' => 'Order placed successfully. Awaiting payment authorization/processing.',
                'location' => 'System'
            ]);

            // 8. Log activity
            self::logActivity('checkout_direct', "Placed direct order {$orderNumber} with total ₹{$total}", ['order_id' => $order->id]);

            return $order;
        });
    }

    /**
     * Cancel an order.
     */
    public function cancel(int $orderId, string $reason = 'Cancelled by customer'): Order
    {
        return DB::transaction(function () use ($orderId, $reason) {
            $order = Order::with('items')->findOrFail($orderId);

            if (!$order->canCancel()) {
                throw new \Exception("This order cannot be cancelled as it is already " . $order->status);
            }

            // 1. Restore stock
            foreach ($order->items as $item) {
                if ($item->variant_id) {
                    ProductVariant::where('id', $item->variant_id)->increment('stock', $item->quantity);
                } else {
                    Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                }
            }

            // 2. Refund to wallet if paid via wallet / online gateways
            if ($order->payment_status === 'paid') {
                $user = $order->user;
                $user->increment('wallet_balance', $order->total);
                
                // Record wallet transaction
                $user->transactions()->create([
                    'amount' => $order->total,
                    'type' => 'credit',
                    'description' => "Refund for cancelled order #{$order->order_number}.",
                ]);

                $order->update(['payment_status' => 'refunded']);
            }

            // 3. Update order status
            $order->update(['status' => 'cancelled']);

            // 4. Create tracking log
            TrackOrder::create([
                'order_id' => $order->id,
                'status' => 'cancelled',
                'description' => "Order cancelled: {$reason}",
                'location' => 'System'
            ]);

            // 5. Log activity
            self::logActivity('cancel_order', "Cancelled order #{$order->order_number}", ['order_id' => $order->id, 'reason' => $reason]);

            // 6. Send status email
            try {
                \Illuminate\Support\Facades\Mail::to($order->user->email)->send(new \App\Mail\OrderStatusMail($order, "Order cancelled: {$reason}"));
            } catch (\Exception $e) {
                logger()->error('Failed to send OrderStatusMail: ' . $e->getMessage());
            }

            return $order;
        });
    }

    /**
     * Completely abort an order and delete it (e.g. when user exits Razorpay payment).
     * This restores stock and removes any trace of the order.
     */
    public function abort(int $orderId): void
    {
        DB::transaction(function () use ($orderId) {
            $order = Order::with('items')->find($orderId);
            
            if (!$order) {
                return;
            }

            // 1. Restore stock
            foreach ($order->items as $item) {
                if ($item->variant_id) {
                    ProductVariant::where('id', $item->variant_id)->increment('stock', $item->quantity);
                } else {
                    Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                }
            }

            // 2. Delete tracking updates
            $order->trackingUpdates()->delete();

            // 3. Delete order items
            $order->items()->delete();

            // 4. Delete the order itself (hard delete to remove trace)
            $order->forceDelete();
        });
    }

    /**
     * Update order status (Admin operation).
     */
    public function updateStatus(int $orderId, string $status, ?string $description = null, ?string $trackingNumber = null, ?string $trackingCarrier = null): Order
    {
        return DB::transaction(function () use ($orderId, $status, $description, $trackingNumber, $trackingCarrier) {
            $order = Order::findOrFail($orderId);
            
            $updateData = ['status' => $status];

            // Save tracking details if provided
            if ($trackingNumber) {
                $updateData['tracking_number'] = $trackingNumber;
                $updateData['tracking_url'] = "https://www.delhivery.com/track/package/{$trackingNumber}";
                $updateData['tracking_carrier'] = $trackingCarrier ?: $order->tracking_carrier;
            } elseif ($trackingCarrier) {
                $updateData['tracking_carrier'] = $trackingCarrier;
            }

            $order->update($updateData);

            // Create tracking update
            TrackOrder::create([
                'order_id' => $order->id,
                'status' => $this->mapTrackStatus($status),
                'description' => $description ?: "Order status updated to " . ucfirst($status),
                'location' => 'Warehouse'
            ]);

            // If order status is delivered, update payment status if it was COD
            if ($status === 'delivered' && $order->payment_method === 'cod') {
                $order->update(['payment_status' => 'paid']);
            }

            // Log activity
            self::logActivity('update_order_status', "Updated order #{$order->order_number} to {$status}", ['order_id' => $order->id, 'status' => $status]);

            // Send status email
            try {
                \Illuminate\Support\Facades\Mail::to($order->user->email)->send(new \App\Mail\OrderStatusMail($order, $description ?: "Your order is now " . ucfirst($status)));
            } catch (\Exception $e) {
                logger()->error('Failed to send OrderStatusMail: ' . $e->getMessage());
            }

            return $order;
        });
    }

    /**
     * Helper to map Order status to Track status.
     */
    protected function mapTrackStatus(string $status): string
    {
        return match ($status) {
            'pending' => 'order_placed',
            'processing' => 'packed',
            'shipped' => 'shipped',
            'delivered' => 'delivered',
            'cancelled' => 'cancelled',
            default => 'processing',
        };
    }
}
