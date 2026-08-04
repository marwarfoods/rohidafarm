<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\VideoReview;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;

class VideoReviewController extends Controller
{
    use LogsActivity;

    /**
     * Display a listing of video reviews.
     */
    public function index()
    {
        $videoReviews = VideoReview::with('product')->orderBy('sort_order')->paginate(15);
        return view('admin.video_reviews.index', compact('videoReviews'));
    }

    /**
     * Show the form for creating a new video review.
     */
    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('admin.video_reviews.create', compact('products'));
    }

    /**
     * Store a newly created video review in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'reviewer_name' => 'required|string|max:255',
            'video' => 'required', // Can be file or path string
            'product_id' => 'nullable|exists:products,id',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $videoPath = null;
        if ($request->hasFile('video')) {
            $file = $request->file('video');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/videos'), $fileName);
            $videoPath = '/uploads/videos/' . $fileName;
        } elseif ($request->filled('video')) {
            $videoPath = $request->input('video');
        }

        $videoReview = VideoReview::create([
            'reviewer_name' => $request->input('reviewer_name'),
            'video_path' => $videoPath,
            'product_id' => $request->input('product_id'),
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->input('sort_order', 0),
        ]);

        $this->logActivity('Created video review for ' . $videoReview->reviewer_name);

        return redirect()->route('admin.video-reviews.index')->with('success', 'Video review created successfully.');
    }

    /**
     * Show the form for editing the specified video review.
     */
    public function edit($id)
    {
        $videoReview = VideoReview::findOrFail($id);
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('admin.video_reviews.edit', compact('videoReview', 'products'));
    }

    /**
     * Update the specified video review in storage.
     */
    public function update(Request $request, $id)
    {
        $videoReview = VideoReview::findOrFail($id);

        $request->validate([
            'reviewer_name' => 'required|string|max:255',
            'video' => 'nullable', // Can be file or path string
            'product_id' => 'nullable|exists:products,id',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = [
            'reviewer_name' => $request->input('reviewer_name'),
            'product_id' => $request->input('product_id'),
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->input('sort_order', 0),
        ];

        if ($request->hasFile('video')) {
            // Delete old file if exists
            if ($videoReview->video_path && file_exists(public_path($videoReview->video_path))) {
                @unlink(public_path($videoReview->video_path));
            }

            $file = $request->file('video');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/videos'), $fileName);
            $data['video_path'] = '/uploads/videos/' . $fileName;
        } elseif ($request->filled('video')) {
            if ($videoReview->video_path && file_exists(public_path($videoReview->video_path)) && $videoReview->video_path !== $request->input('video')) {
                @unlink(public_path($videoReview->video_path));
            }
            $data['video_path'] = $request->input('video');
        }

        $videoReview->update($data);

        $this->logActivity('Updated video review for ' . $videoReview->reviewer_name);

        return redirect()->route('admin.video-reviews.index')->with('success', 'Video review updated successfully.');
    }

    /**
     * Remove the specified video review from storage.
     */
    public function destroy($id)
    {
        $videoReview = VideoReview::findOrFail($id);

        // Delete video file
        if ($videoReview->video_path && file_exists(public_path($videoReview->video_path))) {
            @unlink(public_path($videoReview->video_path));
        }

        $reviewer = $videoReview->reviewer_name;
        $videoReview->delete();

        $this->logActivity('Deleted video review for ' . $reviewer);

        return redirect()->route('admin.video-reviews.index')->with('success', 'Video review deleted successfully.');
    }
}
