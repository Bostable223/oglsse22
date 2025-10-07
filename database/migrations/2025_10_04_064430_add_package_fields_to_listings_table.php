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
        Schema::table('listings', function (Blueprint $table) {
            $table->foreignId('package_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->boolean('is_top')->default(false)->after('is_featured'); // Top listing (appears first)
            $table->timestamp('top_until')->nullable()->after('is_top'); // When top promotion expires
            $table->timestamp('promoted_at')->nullable()->after('published_at'); // When promotion started
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropColumn(['package_id', 'is_top', 'top_until', 'promoted_at']);
        });
    }
};