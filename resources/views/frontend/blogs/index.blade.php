@extends('layouts.app')

@section('content')
<!-- Header Area -->
<section class="py-5 text-center" style="background-color: var(--cream-bg, #FFF9F1); border-bottom: 1px solid var(--border-color);">
    <div class="container py-4" data-aos="fade-up">
        <span class="text-uppercase fw-bold text-success" style="font-size: 0.78rem; letter-spacing: 2px;">Organic Living Journal</span>
        <h1 class="display-4 font-heading fw-bold text-dark mt-2 mb-3">Healthy Living Blog</h1>
        <p class="text-muted mx-auto" style="max-width: 600px; font-size: 1.1rem;">
            Explore Ayurvedic insights, natural cooking secrets, and chemical-free recipes from our experts.
        </p>
    </div>
</section>

<!-- Main Blogs Body -->
<section class="py-5 bg-white position-relative overflow-hidden" style="min-height: 500px;">
    <!-- Section Skeleton Overlay -->
    <div class="section-skeleton-overlay position-absolute top-0 start-0 w-100 h-100 bg-white" style="z-index: 10; pointer-events: none; padding: 40px 0;">
        <div class="container">
            <div class="row g-4">
                @for($i = 0; $i < 6; $i++)
                    <div class="col-lg-4 col-md-6 col-6">
                        <div class="card h-100 border-0 bg-white p-3 rounded-4 border shadow-sm" style="border-color: var(--border-color) !important;">
                            <div class="skeleton-block mb-3 w-100" style="aspect-ratio: 16/10;"></div>
                            <div class="skeleton-block mb-2 animate" style="height: 12px; width: 35%; border-radius: 20px;"></div>
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

    <div class="container">
        <!-- Search & Filter Row -->
        <div class="row g-4 align-items-center mb-5">
            <!-- Categories Filters -->
            <div class="col-lg-8 col-md-7 d-flex gap-2 flex-wrap order-2 order-md-1">
                <a href="{{ route('blogs.index') }}" class="btn btn-sm {{ !request('category') ? 'btn-premium' : 'btn-premium-outline' }} rounded-pill px-4">All Posts</a>
                @foreach($categories as $cat)
                    <a href="{{ route('blogs.index', ['category' => $cat->slug] + request()->except('category', 'page')) }}" class="btn btn-sm {{ request('category') == $cat->slug ? 'btn-premium' : 'btn-premium-outline' }} rounded-pill px-4">
                        {{ $cat->name }} <span class="badge bg-success-subtle text-success ms-1 rounded-circle" style="font-size: 0.7rem;">{{ $cat->blogs_count }}</span>
                    </a>
                @endforeach
            </div>

            <!-- Search Form -->
            <div class="col-lg-4 col-md-5 order-1 order-md-2">
                <form action="{{ route('blogs.index') }}" method="GET" class="position-relative">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <input type="text" name="search" class="form-control rounded-pill pe-5 ps-4 shadow-sm" style="border-color: var(--border-color); height: 42px;" value="{{ request('search') }}" placeholder="Search articles...">
                    <button type="submit" class="btn border-0 position-absolute end-0 top-0 h-100 text-success px-4" style="border-radius: 0 50px 50px 0;">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Blogs Grid -->
        <div class="row g-4">
            @forelse($blogs as $blog)
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white" style="border: 1px solid var(--border-color) !important;">
                        <div class="bg-light" style="aspect-ratio: 16/10; overflow:hidden;">
                            <a href="{{ route('blogs.show', $blog->slug) }}" class="d-block w-100 h-100">
                                @if($blog->featured_image)
                                    <img src="{{ asset($blog->featured_image) }}" class="w-100 h-100 object-fit-cover" style="transition: transform 0.5s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'" alt="{{ $blog->title }}">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-success text-white font-heading fs-5" style="background-color: var(--primary-green) !important;">
                                        RohidaFarm Journal
                                    </div>
                                @endif
                            </a>
                        </div>
                        <div class="card-body p-4 d-flex flex-column">
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill mb-2 py-1 px-2 align-self-start" style="font-size: 0.65rem;">{{ $blog->category->name }}</span>
                            <h5 class="card-title font-heading fw-bold mb-2 fs-5 clamp-2">
                                <a href="{{ route('blogs.show', $blog->slug) }}" class="text-dark text-decoration-none hover-gold">{{ $blog->title }}</a>
                            </h5>
                            <p class="card-text text-muted mb-0 clamp-4" style="font-size: 0.85rem; line-height: 1.6;">{{ $blog->excerpt }}</p>
                            
                            <div class="mt-auto pt-4 d-flex justify-content-between align-items-center border-top" style="font-size: 0.78rem;">
                                <span class="text-muted"><i class="bi bi-person me-1"></i>{{ $blog->author_name }}</span>
                                <span class="text-muted"><i class="bi bi-calendar-event me-1"></i>{{ $blog->published_at ? $blog->published_at->format('d M Y') : '' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted">
                    <i class="bi bi-journal-x display-4 d-block mb-3 text-success"></i>
                    <h4>No articles found</h4>
                    <p class="text-muted mt-2">Try adjusting your filters or search terms.</p>
                    <a href="{{ route('blogs.index') }}" class="btn btn-premium px-4 rounded-pill mt-3 text-uppercase fs-6">Clear All Filters</a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($blogs->hasPages())
            <div class="d-flex justify-content-center mt-5 pt-3">
                {{ $blogs->links() }}
            </div>
        @endif

    </div>
</section>
@endsection
