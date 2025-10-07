<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This stores multiple images for each listing
     */
    public function up(): void
    {
        Schema::create('listing_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade'); // Which listing
            $table->string('image_path'); // Path to the image file
            $table->string('thumbnail_path')->nullable(); // Path to thumbnail (smaller version)
            $table->integer('order')->default(0); // Display order (first image is main)
            $table->boolean('is_primary')->default(false); // Main/cover image
            $table->timestamps();
            
            // Index for faster queries
            $table->index('listing_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_images');
    }
};