<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A Shiprocket Checkout (Fastrr) session and the Laravel order created from it.
 */
class ShiprocketCheckoutOrder extends Model
{
    protected $fillable = [
        'checkout_order_id',
        'fastrr_order_id',
        'platform_order_id',
        'order_id',
        'user_id',
        'browser_token',
        'source',
        'status',
        'payment_type',
        'payment_status',
        'total_amount_payable',
        'cart_items',
        'details',
        'last_error',
        'last_webhook_at',
        'verified_at',
        'processed_at',
    ];

    protected $hidden = ['browser_token'];

    protected $casts = [
        'cart_items' => 'array',
        'details' => 'array',
        'total_amount_payable' => 'decimal:2',
        'last_webhook_at' => 'datetime',
        'verified_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
