<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShiprocketCheckoutVariant;
use Illuminate\Database\Eloquent\Builder;

/**
 * Builds the Shiprocket Checkout catalog payloads (Fetch Products / Fetch Products by
 * Collection / Fetch Collections + product/collection webhooks) from the existing tables.
 *
 * Per the official docs every field is mandatory (blank string when no value),
 * product ids and variant ids must be unique longs, and pagination is page/limit.
 */
class ShiprocketCheckoutCatalog
{
    public const MAX_LIMIT = 250;

    /**
     * @return array{data: array{total: int, products: array}}
     */
    public function products(int $page, int $limit, ?int $collectionId = null): array
    {
        [$page, $limit] = $this->paging($page, $limit);

        $query = $this->productQuery();
        if ($collectionId !== null) {
            $query->where('category_id', $collectionId);
        }

        $total = (clone $query)->count();
        $products = $query->orderBy('id')->forPage($page, $limit)->get();

        return ['data' => [
            'total' => $total,
            'products' => $products->map(fn (Product $p) => $this->formatProduct($p))->values()->all(),
        ]];
    }

    /**
     * @return array{data: array{total: int, collections: array}}
     */
    public function collections(int $page, int $limit): array
    {
        [$page, $limit] = $this->paging($page, $limit);

        $query = Category::query()->where('is_active', true);
        $total = (clone $query)->count();

        return ['data' => [
            'total' => $total,
            'collections' => $query->orderBy('id')->forPage($page, $limit)->get()
                ->map(fn (Category $c) => $this->formatCollection($c))->values()->all(),
        ]];
    }

    /**
     * Push the whole catalog to Shiprocket Checkout with the documented Collection and
     * Product webhooks (same payloads as the catalog APIs).
     *
     * @return array{collections: int, products: int, failed: array<int, string>}
     */
    public function pushAll(ShiprocketCheckoutService $checkout): array
    {
        $result = ['collections' => 0, 'products' => 0, 'failed' => []];

        foreach (Category::where('is_active', true)->orderBy('id')->get() as $category) {
            $res = $checkout->sendCollectionWebhook($this->formatCollection($category));
            $res['ok'] ? $result['collections']++ : $result['failed'][] = "Collection #{$category->id} {$category->name}: {$res['error']}";
        }

        $this->productQuery()->orderBy('id')->chunk(50, function ($products) use ($checkout, &$result) {
            foreach ($products as $product) {
                $res = $checkout->sendProductWebhook($this->formatProduct($product));
                $res['ok'] ? $result['products']++ : $result['failed'][] = "Product #{$product->id} {$product->name}: {$res['error']}";
            }
        });

        return $result;
    }

    public function productQuery(): Builder
    {
        // Soft-deleted products are excluded automatically.
        return Product::query()->with(['brand', 'category', 'subCategory', 'primaryImage', 'images', 'variants']);
    }

    public function formatProduct(Product $product): array
    {
        $productImage = $this->productImageUrl($product);
        $weightKgFallback = $this->toKg($product->weight);
        $variants = $product->variants->sortBy('id')->values();

        if ($variants->isEmpty()) {
            $variantRows = [$this->formatVariant($product, null, $productImage, $weightKgFallback)];
            $options = [['name' => 'Title', 'values' => ['Default Title']]];
        } else {
            $variantRows = $variants->map(fn (ProductVariant $v) => $this->formatVariant($product, $v, $productImage, $weightKgFallback))->all();
            $options = [['name' => 'Size', 'values' => $variants->map(fn ($v) => $this->variantTitle($v))->unique()->values()->all()]];
        }

        return [
            'id' => (int) $product->id,
            'title' => (string) $product->name,
            'body_html' => (string) ($product->description ?: $product->short_description ?: ''),
            'vendor' => (string) ($product->brand?->name ?: config('app.name', '')),
            'product_type' => (string) ($product->category?->name ?? ''),
            'created_at' => $this->date($product->created_at),
            'handle' => (string) $product->slug,
            'updated_at' => $this->date($product->updated_at),
            'tags' => collect([$product->category?->name, $product->subCategory?->name])->filter()->implode(', '),
            'status' => $product->is_active ? 'active' : 'draft',
            'variants' => $variantRows,
            'image' => ['src' => $productImage],
            'options' => $options,
        ];
    }

    public function formatCollection(Category $category): array
    {
        $image = $category->image ?: $category->banner_image;

        return [
            'id' => (int) $category->id,
            'updated_at' => $this->date($category->updated_at),
            'body_html' => (string) ($category->description ?? ''),
            'handle' => (string) $category->slug,
            'image' => ['src' => $image ? $this->absoluteUrl($image) : ''],
            'title' => (string) $category->name,
            'created_at' => $this->date($category->created_at),
        ];
    }

    private function formatVariant(Product $product, ?ProductVariant $variant, string $productImage, float $fallbackKg): array
    {
        $price = $variant && (float) $variant->sale_price > 0 ? $variant->sale_price : $product->sale_price;
        $mrp = $variant ? ($variant->mrp ?: $product->mrp) : $product->mrp;
        $kg = $variant ? ($this->toKg($variant->weight) ?: $this->toKg($variant->name) ?: $fallbackKg) : $fallbackKg;
        $title = $variant ? $this->variantTitle($variant) : 'Default Title';
        $optionName = $variant ? 'Size' : 'Title';

        return [
            'id' => ShiprocketCheckoutVariant::idFor($product->id, $variant?->id),
            'title' => $title,
            'price' => number_format((float) $price, 2, '.', ''),
            'compare_at_price' => number_format((float) max((float) $mrp, (float) $price), 2, '.', ''),
            'sku' => (string) (($variant?->sku) ?: ($product->sku ?? '')),
            'quantity' => max(0, (int) ($variant ? $variant->stock : $product->stock)),
            'created_at' => $this->date(($variant ?? $product)->created_at),
            'updated_at' => $this->date(($variant ?? $product)->updated_at),
            'taxable' => true,
            'option_values' => [$optionName => $title],
            'grams' => (int) round($kg * 1000),
            'image' => ['src' => $variant && $variant->image_path ? $this->absoluteUrl($variant->image_path) : $productImage],
            'weight' => round($kg, 3),
            'weight_unit' => 'kg',
        ];
    }

    private function variantTitle(ProductVariant $variant): string
    {
        return (string) ($variant->name ?: $variant->weight ?: ('Variant ' . $variant->id));
    }

    private function productImageUrl(Product $product): string
    {
        $path = $product->primaryImage?->image_path ?: $product->images->sortBy('sort_order')->first()?->image_path;

        return $path ? $this->absoluteUrl($path) : '';
    }

    private function absoluteUrl(string $path): string
    {
        return preg_match('#^https?://#i', $path) ? $path : asset(ltrim($path, '/'));
    }

    private function toKg($raw): float
    {
        return app(ShiprocketService::class)->parseWeightToKg($raw);
    }

    private function date($value): string
    {
        return $value ? $value->toIso8601String() : '';
    }

    private function paging(int $page, int $limit): array
    {
        return [max(1, $page), min(max(1, $limit), self::MAX_LIMIT)];
    }
}
