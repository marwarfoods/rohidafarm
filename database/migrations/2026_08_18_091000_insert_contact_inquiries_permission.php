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
        if (!DB::table('permissions')->where('name', 'contact-inquiries')->exists()) {
            DB::table('permissions')->insert([
                'name' => 'contact-inquiries',
                'display_name' => 'Manage Contact Form Entries',
                'description' => 'View, mark as read, and delete submissions from the website contact form.',
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
        DB::table('permissions')->where('name', 'contact-inquiries')->delete();
    }
};
