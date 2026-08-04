<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService
{
    /**
     * Process order payment.
     */
    public function process(Order $order, string $paymentMethod, ?string $gatewayId = null): Payment
    {
        return DB::transaction(function () use ($order, $paymentMethod, $gatewayId) {
            $transactionId = $gatewayId ?: 'TXN-' . strtoupper(Str::random(12));
            
            $status = 'pending';
            if ($paymentMethod === 'wallet') {
                $status = 'successful';
            } elseif ($paymentMethod !== 'cod') {
                // Simulating successful gateway completion
                $status = 'successful';
            }

            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'transaction_id' => $transactionId,
                'amount' => $order->total,
                'status' => $status,
                'payload' => [
                    'gateway' => $paymentMethod,
                    'transaction_ref' => $transactionId,
                    'timestamp' => now()->toIso8601String(),
                    'code' => 'PAYMENT_SUCCESS',
                    'message' => 'Processed via RohidaFarm Payment Simulator.'
                ]
            ]);

            // Update order payment status
            if ($status === 'successful') {
                $order->update([
                    'payment_status' => 'paid'
                ]);
            }

            return $payment;
        });
    }

    /**
     * Simulate Payment Webhook.
     */
    public function handleWebhook(array $payload): bool
    {
        $orderNo = $payload['order_number'] ?? null;
        $status = $payload['status'] ?? 'failed';
        $gatewayTxnId = $payload['transaction_id'] ?? null;

        if (!$orderNo) return false;

        return DB::transaction(function () use ($orderNo, $status, $gatewayTxnId) {
            $order = Order::where('order_number', $orderNo)->first();
            if (!$order) return false;

            $payment = Payment::where('order_id', $order->id)->first();
            if ($payment) {
                $payment->update([
                    'status' => $status === 'success' ? 'successful' : 'failed',
                    'transaction_id' => $gatewayTxnId ?: $payment->transaction_id
                ]);
            }

            if ($status === 'success') {
                $order->update(['payment_status' => 'paid']);
            } else {
                $order->update(['payment_status' => 'failed']);
            }

            return true;
        });
    }

    /**
     * Refund payment (Gateway level).
     */
    public function refund(Order $order): Payment
    {
        return DB::transaction(function () use ($order) {
            $originalPayment = Payment::where('order_id', $order->id)
                ->where('status', 'successful')
                ->first();

            if (!$originalPayment) {
                throw new \Exception("No successful payments found to refund.");
            }

            // Create refund payment log
            $refundTxnId = 'REF-' . strtoupper(Str::random(12));
            $refund = Payment::create([
                'order_id' => $order->id,
                'payment_method' => $order->payment_method,
                'transaction_id' => $refundTxnId,
                'amount' => $order->total,
                'status' => 'refunded',
                'payload' => [
                    'original_txn' => $originalPayment->transaction_id,
                    'timestamp' => now()->toIso8601String(),
                    'message' => 'Simulated refund processed successfully.'
                ]
            ]);

            $order->update(['payment_status' => 'refunded']);

            return $refund;
        });
    }
}
