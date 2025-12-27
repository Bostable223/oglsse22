<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\User;
use App\Models\Category;
use App\Models\Package;
use Illuminate\Http\Request;
use App\Services\NotificationService;

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

    // Add to constructor or use dependency injection
        protected NotificationService $notificationService;

        public function __construct(NotificationService $notificationService)
        {
            $this->notificationService = $notificationService;
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

    // Send notification
    $this->notificationService->listingApproved($listing);

    return back()->with('success', 'Oglas je odobren i korisnik je obavešten!');
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

    // Send notification with optional reason
    $reason = $request->input('reason');
    $this->notificationService->listingRejected($listing, $reason);

    return back()->with('success', 'Oglas je odbijen i korisnik je obavešten!');
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
 * Reset user password
 * 
 * Route: POST /admin/users/{id}/reset-password
 */
public function resetUserPassword($id)
{
    $user = User::findOrFail($id);
    
    // Can't reset admin passwords (security measure)
    if ($user->isAdmin()) {
        return back()->withErrors('Ne možete resetovati administratorsku lozinku!');
    }
    
    // Generate a random secure password
    $newPassword = \Str::random(12);
    
    // Update user password
    $user->update([
        'password' => \Hash::make($newPassword),
    ]);
    
    // Try to send email notification
    try {
        // Send email to user with new password
        \Mail::to($user->email)->send(new \App\Mail\PasswordResetByAdmin($user, $newPassword));
        
        return back()->with('success', 
            "Lozinka je resetovana i poslata korisniku na email: {$user->email}"
        );
    } catch (\Exception $e) {
        // If email fails, show password to admin
        \Log::error('Failed to send password reset email: ' . $e->getMessage());
        
        return back()->with('success', 
            "Lozinka je resetovana! Nova lozinka: <strong>{$newPassword}</strong><br>
            <small class='text-gray-600'>Email nije mogao biti poslan. Molimo kopiraćte lozinku i pošaljite korisniku ručno.</small>"
        );
    }
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

    // app/Http/Controllers/AdminController.php

/**
 * Show all packages
 * 
 * Route: GET /admin/packages
 */
public function packages()
{
    $packages = Package::withCount('listings')
                       ->orderBy('type')
                       ->orderBy('order')
                       ->get();
    
    return view('admin.packages', compact('packages'));
}

/**
 * Store new package
 * 
 * Route: POST /admin/packages
 */
public function storePackage(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'type' => 'required|in:top,featured',
        'duration_days' => 'required|integer|min:1|max:365',
        'price' => 'required|numeric|min:0',
        'currency' => 'required|in:RSD,EUR,USD',
        'order' => 'nullable|integer',
        'features' => 'nullable|array',
        'features.*' => 'string',
    ]);

    $validated['slug'] = \Str::slug($validated['name']) . '-' . \Str::random(6);
    $validated['is_active'] = true;

    Package::create($validated);

    return back()->with('success', 'Paket je uspešno kreiran!');
}

/**
 * Update package
 * 
 * Route: PUT /admin/packages/{id}
 */
public function updatePackage(Request $request, $id)
{
    $package = Package::findOrFail($id);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'type' => 'required|in:top,featured',
        'duration_days' => 'required|integer|min:1|max:365',
        'price' => 'required|numeric|min:0',
        'currency' => 'required|in:RSD,EUR,USD',
        'order' => 'nullable|integer',
        'is_active' => 'boolean',
        'features' => 'nullable|array',
        'features.*' => 'string',
    ]);

    // Keep the same slug unless name changed significantly
    if ($validated['name'] !== $package->name) {
        $validated['slug'] = \Str::slug($validated['name']) . '-' . \Str::random(6);
    }

    $package->update($validated);

    return back()->with('success', 'Paket je uspešno ažuriran!');
}

/**
 * Toggle package active status
 * 
 * Route: POST /admin/packages/{id}/toggle-active
 */
