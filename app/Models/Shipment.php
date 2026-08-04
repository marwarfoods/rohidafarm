<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    protected $fillable = [
        'order_id',
        'delhivery_shipment_id',
        'delhivery_order_id',
        'awb_code',
        'courier_name',
        'status',
        'response_payload',
    ];

    protected $casts = [
        'response_payload' => 'array',
    ];

    /**
     * Order relation.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
