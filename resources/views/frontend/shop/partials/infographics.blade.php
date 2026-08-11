{{-- ── Product Story / Infographic Banners Section ── --}}
@php
    $infographics = $product->infographic_images ?? [];
    if (is_string($infographics)) {
        $infographics = json_decode($infographics, true) ?? [];
    }
@endphp

@if(is_array($infographics) && count($infographics) > 0)
    <div class="product-story-infographics-wrapper my-3 my-md-4">
        <div class="container-fluid px-2 px-md-3" style="max-width: 100%;">
            <div class="row justify-content-center g-0">
                <div class="col-12 col-xl-11">
                    <div class="d-flex flex-column gap-1 align-items-center">
                        @foreach($infographics as $img)
                            @php $imgPath = is_array($img) ? ($img['image_path'] ?? $img['url'] ?? reset($img)) : $img; @endphp
                            @if(is_string($imgPath) && !empty($imgPath))
                                <div class="product-story-banner-card w-100 shadow-sm rounded-3 overflow-hidden"
                                     style="border: 1px solid rgba(92, 61, 46, 0.08); background-color: #ffffff;">
                                    <img src="{{ asset($imgPath) }}"
                                         alt="{{ $product->name }} Story & Benefits"
                                         class="w-100 h-auto d-block"
                                         loading="lazy"
                                         decoding="async"
                                         style="object-fit: contain; max-width: 100%;">
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
