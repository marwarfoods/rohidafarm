<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Services\SeoService;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    protected $seoService;

    public function __construct(SeoService $seoService)
    {
        $this->seoService = $seoService;
    }

    /**
     * Display blog posts catalog.
     */
    public function index(Request $request)
    {
        $query = Blog::with('category')->where('is_published', true);

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->query('category'));
            });
        }

        // Search query
        if ($request->has('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $blogs = $query->orderBy('published_at', 'desc')->paginate(6);
        $categories = BlogCategory::where('is_active', true)->withCount('blogs')->get();

        $seo = $this->seoService->generateTags([
            'title' => 'Healthy Living Blog',
            'description' => 'Ayurvedic nutrition guidance, Ghee wellness benefits, and organic lifestyle recipes.'
        ]);

        return view('frontend.blogs.index', compact('blogs', 'categories', 'seo'));
    }

    /**
     * Display a blog post.
     */
    public function show(string $slug)
    {
        $blog = Blog::with('category')->where('slug', $slug)->firstOrFail();
        
        // Increment read count
        $blog->increment('view_count');

        // Related blogs
        $relatedBlogs = Blog::where('blog_category_id', $blog->blog_category_id)
            ->where('id', '!=', $blog->id)
            ->where('is_published', true)
            ->limit(3)
            ->get();

        $seo = $this->seoService->generateTags([
            'title' => $blog->title,
            'description' => $blog->excerpt,
            'keywords' => $blog->keywords,
            'image' => asset($blog->featured_image)
        ]);

        return view('frontend.blogs.show', compact('blog', 'relatedBlogs', 'seo'));
    }
}
