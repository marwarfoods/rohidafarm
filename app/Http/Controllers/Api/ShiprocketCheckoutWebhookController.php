<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShiprocketCheckoutOrder;
use App\Services\ShiprocketCheckoutOrderProcessor;
use App\Services\ShiprocketCheckoutService;
use Illuminate\Http\Request;

/**
 * Order webhook sent by Shiprocket Checkout to the seller's registered URL.
 *
 * The official docs define no signature for this webhook, so it is protected by a secret
 * URL token, and the payload is only used as a hint: the order is re-verified with the
 * HMAC-authenticated Order Details API before anything is created. Webhooks may be sent
 * more than once — processing is idempotent. Shiprocket expects HTTP 200.
 */
class ShiprocketCheckoutWebhookController extends Controller
{
    public function order(Request $request, string $token, ShiprocketCheckoutService $checkout, ShiprocketCheckoutOrderProcessor $processor)
    {
        if (!$checkout->isValidWebhookToken($token)) {
            abort(404);
        }

        $data = $request->validate([
            'order_id' => ['required', 'string', 'max:64', 'regex:/^[A-Za-z0-9_-]+$/'],
            'status' => 'nullable|string|max:20',
        ]);

        $checkout->touch('shiprocket_checkout_last_webhook_at');

        $record = ShiprocketCheckoutOrder::firstOrCreate(
            ['checkout_order_id' => $data['order_id']],
            ['source' => 'webhook', 'status' => 'CREATED']
        );
        $record->update(['last_webhook_at' => now()]);

        if (!$checkout->hasCredentials()) {
            // Cannot verify yet — kept for the reconcile job once credentials exist.
            $record->update(['last_error' => 'Webhook received but API credentials are not configured.']);

            return response()->json(['ok' => true, 'queued' => true]);
        }

        $record = $processor->sync($data['order_id'], 'webhook');

        return response()->json([
            'ok' => true,
            'status' => $record->status,
            'order_created' => (bool) $record->order_id,
        ]);
    }
}
