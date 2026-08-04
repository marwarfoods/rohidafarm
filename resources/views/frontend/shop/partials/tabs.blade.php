<!-- ── Specification Tabs ── -->
<div class="bg-white p-4 rounded-4 shadow-sm border mb-5" style="border-color:#f6f3eb !important;">
    <ul class="nav nav-tabs border-bottom mb-4 flex-nowrap overflow-x-auto" id="detailTabs" role="tablist" style="scrollbar-width: none; -webkit-overflow-scrolling: touch;">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold font-heading fs-5 border-0 bg-transparent" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">Description</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold font-heading fs-5 border-0 bg-transparent" id="benefits-tab" data-bs-toggle="tab" data-bs-target="#benefits" type="button" role="tab">Benefits</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold font-heading fs-5 border-0 bg-transparent" id="ingredients-tab" data-bs-toggle="tab" data-bs-target="#ingredients" type="button" role="tab">Ingredients &amp; Nutrition</button>
        </li>
    </ul>
    <div class="tab-content" id="detailTabsContent" style="font-size:0.9rem;line-height:1.8;">
        <div class="tab-pane fade show active text-muted" id="description" role="tabpanel">{!! $product->description !!}</div>
        <div class="tab-pane fade text-muted" id="benefits" role="tabpanel">{!! $product->benefits ?: 'No specific benefits configured.' !!}</div>
        <div class="tab-pane fade text-muted" id="ingredients" role="tabpanel">
            {!! $product->ingredients ?: '100% Organic Pure Ingredients.' !!}
            @if($product->nutrition_facts)
                <div class="mt-4">
                    {!! $product->nutrition_facts !!}
                </div>
            @endif
        </div>
    </div>
</div>
