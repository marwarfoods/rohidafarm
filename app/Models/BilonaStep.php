<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BilonaStep extends Model
{
    protected $fillable = ['title', 'description', 'image_path', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
