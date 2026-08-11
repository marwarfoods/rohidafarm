<!-- Latest Blogs Section -->
<section class="py-5 position-relative overflow-hidden"
    style="background: url('{{ asset('images/vectors/bg9.png') }}') center center / cover no-repeat; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
    <!-- Section Skeleton Overlay -->
    <div class="section-skeleton-overlay position-absolute top-0 start-0 w-100 h-100 bg-white" style="z-index: 10; pointer-events: none; padding: 80px 48px;">
        <div class="container">
            <div class="row g-4 flex-nowrap overflow-hidden">
                @for($i = 0; $i < 4; $i++)
                    <div class="col-lg-3 col-md-4 col-6" style="flex-shrink: 0;">
                        <div class="card h-100 border-0 bg-white p-3 rounded-4 border shadow-sm" style="border-color: var(--border-color) !important;">
                            <div class="skeleton-block mb-3 w-100" style="aspect-ratio: 16/10;"></div>
                            <div class="skeleton-block mb-2" style="height: 12px; width: 35%; border-radius: 20px;"></div>
                            <div class="skeleton-block mb-2 w-100" style="height: 16px;"></div>
                            <div class="skeleton-block mb-3 w-70" style="height: 16px;"></div>
                            <div class="skeleton-block mb-1 w-100" style="height: 10px;"></div>
                            <div class="skeleton-block mb-1 w-100" style="height: 10px;"></div>
                            <div class="skeleton-block w-80" style="height: 10px;"></div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <div class="position-absolute top-0 start-0 w-100 h-100"
         style="background: rgba(255, 255, 255, 0.55); z-index: 0; pointer-events: none;"></div>

    <div class="container position-relative" style="z-index: 1;">
        <div class="row align-items-end mb-5">
            <div class="col-md-8 text-center text-md-start" data-aos="fade-right">
                <span class="text-uppercase fw-bold text-success" style="font-size: 0.75rem; letter-spacing: 2px;">Healthy Living</span>
                <h2 class="display-5 font-heading fw-bold mt-1 mb-0">From Our Journal</h2>
            </div>
            <div class="col-md-4 text-center text-md-end mt-3 mt-md-0" data-aos="fade-left">
                <a href="{{ route('blogs.index') }}" class="btn btn-link text-success fw-bold text-decoration-none p-0 fs-6" aria-label="Read all healthy living blogs">Read All Blogs <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i></a>
            </div>
        </div>

        <div class="swiper blogs-slider" style="overflow: hidden;" data-aos="fade-up">
            <div class="swiper-wrapper">
                @foreach($blogs as $blog)
                    <div class="swiper-slide h-auto py-2">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white" style="border: 1px solid var(--border-color) !important;">
                            <div class="bg-light" style="aspect-ratio: 16/10; overflow:hidden;">
                                <a href="{{ route('blogs.show', $blog->slug) }}" class="d-block w-100 h-100">
                                    @if($blog->featured_image)
                                        <img src="{{ asset($blog->featured_image) }}" class="w-100 h-100 object-fit-cover" width="400" height="250" style="transition: transform 0.5s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'" alt="{{ $blog->title }}" loading="lazy" decoding="async">
                                    @else
                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-success text-white font-heading fs-5" style="background-color: var(--primary-green) !important;">
                                            RohidaFarm Journal
                                        </div>
                                    @endif
                                </a>
                            </div>
                            <div class="card-body p-4 d-flex flex-column">
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill mb-2 py-1 px-2 align-self-start" style="font-size: 0.65rem;">{{ $blog->category->name }}</span>
                                <h5 class="card-title font-heading fw-bold mb-2 fs-6 clamp-2">
                                    <a href="{{ route('blogs.show', $blog->slug) }}" class="text-dark text-decoration-none hover-gold">{{ $blog->title }}</a>
                                </h5>
                                <p class="card-text text-muted mb-0 clamp-4" style="font-size: 0.82rem; line-height: 1.6;">{{ $blog->excerpt }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
