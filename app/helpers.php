<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('listing_image')) {
    /**
     * Get optimized listing image URL
     *
     * @param string|null $path Original image path
     * @param string $size Size: thumbnail, medium, large, original
     * @param string|null $default Default image if path is null
     * @return string
     */
    function listing_image(?string $path, string $size = 'medium', ?string $default = null): string
    {
        if (!$path) {
            return $default ?? asset('images/no-image.jpg');
        }

        // Extract listing ID and filename from path
        $parts = explode('/', $path);
        
        if (count($parts) < 3) {
            // Old format or invalid path, return as is
            return Storage::url($path);
        }

        $listingId = $parts[1] ?? '';
        $filename = basename($path);

        // Build path to sized image
        $sizePath = "listings/{$listingId}/{$size}/{$filename}";

        // Check if sized image exists
        if (Storage::disk('public')->exists($sizePath)) {
            return Storage::url($sizePath);
        }

        // Fallback to original
        if (Storage::disk('public')->exists($path)) {
            return Storage::url($path);
        }

        // Return default
        return $default ?? asset('images/no-image.jpg');
    }
}

if (!function_exists('avatar_image')) {
    /**
     * Get user avatar image URL
     *
     * @param string|null $path Avatar path
     * @param string|null $default Default avatar if path is null
     * @return string
     */
    function avatar_image(?string $path, ?string $default = null): string
    {
        if (!$path) {
            return $default ?? asset('images/default-avatar.jpg');
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::url($path);
        }

        return $default ?? asset('images/default-avatar.jpg');
    }
}

if (!function_exists('format_price')) {
    /**
     * Format price for display
     *
     * @param float $price
     * @return string
     */
    function format_price(float $price): string
    {
        return number_format($price, 0, ',', '.') . ' RSD';
    }
}

if (!function_exists('format_date')) {
    /**
     * Format date in Serbian format
     *
     * @param string|\DateTime $date
     * @return string
     */
    function format_date($date): string
    {
        if (is_string($date)) {
            $date = new \DateTime($date);
        }

        return $date->format('d.m.Y');
    }
}

if (!function_exists('time_ago')) {
    /**
     * Get human-readable time ago
     *
     * @param string|\DateTime $date
     * @return string
     */
    function time_ago($date): string
    {
        if (is_string($date)) {
            $date = new \DateTime($date);
        }

        $now = new \DateTime();
        $diff = $now->diff($date);

        if ($diff->y > 0) {
            return $diff->y === 1 ? 'Pre godinu dana' : "Pre {$diff->y} godina";
        } elseif ($diff->m > 0) {
            return $diff->m === 1 ? 'Pre mesec dana' : "Pre {$diff->m} meseci";
        } elseif ($diff->d > 0) {
            return $diff->d === 1 ? 'Juče' : "Pre {$diff->d} dana";
        } elseif ($diff->h > 0) {
            return $diff->h === 1 ? 'Pre sat vremena' : "Pre {$diff->h} sati";
        } elseif ($diff->i > 0) {
            return $diff->i === 1 ? 'Pre minut' : "Pre {$diff->i} minuta";
        } else {
            return 'Upravo sada';
        }
    }
}

if (!function_exists('serbian_cities')) {
    /**
     * Get list of major Serbian cities
     *
     * @return array
     */
    function serbian_cities(): array
    {
        return [
            'Beograd',
            'Novi Sad',
            'Niš',
            'Kragujevac',
            'Subotica',
            'Zrenjanin',
            'Pančevo',
            'Čačak',
            'Kruševac',
            'Kraljevo',
            'Novi Pazar',
            'Smederevo',
            'Leskovac',
            'Užice',
            'Vranje',
            'Šabac',
            'Valjevo',
            'Kruševac',
            'Požarevac',
            'Pirot',
            'Zaječar',
            'Kikinda',
            'Sombor',
            'Jagodina',
            'Vršac',
            'Bor',
            'Prokuplje',
            'Sremska Mitrovica',
            'Inđija',
            'Ruma',
        ];
    }
}