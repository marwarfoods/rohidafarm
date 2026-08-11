<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->bind(
            \App\Repositories\Contracts\ProductRepositoryInterface::class,
            \App\Repositories\Eloquent\ProductRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\CategoryRepositoryInterface::class,
            \App\Repositories\Eloquent\CategoryRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\OrderRepositoryInterface::class,
            \App\Repositories\Eloquent\OrderRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Implicitly grant admin all permissions
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->isAdmin() ? true : null;
        });

        // Register permission-based gates
        \Illuminate\Support\Facades\Gate::define('manage-catalog', function ($user) {
            return $user->hasPermission('manage-catalog');
        });

        \Illuminate\Support\Facades\Gate::define('manage-orders', function ($user) {
            return $user->hasPermission('manage-orders');
        });

        \Illuminate\Support\Facades\Gate::define('manage-customers', function ($user) {
            return $user->hasPermission('manage-customers');
        });

        // Share default SEO tags with all views and override config dynamically
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                // Dynamically override mailer configuration
                $mailer = strtolower(\App\Models\Setting::get('mail_mailer', config('mail.default')));
                
                config([
                    'mail.default' => $mailer,
                    'mail.mailers.smtp.host' => \App\Models\Setting::get('mail_host', config('mail.mailers.smtp.host')),
                    'mail.mailers.smtp.port' => \App\Models\Setting::get('mail_port', config('mail.mailers.smtp.port')),
                    'mail.mailers.smtp.encryption' => \App\Models\Setting::get('mail_encryption', config('mail.mailers.smtp.encryption')),
                    'mail.mailers.smtp.username' => \App\Models\Setting::get('mail_username', config('mail.mailers.smtp.username')),
                    'mail.mailers.smtp.password' => \App\Models\Setting::get('mail_password', config('mail.mailers.smtp.password')),
                    'mail.from.address' => \App\Models\Setting::get('mail_from_address', config('mail.from.address')),
                    
                    // Payment overrides
                    'services.razorpay.key' => \App\Models\Setting::get('razorpay_key_id', config('services.razorpay.key')),
                    'services.stripe.key' => \App\Models\Setting::get('stripe_key', config('services.stripe.key')),
                ]);
                
                // Force Laravel to rebuild the mailer with the new config
                \Illuminate\Support\Facades\Mail::purge($mailer);
                \Illuminate\Support\Facades\Mail::purge();
            }
            
            view()->composer('*', function ($view) {
                $view->with('seo', app(\App\Services\SeoService::class)->generateTags());
            });

            // Share only currently valid, active coupons with the store coupons popup,
            // each resolved to the product/category page it should deep-link to.
            view()->composer('frontend.partials.coupons-offcanvas', function ($view) {
                $now = now();

                $coupons = \App\Models\Coupon::where('is_active', true)
                    ->where(function ($q) use ($now) {
                        $q->whereNull('active_from')->orWhere('active_from', '<=', $now);
                    })
                    ->where(function ($q) use ($now) {
                        $q->whereNull('active_until')->orWhere('active_until', '>=', $now);
                    })
                    ->where(function ($q) {
                        $q->whereNull('usage_limit')->orWhereColumn('usage_count', '<', 'usage_limit');
                    })
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function ($coupon) {
                        $link = route('shop.index');

                        if ($coupon->target_type === 'products' && !empty($coupon->target_ids)) {
                            $product = \App\Models\Product::whereIn('id', $coupon->target_ids)
                                ->where('is_active', true)->first();
                            if ($product) {
                                $link = route('shop.show', $product->slug);
                            }
                        } elseif ($coupon->target_type === 'categories' && !empty($coupon->target_ids)) {
                            $category = \App\Models\Category::whereIn('id', $coupon->target_ids)
                                ->where('is_active', true)->first();
                            if ($category) {
                                $link = route('shop.category', $category->slug);
                            }
                        }

                        $coupon->target_link = $link;
                        return $coupon;
                    });

                $view->with('coupons', $coupons);
            });
        } catch (\Exception $e) {
            // Silently catch exceptions if database is not migrated yet
        }

        // ── Rate Limiters (Security Protection) ──
        \Illuminate\Support\Facades\RateLimiter::for('login', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)
                ->by($request->ip())
                ->response(function (\Illuminate\Http\Request $request) {
                    return back()->with('error', 'Too many login attempts. Please try again in a minute.');
                });
        });

        \Illuminate\Support\Facades\RateLimiter::for('register', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(3)
                ->by($request->ip())
                ->response(function (\Illuminate\Http\Request $request) {
                    return back()->with('error', 'Too many registration attempts. Please try again in a minute.');
                });
        });

        \Illuminate\Support\Facades\RateLimiter::for('checkout', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(3)
                ->by($request->ip())
                ->response(function (\Illuminate\Http\Request $request) {
                    return back()->with('error', 'Too many checkout attempts. Please wait before trying again.');
                });
        });

        // ── Smart Cache Auto-Flushing on Admin Model Updates ──
        $flushHomeCache = function () {
            \Illuminate\Support\Facades\Cache::forget('home_data_payload');
        };

        \App\Models\Product::saved(function ($product) use ($flushHomeCache) {
            $flushHomeCache();
            \Illuminate\Support\Facades\Cache::forget("product_detail_{$product->slug}");
            \Illuminate\Support\Facades\Cache::forget("product_related_{$product->id}");
        });

        \App\Models\Product::deleted(function ($product) use ($flushHomeCache) {
            $flushHomeCache();
            \Illuminate\Support\Facades\Cache::forget("product_detail_{$product->slug}");
            \Illuminate\Support\Facades\Cache::forget("product_related_{$product->id}");
        });

        \App\Models\Category::saved($flushHomeCache);
        \App\Models\Category::deleted($flushHomeCache);
        \App\Models\Banner::saved($flushHomeCache);
        \App\Models\Banner::deleted($flushHomeCache);
        \App\Models\Slider::saved($flushHomeCache);
        \App\Models\Slider::deleted($flushHomeCache);
        \App\Models\Blog::saved($flushHomeCache);
        \App\Models\Blog::deleted($flushHomeCache);
    }
}
