<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

use function Illuminate\Support\defer;

/**
 * Real-time catalog sync: sends the documented Product / Collection webhooks to
 * Shiprocket Checkout when products, variants (price/stock) or categories change.
 * Runs after the response is sent (no queue worker needed) and once per record per request.
 */
class ShiprocketCheckoutCatalogSync
{
    private static array $pendingProducts = [];
    private static array $pendingCollections = [];

    public static function product(?int $productId): void
    {
        if (!$productId || isset(self::$pendingProducts[$productId]) || !self::active()) {
            return;
        }
        self::$pendingProducts[$productId] = true;

        defer(function () use ($productId) {
            unset(self::$pendingProducts[$productId]);
            try {
                $product = app(ShiprocketCheckoutCatalog::class)->productQuery()->withTrashed()->find($productId);
                if (!$product) {
                    return;
                }
                $payload = app(ShiprocketCheckoutCatalog::class)->formatProduct($product);
                if ($product->trashed()) {
                    $payload['status'] = 'archived';
                }
                $res = app(ShiprocketCheckoutService::class)->sendProductWebhook($payload);
                if (!$res['ok']) {
                    app(ShiprocketCheckoutService::class)->recordError("Product webhook #{$productId}: {$res['error']}");
                }
            } catch (\Throwable $e) {
                Log::warning('Shiprocket Checkout product webhook failed', ['product_id' => $productId, 'error' => $e->getMessage()]);
            }
        }, 'shiprocket-checkout-product-' . $productId);
    }

    public static function collection(?int $categoryId): void
    {
        if (!$categoryId || isset(self::$pendingCollections[$categoryId]) || !self::active()) {
            return;
        }
        self::$pendingCollections[$categoryId] = true;

        defer(function () use ($categoryId) {
            unset(self::$pendingCollections[$categoryId]);
            try {
                $category = Category::find($categoryId);
                if (!$category) {
                    return;
                }
                $res = app(ShiprocketCheckoutService::class)->sendCollectionWebhook(
                    app(ShiprocketCheckoutCatalog::class)->formatCollection($category)
                );
                if (!$res['ok']) {
                    app(ShiprocketCheckoutService::class)->recordError("Collection webhook #{$categoryId}: {$res['error']}");
                }
            } catch (\Throwable $e) {
                Log::warning('Shiprocket Checkout collection webhook failed', ['category_id' => $categoryId, 'error' => $e->getMessage()]);
            }
        }, 'shiprocket-checkout-collection-' . $categoryId);
    }

    private static function active(): bool
    {
        try {
            return app(ShiprocketCheckoutService::class)->isActive();
        } catch (\Throwable $e) {
            return false; // e.g. settings table missing during migrations
        }
    }
}
