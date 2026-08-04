@extends('mail.components.layout', ['title' => 'Welcome to RohidaFarm!'])

@section('content')
<h1 class="email-title">Welcome to RohidaFarm!</h1>
<p class="email-text">Hi {{ $user->name }},</p>
<p class="email-text">Thank you for placing your first order with us! We have automatically created an account for you so you can easily track your orders and checkout faster next time.</p>

<div class="bg-light p-3 mb-3 text-center">
    <p class="email-text" style="margin-bottom: 5px;"><strong>Your Login Email:</strong> {{ $user->email }}</p>
    <p class="email-text" style="margin-bottom: 0;"><strong>Your Temporary Password:</strong> <span style="background: #e9ecef; padding: 2px 8px; border-radius: 4px; font-family: monospace; letter-spacing: 1px;">{{ $password }}</span></p>
</div>

<p class="email-text">We recommend changing your password after you log in for the first time.</p>
<div class="text-center">
    <a href="{{ route('login') }}" class="btn">Login to Your Account</a>
</div>
@endsection
