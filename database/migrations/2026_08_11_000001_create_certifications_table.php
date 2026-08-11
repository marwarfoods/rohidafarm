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
        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ISO, FSSAI, FDA, APEDA, etc.
            $table->string('slug')->unique();
            $table->string('logo_path')->nullable(); // Pill icon logo
            $table->json('certificate_images')->nullable(); // Multi-page certificate document images
            $table->string('certificate_number')->nullable(); // License or certificate ID
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certifications');
    }
};
