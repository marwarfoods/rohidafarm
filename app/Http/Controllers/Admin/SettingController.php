<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\SmtpService;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    use LogsActivity;

    protected $smtpService;

    public function __construct(SmtpService $smtpService)
    {
        $this->smtpService = $smtpService;
    }

    /**
     * Show site settings management layout.
     */
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        $globalFaqs = \App\Models\Faq::orderBy('sort_order')->get();
        return view('admin.settings.index', compact('settings', 'globalFaqs'));
    }

    /**
     * Bulk save configurations.
     */
    public function store(Request $request)
    {
        $rules = [
            'settings' => 'nullable|array',
            'settings.*' => 'nullable|string',
            'global_faqs' => 'nullable|array',
            'settings.shiprocket_checkout_environment' => 'nullable|in:production,staging',
            'settings.shiprocket_checkout_base_url' => 'nullable|url:https|max:255',
            'settings.shiprocket_checkout_api_key' => 'nullable|string|max:255',
            'settings.shiprocket_checkout_secret_key' => 'nullable|string|max:255',
            'clear_secrets' => 'nullable|array',
        ];
        
        $request->validate($rules);
        $settings = $request->input('settings', []);

        // Handle file uploads
        if ($request->hasFile('settings_files')) {
            foreach ($request->file('settings_files') as $key => $file) {
                if ($file->isValid()) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/settings'), $filename);
                    
                    $settingModel = Setting::where('key', $key)->first();
                    $path = 'uploads/settings/' . $filename;
                    
                    if ($settingModel) {
                        Setting::set($key, $path, $settingModel->type, $settingModel->group, $settingModel->description);
                    } else {
                        Setting::set($key, $path, 'file', 'branding', ucfirst(str_replace('_', ' ', $key)));
                    }
                }
            }
        }

        // Secrets: stored encrypted, never echoed back; blank keeps the saved value.
        $encryptedKeys = [
            'shiprocket_engage_password' => ['shiprocket_engage', 'Shiprocket Engage API user password (encrypted)'],
            'shiprocket_checkout_api_key' => ['shiprocket_checkout', 'Shiprocket Checkout API Key (encrypted)'],
            'shiprocket_checkout_secret_key' => ['shiprocket_checkout', 'Shiprocket Checkout Secret Key (encrypted)'],
        ];
        $clearSecrets = (array) $request->input('clear_secrets', []);
        foreach ($encryptedKeys as $key => [$group, $description]) {
            if (array_key_exists($key, $settings)) {
                $secret = trim((string) $settings[$key]);
                unset($settings[$key]);
                if ($secret !== '') {
                    Setting::set($key, \Illuminate\Support\Facades\Crypt::encryptString($secret), 'encrypted', $group, $description);
                }
            }
            if (filter_var($clearSecrets[$key] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                Setting::set($key, null, 'encrypted', $group, $description);
            }
        }

        foreach ($settings as $key => $value) {
            $settingModel = Setting::where('key', $key)->first();
            
            $type = 'string';
            $group = 'general';
            $description = null;

            if ($settingModel) {
                $type = $settingModel->type;
                $group = $settingModel->group;
                $description = $settingModel->description;
                
                // If it is boolean, make sure it is handled
                if ($type === 'boolean' || str_ends_with($key, '_enabled')) {
                    $type = 'boolean';
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
                }
            } else {
                if (str_ends_with($key, '_enabled')) {
                    $type = 'boolean';
                    $group = 'integrations';
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
                } elseif (str_starts_with($key, 'meta_pixel_')) {
                    $group = 'integrations';
                }
                if (str_starts_with($key, 'shiprocket_engage_')) {
                    $group = 'shiprocket_engage';
                }
                if (str_starts_with($key, 'shiprocket_checkout_')) {
                    $group = 'shiprocket_checkout';
                }
                // If it's the state-wise JSON, force it to be JSON type
                if ($key === 'tax_state_wise') {
                    $type = 'json';
                }
            }

            // If this is state wise json, let's make sure it is valid JSON
            if ($key === 'tax_state_wise' && is_string($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $value = $decoded; // Setting::set will json_encode it if it's an array
                    $type = 'json';
                }
            }

            Setting::set($key, $value, $type, $group, $description);
        }

        // Synchronize delivery charge model if configured
        if (isset($settings['default_delivery_charge']) || isset($settings['free_shipping_threshold'])) {
            $dc = \App\Models\DeliveryCharge::first();
            if ($dc) {
                $dcUpdate = [];
                if (isset($settings['default_delivery_charge'])) {
                    $dcUpdate['charge_amount'] = (float) $settings['default_delivery_charge'];
                }
                if (isset($settings['free_shipping_threshold'])) {
                    $dcUpdate['min_order_amount'] = (float) $settings['free_shipping_threshold'];
                }
                $dc->update($dcUpdate);
            }
        }

        // Handle Global FAQs synchronization
        if ($request->has('global_faqs_submitted')) {
            \App\Models\Faq::truncate();
            $rawFaqs = $request->input('global_faqs', []);
            foreach ($rawFaqs as $index => $faqData) {
                $q = trim($faqData['question'] ?? '');
                $a = trim($faqData['answer'] ?? '');
                if (!empty($q) && !empty($a)) {
                    \App\Models\Faq::create([
                        'question' => $q,
                        'answer' => $a,
                        'category' => 'General',
                        'sort_order' => $index,
                        'is_active' => true,
                    ]);
                }
            }
        }

        self::logActivity('settings_update', 'Updated dynamic system configuration options.');

        $activeTab = $request->input('active_tab', '#site');
        if (!str_starts_with($activeTab, '#')) {
            $activeTab = '#' . $activeTab;
        }

        return redirect()->to(route('admin.settings.index') . $activeTab)->with('success', 'Site settings updated successfully.');
    }

    /**
     * Diagnostic testing for dynamic SMTP credentials.
     */
    public function testSmtp(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email'
        ]);

        $recipient = $request->input('test_email');
        $success = $this->smtpService->sendTestEmail($recipient);

        if ($success) {
            return back()->with('success', "Test email sent successfully to {$recipient}. Your SMTP settings are valid!");
        }

        return back()->with('error', "Failed to send test email. Please check your SMTP credentials or logs.");
    }

    /**
     * Test Google OAuth credentials configuration.
     */
    public function testGoogleOAuth(Request $request)
    {
        $clientId = $request->input('google_client_id') ?: Setting::get('google_client_id');
        $clientSecret = $request->input('google_client_secret') ?: Setting::get('google_client_secret');

        if (empty($clientId) || empty($clientSecret)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please enter both Google Client ID and Google Client Secret before testing.'
            ], 400);
        }

        if (!str_contains($clientId, '.apps.googleusercontent.com') && strlen($clientId) < 15) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Google Client ID format. A valid Client ID typically ends with .apps.googleusercontent.com'
            ], 400);
        }

        if (strlen($clientSecret) < 10) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Google Client Secret format. Please double check your secret key from Google Cloud Console.'
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Google OAuth configuration format valid! Redirect URL: ' . route('auth.google.callback')
        ]);
    }

    /**
     * Test Google reCAPTCHA v3 credentials.
     */
    public function testRecaptcha(Request $request)
    {
        $siteKey = trim($request->input('recaptcha_site_key') ?: Setting::get('recaptcha_site_key', ''));
        $secretKey = trim($request->input('recaptcha_secret_key') ?: Setting::get('recaptcha_secret_key', ''));

        if (empty($siteKey) || empty($secretKey)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please provide both reCAPTCHA Site Key and Secret Key before testing.'
            ], 400);
        }

        if (strlen($siteKey) < 20 || strlen($secretKey) < 20) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid reCAPTCHA key format. Google keys are 40 characters long and usually start with 6L...'
            ], 400);
        }

        try {
            // A dummy token: Google answers "invalid-input-secret" only when the secret itself is wrong.
            $response = \Illuminate\Support\Facades\Http::asForm()->timeout(8)->post(\App\Services\RecaptchaService::VERIFY_URL, [
                'secret' => $secretKey,
                'response' => 'test_dummy_verification_token',
            ]);

            $errorCodes = $response->json()['error-codes'] ?? [];

            if (in_array('invalid-input-secret', $errorCodes)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Google rejected this Secret Key (invalid-input-secret). Please check the Secret Key in your reCAPTCHA Admin Console.'
                ], 422);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Google reCAPTCHA Secret Key verified! Make sure the Site Key is a v3 (score based) key for this domain.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Could not connect to Google reCAPTCHA API: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test the Shiprocket configuration (auth + pickup location).
     * Requires HTTPS — meant to be run on the live/production site.
     */
    public function testShiprocket(Request $request)
    {
        if (!$request->secure()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Shiprocket testing requires HTTPS. Deploy to your live site and run this test there.'
            ], 400);
        }

        $email = trim($request->input('shiprocket_email') ?: Setting::get('shiprocket_email', ''));
        $password = $request->input('shiprocket_password') ?: Setting::get('shiprocket_password', '');

        if (empty($email) || empty($password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Enter your Shiprocket account email and password first, then save settings.'
            ], 400);
        }

        try {
            $result = app(\App\Services\ShiprocketService::class)->testConnection($email, $password);

            $message = '✅ Authenticated with Shiprocket successfully.';
            if (!empty($result['pickup_locations'])) {
                $message .= ' Pickup locations found: ' . implode(', ', $result['pickup_locations']) . '.';
            }
            if ($result['configured_pickup'] && !$result['pickup_ok']) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Login works, but the pickup location \"{$result['configured_pickup']}\" was not found in your Shiprocket account. Use one of: " . implode(', ', $result['pickup_locations'])
                ], 422);
            }

            return response()->json(['status' => 'success', 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Shiprocket test failed: ' . $e->getMessage()
            ], 422);
        }
    }
}
