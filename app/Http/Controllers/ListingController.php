<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Services\ImageService;

class ListingController extends Controller
{


    protected $imageService;
    
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

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
        $categories = Category::where('is_active', true)
                              ->orderBy('order')
                              ->withCount('listings')
                              ->get();

        // Get statistics for hero
        $stats = [
            'total_listings' => Listing::where('status', 'active')->count(),
            'active_users' => \App\Models\User::where('role', 'user')->count(),
            'cities' => Listing::where('status', 'active')->distinct('city')->count('city'),
        ];

        // Get all unique locations (city, municipality, address parts) for autocomplete
        $allLocations = Listing::where('status', 'active')
            ->select('city', 'municipality')
            ->get()
            ->flatMap(function($listing) {
                $locations = [];
                if ($listing->city) $locations[] = $listing->city;
                if ($listing->municipality) $locations[] = $listing->city . ', ' . $listing->municipality;
                return $locations;
            })
            ->unique()
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
    // Start with active listings query
    $query = Listing::with(['category', 'primaryImage', 'user'])
                    ->where('status', 'active')
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());

    // Apply filters from search form
    
    // Filter by category
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }

    // Filter by city/location
    if ($request->filled('city')) {
        $location = $request->city;
        
        // Split by comma if format is "City, Municipality"
        $parts = array_map('trim', explode(',', $location));
        
        if (count($parts) > 1) {
            // Format: "Beograd, Stari Grad"
            $city = $parts[0];
            $municipality = $parts[1];
            
            $query->where(function($q) use ($city, $municipality) {
                $q->where('city', 'like', '%' . $city . '%')
                  ->where('municipality', 'like', '%' . $municipality . '%');
            });
        } else {
            // Single term - search everywhere
            $query->where(function($q) use ($location) {
                $q->where('city', 'like', '%' . $location . '%')
                  ->orWhere('municipality', 'like', '%' . $location . '%')
                  ->orWhere('address', 'like', '%' . $location . '%');
            });
        }
    }

    // Filter by listing type (sale or rent)
    if ($request->filled('listing_type')) {
        $query->where('listing_type', $request->listing_type);
    }

    // Filter by price range
    if ($request->filled('price_min')) {
        $query->where('price', '>=', $request->price_min);
    }
    if ($request->filled('price_max')) {
        $query->where('price', '<=', $request->price_max);
    }

    // Filter by area (square meters)
    if ($request->filled('area_min')) {
        $query->where('area', '>=', $request->area_min);
    }
    if ($request->filled('area_max')) {
        $query->where('area', '<=', $request->area_max);
    }

    // Filter by number of rooms
    if ($request->filled('rooms')) {
        $rooms = $request->rooms;
        if ($rooms == '5') {
            $query->where('rooms', '>=', 5);
        } else {
            $query->where('rooms', $rooms);
        }
    }

    // Filter by number of bathrooms
    if ($request->filled('bathrooms')) {
        $bathrooms = $request->bathrooms;
        if ($bathrooms == '3') {
            $query->where('bathrooms', '>=', 3);
        } else {
            $query->where('bathrooms', $bathrooms);
        }
    }

    // Filter by floor
    if ($request->filled('floor')) {
        $floor = $request->floor;
        if ($floor === '0') {
            $query->where('floor', 0);
        } elseif ($floor === '1-3') {
            $query->whereBetween('floor', [1, 3]);
        } elseif ($floor === '4+') {
            $query->where('floor', '>=', 4);
        }
    }

    // Filter by year built
    if ($request->filled('year_min')) {
        $query->where('year_built', '>=', $request->year_min);
    }
    if ($request->filled('year_max')) {
        $query->where('year_built', '<=', $request->year_max);
    }

    // Filter by features (JSON column)
    if ($request->filled('features') && is_array($request->features)) {
        foreach ($request->features as $feature) {
            $query->whereJsonContains('features', $feature);
        }
    }

    // Search by keyword
    if ($request->filled('search')) {
        $keyword = $request->search;
        $query->where(function($q) use ($keyword) {
            $q->where('title', 'like', '%' . $keyword . '%')
              ->orWhere('description', 'like', '%' . $keyword . '%')
              ->orWhere('city', 'like', '%' . $keyword . '%');
        });
    }

    // Sorting
    $sortBy = $request->get('sort', 'newest');
    
    // ALWAYS prioritize: Featured → Top → Regular
    $query->orderByRaw("CASE 
        WHEN is_featured = 1 AND (featured_until IS NULL OR featured_until > NOW()) THEN 1
        WHEN is_top = 1 AND (top_until IS NULL OR top_until > NOW()) THEN 2
        ELSE 3
    END");
    
    // Then apply user's selected sorting
    switch ($sortBy) {
        case 'price_asc':
            $query->orderBy('price', 'asc');
            break;
        case 'price_desc':
            $query->orderBy('price', 'desc');
            break;
        case 'area_asc':
            $query->orderBy('area', 'asc');
            break;
        case 'area_desc':
            $query->orderBy('area', 'desc');
            break;
        case 'oldest':
            $query->orderBy('published_at', 'asc');
            break;
        case 'newest':
        default:
            $query->orderBy('published_at', 'desc');
            break;
    }

    // Get featured listings separately for homepage
    $featuredListings = Listing::with(['category', 'primaryImage'])
                               ->where('status', 'active')
                               ->whereNotNull('published_at')
                               ->where('published_at', '<=', now())
                               ->where('is_featured', true)
                               ->where(function($q) {
                                   $q->whereNull('featured_until')
                                     ->orWhere('featured_until', '>', now());
                               })
                               ->limit(6)
                               ->get();

    // Paginate results (15 per page)
    $listings = $query->paginate(15)->withQueryString();

    // Get all categories for filter dropdown
    $categories = Category::where('is_active', true)
                          ->orderBy('order')
                          ->get();

    // Get unique cities for filter dropdown
    $cities = Listing::where('status', 'active')
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now())
                    ->distinct()
                    ->pluck('city')
                    ->filter()
                    ->sort()
                    ->values();

    return view('listings.index', compact(
        'listings',
        'featuredListings',
        'categories',
        'cities'
    ));
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

        // Store listing data in session and redirect to package selection
        session(['listing_draft' => $validated]);
        session(['listing_images' => $request->file('images')]);

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
        $topPackages = \App\Models\Package::where('is_active', true)
                                          ->where('type', 'top')
                                          ->orderBy('duration_days')
                                          ->get();

        $featuredPackages = \App\Models\Package::where('is_active', true)
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
            $package = \App\Models\Package::findOrFail($request->package_id);
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

    // Delete all images using ImageService
    $this->imageService->deleteAllListingImages($listing->id);

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
             try {
             // Use ImageService to upload and optimize
             $paths = $this->imageService->uploadListingImage($image, $listing->id);
            
                // Create listing image record with original path
             $listing->images()->create([
                'image_path' => $paths['original'],
                'order' => $index,
                'is_primary' => $index === 0,
             ]);
             } catch (\Exception $e) {
             \Log::error('Failed to upload image: ' . $e->getMessage());
                // Continue with other images even if one fails
             }
         }
    }
    /**
 * Delete a single image from a listing
 * 
 * Route: DELETE /listings/{slug}/images/{image}
 */
public function deleteImage($slug, $imageId)
{
    $listing = Listing::where('slug', $slug)->firstOrFail();

    // Check if user owns this listing
    if (Auth::id() !== $listing->user_id && !Auth::user()->isAdmin()) {
        abort(403);
    }

    // Find the image
    $image = $listing->images()->findOrFail($imageId);

    // Extract filename from path
    $filename = basename($image->image_path);

    // Delete all sizes of the image
    $this->imageService->deleteListingImage($listing->id, $filename);

    // Delete database record
    $image->delete();

    return back()->with('success', 'Slika je obrisana!');
}







}