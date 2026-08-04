<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\DelhiveryService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;
    protected $delhiveryService;

    public function __construct(OrderService $orderService, DelhiveryService $delhiveryService)
    {
        $this->orderService = $orderService;
        $this->delhiveryService = $delhiveryService;
    }

    /**
     * Display all orders.
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'shipment']);

        if ($request->has('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display order detail page.
     */
    public function show($id)
    {
        $order = Order::with(['items.product', 'payments', 'trackingUpdates', 'shipment'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status timeline.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled,refunded,cancellation_requested',
            'description' => 'nullable|string|max:500',
            'tracking_number' => 'nullable|string|max:100',
            'tracking_carrier' => 'nullable|string|max:100',
        ]);

        try {
            $order = $this->orderService->updateStatus(
                $id,
                $request->input('status'),
                $request->input('description'),
                $request->input('tracking_number'),
                $request->input('tracking_carrier')
            );

            return back()->with('success', "Order status successfully updated to " . ucfirst($order->status));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Process order cancel/refund.
     */
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        try {
            $this->orderService->cancel($id, $request->input('reason'));
            return back()->with('success', 'Order cancelled and stock restored successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Manually push order to Delhivery.
     */
    public function syncDelhivery($id)
    {
        $order = Order::with(['items.product', 'items.variant', 'user'])->findOrFail($id);

        try {
            $shipment = $this->delhiveryService->createShipment($order);

            // Update tracking on order if AWB was returned
            if ($shipment->awb_code) {
                $order->update([
                    'tracking_number' => $shipment->awb_code,
                    'tracking_url'    => "https://www.delhivery.com/track/package/{$shipment->awb_code}",
                    'tracking_carrier' => $shipment->courier_name,
                ]);
            }

            return back()->with('success', '✅ Order successfully pushed to Delhivery! Shipment ID: ' . $shipment->delhivery_shipment_id);
        } catch (\Exception $e) {
            \Log::error('Manual Delhivery Sync Failed', ['order_id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', '❌ Delhivery sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete an order.
     */
    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->delete();
            return back()->with('success', 'Order moved to trash successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk soft delete orders.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        try {
            Order::whereIn('id', $request->ids)->delete();
            return back()->with('success', count($request->ids) . ' orders moved to trash.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display trashed orders.
     */
    public function trash(Request $request)
    {
        $query = Order::onlyTrashed()->with(['user']);

        if ($request->has('search') && $request->input('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('shipping_name', 'like', "%{$search}%")
                  ->orWhere('shipping_phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->orderBy('deleted_at', 'desc')->paginate(15);
        return view('admin.orders.trash', compact('orders'));
    }

    /**
     * Restore a soft deleted order.
     */
    public function restore($id)
    {
        try {
            Order::onlyTrashed()->findOrFail($id)->restore();
            return back()->with('success', 'Order restored successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk restore soft deleted orders.
     */
    public function bulkRestore(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        try {
            Order::onlyTrashed()->whereIn('id', $request->ids)->restore();
            return back()->with('success', count($request->ids) . ' orders restored.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Permanently delete an order.
     */
    public function forceDelete($id)
    {
        try {
            $order = Order::onlyTrashed()->findOrFail($id);
            // Optionally delete related items/payments to avoid orphans, or rely on cascading
            $order->items()->delete();
            $order->trackingUpdates()->delete();
            $order->payments()->delete();
            $order->forceDelete();
            
            return back()->with('success', 'Order permanently deleted.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk permanently delete orders.
     */
    public function bulkForceDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        try {
            $orders = Order::onlyTrashed()->whereIn('id', $request->ids)->get();
            foreach ($orders as $order) {
                $order->items()->delete();
                $order->trackingUpdates()->delete();
                $order->payments()->delete();
                $order->forceDelete();
            }
            return back()->with('success', count($request->ids) . ' orders permanently deleted.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
