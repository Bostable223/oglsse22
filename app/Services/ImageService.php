<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Http\UploadedFile;

class ImageService
{
    /**
     * Image size configurations
     */
    private const SIZES = [
        'thumbnail' => ['width' => 300, 'height' => 300],
        'medium' => ['width' => 800, 'height' => 600],
        'large' => ['width' => 1200, 'height' => 900],
    ];

    /**
     * Maximum file size in bytes (5MB)
     */
    private const MAX_FILE_SIZE = 5 * 1024 * 1024;

    /**
     * Allowed image types
     */
    private const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

    /**
     * Upload and optimize listing image
     *
     * @param UploadedFile $file
     * @param int $listingId
     * @return array Returns paths to all image sizes
     */
    public function uploadListingImage(UploadedFile $file, int $listingId): array
    {
        // Validate file
        $this->validateImage($file);

        // Generate unique filename
        $filename = $this->generateFilename($file);
        
        // Create directories
        $basePath = "listings/{$listingId}";
        
        $paths = [];

        // Process and save each size
        foreach (self::SIZES as $size => $dimensions) {
            $path = "{$basePath}/{$size}/{$filename}";
            $this->processAndSaveImage($file, $path, $dimensions);
            $paths[$size] = $path;
        }

        // Also save original (optimized)
        $originalPath = "{$basePath}/original/{$filename}";
        $this->processAndSaveImage($file, $originalPath, ['width' => 1920, 'height' => 1920]);
        $paths['original'] = $originalPath;

        return $paths;
    }

    /**
     * Upload and optimize avatar image
     *
     * @param UploadedFile $file
     * @param int $userId
     * @return string Returns path to avatar
     */
    public function uploadAvatar(UploadedFile $file, int $userId): string
    {
        $this->validateImage($file);

        $filename = $this->generateFilename($file);
        $path = "avatars/{$userId}/{$filename}";

        // Avatars are square and smaller
        $this->processAndSaveImage($file, $path, ['width' => 400, 'height' => 400]);

        return $path;
    }

    /**
     * Delete listing images (all sizes)
     *
     * @param int $listingId
     * @param string $filename
     * @return void
     */
    public function deleteListingImage(int $listingId, string $filename): void
    {
        $basePath = "listings/{$listingId}";

        // Delete all sizes
        foreach (array_keys(self::SIZES) as $size) {
            Storage::disk('public')->delete("{$basePath}/{$size}/{$filename}");
        }

        // Delete original
        Storage::disk('public')->delete("{$basePath}/original/{$filename}");
    }

    /**
     * Delete all images for a listing
     *
     * @param int $listingId
     * @return void
     */
    public function deleteAllListingImages(int $listingId): void
    {
        Storage::disk('public')->deleteDirectory("listings/{$listingId}");
    }

    /**
     * Delete avatar
     *
     * @param int $userId
     * @param string $filename
     * @return void
     */
    public function deleteAvatar(int $userId, string $filename): void
    {
        Storage::disk('public')->delete("avatars/{$userId}/{$filename}");
    }

    /**
     * Process and save image with optimization
     *
     * @param UploadedFile $file
     * @param string $path
     * @param array $dimensions
     * @return void
     */
    private function processAndSaveImage(UploadedFile $file, string $path, array $dimensions): void
    {
        $image = Image::read($file);

        // Resize image maintaining aspect ratio
        $image->scale(
            width: $dimensions['width'],
            height: $dimensions['height']
        );

        // Optimize based on format
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (in_array($extension, ['jpg', 'jpeg'])) {
            // JPEG optimization - quality 85%
            $encoded = $image->toJpeg(quality: 85);
        } elseif ($extension === 'png') {
            // PNG optimization
            $encoded = $image->toPng();
        } elseif ($extension === 'webp') {
            // WebP optimization - quality 85%
            $encoded = $image->toWebp(quality: 85);
        } else {
            // Default to JPEG
            $encoded = $image->toJpeg(quality: 85);
        }

        // Save to storage
        Storage::disk('public')->put($path, $encoded);
    }

    /**
     * Validate uploaded image
     *
     * @param UploadedFile $file
     * @return void
     * @throws \Exception
     */
    private function validateImage(UploadedFile $file): void
    {
        // Check if file exists
        if (!$file->isValid()) {
            throw new \Exception('Nevažeća datoteka.');
        }

        // Check file size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \Exception('Slika je prevelika. Maksimalna veličina je 5MB.');
        }

        // Check mime type
        if (!in_array($file->getMimeType(), self::ALLOWED_TYPES)) {
            throw new \Exception('Nevažeći tip slike. Dozvoljeni su: JPG, PNG, WEBP.');
        }
    }

    /**
     * Generate unique filename
     *
     * @param UploadedFile $file
     * @return string
     */
    private function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        return uniqid() . '_' . time() . '.' . $extension;
    }

    /**
     * Get image URL for specific size
     *
     * @param string $path
     * @param string $size
     * @return string
     */
    public function getImageUrl(string $path, string $size = 'medium'): string
    {
        // Extract filename from path
        $filename = basename($path);
        $listingId = explode('/', $path)[1] ?? '';

        $sizePath = "listings/{$listingId}/{$size}/{$filename}";

        if (Storage::disk('public')->exists($sizePath)) {
            return Storage::url($sizePath);
        }

        // Fallback to original
        return Storage::url($path);
    }

    /**
     * Convert existing images to optimized versions (for migration)
     *
     * @param string $oldPath
     * @param int $listingId
     * @return array
     */
    public function migrateExistingImage(string $oldPath, int $listingId): array
    {
        if (!Storage::disk('public')->exists($oldPath)) {
            throw new \Exception("Image not found: {$oldPath}");
        }

        // Read existing image
        $imageContent = Storage::disk('public')->get($oldPath);
        $image = Image::read($imageContent);

        // Get original filename
        $originalFilename = basename($oldPath);
        $basePath = "listings/{$listingId}";
        
        $paths = [];

        // Process and save each size
        foreach (self::SIZES as $size => $dimensions) {
            $path = "{$basePath}/{$size}/{$originalFilename}";
            
            $resized = clone $image;
            $resized->scale(
                width: $dimensions['width'],
                height: $dimensions['height']
            );
            
            Storage::disk('public')->put($path, $resized->toJpeg(quality: 85));
            $paths[$size] = $path;
        }

        // Move original to new location
        $newOriginalPath = "{$basePath}/original/{$originalFilename}";
        Storage::disk('public')->move($oldPath, $newOriginalPath);
        $paths['original'] = $newOriginalPath;

        return $paths;
    }
}