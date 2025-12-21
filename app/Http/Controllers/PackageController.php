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


    /**
     * Show promotion/upgrade options for a listing
     * 
     * Route: GET /listings/{listing}/promote
     */
    public function showPromoteOptions($listingId)
    {
        $listing = Listing::with('package')->findOrFail($listingId);

        // Check if user owns this listing
        if (Auth::id() !== $listing->user_id) {
            abort(403, 'Nemate dozvolu da pristupite ovom oglasu.');
        }

        // Get current package value (for comparison)
        $currentPackageValue = 0;
        if ($listing->package_id) {
            $currentPackageValue = $this->calculatePackageValue($listing->package);
        }

        // Get all active packages
        $allPackages = Package::where('is_active', true)->orderBy('type')->orderBy('order')->get();

        // Filter packages to show only upgrades
        $topPackages = $allPackages->where('type', 'top')->filter(function($package) use ($currentPackageValue) {
            return $this->calculatePackageValue($package) > $currentPackageValue;
        });

        $featuredPackages = $allPackages->where('type', 'featured')->filter(function($package) use ($currentPackageValue) {
            return $this->calculatePackageValue($package) > $currentPackageValue;
        });

        return view('listings.promote', compact('listing', 'topPackages', 'featuredPackages'));
    }

    /**
     * Apply promotion/upgrade to listing
     * 
     * Route: POST /listings/{listing}/apply-promotion
     */
    public function applyPromotion(Request $request, $listingId)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
        ]);

        $listing = Listing::findOrFail($listingId);

        // Check if user owns this listing
        if (Auth::id() !== $listing->user_id) {
            abort(403);
        }

        $newPackage = Package::findOrFail($validated['package_id']);

        // Verify it's actually an upgrade
        $currentValue = $listing->package_id ? $this->calculatePackageValue($listing->package) : 0;
        $newValue = $this->calculatePackageValue($newPackage);

        if ($newValue <= $currentValue) {
            return back()->withErrors('Možete samo nadograditi na viši paket!');
        }

        // Calculate expiration date
        $expiresAt = now()->addDays($newPackage->duration_days);

        // Update listing with new package
        $updateData = [
            'package_id' => $newPackage->id,
            'promoted_at' => now(),
        ];

        if ($newPackage->type === 'top') {
            $updateData['is_top'] = true;
            $updateData['top_until'] = $expiresAt;
        } elseif ($newPackage->type === 'featured') {
            $updateData['is_featured'] = true;
            $updateData['featured_until'] = $expiresAt;
        }

        $listing->update($updateData);

        // In a real application, redirect to payment here
        // For now, we'll just activate it
        
        return redirect()
            ->route('dashboard.my-listings')
            ->with('success', 'Oglas je uspešno nadograđen! Paket: ' . $newPackage->name);
    }

    /**
     * Calculate package value for comparison
     * Higher value = better package
     */
    private function calculatePackageValue($package)
    {
        if (!$package) return 0;
        
        // Featured packages are worth more than top packages
        $typeMultiplier = $package->type === 'featured' ? 2 : 1;
        
        // Calculate value: (price * duration * type multiplier)
        return $package->price * $package->duration_days * $typeMultiplier;
    }
}