<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasUuid, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'order_number',
        'status', // pending, processing, shipped, delivered, cancelled, refunded
        'subtotal',
        'tax',
        'shipping_charges',
        'coupon_code',
        'discount_amount',
        'total',
        'payment_method', // cod, razorpay, stripe, paypal, wallet
        'payment_status', // pending, paid, failed, refunded
        'shipping_name',
        'shipping_phone',
        'shipping_address_line1',
        'shipping_address_line2',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',
        'tracking_number',
        'tracking_url',
        'tracking_carrier',
        'estimated_delivery',
        'shipment_status',
        'advance_amount',
        'cod_due_amount',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping_charges' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'advance_amount' => 'decimal:2',
        'cod_due_amount' => 'decimal:2',
        'estimated_delivery' => 'datetime',
    ];

    /**
     * User relation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Order items relation.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Payments relation.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Order tracking logs relation.
     */
    public function trackingUpdates(): HasMany
    {
        return $this->hasMany(TrackOrder::class)->orderBy('occurred_at', 'desc');
    }

    /**
     * Shipment integration details relation.
     */
    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
    }

    /**
     * Check if order is editable/cancellable.
     */
    public function canCancel(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Get status color badge class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-warning text-dark',
            'processing' => 'bg-info text-white',
            'shipped' => 'bg-primary text-white',
            'delivered' => 'bg-success text-white',
            'cancelled' => 'bg-danger text-white',
            'refunded' => 'bg-secondary text-white',
            default => 'bg-light text-dark',
        };
    }
}
