<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Mail\ForgotPasswordMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PasswordResetController extends Controller
{
    /**
     * Show the form to request a password reset link.
     */
    public function showLinkRequestForm()
    {
        return view('frontend.auth.forgot-password');
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'cf-turnstile-response' => [new \App\Rules\Turnstile()],
        ]);

        $trashedUser = User::withTrashed()->where('email', $request->email)->first();
        if ($trashedUser && $trashedUser->trashed()) {
            return back()->withErrors(['email' => 'Your account has been deactivated. Please contact administration.']);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'We could not find a user with that email address.']);
        }

        // Generate a random token
        $token = Str::random(60);

        // Store token in database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        $resetLink = route('password.reset', ['token' => $token, 'email' => $user->email]);

        try {
            Mail::to($user->email)->send(new ForgotPasswordMail($resetLink, $user->name));
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Failed to send email. Please try again later.']);
        }

        return back()->with('status', 'We have emailed your password reset link!');
    }

    /**
     * Show the password reset form.
     */
    public function showResetForm(Request $request, $token)
    {
        $email = $request->query('email', $request->input('email'));

        if (!$email || !$token) {
            return redirect()->route('login')->with('error', 'This password reset link is invalid or has expired.');
        }

        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$record || !Hash::check($token, $record->token)) {
            return redirect()->route('login')->with('error', 'This password reset link is invalid or has already been used.');
        }

        // Check if token is older than 10 minutes
        if (\Carbon\Carbon::parse($record->created_at)->diffInMinutes(now()) >= 10) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return redirect()->route('login')->with('error', 'This password reset link has expired (valid for 10 minutes only). Please request a new one.');
        }

        return view('frontend.auth.reset-password', ['token' => $token, 'email' => $email]);
    }

    /**
     * Reset the given user's password.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
            'cf-turnstile-response' => [new \App\Rules\Turnstile()],
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return redirect()->route('login')->with('error', 'This password reset link is invalid or has already been used.');
        }

        // Check if token is older than 10 minutes
        if (\Carbon\Carbon::parse($record->created_at)->diffInMinutes(now()) >= 10) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return redirect()->route('login')->with('error', 'This password reset link has expired (valid for 10 minutes only). Please request a new one.');
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'We could not find an active account with that email address.');
        }

        $user->forceFill([
            'password' => Hash::make($request->password)
        ])->save();

        // One-time use: Delete immediately so this reset link can never be used again
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        return redirect()->route('login')->with('success', 'Your password has been reset successfully! Please login with your new password.');
    }
}
