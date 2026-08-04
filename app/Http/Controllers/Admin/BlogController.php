<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    use LogsActivity;

    /**
     * Display a listing of the blogs.
     */
    public function index()
    {
        $blogs = Blog::with('category')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.blogs.index', compact('blogs'));
    }

    /**
     * Show the form for creating a new blog.
     */
    public function create()
    {
        $categories = BlogCategory::where('is_active', true)->get();
        return view('admin.blogs.create', compact('categories'));
    }

    /**
     * Store a newly created blog in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'featured_image' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'keywords' => 'nullable|string',
        ]);

        $slug = Str::slug($request->title);
        // Ensure slug is unique
        $slugCount = Blog::where('slug', 'like', $slug . '%')->count();
        if ($slugCount > 0) {
            $slug = $slug . '-' . ($slugCount + 1);
        }

        $featuredImagePath = $request->featured_image;

        $blog = Blog::create([
            'blog_category_id' => $request->blog_category_id,
            'title' => $request->title,
            'slug' => $slug,
            'author_name' => auth()->user() ? auth()->user()->name : 'Admin',
            'excerpt' => $request->excerpt,
            'content' => $request->input('content'),
            'featured_image' => $featuredImagePath,
            'is_published' => $request->has('is_published'),
            'published_at' => $request->has('is_published') ? now() : null,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'keywords' => $request->keywords,
        ]);

        self::logActivity('blog_create', "Created blog post: {$blog->title}");

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post created successfully.');
    }

    /**
     * Show the form for editing the specified blog.
     */
    public function edit($id)
    {
        $blog = Blog::findOrFail($id);
        $categories = BlogCategory::where('is_active', true)->get();
        return view('admin.blogs.edit', compact('blog', 'categories'));
    }

    /**
     * Update the specified blog in storage.
     */
    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'featured_image' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'keywords' => 'nullable|string',
        ]);

        $slug = $blog->slug;
        if ($blog->title !== $request->title) {
            $slug = Str::slug($request->title);
            $slugCount = Blog::where('slug', 'like', $slug . '%')->where('id', '!=', $id)->count();
            if ($slugCount > 0) {
                $slug = $slug . '-' . ($slugCount + 1);
            }
        }

        $featuredImagePath = $request->featured_image;

        $wasPublished = $blog->is_published;
        $isPublished = $request->has('is_published');

        $blog->update([
            'blog_category_id' => $request->blog_category_id,
            'title' => $request->title,
            'slug' => $slug,
            'excerpt' => $request->excerpt,
            'content' => $request->input('content'),
            'featured_image' => $featuredImagePath,
            'is_published' => $isPublished,
            'published_at' => $isPublished ? ($wasPublished ? $blog->published_at : now()) : null,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'keywords' => $request->keywords,
        ]);

        self::logActivity('blog_update', "Updated blog post: {$blog->title}");

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post updated successfully.');
    }

    /**
     * Remove the specified blog from storage.
     */
    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);
        $title = $blog->title;

        $blog->delete();

        self::logActivity('blog_delete', "Deleted blog post: {$title}");

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post deleted successfully.');
    }
}
