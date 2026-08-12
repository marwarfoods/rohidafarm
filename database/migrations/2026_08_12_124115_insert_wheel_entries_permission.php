<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!DB::table('permissions')->where('name', 'wheel-entries')->exists()) {
            DB::table('permissions')->insert([
                'name' => 'wheel-entries',
                'display_name' => 'Manage Wheel Popup Entries',
                'description' => 'View and delete visitor entries submitted via the spin-the-wheel popup.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('permissions')->where('name', 'wheel-entries')->delete();
    }
};
