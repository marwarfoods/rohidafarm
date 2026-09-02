@extends('layouts.app')

@section('content')
<!-- Blog Detail Section -->
<section class="py-5 bg-white position-relative overflow-hidden" style="min-height: 700px;">
    <!-- Section Skeleton Overlay -->
    <div class="section-skeleton-overlay position-absolute top-0 start-0 w-100 h-100 bg-white" style="z-index: 10; pointer-events: none; padding: 50px 0;">
        <div class="container" style="max-width: 1200px;">
            <div class="row g-5">
                <!-- Left Main Content Skeleton -->
                <div class="col-lg-8">
                    <!-- Title skeleton -->
                    <div class="skeleton-block mb-2" style="height: 38px; width: 90%;"></div>
                    <div class="skeleton-block mb-4" style="height: 38px; width: 70%;"></div>
                    <!-- Metadata line skeleton -->
                    <div class="skeleton-block mb-5" style="height: 14px; width: 45%;"></div>
                    <!-- Featured Image skeleton -->
                    <div class="skeleton-block mb-5 w-100" style="aspect-ratio: 21/10; border-radius: 16px;"></div>
                    <!-- Paragraphs skeletons -->
                    @for($p = 0; $p < 3; $p++)
                        <div class="skeleton-block mb-2 w-100" style="height: 11px;"></div>
                        <div class="skeleton-block mb-2 w-100" style="height: 11px;"></div>
                        <div class="skeleton-block mb-2 w-100" style="height: 11px;"></div>
                        <div class="skeleton-block mb-4 w-60" style="height: 11px;"></div>
                    @endfor
                </div>

                <!-- Right Sidebar Skeleton -->
                <div class="col-lg-4 d-none d-lg-block">
                    <div class="skeleton-block mb-4" style="height: 22px; width: 60%;"></div>
                    @for($s = 0; $s < 3; $s++)
                        <div class="d-flex gap-3 mb-4 align-items-center">
                            <div class="skeleton-block" style="width: 75px; height: 55px; border-radius: 8px; flex-shrink: 0;"></div>
                            <div class="flex-grow-1">
                                <div class="skeleton-block mb-2 w-100" style="height: 12px;"></div>
                                <div class="skeleton-block w-40" style="height: 10px;"></div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- Actual Page Content -->
    <div class="container" style="max-width: 1200px;">
        <div class="row g-5">
            
            <!-- Left Main Column: Blog Content -->
            <div class="col-lg-8" data-aos="fade-right">
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill mb-3 py-1.5 px-3" style="font-size: 0.72rem; letter-spacing: 0.5px;">{{ $blog->category->name }}</span>
                <h1 class="font-heading display-5 fw-bold text-dark mb-3" style="line-height: 1.25;">{{ $blog->title }}</h1>
                
                <div class="d-flex align-items-center text-muted mb-4 pb-3 border-bottom" style="font-size: 0.88rem; gap: 20px;">
                    @if($blog->author_name)
                        <span><i class="bi bi-person-circle text-success me-1"></i>By {{ $blog->author_name }}</span>
                    @endif
                    <span><i class="bi bi-calendar3 text-success me-1"></i>{{ $blog->published_at ? $blog->published_at->format('d M Y') : '' }}</span>
                    <span><i class="bi bi-eye text-success me-1"></i>{{ $blog->view_count }} reads</span>
                </div>

                @if($blog->featured_image)
                    <div class="mb-5 overflow-hidden rounded-4 border" style="max-height: 480px; border-color: var(--border-color) !important;">
                        <img src="{{ asset($blog->featured_image) }}" class="w-100 h-100 object-fit-cover" alt="{{ $blog->title }}">
                    </div>
                @endif

                <!-- Article Content Body -->
                <div class="blog-rich-content text-muted" style="line-height: 1.8; font-size: 1.05rem;">
                    {!! $blog->content !!}
                </div>
            </div>

            <!-- Right Column: Sticky Sidebar -->
            <div class="col-lg-4" data-aos="fade-left">
                <div class="position-sticky" style="top: 100px;">
                    <!-- Related / Recent Articles widget -->
                    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white border mb-4" style="border-color: var(--border-color) !important;">
                        <h4 class="font-heading fw-bold text-dark border-bottom pb-2 mb-4 fs-5"><i class="bi bi-journal-text me-2 text-success"></i>Recent Articles</h4>
                        
                        <div class="d-flex flex-column gap-4">
                            @forelse($relatedBlogs as $rel)
                                <div class="d-flex gap-3 align-items-center">
                                    <div class="bg-light rounded overflow-hidden" style="width: 75px; height: 55px; flex-shrink: 0; border: 1px solid var(--border-color);">
                                        <a href="{{ route('blogs.show', $rel->slug) }}">
                                            @if($rel->featured_image)
                                                <img src="{{ asset($rel->featured_image) }}" class="w-100 h-100 object-fit-cover" alt="{{ $rel->title }}">
                                            @else
                                                <div class="w-100 h-100 bg-success text-white d-flex align-items-center justify-content-center" style="font-size: 0.6rem; font-weight: bold; background-color: var(--primary-green) !important;">Journal</div>
                                            @endif
                                        </a>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold font-heading m-0 fs-6 clamp-2" style="line-height: 1.4;">
                                            <a href="{{ route('blogs.show', $rel->slug) }}" class="text-dark text-decoration-none hover-gold">{{ $rel->title }}</a>
                                        </h6>
                                        <span class="text-muted" style="font-size: 0.72rem;">{{ $rel->published_at ? $rel->published_at->format('d M Y') : '' }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-muted text-center py-3" style="font-size: 0.9rem;">No other articles available.</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Dynamic Promotional Box widget -->
                    <div class="card border-0 rounded-4 shadow-sm p-4 text-center border text-white" style="background-color: var(--dark-green) !important; border-color: var(--dark-green) !important;">
                        <h5 class="font-heading fw-bold mb-2 text-white">Taste Real Purity</h5>
                        <p class="text-white mb-3" style="font-size: 0.85rem;">Prepared using traditional double wooden bilona churning.</p>
                        <a href="/shop" class="btn btn-premium w-100 py-2 rounded-pill font-heading text-uppercase fs-6">Buy A2 Ghee Now</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
