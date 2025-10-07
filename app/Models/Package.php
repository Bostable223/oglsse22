<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'duration_days',
        'price',
        'currency',
        'is_active',
        'order',
        'features',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'features' => 'array',
    ];

    /**
     * Get listings using this package
     */
    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    /**
     * Get formatted price
     */
    public function formattedPrice(): string
    {
        return number_format($this->price, 0, ',', '.') . ' ' . $this->currency;
    }

    /**
     * Check if package is for top listings
     */
    public function isTop(): bool
    {
        return $this->type === 'top';
    }

    /**
     * Check if package is for featured listings
     */
    public function isFeatured(): bool
    {
        return $this->type === 'featured';
    }

    /**
     * Scope: Get only active packages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get packages by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Order by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('duration_days');
    }
}