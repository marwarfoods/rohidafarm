<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstagramFeed;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InstagramFeedController extends Controller
{
    /**
     * Display a listing of the Instagram Feed posts.
     */
    public function index()
    {
        $posts = InstagramFeed::orderBy('sort_order', 'asc')->orderBy('created_at', 'desc')->get();
        return view('admin.instagram-feed.index', compact('posts'));
    }

    /**
     * Show the form for creating a new Instagram post.
     */
    public function create()
    {
        return view('admin.instagram-feed.create');
    }

    /**
     * Store a newly created Instagram post in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'row' => 'required|in:1,2',
            'link_url' => 'nullable|url|max:500',
            'caption' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_insta_' . Str::random(5) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/instagram'), $fileName);
            $imagePath = '/uploads/instagram/' . $fileName;
        } elseif ($request->filled('image_path')) {
            $imagePath = $request->input('image_path');
        } elseif ($request->filled('media_url')) {
            $imagePath = ltrim(parse_url($request->input('media_url'), PHP_URL_PATH), '/');
        }

        if (!$imagePath) {
            return back()->withInput()->withErrors(['image' => 'Please upload an image or select one from the Media Library.']);
        }

        InstagramFeed::create([
            'image_path' => $imagePath,
            'row' => $request->input('row', 1),
            'link_url' => $request->input('link_url'),
            'caption' => $request->input('caption'),
            'sort_order' => $request->input('sort_order', 0),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.instagram-feed.index')->with('success', 'Instagram post added successfully.');
    }

    /**
     * Show the form for editing the specified Instagram post.
     */
    public function edit($id)
    {
        $post = InstagramFeed::findOrFail($id);
        return view('admin.instagram-feed.edit', compact('post'));
    }

    /**
     * Update the specified Instagram post in storage.
     */
    public function update(Request $request, $id)
    {
        $post = InstagramFeed::findOrFail($id);

        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'row' => 'required|in:1,2',
            'link_url' => 'nullable|url|max:500',
            'caption' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        $imagePath = $post->image_path;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_insta_' . Str::random(5) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/instagram'), $fileName);
            $imagePath = '/uploads/instagram/' . $fileName;
        } elseif ($request->filled('image_path')) {
            $imagePath = $request->input('image_path');
        } elseif ($request->filled('media_url')) {
            $imagePath = ltrim(parse_url($request->input('media_url'), PHP_URL_PATH), '/');
        }

        $post->update([
            'image_path' => $imagePath,
            'row' => $request->input('row', 1),
            'link_url' => $request->input('link_url'),
            'caption' => $request->input('caption'),
            'sort_order' => $request->input('sort_order', 0),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.instagram-feed.index')->with('success', 'Instagram post updated successfully.');
    }

    /**
     * Remove the specified Instagram post from storage.
     */
    public function destroy($id)
    {
        $post = InstagramFeed::findOrFail($id);
        $post->delete();

        return redirect()->route('admin.instagram-feed.index')->with('success', 'Instagram post deleted successfully.');
    }
}
