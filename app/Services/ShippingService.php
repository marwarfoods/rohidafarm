<?php

namespace App\Services;

use App\Models\Order;

class ShippingService
{
    /**
     * Estimate delivery date based on customer shipping pincode.
     */
    public function estimateDelivery(string $pincode): array
    {
        if (empty($pincode) || strlen($pincode) !== 6 || !is_numeric($pincode)) {
            return [
                'status' => 'error',
                'message' => 'Invalid Pincode. Must be 6 digits.',
                'days' => null,
                'date' => null
            ];
        }

        // Simple heuristic: Maharashtra codes start with 4, Gujarat with 3, Delhi with 1, etc.
        $days = 5; // Default standard shipping days
        $firstDigit = substr($pincode, 0, 1);
        if ($firstDigit == '4') {
            $days = 3; // Closer to Maharashtra warehouse (Pune)
        } elseif (in_array($firstDigit, ['3', '5'])) {
            $days = 4;
        }

        $estimatedDate = now()->addDays($days);

        return [
            'status' => 'success',
            'days' => $days,
            'date' => $estimatedDate->format('d M, Y'),
            'cod_available' => true
        ];
    }

    /**
     * Build a customer-facing tracking timeline for the order.
     */
    public function getTrackingTimeline(Order $order): array
    {
        $timeline = [];
        $shipment = $order->shipment;
        $courierName = $shipment ? $shipment->courier_name : ($order->tracking_carrier ?: 'our courier partner');
        $created = $order->created_at;

        $timeline[] = [
            'status' => 'Order Placed',
            'activity' => 'Order Placed',
            'description' => 'Your order has been successfully placed.',
            'location' => 'Pune Warehouse',
            'time' => $created->format('d M Y, h:i A'),
            'date' => $created->format('d M Y, h:i A'),
            'completed' => true
        ];

        if ($order->status === 'cancelled') {
            $timeline[] = [
                'status' => 'Cancelled',
                'activity' => 'Cancelled',
                'description' => 'The order has been cancelled.',
                'location' => 'System',
                'time' => $order->updated_at->format('d M Y, h:i A'),
                'date' => $order->updated_at->format('d M Y, h:i A'),
                'completed' => true
            ];
            return array_reverse($timeline);
        }

        if (in_array($order->status, ['processing', 'shipped', 'delivered'])) {
            $timeline[] = [
                'status' => 'Packed & Manifested',
                'activity' => 'Packed & Manifested',
                'description' => 'Package has been sealed and manifest details sent to ' . $courierName,
                'location' => 'Pune Warehouse',
                'time' => $created->copy()->addHours(4)->format('d M Y, h:i A'),
                'date' => $created->copy()->addHours(4)->format('d M Y, h:i A'),
                'completed' => true
            ];
        }

        if (in_array($order->status, ['shipped', 'delivered'])) {
            $timeline[] = [
                'status' => 'Dispatched',
                'description' => 'In transit to customer city hub.',
                'location' => 'Surat Logistics Hub',
                'time' => $created->copy()->addDays(1)->format('d M Y, h:i A'),
                'completed' => true
            ];
            $timeline[] = [
                'status' => 'Out for Delivery',
                'description' => 'Courier agent is out for delivery with your package.',
                'location' => $order->shipping_city . ' Hub',
                'time' => $created->copy()->addDays(2)->format('d M Y, h:i A'),
                'completed' => true
            ];
        }

        if ($order->status === 'delivered') {
            $timeline[] = [
                'status' => 'Delivered',
                'description' => 'Package delivered successfully. Signed by customer.',
                'location' => $order->shipping_city,
                'time' => $order->updated_at->format('d M Y, h:i A'),
                'completed' => true
            ];
        }

        return array_reverse($timeline);
    }
}
