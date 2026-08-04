@extends('layouts.app')

@section('content')
{{-- Page Hero --}}
<section class="py-5 text-center position-relative overflow-hidden"
    style="background: linear-gradient(135deg, #f0f9f4 0%, #ffffff 60%, #f7fdf9 100%); border-bottom: 1px solid #e8f5e9;">
    <div class="container py-4" data-aos="fade-up">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb justify-content-center" style="font-size:0.82rem;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-success text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active text-muted">FAQs</li>
            </ol>
        </nav>
        <span class="text-uppercase fw-bold text-success" style="font-size:0.75rem;letter-spacing:2.5px;">
            <i class="bi bi-question-circle me-1"></i>Help Center
        </span>
        <h1 class="display-5 font-heading fw-bold text-dark mt-2 mb-3">Frequently Asked Questions</h1>
        <p class="text-muted mx-auto" style="max-width:560px;font-size:1rem;">
            Everything you need to know about our products, ordering, and delivery. Can't find what you're looking for?
            <a href="{{ route('contact') }}" class="text-success fw-semibold text-decoration-none">Contact us</a>.
        </p>
    </div>
</section>

{{-- FAQ Accordion --}}
<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                @if($faqs->isEmpty())
                    <div class="text-center py-5 text-muted" data-aos="fade-up">
                        <i class="bi bi-question-circle display-3 d-block mb-4 opacity-25"></i>
                        <h4 class="fw-semibold mb-2">No FAQs Yet</h4>
                        <p class="mb-4">We're working on adding helpful answers. Check back soon!</p>
                        <a href="{{ route('contact') }}" class="btn btn-outline-success rounded-pill px-4">
                            <i class="bi bi-envelope me-2"></i>Ask Us Directly
                        </a>
                    </div>
                @else
                    @php
                        $grouped = $faqs->groupBy('category');
                        $categoryIcons = [
                            'General'  => 'bi-info-circle',
                            'Products' => 'bi-box-seam',
                            'Shipping' => 'bi-truck',
                            'Payments' => 'bi-credit-card',
                        ];
                    @endphp

                    @foreach($grouped as $category => $categoryFaqs)
                        <div class="mb-5" data-aos="fade-up">
                            {{-- Category heading --}}
                            <h5 class="fw-bold text-dark d-flex align-items-center gap-2 mb-3 pb-2" style="border-bottom:2px solid #e8f5e9;">
                                <i class="bi {{ $categoryIcons[$category] ?? 'bi-chat-dots' }} text-success fs-5"></i>
                                {{ $category }}
                            </h5>

                            {{-- Accordion --}}
                            <div class="accordion accordion-flush border rounded-4 overflow-hidden" id="faq-{{ Str::slug($category) }}">
                                @foreach($categoryFaqs as $index => $faq)
                                    <div class="accordion-item border-0 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color:#e8f5e9 !important;">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button {{ $loop->first && $loop->parent->first ? '' : 'collapsed' }} fw-semibold py-4"
                                                    type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#faq-item-{{ $faq->id }}"
                                                    aria-expanded="{{ $loop->first && $loop->parent->first ? 'true' : 'false' }}"
                                                    aria-controls="faq-item-{{ $faq->id }}"
                                                    style="font-size:0.95rem;color:#1a4d2e;background:transparent;">
                                                {{ $faq->question }}
                                            </button>
                                        </h2>
                                        <div id="faq-item-{{ $faq->id }}"
                                             class="accordion-collapse collapse {{ $loop->first && $loop->parent->first ? 'show' : '' }}"
                                             data-bs-parent="#faq-{{ Str::slug($category) }}">
                                            <div class="accordion-body pt-0 pb-4" style="font-size:0.92rem;color:#444;line-height:1.75;">
                                                {!! nl2br(e($faq->answer)) !!}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif

                {{-- Still have questions? --}}
                <div class="rounded-4 p-4 text-center mt-4" style="background:#f0f9f4;border:1px solid #c8e6c9;" data-aos="fade-up">
                    <i class="bi bi-headset text-success fs-2 mb-3 d-block"></i>
                    <h6 class="fw-bold text-dark mb-1">Still have a question?</h6>
                    <p class="text-muted mb-3" style="font-size:0.88rem;">Our team is here to help you with anything.</p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="{{ route('contact') }}" class="btn btn-success rounded-pill px-4 py-2">
                            <i class="bi bi-envelope me-2"></i>Send a Message
                        </a>
                        @if(\App\Models\Setting::get('contact_mobile_1'))
                            <a href="tel:{{ \App\Models\Setting::get('contact_mobile_1') }}" class="btn btn-outline-success rounded-pill px-4 py-2">
                                <i class="bi bi-telephone me-2"></i>Call Us
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .accordion-button:not(.collapsed) {
        color: #248443 !important;
        background: #f0f9f4 !important;
        box-shadow: none !important;
    }
    .accordion-button:focus {
        box-shadow: none !important;
    }
    .accordion-button::after {
        filter: invert(40%) sepia(80%) saturate(400%) hue-rotate(100deg);
    }
</style>
@endpush
