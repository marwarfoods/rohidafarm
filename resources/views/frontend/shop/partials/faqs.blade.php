@php
    $displayFaqs = ($product->use_global_faqs ?? true) 
        ? \App\Models\Faq::where('is_active', true)->orderBy('sort_order')->get()
        : $product->faqs;
@endphp

@if($displayFaqs->isNotEmpty())
<!-- ── Product FAQs ── -->
<div id="product-faqs" class="bg-white p-4 p-md-5 rounded-4 shadow-sm border mb-5" style="border-color:#f6f3eb !important;">
    <h3 class="font-heading fw-bold mb-4 text-dark text-center display-6">Frequently Asked Questions</h3>

    <div class="accordion mx-auto" id="productFaqAccordion" style="max-width: 780px;">
        @foreach($displayFaqs as $index => $faq)
            <div class="accordion-item border-0 border-bottom" style="border-color:#f6f3eb !important;">
                <h2 class="accordion-header" id="faqHeading{{ $faq->id ?? $index }}">
                    <button class="accordion-button collapsed fw-bold text-dark bg-transparent shadow-none px-2 py-3" type="button"
                            data-bs-toggle="collapse" data-bs-target="#faqCollapse{{ $faq->id ?? $index }}"
                            aria-expanded="false" aria-controls="faqCollapse{{ $faq->id ?? $index }}" style="font-size: 0.95rem;">
                        {{ $faq->question }}
                    </button>
                </h2>
                <div id="faqCollapse{{ $faq->id ?? $index }}" class="accordion-collapse collapse" aria-labelledby="faqHeading{{ $faq->id ?? $index }}" data-bs-parent="#productFaqAccordion">
                    <div class="accordion-body text-muted px-2 pb-3 pt-0" style="font-size: 0.88rem; line-height: 1.75;">
                        {{ $faq->answer }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
