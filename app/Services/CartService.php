<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\DeliveryCharge;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Get items in the cart.
     */
    public function getItems()
    {
        if (Auth::check()) {
            $items = Cart::with(['product.images', 'variant'])
                ->where('user_id', Auth::id())
                ->get();
            
            foreach ($items as $item) {
                $item->cart_id = $item->id;
            }
            
            return $items;
        }

        $sessionCart = Session::get('cart', []);
        $items = collect();

        foreach ($sessionCart as $key => $item) {
            $product = Product::with('images')->find($item['product_id']);
            if (!$product) continue;
            
            $variant = null;
            if (!empty($item['variant_id'])) {
                $variant = ProductVariant::find($item['variant_id']);
            }

            $cartItem = new Cart([
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'],
                'quantity' => $item['quantity'],
                'product' => $product,
                'variant' => $variant
            ]);
            $cartItem->cart_id = $key;
            $items->push($cartItem);
        }

        return $items;
    }

    /**
     * Add an item to the cart.
     */
    public function add(int $productId, ?int $variantId = null, int $quantity = 1): void
    {
        $product = Product::findOrFail($productId);
        
        // Auto-resolve default variant if variantId is missing but product has variants
        if (!$variantId && $product->variants()->exists()) {
            $variantId = $product->variants()->where('stock', '>', 0)->first()?->id ?? $product->variants()->first()?->id;
        }

        // Validate stock & max cart qty
        $availableStock = $product->stock;
        $maxQty = null;
        if ($variantId) {
            $variant = ProductVariant::findOrFail($variantId);
            $availableStock = $variant->stock;
            $maxQty = $variant->max_cart_qty;
        }

        if ($maxQty !== null && $quantity > $maxQty) {
            throw new \Exception("Cannot add more than {$maxQty} of this variant to cart.");
        }

        if (Auth::check()) {
            $cartItem = Cart::where('user_id', Auth::id())
                ->where('product_id', $productId)
                ->where('variant_id', $variantId)
                ->first();

            $newQty = $cartItem ? $cartItem->quantity + $quantity : $quantity;
            
            if ($maxQty !== null && $newQty > $maxQty) {
                throw new \Exception("Cannot add more than {$maxQty} of this variant to cart.");
            }
            if ($newQty > $availableStock) {
                throw new \Exception("Requested quantity exceeds available stock!");
            }

            if ($cartItem) {
                $cartItem->update(['quantity' => $newQty]);
            } else {
                Cart::create([
                    'user_id' => Auth::id(),
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'quantity' => $quantity,
                ]);
            }
        } else {
            $cart = Session::get('cart', []);
            $key = $productId . '_' . ($variantId ?? '0');

            $newQty = isset($cart[$key]) ? $cart[$key]['quantity'] + $quantity : $quantity;
            
            if ($maxQty !== null && $newQty > $maxQty) {
                throw new \Exception("Cannot add more than {$maxQty} of this variant to cart.");
            }
            if ($newQty > $availableStock) {
                throw new \Exception("Requested quantity exceeds available stock!");
            }

            $cart[$key] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $newQty
            ];

            Session::put('cart', $cart);
        }
    }

    /**
     * Update quantity of a cart item.
     */
    public function update($cartId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->remove($cartId);
            return;
        }

        if (Auth::check()) {
            $cartItem = Cart::with(['product', 'variant'])->where('user_id', Auth::id())->findOrFail($cartId);
            
            // Validate stock
            $stock = $cartItem->variant ? $cartItem->variant->stock : $cartItem->product->stock;
            $maxQty = $cartItem->variant ? $cartItem->variant->max_cart_qty : null;

            if ($maxQty !== null && $quantity > $maxQty) {
                throw new \Exception("Cannot add more than {$maxQty} of this variant to cart.");
            }
            if ($quantity > $stock) {
                throw new \Exception("Requested quantity exceeds available stock!");
            }

            $cartItem->update(['quantity' => $quantity]);
        } else {
            $cart = Session::get('cart', []);
            if (isset($cart[$cartId])) {
                $productId = $cart[$cartId]['product_id'];
                $variantId = $cart[$cartId]['variant_id'];

                $product = Product::findOrFail($productId);
                $stock = $product->stock;
                $maxQty = null;

                if ($variantId) {
                    $variant = ProductVariant::findOrFail($variantId);
                    $stock = $variant->stock;
                    $maxQty = $variant->max_cart_qty;
                }

                if ($maxQty !== null && $quantity > $maxQty) {
                    throw new \Exception("Cannot add more than {$maxQty} of this variant to cart.");
                }
                if ($quantity > $stock) {
                    throw new \Exception("Requested quantity exceeds available stock!");
                }

                $cart[$cartId]['quantity'] = $quantity;
                Session::put('cart', $cart);
            }
        }
    }

    /**
     * Remove an item from the cart.
     */
    public function remove($cartId): void
    {
        if (Auth::check()) {
            $item = Cart::where('user_id', Auth::id())->find($cartId);
            if ($item) {
                $item->delete();
            }
        } else {
            $cart = Session::get('cart', []);
            if (isset($cart[$cartId])) {
                unset($cart[$cartId]);
                Session::put('cart', $cart);
            }
        }
    }

    /**
     * Sync guest session cart to user database after login.
     */
    public function syncSessionToDb(): void
    {
        if (!Auth::check()) return;

        $sessionCart = Session::get('cart', []);
        foreach ($sessionCart as $item) {
            $this->add($item['product_id'], $item['variant_id'], $item['quantity']);
        }

        Session::forget('cart');
    }

    /**
     * Clear all items in the cart.
     */
    public function clear(): void
    {
        if (Auth::check()) {
            Cart::where('user_id', Auth::id())->delete();
        } else {
            Session::forget('cart');
        }
        Session::forget('applied_coupon');
    }

    /**
     * Apply a discount coupon.
     */
    public function applyCoupon(string $code): Coupon
    {
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            throw new \Exception("Invalid coupon code.");
        }

        // Validate basic coupon properties (active, dates, usage limit)
        if (!$coupon->is_active) {
            throw new \Exception("This coupon is no longer active.");
        }
        $now = now();
        if ($coupon->active_from && $coupon->active_from->gt($now)) {
            throw new \Exception("This coupon is not active yet.");
        }
        if ($coupon->active_until && $coupon->active_until->lt($now)) {
            throw new \Exception("This coupon has expired.");
        }
        if ($coupon->usage_limit !== null && $coupon->usage_count >= $coupon->usage_limit) {
            throw new \Exception("This coupon has reached its usage limit.");
        }

        $items = $this->getItems();
        $eligibleSubtotal = 0.00;

        foreach ($items as $item) {
            $isEligible = false;
            if ($coupon->target_type === 'all') {
                $isEligible = true;
            } elseif ($coupon->target_type === 'products') {
                $isEligible = in_array($item->product_id, $coupon->target_ids ?? []);
            } elseif ($coupon->target_type === 'categories') {
                $isEligible = in_array($item->product->category_id, $coupon->target_ids ?? []);
            }

            if ($isEligible) {
                $price = $item->variant ? $item->variant->sale_price : $item->product->sale_price;
                $eligibleSubtotal += $price * $item->quantity;
            }
        }

        if ($eligibleSubtotal <= 0) {
            throw new \Exception("This coupon is not applicable to any products in your cart.");
        }

        if ($eligibleSubtotal < $coupon->min_amount) {
            throw new \Exception("Minimum eligible cart value of ₹{$coupon->min_amount} required.");
        }

        Session::put('applied_coupon', $coupon->code);
        return $coupon;
    }

    /**
     * Remove applied coupon.
     */
    public function removeCoupon(): void
    {
        Session::forget('applied_coupon');
    }

    /**
     * Get coupon details if applied.
     */
    public function getAppliedCoupon(): ?Coupon
    {
        $code = Session::get('applied_coupon');
        if (!$code) return null;

        return Coupon::where('code', $code)->where('is_active', true)->first();
    }

    /**
     * Get detailed pricing summary.
     */
    public function getTotals(?string $state = null, ?string $paymentMethod = null): array
    {
        $items = $this->getItems();
        $subtotal = 0.00;
        $productSubtotals = [];

        foreach ($items as $item) {
            $price = $item->variant ? $item->variant->sale_price : $item->product->sale_price;
            $lineTotal = $price * $item->quantity;
            $subtotal += $lineTotal;
            $productSubtotals[$item->product_id] = ($productSubtotals[$item->product_id] ?? 0) + $lineTotal;
        }

        // Coupon calculation
        $discount = 0.00;
        $coupon = $this->getAppliedCoupon();
        if ($coupon) {
            $eligibleSubtotal = 0.00;
            foreach ($items as $item) {
                $isEligible = false;
                if ($coupon->target_type === 'all') {
                    $isEligible = true;
                } elseif ($coupon->target_type === 'products') {
                    $isEligible = in_array($item->product_id, $coupon->target_ids ?? []);
                } elseif ($coupon->target_type === 'categories') {
                    $isEligible = in_array($item->product->category_id, $coupon->target_ids ?? []);
                }

                if ($isEligible) {
                    $price = $item->variant ? $item->variant->sale_price : $item->product->sale_price;
                    $eligibleSubtotal += $price * $item->quantity;
                }
            }

            if ($eligibleSubtotal > 0 && $eligibleSubtotal >= $coupon->min_amount) {
                $discount = $coupon->calculateDiscount($eligibleSubtotal);
            } else {
                Session::forget('applied_coupon'); // clean invalid coupon
                $coupon = null;
            }
        }

        $taxableAmount = max(0, $subtotal - $discount);

        // Tax calculation dynamically from Settings
        $tax = 0.00;
        $gstEnabled = filter_var(\App\Models\Setting::get('gst_enabled', 'false'), FILTER_VALIDATE_BOOLEAN);
        
        if ($gstEnabled && $taxableAmount > 0) {
            $taxMode = \App\Models\Setting::get('tax_mode', 'all_india');
            $taxPercent = 0;

            if ($taxMode === 'all_india') {
                $taxPercent = (float) \App\Models\Setting::get('tax_all_india_percent', 0);
            } elseif ($taxMode === 'state_wise') {
                $stateMap = \App\Models\Setting::get('tax_state_wise');
                if (is_string($stateMap)) {
                    $stateMap = json_decode($stateMap, true);
                }
                
                // If a specific state is provided (e.g. from checkout form)
                if ($state && is_array($stateMap)) {
                    // Try exact match or case-insensitive match
                    foreach ($stateMap as $key => $val) {
                        if (strtolower(trim($key)) === strtolower(trim($state))) {
                            $taxPercent = (float) $val;
                            break;
                        }
                    }
                }
                // If no state provided or no match, tax percent remains 0 or fallback
            }

            $tax = round($taxableAmount * ($taxPercent / 100), 2);
        }

        // Shipping Charges
        $shipping = 0.00;
        $freeShipping = false;

        // Check product-specific free shipping threshold
        foreach ($items as $item) {
            if ($item->product->free_shipping_threshold > 0 && $productSubtotals[$item->product_id] >= $item->product->free_shipping_threshold) {
                $freeShipping = true;
                break;
            }
        }

        if (!$freeShipping) {
            $deliveryCharge = DeliveryCharge::where('is_active', true)->first();
            if ($deliveryCharge && $taxableAmount > 0) {
                if ($taxableAmount < $deliveryCharge->min_order_amount) {
                    $shipping = (float) $deliveryCharge->charge_amount;
                }
            }
        }

        // Payment Mode Adjustments
        $paymentAdjustment = 0.00;
        if ($paymentMethod === 'cod') {
            $codCharge = (float) \App\Models\Setting::get('cod_extra_charge', 0);
            $paymentAdjustment = $codCharge; // positive means extra charge
        } elseif ($paymentMethod === 'razorpay' || $paymentMethod === 'stripe') {
            $onlineDiscountPercent = (float) \App\Models\Setting::get('online_discount_percent', 0);
            if ($onlineDiscountPercent > 0) {
                // Apply discount on the taxable amount (or final amount, let's say taxable amount)
                $paymentAdjustment = -round($taxableAmount * ($onlineDiscountPercent / 100), 2); // negative means discount
            }
        }

        $total = $taxableAmount + $tax + $shipping + $paymentAdjustment;

        $codAdvance = 0.00;
        $codDue = 0.00;
        if ($paymentMethod === 'cod') {
            $advanceSetting = (float) \App\Models\Setting::get('cod_advance_amount', 0);
            if ($advanceSetting > 0) {
                $codAdvance = min($advanceSetting, $total);
                $codDue = max(0, $total - $codAdvance);
            } else {
                $codDue = $total;
            }
        }

        return [
            'items_count' => $items->sum('quantity'),
            'subtotal'    => round($subtotal, 2),
            'discount'    => round($discount, 2),
            'tax'         => round($tax, 2),
            'shipping'    => round($shipping, 2),
            'payment_adj' => round($paymentAdjustment, 2),
            'total'       => round($total, 2),
            'coupon'      => $coupon,
            'cod_advance' => round($codAdvance, 2),
            'cod_due'     => round($codDue, 2),
        ];
    }

    /**
     * Get totals prior to coupon application.
     */
    protected function getTotalsWithoutCoupon(): array
    {
        $items = $this->getItems();
        $subtotal = 0.00;

        foreach ($items as $item) {
            $price = $item->variant ? $item->variant->sale_price : $item->product->sale_price;
            $subtotal += $price * $item->quantity;
        }

        return ['subtotal' => $subtotal];
    }

    /**
     * Get total quantity count of all items in the cart.
     */
    public function getQtyCount(): int
    {
        return (int) $this->getItems()->sum('quantity');
    }
}
