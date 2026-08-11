<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstagramFeed extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_path',
        'row',
        'link_url',
        'caption',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'row' => 'integer',
    ];
}
