@extends('layouts.app')

@section('title', 'Početna')

@section('content')
<!-- Hero Section -->
<div class="relative min-h-screen flex items-center py-12 md:py-0">
    <!-- Background Image with Overlay -->
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=2075&q=80" 
             alt="Luxury Home" 
             class="w-full h-full object-cover">
        <!-- Dark overlay for better text readability -->
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
    </div>

    <!-- Hero Content -->
    <div class="relative z-10 w-full">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                
                <!-- Left Side - Text Content -->
                <div class="text-white text-center lg:text-left">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                        Pronađite Savršenu Nekretninu
                    </h1>
                    <p class="text-lg md:text-xl lg:text-2xl mb-8 text-gray-200">
                        Pregledajte hiljade oglasa za stanove, kuće i poslovne prostore širom Srbije
                    </p>
                    
                    <!-- Quick Stats -->
                    <div class="flex justify-center lg:justify-start gap-6 md:gap-8 mb-8">
                        <div>
                            <div class="text-2xl md:text-3xl font-bold">{{ $stats['total_listings'] ?? 0 }}+</div>
                            <div class="text-gray-300 text-sm md:text-base">Oglasa</div>
                        </div>
                        <div>
                            <div class="text-2xl md:text-3xl font-bold">{{ $stats['active_users'] ?? 0 }}+</div>
                            <div class="text-gray-300 text-sm md:text-base">Korisnika</div>
                        </div>
                        <div>
                            <div class="text-2xl md:text-3xl font-bold">{{ $stats['cities'] ?? 0 }}+</div>
                            <div class="text-gray-300 text-sm md:text-base">Gradova</div>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Search Form (100% scale) -->
                <div class="flex justify-center">
                    <div class="bg-white rounded-2xl shadow-2xl p-6 w-full lg:max-w-md" style="transform: scale(1.0); transform-origin: right center;">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">Pretraži nekretnine</h3>
                        
                        <form action="{{ route('listings.index') }}" method="GET">
                            <!-- Location with Autocomplete -->
                            <div class="mb-4 relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Lokacija</label>
                                <input 
                                    type="text" 
                                    name="city" 
                                    id="locationInput"
                                    placeholder="Grad, opština ili naselje"
                                    autocomplete="off"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                <div id="locationDropdown" class="hidden absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto"></div>
                            </div>

                            <!-- Property Type and Transaction Type in Same Row -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <!-- Property Type -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tip nekretnine</label>
                                    <select name="category" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <option value="">Sve</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Listing Type (Rent/Sale Buttons) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Transakcija</label>
                                    <div class="flex gap-2">
                                        <button type="button" onclick="selectListingType(event, 'sale')" data-type="sale"
                                                class="listing-type-btn flex-1 px-3 py-3 border-2 border-gray-300 rounded-lg hover:border-blue-500 transition-colors text-center font-medium text-sm">
                                            Prodaja
                                        </button>
                                        <button type="button" onclick="selectListingType(event, 'rent')" data-type="rent"
                                                class="listing-type-btn flex-1 px-3 py-3 border-2 border-gray-300 rounded-lg hover:border-blue-500 transition-colors text-center font-medium text-sm">
                                            Izdavanje
                                        </button>
                                    </div>
                                    <input type="hidden" name="listing_type" id="listingTypeInput" value="">
                                </div>
                            </div>

                            <!-- Price Range -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Min. Cena</label>
                                    <input 
                                        type="number" 
                                        name="price_min" 
                                        placeholder="0"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Max. Cena</label>
                                    <input 
                                        type="number" 
                                        name="price_max" 
                                        placeholder="∞"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    >
                                </div>
                            </div>

                            <!-- Area and Rooms -->
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Površina (m²)</label>
                                    <select name="area_min" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <option value="">Bilo koja</option>
                                        <option value="20">20+ m²</option>
                                        <option value="40">40+ m²</option>
                                        <option value="60">60+ m²</option>
                                        <option value="80">80+ m²</option>
                                        <option value="100">100+ m²</option>
                                        <option value="150">150+ m²</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Sobe</label>
                                    <select name="rooms" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <option value="">Bilo koji</option>
                                        <option value="0.5">0.5</option>
                                        <option value="1">1</option>
                                        <option value="1.5">1.5</option>
                                        <option value="2">2</option>
                                        <option value="2.5">2.5</option>
                                        <option value="3">3</option>
                                        <option value="3.5">3.5</option>
                                        <option value="4">4+</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Search Button -->
                            <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-lg hover:bg-blue-700 font-semibold text-lg transition-colors">
                                <i class="fas fa-search mr-2"></i> Pretraži
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll Down Indicator (Hidden on Mobile) -->
    <div class="hidden lg:block absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white animate-bounce">
        <i class="fas fa-chevron-down text-3xl"></i>
    </div>
