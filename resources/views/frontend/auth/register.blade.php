@extends('layouts.app')

@section('content')
<section class="py-5" style="background-color: var(--cream-bg);">
    <div class="container" style="max-width: 500px;">
        <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm border" style="border-color: var(--border-color) !important;">
            <div class="text-center mb-4">
                <h2 class="font-heading fw-bold text-dark m-0">Create Account</h2>
                <p class="text-muted mt-1" style="font-size: 0.9rem;">Join RohidaFarm Organic Platform</p>
            </div>

            <form action="{{ route('register.submit') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Full Name *</label>
                    <input type="text" name="name" class="form-control bg-light border p-2.5 @error('name') is-invalid @enderror" placeholder="Your Full Name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Email Address *</label>
                    <input type="email" name="email" class="form-control bg-light border p-2.5 @error('email') is-invalid @enderror" placeholder="yourname@domain.com" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Mobile Number *</label>
                    <input type="text" name="phone" class="form-control bg-light border p-2.5 @error('phone') is-invalid @enderror" placeholder="10 Digit Number" value="{{ old('phone') }}" required>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Password *</label>
                    <input type="password" name="password" class="form-control bg-light border p-2.5 @error('password') is-invalid @enderror" placeholder="Min. 8 characters" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Confirm Password *</label>
                    <input type="password" name="password_confirmation" class="form-control bg-light border p-2.5" placeholder="Repeat Password" required>
                </div>

                <button type="submit" class="btn btn-premium w-100 py-3 rounded-pill text-uppercase font-heading fw-bold shadow-sm" style="font-size: 0.85rem; letter-spacing: 0.5px;">Register Account</button>
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

            <div class="text-center mt-4" style="font-size: 0.85rem;">
                <span class="text-muted">Already have an account?</span>
                <a href="{{ route('login') }}" class="text-success fw-bold text-decoration-none ms-1">Login here</a>
            </div>
        </div>
    </div>
</section>
@endsection
