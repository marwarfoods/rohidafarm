@extends('mail.components.layout', ['title' => 'Reset Your Password'])

@section('content')
<h1 class="email-title">Password Reset Request</h1>
<p class="email-text">Hi {{ $userName }},</p>
<p class="email-text">You are receiving this email because we received a password reset request for your account.</p>

<div class="text-center mt-3 mb-3">
    <a href="{{ $resetLink }}" class="btn">Reset Password</a>
</div>

<p class="email-text text-center">
    <small>This password reset link will expire in 60 minutes.</small>
</p>

<p class="email-text">If you did not request a password reset, no further action is required.</p>

<div class="bg-light p-3 mt-3">
    <p class="email-text" style="font-size: 12px; margin: 0; color: #777;">
        If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:<br>
        <a href="{{ $resetLink }}" style="word-break: break-all; color: #1a4f3b;">{{ $resetLink }}</a>
    </p>
</div>
@endsection
