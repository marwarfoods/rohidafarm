@extends('layouts.app')

@section('content')
<!-- Header Section -->
<section class="py-5 text-center" style="background-color: var(--cream-bg, #FFF9F1); border-bottom: 1px solid var(--border-color);">
    <div class="container py-4" data-aos="fade-up">
        <span class="text-uppercase fw-bold text-success" style="font-size: 0.78rem; letter-spacing: 2px;">Support & Inquiry</span>
        <h1 class="display-4 font-heading fw-bold text-dark mt-2 mb-3">Contact Us</h1>
        <p class="text-muted mx-auto" style="max-width: 600px; font-size: 1.1rem;">
            Have questions about our traditional Bilona Cow Ghee or your delivery? Reach out to our wellness team.
        </p>
    </div>
</section>

<!-- Form & Info split columns -->
<section class="py-5 bg-white">
    <div class="container py-4">
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-5 p-3" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-3 fs-4 text-success"></i>
                    <div>
                        <strong class="d-block text-success">Message Sent Successfully!</strong>
                        <span class="text-muted" style="font-size: 0.9rem;">{{ session('success') }}</span>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-5">
            <!-- Left Column: Details -->
            <div class="col-lg-5" data-aos="fade-right">
                <span class="text-success fw-bold text-uppercase d-block mb-2" style="font-size: 0.8rem; letter-spacing: 1px;">Quick Connect</span>
                <h2 class="font-heading display-6 fw-bold text-dark mb-4">Get In Touch</h2>
                
                <p class="text-muted mb-5" style="line-height: 1.6;">
                    Whether you are an individual wanting to check bulk ordering pricing, or a chef looking to source premium Vedic ghee, we are happy to assist you.
                </p>

                <!-- Contact Rows -->
                <div class="d-flex align-items-start mb-4">
                    <div class="bg-success text-white p-3 rounded-circle fs-4 d-flex align-items-center justify-content-center me-3" style="width: 54px; height: 54px; flex-shrink: 0;">
                        <i class="bi bi-geo-alt"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold font-heading text-dark m-0 fs-6">Our Farm Address</h5>
                        <p class="text-muted m-0 mt-1" style="font-size: 0.92rem;">RohidaFarm Pastures, Ward No. 3, Sheoganj, Sirohi, Rajasthan, India - 307027</p>
                    </div>
                </div>

                <div class="d-flex align-items-start mb-4">
                    <div class="bg-success text-white p-3 rounded-circle fs-4 d-flex align-items-center justify-content-center me-3" style="width: 54px; height: 54px; flex-shrink: 0;">
                        <i class="bi bi-telephone"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold font-heading text-dark m-0 fs-6">Phone Hotline</h5>
                        <p class="text-muted m-0 mt-1" style="font-size: 0.92rem;">+91 98765 43210 (Mon-Sat, 9:00 AM - 6:00 PM)</p>
                    </div>
                </div>

                <div class="d-flex align-items-start">
                    <div class="bg-success text-white p-3 rounded-circle fs-4 d-flex align-items-center justify-content-center me-3" style="width: 54px; height: 54px; flex-shrink: 0;">
                        <i class="bi bi-envelope"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold font-heading text-dark m-0 fs-6">Email Support</h5>
                        <p class="text-muted m-0 mt-1" style="font-size: 0.92rem;">support@rohidafarm.com</p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Form -->
            <div class="col-lg-7" data-aos="fade-left">
                <div class="card border-0 rounded-4 p-md-5 p-4 shadow-sm border" style="border-color: var(--border-color) !important;">
                    <h4 class="font-heading fw-bold text-dark border-bottom pb-3 mb-4"><i class="bi bi-envelope-paper me-2 text-success"></i>Send a Message</h4>
                    
                    <form action="{{ route('contact.submit') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold text-dark">Your Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control rounded-3 p-2.5 @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Rajendra Kumar">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold text-dark">Your Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control rounded-3 p-2.5 @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="e.g. rajendra@example.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="phone" class="form-label fw-semibold text-dark">10-Digit Mobile Number <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="phone" class="form-control rounded-3 p-2.5 @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required placeholder="e.g. 9876543210" pattern="[0-9]{10}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="subject" class="form-label fw-semibold text-dark">Subject <span class="text-danger">*</span></label>
                                <input type="text" name="subject" id="subject" class="form-control rounded-3 p-2.5 @error('subject') is-invalid @enderror" value="{{ old('subject') }}" required placeholder="e.g. Inquiry about Bilona Ghee Bulk Orders">
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="message" class="form-label fw-semibold text-dark">Your Message <span class="text-danger">*</span></label>
                                <textarea name="message" id="message" rows="5" class="form-control rounded-3 p-2.5 @error('message') is-invalid @enderror" required placeholder="Tell us how we can help you (minimum 10 characters)...">{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mt-4 text-end">
                                <button type="submit" class="btn btn-premium px-5 py-2.5 rounded-pill text-uppercase font-heading" style="font-size: 0.82rem;"><i class="bi bi-send me-2"></i>Send Message</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Google Map section -->
<section class="p-0 border-top" data-aos="fade-up">
    <div class="w-100" style="height: 400px; filter: grayscale(1) contrast(1.1) invert(0);">
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14392.204558509378!2d72.98634839999999!3d25.1001476!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39420b991a0c8b67%3A0xe54e6ec56e29780e!2sSheoganj%2C%20Rajasthan%20307027!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin" 
            width="100%" 
            height="100%" 
            style="border:0;" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>
</section>
@endsection
