<?php

namespace App\Console\Commands;

use App\Services\ShiprocketCheckoutCatalog;
use App\Services\ShiprocketCheckoutService;
use Illuminate\Console\Command;

/**
 * Push every collection and product to Shiprocket Checkout via the documented webhooks.
 */
class ShiprocketCheckoutSyncCatalog extends Command
{
    protected $signature = 'shiprocket-checkout:sync-catalog';

    protected $description = 'Send all categories and products to Shiprocket Checkout (product/collection webhooks)';

    public function handle(ShiprocketCheckoutService $checkout, ShiprocketCheckoutCatalog $catalog): int
    {
        if (!$checkout->hasCredentials()) {
            $this->error('Shiprocket Checkout API Key / Secret Key are not configured.');

            return self::FAILURE;
        }

        $result = $catalog->pushAll($checkout);

        $this->info("Synced {$result['collections']} collection(s) and {$result['products']} product(s).");
        foreach ($result['failed'] as $line) {
            $this->warn($line);
        }

        return $result['failed'] ? self::FAILURE : self::SUCCESS;
    }
}
