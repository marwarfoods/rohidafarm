<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\SubCategory;
use App\Models\Coupon;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use LogsActivity;

    /**
     * List all products.
     */
    public function index()
    {
        $products = Product::with(['category', 'subCategory', 'brand'])->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Create Product Form.
     */
    public function create()
    {
        $categories = Category::all();
        $subcategories = SubCategory::all();
        $brands = Brand::all();
        $coupons = Coupon::where('is_active', true)->get();
        return view('admin.products.create', compact('categories', 'subcategories', 'brands', 'coupons'));
    }

    /**
     * Store new product.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:products,slug',
            'sku' => 'required|string|unique:products,sku',
            'weight' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'mrp' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0|lte:mrp',
            'short_description' => 'required|string',
            'description' => 'nullable|string',
            'benefits' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'nutrition_facts' => 'nullable|string',
            'how_to_use' => 'nullable|string',
            'image' => 'nullable', // Can be file or path string
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'display_coupons' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        $slug = $request->filled('slug') ? Str::slug($request->input('slug')) : Str::slug($request->input('name'));

        // Handle image upload simulation or existing gallery path selection
        $imagePath = '/assets/images/products/placeholder.jpg';
        if ($request->hasFile('image')) {
            // In standard setup we store it, here we simulate:
            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/products'), $fileName);
            
            // Compress the image in-place
            \App\Services\ImageOptimizerService::optimize(public_path('uploads/products/' . $fileName));
            
            $imagePath = '/uploads/products/' . $fileName;
        } elseif ($request->filled('image')) {
            $imagePath = $request->input('image');
        }

        $infographics = [];
        if ($request->hasFile('infographic_images')) {
            foreach ($request->file('infographic_images') as $file) {
                $fileName = time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/products/infographics'), $fileName);
                $infographics[] = '/uploads/products/infographics/' . $fileName;
            }
        }
        if ($request->filled('infographic_urls')) {
            $raw = $request->input('infographic_urls');
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                foreach ($decoded as $item) {
                    $path = is_array($item) ? ($item['full_url'] ?? $item['file_path'] ?? reset($item)) : (string)$item;
                    if ($path) $infographics[] = $path;
                }
            } else {
                $lines = array_filter(array_map('trim', explode("\n", $raw)));
                foreach ($lines as $line) {
                    if ($line) $infographics[] = $line;
                }
            }
        }

        $product = Product::create($request->except('image', 'gallery', 'variants', 'slug', 'infographic_images', 'infographic_urls') + [
            'slug' => $slug,
            'is_bilona' => $request->has('is_bilona'),
            'is_organic' => $request->has('is_organic'),
            'is_featured' => $request->has('is_featured'),
            'is_trending' => $request->has('is_trending'),
            'is_best_seller' => $request->has('is_best_seller'),
            'is_new_arrival' => $request->has('is_new_arrival'),
            'free_shipping_threshold' => $request->input('free_shipping_threshold'),
            'display_coupons' => $request->input('display_coupons'),
            'infographic_images' => array_values(array_unique($infographics)),
        ]);

        // Create main image mapping
        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => $imagePath,
            'is_primary' => true,
            'sort_order' => 0
        ]);

        // Save gallery images & videos
        if ($request->has('gallery')) {
            foreach ($request->input('gallery') as $index => $gItem) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $gItem['image_path'] ?? $gItem,
                    'video_path' => $gItem['video_path'] ?? null,
                    'is_primary' => false,
                    'sort_order' => $index + 1
                ]);
            }
        }

        // Save variants (weight options)
        if ($request->has('variants')) {
            foreach ($request->input('variants') as $v) {
                if (!empty($v['weight'])) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'name' => $product->name . ' - ' . $v['weight'],
                        'sku' => $product->sku . '-' . Str::slug($v['weight']),
                        'weight' => $v['weight'],
                        'mrp' => $v['mrp'] ?? $product->mrp,
                        'sale_price' => $v['sale_price'] ?? $product->sale_price,
                        'stock' => $v['stock'] ?? $product->stock,
                        'max_cart_qty' => $v['max_cart_qty'] ?? null,
                    ]);
                }
            }
        }

        // Save FAQs (question/answer pairs, in submitted order)
        if ($request->has('faqs')) {
            foreach ($request->input('faqs') as $index => $f) {
                if (!empty($f['question']) && !empty($f['answer'])) {
                    \App\Models\ProductFaq::create([
                        'product_id' => $product->id,
                        'question' => $f['question'],
                        'answer' => $f['answer'],
                        'sort_order' => $f['sort_order'] ?? $index,
                    ]);
                }
            }
        }

        self::logActivity('product_create', "Created product {$product->name} (SKU: {$product->sku})", ['product_id' => $product->id]);

        return redirect()->route('admin.products.index')->with('success', 'Product added successfully.');
    }

    /**
     * Edit Product Form.
     */
    public function edit($id)
    {
        $product = Product::with(['images' => function($q) {
            $q->orderBy('sort_order', 'asc');
        }, 'variants', 'faqs'])->findOrFail($id);
        $categories = Category::all();
        $subcategories = SubCategory::where('category_id', $product->category_id)->get();
        $brands = Brand::all();
        $coupons = Coupon::where('is_active', true)->get();

        // Real review stats for sidebar summary
        $approvedReviewsCount = \App\Models\ProductReview::where('product_id', $id)->where('is_approved', true)->count();
        $pendingReviewsCount  = \App\Models\ProductReview::where('product_id', $id)->where('is_approved', false)->count();
        $avgRating = \App\Models\ProductReview::where('product_id', $id)->where('is_approved', true)->avg('rating');

        return view('admin.products.edit', compact(
            'product', 'categories', 'subcategories', 'brands', 'coupons',
            'approvedReviewsCount', 'pendingReviewsCount', 'avgRating'
        ));
    }

    /**
     * Update Product.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:products,slug,' . $product->id,
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'stock' => 'required|integer|min:0',
            'mrp' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0|lte:mrp',
            'short_description' => 'required|string',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'display_coupons' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        $slug = $request->filled('slug') ? Str::slug($request->input('slug')) : Str::slug($request->input('name'));

        $product->update($request->except('image', 'gallery', 'variants', 'slug') + [
            'slug' => $slug,
            'is_bilona' => $request->has('is_bilona'),
            'is_organic' => $request->has('is_organic'),
            'is_featured' => $request->has('is_featured'),
            'is_trending' => $request->has('is_trending'),
            'is_best_seller' => $request->has('is_best_seller'),
            'is_new_arrival' => $request->has('is_new_arrival'),
            'free_shipping_threshold' => $request->input('free_shipping_threshold'),
            'display_coupons' => $request->input('display_coupons'),
        ]);

        // Infographic / Product Story Images
        $infographics = [];
        if ($request->has('infographic_form_submitted')) {
            $existing = $request->input('existing_infographics', []);
            if (is_array($existing)) {
                foreach ($existing as $eImg) {
                    if (!empty($eImg)) $infographics[] = $eImg;
                }
            }
        } else {
            $infographics = $product->infographic_images ?? [];
            if (!is_array($infographics)) {
                $infographics = is_string($infographics) ? (json_decode($infographics, true) ?? []) : [];
            }
        }

        if ($request->hasFile('infographic_images')) {
            foreach ($request->file('infographic_images') as $file) {
                $fileName = time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/products/infographics'), $fileName);
                $infographics[] = '/uploads/products/infographics/' . $fileName;
            }
        }
        if ($request->filled('infographic_urls')) {
            $raw = $request->input('infographic_urls');
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                foreach ($decoded as $item) {
                    $path = is_array($item) ? ($item['full_url'] ?? $item['file_path'] ?? reset($item)) : (string)$item;
                    if ($path) $infographics[] = $path;
                }
            } else {
                $lines = array_filter(array_map('trim', explode("\n", $raw)));
                foreach ($lines as $line) {
                    if ($line) $infographics[] = $line;
                }
            }
        }
        $product->update(['infographic_images' => array_values(array_unique($infographics))]);

        // Handle image upload simulation or existing gallery path selection
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/products'), $fileName);
            
            // Compress the image in-place
            \App\Services\ImageOptimizerService::optimize(public_path('uploads/products/' . $fileName));
            
            $imagePath = '/uploads/products/' . $fileName;

            // Remove previous primary
            ProductImage::where('product_id', $product->id)->update(['is_primary' => false]);
            
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $imagePath,
                'is_primary' => true,
                'sort_order' => 0
            ]);
        } elseif ($request->filled('image')) {
            $imagePath = $request->input('image');
            ProductImage::where('product_id', $product->id)->update(['is_primary' => false]);
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $imagePath,
                'is_primary' => true,
                'sort_order' => 0
            ]);
        }

        // Delete old variants, non-primary images, and FAQs
        ProductVariant::where('product_id', $product->id)->delete();
        ProductImage::where('product_id', $product->id)->where('is_primary', false)->delete();
        \App\Models\ProductFaq::where('product_id', $product->id)->delete();

        // Save new gallery items
        if ($request->has('gallery')) {
            foreach ($request->input('gallery') as $index => $gItem) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $gItem['image_path'] ?? $gItem,
                    'video_path' => $gItem['video_path'] ?? null,
                    'is_primary' => false,
                    'sort_order' => $index + 1
                ]);
            }
        }

        // Save new variants
        if ($request->has('variants')) {
            foreach ($request->input('variants') as $v) {
                if (!empty($v['weight'])) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'name' => $product->name . ' - ' . $v['weight'],
                        'sku' => $product->sku . '-' . Str::slug($v['weight']),
                        'weight' => $v['weight'],
                        'mrp' => $v['mrp'] ?? $product->mrp,
                        'sale_price' => $v['sale_price'] ?? $product->sale_price,
                        'stock' => $v['stock'] ?? $product->stock,
                        'max_cart_qty' => $v['max_cart_qty'] ?? null,
                    ]);
                }
            }
        }

        // Save new FAQs (question/answer pairs, in submitted order)
        if ($request->has('faqs')) {
            foreach ($request->input('faqs') as $index => $f) {
                if (!empty($f['question']) && !empty($f['answer'])) {
                    \App\Models\ProductFaq::create([
                        'product_id' => $product->id,
                        'question' => $f['question'],
                        'answer' => $f['answer'],
                        'sort_order' => $f['sort_order'] ?? $index,
                    ]);
                }
            }
        }

        self::logActivity('product_update', "Updated product {$product->name}", ['product_id' => $product->id]);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Delete Product.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $name = $product->name;
        $product->delete();

        self::logActivity('product_delete', "Deleted product {$name}", ['product_id' => $id]);

        return redirect()->route('admin.products.index')->with('success', 'Product deleted (soft deleted) successfully.');
    }

    /**
     * Quick stock status toggle.
     */
    public function updateStock(Request $request, $id)
    {
        $request->validate([
            'stock' => 'required|integer|min:0'
        ]);

        $product = Product::findOrFail($id);
        $product->update(['stock' => $request->input('stock')]);

        self::logActivity('product_restock', "Updated stock of {$product->name} to {$request->input('stock')}", ['product_id' => $id]);

        return response()->json([
            'status' => 'success',
            'message' => 'Stock quantity updated successfully.'
        ]);
    }

    /**
     * Update a single product's active/featured status. Used both for the
     * per-row toggle and, called once per selected product, by the bulk
     * status action on the products list (so the UI can show real,
     * item-by-item progress instead of one opaque bulk request).
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'field' => 'required|string|in:is_active,is_featured',
            'value' => 'required|boolean',
        ]);

        $product = Product::findOrFail($id);
        $product->update([$request->input('field') => $request->boolean('value')]);

        self::logActivity('product_status_update', "Set {$request->input('field')} = " . ($request->boolean('value') ? 'true' : 'false') . " for {$product->name}", ['product_id' => $id]);

        return response()->json([
            'status' => 'success',
            'message' => 'Product status updated successfully.',
            'product' => ['id' => $product->id, $request->input('field') => $product->{$request->input('field')}],
        ]);
    }

    /**
     * Display reviews management panel with filters.
     */
    public function reviewsIndex(\Illuminate\Http\Request $request)
    {
        $query = \App\Models\ProductReview::with(['product', 'user']);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('status')) {
            $query->where('is_approved', $request->status === 'approved');
        }
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        $reviews  = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $products = Product::orderBy('name')->get(['id', 'name']);
        return view('admin.reviews.index', compact('reviews', 'products'));
    }

    /**
     * Approve review.
     */
    public function reviewsApprove($id)
    {
        $review = \App\Models\ProductReview::findOrFail($id);
        $review->update(['is_approved' => true]);

        // Recalculate average rating of associated product
        $product = $review->product;
        $avgRating = \App\Models\ProductReview::where('product_id', $product->id)
            ->where('is_approved', true)
            ->avg('rating');
        $reviewsCount = \App\Models\ProductReview::where('product_id', $product->id)
            ->where('is_approved', true)
            ->count();

        $product->update([
            'rating' => $avgRating ?: 5.0,
            'reviews_count' => $reviewsCount
        ]);

        self::logActivity('review_approve', "Approved review: {$review->title} for product {$product->name}", ['review_id' => $id]);

        return redirect()->back()->with('success', 'Review approved successfully.');
    }

    /**
     * Delete a single review (also removes uploaded images from disk).
     */
    public function reviewsDelete($id)
    {
        $review = \App\Models\ProductReview::findOrFail($id);
        $product = $review->product;

        // Delete uploaded review images from disk
        if (!empty($review->images)) {
            foreach ($review->images as $imgPath) {
                $fullPath = public_path($imgPath);
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }
        }

        $review->forceDelete();

        // Recalculate product rating
        $avgRating    = \App\Models\ProductReview::where('product_id', $product->id)->where('is_approved', true)->avg('rating');
        $reviewsCount = \App\Models\ProductReview::where('product_id', $product->id)->where('is_approved', true)->count();
        $product->update(['rating' => $avgRating ?: 5.0, 'reviews_count' => $reviewsCount]);

        self::logActivity('review_delete', "Deleted review: {$review->title}", ['review_id' => $id]);

        return redirect()->back()->with('success', 'Review deleted successfully.');
    }

    /**
     * Bulk delete reviews (checkbox selection) — also cleans up images.
     */
    public function reviewsBulkDelete(\Illuminate\Http\Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);

        $reviews = \App\Models\ProductReview::whereIn('id', $request->ids)->get();
        $affectedProductIds = $reviews->pluck('product_id')->unique();

        foreach ($reviews as $review) {
            if (!empty($review->images)) {
                foreach ($review->images as $imgPath) {
                    $fullPath = public_path($imgPath);
                    if (file_exists($fullPath)) @unlink($fullPath);
                }
            }
            $review->forceDelete();
        }

        // Recalculate ratings for all affected products
        foreach ($affectedProductIds as $pid) {
            $product = \App\Models\Product::find($pid);
            if ($product) {
                $avg   = \App\Models\ProductReview::where('product_id', $pid)->where('is_approved', true)->avg('rating');
                $count = \App\Models\ProductReview::where('product_id', $pid)->where('is_approved', true)->count();
                $product->update(['rating' => $avg ?: 5.0, 'reviews_count' => $count]);
            }
        }

        self::logActivity('review_bulk_delete', 'Bulk deleted ' . count($request->ids) . ' reviews.');

        return redirect()->back()->with('success', count($request->ids) . ' review(s) deleted successfully.');
    }

    /**
     * Update a review (title, body, rating, approval status) from admin.
     */
    public function reviewsUpdate(\Illuminate\Http\Request $request, $id)
    {
        $review = \App\Models\ProductReview::findOrFail($id);

        $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'customer_email'=> 'nullable|email|max:255',
            'customer_phone'=> 'nullable|string|max:20',
            'title'       => 'required|string|max:255',
            'review'      => 'required|string',
            'rating'      => 'required|integer|min:1|max:5',
            'is_approved' => 'nullable|boolean',
            'review_images.*' => 'nullable|image|max:2048',
        ]);

        $uploadedImages = $review->images ?? [];
        
        // Remove specific images if requested
        if ($request->has('remove_images')) {
            foreach ($request->remove_images as $imgToRemove) {
                if (($key = array_search($imgToRemove, $uploadedImages)) !== false) {
                    unset($uploadedImages[$key]);
                    $fullPath = public_path($imgToRemove);
                    if (file_exists($fullPath)) @unlink($fullPath);
                }
            }
            $uploadedImages = array_values($uploadedImages);
        }

        // Add new images (max 4 total)
        if ($request->hasFile('review_images')) {
            $remainingSlots = 4 - count($uploadedImages);
            $files = array_slice($request->file('review_images'), 0, max(0, $remainingSlots));
            
            foreach ($files as $file) {
                $fileName = time() . '_' . rand(100, 999) . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/reviews'), $fileName);
                \App\Services\ImageOptimizerService::optimize(public_path('uploads/reviews/' . $fileName));
                $uploadedImages[] = '/uploads/reviews/' . $fileName;
            }
        }

        $wasApproved = $review->is_approved;
        $review->update([
            'customer_name' => $request->customer_name,
            'customer_email'=> $request->customer_email,
            'customer_phone'=> $request->customer_phone,
            'title'       => $request->title,
            'review'      => $request->review,
            'rating'      => $request->rating,
            'is_approved' => $request->boolean('is_approved'),
            'images'      => empty($uploadedImages) ? null : $uploadedImages,
        ]);

        // Recalculate product rating if approval status changed or rating changed
        $product = $review->product;
        $avgRating    = \App\Models\ProductReview::where('product_id', $product->id)->where('is_approved', true)->avg('rating');
        $reviewsCount = \App\Models\ProductReview::where('product_id', $product->id)->where('is_approved', true)->count();
        $product->update([
            'rating'        => $avgRating ?: 5.0,
            'reviews_count' => $reviewsCount
        ]);

        self::logActivity('review_update', "Edited review #{$id}: {$review->title}", ['review_id' => $id]);

        return redirect()->back()->with('success', 'Review updated successfully.');
    }

    /**
     * Show form to manually create a review.
     */
    public function reviewsCreate()
    {
        $products = Product::orderBy('name')->get();
        return view('admin.reviews.create', compact('products'));
    }

    /**
     * Store a manually created review.
     */
    public function reviewsStore(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'product_id'    => 'required|exists:products,id',
            'customer_name' => 'required|string|max:255',
            'customer_email'=> 'required|email|max:255',
            'customer_phone'=> 'nullable|string|max:20',
            'title'         => 'required|string|max:255',
            'review'        => 'required|string',
            'rating'        => 'required|integer|min:1|max:5',
            'review_images.*' => 'nullable|image|max:2048',
        ]);

        $uploadedImages = [];
        if ($request->hasFile('review_images')) {
            $files = array_slice($request->file('review_images'), 0, 4);
            foreach ($files as $file) {
                $fileName = time() . '_' . rand(100, 999) . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/reviews'), $fileName);
                \App\Services\ImageOptimizerService::optimize(public_path('uploads/reviews/' . $fileName));
                $uploadedImages[] = '/uploads/reviews/' . $fileName;
            }
        }

        $review = \App\Models\ProductReview::create([
            'product_id'    => $request->product_id,
            'user_id'       => \Illuminate\Support\Facades\Auth::id(),
            'customer_name' => $request->customer_name,
            'customer_email'=> $request->customer_email,
            'customer_phone'=> $request->customer_phone,
            'title'         => $request->title,
            'review'        => $request->review,
            'rating'        => $request->rating,
            'images'        => empty($uploadedImages) ? null : $uploadedImages,
            'is_approved'   => true, // Auto-approve manual admin reviews
        ]);

        // Recalculate product rating
        $product = Product::find($request->product_id);
        $avgRating    = \App\Models\ProductReview::where('product_id', $product->id)->where('is_approved', true)->avg('rating');
        $reviewsCount = \App\Models\ProductReview::where('product_id', $product->id)->where('is_approved', true)->count();
        $product->update([
            'rating'        => $avgRating ?: 5.0,
            'reviews_count' => $reviewsCount
        ]);

        self::logActivity('review_manual_create', "Manually created review for product {$product->name}");

        return redirect()->route('admin.reviews.index')->with('success', 'Review added successfully.');
    }
}
