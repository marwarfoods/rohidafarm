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
