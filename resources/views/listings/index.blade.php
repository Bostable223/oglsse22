@extends('layouts.app')

@section('title', 'Svi oglasi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        <!-- LEFT SIDEBAR - FILTERS -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold flex items-center">
                        <i class="fas fa-filter mr-2 text-blue-600"></i> Filteri
                    </h3>
                    @if(request()->hasAny(['category', 'city', 'listing_type', 'price_min', 'price_max', 'area_min', 'area_max', 'rooms', 'bathrooms', 'floor', 'year_min', 'year_max', 'features', 'search']))
                        <a href="{{ route('listings.index') }}" class="text-xs text-red-600 hover:text-red-700">
                            <i class="fas fa-times-circle"></i> Obriši
                        </a>
                    @endif
                </div>

                <form action="{{ route('listings.index') }}" method="GET">
                    
                    <!-- Category Filter -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategorija</label>
                        <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                            <option value="">Sve kategorije</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- City Filter with Autocomplete -->
                    <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Grad</label>
                    <input 
                    type="text" 
                    name="city" 
                    id="city-autocomplete" 
                    value="{{ request('city') }}"
                    placeholder="Unesite grad"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                    autocomplete="off"
                     >
                    </div>

                    <!-- Listing Type -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tip</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center justify-center p-2 border rounded-lg cursor-pointer text-sm {{ request('listing_type') == 'sale' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-300 hover:bg-gray-50' }}">
                                <input type="radio" name="listing_type" value="sale" {{ request('listing_type') == 'sale' ? 'checked' : '' }} class="sr-only" onchange="this.form.submit()">
                                Prodaja
                            </label>
                            <label class="flex items-center justify-center p-2 border rounded-lg cursor-pointer text-sm {{ request('listing_type') == 'rent' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-300 hover:bg-gray-50' }}">
                                <input type="radio" name="listing_type" value="rent" {{ request('listing_type') == 'rent' ? 'checked' : '' }} class="sr-only" onchange="this.form.submit()">
                                Izdavanje
                            </label>
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cena</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="price_min" placeholder="Od" value="{{ request('price_min') }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                            <input type="number" name="price_max" placeholder="Do" value="{{ request('price_max') }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Area Range -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Površina (m²)</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="area_min" placeholder="Od" value="{{ request('area_min') }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                            <input type="number" name="area_max" placeholder="Do" value="{{ request('area_max') }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Rooms -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sobe</label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach(['1', '2', '3', '4', '5'] as $room)
                                <label class="flex items-center justify-center p-2 border rounded-lg cursor-pointer text-sm {{ request('rooms') == $room ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-300 hover:bg-gray-50' }}">
                                    <input type="radio" name="rooms" value="{{ $room }}" {{ request('rooms') == $room ? 'checked' : '' }} class="sr-only" onchange="this.form.submit()">
                                    {{ $room }}{{ $room == '5' ? '+' : '' }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Bathrooms -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kupatila</label>
                        <select name="bathrooms" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                            <option value="">Sve</option>
                            <option value="1" {{ request('bathrooms') == '1' ? 'selected' : '' }}>1</option>
                            <option value="2" {{ request('bathrooms') == '2' ? 'selected' : '' }}>2</option>
                            <option value="3" {{ request('bathrooms') == '3' ? 'selected' : '' }}>3+</option>
                        </select>
                    </div>

                    <!-- Floor -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sprat</label>
                        <select name="floor" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                            <option value="">Sve</option>
                            <option value="0" {{ request('floor') === '0' ? 'selected' : '' }}>Prizemlje</option>
                            <option value="1-3" {{ request('floor') == '1-3' ? 'selected' : '' }}>1-3 sprat</option>
                            <option value="4+" {{ request('floor') == '4+' ? 'selected' : '' }}>4+ sprat</option>
                        </select>
                    </div>

                    <!-- Year Built -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Godina gradnje</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="year_min" placeholder="Od" value="{{ request('year_min') }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                            <input type="number" name="year_max" placeholder="Do" value="{{ request('year_max') }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Features -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Karakteristike</label>
                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            @php
                                $availableFeatures = [
                                    'parking' => 'Parking',
                                    'balcony' => 'Balkon',
                                    'elevator' => 'Lift',
                                    'furnished' => 'Namešten',
                                    'garage' => 'Garaža',
                                    'garden' => 'Bašta',
                                    'ac' => 'Klima',
                                    'heating' => 'Grejanje',
                                    'basement' => 'Podrum',
                                    'terrace' => 'Terasa'
                                ];
                                $selectedFeatures = request('features', []);
                            @endphp

                            @foreach($availableFeatures as $key => $label)
                                <label class="flex items-center hover:bg-gray-50 p-1 rounded cursor-pointer">
                                    <input type="checkbox" name="features[]" value="{{ $key }}" {{ in_array($key, $selectedFeatures) ? 'checked' : '' }} class="mr-2 rounded text-blue-600" onchange="this.form.submit()">
                                    <span class="text-sm">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Apply Button (only for price/area inputs) -->
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold text-sm">
                        <i class="fas fa-search mr-2"></i> Primeni filtere
                    </button>
                </form>
            </div>
        </div>

        <!-- RIGHT CONTENT - LISTINGS -->
        <div class="lg:col-span-3">
            
            <!-- Active Filters Tags -->
            @if(request()->hasAny(['category', 'city', 'listing_type', 'price_min', 'price_max', 'area_min', 'area_max', 'rooms', 'bathrooms', 'floor', 'year_min', 'year_max', 'features', 'search']))
            <div class="bg-blue-50 rounded-lg p-4 mb-4">
                <div class="flex items-center flex-wrap gap-2">
                    <span class="text-xs font-medium text-gray-700">Aktivni filteri:</span>
                    
                    @if(request('category'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-white border border-blue-200">
                            {{ $categories->find(request('category'))->name ?? 'Kategorija' }}
                            <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="ml-1 text-blue-600 hover:text-blue-800">×</a>
                        </span>
                    @endif

                    @if(request('city'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-white border border-blue-200">
                            {{ request('city') }}
                            <a href="{{ request()->fullUrlWithQuery(['city' => null]) }}" class="ml-1 text-blue-600 hover:text-blue-800">×</a>
                        </span>
                    @endif

                    @if(request('listing_type'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-white border border-blue-200">
                            {{ request('listing_type') == 'sale' ? 'Prodaja' : 'Izdavanje' }}
                            <a href="{{ request()->fullUrlWithQuery(['listing_type' => null]) }}" class="ml-1 text-blue-600 hover:text-blue-800">×</a>
                        </span>
                    @endif

                    @if(request('rooms'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-white border border-blue-200">
                            {{ request('rooms') }} sobe
                            <a href="{{ request()->fullUrlWithQuery(['rooms' => null]) }}" class="ml-1 text-blue-600 hover:text-blue-800">×</a>
                        </span>
                    @endif

                    <a href="{{ route('listings.index') }}" class="text-xs text-red-600 hover:text-red-700 font-medium ml-2">
                        <i class="fas fa-times-circle"></i> Obriši sve
                    </a>
                </div>
            </div>
            @endif

            <!-- Toolbar: View Toggle, Sort, Count -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
                <div class="flex items-center justify-between">
                    <!-- Results Count -->
                    <div class="text-sm text-gray-600">
                        <strong>{{ $listings->total() }}</strong> oglasa
                    </div>

                    <div class="flex items-center gap-4">
                        <!-- View Toggle -->
                        <div class="flex items-center gap-2">
                            <button 
                                onclick="setView('grid')" 
                                id="gridViewBtn"
                                class="p-2 rounded hover:bg-gray-100 view-toggle active"
                                title="Grid prikaz"
                            >
                                <i class="fas fa-th-large"></i>
                            </button>
                            <button 
                                onclick="setView('list')" 
                                id="listViewBtn"
                                class="p-2 rounded hover:bg-gray-100 view-toggle"
                                title="Lista prikaz"
                            >
                                <i class="fas fa-list"></i>
                            </button>
                        </div>

                        <!-- Sort Dropdown -->
                        <form action="{{ route('listings.index') }}" method="GET" id="sortForm">
                            @foreach(request()->except('sort') as $key => $value)
                                @if(is_array($value))
                                    @foreach($value as $v)
                                        <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            
                            <select name="sort" onchange="document.getElementById('sortForm').submit()" 
                                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Najnovije</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Najstarije</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Cena ↑</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Cena ↓</option>
                                <option value="area_asc" {{ request('sort') == 'area_asc' ? 'selected' : '' }}>Površina ↑</option>
                                <option value="area_desc" {{ request('sort') == 'area_desc' ? 'selected' : '' }}>Površina ↓</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Listings Grid/List -->
            @if($listings->count() > 0)
                <div id="listingsContainer" class="grid-view">
                    @foreach($listings as $listing)
                        <div>
                            @include('partials.listing-card', ['listing' => $listing])
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $listings->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-2xl font-semibold text-gray-700 mb-2">Nema rezultata</h3>
                    <p class="text-gray-500 mb-6">Pokušajte sa drugačijim filterima</p>
                    <a href="{{ route('listings.index') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                        Pogledaj sve oglase
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
 

<style>
/* Grid View - Equal height cards */
#listingsContainer.grid-view {
    display: grid;
    grid-template-columns: repeat(1, 1fr);
    gap: 1.5rem;
}

@media (min-width: 768px) {
    #listingsContainer.grid-view {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1280px) {
    #listingsContainer.grid-view {
        grid-template-columns: repeat(3, 1fr);
    }
}

#listingsContainer.grid-view > div {
    height: 100%;
}

/* List View - Horizontal Layout (ALL DEVICES) */
#listingsContainer.list-view {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

#listingsContainer.list-view > div {
    width: 100%;
}

/* List View Card Modifications - ALWAYS HORIZONTAL */
#listingsContainer.list-view .listing-card {
    flex-direction: row !important;
    height: auto !important;
}

#listingsContainer.list-view .listing-card-link {
    flex-direction: row !important;
}

/* Desktop Image Size */
#listingsContainer.list-view .listing-card-image {
    width: 280px;
    height: 200px;
    flex-shrink: 0;
}

/* Mobile: Smaller image but still horizontal */
@media (max-width: 767px) {
    #listingsContainer.list-view .listing-card-image {
        width: 120px;
        height: 160px;
    }
    
    #listingsContainer.list-view .listing-card-content {
        padding: 0.75rem;
    }
    
    #listingsContainer.list-view .listing-card-content h3 {
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }
    
    #listingsContainer.list-view .listing-card-content .text-xl {
        font-size: 1rem;
    }
    
    #listingsContainer.list-view .listing-card-details {
        font-size: 0.75rem;
        gap: 0.5rem;
    }
}

#listingsContainer.list-view .listing-card-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 1.5rem;
}

#listingsContainer.list-view .listing-card-content h3 {
    font-size: 1.25rem;
    min-height: auto;
    line-clamp: 1;
    -webkit-line-clamp: 1;
}

#listingsContainer.list-view .listing-card-details {
    flex-wrap: wrap;
    gap: 1rem;
}

