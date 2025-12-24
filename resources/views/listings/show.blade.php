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
            
           <!-- Images Gallery -->
@if($listing->images->count() > 0)
    <div class="bg-white rounded-lg shadow-sm overflow-hidden p-6 mb-6">
        <x-image-gallery-main :images="$listing->images" :title="$listing->title" />
    </div>
@endif

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
                    
                    <!-- Listing ID and Date -->
                    <div class="flex items-center gap-4 mt-3 text-sm text-gray-500">
                        <span><i class="fas fa-hashtag"></i> ID: {{ $listing->id }}</span>
                        <span><i class="fas fa-calendar"></i> Objavljeno: {{ $listing->published_at->format('d.m.Y') }}</span>
                    </div>
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

    @if($listing->price && $listing->area && $listing->area > 0)
        <div class="text-sm text-gray-600 mt-1">
            Cena po m²: 
            <span class="font-semibold text-gray-800">
                {{ number_format($listing->price / $listing->area, 0, ',', '.') }} €/m²
            </span>
        </div>
    @endif
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

            <!-- Recommended Listings Slider -->
            @if($similarListings->count() > 0)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold">Preporučeni oglasi</h3>
                    <div class="flex gap-2">
                        <button onclick="slideRecommended('prev')" class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-full transition-colors">
                            <i class="fas fa-chevron-left text-gray-600"></i>
                        </button>
                        <button onclick="slideRecommended('next')" class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-full transition-colors">
                            <i class="fas fa-chevron-right text-gray-600"></i>
                        </button>
                    </div>
                </div>
                
                <div class="relative overflow-hidden">
                    <div id="recommendedSlider" class="flex transition-transform duration-300 ease-in-out">
                        @foreach($similarListings as $similar)
                            <div class="w-full md:w-1/3 flex-shrink-0 px-3">
                                @include('partials.listing-card', ['listing' => $similar])
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Slider Dots -->
                @if($similarListings->count() > 3)
                <div class="flex justify-center gap-2 mt-4">
                    @for($i = 0; $i < ceil($similarListings->count() / 3); $i++)
                        <button onclick="goToSlide({{ $i }})" class="slider-dot w-2 h-2 rounded-full bg-gray-300 hover:bg-gray-400 transition-colors" data-slide="{{ $i }}"></button>
                    @endfor
                </div>
                @endif
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
// Image Gallery Slider
let currentImageIndex = 0;
const totalImages = {{ $listing->images->count() }};

function changeImage(direction) {
    const slides = document.querySelectorAll('#imageSlider [data-slide]');
    const thumbnails = document.querySelectorAll('[data-thumbnail]');
    
    // Hide current image
    slides[currentImageIndex].classList.remove('opacity-100');
    slides[currentImageIndex].classList.add('opacity-0');
    thumbnails[currentImageIndex].classList.remove('border-blue-500');
    thumbnails[currentImageIndex].classList.add('border-transparent');
    
    // Calculate new index
    if (direction === 'next') {
        currentImageIndex = (currentImageIndex + 1) % totalImages;
    } else {
        currentImageIndex = (currentImageIndex - 1 + totalImages) % totalImages;
    }
    
    // Show new image
    slides[currentImageIndex].classList.remove('opacity-0');
    slides[currentImageIndex].classList.add('opacity-100');
    thumbnails[currentImageIndex].classList.remove('border-transparent');
    thumbnails[currentImageIndex].classList.add('border-blue-500');
    
    // Update counter
    document.getElementById('currentImageIndex').textContent = currentImageIndex + 1;
}

function goToImage(index) {
    const slides = document.querySelectorAll('#imageSlider [data-slide]');
    const thumbnails = document.querySelectorAll('[data-thumbnail]');
    
    // Hide current image
    slides[currentImageIndex].classList.remove('opacity-100');
    slides[currentImageIndex].classList.add('opacity-0');
    thumbnails[currentImageIndex].classList.remove('border-blue-500');
    thumbnails[currentImageIndex].classList.add('border-transparent');
    
    // Update index
    currentImageIndex = index;
    
    // Show new image
    slides[currentImageIndex].classList.remove('opacity-0');
    slides[currentImageIndex].classList.add('opacity-100');
    thumbnails[currentImageIndex].classList.remove('border-transparent');
    thumbnails[currentImageIndex].classList.add('border-blue-500');
    
    // Update counter
    document.getElementById('currentImageIndex').textContent = currentImageIndex + 1;
}

// Phone Number Reveal
function revealPhone() {
    document.getElementById('phoneButton').classList.add('hidden');
    document.getElementById('phoneLink').classList.remove('hidden');
}

// Recommended Listings Slider
let currentSlide = 0;
const totalListings = {{ $similarListings->count() }};
const slidesPerView = window.innerWidth >= 768 ? 3 : 1;
const maxSlide = Math.max(0, totalListings - slidesPerView);

function slideRecommended(direction) {
    const slider = document.getElementById('recommendedSlider');
    const slideWidth = slider.children[0].offsetWidth;
    
    if (direction === 'next') {
        currentSlide = Math.min(currentSlide + 1, maxSlide);
    } else {
        currentSlide = Math.max(currentSlide - 1, 0);
    }
    
    slider.style.transform = `translateX(-${currentSlide * slideWidth}px)`;
    updateDots();
}

function goToSlide(index) {
    currentSlide = index;
    const slider = document.getElementById('recommendedSlider');
    const slideWidth = slider.children[0].offsetWidth;
    slider.style.transform = `translateX(-${currentSlide * slideWidth}px)`;
    updateDots();
}

function updateDots() {
    document.querySelectorAll('.slider-dot').forEach((dot, index) => {
        if (index === currentSlide) {
            dot.classList.remove('bg-gray-300');
            dot.classList.add('bg-blue-600');
        } else {
            dot.classList.remove('bg-blue-600');
            dot.classList.add('bg-gray-300');
        }
    });
}

// Initialize dots
updateDots();

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