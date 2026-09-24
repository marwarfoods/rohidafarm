<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Stable Fastrr variant IDs. The row id IS the variant_id sent to Shiprocket Checkout,
        // so products without variants get a "default" row (product_variant_id = null).
        Schema::create('shiprocket_checkout_variants', function (Blueprint $table) {
            $table->id();
            $table->string('variant_key', 64)->unique(); // "p{product_id}-v{variant_id|0}"
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->timestamps();
        });

        // One row per Shiprocket Checkout session (access token) → at most one Laravel order.
        Schema::create('shiprocket_checkout_orders', function (Blueprint $table) {
            $table->id();
            $table->string('checkout_order_id', 64)->unique(); // result.data.order_id / webhook order_id
            $table->string('fastrr_order_id', 64)->nullable()->index();
            $table->string('platform_order_id', 64)->nullable();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('browser_token', 64)->nullable();
            $table->string('source', 20)->default('cart'); // cart | buy_now | webhook
            $table->string('status', 20)->default('CREATED'); // CREATED / INITIATED / FAILED / SUCCESS
            $table->string('payment_type', 30)->nullable();
            $table->string('payment_status', 30)->nullable();
            $table->decimal('total_amount_payable', 12, 2)->nullable();
            $table->json('cart_items')->nullable();
            $table->json('details')->nullable(); // last verified Order Details response
            $table->string('last_error', 500)->nullable();
            $table->timestamp('last_webhook_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shiprocket_checkout_orders');
        Schema::dropIfExists('shiprocket_checkout_variants');
    }
};
