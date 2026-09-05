@extends('layouts.app')

@section('content')
{{-- Page Hero Banner --}}
<section class="py-5 text-center position-relative overflow-hidden"
    style="background: linear-gradient(135deg, #f0f9f4 0%, #ffffff 60%, #f7fdf9 100%); border-bottom: 1px solid #e8f5e9;">
    <div class="container py-4" data-aos="fade-up">
        <h1 class="display-5 font-heading fw-bold text-dark mt-2 mb-0">{{ $page->title }}</h1>
    </div>
</section>

{{-- Policy Page Content --}}
<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">

                @if($page->content)
                    {{-- Dynamic content from DB via CKEditor --}}
                    <div class="policy-content" data-aos="fade-up" style="
                        font-size: 0.95rem;
                        line-height: 1.85;
                        color: #3a3a3a;
                    ">
                        {!! $page->content !!}
                    </div>
                @else
                    {{-- Placeholder when content is not written yet --}}
                    <div class="text-center py-5 text-muted" data-aos="fade-up">
                        <i class="bi bi-file-earmark-text display-3 d-block mb-4 opacity-25"></i>
                        <h4 class="fw-semibold mb-2">Content Coming Soon</h4>
                        <p class="mb-4">This page is being prepared. Please check back shortly.</p>
                        <a href="{{ route('contact') }}" class="btn btn-outline-success rounded-pill px-4">
                            <i class="bi bi-envelope me-2"></i>Contact Us for Help
                        </a>
                    </div>
                @endif

                {{-- Bottom separator and quick links --}}
                <hr class="my-5" style="border-color: #e8f5e9;">
                <div class="d-flex flex-wrap gap-3 justify-content-center" style="font-size:0.85rem;">
                    @php
                        $policyLinks = [
                            'privacy-policy'   => 'Privacy Policy',
                            'terms-conditions' => 'Terms & Conditions',
                            'refund-policy'    => 'Refund & Return Policy',
                            'shipping-policy'  => 'Shipping Policy',
                        ];
                        // Load active pages for the quick links
                        $activePageSlugs = \App\Models\Page::whereIn('slug', array_keys($policyLinks))
                                            ->where('is_active', true)
                                            ->pluck('slug')
                                            ->toArray();
                    @endphp
                    @foreach($policyLinks as $slug => $label)
                        @if(in_array($slug, $activePageSlugs))
                            <a href="{{ url('/' . $slug) }}"
                               class="text-decoration-none {{ $page->slug === $slug ? 'text-success fw-bold' : 'text-muted' }}">
                                @if($page->slug === $slug)
                                    <i class="bi bi-check-circle-fill me-1 text-success"></i>
                                @endif
                                {{ $label }}
                            </a>
                        @endif
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    /* ── Policy Page Rich Content Styling ── */
    .policy-content h2 {
        font-size: 1.35rem;
        font-weight: 700;
        color: #1a4d2e;
        margin-top: 2rem;
        margin-bottom: 0.75rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e8f5e9;
    }
    .policy-content h3 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #248443;
        margin-top: 1.5rem;
        margin-bottom: 0.5rem;
    }
    .policy-content p {
        margin-bottom: 1rem;
        color: #444;
    }
    .policy-content ul, .policy-content ol {
        margin-left: 1.5rem;
        margin-bottom: 1rem;
    }
    .policy-content ul li, .policy-content ol li {
        margin-bottom: 0.4rem;
        color: #444;
    }
    .policy-content blockquote {
        border-left: 4px solid #a8d5a2;
        padding: 0.75rem 1.25rem;
        background: #f0f9f4;
        border-radius: 0 8px 8px 0;
        margin: 1.5rem 0;
        color: #2e7d32;
        font-style: italic;
    }
    .policy-content a {
        color: #248443;
        text-decoration: underline;
    }
    .policy-content strong { color: #1a4d2e; }
    .policy-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 1.5rem 0;
    }
    .policy-content table th,
    .policy-content table td {
        border: 1px solid #e0e0e0;
        padding: 0.6rem 0.9rem;
        font-size: 0.88rem;
    }
    .policy-content table th {
        background: #f0f9f4;
        color: #1a4d2e;
        font-weight: 700;
    }
</style>
@endpush
