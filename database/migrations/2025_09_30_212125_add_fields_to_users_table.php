<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds additional fields to the default users table
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->text('bio')->nullable()->after('phone');
            $table->string('avatar')->nullable()->after('bio'); // Profile picture
            $table->string('city')->nullable()->after('avatar');
            $table->enum('role', ['user', 'admin'])->default('user')->after('city');
            $table->boolean('is_verified')->default(false)->after('role'); // Email verified
            $table->boolean('is_active')->default(true)->after('is_verified'); // Can ban users
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'bio',
                'avatar',
                'city',
                'role',
                'is_verified',
                'is_active',
                'last_login_at'
            ]);
        });
    }
};