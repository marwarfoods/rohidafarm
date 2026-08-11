<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo_path',
        'certificate_images',
        'certificate_number',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'certificate_images' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
