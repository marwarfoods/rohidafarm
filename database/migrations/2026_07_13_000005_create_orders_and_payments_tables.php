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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('order_number')->unique()->index();
            $table->string('status')->default('pending')->index(); // pending, processing, shipped, delivered, cancelled, refunded
            
            // Financial details
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0.00);
            $table->decimal('shipping_charges', 10, 2)->default(0.00);
            $table->string('coupon_code')->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2);
            
            // Payment details
            $table->string('payment_method')->default('cod'); // cod, razorpay, stripe, paypal, wallet
            $table->string('payment_status')->default('pending')->index(); // pending, paid, failed, refunded
            
            // Shipping details snapshot
            $table->string('shipping_name');
            $table->string('shipping_phone');
            $table->string('shipping_address_line1');
            $table->string('shipping_address_line2')->nullable();
            $table->string('shipping_city');
            $table->string('shipping_state');
            $table->string('shipping_postal_code');
            $table->string('shipping_country')->default('India');
            
            // Delivery details
            $table->string('tracking_number')->nullable()->index();
            $table->string('tracking_url')->nullable();
            $table->string('tracking_carrier')->nullable();
            $table->timestamp('estimated_delivery')->nullable();
            $table->string('shipment_status')->nullable(); // delhivery shipment status
            
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->onDelete('set null');
            $table->string('product_name');
            $table->string('variant_name')->nullable();
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('payment_method');
            $table->string('transaction_id')->nullable()->index();
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending'); // pending, successful, failed, refunded
            $table->text('payload')->nullable(); // to log gateway API responses
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('type'); // credit, debit
            $table->string('description')->nullable();
            $table->nullableMorphs('referable'); // e.g. referable_id = order_id, referable_type = App\Models\Order
            $table->timestamps();
        });

        Schema::create('track_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('status'); // order_placed, packed, shipped, out_for_delivery, delivered, cancelled
            $table->string('description')->nullable(); // description e.g. "Package has been dispatched from Surat Hub"
            $table->string('location')->nullable();
            $table->timestamp('occurred_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('shiprocket_shipment_id')->nullable()->index();
            $table->string('shiprocket_order_id')->nullable()->index();
            $table->string('awb_code')->nullable()->index();
            $table->string('courier_name')->nullable();
            $table->string('status')->nullable();
            $table->text('response_payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('track_orders');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
