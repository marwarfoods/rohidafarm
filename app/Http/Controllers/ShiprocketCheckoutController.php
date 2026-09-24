<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ShiprocketCheckoutOrder;
use App\Models\ShiprocketCheckoutVariant;
use App\Services\CartService;
use App\Services\ShiprocketCheckoutOrderProcessor;
use App\Services\ShiprocketCheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * Storefront side of Shiprocket Checkout: access token → HeadlessCheckout → redirect back.
 * Any failure returns a fallback URL to the native checkout so customers never get stuck.
 */
class ShiprocketCheckoutController extends Controller
{
    public function __construct(
        private ShiprocketCheckoutService $checkout,
        private CartService $cartService,
    ) {
    }

    /**
     * Generate a checkout access token (server-side; the secret never reaches the browser).
     */
    public function token(Request $request)
    {
        $data = $request->validate([
            'source' => 'required|in:cart,buy_now',
            'product_id' => 'required_if:source,buy_now|nullable|integer',
            'variant_id' => 'nullable|integer',
            'quantity' => 'nullable|integer|min:1|max:50',
        ]);

        $source = $data['source'];
        $fallback = route('checkout.index');

        if (!$this->checkout->isActiveFor($source)) {
            return $this->fail('Shiprocket Checkout is disabled.', $fallback, 409);
        }

        // 1. Build the cart lines (product / variant / qty) from the existing catalog.
        if ($source === 'buy_now') {
            $product = Product::with('variants')->where('is_active', true)->find($data['product_id']);
            if (!$product) {
                return $this->fail('Product not available.', $fallback, 404);
            }
            $variant = null;
            if ($product->variants->isNotEmpty()) {
                $variant = !empty($data['variant_id'])
                    ? $product->variants->firstWhere('id', (int) $data['variant_id'])
                    : ($product->variants->firstWhere('stock', '>', 0) ?? $product->variants->first());
                if (!$variant) {
                    return $this->fail('Selected option is not available.', $fallback, 422);
                }
            }
            $qty = (int) ($data['quantity'] ?? 1);
            $fallback = URL::temporarySignedRoute('shiprocket-checkout.fallback', now()->addHours(2), array_filter([
                'product_id' => $product->id, 'variant_id' => $variant?->id, 'quantity' => $qty,
            ]));
            if ((int) ($variant ? $variant->stock : $product->stock) < $qty) {
                return $this->fail('Not enough stock.', $fallback, 422);
            }
            $lines = [['product_id' => $product->id, 'variant_id' => $variant?->id, 'quantity' => $qty]];
        } else {
            $cartItems = $this->cartService->getItems();
            if ($cartItems->isEmpty()) {
                return $this->fail('Your cart is empty.', route('cart.index'), 422);
            }
            $lines = $cartItems->map(fn ($i) => [
                'product_id' => (int) $i->product_id,
                'variant_id' => $i->variant_id ? (int) $i->variant_id : null,
                'quantity' => (int) $i->quantity,
            ])->all();
        }

        // 2. Map to Fastrr variant_ids (must be unique per item → merge duplicates).
        $items = [];
        foreach ($lines as $line) {
            $vid = (string) ShiprocketCheckoutVariant::idFor($line['product_id'], $line['variant_id']);
            $items[$vid] = ['variant_id' => $vid, 'quantity' => ($items[$vid]['quantity'] ?? 0) + $line['quantity']];
        }

        // 3. Request the access token.
        $browserToken = $request->session()->get('shiprocket_checkout_browser') ?: Str::random(40);
        $request->session()->put('shiprocket_checkout_browser', $browserToken);

        $result = $this->checkout->createAccessToken(array_values($items), route('shiprocket-checkout.return'));
        if (!$result['ok']) {
            $this->checkout->recordError('Access token: ' . $result['error']);

            return $this->fail('Shiprocket Checkout is unavailable right now.', $fallback, 502);
        }

        if ($result['order_id']) {
            ShiprocketCheckoutOrder::updateOrCreate(
                ['checkout_order_id' => $result['order_id']],
                [
                    'user_id' => Auth::id(),
                    'browser_token' => $browserToken,
                    'source' => $source,
                    'status' => 'CREATED',
                    'cart_items' => array_values($items),
                ]
            );
        }

        return response()->json(['ok' => true, 'token' => $result['token'], 'fallback_url' => $fallback]);
    }

    /**
     * redirect_url target — Shiprocket appends ?oid=<order id>&ost=<status>.
     */
    public function return(Request $request, ShiprocketCheckoutOrderProcessor $processor)
    {
        $oid = (string) $request->query('oid', '');
        $ost = strtoupper((string) $request->query('ost', ''));

        if (!preg_match('/^[A-Za-z0-9_-]{1,64}$/', $oid)) {
            return redirect()->route('cart.index')->with('error', 'Checkout was not completed.');
        }

        if ($ost !== 'SUCCESS') {
            return redirect()->route('cart.index')->with('error', 'Checkout was not completed. You can try again or use the regular checkout.');
        }

        // Verifies with the Order Details API — the query string alone is never trusted.
        $record = $processor->sync($oid, 'redirect');

        $browserToken = (string) $request->session()->get('shiprocket_checkout_browser', '');
        $sameBrowser = $record->browser_token && $browserToken !== '' && hash_equals($record->browser_token, $browserToken);

        if ($sameBrowser && $record->source === 'cart') {
            $this->cartService->clear();
        }

        $order = $record->order_id ? Order::find($record->order_id) : null;
        if (!$order) {
            return redirect()->route('home')->with('success', 'Thank you! Your order has been received and will be confirmed shortly.');
        }

        // Same auto-login behaviour as the native guest checkout, but only for the browser that started it.
        if (!Auth::check() && $sameBrowser) {
            Auth::login($order->user);
        }

        if (Auth::id() === $order->user_id) {
            return redirect()->route('checkout.success', $order->uuid)->with('success', 'Order placed successfully!');
        }

        return redirect()->route('home')->with('success', "Thank you! Order #{$order->order_number} has been placed.");
    }

    /**
     * Native fallback for "Buy Now": add the item to the cart and open the regular checkout.
     */
    public function fallback(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer',
            'variant_id' => 'nullable|integer',
            'quantity' => 'nullable|integer|min:1|max:50',
        ]);

        try {
            $this->cartService->add((int) $data['product_id'], isset($data['variant_id']) ? (int) $data['variant_id'] : null, (int) ($data['quantity'] ?? 1));
        } catch (\Throwable $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }

        return redirect()->route('checkout.index');
    }

    private function fail(string $message, string $fallback, int $status)
    {
        return response()->json(['ok' => false, 'message' => $message, 'fallback_url' => $fallback], $status);
    }
}
