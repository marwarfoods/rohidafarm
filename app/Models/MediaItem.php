<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaItem extends Model
{
    protected $fillable = [
        'filename',
        'file_path',
        'file_type',
        'file_size',
        'url',
    ];
}
