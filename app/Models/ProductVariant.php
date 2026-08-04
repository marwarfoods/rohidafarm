<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'name', 'sku', 'weight', 'stock', 'mrp', 'sale_price', 'max_cart_qty'];

    protected $casts = [
        'mrp' => 'decimal:2',
        'sale_price' => 'decimal:2',
    ];

    /**
     * Product relation.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate discount percentage.
     */
    public function discountPercentage(): int
    {
        if ($this->mrp > $this->sale_price && $this->mrp > 0) {
            return round((($this->mrp - $this->sale_price) / $this->mrp) * 100);
        }
        return 0;
    }
}
