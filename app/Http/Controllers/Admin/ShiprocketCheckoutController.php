<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ShiprocketCheckoutService;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;

class ShiprocketCheckoutController extends Controller
{
    use LogsActivity;

    /**
     * Test Shiprocket Checkout credentials with a real signed API call.
     */
    public function testConnection(Request $request, ShiprocketCheckoutService $checkout)
    {
        $request->validate([
            'api_key' => 'nullable|string|max:255',
            'secret_key' => 'nullable|string|max:255',
        ]);

        // Unsaved form values are used only if both are typed in; otherwise the saved ones.
        $apiKey = trim((string) $request->input('api_key'));
        $secret = (string) $request->input('secret_key');
        $useForm = $apiKey !== '' && $secret !== '';

        if (!$useForm && !$checkout->hasCredentials()) {
            return response()->json([
                'status' => 'error',
                'message' => '✗ Connection failed. Enter the API Key and Secret Key (or save them) first.',
                'details' => [],
            ], 422);
        }

        $result = $useForm ? $checkout->testConnection($apiKey, $secret) : $checkout->testConnection();

        self::logActivity('shiprocket_checkout_test', 'Tested Shiprocket Checkout connection: ' . ($result['ok'] ? 'success' : 'failed'));

        return response()->json([
            'status' => $result['ok'] ? 'success' : 'error',
            'message' => $result['message'],
            'details' => $result['details'],
        ], $result['ok'] ? 200 : 422);
    }

    /**
     * Rotate the secret token in the order webhook URL.
     */
    public function regenerateWebhook(ShiprocketCheckoutService $checkout)
    {
        \App\Models\Setting::set('shiprocket_checkout_webhook_token', null, 'string', 'shiprocket_checkout', null);
        $url = $checkout->webhookUrl();

        self::logActivity('shiprocket_checkout_webhook_rotate', 'Regenerated Shiprocket Checkout webhook URL.');

        return redirect()->to(route('admin.settings.index') . '#shiprocket-checkout')
            ->with('success', 'New webhook URL generated. Update it in Shiprocket Checkout: ' . $url);
    }
}
