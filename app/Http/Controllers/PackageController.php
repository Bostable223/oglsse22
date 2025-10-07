<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    /**
     * Show package selection page
     * 
     * Route: GET /listings/{listing}/select-package
     */
    public function selectPackage($listingId)
    {
        $listing = Listing::findOrFail($listingId);

        // Check if user owns this listing
        if (Auth::id() !== $listing->user_id) {
            abort(403, 'Nemate dozvolu da pristupite ovom oglasu.');
        }

        // Get all active packages grouped by type
        $topPackages = Package::where('type', 'top')
                              ->where('is_active', true)
                              ->orderBy('order')
                              ->get();

        $featuredPackages = Package::where('type', 'featured')
                                   ->where('is_active', true)
                                   ->orderBy('order')
                                   ->get();

        return view('packages.select', compact('listing', 'topPackages', 'featuredPackages'));
    }

    /**
     * Apply package to listing
     * 
     * Route: POST /listings/{listing}/apply-package
     */
    public function applyPackage(Request $request, $listingId)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
        ]);

        $listing = Listing::findOrFail($listingId);

        // Check if user owns this listing
        if (Auth::id() !== $listing->user_id) {
            abort(403);
        }

        $package = Package::findOrFail($validated['package_id']);

        // Calculate expiration date based on package duration
        $expiresAt = now()->addDays($package->duration_days);

        // Update listing with package
        $listing->update([
            'package_id' => $package->id,
            'is_featured' => $package->type === 'featured',
            'featured_until' => $package->type === 'featured' ? $expiresAt : null,
            'expires_at' => $expiresAt,
            'status' => 'pending', // Admin needs to approve after payment
        ]);

        // In a real application, you would:
        // 1. Redirect to payment gateway
        // 2. Process payment
        // 3. Then activate the listing

        return redirect()
            ->route('dashboard.my-listings')
            ->with('success', 'Paket je uspešno izabran! Oglas će biti aktiviran nakon odobrenja.');
    }

    /**
     * Skip package selection (free listing)
     * 
     * Route: POST /listings/{listing}/skip-package
     */
    public function skipPackage($listingId)
    {
        $listing = Listing::findOrFail($listingId);

        // Check if user owns this listing
        if (Auth::id() !== $listing->user_id) {
            abort(403);
        }

        // Set as basic free listing
        $listing->update([
            'package_id' => null,
            'is_featured' => false,
            'featured_until' => null,
            'expires_at' => now()->addDays(30), // Free listings last 30 days
            'status' => 'pending', // Needs admin approval
        ]);

        return redirect()
            ->route('dashboard.my-listings')
            ->with('success', 'Oglas je poslat na odobrenje!');
    }
}