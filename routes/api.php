<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| REST API Routes
|--------------------------------------------------------------------------
*/

Route::get('/categories', [ApiController::class, 'categories']);
Route::get('/products', [ApiController::class, 'products']);
Route::get('/product/{slug}', [ApiController::class, 'product']);
Route::post('/checkout', [ApiController::class, 'checkout']);

/*
|--------------------------------------------------------------------------
| Shiprocket Checkout (Fastrr) — separate from Shiprocket Shipping
|--------------------------------------------------------------------------
| Catalog APIs + order webhook called server-to-server by Shiprocket Checkout.
*/
Route::prefix('fastrr')->name('shiprocket-checkout.')->middleware('throttle:120,1')->group(function () {
    Route::get('/products', [\App\Http\Controllers\Api\ShiprocketCheckoutCatalogController::class, 'products'])->name('catalog.products');
    Route::get('/products/collection', [\App\Http\Controllers\Api\ShiprocketCheckoutCatalogController::class, 'collectionProducts'])->name('catalog.collection-products');
    Route::get('/collections', [\App\Http\Controllers\Api\ShiprocketCheckoutCatalogController::class, 'collections'])->name('catalog.collections');
    Route::post('/webhook/order/{token}', [\App\Http\Controllers\Api\ShiprocketCheckoutWebhookController::class, 'order'])
        ->where('token', '[A-Za-z0-9]{20,64}')->name('webhook.order');
});
