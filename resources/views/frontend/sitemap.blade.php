{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    {{-- Core Main Pages --}}
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ url('/shop') }}</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>{{ url('/about') }}</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>{{ url('/blogs') }}</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>{{ url('/contact') }}</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    <url>
        <loc>{{ url('/faq') }}</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>{{ url('/track-order') }}</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.5</priority>
    </url>

    {{-- Legal & Policy Pages (Mandatory for Google Ads & Compliance) --}}
    <url>
        <loc>{{ url('/privacy-policy') }}</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>{{ url('/terms-conditions') }}</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>{{ url('/refund-policy') }}</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>{{ url('/shipping-policy') }}</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>

    {{-- Dynamic Category URLs --}}
    @if(isset($categories) && $categories->isNotEmpty())
        @foreach($categories as $category)
            <url>
                <loc>{{ url('/shop-category/' . $category->slug) }}</loc>
                <lastmod>{{ $category->updated_at ? $category->updated_at->format('Y-m-d') : now()->format('Y-m-d') }}</lastmod>
                <changefreq>weekly</changefreq>
                <priority>0.8</priority>
            </url>
        @endforeach
    @endif

    {{-- Dynamic Product URLs --}}
    @if(isset($products) && $products->isNotEmpty())
        @foreach($products as $product)
            <url>
                <loc>{{ url('/product/' . $product->slug) }}</loc>
                <lastmod>{{ $product->updated_at ? $product->updated_at->format('Y-m-d') : now()->format('Y-m-d') }}</lastmod>
                <changefreq>weekly</changefreq>
                <priority>0.9</priority>
            </url>
        @endforeach
    @endif

    {{-- Dynamic Blog URLs --}}
    @if(isset($blogs) && $blogs->isNotEmpty())
        @foreach($blogs as $blog)
            <url>
                <loc>{{ url('/blog/' . $blog->slug) }}</loc>
                <lastmod>{{ $blog->updated_at ? $blog->updated_at->format('Y-m-d') : now()->format('Y-m-d') }}</lastmod>
                <changefreq>monthly</changefreq>
                <priority>0.7</priority>
            </url>
        @endforeach
    @endif
</urlset>
