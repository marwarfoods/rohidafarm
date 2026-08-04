<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'min_amount',
        'max_discount',
        'active_from',
        'active_until',
        'usage_limit',
        'usage_count',
        'is_active',
        'target_type',
        'target_ids',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'active_from' => 'datetime',
        'active_until' => 'datetime',
        'discount_value' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'target_ids' => 'array',
    ];

    /**
     * Check if coupon is currently valid.
     */
    public function isValid(?float $cartTotal = 0.00): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        if ($this->active_from && $this->active_from->gt($now)) {
            return false;
        }
        if ($this->active_until && $this->active_until->lt($now)) {
            return false;
        }

        if ($this->usage_limit !== null && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        if ($cartTotal < $this->min_amount) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount amount for a given subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        $discount = 0.00;
        if ($this->discount_type === 'percentage') {
            $discount = ($subtotal * $this->discount_value) / 100;
            if ($this->max_discount !== null && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }
        } elseif ($this->discount_type === 'fixed') {
            $discount = $this->discount_value;
        }

        return min($discount, $subtotal); // discount cannot exceed subtotal
    }
}
