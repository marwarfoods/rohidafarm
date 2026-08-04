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
        $permissions = [
            ['name' => 'orders', 'display_name' => 'Manage Orders', 'description' => 'Fulfill, view, update and delete customer orders.'],
            ['name' => 'customers', 'display_name' => 'Manage Customers', 'description' => 'Create, edit, view and delete customers.'],
            ['name' => 'products', 'display_name' => 'Manage Products', 'description' => 'Create, edit, view and delete store products.'],
            ['name' => 'categories', 'display_name' => 'Manage Categories', 'description' => 'Manage product categories and subcategories.'],
            ['name' => 'coupons', 'display_name' => 'Manage Coupons', 'description' => 'Manage store discount coupons.'],
            ['name' => 'stock', 'display_name' => 'Manage Stock', 'description' => 'Monitor and log product stock adjustments.'],
            ['name' => 'reviews', 'display_name' => 'Manage Reviews', 'description' => 'Moderate customer product reviews.'],
            ['name' => 'video-reviews', 'display_name' => 'Manage Video Reviews', 'description' => 'Moderate video reviews content.'],
            ['name' => 'blogs', 'display_name' => 'Manage Blogs', 'description' => 'Create, edit and delete blog posts and categories.'],
            ['name' => 'pages', 'display_name' => 'Manage Pages', 'description' => 'Edit policy pages and dynamic content.'],
            ['name' => 'media', 'display_name' => 'Manage Media & Layout', 'description' => 'Access media library, upload items, configure sliders and homepage banners.'],
            ['name' => 'settings', 'display_name' => 'Manage Settings', 'description' => 'View and edit SMTP, store settings and application settings.'],
            ['name' => 'logs', 'display_name' => 'Manage Audit Logs', 'description' => 'Access system audit logs.'],
            ['name' => 'roles', 'display_name' => 'Manage Roles & Staff', 'description' => 'Create roles, edit permissions, assign roles to administrative staff.'],
        ];

        foreach ($permissions as $p) {
            \DB::table('permissions')->insert(array_merge($p, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::table('permissions')->truncate();
    }
};
