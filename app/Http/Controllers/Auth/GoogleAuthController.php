<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Configure Socialite Google credentials dynamically from DB Settings.
     */
    protected function setupGoogleConfig(): void
    {
        $clientId = Setting::get('google_client_id');
        $clientSecret = Setting::get('google_client_secret');

        config([
            'services.google' => [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect' => route('auth.google.callback'),
            ]
        ]);
    }

    /**
     * Redirect user to Google OAuth page.
     */
    public function redirectToGoogle()
    {
        if (!Setting::get('google_login_enabled', false)) {
            return redirect()->route('login')->with('error', 'Google Login is currently disabled by admin.');
        }

        $clientId = Setting::get('google_client_id');
        $clientSecret = Setting::get('google_client_secret');

        if (empty($clientId) || empty($clientSecret)) {
            return redirect()->route('login')->with('error', 'Google Login is not fully configured yet in Admin settings.');
        }

        $this->setupGoogleConfig();

        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth Callback.
     */
    public function handleGoogleCallback()
    {
        if (!Setting::get('google_login_enabled', false)) {
            return redirect()->route('login')->with('error', 'Google Login is currently disabled.');
        }

        $this->setupGoogleConfig();

        try {
            $googleUser = Socialite::driver('google')->user();
            $email = trim(strtolower($googleUser->getEmail()));
            $googleId = $googleUser->getId();

            $user = User::withTrashed()
                ->where(function ($query) use ($googleId, $email) {
                    $query->where('google_id', $googleId)
                          ->orWhere('email', $email);
                })
                ->first();

            if ($user) {
                // If the account has been deactivated / soft-deleted, block login
                if ($user->trashed()) {
                    return redirect()->route('login')->with('error', 'Your account has been deactivated. Please contact administration.');
                }

                $updates = [];
                if (!$user->google_id) {
                    $updates['google_id'] = $googleId;
                }
                if (!$user->avatar && $googleUser->getAvatar()) {
                    $updates['avatar'] = $googleUser->getAvatar();
                }
                if (!$user->email_verified_at) {
                    $updates['email_verified_at'] = now();
                }

                if (!empty($updates)) {
                    $user->update($updates);
                }
            } else {
                $user = User::create([
                    'uuid' => (string) Str::uuid(),
                    'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Google User',
                    'email' => $email,
                    'google_id' => $googleId,
                    'avatar' => $googleUser->getAvatar(),
                    'password' => bcrypt(Str::random(24)),
                    'email_verified_at' => now(),
                    'role' => 'customer',
                ]);

                $role = \App\Models\Role::where('name', 'customer')->first();
                if ($role) {
                    $user->roles()->attach($role);
                }
            }

            Auth::login($user, true);

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'))->with('success', 'Logged in with Google successfully!');
            }

            return redirect()->intended(route('customer.dashboard'))->with('success', 'Logged in with Google successfully!');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Google authentication failed: ' . $e->getMessage());
        }
    }
}
