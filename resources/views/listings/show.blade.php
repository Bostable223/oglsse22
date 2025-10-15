@extends('layouts.app')

@section('title', $listing->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ url()->previous() }}" class="text-blue-600 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-2"></i> Nazad na pretragu
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Main Content -->
        <div class="lg:col-span-2">
            
            <!-- Image Gallery -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                @if($listing->images->count() > 0)
                    <!-- Main Image -->
                    <div class="relative h-96 bg-gray-200" id="mainImage">
                        <img src="{{ listing_image($listing->images->first()->image_path, 'large') }}" alt="{{ $listing->title }}" class="w-full h-full object-cover">
                        
                        <!-- Featured Badge -->
                        @if($listing->isFeaturedActive())
                            <div class="absolute top-4 left-4 bg-yellow-500 text-white px-4 py-2 rounded-full font-semibold">
                                <i class="fas fa-star"></i> Istaknuto
                            </div>
                        @endif
                    </div>

                    <!-- Thumbnail Gallery -->
                    @if($listing->images->count() > 1)
                        <div class="p-4 grid grid-cols-6 gap-2">
                            @foreach($listing->images as $image)
                                <div class="cursor-pointer hover:opacity-75 transition-opacity">
                                    <img src="{{ listing_image($image->image_path, 'thumbnail') }}" alt="Slika {{ $loop->iteration }}" 
                                         class="w-full h-20 object-cover rounded"
                                         onclick="document.getElementById('mainImage').querySelector('img').src = '{{ listing_image($image->image_path, 'large') }}'">
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="h-96 bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-image text-6xl text-gray-400"></i>
                    </div>
                @endif
            </div>

            <!-- Listing Details -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <!-- Status Badge (only for owner/admin) -->
                @if(auth()->check() && (auth()->id() === $listing->user_id || auth()->user()->isAdmin()))
                    <div class="mb-4">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                            @if($listing->status === 'active') bg-green-100 text-green-800
                            @elseif($listing->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($listing->status === 'rejected') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            Status: {{ ucfirst($listing->status) }}
                        </span>
                    </div>
                @endif

                <!-- Title and Category -->
                <div class="mb-4">
                    <div class="text-sm text-gray-500 mb-2">
                        <i class="fas fa-tag"></i> {{ $listing->category->name }}
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $listing->title }}</h1>
                </div>

                <!-- Location -->
                <div class="text-lg text-gray-600 mb-6">
                    <i class="fas fa-map-marker-alt text-red-500"></i> 
                    {{ $listing->city }}{{ $listing->municipality ? ', ' . $listing->municipality : '' }}
                    @if($listing->address)
                        <br>
                        <span class="text-sm ml-5">{{ $listing->address }}</span>
                    @endif
                </div>

                <!-- Price -->
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <div class="text-4xl font-bold text-blue-600">
                        {{ $listing->formattedPrice() }}
                    </div>
                    <div class="text-sm text-gray-600 mt-1">
                        {{ $listing->listing_type == 'sale' ? 'Prodajna cena' : 'Mesečna kirija' }}
                    </div>
                </div>

                <!-- Property Details Grid -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                    @if($listing->area)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-gray-500 text-sm mb-1"><i class="fas fa-ruler-combined"></i> Površina</div>
                            <div class="text-lg font-semibold">{{ $listing->area }} m²</div>
                        </div>
                    @endif

                    @if($listing->rooms)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-gray-500 text-sm mb-1"><i class="fas fa-bed"></i> Sobe</div>
                            <div class="text-lg font-semibold">{{ $listing->rooms }}</div>
                        </div>
                    @endif

                    @if($listing->bathrooms)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-gray-500 text-sm mb-1"><i class="fas fa-bath"></i> Kupatila</div>
                            <div class="text-lg font-semibold">{{ $listing->bathrooms }}</div>
                        </div>
                    @endif

                    @if($listing->floor !== null)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-gray-500 text-sm mb-1"><i class="fas fa-building"></i> Sprat</div>
                            <div class="text-lg font-semibold">
                                {{ $listing->floor }}{{ $listing->total_floors ? '/' . $listing->total_floors : '' }}
                            </div>
                        </div>
                    @endif

                    @if($listing->year_built)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-gray-500 text-sm mb-1"><i class="fas fa-calendar"></i> Godina gradnje</div>
                            <div class="text-lg font-semibold">{{ $listing->year_built }}</div>
                        </div>
                    @endif
                </div>

                <!-- Features -->
                @if($listing->features && count($listing->features) > 0)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-3">Dodatne karakteristike</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($listing->features as $feature)
                                <div class="flex items-center text-gray-700">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    {{ $feature }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Description -->
                <div>
                    <h3 class="text-lg font-semibold mb-3">Opis</h3>
                    <div class="text-gray-700 whitespace-pre-line">{{ $listing->description }}</div>
                </div>

                <!-- Stats -->
                <div class="mt-6 pt-6 border-t border-gray-200 flex items-center gap-6 text-sm text-gray-500">
                    <span><i class="fas fa-eye"></i> {{ $listing->views }} pregleda</span>
                    <span><i class="fas fa-clock"></i> Objavljeno {{ $listing->published_at->diffForHumans() }}</span>
                </div>
            </div>

            <!-- Recommended Listings (Bottom) -->
            @if($similarListings->count() > 0)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-2xl font-bold mb-6">Preporučeni oglasi</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($similarListings as $similar)
                        @include('partials.listing-card', ['listing' => $similar])
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            
            <!-- Contact Card -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Kontakt informacije</h3>
                
                <!-- Owner Info -->
                <div class="flex items-center mb-4 pb-4 border-b border-gray-200">
                    <img src="{{ $listing->user->avatarUrl() }}" alt="{{ $listing->user->name }}" class="w-12 h-12 rounded-full mr-3">
                    <div>
                        <div class="font-semibold">{{ $listing->contact_name ?? $listing->user->name }}</div>
                        <div class="text-sm text-gray-500">Prodavac</div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3 mb-6">
                    <!-- Phone Button (Click to Reveal) -->
                    @if($listing->contact_phone)
                        <button 
                            onclick="revealPhone()" 
                            id="phoneButton"
                            class="block w-full bg-blue-600 text-white text-center px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold transition"
                        >
                            <i class="fas fa-phone mr-2"></i> Prikaži broj telefona
                        </button>
                        <a 
                            href="tel:{{ $listing->contact_phone }}" 
                            id="phoneLink"
                            class="hidden w-full bg-green-600 text-white text-center px-6 py-3 rounded-lg hover:bg-green-700 font-semibold block"
                        >
                            <i class="fas fa-phone mr-2"></i> {{ $listing->contact_phone }}
                        </a>
                    @endif

                    @auth
                        <!-- Favorite Button -->
                        <form action="{{ route('listings.favorite', $listing->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="block w-full {{ $isFavorited ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-700' }} text-center px-6 py-3 rounded-lg hover:bg-gray-200 font-semibold">
                                <i class="fas fa-heart mr-2"></i> 
                                {{ $isFavorited ? 'Ukloni iz omiljenih' : 'Dodaj u omiljene' }}
                            </button>
                        </form>

                        <!-- Edit/Delete (if owner) -->
                        @if(auth()->id() === $listing->user_id || auth()->user()->isAdmin())
                            <a href="{{ route('listings.edit', $listing->slug) }}" class="block w-full bg-yellow-100 text-yellow-700 text-center px-6 py-3 rounded-lg hover:bg-yellow-200 font-semibold">
                                <i class="fas fa-edit mr-2"></i> Izmeni oglas
                            </a>

                            <form action="{{ route('listings.destroy', $listing->slug) }}" method="POST" onsubmit="return confirm('Da li ste sigurni da želite obrisati ovaj oglas?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="block w-full bg-red-100 text-red-700 text-center px-6 py-3 rounded-lg hover:bg-red-200 font-semibold">
                                    <i class="fas fa-trash mr-2"></i> Obriši oglas
                                </button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Recently Viewed Listings -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Nedavno pregledani</h3>
                <div id="recentlyViewed" class="space-y-4">
                    <!-- Will be populated by JavaScript -->
                    <p class="text-sm text-gray-500 text-center py-4">Nema nedavno pregledanih oglasa</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Phone Number Reveal
function revealPhone() {
    document.getElementById('phoneButton').classList.add('hidden');
    document.getElementById('phoneLink').classList.remove('hidden');
}

// Recently Viewed Listings
const currentListing = {
    id: {{ $listing->id }},
    slug: '{{ $listing->slug }}',
    title: '{{ addslashes($listing->title) }}',
    price: '{{ $listing->formattedPrice() }}',
    city: '{{ $listing->city }}',
    image: '{{ $listing->primaryImage ? listing_image($listing->primaryImage->image_path, "thumbnail") : "" }}'
};

// Get recently viewed from localStorage
let recentlyViewed = JSON.parse(localStorage.getItem('recentlyViewed') || '[]');

// Remove current listing if it exists
recentlyViewed = recentlyViewed.filter(item => item.id !== currentListing.id);

// Add current listing to the beginning
recentlyViewed.unshift(currentListing);

// Keep only last 5
recentlyViewed = recentlyViewed.slice(0, 5);

// Save back to localStorage
localStorage.setItem('recentlyViewed', JSON.stringify(recentlyViewed));

// Display recently viewed (excluding current)
const recentlyViewedContainer = document.getElementById('recentlyViewed');
const displayRecent = recentlyViewed.filter(item => item.id !== currentListing.id).slice(0, 4);

if (displayRecent.length > 0) {
    recentlyViewedContainer.innerHTML = displayRecent.map(item => `
        <a href="/listings/${item.slug}" class="block hover:bg-gray-50 p-3 rounded-lg transition-colors">
            <div class="flex gap-3">
                <div class="w-20 h-20 bg-gray-200 rounded flex-shrink-0">
                    ${item.image ? 
                        `<img src="${item.image}" alt="${item.title}" class="w-full h-full object-cover rounded">` :
                        `<div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fas fa-image"></i></div>`
                    }
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="font-semibold text-sm text-gray-900 line-clamp-2 mb-1">${item.title}</h4>
                    <p class="text-blue-600 font-bold text-sm">${item.price}</p>
                    <p class="text-xs text-gray-500"><i class="fas fa-map-marker-alt"></i> ${item.city}</p>
                </div>
            </div>
        </a>
    `).join('');
}
</script>
@endpush
@endsection