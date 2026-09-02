<div class="tab-pane fade" id="faqs" role="tabpanel" aria-labelledby="faqs-tab">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="font-heading fw-bold text-dark m-0"><i class="bi bi-question-circle text-success me-2"></i>Global Frequently Asked Questions</h5>
            <p class="text-muted m-0 mt-1" style="font-size:0.78rem;">
                These FAQs are displayed by default on all product pages unless a product specifies custom FAQs. Drag items to reorder.
            </p>
        </div>
        <button type="button" class="btn btn-success btn-sm rounded-pill px-3" id="btnAddGlobalFaq">
            <i class="bi bi-plus-lg me-1"></i> Add Global FAQ
        </button>
    </div>

    <!-- Hidden indicator so backend knows to sync FAQs on save -->
    <input type="hidden" name="global_faqs_submitted" value="1">

    <div id="globalFaqsContainer" class="d-flex flex-column gap-3 mt-3">
        @forelse($globalFaqs as $index => $faq)
            <div class="global-faq-item-card card border rounded-3 p-3 bg-light shadow-sm">
                <div class="d-flex align-items-start gap-3">
                    <div class="global-faq-drag-handle text-muted pt-2" style="cursor: grab;" title="Drag to reorder">
                        <i class="bi bi-grip-vertical fs-5"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="mb-2">
                            <label class="form-label text-dark fw-semibold mb-1" style="font-size: 0.8rem;">Question</label>
                            <input type="text" name="global_faqs[{{ $index }}][question]" class="form-control form-control-sm border bg-white global-faq-q" required value="{{ $faq->question }}" placeholder="e.g. How is Rohida Farm A2 Ghee made?">
                        </div>
                        <div>
                            <label class="form-label text-dark fw-semibold mb-1" style="font-size: 0.8rem;">Answer</label>
                            <textarea name="global_faqs[{{ $index }}][answer]" class="form-control form-control-sm border bg-white global-faq-a" rows="2" required placeholder="Detailed answer...">{{ $faq->answer }}</textarea>
                        </div>
                        <input type="hidden" class="global-faq-sort" name="global_faqs[{{ $index }}][sort_order]" value="{{ $index }}">
                    </div>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-circle p-0 d-flex align-items-center justify-content-center btn-remove-global-faq" style="width: 28px; height: 28px;" title="Delete FAQ">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-muted border rounded-3 bg-white" id="globalFaqsEmpty" style="font-size:0.85rem;">
                <i class="bi bi-question-circle text-success fs-3 d-block mb-2"></i>
                No global FAQs added yet. Click <strong>"Add Global FAQ"</strong> above to create common FAQs for your products.
            </div>
        @endforelse
    </div>
</div>