/* View Toggle Active State */
.view-toggle.active {
    background-color: #3B82F6;
    color: white;
}

.view-toggle {
    transition: all 0.2s;
}
</style>

<script>
function setView(view) {
    const container = document.getElementById('listingsContainer');
    const gridBtn = document.getElementById('gridViewBtn');
    const listBtn = document.getElementById('listViewBtn');
    
    console.log('Setting view to:', view); // Debug
    
    if (view === 'grid') {
        container.classList.remove('list-view');
        container.classList.add('grid-view');
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
        localStorage.setItem('listingsView', 'grid');
    } else {
        container.classList.remove('grid-view');
        container.classList.add('list-view');
        listBtn.classList.add('active');
        gridBtn.classList.remove('active');
        localStorage.setItem('listingsView', 'list');
    }
    
    console.log('Container classes:', container.className); // Debug
}

// Remember user's view preference
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('listingsView') || 'grid';
    console.log('Loaded saved view:', savedView); // Debug
    setView(savedView);
});
</script>

<script>
function toggleAdvancedFilters() {
    const filters = document.getElementById('advancedFilters');
    const toggleText = document.getElementById('advancedToggleText');
    
    if (filters.classList.contains('hidden')) {
        filters.classList.remove('hidden');
        toggleText.textContent = 'Sakrij napredne filtere';
    } else {
        filters.classList.add('hidden');
        toggleText.textContent = 'Prikaži napredne filtere';
    }
}

// Auto-show advanced filters if any are active
document.addEventListener('DOMContentLoaded', function() {
    const hasAdvancedFilters = {{ request()->hasAny(['city', 'price_min', 'price_max', 'area_min', 'area_max', 'bathrooms', 'floor', 'year_min', 'year_max', 'features']) ? 'true' : 'false' }};
    
    if (hasAdvancedFilters) {
        toggleAdvancedFilters();
    }
});
</script>

<script>
$(function() {
    $('#city-autocomplete').autocomplete({
        source: '/api/cities', // or whatever endpoint you're using
        minLength: 2,
        select: function(event, ui) {
            $(this).val(ui.item.value);
            this.form.submit(); // auto-submit after selecting
        }
    });
});
</script>




@endsection