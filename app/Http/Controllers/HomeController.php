<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Certification;
use App\Models\InstagramFeed;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Slider;
use App\Models\VideoReview;
use App\Services\SeoService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $seoService;

    public function __construct(SeoService $seoService)
    {
        $this->seoService = $seoService;
    }

    /**
     * Display home page.
     * Heavy product queries are cached; lightweight queries run fresh each time.
     */
    public function index()
    {
        // ── Sliders & Banners ─────────────────────────────────────────
        $sliders         = Slider::where('is_active', true)->orderBy('sort_order')->get();
        $promoBanners    = Banner::where('is_active', true)->where('position', 'homepage_promo')->get();
        $fullWidthBanner = Banner::where('is_active', true)->where('position', 'full_width_banner')->first();

        // ── Categories ────────────────────────────────────────────────
        $categories = Category::with(['products' => function ($q) {
            $q->where('is_active', true)->where('show_on_home', true)->with(['category:id,name,slug', 'images', 'primaryImage', 'variants'])->limit(8);
        }])->where('is_active', true)->get();

        // ── Tabbed Products ───────────────────────────────────────────
        $featuredProducts = Product::with(['category:id,name,slug', 'images', 'primaryImage', 'variants'])
            ->where('is_active', true)->where('show_on_home', true)->where('is_featured', true)->limit(8)->get();

        $trendingProducts = Product::with(['category:id,name,slug', 'images', 'primaryImage', 'variants'])
            ->where('is_active', true)->where('show_on_home', true)->where('is_trending', true)->limit(8)->get();

        $bestSellers = Product::with(['category:id,name,slug', 'images', 'primaryImage', 'variants'])
            ->where('is_active', true)->where('show_on_home', true)->where('is_best_seller', true)->limit(8)->get();

        $newArrivals = Product::with(['category:id,name,slug', 'images', 'primaryImage', 'variants'])
            ->where('is_active', true)->where('show_on_home', true)->where('is_new_arrival', true)->limit(8)->get();

        // ── Blogs ─────────────────────────────────────────────────────
        $blogs = Blog::with('category:id,name,slug')
            ->where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->limit(3)->get();

        // ── Reviews ───────────────────────────────────────────────────
        $reviews = ProductReview::with(['product:id,name,slug', 'user:id,name'])
            ->where('is_approved', true)->where('is_featured', true)->limit(6)->get();

        // ── Video Reviews ─────────────────────────────────────────────
        $videoReviews = VideoReview::with('product:id,name,slug,sale_price,mrp')
            ->where('is_active', true)->orderBy('sort_order')->get();

        // ── Native Ingredients ────────────────────────────────────────
        $nativeIngredients = \App\Models\NativeIngredient::where('is_active', true)
            ->orderBy('sort_order')->get();

        // ── Vedic Craftsmanship (Bilona Process) Steps ─────────────────
        $bilonaSteps = \App\Models\BilonaStep::where('is_active', true)
            ->orderBy('sort_order')->get();

        \Illuminate\Support\Facades\Log::info('🏠 HOMEPAGE LOAD — Vedic Craftsmanship steps fetched from DB: ' . $bilonaSteps->map(fn($s) => "[#{$s->id} \"{$s->title}\" -> {$s->image_path}]")->implode(' | '));

        // ── All Products Grid ─────────────────────────────────────────
        $allProducts = Product::with(['category:id,name,slug', 'images', 'primaryImage', 'variants'])
            ->where('is_active', true)
            ->where('show_on_home', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // ── Certifications & Trust Marks ──────────────────────────────
        $certifications = Certification::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        // ── Instagram Feed (Marquee) ──────────────────────────────────
        $instagramPosts = InstagramFeed::where('is_active', true)
            ->orderBy('sort_order')->orderBy('created_at', 'desc')
            ->get();
        $instaRow1 = $instagramPosts->where('row', 1)->values();
        $instaRow2 = $instagramPosts->where('row', 2)->values();

        // ── SEO — dynamic from settings ───────────────────────────────
        $seo = $this->seoService->generateTags();

        return view('frontend.home', compact(
            'sliders',
            'promoBanners',
            'fullWidthBanner',
            'categories',
            'featuredProducts',
            'trendingProducts',
            'bestSellers',
            'newArrivals',
            'blogs',
            'reviews',
            'videoReviews',
            'seo',
            'nativeIngredients',
            'bilonaSteps',
            'allProducts',
            'certifications',
            'instaRow1',
            'instaRow2'
        ));
    }
}
