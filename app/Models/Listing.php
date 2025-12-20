<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Listing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'package_id',
        'title',
        'slug',
        'description',
        'price',
        'currency',
        'city',
        'municipality',
        'address',
        'latitude',
        'longitude',
        'area',
        'rooms',
        'bathrooms',
        'floor',
        'total_floors',
        'year_built',
        'features',
        'listing_type',
        'status',
        'is_featured',
        'featured_until',
        'is_top',
        'top_until',
        'contact_name',
        'contact_phone',
        'contact_email',
        'meta_title',
        'meta_description',
        'views',
        'published_at',
        'expires_at',
        'promoted_at',
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'area' => 'decimal:0',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'features' => 'array',
        'is_featured' => 'boolean',
        'featured_until' => 'datetime',
        'is_top' => 'boolean',
        'top_until' => 'datetime',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'promoted_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ListingImage::class)->orderBy('order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ListingImage::class)->where('is_primary', true);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function isFavoritedBy(User $user): bool
    {
        return $this->favoritedBy()->where('user_id', $user->id)->exists();
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function formattedPrice(): string
    {
        return number_format($this->price, 0, ',', '.') . ' ' . $this->currency;
    }

    public function isTopActive(): bool
    {
        if (!$this->is_top) {
            return false;
        }
        
        if ($this->top_until === null) {
            return true;
        }
        
        return $this->top_until->isFuture();
    }

    public function isFeaturedActive(): bool
    {
        if (!$this->is_featured) {
            return false;
        }
        
        if ($this->featured_until === null) {
            return true;
        }
        
        return $this->featured_until->isFuture();
    }

    protected static function booted()
        {
     static::deleting(function ($listing) {
        // Delete all related images when a listing is deleted (soft or hard)
        foreach ($listing->images as $image) {
            $image->delete(); // This will also delete the image file via ListingImage model
        }
     });
    }



}