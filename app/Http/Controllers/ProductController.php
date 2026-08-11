<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\SeoService;
use App\Services\DelhiveryService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productRepo;
    protected $categoryRepo;
    protected $delhiveryService;
    protected $seoService;

    public function __construct(
        ProductRepositoryInterface $productRepo,
        CategoryRepositoryInterface $categoryRepo,
        DelhiveryService $delhiveryService,
        SeoService $seoService
    ) {
        $this->productRepo = $productRepo;
        $this->categoryRepo = $categoryRepo;
        $this->delhiveryService = $delhiveryService;
        $this->seoService = $seoService;
    }

    /**
     * Display shop listing page with filters.
     */
    public function index(Request $request, $category = null, $subcategory = null)
    {
        $filters = $request->only([
            'min_price', 'max_price', 'brand', 'rating', 'in_stock', 'search', 'sort'
        ]);

        if ($category) {
            $filters['category'] = $category;
        } else {
            $filters['category'] = $request->input('category');
        }

        if ($subcategory) {
            $filters['subcategory'] = $subcategory;
        } else {
            $filters['subcategory'] = $request->input('subcategory');
        }

        $products = $this->productRepo->filterAndPaginate($filters, 10);
        $categories = $this->categoryRepo->getWithSubcategories();

        $seo = $this->seoService->generateTags([
            'title' => 'Shop Organic Farm Products',
            'description' => 'Explore the finest selection of A2 Cow Ghee, wood pressed oils, wild honey, and natural spices.',
            'keywords' => 'ghee shop, purchase organic ghee online, raw forest honey store'
        ]);

        return view('frontend.shop.index', compact('products', 'categories', 'filters', 'seo'));
    }

    /**
     * Display a product details page.
     */
    public function show(string $slug)
    {
        $product = $this->productRepo->findBySlug($slug);
        
        $relatedProducts = $this->productRepo->getRelated(
            $product->category_id,
            $product->id,
            4
        );

        // Fetch recently viewed items from session
        $recentlyViewedIds = session()->get('recently_viewed', []);
        
        // Exclude current product from list
        $recentlyViewed = [];
        if (!empty($recentlyViewedIds)) {
            $recentlyViewed = \App\Models\Product::with(['category', 'images'])
                ->whereIn('id', $recentlyViewedIds)
                ->where('id', '!=', $product->id)
                ->limit(4)
                ->get();
        }

        // Push current product ID to session
        if (!in_array($product->id, $recentlyViewedIds)) {
            array_unshift($recentlyViewedIds, $product->id);
            // Limit to last 5 products
            $recentlyViewedIds = array_slice($recentlyViewedIds, 0, 5);
            session()->put('recently_viewed', $recentlyViewedIds);
        }

        $seo = $this->seoService->generateTags([
            'title' => $product->meta_title ?: ($product->name . ' | RohidaFarm'),
            'description' => $product->meta_description ?: strip_tags($product->short_description),
            'keywords' => $product->meta_keywords ?: (strtolower($product->name) . ', organic, rohidafarm ghee'),
            'image' => $product->primaryImage ? asset($product->primaryImage->image_path) : null
        ]);

        $productCoupons = collect();
        if (!empty($product->display_coupons)) {
            if (in_array('all', $product->display_coupons)) {
                $productCoupons = \App\Models\Coupon::where('is_active', true)->get();
            } else {
                $productCoupons = \App\Models\Coupon::where('is_active', true)
                                    ->whereIn('code', $product->display_coupons)
                                    ->get();
            }
        }

        return view('frontend.shop.show', compact('product', 'relatedProducts', 'recentlyViewed', 'seo', 'productCoupons'));
    }

    /**
     * Check estimated delivery date for a pincode (API).
     */
    public function checkDelivery(Request $request)
    {
        $pincode = $request->input('pincode');
        $response = $this->delhiveryService->estimateDelivery($pincode);
        return response()->json($response);
    }

    /**
     * Store customer product review.
     */
    public function storeReview(Request $request, $id)
    {
        $rules = [
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'review' => 'required|string',
            'review_images.*' => 'nullable|image|max:2048',
        ];

        if (!auth()->check()) {
            $rules['guest_name'] = 'required|string|max:255';
            $rules['guest_email'] = 'required|email|max:255';
            $rules['guest_phone'] = 'nullable|string|max:20';
        }

        $request->validate($rules);

        $userId = null;
        $customerName = null;
        $customerEmail = null;
        $customerPhone = null;

        if (auth()->check()) {
            $userId = auth()->id();
        } else {
            $customerName = $request->input('guest_name');
            $customerEmail = $request->input('guest_email');
            $customerPhone = $request->input('guest_phone');
        }

        $uploadedImages = [];
        if ($request->hasFile('review_images')) {
            $files = array_slice($request->file('review_images'), 0, 4); // Max 4 images
            foreach ($files as $file) {
                $fileName = time() . '_' . rand(100, 999) . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/reviews'), $fileName);
                
                // Compress review images in-place
                \App\Services\ImageOptimizerService::optimize(public_path('uploads/reviews/' . $fileName));
                
                $uploadedImages[] = '/uploads/reviews/' . $fileName;
            }
        }

        \App\Models\ProductReview::create([
            'product_id' => $id,
            'user_id' => $userId,
            'customer_name' => $customerName,
            'customer_email' => $customerEmail,
            'customer_phone' => $customerPhone,
            'rating' => $request->input('rating'),
            'title' => $request->input('title'),
            'review' => $request->input('review'),
            'images' => $uploadedImages,
            'is_approved' => false // pending approval
        ]);

        return redirect()->back()->with('success', 'Thank you! Your review has been submitted and is pending admin approval.');
    }

    /**
     * Real-time AJAX Search.
     */
    public function ajaxSearch(Request $request)
    {
        $query = trim($request->input('search'));
        if (empty($query)) {
            return response()->json([]);
        }

        $products = \App\Models\Product::with(['primaryImage', 'category', 'variants'])
            ->where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('short_description', 'like', "%{$query}%")
                  ->orWhereHas('category', function($catQ) use ($query) {
                      $catQ->where('name', 'like', "%{$query}%");
                  })
                  ->orWhereHas('variants', function($varQ) use ($query) {
                      $varQ->where('name', 'like', "%{$query}%")
                           ->orWhere('sku', 'like', "%{$query}%");
                  });
            })
            ->limit(8)
            ->get();

        $results = $products->map(function($product) {
            return [
                'name' => $product->name,
                'slug' => $product->slug,
                'sale_price' => number_format($product->sale_price, 0),
                'mrp' => number_format($product->mrp, 0),
                'category_name' => $product->category ? $product->category->name : 'Rohida Farm',
                'image' => $product->primaryImage ? asset($product->primaryImage->image_path) : asset('/assets/images/products/placeholder.jpg'),
            ];
        });

        return response()->json($results);
    }
}
