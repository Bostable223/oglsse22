<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This creates the main listings table - the core of the classifieds site
     */
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Who posted it
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // What type of property
            
            // Basic Information
            $table->string('title'); // Listing title
            $table->string('slug')->unique(); // URL-friendly version
            $table->text('description'); // Full description
            $table->decimal('price', 12, 2); // Price (supports up to 9,999,999,999.99)
            $table->string('currency', 3)->default('RSD'); // RSD, EUR, USD
            
            // Location
            $table->string('city'); // Belgrade, Novi Sad, etc.
            $table->string('municipality')->nullable(); // Vračar, Novi Beograd, etc.
            $table->string('address')->nullable(); // Street address (optional)
            $table->decimal('latitude', 10, 8)->nullable(); // For maps
            $table->decimal('longitude', 11, 8)->nullable(); // For maps
            
            // Property Details
            $table->decimal('area', 8, 2)->nullable(); // Square meters
            $table->integer('rooms')->nullable(); // Number of rooms
            $table->integer('bathrooms')->nullable(); // Number of bathrooms
            $table->integer('floor')->nullable(); // Which floor
            $table->integer('total_floors')->nullable(); // Total floors in building
            $table->integer('year_built')->nullable(); // Construction year
            
            // Features (stored as JSON for flexibility)
            $table->json('features')->nullable(); // parking, elevator, balcony, etc.
            
            // Listing Type
            $table->enum('listing_type', ['sale', 'rent'])->default('sale'); // For sale or rent
            
            // Status and Promotion
            $table->enum('status', ['pending', 'active', 'sold', 'rented', 'expired', 'rejected'])
                  ->default('pending');
            $table->boolean('is_featured')->default(false); // Premium/featured listings
            $table->timestamp('featured_until')->nullable(); // When featured expires
            
            // Contact Information
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            
            // Statistics
            $table->integer('views')->default(0); // How many times viewed
            
            // Timestamps
            $table->timestamp('published_at')->nullable(); // When it went live
            $table->timestamp('expires_at')->nullable(); // Optional expiration
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at (for soft deletes)
            
            // Indexes for better performance
            $table->index('category_id');
            $table->index('user_id');
            $table->index('city');
            $table->index('status');
            $table->index('is_featured');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};