<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'weight',
        'image_path',
        'gallery_images',
        'stock',
        'mrp',
        'sale_price',
        'max_cart_qty',
    ];

    protected $casts = [
        'mrp' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'gallery_images' => 'array',
    ];

    /**
     * Get all images for this variant (main image + gallery images).
     */
    public function getAllImagesAttribute(): array
    {
        $images = [];
        if (!empty($this->image_path)) {
            $images[] = $this->image_path;
        }
        if (!empty($this->gallery_images) && is_array($this->gallery_images)) {
            foreach ($this->gallery_images as $g) {
                if (!empty($g) && !in_array($g, $images)) {
                    $images[] = $g;
                }
            }
        }
        return $images;
    }

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
