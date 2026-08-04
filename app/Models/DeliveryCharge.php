<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryCharge extends Model
{
    protected $fillable = ['min_order_amount', 'charge_amount', 'pincodes', 'is_active'];

    protected $casts = [
        'min_order_amount' => 'decimal:2',
        'charge_amount' => 'decimal:2',
        'pincodes' => 'array',
        'is_active' => 'boolean',
    ];
}
