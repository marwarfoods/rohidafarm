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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('free_shipping_threshold', 10, 2)->nullable()->after('is_featured');
            $table->json('display_coupons')->nullable()->after('free_shipping_threshold');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->integer('max_cart_qty')->nullable()->after('stock');
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->string('target_type')->default('all')->after('discount_value'); // all, products, categories
            $table->json('target_ids')->nullable()->after('target_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['free_shipping_threshold', 'display_coupons']);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('max_cart_qty');
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn(['target_type', 'target_ids']);
        });
    }
};
