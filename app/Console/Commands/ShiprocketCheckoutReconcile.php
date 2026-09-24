<?php

namespace App\Console\Commands;

use App\Models\ShiprocketCheckoutOrder;
use App\Services\ShiprocketCheckoutOrderProcessor;
use App\Services\ShiprocketCheckoutService;
use Illuminate\Console\Command;

/**
 * Failsafe recommended by the Shiprocket Checkout docs: re-check pending checkout
 * sessions with the Order Details API in case an order webhook was missed.
 */
class ShiprocketCheckoutReconcile extends Command
{
    protected $signature = 'shiprocket-checkout:reconcile {--hours=48 : Look back this many hours} {--limit=100}';

    protected $description = 'Verify pending Shiprocket Checkout orders and create any missing Laravel orders';

    public function handle(ShiprocketCheckoutService $checkout, ShiprocketCheckoutOrderProcessor $processor): int
    {
        if (!$checkout->hasCredentials()) {
            $this->info('Shiprocket Checkout credentials not configured — skipping.');

            return self::SUCCESS;
        }

        $pending = ShiprocketCheckoutOrder::whereNull('order_id')
            ->whereNotIn('status', ['FAILED'])
            ->where('created_at', '>=', now()->subHours((int) $this->option('hours')))
            ->where('created_at', '<=', now()->subMinutes(2)) // give the webhook a chance first
            ->where(fn ($q) => $q->whereNull('verified_at')->orWhere('verified_at', '<=', now()->subMinutes(30))) // back off abandoned sessions
            ->orderBy('id')
            ->limit((int) $this->option('limit'))
            ->get();

        $created = 0;
        foreach ($pending as $record) {
            $record = $processor->sync($record->checkout_order_id, 'reconcile');
            if ($record->order_id) {
                $created++;
                $this->line("Created order #{$record->order_id} for checkout {$record->checkout_order_id}");
            }
        }

        $this->info("Checked {$pending->count()} pending checkout(s), created {$created} order(s).");

        return self::SUCCESS;
    }
}
