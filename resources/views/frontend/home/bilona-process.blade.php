@if($bilonaSteps->isNotEmpty())
<!-- ── The Traditional Journey Of Our Ghee (2-Column: Left BG visible, Right Steps) ── -->
<section class="py-3 py-md-4 bilona-kasutam-section position-relative overflow-hidden" id="bilona-process"
    style="background: url('{{ asset('images/vectors/full-tree.png') }}') left bottom / cover no-repeat; min-height: 480px;">

    {{-- Dynamic gradient overlay: crystal clear on left on PC, smooth soft cream on right for readable cards --}}
    <div class="position-absolute top-0 start-0 w-100 h-100 bilona-split-overlay"
         style="z-index: 0; pointer-events: none;"></div>

    <div class="container-fluid px-0 py-md-2 position-relative" style="z-index: 1;">
        <div class="row g-0 align-items-stretch" style="min-height: 460px;">

            {{-- LEFT COLUMN — crystal clear view of the background cow & farm illustration (40%) --}}
            <div class="col-lg-5 d-none d-lg-block"></div>

            {{-- RIGHT COLUMN — heading + all 5 steps (60%) --}}
            <div class="col-12 col-lg-7">
                <div class="py-3 py-md-4 px-3 px-lg-5">

                    <!-- ── Section Header ── -->
                    <div class="mb-2" data-aos="fade-up">
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill font-heading fw-bold text-uppercase mb-3"
                             style="font-size: 0.78rem; letter-spacing: 2px; color: #5C3D2E; background: rgba(196, 154, 69, 0.18); border: 1px solid rgba(196, 154, 69, 0.4);">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2L14.5 9H22L16 13.5L18.5 20.5L12 16L5.5 20.5L8 13.5L2 9H9.5L12 2Z" fill="#C49A45"/>
                            </svg>
                            <span>Vedic Craftsmanship</span>
                        </div>
                        <h2 class="display-6 font-heading fw-bold mb-2" style="color: #5C3D2E;">The Traditional Journey Of Our Ghee</h2>
                        <p class="fs-6 mb-0" style="color: #6E5B4F; max-width: 580px; line-height: 1.7;">
                            Crafted strictly according to Shastras using 100% A2 Tharparkar Cow milk, bi-directional wooden churners, and earthenware pots.
                        </p>
                    </div>

                    <!-- ── Steps Timeline ── -->
                    <div class="kasutam-timeline-wrapper ps-1 ps-sm-2 mt-2">

                        <div class="kasutam-dashed-line"></div>

                        @foreach($bilonaSteps as $step)
                            <div class="kasutam-step-item d-flex align-items-center gap-3 gap-md-4" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                                <div class="kasutam-num-badge">{{ $loop->iteration }}</div>
                                <div class="kasutam-card">
                                    <div class="kasutam-scalloped-frame">
                                        <img src="{{ Illuminate\Support\Str::startsWith($step->image_path, 'http') ? $step->image_path : asset($step->image_path) }}"
                                             alt="{{ $step->title }}" class="kasutam-step-img">
                                    </div>
                                    <div>
                                        <h3 class="kasutam-card-title">{{ $step->title }}</h3>
                                        <p class="kasutam-card-desc">{{ $step->description }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<style>
    .bilona-split-overlay {
        background: rgba(250, 247, 238, 0.92);
    }
    @media (min-width: 992px) {
        .bilona-split-overlay {
            background: linear-gradient(
                to right,
                rgba(250, 247, 238, 0) 0%,
                rgba(250, 247, 238, 0) 38%,
                rgba(250, 247, 238, 0.6) 48%,
                rgba(250, 247, 238, 0.92) 58%,
                rgba(250, 247, 238, 0.96) 100%
            ) !important;
        }
    }
</style>
@endif
