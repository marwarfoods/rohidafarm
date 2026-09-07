<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerReview extends Model
{
    protected $fillable = [
        'title',
        'rating',
        'review',
        'customer_name',
        'product_id',
        'image_path',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the product this customer review is linked to.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
