<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This creates the categories table for organizing listings
     * (e.g., Apartments, Houses, Land, Commercial)
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Category name (e.g., "Stanovi", "Kuće")
            $table->string('slug')->unique(); // URL-friendly version (e.g., "stanovi", "kuce")
            $table->text('description')->nullable(); // Optional description
            $table->string('icon')->nullable(); // Icon class or image for category
            $table->integer('order')->default(0); // Display order
            $table->boolean('is_active')->default(true); // Can disable categories
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};