<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'name',
        'phone',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * User relation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to get full address string.
     */
    public function getFullAddressAttribute(): string
    {
        $addr = $this->address_line1;
        if ($this->address_line2) {
            $addr .= ', ' . $this->address_line2;
        }
        return "{$this->name}, {$addr}, {$this->city}, {$this->state} - {$this->postal_code}, {$this->country}. Phone: {$this->phone}";
    }
}
