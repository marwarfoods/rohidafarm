<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLog extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'old_stock',
        'new_stock',
        'change_amount'
    ];

    /**
     * Get the product associated with the stock log.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user (admin) who updated the stock.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
