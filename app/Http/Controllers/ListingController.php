<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Package;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ListingController extends Controller
{
    /**
     * Display the homepage with hero section
     * 
     * Route: GET /
     */
    public function home()
{
    // Get featured listings
    $featuredListings = Listing::with(['category', 'primaryImage'])
                               ->where('status', 'active')
                               ->whereNotNull('published_at')
                               ->where('published_at', '<=', now())
                               ->where('is_featured', true)
                               ->where(function($q) {
                                   $q->whereNull('featured_until')
                                     ->orWhere('featured_until', '>', now());
                               })
                               ->limit(8)
                               ->get();

    // Get new listings (most recent)
    $newListings = Listing::with(['category', 'primaryImage'])
                          ->where('status', 'active')
                          ->whereNotNull('published_at')
                          ->where('published_at', '<=', now())
                          ->orderBy('published_at', 'desc')
                          ->limit(8)
                          ->get();

    // Get all categories
    $categories = Cache::remember('active_categories', 60*60*24, function () {
        return Category::where('is_active', true)->orderBy('order')->withCount('listings')->get();
    });

    // Get statistics for hero
    $stats = Cache::remember('homepage_stats', 60*30, function () {
    return [
        'total_listings' => Listing::where('status', 'active')->count(),
        'active_users' => User::where('role', 'user')->count(),
        'cities' => Listing::where('status', 'active')->distinct('city')->count('city'),
    ];
    });

    // Get all unique locations (FIXED VERSION)
// Concatenates City and Municipality at the database level
        $allLocations = Listing::where('status', 'active')
            ->selectRaw("DISTINCT CASE 
                WHEN municipality IS NOT NULL AND municipality != '' 
                THEN CONCAT(city, ', ', municipality) 
                ELSE city 
            END as location")
            ->pluck('location')
            ->sort()
            ->values();

    return view('home', compact('featuredListings', 'newListings', 'categories', 'stats', 'allLocations'));
}

    /**
     * Display a listing of all properties (search results page)
     * 
     * Route: GET /listings
     */
    public function index(Request $request)
{
    $query = Listing::with(['category', 'primaryImage', 'user'])
        ->where('status', 'active')
        ->whereNotNull('published_at')
        ->where('published_at', '<=', now());

    // Apply filters
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }

    if ($request->filled('city')) {
        $location = $request->city;
        $parts = array_map('trim', explode(',', $location));
        
        if (count($parts) > 1) {
            $city = $parts[0];
            $municipality = $parts[1];
            $query->where(function($q) use ($city, $municipality) {
                $q->where('city', 'like', '%' . $city . '%')
                  ->where('municipality', 'like', '%' . $municipality . '%');
            });
        } else {
            $query->where(function($q) use ($location) {
                $q->where('city', 'like', '%' . $location . '%')
                  ->orWhere('municipality', 'like', '%' . $location . '%')
                  ->orWhere('address', 'like', '%' . $location . '%');
            });
        }
    }

    if ($request->filled('listing_type')) {
        $query->where('listing_type', $request->listing_type);
    }

    if ($request->filled('price_min')) {
        $query->where('price', '>=', $request->price_min);
    }

    if ($request->filled('price_max')) {
        $query->where('price', '<=', $request->price_max);
    }

    if ($request->filled('area_min')) {
        $query->where('area', '>=', $request->area_min);
    }

    if ($request->filled('area_max')) {
        $query->where('area', '<=', $request->area_max);
    }

    if ($request->filled('rooms')) {
        $query->where('rooms', $request->rooms);
    }

    if ($request->filled('floor')) {
        $floor = $request->floor;
        if ($floor === '7+') {
            $query->where('floor', '>=', 7);
        } elseif (str_contains($floor, '-')) {
            [$min, $max] = explode('-', $floor);
            $query->whereBetween('floor', [(int)$min, (int)$max]);
        } else {
            $query->where('floor', $floor);
        }
    }

    // Features filter - FIXED
    if ($request->filled('features')) {
        $features = is_array($request->features) ? $request->features : [$request->features];
        
        foreach ($features as $feature) {
            $query->whereJsonContains('features', $feature);
        }
    }

    // Sorting
    $sortBy = $request->get('sort', 'newest');
    switch ($sortBy) {
        case 'price_asc':
            $query->orderBy('price', 'asc');
            break;
        case 'price_desc':
            $query->orderBy('price', 'desc');
            break;
        case 'area_desc':
            $query->orderBy('area', 'desc');
            break;
        case 'oldest':
            $query->orderBy('published_at', 'asc');
            break;
        case 'newest':
        default:
            $query->orderByDesc('is_top')
                  ->orderByDesc('is_featured')
                  ->orderBy('published_at', 'desc');
            break;
    }

    $listings = $query->paginate(15)->withQueryString();
    $categories = Category::where('is_active', true)->orderBy('order')->get();
    $selectedCategory = $request->filled('category') ? $categories->find($request->category) : null;
    
    // Get all unique locations (FIXED VERSION)
// Concatenates City and Municipality at the database level
        $allLocations = Listing::where('status', 'active')
            ->selectRaw("DISTINCT CASE 
                WHEN municipality IS NOT NULL AND municipality != '' 
                THEN CONCAT(city, ', ', municipality) 
                ELSE city 
            END as location")
            ->pluck('location')
            ->sort()
            ->values();

    return view('listings.index', compact('listings', 'categories', 'allLocations', 'selectedCategory'));
}

    /**
     * Show the form for creating a new listing
     * 
     * Route: GET /listings/create
     */
    public function create()
    {
        // Get all active categories
        $categories = Category::where('is_active', true)
                              ->orderBy('order')
                              ->get();

        return view('listings.create', compact('categories'));
    }

    /**
     * Store a newly created listing in database
     * 
     * Route: POST /listings
     */
    public function store(Request $request)
    {
        // Validate the incoming data
        $validated = $request->validate([
    'category_id' => 'required|exists:categories,id',
    'title' => 'required|string|max:255',
    'description' => 'required|string|min:50',
    'price' => 'required|numeric|min:0',
    'currency' => 'required|in:RSD,EUR,USD',
    'city' => 'required|string|max:100',
    'municipality' => 'nullable|string|max:100',
    'address' => 'nullable|string|max:255',
    'area' => 'nullable|numeric|min:0',
    'rooms' => 'nullable|integer|min:0',
    'bathrooms' => 'nullable|integer|min:0',
    'floor' => 'nullable|integer',
    'total_floors' => 'nullable|integer|min:0',
    'year_built' => 'nullable|integer|min:1800|max:' . date('Y'),
    'listing_type' => 'required|in:sale,rent',
    'contact_name' => 'nullable|string|max:100',
    'contact_phone' => 'required|string|max:50',
    'contact_email' => 'nullable|email|max:100',
    'features' => 'nullable|array',
    'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
]);

// Remove 'images' key before storing in session
$cleanedData = collect($validated)->except('images')->toArray();

$uploadedImages = $request->file('images');
$imagePaths = [];

if ($uploadedImages) {
    foreach ($uploadedImages as $image) {
        $path = $image->store('temp-listing-images', 'public');

        $imagePaths[] = $path;
    }
}

session(['listing_draft' => $cleanedData]);
session(['listing_images' => $imagePaths]);

return redirect()->route('listings.select-package');

    }

    /**
     * Show package selection page
     * 
     * Route: GET /listings/select-package
     */
    public function selectPackage()
    {
        // Check if there's listing data in session
        if (!session()->has('listing_draft')) {
            return redirect()->route('listings.create')
                           ->with('error', 'Morate prvo popuniti formular.');
        }

        // Get all active packages, grouped by type
        $topPackages = Package::where('is_active', true)
                                          ->where('type', 'top')
                                          ->orderBy('duration_days')
                                          ->get();

        $featuredPackages = Package::where('is_active', true)
                                                ->where('type', 'featured')
                                                ->orderBy('duration_days')
                                                ->get();

        return view('listings.select-package', compact('topPackages', 'featuredPackages'));
    }

    /**
     * Store listing with selected package
     * 
     * Route: POST /listings/store-with-package
     */
    public function storeWithPackage(Request $request)
    {
        // Get listing data from session
        $listingData = session('listing_draft');
        
        if (!$listingData) {
            return redirect()->route('listings.create')
                           ->with('error', 'Sesija je istekla. Pokušajte ponovo.');
        }

        // Add user_id and create slug
        $listingData['user_id'] = Auth::id();
        $listingData['slug'] = Str::slug($listingData['title']) . '-' . Str::random(6);
        $listingData['status'] = 'pending';
        $listingData['published_at'] = now();

        // Handle package selection
        if ($request->filled('package_id')) {
            $package = Package::findOrFail($request->package_id);
            $listingData['package_id'] = $package->id;
            
            // Set promotion dates based on package type
            $expirationDate = now()->addDays($package->duration_days);
            
            if ($package->type === 'top') {
                $listingData['is_top'] = true;
                $listingData['top_until'] = $expirationDate;
            } elseif ($package->type === 'featured') {
                $listingData['is_featured'] = true;
                $listingData['featured_until'] = $expirationDate;
            }
            
            $listingData['promoted_at'] = now();
        }

        // Create the listing
        $listing = Listing::create($listingData);

        // Handle image uploads from session
        if (session()->has('listing_images')) {
            $images = session('listing_images');
            if ($images) {
                $this->handleImageUpload($images, $listing);
            }
        }

        // Clear session data
        session()->forget(['listing_draft', 'listing_images']);

        return redirect()
            ->route('listings.show', $listing->slug)
            ->with('success', 'Oglas je uspešno kreiran i čeka odobrenje!');
    }

    /**
     * Display the specified listing
     * 
     * Route: GET /listings/{slug}
     */
    public function show($slug)
    {
        // Get listing with all relationships
        $listing = Listing::with(['category', 'user', 'images'])
                         ->where('slug', $slug)
                         ->firstOrFail();

        // Check if user can view this listing
        if ($listing->status !== 'active') {
            if (!Auth::check() || (Auth::id() !== $listing->user_id && !Auth::user()->isAdmin())) {
                abort(404);
            }
        }

        // Increment view count (only if not the owner viewing)
        if (!Auth::check() || Auth::id() !== $listing->user_id) {
            $listing->incrementViews();
        }

        // Get similar listings
        $similarListings = Listing::with(['primaryImage'])
                                  ->where('status', 'active')
                                  ->whereNotNull('published_at')
                                  ->where('published_at', '<=', now())
                                  ->where('id', '!=', $listing->id)
                                  ->where('category_id', $listing->category_id)
                                  ->where('city', $listing->city)
                                  ->limit(4)
                                  ->get();

        // Check if current user has favorited this
        $isFavorited = Auth::check() ? $listing->isFavoritedBy(Auth::user()) : false;

        return view('listings.show', compact('listing', 'similarListings', 'isFavorited'));
    }

    /**
     * Show the form for editing the specified listing
     * 
     * Route: GET /listings/{slug}/edit
     */
    public function edit($slug)
    {
        $listing = Listing::where('slug', $slug)->firstOrFail();

        // Check if user owns this listing
        if (Auth::id() !== $listing->user_id && !Auth::user()->isAdmin()) {
            abort(403, 'Nemate dozvolu da izmenite ovaj oglas.');
        }

        $categories = Category::where('is_active', true)
                              ->orderBy('order')
                              ->get();

        return view('listings.edit', compact('listing', 'categories'));
    }

    /**
     * Update the specified listing in database
     * 
     * Route: PUT/PATCH /listings/{slug}
     */
    public function update(Request $request, $slug)
    {
        $listing = Listing::where('slug', $slug)->firstOrFail();

        // Check if user owns this listing
        if (Auth::id() !== $listing->user_id && !Auth::user()->isAdmin()) {
            abort(403);
        }

        // Validate
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:50',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|in:RSD,EUR,USD',
            'city' => 'required|string|max:100',
            'municipality' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'area' => 'nullable|numeric|min:0',
            'rooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'floor' => 'nullable|integer',
            'total_floors' => 'nullable|integer|min:0',
            'year_built' => 'nullable|integer|min:1800|max:' . date('Y'),
            'listing_type' => 'required|in:sale,rent',
            'contact_name' => 'nullable|string|max:100',
            'contact_phone' => 'required|string|max:50',
            'contact_email' => 'nullable|email|max:100',
            'features' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // Update slug if title changed
        if ($validated['title'] !== $listing->title) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);
        }

        // Update the listing
        $listing->update($validated);

        // Handle new image uploads
        if ($request->hasFile('images')) {
            $this->handleImageUpload($request->file('images'), $listing);
        }

        return redirect()
            ->route('listings.show', $listing->slug)
            ->with('success', 'Oglas je uspešno ažuriran!');
    }

    /**
     * Remove the specified listing from database
     * 
     * Route: DELETE /listings/{slug}
     */
    public function destroy($slug)
    {
        $listing = Listing::where('slug', $slug)->firstOrFail();

        // Check if user owns this listing
        if (Auth::id() !== $listing->user_id && !Auth::user()->isAdmin()) {
            abort(403);
        }

        // Delete the listing (soft delete)
        $listing->delete();

        return redirect()
            ->route('dashboard.my-listings')
            ->with('success', 'Oglas je uspešno obrisan!');
    }

    /**
     * Handle image upload for a listing
     */
    private function handleImageUpload($images, $listing)
{
    foreach ($images as $index => $image) {
        // Get extension from the stored path
        $originalExtension = pathinfo($image, PATHINFO_EXTENSION);
        $filename = Str::random(20) . '.' . $originalExtension;

        // Move from temp to permanent location
        $newPath = 'listings/' . $filename;
        if (\Storage::disk('public')->exists($image)) {
         \Storage::disk('public')->copy($image, $newPath);
         \Storage::disk('public')->delete($image);
}

        // Save to DB
        $listing->images()->create([
            'image_path' => $newPath,
            'order' => $index,
            'is_primary' => $index === 0,
        ]);
    }
}

}