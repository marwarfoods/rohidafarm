<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'type', // credit, debit
        'description',
        'referable_id',
        'referable_type',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * User relation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Polymorphic relation to orders, refunds, etc.
     */
    public function referable(): MorphTo
    {
        return $this->morphTo();
    }
}
