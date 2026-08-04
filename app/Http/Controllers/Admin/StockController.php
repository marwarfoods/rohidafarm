<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockLog;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    use LogsActivity;

    /**
     * Display a listing of products prioritizing low stock levels.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        // Calculate dynamic dashboard metric cards
        $metrics = [
            'total_products' => Product::count(),
            'low_stock' => Product::where('stock', '<=', 5)->where('stock', '>', 0)->count(),
            'out_of_stock' => Product::where('stock', '<=', 0)->count(),
            'total_items_sold' => DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->whereNull('orders.deleted_at')
                ->where('orders.status', '!=', 'cancelled')
                ->sum('order_items.quantity')
        ];

        // Query products sorted by stock level ascending (low stock first)
        $query = Product::with(['category', 'primaryImage'])->orderBy('stock', 'asc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(15)->appends(['search' => $search]);

        return view('admin.stock.index', compact('products', 'metrics', 'search'));
    }

    /**
     * Update stock level for a product and create adjustment log.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'stock' => 'required|integer|min:0'
        ]);

        $product = Product::findOrFail($id);
        $oldStock = $product->stock;
        $newStock = (int) $request->input('stock');
        $changeAmount = $newStock - $oldStock;

        // Skip log if there is no stock difference
        if ($changeAmount !== 0) {
            // Save to stock adjustment logs
            StockLog::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'old_stock' => $oldStock,
                'new_stock' => $newStock,
                'change_amount' => $changeAmount
            ]);

            // Update product
            $product->update(['stock' => $newStock]);

            $this->logActivity('Adjusted stock level of Product: ' . $product->name . ' (SKU: ' . $product->sku . ') from ' . $oldStock . ' to ' . $newStock);
        }

        return redirect()->route('admin.stock.index')->with('success', 'Stock level updated successfully.');
    }

    /**
     * Show stock update history log and daily sales metrics.
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);

        // Fetch stock log history
        $logs = StockLog::with('user')
            ->where('product_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch daily sales grouped by date for this product
        $dailyOrders = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('order_items.product_id', $id)
            ->whereNull('orders.deleted_at')
            ->where('orders.status', '!=', 'cancelled')
            ->select(
                DB::raw('DATE(orders.created_at) as order_date'),
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            )
            ->groupBy('order_date')
            ->orderBy('order_date', 'desc')
            ->get();

        return view('admin.stock.show', compact('product', 'logs', 'dailyOrders'));
    }
}
