<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    /**
     * Get categories list.
     */
    public function categories()
    {
        $categories = Category::where('is_active', true)->get();
        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    /**
     * Get products list with filters.
     */
    public function products(Request $request)
    {
        $query = Product::with(['category', 'images'])->where('is_active', true);

        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->query('category'));
            });
        }

        if ($request->has('search')) {
            $search = $request->query('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $products = $query->paginate(12);

        return response()->json([
            'status' => 'success',
            'data' => $products
        ]);
    }

    /**
     * Get single product detail.
     */
    public function product(string $slug)
    {
        try {
            $product = Product::with(['category', 'subCategory', 'brand', 'images', 'gallery', 'variants', 'reviews.user'])
                ->where('slug', $slug)
                ->firstOrFail();

            return response()->json([
                'status' => 'success',
                'data' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found.'
            ], 404);
        }
    }

    /**
     * Guest Checkout Endpoint.
     */
    public function checkout(Request $request, OrderService $orderService, PaymentService $paymentService)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|regex:/^[0-9]{10}$/',
            'address_line1' => 'required|string|max:255',
            'city' => 'required|string',
            'state' => 'required|string',
            'postal_code' => 'required|string|regex:/^[0-9]{6}$/',
            'payment_method' => 'required|string|in:cod,online_gateway',
            'cart_items' => 'required|array',
            'cart_items.*.product_id' => 'required|exists:products,id',
            'cart_items.*.quantity' => 'required|integer|min:1',
            'cart_items.*.variant_id' => 'nullable|exists:product_variants,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Implementation of API-based custom order builder (skipping Session CartService)
        return response()->json([
            'status' => 'success',
            'message' => 'API Order checkout validated. Ready for payment capture.',
            'mock_order_id' => 'RF-API-' . rand(1000, 9999),
            'amount_payable' => 1799.00
        ]);
    }
}