public function togglePackageActive($id)
{
    $package = Package::findOrFail($id);
    
    $package->update([
        'is_active' => !$package->is_active,
    ]);

    $message = $package->is_active ? 'Paket je aktiviran!' : 'Paket je deaktiviran!';
    
    return back()->with('success', $message);
}

/**
 * Delete package
 * 
 * Route: DELETE /admin/packages/{id}
 */
public function deletePackage($id)
{
    $package = Package::findOrFail($id);
    
    // Check if package is being used by any listings
    if ($package->listings()->count() > 0) {
        return back()->withErrors('Ne možete obrisati paket koji se koristi u oglasima!');
    }

    $package->delete();

    return back()->with('success', 'Paket je obrisan!');
}

/**
 * Show edit user form
 * 
 * Route: GET /admin/users/{id}/edit
 */
public function editUser($id)
{
    $user = User::withCount('listings')->findOrFail($id);
    
    // Can't edit admin users (security)
    if ($user->isAdmin()) {
        return back()->withErrors('Ne možete izmeniti administratorski nalog!');
    }
    
    return view('admin.users-edit', compact('user'));
}

/**
 * Update user information
 * 
 * Route: PUT /admin/users/{id}
 */
public function updateUser(Request $request, $id)
{
    $user = User::findOrFail($id);
    
    // Can't edit admin users
    if ($user->isAdmin()) {
        return back()->withErrors('Ne možete izmeniti administratorski nalog!');
    }
    
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id,
        'phone' => 'nullable|string|max:50',
        'city' => 'nullable|string|max:100',
        'bio' => 'nullable|string|max:500',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
    ]);
    
    $user->update($validated);
    
    return redirect()
        ->route('admin.users')
        ->with('success', 'Korisnik je uspešno ažuriran!');
}

/**
 * Show package analytics
 * 
 * Route: GET /admin/packages/analytics
 */
