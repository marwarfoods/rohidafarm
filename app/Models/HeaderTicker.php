<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeaderTicker extends Model
{
    protected $fillable = [
        'text',
        'icon_class',
        'sort_order',
        'is_active',
    ];
}
