<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    /**
     * Handle an incoming request and sanitize inputs to prevent XSS.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();
        
        $input = $this->sanitize($input);
        
        $request->merge($input);
        
        return $next($request);
    }

    /**
     * Recursively sanitize input array, excluding rich text fields from stripping HTML.
     */
    protected function sanitize(array $data, string $parentKey = ''): array
    {
        $excludeKeys = [
            'description',
            'short_description',
            'benefits',
            'ingredients',
            'nutrition_facts',
            'how_to_use',
            'content', // Allowed for Page Content, etc.
        ];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->sanitize($value, $key);
            } elseif (is_string($value)) {
                if (in_array($key, $excludeKeys) || in_array($parentKey, $excludeKeys)) {
                    // Preserve HTML tags (bold, italic, tables) for rich text editor fields
                    $data[$key] = trim($value);
                } else {
                    $data[$key] = strip_tags(trim($value));
                }
            }
        }

        return $data;
    }
}
