<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'bio',
        'avatar',
        'city',
        'role',
        'is_verified',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    /**
     * Get all listings posted by this user
     * 
     * Usage: $user->listings
     */
    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    /**
     * Get active listings posted by this user
     * 
     * Usage: $user->activeListings
     */
    public function activeListings(): HasMany
    {
        return $this->hasMany(Listing::class)
                    ->where('status', 'active');
    }

    /**
     * Get listings this user has favorited
     * 
     * Usage: $user->favorites
     */
    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Listing::class, 'favorites')
                    ->withTimestamps();
    }

    /**
     * Check if user is an admin
     * 
     * Usage: $user->isAdmin()
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is active
     * 
     * Usage: $user->isActive()
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Get avatar URL
     * 
     * Usage: $user->avatarUrl()
     */
    public function avatarUrl(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        // Return default avatar or use a service like Gravatar
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name);
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }
}