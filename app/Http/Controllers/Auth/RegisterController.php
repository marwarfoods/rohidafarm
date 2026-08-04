<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    use LogsActivity;

    /**
     * Show registration form.
     */
    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('frontend.auth.register');
    }

    /**
     * Handle user registration.
     */
    public function register(RegisterRequest $request)
    {
        $role = Role::where('name', 'customer')->first();

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'password' => Hash::make($request->input('password')),
            'role' => 'customer',
        ]);

        if ($role) {
            $user->roles()->attach($role);
        }

        // Automatically log in user
        Auth::login($user);

        self::logActivity('register', 'User registered a new retail account.');

        return redirect()->route('customer.dashboard')->with('success', 'Welcome to RohidaFarm! Account created successfully.');
    }
}
