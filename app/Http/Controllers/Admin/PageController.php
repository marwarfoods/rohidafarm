<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Predefined policy pages configuration.
     * slug => display label
     */
    protected array $predefinedPages = [
        'privacy-policy'      => 'Privacy Policy',
        'terms-conditions'    => 'Terms & Conditions',
        'refund-policy'       => 'Refund & Return Policy',
        'shipping-policy'     => 'Shipping Policy',
    ];

    /**
     * Ensure all predefined pages exist in DB (seed on first visit).
     */
    protected function ensurePages(): void
    {
        foreach ($this->predefinedPages as $slug => $title) {
            Page::firstOrCreate(
                ['slug' => $slug],
                [
                    'title'     => $title,
                    'content'   => '',
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * Admin Pages index — multi-tab list view.
     */
    public function index()
    {
        $this->ensurePages();

        $pages = Page::whereIn('slug', array_keys($this->predefinedPages))
                     ->get()
                     ->keyBy('slug');

        return view('admin.pages.index', [
            'pages'           => $pages,
            'predefinedPages' => $this->predefinedPages,
        ]);
    }

    /**
     * Show edit form for a specific page slug.
     */
    public function edit(string $slug)
    {
        $this->ensurePages();

        if (!array_key_exists($slug, $this->predefinedPages)) {
            abort(404, 'Page not found.');
        }

        $page = Page::where('slug', $slug)->firstOrFail();
        $label = $this->predefinedPages[$slug];

        return view('admin.pages.edit', compact('page', 'label'));
    }

    /**
     * Save / update a specific page's content and status.
     */
    public function update(Request $request, string $slug)
    {
        if (!array_key_exists($slug, $this->predefinedPages)) {
            abort(404, 'Page not found.');
        }

        $validated = $request->validate([
            'content'          => 'nullable|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'keywords'         => 'nullable|string|max:255',
            'is_active'        => 'nullable|boolean',
        ]);

        $page = Page::where('slug', $slug)->firstOrFail();
        $page->update([
            'content'          => $validated['content'] ?? '',
            'meta_title'       => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'keywords'         => $validated['keywords'] ?? null,
            'is_active'        => isset($request->is_active) ? 1 : 0,
        ]);

        return redirect()
            ->route('admin.pages.index', ['tab' => $slug])
            ->with('success', "'{$this->predefinedPages[$slug]}' page has been updated successfully!");
    }
}
