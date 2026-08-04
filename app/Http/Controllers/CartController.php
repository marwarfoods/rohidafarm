<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\SeoService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;
    protected $seoService;

    public function __construct(CartService $cartService, SeoService $seoService)
    {
        $this->cartService = $cartService;
        $this->seoService = $seoService;
    }

    /**
     * Show cart items page.
     */
    public function index()
    {
        $items = $this->cartService->getItems();
        $totals = $this->cartService->getTotals();
        
        $seo = $this->seoService->generateTags([
            'title' => 'Shopping Cart',
            'description' => 'View items in your shopping cart.'
        ]);

        return response()
            ->view('frontend.cart.index', compact('items', 'totals', 'seo'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    /**
     * Return cart offcanvas drawer HTML via AJAX.
     */
    public function ajaxDrawer()
    {
        $items = $this->cartService->getItems();
        $totals = $this->cartService->getTotals();

        return response()
            ->view('frontend.partials.cart-offcanvas', compact('items', 'totals'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    /**
     * Add item to cart (Ajax & Standard).
     */
    public function add(Request $request)
    {
        if ($request->has('variant_id') && ($request->input('variant_id') === '' || $request->input('variant_id') === 'null')) {
            $request->merge(['variant_id' => null]);
        }

        $request->validate([
            'product_id' => 'required|integer',
            'variant_id' => 'nullable|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $this->cartService->add(
                $request->input('product_id'),
                $request->input('variant_id'),
                $request->input('quantity')
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Product successfully added to cart!',
                    'cart_count' => $this->cartService->getItems()->sum('quantity')
                ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            }

            return redirect()->route('cart.index')
                ->with('success', 'Product added to cart!')
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 400)->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update cart item quantity (Ajax).
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            $this->cartService->update($id, $request->input('quantity'));
            $totals = $this->cartService->getTotals();

            return response()->json([
                'status' => 'success',
                'message' => 'Cart updated successfully.',
                'totals' => $totals,
                'cart_count' => $totals['items_count']
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400)->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        }
    }

    /**
     * Remove item from cart.
     */
    public function remove($id)
    {
        try {
            $this->cartService->remove($id);
            return response()->json([
                'status' => 'success',
                'message' => 'Item removed from cart.',
                'totals' => $this->cartService->getTotals()
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400)->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        }
    }

    /**
     * Apply coupon code.
     */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        try {
            $coupon = $this->cartService->applyCoupon($request->input('code'));
            $totals = $this->cartService->getTotals();

            return response()->json([
                'status' => 'success',
                'message' => "Coupon '{$coupon->code}' applied successfully!",
                'totals' => $totals
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400)->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        }
    }

    /**
     * Remove applied coupon.
     */
    public function removeCoupon()
    {
        $this->cartService->removeCoupon();
        return response()->json([
            'status' => 'success',
            'message' => 'Coupon removed.',
            'totals' => $this->cartService->getTotals()
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }
}
