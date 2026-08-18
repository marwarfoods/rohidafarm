@extends('layouts.app')

@section('content')
<section class="py-5" style="background-color: var(--cream-bg);">
    <div class="container" style="max-width: 500px;">
        <!-- Alerts -->
        @if(session('error'))
            <div class="alert alert-danger rounded-3 mb-4 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success rounded-3 mb-4 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm border" style="border-color: var(--border-color) !important;">
            <div class="text-center mb-4">
                <h2 class="font-heading fw-bold text-dark m-0">Welcome Back</h2>
                <p class="text-muted mt-1" style="font-size: 0.9rem;">Sign in to your RohidaFarm Account</p>
            </div>

            <!-- Email & Password Form -->
            <form action="{{ route('login.submit') }}" method="POST" id="emailLoginForm">
                @csrf
                <div class="mb-3">
                    <label for="login" class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Email or Mobile Number</label>
                    <input type="text" name="login" class="form-control bg-light border p-2.5 @error('login') is-invalid @enderror" placeholder="customer@rohidafarm.com or 8888888888" value="{{ old('login') }}" required>
                    @error('login')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <label for="password" class="form-label fw-semibold text-dark m-0" style="font-size: 0.85rem;">Password</label>
                        <a href="{{ route('password.request') }}" class="text-success text-decoration-none fw-semibold" style="font-size: 0.8rem;">Forgot Password?</a>
                    </div>
                    <input type="password" name="password" class="form-control bg-light border p-2.5" placeholder="••••••••" required>
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                    <label class="form-check-label text-muted" for="remember" style="font-size: 0.85rem;">Remember me</label>
                </div>

                <x-turnstile />

                <button type="submit" class="btn btn-premium w-100 py-3 rounded-pill text-uppercase font-heading fw-bold shadow-sm" style="font-size: 0.85rem; letter-spacing: 0.5px;">Login Account</button>
            </form>

            <!-- Dynamic Google OAuth Button -->
            @if(\App\Models\Setting::get('google_login_enabled'))
                <div class="text-center my-4 position-relative">
                    <hr style="border-color: var(--border-color);">
                    <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted" style="font-size: 0.8rem;">OR</span>
                </div>

                <a href="{{ route('auth.google') }}" class="btn btn-outline-dark w-100 py-2.5 rounded-pill d-flex align-items-center justify-content-center gap-2 mb-3 shadow-2xs" style="font-size: 0.88rem; font-family: var(--font-body); border-color: #DDD;">
                    <svg width="18" height="18" viewBox="0 0 18 18">
                        <path fill="#4285F4" d="M17.64 9.2c0-.63-.06-1.25-.16-1.84H9v3.47h4.84c-.21 1.12-.84 2.07-1.79 2.7l2.76 2.13c1.61-1.49 2.54-3.69 2.54-6.46z"/>
                        <path fill="#34A853" d="M9 18c2.43 0 4.47-.8 5.96-2.18l-2.76-2.13c-.76.51-1.74.82-3.2.82-2.46 0-4.55-1.66-5.3-3.89l-2.85 2.2C2.34 15.42 5.37 18 9 18z"/>
                        <path fill="#FBBC05" d="M3.7 10.62c-.19-.58-.3-1.2-.3-1.84s.11-1.26.3-1.84L.85 4.74C.3 5.86 0 7.13 0 8.5s.3 2.64.85 3.76l2.85-2.2z"/>
                        <path fill="#EA4335" d="M9 3.58c1.32 0 2.5.45 3.44 1.35l2.58-2.58C13.47.8 11.43 0 9 0 5.37 0 2.34 2.58.85 5.76l2.85 2.2C4.45 5.24 6.54 3.58 9 3.58z"/>
                    </svg>
                    <span>Continue with Google</span>
                </a>
            @endif

            <!-- Register Redirect -->
            <div class="text-center mt-4" style="font-size: 0.85rem;">
                <span class="text-muted">New to RohidaFarm?</span>
                <a href="{{ route('register') }}" class="text-success fw-bold text-decoration-none ms-1">Create an Account</a>
            </div>
        </div>
    </div>
</section>
@endsection
