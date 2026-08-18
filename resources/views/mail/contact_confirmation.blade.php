@extends('mail.components.layout', ['title' => 'Thank you for reaching out - RohidaFarm'])

@section('content')
<div class="text-center" style="margin-bottom: 30px;">
    <h1 style="color: #1a4f3b; font-size: 24px; margin-bottom: 10px; font-family: 'Georgia', serif;">Thank You for Contacting Us!</h1>
    <p style="color: #666; font-size: 16px;">Dear <strong>{{ $inquiry->name }}</strong>,</p>
    <p style="color: #444; font-size: 15px; max-width: 480px; margin: 10px auto; line-height: 1.6;">
        We have received your message. Our wellness team will review your inquiry and get back to you at <strong>{{ $inquiry->email }}</strong> as soon as possible.
    </p>
</div>

<div class="info-box" style="background-color: #f9fbf9; border: 1px solid #d8e8dc; border-radius: 8px; padding: 20px; margin-bottom: 25px;">
    <h3 style="font-size: 15px; margin-bottom: 12px; color: #1a4f3b; border-bottom: 1px solid #d8e8dc; padding-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Summary of Your Message</h3>
    
    @if($inquiry->subject)
    <p style="font-size: 14px; margin: 0 0 8px 0; color: #555;">
        <strong>Subject:</strong> {{ $inquiry->subject }}
    </p>
    @endif

    @if($inquiry->phone)
    <p style="font-size: 14px; margin: 0 0 8px 0; color: #555;">
        <strong>Phone:</strong> {{ $inquiry->phone }}
    </p>
    @endif

    <p style="font-size: 14px; margin: 0 0 4px 0; color: #555;">
        <strong>Message:</strong>
    </p>
    <div style="background-color: #ffffff; border: 1px solid #e2ece5; border-radius: 6px; padding: 12px; font-size: 14px; color: #333; line-height: 1.6; white-space: pre-wrap;">{{ $inquiry->message }}</div>
</div>

<div class="text-center" style="margin-top: 30px;">
    <p style="font-size: 14px; color: #666; margin-bottom: 15px;">In the meantime, feel free to explore our pure Vedic Bilona Ghee collection.</p>
    <a href="{{ route('shop.index') }}" class="btn" style="background-color: #1a4f3b; color: #ffffff; padding: 12px 28px; border-radius: 30px; text-decoration: none; font-weight: bold; text-transform: uppercase; font-size: 13px; display: inline-block; letter-spacing: 0.5px;">Explore Products</a>
</div>
@endsection
