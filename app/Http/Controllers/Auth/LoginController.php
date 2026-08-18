<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Services\CartService;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use LogsActivity;

    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Show single login page.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectUser();
        }
        return view('frontend.auth.login');
    }

    /**
     * Authenticate credentials.
     */
    public function login(LoginRequest $request)
    {
        $loginValue = $request->input('login');
        $password = $request->input('password');
        $remember = $request->has('remember');

        // Determine if logging in using email or phone number
        $fieldType = filter_var($loginValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (Auth::attempt([$fieldType => $loginValue, 'password' => $password], $remember)) {
            $request->session()->regenerate();
            
            $user = Auth::user();

            // Sync session cart items to database for customer
            if ($user->role === 'customer') {
                $this->cartService->syncSessionToDb();
            }

            self::logActivity('login', "User logged in using {$fieldType}.");

            return $this->redirectUser();
        }

        // Check if user exists but has been deactivated / soft-deleted
        $trashedUser = User::withTrashed()->where($fieldType, $loginValue)->first();
        if ($trashedUser && $trashedUser->trashed()) {
            return back()->withErrors([
                'login' => 'Your account has been deactivated. Please contact administration.',
            ])->withInput($request->only('login', 'remember'));
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('login', 'remember'));
    }



    /**
     * Terminate session.
     */
    public function logout(Request $request)
    {
        self::logActivity('logout', 'User logged out.');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Logged out successfully.');
    }

    /**
     * Role-based redirection helper.
     */
    protected function redirectUser()
    {
        if (Auth::user()->isAdmin() || Auth::user()->roles()->exists()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('customer.dashboard');
    }
}
