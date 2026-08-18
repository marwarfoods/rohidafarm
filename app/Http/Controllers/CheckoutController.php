<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Address;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    protected $cartService;
    protected $orderService;
    protected $paymentService;
    protected $seoService;

    public function __construct(
        CartService $cartService,
        OrderService $orderService,
        PaymentService $paymentService,
        SeoService $seoService
    ) {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
        $this->paymentService = $paymentService;
        $this->seoService = $seoService;
    }

    /**
     * Show Checkout Page.
     */
    public function index()
    {
        $items = $this->cartService->getItems();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $totals = $this->cartService->getTotals();
        $user = Auth::user();
        
        // Fetch saved addresses if logged in
        if ($user) {
            $user->syncAddressFromLastOrder();
        }
        $addresses = $user ? $user->addresses()->get() : collect();
        $defaultAddress = $addresses->where('is_default', true)->first() ?: $addresses->first();

        $seo = $this->seoService->generateTags([
            'title' => 'Secure Checkout',
            'description' => 'Complete your order securely.'
        ]);

        return response()
            ->view('frontend.checkout.index', compact('items', 'totals', 'addresses', 'defaultAddress', 'seo'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    /**
     * Calculate totals dynamically (AJAX).
     */
    public function calculateTotals(Request $request)
    {
        $state = $request->input('state');
        $paymentMethod = $request->input('payment_method');
        $totals = $this->cartService->getTotals($state, $paymentMethod);
        
        return response()->json([
            'status' => 'success',
            'totals' => $totals
        ]);
    }

    /**
     * Process checkout form.
     */
    public function store(CheckoutRequest $request)
    {
        $items = $this->cartService->getItems();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        try {
            // 0. Auto-register or Auto-login Guest Account if not authenticated
            if (!Auth::check()) {
                $existingUser = \App\Models\User::withTrashed()->where('email', $request->input('email'))->first();
                if ($existingUser) {
                    if ($existingUser->trashed()) {
                        $existingUser->restore();
                    }
                    Auth::login($existingUser);
                    $this->cartService->syncSessionToDb();
                } else {
                    $rawPassword = \Illuminate\Support\Str::random(12);
                    $user = \App\Models\User::create([
                        'name' => $request->input('name'),
                        'email' => $request->input('email'),
                        'phone' => $request->input('phone'),
                        'password' => bcrypt($rawPassword),
                        'role' => 'customer',
                        'wallet_balance' => 0.00
                    ]);
                    Auth::login($user);
                    
                    // Send welcome email with password
                    try {
                        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\NewAccountMail($user, $rawPassword));
                    } catch (\Exception $e) {
                        logger()->error('Failed to send NewAccountMail: ' . $e->getMessage());
                    }
                    
                    // Sync guest session items to newly created database account
                    $this->cartService->syncSessionToDb();
                }
            }

            // 1. Gather address details
            $shippingData = [
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'address_line1' => $request->input('address_line1'),
                'address_line2' => $request->input('address_line2'),
                'city' => $request->input('city'),
                'state' => $request->input('state'),
                'postal_code' => trim($request->input('postal_code')),
                'country' => 'India',
            ];

            // Check blocked pincodes
            $blockedPincodes = \App\Models\Setting::get('blocked_pincodes');
            if ($blockedPincodes) {
                $blockedArr = array_map('trim', explode(',', (string)$blockedPincodes));
                if (in_array($shippingData['postal_code'], $blockedArr)) {
                    return back()->with('error', 'Sorry, we currently do not deliver to this pincode ('.$shippingData['postal_code'].'). Please use a different address.')->withInput();
                }
            }

            // Save address for future use if user checkmarked it
            if (Auth::check() && $request->has('save_address')) {
                Address::updateOrCreate(
                    [
                        'user_id' => Auth::id(),
                        'address_line1' => $shippingData['address_line1'],
                        'postal_code' => $shippingData['postal_code']
                    ],
                    $shippingData + ['type' => 'shipping', 'is_default' => false]
                );
            }

            // 2. Create the order using OrderService (handles stock deduction, cart clearing)
            $order = $this->orderService->createFromCart(
                $shippingData,
                $request->input('payment_method')
            );

            // Handle Razorpay Payment Flow (or COD advance flow)
            if ($request->input('payment_method') === 'razorpay' || ($request->input('payment_method') === 'cod' && $order->advance_amount > 0)) {
                try {
                    $keyId = \App\Models\Setting::get('razorpay_key_id') ?: env('RAZORPAY_KEY', 'rzp_test_dummy');
                    $keySecret = \App\Models\Setting::get('razorpay_secret_key') ?: env('RAZORPAY_SECRET', 'dummy_secret');
                    
                    $api = new \Razorpay\Api\Api($keyId, $keySecret);
                    
                    $amountToPay = $request->input('payment_method') === 'cod' ? $order->advance_amount : $order->total;

                    $razorpayOrder = $api->order->create([
                        'receipt'  => $order->order_number,
                        'amount'   => (int)round($amountToPay * 100), // convert to paise
                        'currency' => 'INR',
                    ]);

                    return response()->view('frontend.checkout.razorpay', [
                        'order' => $order,
                        'razorpayOrderId' => $razorpayOrder['id'],
                        'amount' => (int)round($amountToPay * 100),
                        'key' => $keyId,
                        'user' => Auth::user() ?? (object)['name' => $shippingData['name'], 'email' => $request->input('email'), 'phone' => $shippingData['phone']]
                    ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
                } catch (\Exception $e) {
                    // Abort the orphaned order if razorpay initialization fails
                    $this->orderService->abort($order->id);
                    return back()->with('error', 'Payment Gateway Error: ' . $e->getMessage());
                }
            }

            // 3. Process payment for Cash On Delivery (without advance) or Wallet
            $this->paymentService->process($order, $request->input('payment_method'));

            // Clear cart ONLY for COD / successful upfront flows (not Razorpay which happens later)
            $this->cartService->clear();

            // Send Order Confirmation Mail
            try {
                \Illuminate\Support\Facades\Mail::to($order->user->email ?? $request->input('email'))->send(new \App\Mail\OrderPlacedMail($order));
            } catch (\Exception $e) {
                logger()->error('Failed to send OrderPlacedMail: ' . $e->getMessage());
            }

            // Send Admin Notification Mail
            try {
                $adminEmails = \App\Models\User::where('role', 'admin')->pluck('email')->toArray();
                $primarySettingEmail = \App\Models\Setting::get('contact_email_1') ?: \App\Models\Setting::get('contact_email');
                if ($primarySettingEmail && !in_array($primarySettingEmail, $adminEmails)) {
                    $adminEmails[] = $primarySettingEmail;
                }

                if (!empty($adminEmails)) {
                    \Illuminate\Support\Facades\Mail::to($adminEmails)->send(new \App\Mail\AdminNewOrderMail($order));
                }
            } catch (\Exception $e) {
                logger()->error('Failed to send AdminNewOrderMail: ' . $e->getMessage());
            }

            return redirect()->route('checkout.success', $order->uuid)->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Process direct Buy Now checkout.
     */
    public function directBuy(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
        ]);

        try {
            // 0. Auto-register or Auto-login Guest Account if not authenticated
            if (!Auth::check()) {
                $existingUser = \App\Models\User::withTrashed()->where('email', $request->input('email'))->first();
                if ($existingUser) {
                    if ($existingUser->trashed()) {
                        $existingUser->restore();
                    }
                    Auth::login($existingUser);
                } else {
                    $rawPassword = \Illuminate\Support\Str::random(12);
                    $user = \App\Models\User::create([
                        'name' => $request->input('name'),
                        'email' => $request->input('email'),
                        'phone' => $request->input('phone'),
                        'password' => bcrypt($rawPassword),
                        'role' => 'customer',
                        'wallet_balance' => 0.00
                    ]);
                    Auth::login($user);
                    
                    try {
                        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\NewAccountMail($user, $rawPassword));
                    } catch (\Exception $e) {
                        logger()->error('Failed to send NewAccountMail: ' . $e->getMessage());
                    }
                }
            }

            $shippingData = [
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'address_line1' => $request->input('address_line1'),
                'address_line2' => null,
                'city' => $request->input('city'),
                'state' => $request->input('state'),
                'postal_code' => trim($request->input('postal_code')),
                'country' => 'India',
            ];

            // Check blocked pincodes
            $blockedPincodes = \App\Models\Setting::get('blocked_pincodes');
            if ($blockedPincodes) {
                $blockedArr = array_map('trim', explode(',', (string)$blockedPincodes));
                if (in_array($shippingData['postal_code'], $blockedArr)) {
                    return response()->json(['message' => 'Sorry, we currently do not deliver to this pincode.'], 400);
                }
            }

            // Save address for future use
            Address::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'address_line1' => $shippingData['address_line1'],
                    'postal_code' => $shippingData['postal_code']
                ],
                $shippingData + ['type' => 'shipping', 'is_default' => false]
            );

            // Fetch product and variant
            $product = \App\Models\Product::findOrFail($request->input('product_id'));
            $variant = $request->input('variant_id') ? \App\Models\ProductVariant::find($request->input('variant_id')) : null;

            // 2. Create the order directly using OrderService
            $order = $this->orderService->createDirectOrder(
                $product,
                $variant,
                (int)$request->input('quantity'),
                $shippingData,
                'razorpay'
            );

            // Handle Razorpay Payment Flow
            $keyId = \App\Models\Setting::get('razorpay_key_id') ?: env('RAZORPAY_KEY', 'rzp_test_dummy');
            $keySecret = \App\Models\Setting::get('razorpay_secret_key') ?: env('RAZORPAY_SECRET', 'dummy_secret');
            
            $api = new \Razorpay\Api\Api($keyId, $keySecret);
            
            $razorpayOrder = $api->order->create([
                'receipt'  => $order->order_number,
                'amount'   => (int)round($order->total * 100), // convert to paise
                'currency' => 'INR',
            ]);

            return response()->json([
                'status' => 'success',
                'razorpayOrderId' => $razorpayOrder['id'],
                'amount' => (int)round($order->total * 100),
                'key' => $keyId,
                'uuid' => $order->uuid,
                'app_name' => config('app.name', 'RohidaFarm'),
                'user' => Auth::user() ?? (object)['name' => $shippingData['name'], 'email' => $request->input('email'), 'phone' => $shippingData['phone']]
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Razorpay Callback Verification
     */
    public function razorpayCallback(Request $request)
    {
        $signature = $request->input('razorpay_signature');
        $paymentId = $request->input('razorpay_payment_id');
        $orderId = $request->input('razorpay_order_id');
        $uuid = $request->input('uuid');

        try {
            $keyId = \App\Models\Setting::get('razorpay_key_id') ?: env('RAZORPAY_KEY', 'rzp_test_dummy');
            $keySecret = \App\Models\Setting::get('razorpay_secret_key') ?: env('RAZORPAY_SECRET', 'dummy_secret');
            
            $api = new \Razorpay\Api\Api($keyId, $keySecret);
            
            // Verify signature
            $api->utility->verifyPaymentSignature([
                'razorpay_signature'  => $signature,
                'razorpay_payment_id' => $paymentId,
                'razorpay_order_id'   => $orderId
            ]);

            $order = Order::where('uuid', $uuid)->firstOrFail();
            
            // Process payment as successful
            $paymentAmount = $order->payment_method === 'cod' ? $order->advance_amount : null;
            $this->paymentService->process($order, 'razorpay', $paymentId, $paymentAmount);

            if ($order->payment_method === 'cod') {
                $order->update(['payment_status' => 'partial']);
            }

            // Clear the cart here upon successful Razorpay payment
            $this->cartService->clear();

            // Send Order Confirmation Mail
            try {
                \Illuminate\Support\Facades\Mail::to($order->user->email)->send(new \App\Mail\OrderPlacedMail($order));
            } catch (\Exception $e) {
                logger()->error('Failed to send OrderPlacedMail: ' . $e->getMessage());
            }

            // Send Admin Notification Mail
            try {
                $adminEmails = \App\Models\User::where('role', 'admin')->pluck('email')->toArray();
                $primarySettingEmail = \App\Models\Setting::get('contact_email_1') ?: \App\Models\Setting::get('contact_email');
                if ($primarySettingEmail && !in_array($primarySettingEmail, $adminEmails)) {
                    $adminEmails[] = $primarySettingEmail;
                }

                if (!empty($adminEmails)) {
                    \Illuminate\Support\Facades\Mail::to($adminEmails)->send(new \App\Mail\AdminNewOrderMail($order));
                }
            } catch (\Exception $e) {
                logger()->error('Failed to send AdminNewOrderMail: ' . $e->getMessage());
            }

            return redirect()->route('checkout.success', $order->uuid)->with('success', 'Payment successful! Order placed.');
        } catch (\Exception $e) {
            // Failed signature or error
            $order = Order::where('uuid', $uuid)->first();
            if ($order && $order->status === 'pending') {
                $this->orderService->abort($order->id);
            }
            return redirect()->route('checkout.index')->with('error', 'Payment verification failed: ' . $e->getMessage());
        }
    }

    /**
     * Cancel an orphaned Razorpay Order
     */
    public function razorpayCancel(string $uuid)
    {
        $order = Order::where('uuid', $uuid)->first();
        if ($order && $order->status === 'pending') {
            $this->orderService->abort($order->id);
        }
        return redirect()->route('checkout.index')->with('error', 'Payment was cancelled. Your cart has been restored.');
    }

    /**
     * Order success page.
     */
    public function success(string $uuid)
    {
        $order = Order::with(['items.product', 'payments'])->where('uuid', $uuid)->firstOrFail();
        
        // Ensure security so that only the user who owns the order can view the success screen
        if (Auth::id() !== $order->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $seo = $this->seoService->generateTags([
            'title' => 'Order Confirmed - #' . $order->order_number,
        ]);

        return view('frontend.checkout.success', compact('order', 'seo'));
    }

    /**
     * View Printable Invoice Receipt (PDF Downloader)
     */
    public function receipt(string $uuid)
    {
        $order = Order::with(['items.product', 'payments', 'user'])->where('uuid', $uuid)->firstOrFail();
        
        // Ensure security
        if (Auth::id() !== $order->user_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('frontend.order.receipt', compact('order'));
    }
}
