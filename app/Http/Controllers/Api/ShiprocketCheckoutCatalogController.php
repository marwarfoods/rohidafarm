<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ShiprocketCheckoutCatalog;
use App\Services\ShiprocketCheckoutService;
use Illuminate\Http\Request;

/**
 * Seller catalog APIs consumed by Shiprocket Checkout (Fetch Products / by Collection / Collections).
 * Response structure follows the "Catalog APIs" section of the official Fastrr API documentation.
 */
class ShiprocketCheckoutCatalogController extends Controller
{
    public function __construct(
        private ShiprocketCheckoutCatalog $catalog,
        private ShiprocketCheckoutService $checkout,
    ) {
    }

    public function products(Request $request)
    {
        [$page, $limit] = $this->paging($request);
        $payload = $this->catalog->products($page, $limit);
        // Only mark the catalog as configured once a fetch has actually succeeded.
        $this->checkout->touch('shiprocket_checkout_last_catalog_fetch_at');

        return response()->json($payload);
    }

    public function collectionProducts(Request $request)
    {
        $request->validate(['collection_id' => 'required|integer|min:1']);
        [$page, $limit] = $this->paging($request);

        return response()->json($this->catalog->products($page, $limit, (int) $request->query('collection_id')));
    }

    public function collections(Request $request)
    {
        [$page, $limit] = $this->paging($request);

        return response()->json($this->catalog->collections($page, $limit));
    }

    private function paging(Request $request): array
    {
        $request->validate([
            'page' => 'nullable|integer|min:0',
            'limit' => 'nullable|integer|min:1|max:' . ShiprocketCheckoutCatalog::MAX_LIMIT,
        ]);

        return [(int) $request->query('page', 1), (int) $request->query('limit', 100)];
    }
}
