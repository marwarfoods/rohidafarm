@extends('mail.components.layout', ['title' => 'New Contact Form Submission - RohidaFarm Admin'])

@section('content')
<div class="text-center" style="margin-bottom: 25px;">
    <h1 style="color: #1a4f3b; font-size: 24px; margin-bottom: 8px; font-family: 'Georgia', serif;">New Contact Form Entry</h1>
    <p style="color: #666; font-size: 15px;">A new inquiry has been submitted via the website contact form.</p>
</div>

<div class="info-box" style="background-color: #ffffff; border: 1px solid #e1e7e3; border-radius: 8px; padding: 22px; margin-bottom: 25px; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
    <h3 style="font-size: 14px; margin-bottom: 15px; color: #1a4f3b; border-bottom: 2px solid #e1e7e3; padding-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Contact Details</h3>
    
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 6px 0; color: #777; font-size: 14px; width: 120px;"><strong>Name:</strong></td>
            <td style="padding: 6px 0; color: #222; font-size: 14px; font-weight: 600;">{{ $inquiry->name }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 0; color: #777; font-size: 14px;"><strong>Email:</strong></td>
            <td style="padding: 6px 0; color: #222; font-size: 14px;"><a href="mailto:{{ $inquiry->email }}" style="color: #1a4f3b; text-decoration: underline;">{{ $inquiry->email }}</a></td>
        </tr>
        @if($inquiry->phone)
        <tr>
            <td style="padding: 6px 0; color: #777; font-size: 14px;"><strong>Phone:</strong></td>
            <td style="padding: 6px 0; color: #222; font-size: 14px;"><a href="tel:{{ $inquiry->phone }}" style="color: #1a4f3b; text-decoration: none;">{{ $inquiry->phone }}</a></td>
        </tr>
        @endif
        @if($inquiry->subject)
        <tr>
            <td style="padding: 6px 0; color: #777; font-size: 14px;"><strong>Subject:</strong></td>
            <td style="padding: 6px 0; color: #222; font-size: 14px;">{{ $inquiry->subject }}</td>
        </tr>
        @endif
        <tr>
            <td style="padding: 6px 0; color: #777; font-size: 14px;"><strong>Submitted On:</strong></td>
            <td style="padding: 6px 0; color: #666; font-size: 13px;">{{ $inquiry->created_at ? $inquiry->created_at->format('d M Y, h:i A') : now()->format('d M Y, h:i A') }}</td>
        </tr>
    </table>

    <div style="margin-top: 18px; padding-top: 14px; border-top: 1px dashed #e1e7e3;">
        <strong style="color: #1a4f3b; font-size: 14px; display: block; margin-bottom: 6px;">Message Content:</strong>
        <div style="background-color: #f7f9f7; border: 1px solid #e1e7e3; border-radius: 6px; padding: 14px; font-size: 14px; color: #333; line-height: 1.6; white-space: pre-wrap;">{{ $inquiry->message }}</div>
    </div>
</div>

<div class="text-center" style="margin-top: 25px;">
    <a href="{{ route('admin.contact-inquiries.index') }}" class="btn" style="background-color: #1a4f3b; color: #ffffff; padding: 12px 28px; border-radius: 30px; text-decoration: none; font-weight: bold; text-transform: uppercase; font-size: 13px; display: inline-block; letter-spacing: 0.5px;">View in Admin Panel</a>
</div>
@endsection
