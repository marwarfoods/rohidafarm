<!-- ── Product Details Accordion Section ── -->
<div class="bg-white p-3 p-md-4 rounded-4 shadow-sm border mb-4 mb-md-5" style="border-color:#f6f3eb !important;">
    <div class="accordion accordion-flush" id="productDetailAccordion">

        {{-- 1. Description --}}
        @if(!empty($product->description))
            <div class="accordion-item border-bottom">
                <h2 class="accordion-header" id="headingDescription">
                    <button class="accordion-button collapsed font-heading fw-semibold fs-6 fs-md-5 text-dark bg-transparent shadow-none"
                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseDescription"
                            aria-expanded="false" aria-controls="collapseDescription"
                            style="color: var(--dark-brown) !important; padding: 16px 0;">
                        <i class="bi bi-file-text text-success me-2 fs-4"></i> Product Description
                    </button>
                </h2>
                <div id="collapseDescription" class="accordion-collapse collapse"
                     aria-labelledby="headingDescription" data-bs-parent="#productDetailAccordion">
                    <div class="accordion-body px-0 text-muted" style="font-size: 0.92rem; line-height: 1.8;">
                        {!! $product->description !!}
                    </div>
                </div>
            </div>
        @endif

        {{-- 2. Benefits --}}
        @if(!empty($product->benefits))
            <div class="accordion-item border-bottom">
                <h2 class="accordion-header" id="headingBenefits">
                    <button class="accordion-button collapsed font-heading fw-semibold fs-6 fs-md-5 text-dark bg-transparent shadow-none"
                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseBenefits"
                            aria-expanded="false" aria-controls="collapseBenefits"
                            style="color: var(--dark-brown) !important; padding: 16px 0;">
                        <i class="bi bi-heart-pulse text-success me-2 fs-4"></i> Key Benefits
                    </button>
                </h2>
                <div id="collapseBenefits" class="accordion-collapse collapse"
                     aria-labelledby="headingBenefits" data-bs-parent="#productDetailAccordion">
                    <div class="accordion-body px-0 text-muted" style="font-size: 0.92rem; line-height: 1.8;">
                        {!! $product->benefits !!}
                    </div>
                </div>
            </div>
        @endif

        {{-- 3. Ingredients & Nutrition --}}
        @if(!empty($product->ingredients) || !empty($product->nutrition_facts))
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingIngredients">
                    <button class="accordion-button collapsed font-heading fw-semibold fs-6 fs-md-5 text-dark bg-transparent shadow-none"
                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseIngredients"
                            aria-expanded="false" aria-controls="collapseIngredients"
                            style="color: var(--dark-brown) !important; padding: 16px 0;">
                        <i class="bi bi-leaf text-success me-2 fs-4"></i> Ingredients &amp; Nutritional Info
                    </button>
                </h2>
                <div id="collapseIngredients" class="accordion-collapse collapse"
                     aria-labelledby="headingIngredients" data-bs-parent="#productDetailAccordion">
                    <div class="accordion-body px-0 text-muted" style="font-size: 0.92rem; line-height: 1.8;">
                        {!! $product->ingredients ?: '100% Organic Pure Ingredients.' !!}
                        @if($product->nutrition_facts)
                            <div class="mt-3">
                                {!! $product->nutrition_facts !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
