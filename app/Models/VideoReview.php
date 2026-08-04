<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoReview extends Model
{
    protected $fillable = [
        'reviewer_name',
        'video_path',
        'product_id',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the product associated with the video review.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
