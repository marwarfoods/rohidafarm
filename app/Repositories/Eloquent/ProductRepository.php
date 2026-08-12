<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class ProductRepository implements ProductRepositoryInterface
{
    protected $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return Cache::remember('products.all', 3600, function () {
            return $this->model->with(['category', 'images'])->where('is_active', true)->get();
        });
    }

    public function find(int $id)
    {
        return $this->model->with(['category', 'subCategory', 'brand', 'images', 'gallery', 'variants', 'reviews.user'])->findOrFail($id);
    }

    public function findByUuid(string $uuid)
    {
        return $this->model->with(['category', 'subCategory', 'brand', 'images', 'gallery', 'variants', 'reviews.user'])->where('uuid', $uuid)->firstOrFail();
    }

    public function findBySlug(string $slug)
    {
        return $this->model->with(['category', 'subCategory', 'brand', 'images', 'gallery', 'variants', 'reviews.user', 'faqs'])->where('slug', $slug)->firstOrFail();
    }

    public function getFeatured(int $limit = 4)
    {
        return Cache::remember("products.featured.{$limit}", 3600, function () use ($limit) {
            return $this->model->with(['category', 'images'])
                ->where('is_active', true)
                ->where('is_featured', true)
                ->limit($limit)
                ->get();
        });
    }

    public function getTrending(int $limit = 4)
    {
        return Cache::remember("products.trending.{$limit}", 3600, function () use ($limit) {
            return $this->model->with(['category', 'images'])
                ->where('is_active', true)
                ->where('is_trending', true)
                ->limit($limit)
                ->get();
        });
    }

    public function getBestSellers(int $limit = 4)
    {
        return Cache::remember("products.bestsellers.{$limit}", 3600, function () use ($limit) {
            return $this->model->with(['category', 'images'])
                ->where('is_active', true)
                ->where('is_best_seller', true)
                ->limit($limit)
                ->get();
        });
    }

    public function getNewArrivals(int $limit = 4)
    {
        return Cache::remember("products.newarrivals.{$limit}", 3600, function () use ($limit) {
            return $this->model->with(['category', 'images'])
                ->where('is_active', true)
                ->where('is_new_arrival', true)
                ->limit($limit)
                ->get();
        });
    }

    public function getRelated(int $categoryId, int $excludeId, int $limit = 4)
    {
        return $this->model->with(['category', 'images'])
            ->where('is_active', true)
            ->where('category_id', $categoryId)
            ->where('id', '!=', $excludeId)
            ->limit($limit)
            ->get();
    }

    public function filterAndPaginate(array $filters, int $perPage = 12)
    {
        $query = $this->model->with(['category', 'images'])->where('is_active', true);

        // Filter by Category
        if (!empty($filters['category'])) {
            $query->whereHas('category', function ($q) use ($filters) {
                $q->where('slug', $filters['category']);
            });
        }

        // Filter by Sub Category
        if (!empty($filters['subcategory'])) {
            $query->whereHas('subCategory', function ($q) use ($filters) {
                $q->where('slug', $filters['subcategory']);
            });
        }

        // Filter by Price range
        if (isset($filters['min_price'])) {
            $query->where('sale_price', '>=', $filters['min_price']);
        }
        if (isset($filters['max_price'])) {
            $query->where('sale_price', '<=', $filters['max_price']);
        }

        // Filter by Brand
        if (!empty($filters['brand'])) {
            $query->whereHas('brand', function ($q) use ($filters) {
                $q->where('slug', $filters['brand']);
            });
        }

        // Filter by Rating
        if (isset($filters['rating'])) {
            $query->where('rating', '>=', $filters['rating']);
        }

        // Filter by Availability (Stock)
        if (isset($filters['in_stock'])) {
            if ($filters['in_stock'] === 'true') {
                $query->where('stock', '>', 0);
            } elseif ($filters['in_stock'] === 'false') {
                $query->where('stock', '=', 0);
            }
        }

        // Search Query
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sort = $filters['sort'] ?? 'featured';
        switch ($sort) {
            case 'price_low_high':
                $query->orderBy('sale_price', 'asc');
                break;
            case 'price_high_low':
                $query->orderBy('sale_price', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'featured':
            default:
                $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
                break;
        }

        return $query->paginate($perPage)->withQueryString();
    }
}
