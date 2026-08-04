<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class AdminDashboardController extends Controller
{
    /**
     * Show Administrative Analytics Dashboard.
     */
    /**
     * Show Administrative Analytics Dashboard.
     */
    public function index(Request $request)
    {
        $dateFilter = $request->input('date_filter', '30days');
        $startDate = null;
        $endDate = null;

        if ($dateFilter === '7days') {
            $startDate = now()->subDays(7)->startOfDay();
            $endDate = now()->endOfDay();
        } elseif ($dateFilter === '30days') {
            $startDate = now()->subDays(30)->startOfDay();
            $endDate = now()->endOfDay();
        } elseif ($dateFilter === 'custom') {
            $startDate = $request->input('start_date') ? \Carbon\Carbon::parse($request->input('start_date'))->startOfDay() : now()->subDays(30)->startOfDay();
            $endDate = $request->input('end_date') ? \Carbon\Carbon::parse($request->input('end_date'))->endOfDay() : now()->endOfDay();
        }

        // 1. Fetch KPI Statistics (Filtered by Date)
        $salesQuery = Order::where('payment_status', 'paid');
        $ordersQuery = Order::query();
        $customersQuery = User::where('role', 'customer');

        if ($startDate && $endDate) {
            $salesQuery->whereBetween('created_at', [$startDate, $endDate]);
            $ordersQuery->whereBetween('created_at', [$startDate, $endDate]);
            $customersQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $totalSales = $salesQuery->sum('total');
        $totalOrdersCount = $ordersQuery->count();
        $totalCustomersCount = $customersQuery->count();
        
        // 2. Fetch sales trends (By day if short span, by month if long span)
        $diffInDays = $startDate && $endDate ? $startDate->diffInDays($endDate) : ($dateFilter === 'alltime' ? 365 : 30);
        
        if ($diffInDays <= 31) {
            // Group by Day
            $trendData = Order::selectRaw("DATE_FORMAT(created_at, '%d %b') as label, SUM(total) as total")
                ->where('payment_status', 'paid');
            if ($startDate && $endDate) {
                $trendData->whereBetween('created_at', [$startDate, $endDate]);
            }
            $trendData = $trendData->groupBy('label')
                ->orderBy('created_at', 'asc')
                ->get();
        } else {
            // Group by Month
            $trendData = Order::selectRaw("DATE_FORMAT(created_at, '%b %Y') as label, SUM(total) as total")
                ->where('payment_status', 'paid');
            if ($startDate && $endDate) {
                $trendData->whereBetween('created_at', [$startDate, $endDate]);
            }
            $trendData = $trendData->groupBy('label')
                ->orderBy('created_at', 'asc')
                ->get();
        }
            
        $salesLabels = $trendData->pluck('label')->toArray();
        $salesValues = $trendData->pluck('total')->map(fn($v) => (float)$v)->toArray();

        // Fetch order statuses (Filtered by Date)
        $statusCountsQuery = Order::selectRaw('status, count(*) as count');
        if ($startDate && $endDate) {
            $statusCountsQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        $statusCounts = $statusCountsQuery->groupBy('status')->get();
            
        $statusLabels = $statusCounts->pluck('status')->map(fn($s) => ucfirst($s))->toArray();
        $statusValues = $statusCounts->pluck('count')->toArray();

        // 3. Fetch critical alerts e.g. low stock alerts (< 10 units)
        $lowStockProducts = Product::where('stock', '<', 10)->limit(5)->get();

        // 4. Eager load recent orders and users (Filtered by Date)
        $recentOrdersQuery = Order::with('user');
        $recentUsersQuery = User::where('role', 'customer');
        if ($startDate && $endDate) {
            $recentOrdersQuery->whereBetween('created_at', [$startDate, $endDate]);
            $recentUsersQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        $recentOrders = $recentOrdersQuery->orderBy('created_at', 'desc')->limit(6)->get();
        $recentUsers = $recentUsersQuery->orderBy('created_at', 'desc')->limit(6)->get();

        // 5. Fetch recent logs
        $activityLogs = ActivityLog::with('user')->orderBy('created_at', 'desc')->limit(10)->get();

        return view('admin.dashboard', compact(
            'totalSales',
            'totalOrdersCount',
            'totalCustomersCount',
            'lowStockProducts',
            'recentOrders',
            'recentUsers',
            'activityLogs',
            'salesLabels',
            'salesValues',
            'statusLabels',
            'statusValues',
            'dateFilter',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Clear application cache dynamically.
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            
            Cache::flush();

            return back()->with('success', 'Application cache, view routes, and settings flushed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }
}
