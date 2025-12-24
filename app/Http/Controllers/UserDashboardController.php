<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    /**
     * Show user dashboard
     * 
     * Route: GET /dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // Get statistics
        $stats = [
            'total_listings' => $user->listings()->count(),
            'active_listings' => $user->listings()->where('status', 'active')->count(),
            'pending_listings' => $user->listings()->where('status', 'pending')->count(),
            'total_views' => $user->listings()->sum('views'),
            'favorites_count' => $user->favorites()->count(),
        ];

        // Get recent listings
        $recentListings = $user->listings()
                              ->with(['category', 'primaryImage'])
                              ->latest()
                              ->limit(5)
                              ->get();

        return view('dashboard.index', compact('stats', 'recentListings'));
    }

    /**
     * Show all user's listings
     * 
     * Route: GET /my-listings
     */
    public function myListings(Request $request)
    {
        $query = Auth::user()->listings()
                    ->with(['category', 'primaryImage']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sort
        $sortBy = $request->get('sort', 'newest');
        switch ($sortBy) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'views':
                $query->orderBy('views', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $listings = $query->paginate(12);

        return view('dashboard.my-listings', compact('listings'));
    }

    /**
     * Show user's favorite listings
     * 
     * Route: GET /favorites
     */
    public function favorites()
    {
        $favorites = Auth::user()
                        ->favorites()
                        ->with(['category', 'primaryImage', 'user'])
                        ->where('status', 'active')
                        ->whereNotNull('published_at')
                        ->where('published_at', '<=', now())
                        ->paginate(12);

        return view('dashboard.favorites', compact('favorites'));
    }

    /**
 * Toggle favorite status for a listing
 * 
 * Route: POST /listings/{id}/favorite
 */
public function toggleFavorite($id)
{
    $user = Auth::user();
    $listing = Listing::findOrFail($id);

    // Check if already favorited
    $isFavorited = $user->favorites()->where('listing_id', $id)->exists();
    
    if ($isFavorited) {
        // Remove from favorites
        $user->favorites()->detach($id);
        $message = 'Uklonjeno iz omiljenih';
        $favorited = false;
    } else {
        // Add to favorites
        $user->favorites()->attach($id);
        $message = 'Dodato u omiljene';
        $favorited = true;
    }

    // Always return JSON for AJAX requests
    return response()->json([
        'success' => true,
        'favorited' => $favorited,
        'message' => $message,
        'favorites_count' => $user->favorites()->count()
    ]);
}

    /**
     * Show user profile edit form
     * 
     * Route: GET /profile/edit
     */
    public function editProfile()
    {
        $user = Auth::user();
        return view('dashboard.edit-profile', compact('user'));
    }

    /**
     * Update user profile
     * 
     * Route: PUT /profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                \Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $user->update($validated);

        return back()->with('success', 'Profil uspešno ažuriran!');
    }

    /**
     * Change user password
     * 
     * Route: PUT /profile/password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Check if current password is correct
        if (!\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Trenutna lozinka nije ispravna']);
        }

        // Update password
        $user->update([
            'password' => \Hash::make($request->password)
        ]);

        return back()->with('success', 'Lozinka uspešno promenjena!');
    }
}