</div>

<!-- Categories Section -->
<div class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Pretraži po kategoriji</h2>
            <p class="text-lg text-gray-600">Brzo pronađite šta tražite</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
            @foreach($categories as $category)
                <a href="{{ route('listings.index', ['category' => $category->id]) }}" 
                   class="group bg-white rounded-xl shadow-md hover:shadow-xl transition-all p-6 text-center border-2 border-gray-100 hover:border-blue-500">
                    <div class="text-4xl mb-4 group-hover:scale-110 transition-transform">
                        @if($category->icon)
                            <i class="fas fa-{{ $category->icon }} text-blue-600"></i>
                        @else
                            <i class="fas fa-home text-blue-600"></i>
                        @endif
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">{{ $category->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $category->listings_count }} oglasa</p>
                </a>
            @endforeach
        </div>
    </div>
</div>

<!-- Featured Listings Slider -->
@if($featuredListings->count() > 0)
<div class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12">
             <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-4xl font-bold text-gray-900 mb-2">Istaknuti oglasi</h2>
            <p class="text-lg text-gray-600">Pregledajte naše najpopularnije nekretnine</p>
        </div>
        <div class="flex gap-3">
            <button onclick="featuredSlider.prev()" class="bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition-colors shadow-md">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button onclick="featuredSlider.next()" class="bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition-colors shadow-md">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
        
        <!-- Changed: Added padding and changed overflow-hidden to overflow-x-clip -->
        <div class="relative -mx-4 px-4 py-4" style="overflow-x: clip;">
            <div id="featuredSlider" class="flex transition-transform duration-500 ease-in-out gap-6">
                @foreach($featuredListings as $listing)
                    <div class="flex-shrink-0" style="width: 285px;">
                        @include('partials.listing-card', ['listing' => $listing, 'featured' => true])
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<!-- New Listings Slider -->
@if($newListings->count() > 0)
<div class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
       <div class="mb-12">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-4xl font-bold text-gray-900 mb-2">Novi oglasi</h2>
            <p class="text-lg text-gray-600">Najnovije dodati oglasi</p>
        </div>
        <div class="flex gap-3">
            <button onclick="newSlider.prev()" class="bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition-colors shadow-md">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button onclick="newSlider.next()" class="bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition-colors shadow-md">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
        
        <!-- Changed: Added padding and changed overflow-hidden to overflow-x-clip -->
        <div class="relative -mx-4 px-4 py-4" style="overflow-x: clip;">
            <div id="newSlider" class="flex transition-transform duration-500 ease-in-out gap-6">
                @foreach($newListings as $listing)
                    <div class="flex-shrink-0" style="width: 285px;">
                        @include('partials.listing-card', ['listing' => $listing])
                    </div>
                @endforeach
            </div>
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('listings.index') }}" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                Vidi sve oglase <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</div>
@endif

<!-- How It Works Section -->
<div class="py-16 bg-gradient-to-br from-blue-600 to-blue-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold mb-4">Kako funkcioniše?</h2>
            <p class="text-xl text-blue-100">Jednostavno i brzo do savršene nekretnine</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white bg-opacity-20 rounded-full mb-6">
                    <span class="text-4xl font-bold">1</span>
                </div>
                <h3 class="text-2xl font-bold mb-4">Pretražite</h3>
                <p class="text-blue-100">Koristite napredne filtere da pronađete nekretninu koja vam odgovara</p>
            </div>

            <div class="text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white bg-opacity-20 rounded-full mb-6">
                    <span class="text-4xl font-bold">2</span>
                </div>
                <h3 class="text-2xl font-bold mb-4">Kontaktirajte</h3>
                <p class="text-blue-100">Direktno stupite u kontakt sa vlasnikom ili agentom</p>
            </div>

            <div class="text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white bg-opacity-20 rounded-full mb-6">
                    <span class="text-4xl font-bold">3</span>
                </div>
                <h3 class="text-2xl font-bold mb-4">Pronađite dom</h3>
                <p class="text-blue-100">Zatvorite posao i preselite se u svoj novi dom</p>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="py-16 bg-gray-900 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl font-bold mb-6">Imate nekretninu za prodaju ili iznajmljivanje?</h2>
        <p class="text-xl text-gray-300 mb-8">Postavite oglas besplatno i dođite do hiljada kupaca</p>
        <a href="{{ route('listings.create') }}" class="inline-block bg-blue-600 text-white px-8 py-4 rounded-lg hover:bg-blue-700 font-semibold text-lg">
            <i class="fas fa-plus mr-2"></i> Postavi oglas besplatno
        </a>
    </div>
