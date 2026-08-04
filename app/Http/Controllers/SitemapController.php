<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Blog;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate dynamic XML sitemap.
     */
    public function index()
    {
        $products = Product::where('is_active', true)->get();
        $blogs = Blog::where('is_published', true)->get();

        $content = view('frontend.sitemap', compact('products', 'blogs'))->render();

        return response($content, 200)
            ->header('Content-Type', 'text/xml');
    }
}
