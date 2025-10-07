<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This creates the packages table for listing promotion options
     */
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "7 Days Top Listing"
            $table->string('slug')->unique(); // e.g., "7-days-top"
            $table->text('description')->nullable(); // Package benefits
            $table->enum('type', ['top', 'featured']); // top or featured
            $table->integer('duration_days'); // 7, 15, or 30 days
            $table->decimal('price', 10, 2); // Price in RSD or EUR
            $table->string('currency', 3)->default('RSD');
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0); // Display order
            $table->json('features')->nullable(); // Array of features/benefits
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};