<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_seen_orders_at')->nullable()->after('wallet_balance');
            $table->timestamp('last_seen_customers_at')->nullable()->after('last_seen_orders_at');
        });

        // Initialize existing admin accounts so they start fresh
        DB::table('users')->where('role', 'admin')->update([
            'last_seen_orders_at' => now(),
            'last_seen_customers_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['last_seen_orders_at', 'last_seen_customers_at']);
        });
    }
};
