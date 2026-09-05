<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Shipment;

class DelhiveryService
{
    /**
     * Estimate delivery date based on customer shipping pincode.
     */
    public function estimateDelivery(string $pincode): array
    {
        $pincodeInt = (int)$pincode;

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
            'courier' => 'Delhivery',
            'cod_available' => true
        ];
    }

    public function createShipment(Order $order): Shipment
    {
        $token = \App\Models\Setting::get('delhivery_api_token');
        $pickupLocation = \App\Models\Setting::get('delhivery_pickup_location');

        if ($token && $pickupLocation) {
            return $this->createRealShipment($order, $token, $pickupLocation);
        }

        // Fallback to Sandbox Simulator
        return $this->createSimulatedShipment($order);
    }

    private function createRealShipment(Order $order, string $token, string $pickupLocation): Shipment
    {
        $clientName = \App\Models\Setting::get('delhivery_client_name', $pickupLocation);

        // 1. Fetch a waybill number
        $waybillRes = \Illuminate\Support\Facades\Http::withHeaders(['Authorization' => 'Token ' . $token])
            ->get('https://track.delhivery.com/waybill/api/bulk/json/', [
                'count' => 1,
                'cl' => $clientName,
            ]);

        if (!$waybillRes->successful() || trim($waybillRes->body()) === '') {
            \Log::error('Delhivery Waybill Generation Failed', ['response' => $waybillRes->body()]);
            throw new \Exception('Delhivery Authentication Failed. Please check the API Token and Client Name in settings.');
        }

        $waybill = trim(str_replace(['[', ']', '"'], '', $waybillRes->body()));

        // 2. Prepare shipment payload
        $totalWeight = 0;
        $totalQuantity = 0;
        $productNames = [];
        foreach ($order->items as $item) {
            $weightInKg = $item->product->weight ? ((float)$item->product->weight / 1000) : 0.5;
            $totalWeight += ($weightInKg * $item->quantity);
            $totalQuantity += $item->quantity;
            $productNames[] = $item->product_name;
        }

        $isCod = strtolower($order->payment_method) === 'cod';

        $shipmentData = [
            "name" => $order->shipping_name,
            "add" => trim($order->shipping_address_line1 . ' ' . $order->shipping_address_line2),
            "pin" => $order->shipping_postal_code,
            "city" => $order->shipping_city,
            "state" => $order->shipping_state,
            "country" => "India",
            "phone" => $order->shipping_phone,
            "order" => $order->order_number,
            "payment_mode" => $isCod ? "COD" : "Prepaid",
            "products_desc" => implode(', ', array_slice($productNames, 0, 5)),
            "cod_amount" => $isCod ? $order->total : 0,
            "order_date" => $order->created_at->format('Y-m-d H:i:s'),
            "total_amount" => $order->total,
            "quantity" => $totalQuantity ?: 1,
            "waybill" => $waybill,
            "shipment_width" => 10,
            "shipment_height" => 10,
            "weight" => $totalWeight > 0 ? $totalWeight : 0.5,
            "shipping_mode" => "Surface",
            "address_type" => "home",
        ];

        $payload = [
            "shipments" => [$shipmentData],
            "pickup_location" => [
                "name" => $pickupLocation,
            ],
        ];

        // 3. Create the shipment in Delhivery
        $createRes = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Token ' . $token,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])
            ->asForm()
            ->post('https://track.delhivery.com/api/cmu/create.json', [
                'format' => 'json',
                'data' => json_encode($payload),
            ]);

        $responseData = $createRes->json();
        $package = $responseData['packages'][0] ?? null;

        if (!$createRes->successful() || !$package || ($package['status'] ?? '') !== 'Success') {
            \Log::error('Delhivery Shipment Creation Failed', ['response' => $responseData]);

            $errorMessage = 'Delhivery API Error.';
            if ($package && !empty($package['remarks'])) {
                $remarks = is_array($package['remarks']) ? implode(' | ', $package['remarks']) : $package['remarks'];
                $errorMessage .= ' Details: ' . $remarks;
            } elseif (isset($responseData['rmk'])) {
                $errorMessage .= ' Details: ' . $responseData['rmk'];
            }

            throw new \Exception($errorMessage);
        }

        $shipment = Shipment::create([
            'order_id' => $order->id,
            'delhivery_shipment_id' => (string)$waybill,
            'delhivery_order_id' => $order->order_number,
            'awb_code' => $waybill,
            'courier_name' => 'Delhivery',
            'status' => 'Order Created',
            'response_payload' => $responseData,
        ]);

        $order->update([
            'shipment_status' => 'Order Created in Delhivery',
            'tracking_number' => $waybill,
            'tracking_url' => "https://www.delhivery.com/track/package/{$waybill}",
        ]);

        \Log::info('Delhivery Shipment Created', ['order_id' => $order->id, 'waybill' => $waybill]);

        return $shipment;
    }

    private function createSimulatedShipment(Order $order): Shipment
    {
        $delhiveryOrderId = $order->order_number;
        $delhiveryShipmentId = 'DL-SHIP-' . rand(1000000, 9999999);
        $waybill = (string) rand(1000000000, 9999999999);

        $shipment = Shipment::create([
            'order_id' => $order->id,
            'delhivery_shipment_id' => $delhiveryShipmentId,
            'delhivery_order_id' => $delhiveryOrderId,
            'awb_code' => $waybill,
            'courier_name' => 'Delhivery',
            'status' => 'AWB Assigned',
            'response_payload' => [
                'status' => 'Success',
                'remarks' => ['Order and shipment generated in sandbox.'],
            ]
        ]);

        // Update the order with tracking details
        $order->update([
            'tracking_number' => $waybill,
            'tracking_carrier' => 'Delhivery',
            'tracking_url' => "https://www.delhivery.com/track/package/{$waybill}",
            'shipment_status' => 'AWB Assigned'
        ]);

        return $shipment;
    }

    /**
     * Build a customer-facing tracking timeline for the order.
     */
    public function getTrackingTimeline(Order $order): array
    {
        $timeline = [];
        $shipment = $order->shipment;
        $courierName = $shipment ? $shipment->courier_name : ($order->tracking_carrier ?: 'Delhivery');
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