public function packageAnalytics(Request $request)
{
    try {
        // Get date range from request or default to last 30 days
        $startDate = $request->input('start_date', now()->subDays(30)->startOfDay());
        $endDate = $request->input('end_date', now()->endOfDay());
        
        // Convert to Carbon instances if they're strings
        if (is_string($startDate)) {
            $startDate = \Carbon\Carbon::parse($startDate)->startOfDay();
        }
        if (is_string($endDate)) {
            $endDate = \Carbon\Carbon::parse($endDate)->endOfDay();
        }

        // Get all packages with usage statistics (filtered by date range)
        $packages = Package::withCount(['listings' => function($query) use ($startDate, $endDate) {
            $query->whereNotNull('promoted_at')
                  ->whereBetween('promoted_at', [$startDate, $endDate]);
        }])->orderBy('type')->orderBy('order')->get();

        // Active promotions count (current, not filtered by date range)
        $activePromotions = [
            'top' => Listing::where('is_top', true)
                           ->where(function($q) {
                               $q->whereNull('top_until')
                                 ->orWhere('top_until', '>', now());
                           })
                           ->count(),
            'featured' => Listing::where('is_featured', true)
                                ->where(function($q) {
                                    $q->whereNull('featured_until')
                                      ->orWhere('featured_until', '>', now());
                                })
                                ->count(),
        ];

        // Revenue by package (filtered by date range)
        $revenueByPackage = collect();
        foreach ($packages as $package) {
            $revenueByPackage->push([
                'package_name' => $package->name,
                'total_revenue' => $package->price * $package->listings_count,
                'usage_count' => $package->listings_count,
            ]);
        }

        // Total revenue and sales (filtered)
        $totalRevenue = $revenueByPackage->sum('total_revenue');
        $totalSales = $revenueByPackage->sum('usage_count');

        // Package usage by type (filtered by date range)
        $usageByType = [
            'top' => Package::where('type', 'top')
                           ->withCount(['listings' => function($query) use ($startDate, $endDate) {
                               $query->whereNotNull('promoted_at')
                                     ->whereBetween('promoted_at', [$startDate, $endDate]);
                           }])
                           ->get()
                           ->sum('listings_count'),
            'featured' => Package::where('type', 'featured')
                                ->withCount(['listings' => function($query) use ($startDate, $endDate) {
                                    $query->whereNotNull('promoted_at')
                                          ->whereBetween('promoted_at', [$startDate, $endDate]);
                                }])
                                ->get()
                                ->sum('listings_count'),
            'free' => Listing::whereNull('package_id')
                            ->whereBetween('created_at', [$startDate, $endDate])
                            ->count(),
        ];

        // Most popular packages (top 5, filtered by date range)
        $popularPackages = Package::withCount(['listings' => function($query) use ($startDate, $endDate) {
            $query->whereNotNull('promoted_at')
                  ->whereBetween('promoted_at', [$startDate, $endDate]);
        }])
        ->orderBy('listings_count', 'desc')
        ->limit(5)
        ->get();

        // Recent package purchases (last 10, within date range)
        $recentPurchases = Listing::whereNotNull('package_id')
                                  ->whereNotNull('promoted_at')
                                  ->whereBetween('promoted_at', [$startDate, $endDate])
                                  ->with(['package', 'user'])
                                  ->orderBy('promoted_at', 'desc')
                                  ->limit(10)
                                  ->get();

        // Calculate number of months/days to show in trend
        // 1. Calculate number of days to determine grouping
        $daysDiff = $startDate->diffInDays($endDate);

        // 2. OPTIMIZATION: Fetch ALL data in ONE query (Fixes N+1)
        // We eager load 'package' so we don't run a query for every single price calculation
        $allPeriodListings = Listing::whereNotNull('package_id')
                                  ->whereNotNull('promoted_at')
                                  ->whereBetween('promoted_at', [$startDate, $endDate])
                                  ->with('package') 
                                  ->get();

        $monthlyTrend = [];

        if ($daysDiff <= 31) {
            // Group by day in memory using the collection
            $groupedByDay = $allPeriodListings->groupBy(function($item) {
                return $item->promoted_at->format('Y-m-d');
            });

for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
                $dateKey = $date->format('Y-m-d');
                // FIXED: Use the collection from memory, do not query DB again
                $dayListings = $groupedByDay->get($dateKey, collect()); 
                
                $revenue = $dayListings->sum(function($listing) {
                    return $listing->package ? $listing->package->price : 0;
                });
                
                $monthlyTrend[] = [
                    'month' => $date->format('d M'),
                    'count' => $dayListings->count(),
                    'revenue' => $revenue,
                ];
            }
        } else {
            // Group by month in memory using the collection
            $groupedByMonth = $allPeriodListings->groupBy(function($item) {
                return $item->promoted_at->format('Y-m');
            });

            $currentDate = $startDate->copy()->startOfMonth();
            $endDateMonth = $endDate->copy()->endOfMonth();
            
            while ($currentDate <= $endDateMonth) {
                $monthKey = $currentDate->format('Y-m');
                // FIXED: Use the collection from memory
                $monthListings = $groupedByMonth->get($monthKey, collect());
                
                $revenue = $monthListings->sum(function($listing) {
                    return $listing->package ? $listing->package->price : 0;
                });
                
                $monthlyTrend[] = [
                    'month' => $currentDate->format('M Y'),
                    'count' => $monthListings->count(),
                    'revenue' => $revenue,
                ];
                $currentDate->addMonth();
            }
        }

        return view('admin.package-analytics', compact(
            'packages',
            'activePromotions',
            'revenueByPackage',
            'totalRevenue',
            'totalSales',
            'usageByType',
            'popularPackages',
            'recentPurchases',
            'monthlyTrend',
            'startDate',
            'endDate'
        ));
        
    } catch (\Exception $e) {
        \Log::error('Package Analytics Error: ' . $e->getMessage());
        return back()->withErrors('Greška pri učitavanju analitike: ' . $e->getMessage());
    }
}

}