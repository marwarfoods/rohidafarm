<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Maps a product (+ optional variant) to the variant_id used in Shiprocket Checkout.
 * The primary key is the Fastrr variant_id.
 */
class ShiprocketCheckoutVariant extends Model
{
    protected $fillable = ['variant_key', 'product_id', 'product_variant_id'];

    public static function keyFor(int $productId, ?int $variantId): string
    {
        return 'p' . $productId . '-v' . ($variantId ?: 0);
    }

    /**
     * Get (or create) the Fastrr variant_id for a product / variant.
     */
    public static function idFor(int $productId, ?int $variantId): int
    {
        return (int) static::firstOrCreate(
            ['variant_key' => static::keyFor($productId, $variantId)],
            ['product_id' => $productId, 'product_variant_id' => $variantId ?: null]
        )->id;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
