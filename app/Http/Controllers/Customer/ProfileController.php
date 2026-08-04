<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\SeoService;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    use LogsActivity;

    protected $seoService;

    public function __construct(SeoService $seoService)
    {
        $this->seoService = $seoService;
    }

    /**
     * Show profile update form.
     */
    public function index()
    {
        $user = Auth::user();
        $user->syncAddressFromLastOrder(); // Auto-pull address from last order if book is empty
        $seo = $this->seoService->generateTags(['title' => 'Profile Settings']);
        return view('customer.profile', compact('user', 'seo'));
    }

    /**
     * Update customer meta details.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
        ]);

        $user->update($request->only('name', 'email', 'phone'));

        self::logActivity('profile_update', 'Updated personal profile details.');

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Change user password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->input('password'))
        ]);

        self::logActivity('password_change', 'Updated profile account password.');

        return back()->with('success', 'Password updated successfully.');
    }
}
