<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ListingImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'image_path',
        'thumbnail_path',
        'order',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function url($size = 'large')
    {
    return listing_image($this->image_path, $size);
    }

    public function thumbnailUrl()
    {
    return listing_image($this->image_path, 'thumbnail');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($image) {
            if (Storage::exists($image->image_path)) {
                Storage::delete($image->image_path);
            }
            if ($image->thumbnail_path && Storage::exists($image->thumbnail_path)) {
                Storage::delete($image->thumbnail_path);
            }
        });
    }

    

    
}