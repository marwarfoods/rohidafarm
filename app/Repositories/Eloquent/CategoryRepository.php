<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class CategoryRepository implements CategoryRepositoryInterface
{
    protected $model;

    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    public function allActive()
    {
        return $this->model->where('is_active', true)->orderBy('name')->get();
    }

    public function findBySlug(string $slug)
    {
        return $this->model->with(['subCategories', 'products'])->where('slug', $slug)->firstOrFail();
    }

    public function getWithSubcategories()
    {
        return $this->model->with(['subCategories' => function ($query) {
            $query->where('is_active', true);
        }])->where('is_active', true)->get();
    }
}
