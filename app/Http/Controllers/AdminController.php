<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Show admin dashboard
     * 
     * Route: GET /admin
     */
    public function index()
    {
        // Get statistics
        $stats = [
            'total_listings' => Listing::count(),
            'active_listings' => Listing::where('status', 'active')->count(),
            'pending_listings' => Listing::where('status', 'pending')->count(),
            'total_users' => User::where('role', 'user')->count(),
            'total_views' => Listing::sum('views'),
            'featured_listings' => Listing::where('is_featured', true)->count(),
        ];

        // Get recent pending listings
        $pendingListings = Listing::with(['user', 'category'])
                                  ->where('status', 'pending')
                                  ->latest()
                                  ->limit(10)
                                  ->get();

        // Get recent users
        $recentUsers = User::where('role', 'user')
                          ->latest()
                          ->limit(5)
                          ->get();

        return view('admin.dashboard', compact('stats', 'pendingListings', 'recentUsers'));
    }

    /**
     * Show all listings for admin management
     * 
     * Route: GET /admin/listings
     */
    public function listings(Request $request)
    {
        $query = Listing::with(['user', 'category', 'primaryImage']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Search
        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                  ->orWhere('id', $keyword)
                  ->orWhereHas('user', function($q) use ($keyword) {
                      $q->where('name', 'like', '%' . $keyword . '%')
                        ->orWhere('email', 'like', '%' . $keyword . '%');
                  });
            });
        }

        $listings = $query->latest()->paginate(20);
        $categories = Category::all();

        return view('admin.listings', compact('listings', 'categories'));
    }

    /**
     * Approve a pending listing
     * 
     * Route: POST /admin/listings/{id}/approve
     */
    public function approveListing($id)
    {
        $listing = Listing::findOrFail($id);
        
        $listing->update([
            'status' => 'active',
            'published_at' => now(),
        ]);

        return back()->with('success', 'Oglas je odobren!');
    }

    /**
     * Reject a listing
     * 
     * Route: POST /admin/listings/{id}/reject
     */
    public function rejectListing(Request $request, $id)
    {
        $listing = Listing::findOrFail($id);
        
        $listing->update([
            'status' => 'rejected',
        ]);

        // You could send notification to user here

        return back()->with('success', 'Oglas je odbijen!');
    }

    /**
     * Toggle featured status
     * 
     * Route: POST /admin/listings/{id}/toggle-featured
     */
    public function toggleFeatured(Request $request, $id)
    {
        $listing = Listing::findOrFail($id);
        
        $listing->update([
            'is_featured' => !$listing->is_featured,
            'featured_until' => $listing->is_featured ? null : now()->addDays(30),
        ]);

        $message = $listing->is_featured ? 'Oglas je istaknut!' : 'Oglas više nije istaknut!';
        
        return back()->with('success', $message);
    }

    /**
     * Delete a listing permanently
     * 
     * Route: DELETE /admin/listings/{id}
     */
    public function deleteListing($id)
    {
        $listing = Listing::findOrFail($id);
        $listing->delete();

        return back()->with('success', 'Oglas je obrisan!');
    }

    /**
     * Show all users
     * 
     * Route: GET /admin/users
     */
    public function users(Request $request)
    {
        $query = User::withCount('listings');

        // Search
        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                  ->orWhere('email', 'like', '%' . $keyword . '%')
                  ->orWhere('phone', 'like', '%' . $keyword . '%');
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(20);

        return view('admin.users', compact('users'));
    }

    /**
     * Toggle user active status (ban/unban)
     * 
     * Route: POST /admin/users/{id}/toggle-active
     */
    public function toggleUserActive($id)
    {
        $user = User::findOrFail($id);
        
        // Can't ban yourself or other admins
        if ($user->isAdmin()) {
            return back()->withErrors('Ne možete deaktivirati administratora!');
        }

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $message = $user->is_active ? 'Korisnik je aktiviran!' : 'Korisnik je deaktiviran!';
        
        return back()->with('success', $message);
    }

    /**
     * Delete a user
     * 
     * Route: DELETE /admin/users/{id}
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        // Can't delete yourself or other admins
        if ($user->isAdmin()) {
            return back()->withErrors('Ne možete obrisati administratora!');
        }

        $user->delete();

        return back()->with('success', 'Korisnik je obrisan!');
    }

    /**
     * Show all categories
     * 
     * Route: GET /admin/categories
     */
    public function categories()
    {
        $categories = Category::withCount('listings')->get();
        return view('admin.categories', compact('categories'));
    }

    /**
     * Store new category
     * 
     * Route: POST /admin/categories
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'order' => 'nullable|integer',
        ]);

        $validated['slug'] = \Str::slug($validated['name']);
        $validated['is_active'] = true;

        Category::create($validated);

        return back()->with('success', 'Kategorija je uspešno kreirana!');
    }

    /**
     * Update category
     * 
     * Route: PUT /admin/categories/{id}
     */
    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = \Str::slug($validated['name']);

        $category->update($validated);

        return back()->with('success', 'Kategorija je uspešno ažurirana!');
    }

    /**
     * Delete category
     * 
     * Route: DELETE /admin/categories/{id}
     */
    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        
        // Check if category has listings
        if ($category->listings()->count() > 0) {
            return back()->withErrors('Ne možete obrisati kategoriju koja ima oglase!');
        }

        $category->delete();

        return back()->with('success', 'Kategorija je obrisana!');
    }
}