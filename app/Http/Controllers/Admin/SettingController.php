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
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Bulk save configurations.
     */
    public function store(Request $request)
    {
        $rules = [
            'settings' => 'required|array',
            'settings.*' => 'nullable|string'
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
                if ($type === 'boolean') {
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
                }
            } else {
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

        self::logActivity('settings_update', 'Updated dynamic system configuration options.');

        return back()->with('success', 'Site settings updated successfully.');
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
}