</div>

@push('styles')
<style>
    @keyframes bounce {
        0%, 100% {
            transform: translateY(0) translateX(-50%);
        }
        50% {
            transform: translateY(-10px) translateX(-50%);
        }
    }
    
    .animate-bounce {
        animation: bounce 2s infinite;
    }
</style>
@endpush

@push('scripts')
<script>
// Featured Listings Slider
const featuredSlider = {
    currentSlide: 0,
    totalSlides: {{ $featuredListings->count() }},
    cardWidth: 285,
    gap: 24,
    
    next() {
        const container = document.getElementById('featuredSlider').parentElement;
        const visibleCards = Math.floor(container.offsetWidth / (this.cardWidth + this.gap));
        const maxSlide = Math.max(0, this.totalSlides - visibleCards);
        
        if (this.currentSlide < maxSlide) {
            this.currentSlide++;
            this.update();
        }
    },
    
    prev() {
        if (this.currentSlide > 0) {
            this.currentSlide--;
            this.update();
        }
    },
    
    update() {
        const slider = document.getElementById('featuredSlider');
        const moveAmount = this.currentSlide * (this.cardWidth + this.gap);
        slider.style.transform = `translateX(-${moveAmount}px)`;
    }
};

// New Listings Slider
const newSlider = {
    currentSlide: 0,
    totalSlides: {{ $newListings->count() }},
    cardWidth: 285,
    gap: 24,
    
    next() {
        const container = document.getElementById('newSlider').parentElement;
        const visibleCards = Math.floor(container.offsetWidth / (this.cardWidth + this.gap));
        const maxSlide = Math.max(0, this.totalSlides - visibleCards);
        
        if (this.currentSlide < maxSlide) {
            this.currentSlide++;
            this.update();
        }
    },
    
    prev() {
        if (this.currentSlide > 0) {
            this.currentSlide--;
            this.update();
        }
    },
    
    update() {
        const slider = document.getElementById('newSlider');
        const moveAmount = this.currentSlide * (this.cardWidth + this.gap);
        slider.style.transform = `translateX(-${moveAmount}px)`;
    }
};

// No need for responsive adjustments with fixed widths
window.addEventListener('resize', function() {
    featuredSlider.currentSlide = 0;
    newSlider.currentSlide = 0;
    featuredSlider.update();
    newSlider.update();
});

// Location Autocomplete
const locationInput = document.getElementById('locationInput');
const locationDropdown = document.getElementById('locationDropdown');

// All unique locations from your database
const locations = @json($allLocations ?? []);

locationInput.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase().trim();
    
    if (searchTerm.length < 2) {
        locationDropdown.classList.add('hidden');
        return;
    }
    
    // Filter locations that match the search term
    const filtered = locations.filter(loc => 
        loc.toLowerCase().includes(searchTerm)
    );
    
    if (filtered.length === 0) {
        locationDropdown.classList.add('hidden');
        return;
    }
    
    // Display dropdown with results
    locationDropdown.innerHTML = filtered.slice(0, 10).map(loc => 
        `<div class="px-4 py-2 hover:bg-blue-50 cursor-pointer location-item" data-location="${loc}">
            <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>${loc}
        </div>`
    ).join('');
    
    locationDropdown.classList.remove('hidden');
    
    // Add click handlers
    document.querySelectorAll('.location-item').forEach(item => {
        item.addEventListener('click', function() {
            locationInput.value = this.dataset.location;
            locationDropdown.classList.add('hidden');
        });
    });
});

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!locationInput.contains(e.target) && !locationDropdown.contains(e.target)) {
        locationDropdown.classList.add('hidden');
    }
});

// Listing Type Selection
function selectListingType(event, type) {
    event.preventDefault();
    
    const allBtns = document.querySelectorAll('.listing-type-btn');
    const input = document.getElementById('listingTypeInput');
    const clickedBtn = event.currentTarget;
    
    // If clicking the same button, deselect it
    if (input.value === type) {
        clickedBtn.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
        clickedBtn.classList.add('border-gray-300');
        input.value = '';
    } else {
        // Reset all buttons
        allBtns.forEach(btn => {
            btn.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
            btn.classList.add('border-gray-300');
        });
        
        // Highlight selected button
        clickedBtn.classList.remove('border-gray-300');
        clickedBtn.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
        input.value = type;
    }
}
</script>
@endpush
@endsection