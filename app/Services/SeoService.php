<?php

namespace App\Services;

use App\Models\Setting;

class SeoService
{
    /**
     * Generate HTML Meta tags dynamically.
     */
    public function generateTags(?array $customMeta = []): array
    {
        $defaultTitle = Setting::get('meta_title', 'RohidaFarm - Pure and Traditional Organic Ghee');
        $defaultDesc = Setting::get('meta_description', 'Premium luxury organic ghee, wood pressed oils, wild forest honey, and organic spices.');
        $defaultKeywords = Setting::get('meta_keywords', 'ghee, bilona ghee, a2 cow ghee, organic, raw honey, traditional ghee');
        $canonical = Setting::get('meta_canonical', url()->current());

        $title = $customMeta['meta_title'] ?? $customMeta['title'] ?? null;
        if ($title && $title !== $defaultTitle) {
            $title = $title . ' | RohidaFarm';
        } else {
            $title = $defaultTitle;
        }

        $ogImage = Setting::get('seo_og_image')
            ? asset(Setting::get('seo_og_image'))
            : (Setting::get('site_logo') ? asset(Setting::get('site_logo')) : asset('/assets/images/logo.png'));

        return [
            'title'           => $title,
            'description'     => $customMeta['meta_description'] ?? $customMeta['description'] ?? $defaultDesc,
            'keywords'        => $customMeta['keywords'] ?? $defaultKeywords,
            'canonical'       => $canonical,
            'og_title'        => $customMeta['meta_title'] ?? $customMeta['title'] ?? $defaultTitle,
            'og_description'  => $customMeta['meta_description'] ?? $customMeta['description'] ?? $defaultDesc,
            'og_image'        => $customMeta['image'] ?? $ogImage,
            'og_url'          => url()->current(),
            'twitter_card'    => 'summary_large_image',
        ];
    }
}
