<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NativeIngredient extends Model
{
    protected $fillable = ['title', 'image_path', 'link', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
