<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ShiprocketEngageService;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;

class ShiprocketEngageController extends Controller
{
    use LogsActivity;

    /**
     * Test the Shiprocket Engage API connection (works on localhost and live).
     */
    public function testConnection(Request $request, ShiprocketEngageService $engage)
    {
        $request->validate([
            'email' => 'nullable|email',
            'password' => 'nullable|string|max:255',
        ]);

        // Unsaved form values are tested only when both are typed in.
        $email = trim((string) $request->input('email'));
        $password = (string) $request->input('password');
        $useForm = $email !== '' && $password !== '';

        $result = $engage->testConnection($useForm ? $email : null, $useForm ? $password : null);

        self::logActivity('shiprocket_engage_test', 'Tested Shiprocket Engage connection: ' . ($result['ok'] ? 'success' : 'failed'));

        $failed = collect($result['checks'])->firstWhere('status', 'fail');

        return response()->json([
            'status' => $result['ok'] ? 'success' : 'error',
            'message' => $result['ok']
                ? 'Connection Successful'
                : 'Connection Failed — ' . ($failed['detail'] ?? 'Unknown error'),
            'source' => ShiprocketEngageService::sourceLabel($result['source']),
            'checks' => $result['checks'],
        ], $result['ok'] ? 200 : 422);
    }
}
