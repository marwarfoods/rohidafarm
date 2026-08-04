@extends('mail.components.layout', ['title' => 'HTML Template Test'])

@section('content')
<h1 class="email-title">HTML Template Success!</h1>
<p class="email-text">Hello Admin,</p>
<p class="email-text">If you are reading this email and it looks beautifully formatted with colors, buttons, and proper spacing, then your dynamic SMTP credentials and the master HTML email layout are both working perfectly!</p>

<div class="bg-light p-3 mb-3 text-center" style="border: 1px dashed #1a4f3b;">
    <h3 style="font-size: 18px; margin-bottom: 5px; color: #1a4f3b;">System Check: All Green</h3>
    <p class="email-text" style="margin-bottom: 0; color: #555;">All future transactional emails (Invoices, Password Resets) will use this exact layout.</p>
</div>

<p class="email-text">Here is a sample call-to-action button to show how links will render in customer emails:</p>

<div class="text-center mt-4">
    <a href="https://rohidafarm.com" class="btn">Return to Dashboard</a>
</div>
@endsection
