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
        Schema::table('shipments', function (Blueprint $table) {
            $table->renameColumn('shiprocket_shipment_id', 'delhivery_shipment_id');
            $table->renameColumn('shiprocket_order_id', 'delhivery_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->renameColumn('delhivery_shipment_id', 'shiprocket_shipment_id');
            $table->renameColumn('delhivery_order_id', 'shiprocket_order_id');
        });
    }
};
