<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Blog;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate dynamic XML sitemap.
     */
    public function index()
    {
        $products = Product::where('is_active', true)->orderBy('updated_at', 'desc')->get();
        $categories = Category::where('is_active', true)->orderBy('updated_at', 'desc')->get();
        $blogs = Blog::where('is_published', true)->orderBy('updated_at', 'desc')->get();

        $content = view('frontend.sitemap', compact('products', 'categories', 'blogs'))->render();

        return response($content, 200)
            ->header('Content-Type', 'text/xml');
    }
}
