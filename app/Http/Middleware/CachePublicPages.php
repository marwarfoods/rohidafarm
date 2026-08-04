<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CachePublicPages
{
    /**
     * Handle an incoming request for public static page caching.
     * Dynamic routes (Cart, Checkout, Payment, Dashboard, Auth, API) are completely bypassed.
     */
    public function handle(Request $request, Closure $next, int $ttlSeconds = 3600): Response
    {
        // 0. Never cache in local development or debug mode
        if (config('app.debug') || config('app.env') === 'local') {
            return $next($request);
        }
        // 1. Never cache non-GET requests (POST, PUT, DELETE, etc.)
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        // 2. Never cache authenticated user pages or dynamic user sessions
        if (auth()->check()) {
            return $this->addNoCacheHeaders($next($request));
        }

        // 3. Exclude dynamic routes explicitly
        if ($this->shouldBypassCache($request)) {
            return $this->addNoCacheHeaders($next($request));
        }

        // 4. Generate unique cache key for current URL and query parameters
        $cacheKey = 'public_page_' . md5($request->fullUrl());

        // 5. Check if cached HTML content exists
        if (Cache::has($cacheKey)) {
            $cachedContent = Cache::get($cacheKey);
            return response($cachedContent)
                ->header('Cache-Control', 'public, max-age=' . $ttlSeconds . ', s-maxage=' . ($ttlSeconds * 2))
                ->header('X-Rohida-Cache', 'HIT')
                ->header('ETag', md5($cachedContent));
        }

        // 6. Execute request and store response in Cache if HTTP 200 OK
        /** @var Response $response */
        $response = $next($request);

        if ($response->getStatusCode() === 200) {
            $content = $response->getContent();
            if ($content && is_string($content)) {
                Cache::put($cacheKey, $content, $ttlSeconds);
                $response->header('Cache-Control', 'public, max-age=' . $ttlSeconds . ', s-maxage=' . ($ttlSeconds * 2));
                $response->header('X-Rohida-Cache', 'MISS');
                $response->header('ETag', md5($content));
            }
        }

        return $response;
    }

    /**
     * Determine if request path must bypass caching.
     */
    protected function shouldBypassCache(Request $request): bool
    {
        $path = ltrim($request->getPathInfo(), '/');

        $uncachedPrefixes = [
            'cart',
            'checkout',
            'order',
            'customer',
            'admin',
            'login',
            'register',
            'password',
            'api',
            'auth'
        ];

        foreach ($uncachedPrefixes as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add strict anti-caching HTTP headers for real-time dynamic pages.
     */
    protected function addNoCacheHeaders(Response $response): Response
    {
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        $response->headers->set('X-Rohida-Cache', 'BYPASS');

        return $response;
    }
}
