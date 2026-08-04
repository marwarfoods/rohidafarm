<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use HasUuid, SoftDeletes;

    protected $fillable = [
        'uuid',
        'blog_category_id',
        'title',
        'slug',
        'author_name',
        'excerpt',
        'content',
        'featured_image',
        'is_published',
        'published_at',
        'view_count',
        'meta_title',
        'meta_description',
        'keywords',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Blog Category relation.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }
}
