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
            $table->boolean('show_on_home')->default(true)->after('is_new_arrival')->index();
            $table->boolean('show_on_shop')->default(true)->after('show_on_home')->index();
            $table->boolean('show_on_category')->default(true)->after('show_on_shop')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['show_on_home', 'show_on_shop', 'show_on_category']);
        });
    }
};
