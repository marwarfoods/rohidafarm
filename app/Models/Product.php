<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasUuid, SoftDeletes;

    protected $fillable = [
        'uuid',
        'category_id',
        'sub_category_id',
        'brand_id',
        'name',
        'slug',
        'sku',
        'weight',
        'stock',
        'mrp',
        'sale_price',
        'is_bilona',
        'is_organic',
        'is_featured',
        'is_trending',
        'is_best_seller',
        'is_new_arrival',
        'short_description',
        'description',
        'benefits',
        'ingredients',
        'nutrition_facts',
        'how_to_use',
        'rating',
        'reviews_count',
        'is_active',
        'free_shipping_threshold',
        'display_coupons',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'is_bilona' => 'boolean',
        'is_organic' => 'boolean',
        'is_featured' => 'boolean',
        'is_trending' => 'boolean',
        'is_best_seller' => 'boolean',
        'is_new_arrival' => 'boolean',
        'is_active' => 'boolean',
        'mrp' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'rating' => 'decimal:2',
        'free_shipping_threshold' => 'decimal:2',
        'display_coupons' => 'array',
    ];

    /**
     * Category relation.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Sub Category relation.
     */
    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    /**
     * Brand relation.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Product Images relation (usually primary).
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Primary image helper.
     */
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Product Gallery relation (additional images).
     */
    public function gallery(): HasMany
    {
        return $this->hasMany(ProductImage::class)->where('is_primary', false)->orderBy('sort_order');
    }

    /**
     * Product Variants relation.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Product Reviews relation.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class)->where('is_approved', true);
    }

    /**
     * Check if product is in stock.
     */
    public function inStock(): bool
    {
        return $this->stock > 0;
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
