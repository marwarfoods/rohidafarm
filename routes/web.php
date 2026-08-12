<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;
use App\Http\Controllers\Customer\AddressController as CustomerAddressController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\ActivityLogController as AdminActivityLogController;
use App\Http\Controllers\Admin\VideoReviewController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\HomepageManageController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\BlogCategoryController as AdminBlogCategoryController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\CertificationController as AdminCertificationController;
use App\Http\Controllers\Admin\WheelEntryController as AdminWheelEntryController;
use App\Http\Controllers\WheelEntryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Frontend Routes
|--------------------------------------------------------------------------
*/
Route::middleware('cache.public')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
    Route::get('/about', [PageController::class, 'about'])->name('about');
    Route::get('/faq', [PageController::class, 'faq'])->name('faq');
    Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacy-policy');
    Route::get('/refund-policy', [PageController::class, 'refundPolicy'])->name('refund-policy');
    Route::get('/shipping-policy', [PageController::class, 'shippingPolicy'])->name('shipping-policy');
    Route::get('/terms-conditions', [PageController::class, 'termsConditions'])->name('terms-conditions');
    Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');
    Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blogs.show');
});

Route::get('/shop', [ProductController::class, 'index'])->name('shop.index');
Route::get('/shop/ajax-search', [ProductController::class, 'ajaxSearch'])->name('shop.ajax-search');

