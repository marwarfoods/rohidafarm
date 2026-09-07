<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerReview;
use App\Models\Product;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;

class CustomerReviewController extends Controller
{
    use LogsActivity;

    /**
     * Display a listing of customer reviews.
     */
    public function index()
    {
        $customerReviews = CustomerReview::with('product')->orderBy('sort_order')->paginate(15);
        return view('admin.customer_reviews.index', compact('customerReviews'));
    }

    /**
     * Show the form for creating a new customer review.
     */
    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('admin.customer_reviews.create', compact('products'));
    }

    /**
     * Store a newly created customer review.
     */
    public function store(Request $request)
    {
        $data = $this->validateData($request, true);

        $data['image_path'] = $this->resolveImage($request);
        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->input('sort_order', 0);

        $review = CustomerReview::create($data);

        $this->logActivity('Created customer review: ' . $review->title);

        return redirect()->route('admin.customer-reviews.index')
            ->with('success', 'Customer review created successfully.');
    }

    /**
     * Show the form for editing the specified customer review.
     */
    public function edit($id)
    {
        $customerReview = CustomerReview::findOrFail($id);
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('admin.customer_reviews.edit', compact('customerReview', 'products'));
    }

    /**
     * Update the specified customer review.
     */
    public function update(Request $request, $id)
    {
        $review = CustomerReview::findOrFail($id);

        $data = $this->validateData($request, false);

        $newImage = $this->resolveImage($request, $review);
        if ($newImage !== null) {
            $data['image_path'] = $newImage;
        }

        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->input('sort_order', 0);

        $review->update($data);

        $this->logActivity('Updated customer review: ' . $review->title);

        return redirect()->route('admin.customer-reviews.index')
            ->with('success', 'Customer review updated successfully.');
    }

    /**
     * Remove the specified customer review.
     */
    public function destroy($id)
    {
        $review = CustomerReview::findOrFail($id);

        if ($review->image_path
            && str_starts_with($review->image_path, '/uploads/')
            && file_exists(public_path($review->image_path))) {
            @unlink(public_path($review->image_path));
        }

        $title = $review->title;
        $review->delete();

        $this->logActivity('Deleted customer review: ' . $title);

        return redirect()->route('admin.customer-reviews.index')
            ->with('success', 'Customer review deleted successfully.');
    }

    /**
     * Shared validation rules.
     */
    private function validateData(Request $request, bool $imageRequired): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'product_id' => 'nullable|exists:products,id',
            'image' => ($imageRequired && !$request->filled('image_path') ? 'required' : 'nullable') . '|image|max:4096',
            'image_path' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);
    }

    /**
     * Resolve the review image from an upload or a media-library / URL path.
     * Returns null when nothing new was provided (keep existing on update).
     */
    private function resolveImage(Request $request, ?CustomerReview $existing = null): ?string
    {
        if ($request->hasFile('image')) {
            if ($existing
                && $existing->image_path
                && str_starts_with($existing->image_path, '/uploads/')
                && file_exists(public_path($existing->image_path))) {
                @unlink(public_path($existing->image_path));
            }

            $file = $request->file('image');
            $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/customer-reviews'), $fileName);

            return '/uploads/customer-reviews/' . $fileName;
        }

        if ($request->filled('image_path')) {
            return $request->input('image_path');
        }

        return null;
    }
}
