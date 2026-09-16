<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'product_id',
        'variant_id',
        'quantity',
    ];

    /**
     * User relation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Product relation.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Variant relation.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /**
     * Helper to get unit price of cart item.
     */
    public function getUnitPriceAttribute(): float
    {
        if ($this->variant && (float)$this->variant->sale_price > 0) {
            return (float) $this->variant->sale_price;
        }
        return (float) ($this->product?->sale_price ?? 0);
    }

    /**
     * Helper to get subtotal of cart item.
     */
    public function getSubtotalAttribute(): float
    {
        return (float) ($this->unit_price * $this->quantity);
    }
}