// Google Social Login Routes
Route::get('/auth/google', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::get('/shop-category/{category}', [ProductController::class, 'index'])->name('shop.category');
Route::get('/shop-category/{category}/{subcategory}', [ProductController::class, 'index'])->name('shop.subcategory');
Route::redirect('/product', '/shop', 301);
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('shop.show');
Route::post('/product/{id}/review', [ProductController::class, 'storeReview'])->name('product.reviews.store');
Route::post('/api/check-delivery', [ProductController::class, 'checkDelivery'])->name('shop.check-delivery');

Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact/submit', [PageController::class, 'contactSubmit'])->name('contact.submit');
Route::post('/wheel-entry', [WheelEntryController::class, 'store'])->name('wheel-entry.store');
Route::get('/track-order', [PageController::class, 'trackOrder'])->name('track-order');

/*
|--------------------------------------------------------------------------
| Guest Cart Operations
|--------------------------------------------------------------------------
*/
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/drawer', [CartController::class, 'ajaxDrawer'])->name('cart.drawer');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/coupon/apply', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
Route::post('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:login')->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:register')->name('register.submit');

Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');



/*
|--------------------------------------------------------------------------
| Authenticated Customer Routes
|--------------------------------------------------------------------------
*/
// Checkout processing (accessible by guests to support auto-registration checkout)
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/calculate-totals', [CheckoutController::class, 'calculateTotals'])->name('checkout.calculate-totals');
Route::post('/checkout/store', [CheckoutController::class, 'store'])->middleware('throttle:checkout')->name('checkout.store');
Route::post('/checkout/direct-buy', [CheckoutController::class, 'directBuy'])->middleware('throttle:checkout')->name('checkout.direct-buy');
Route::post('/checkout/razorpay-callback', [CheckoutController::class, 'razorpayCallback'])->name('checkout.razorpay.callback');
Route::post('/checkout/razorpay-cancel/{uuid}', [CheckoutController::class, 'razorpayCancel'])->name('checkout.razorpay.cancel');
Route::get('/checkout/success/{uuid}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/order/receipt/{uuid}', [CheckoutController::class, 'receipt'])->name('order.receipt');

Route::middleware(['auth'])->group(function () {
    // Customer Portal Group
    Route::prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
        
        // Orders & Tracking
        Route::get('/orders', [CustomerDashboardController::class, 'orders'])->name('orders');
        Route::get('/orders/{id}', [CustomerDashboardController::class, 'showOrder'])->name('orders.show');
        Route::post('/orders/{id}/cancel', [CustomerDashboardController::class, 'cancelOrder'])->name('orders.cancel');

        // Manage Profile
        Route::get('/profile', [CustomerProfileController::class, 'index'])->name('profile');
        Route::post('/profile', [CustomerProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/password', [CustomerProfileController::class, 'updatePassword'])->name('profile.password');

        // Wallet Add Money
        Route::post('/wallet/add', [CustomerDashboardController::class, 'addWalletBalance'])->name('wallet.add');

        // Manage Addresses
        Route::get('/addresses', [CustomerAddressController::class, 'index'])->name('addresses.index');
        Route::post('/addresses/store', [CustomerAddressController::class, 'store'])->name('addresses.store');
        Route::post('/addresses/{id}/update', [CustomerAddressController::class, 'update'])->name('addresses.update');
        Route::delete('/addresses/{id}/delete', [CustomerAddressController::class, 'destroy'])->name('addresses.delete');
        Route::post('/addresses/{id}/default', [CustomerAddressController::class, 'makeDefault'])->name('addresses.default');
    });
});

/*
|--------------------------------------------------------------------------
| Administrative Dashboard Area
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::post('/cache/clear', [AdminDashboardController::class, 'clearCache'])->name('cache.clear')->middleware('permission:settings');

    // Products
    Route::middleware('permission:products')->group(function () {
        Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
        Route::post('/products/store', [AdminProductController::class, 'store'])->name('products.store');
        Route::get('/products/{id}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
        Route::post('/products/{id}/update', [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}/delete', [AdminProductController::class, 'destroy'])->name('products.delete');
        Route::post('/products/{id}/stock', [AdminProductController::class, 'updateStock'])->name('products.stock');
        Route::post('/products/{id}/status', [AdminProductController::class, 'updateStatus'])->name('products.status');
    });

    // Reviews
    Route::middleware('permission:reviews')->group(function () {
        Route::get('/reviews', [AdminProductController::class, 'reviewsIndex'])->name('reviews.index');
        Route::get('/reviews/create', [AdminProductController::class, 'reviewsCreate'])->name('reviews.create');
        Route::post('/reviews/store', [AdminProductController::class, 'reviewsStore'])->name('reviews.store');
        Route::post('/reviews/{id}/approve', [AdminProductController::class, 'reviewsApprove'])->name('reviews.approve');
        Route::post('/reviews/{id}/update', [AdminProductController::class, 'reviewsUpdate'])->name('reviews.update');
        Route::delete('/reviews/{id}/delete', [AdminProductController::class, 'reviewsDelete'])->name('reviews.delete');
        Route::post('/reviews/bulk-delete', [AdminProductController::class, 'reviewsBulkDelete'])->name('reviews.bulk-delete');
    });

    // Video Reviews
    Route::middleware('permission:video-reviews')->group(function () {
        Route::get('/video-reviews', [VideoReviewController::class, 'index'])->name('video-reviews.index');
        Route::get('/video-reviews/create', [VideoReviewController::class, 'create'])->name('video-reviews.create');
        Route::post('/video-reviews/store', [VideoReviewController::class, 'store'])->name('video-reviews.store');
        Route::get('/video-reviews/{id}/edit', [VideoReviewController::class, 'edit'])->name('video-reviews.edit');
        Route::post('/video-reviews/{id}/update', [VideoReviewController::class, 'update'])->name('video-reviews.update');
        Route::delete('/video-reviews/{id}/delete', [VideoReviewController::class, 'destroy'])->name('video-reviews.delete');
    });

    // Media & Layout
    Route::middleware('permission:media')->group(function () {
        Route::get('/media', [MediaController::class, 'index'])->name('media.index');
        Route::post('/media/store', [MediaController::class, 'store'])->name('media.store');
        Route::get('/media/ajax-list', [MediaController::class, 'listAjax'])->name('media.ajax');
        Route::post('/media/url-import', [MediaController::class, 'storeFromUrl'])->name('media.url');
        Route::delete('/media/{id}/delete', [MediaController::class, 'destroy'])->name('media.delete');
        Route::post('/media/compress-all', [MediaController::class, 'compressAll'])->name('media.compress-all');
        Route::post('/media/bulk-compress', [MediaController::class, 'bulkCompress'])->name('media.bulk-compress');
        Route::post('/media/{id}/compress', [MediaController::class, 'singleCompress'])->name('media.compress');
        Route::post('/media/bulk-delete', [MediaController::class, 'bulkDelete'])->name('media.bulk-delete');

        Route::get('/homepage', [HomepageManageController::class, 'index'])->name('homepage.index');
        Route::post('/homepage/promo/update', [HomepageManageController::class, 'promoUpdate'])->name('homepage.promo.update');
        Route::post('/homepage/section-image/update', [HomepageManageController::class, 'sectionImageUpdate'])->name('homepage.section-image.update');
        Route::post('/homepage/slider/store', [HomepageManageController::class, 'sliderStore'])->name('homepage.slider.store');
        Route::post('/homepage/slider/{id}/update', [HomepageManageController::class, 'sliderUpdate'])->name('homepage.slider.update');
        Route::post('/homepage/slider/reorder', [HomepageManageController::class, 'sliderReorder'])->name('homepage.slider.reorder');
        Route::delete('/homepage/slider/{id}/delete', [HomepageManageController::class, 'sliderDelete'])->name('homepage.slider.delete');
        Route::post('/homepage/ticker/store', [HomepageManageController::class, 'tickerStore'])->name('homepage.ticker.store');
        Route::post('/homepage/ticker/reorder', [HomepageManageController::class, 'tickerReorder'])->name('homepage.ticker.reorder');
        Route::delete('/homepage/ticker/{id}/delete', [HomepageManageController::class, 'tickerDelete'])->name('homepage.ticker.delete');
        Route::post('/homepage/native-ingredient/store', [HomepageManageController::class, 'nativeIngredientStore'])->name('homepage.native.store');
        Route::post('/homepage/native-ingredient/{id}/update', [HomepageManageController::class, 'nativeIngredientUpdate'])->name('homepage.native.update');
        Route::post('/homepage/native-ingredient/reorder', [HomepageManageController::class, 'nativeIngredientReorder'])->name('homepage.native.reorder');
        Route::delete('/homepage/native-ingredient/{id}/delete', [HomepageManageController::class, 'nativeIngredientDelete'])->name('homepage.native.delete');
    });

    // Categories
    Route::middleware('permission:categories')->group(function () {
        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories/store', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::post('/categories/subcategory/store', [AdminCategoryController::class, 'storeSubcategory'])->name('categories.subcategory.store');
        Route::post('/categories/{id}/update', [AdminCategoryController::class, 'update'])->name('categories.update');
        Route::post('/categories/subcategory/{id}/update', [AdminCategoryController::class, 'updateSubcategory'])->name('categories.subcategory.update');
        Route::delete('/categories/{id}/delete', [AdminCategoryController::class, 'destroy'])->name('categories.delete');
        Route::delete('/categories/subcategory/{id}/delete', [AdminCategoryController::class, 'destroySubcategory'])->name('categories.subcategory.delete');
    });

    // Orders
    Route::middleware('permission:orders')->group(function () {
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/trash', [AdminOrderController::class, 'trash'])->name('orders.trash');
        Route::post('/orders/bulk-delete', [AdminOrderController::class, 'bulkDelete'])->name('orders.bulk-delete');
        Route::post('/orders/bulk-update-status', [AdminOrderController::class, 'bulkUpdateStatus'])->name('orders.bulk-update-status');
        Route::post('/orders/trash/bulk-restore', [AdminOrderController::class, 'bulkRestore'])->name('orders.bulk-restore');
        Route::post('/orders/trash/bulk-force-delete', [AdminOrderController::class, 'bulkForceDelete'])->name('orders.bulk-force-delete');
        Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
        Route::post('/orders/{id}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('/orders/{id}/sync-delhivery', [AdminOrderController::class, 'syncDelhivery'])->name('orders.sync-delhivery');
        Route::delete('/orders/{id}/delete', [AdminOrderController::class, 'destroy'])->name('orders.delete');
        Route::post('/orders/{id}/restore', [AdminOrderController::class, 'restore'])->name('orders.restore');
        Route::delete('/orders/{id}/force-delete', [AdminOrderController::class, 'forceDelete'])->name('orders.force-delete');
    });

    // Coupons
    Route::middleware('permission:coupons')->group(function () {
        Route::get('/coupons', [AdminCouponController::class, 'index'])->name('coupons.index');
        Route::get('/coupons/create', [AdminCouponController::class, 'create'])->name('coupons.create');
        Route::post('/coupons/store', [AdminCouponController::class, 'store'])->name('coupons.store');
        Route::get('/coupons/{id}/edit', [AdminCouponController::class, 'edit'])->name('coupons.edit');
        Route::post('/coupons/{id}/update', [AdminCouponController::class, 'update'])->name('coupons.update');
        Route::delete('/coupons/{id}/delete', [AdminCouponController::class, 'destroy'])->name('coupons.delete');
    });

    // Wheel Popup Entries
    Route::middleware('permission:wheel-entries')->group(function () {
        Route::get('/wheel-entries', [AdminWheelEntryController::class, 'index'])->name('wheel-entries.index');
        Route::delete('/wheel-entries/{id}/delete', [AdminWheelEntryController::class, 'destroy'])->name('wheel-entries.delete');
    });

    // Stock
    Route::middleware('permission:stock')->group(function () {
        Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
        Route::get('/stock/{id}', [StockController::class, 'show'])->name('stock.show');
        Route::post('/stock/{id}/update', [StockController::class, 'update'])->name('stock.update');
    });

    // Settings
    Route::middleware('permission:settings')->group(function () {
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings/save', [AdminSettingController::class, 'store'])->name('settings.save');
        Route::post('/settings/smtp/test', [AdminSettingController::class, 'testSmtp'])->name('settings.smtp.test');
        Route::post('/settings/google-oauth/test', [AdminSettingController::class, 'testGoogleOAuth'])->name('settings.google.test');
    });

    // Audit Logs
    Route::middleware('permission:logs')->group(function () {
        Route::get('/logs', [AdminActivityLogController::class, 'index'])->name('logs.index');
        Route::post('/logs/clear', [AdminActivityLogController::class, 'destroyAll'])->name('logs.clear');
    });

    // Blogs CMS
    Route::middleware('permission:blogs')->group(function () {
        Route::get('/blogs', [AdminBlogController::class, 'index'])->name('blogs.index');
        Route::get('/blogs/create', [AdminBlogController::class, 'create'])->name('blogs.create');
        Route::post('/blogs/store', [AdminBlogController::class, 'store'])->name('blogs.store');
        Route::get('/blogs/{id}/edit', [AdminBlogController::class, 'edit'])->name('blogs.edit');
        Route::post('/blogs/{id}/update', [AdminBlogController::class, 'update'])->name('blogs.update');
        Route::delete('/blogs/{id}/delete', [AdminBlogController::class, 'destroy'])->name('blogs.destroy');

        Route::get('/blog-categories', [AdminBlogCategoryController::class, 'index'])->name('blog-categories.index');
        Route::post('/blog-categories/store', [AdminBlogCategoryController::class, 'store'])->name('blog-categories.store');
        Route::post('/blog-categories/{id}/update', [AdminBlogCategoryController::class, 'update'])->name('blog-categories.update');
        Route::delete('/blog-categories/{id}/delete', [AdminBlogCategoryController::class, 'destroy'])->name('blog-categories.destroy');
    });

    // CMS Policy Pages
    Route::middleware('permission:pages')->group(function () {
        Route::get('/pages', [AdminPageController::class, 'index'])->name('pages.index');
        Route::get('/pages/{slug}/edit', [AdminPageController::class, 'edit'])->name('pages.edit');
        Route::post('/pages/{slug}/update', [AdminPageController::class, 'update'])->name('pages.update');
    });

    // Customers
    Route::middleware('permission:customers')->group(function () {
        Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/create', [AdminCustomerController::class, 'create'])->name('customers.create');
        Route::post('/customers/store', [AdminCustomerController::class, 'store'])->name('customers.store');
        Route::get('/customers/{id}', [AdminCustomerController::class, 'show'])->name('customers.show');
        Route::get('/customers/{id}/edit', [AdminCustomerController::class, 'edit'])->name('customers.edit');
        Route::put('/customers/{id}/update', [AdminCustomerController::class, 'update'])->name('customers.update');
        Route::delete('/customers/{id}/delete', [AdminCustomerController::class, 'destroy'])->name('customers.delete');
    });

    // Roles & Staff Permissions
    Route::middleware('permission:roles')->group(function () {
        Route::get('/roles-permissions', [AdminRoleController::class, 'index'])->name('roles.index');
        Route::post('/roles-permissions/store', [AdminRoleController::class, 'storeRole'])->name('roles.store');
        Route::post('/roles-permissions/{id}/update', [AdminRoleController::class, 'updateRole'])->name('roles.update');
        Route::delete('/roles-permissions/{id}/delete', [AdminRoleController::class, 'destroyRole'])->name('roles.delete');
        Route::post('/roles-permissions/assign', [AdminRoleController::class, 'assignRole'])->name('roles.assign');
        Route::delete('/roles-permissions/user/{userId}/remove/{roleId}', [AdminRoleController::class, 'removeUserRole'])->name('roles.remove-user-role');
    });

    // Certifications & Trust Marks
    Route::get('/certifications', [AdminCertificationController::class, 'index'])->name('certifications.index');
    Route::get('/certifications/create', [AdminCertificationController::class, 'create'])->name('certifications.create');
    Route::post('/certifications/store', [AdminCertificationController::class, 'store'])->name('certifications.store');
    Route::get('/certifications/{id}/edit', [AdminCertificationController::class, 'edit'])->name('certifications.edit');
    Route::post('/certifications/{id}/update', [AdminCertificationController::class, 'update'])->name('certifications.update');
    Route::delete('/certifications/{id}/delete', [AdminCertificationController::class, 'destroy'])->name('certifications.destroy');

    // Instagram Feed Gallery
    Route::get('/instagram-feed', [\App\Http\Controllers\Admin\InstagramFeedController::class, 'index'])->name('instagram-feed.index');
    Route::get('/instagram-feed/create', [\App\Http\Controllers\Admin\InstagramFeedController::class, 'create'])->name('instagram-feed.create');
    Route::post('/instagram-feed/store', [\App\Http\Controllers\Admin\InstagramFeedController::class, 'store'])->name('instagram-feed.store');
    Route::get('/instagram-feed/{id}/edit', [\App\Http\Controllers\Admin\InstagramFeedController::class, 'edit'])->name('instagram-feed.edit');
    Route::post('/instagram-feed/{id}/update', [\App\Http\Controllers\Admin\InstagramFeedController::class, 'update'])->name('instagram-feed.update');
    Route::delete('/instagram-feed/{id}/delete', [\App\Http\Controllers\Admin\InstagramFeedController::class, 'destroy'])->name('instagram-feed.destroy');
});
