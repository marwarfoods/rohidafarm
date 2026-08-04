<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\CompareProduct;
use App\Models\Order;
use App\Models\Product;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $seoService;

    public function __construct(SeoService $seoService)
    {
        $this->seoService = $seoService;
    }

    /**
     * Customer Profile Overview.
     */
    public function index()
    {
        $user = Auth::user();
        $user->syncAddressFromLastOrder();
        
        // Retrieve orders
        $orders = Order::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Retrieve saved addresses
        $addresses = Address::where('user_id', $user->id)->get();

        // Wallet transactions
        $transactions = $user->transactions()->orderBy('created_at', 'desc')->limit(5)->get();

        $seo = $this->seoService->generateTags([
            'title' => 'Customer Dashboard',
        ]);

        return view('customer.dashboard', compact('user', 'orders', 'addresses', 'transactions', 'seo'));
    }

    /**
     * Detailed orders history.
     */
    public function orders()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(10);
        
        $seo = $this->seoService->generateTags(['title' => 'Order History']);

        return view('customer.orders', compact('orders', 'seo'));
    }

    /**
     * Add funds mock wallet simulation.
     */
    public function addWalletBalance(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100|max:10000'
        ]);

        $user = Auth::user();
        $amount = (float) $request->input('amount');

        // Increment balance and save transaction log
        $user->increment('wallet_balance', $amount);
        $user->transactions()->create([
            'amount' => $amount,
            'type' => 'credit',
            'description' => 'Funds loaded via payment gateway simulator.',
        ]);

        return back()->with('success', "₹{$amount} has been successfully added to your wallet.");
    }

    /**
     * Request order cancellation.
     */
    public function cancelOrder($id)
    {
        $user = Auth::user();
        $order = Order::where('user_id', $user->id)->findOrFail($id);

        if (in_array($order->status, ['shipped', 'delivered', 'cancelled'])) {
            return back()->with('error', 'This order cannot be cancelled at this stage.');
        }

        $order->status = 'cancellation_requested';
        $order->save();

        return back()->with('success', 'Your cancellation request has been submitted to the admin.');
    }

    /**
     * Show single order details.
     */
    public function showOrder($id)
    {
        $user = Auth::user();
        $order = Order::with(['items.product', 'trackingUpdates'])->where('user_id', $user->id)->findOrFail($id);
        
        $seo = $this->seoService->generateTags(['title' => 'Order Details #' . $order->order_number]);

        return view('customer.show-order', compact('order', 'seo'));
    }
}
