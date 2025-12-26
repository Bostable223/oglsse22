{{-- resources/views/components/search-widget.blade.php --}}
@props(['layout' => 'hero', 'categories' => [], 'locations' => []])

@php
    $isHero = $layout === 'hero';
    $isSidebar = $layout === 'sidebar';
@endphp

@if($isHero)
    {{-- Hero Layout (Homepage) --}}
    <div class="w-full max-w-xl bg-white rounded-2xl shadow-2xl p-6 md:p-8">
        <form action="{{ route('listings.index') }}" method="GET">
            
            {{-- Row 1: Category and Listing Type --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                {{-- Category Select --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-th-large text-blue-600 mr-1"></i>
                        Kategorija
                    </label>
                    <select name="category" 
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-700 font-medium">
                        <option value="">Sve kategorije</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Listing Type --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-tag text-blue-600 mr-1"></i>
                        Tip oglasa
                    </label>
                    <div class="grid grid-cols-2 gap-2 h-[52px]">
                        <button type="button" 
                                onclick="selectListingType(event, 'sale')"
                                class="listing-type-btn px-4 border-2 rounded-xl font-semibold transition-all h-full
                                       {{ request('listing_type') == 'sale' ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:border-blue-300' }}">
                            Prodaja
                        </button>
                        <button type="button" 
                                onclick="selectListingType(event, 'rent')"
                                class="listing-type-btn px-4 border-2 rounded-xl font-semibold transition-all h-full
                                       {{ request('listing_type') == 'rent' ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:border-blue-300' }}">
                            Izdavanje
                        </button>
                    </div>
                    <input type="hidden" name="listing_type" id="listingTypeInput" value="{{ request('listing_type') }}">
                </div>
            </div>

            {{-- Row 2: Location --}}
            <div class="mb-4 relative">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-map-marker-alt text-blue-600 mr-1"></i>
                    Lokacija
                </label>
                <input type="text" 
                       name="city" 
                       id="locationInput"
                       value="{{ request('city') }}"
                       placeholder="Unesite grad ili opštinu..."
                       autocomplete="off"
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                
                {{-- Autocomplete Dropdown --}}
                <div id="locationDropdown" 
                     class="hidden absolute z-10 w-full mt-1 bg-white border-2 border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                    {{-- Populated by JavaScript --}}
                </div>
            </div>

            {{-- Row 3: Price, Area, Rooms --}}
            <div class="grid grid-cols-3 gap-3 mb-4">
                {{-- Price --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-euro-sign text-blue-600 mr-1"></i>
                        Cena
                    </label>
                    <input type="number" 
                           name="price_max" 
                           value="{{ request('price_max') }}"
                           placeholder="Maks"
                           class="w-full px-3 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>

                {{-- Area --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-ruler-combined text-blue-600 mr-1"></i>
                        m²
                    </label>
                    <input type="number" 
                           name="area_min" 
                           value="{{ request('area_min') }}"
                           placeholder="Min"
                           class="w-full px-3 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>

                {{-- Rooms --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-bed text-blue-600 mr-1"></i>
                        Sobe
                    </label>
                    <select name="rooms" 
                            class="w-full px-3 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="">Sve</option>
                        <option value="1" {{ request('rooms') == '1' ? 'selected' : '' }}>1</option>
                        <option value="2" {{ request('rooms') == '2' ? 'selected' : '' }}>2</option>
                        <option value="3" {{ request('rooms') == '3' ? 'selected' : '' }}>3</option>
                        <option value="4" {{ request('rooms') == '4' ? 'selected' : '' }}>4</option>
                        <option value="5" {{ request('rooms') == '5' ? 'selected' : '' }}>5+</option>
                    </select>
                </div>
            </div>

            {{-- Search Button --}}
            <button type="submit" 
                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-4 rounded-xl hover:from-blue-700 hover:to-blue-800 font-bold text-lg shadow-lg transition-all transform hover:scale-105">
                <i class="fas fa-search mr-2"></i> Pretraži
            </button>
        </form>
    </div>

@elseif($isSidebar)
    {{-- Sidebar Layout (Listings Page) --}}
    <div class="bg-white rounded-lg shadow-sm p-6 sticky top-24">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-filter text-blue-600 mr-2"></i>
            Filteri
        </h3>

        <form action="{{ route('listings.index') }}" method="GET" id="sidebarSearchForm">
            
            {{-- Category Filter --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Kategorija</label>
                <select name="category" 
                        onchange="this.form.submit()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Sve kategorije</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Location Filter --}}
            <div class="mb-4 relative">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Lokacija</label>
                <input type="text" 
                       name="city" 
                       id="sidebarLocationInput"
                       value="{{ request('city') }}"
                       placeholder="Grad ili opština..."
                       autocomplete="off"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                
                <div id="sidebarLocationDropdown" 
                     class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                </div>
            </div>

            {{-- Listing Type Filter --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tip oglasa</label>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" 
                            onclick="selectSidebarListingType(event, 'sale')"
                            class="sidebar-type-btn px-3 py-2 text-sm border rounded-lg font-medium transition-all
                                   {{ request('listing_type') == 'sale' ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:border-blue-300' }}">
                        Prodaja
                    </button>
                    <button type="button" 
                            onclick="selectSidebarListingType(event, 'rent')"
                            class="sidebar-type-btn px-3 py-2 text-sm border rounded-lg font-medium transition-all
                                   {{ request('listing_type') == 'rent' ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:border-blue-300' }}">
                        Izdavanje
                    </button>
                </div>
                <input type="hidden" name="listing_type" id="sidebarListingTypeInput" value="{{ request('listing_type') }}">
            </div>

            {{-- Price Range --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Cena</label>
                <div class="grid grid-cols-2 gap-2">
                    <input type="number" 
                           name="price_min" 
                           value="{{ request('price_min') }}"
                           placeholder="Od"
                           class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <input type="number" 
                           name="price_max" 
                           value="{{ request('price_max') }}"
                           placeholder="Do"
                           class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>
            </div>

            {{-- Area Range --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Površina (m²)</label>
                <div class="grid grid-cols-2 gap-2">
                    <input type="number" 
                           name="area_min" 
                           value="{{ request('area_min') }}"
                           placeholder="Od"
                           class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <input type="number" 
                           name="area_max" 
                           value="{{ request('area_max') }}"
                           placeholder="Do"
                           class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>
            </div>

            {{-- Rooms Filter --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Broj soba</label>
                <select name="rooms" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <option value="">Sve</option>
                    <option value="1" {{ request('rooms') == '1' ? 'selected' : '' }}>1</option>
                    <option value="2" {{ request('rooms') == '2' ? 'selected' : '' }}>2</option>
                    <option value="3" {{ request('rooms') == '3' ? 'selected' : '' }}>3</option>
                    <option value="4" {{ request('rooms') == '4' ? 'selected' : '' }}>4</option>
                    <option value="5" {{ request('rooms') == '5' ? 'selected' : '' }}>5+</option>
                </select>
            </div>

            {{-- Floor Filter --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Sprat</label>
                <select name="floor" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <option value="">Sve</option>
                    <option value="0" {{ request('floor') == '0' ? 'selected' : '' }}>Prizemlje</option>
                    <option value="1-3" {{ request('floor') == '1-3' ? 'selected' : '' }}>1-3</option>
                    <option value="4-6" {{ request('floor') == '4-6' ? 'selected' : '' }}>4-6</option>
                    <option value="7+" {{ request('floor') == '7+' ? 'selected' : '' }}>7+</option>
                </select>
            </div>

            {{-- Features Filter --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Karakteristike</label>
                <div class="space-y-2">
                    @php
                        $availableFeatures = ['Parking', 'Lift', 'Balkon', 'Klima', 'Centralno grejanje', 'Renoviran'];
                        $selectedFeatures = request('features', []);
                    @endphp
                    @foreach($availableFeatures as $feature)
                        <label class="flex items-center text-sm">
                            <input type="checkbox" 
                                   name="features[]" 
                                   value="{{ $feature }}"
                                   {{ in_array($feature, $selectedFeatures) ? 'checked' : '' }}
                                   class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-gray-700">{{ $feature }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="space-y-2">
                <button type="submit" 
                        class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-semibold transition-colors">
                    <i class="fas fa-search mr-2"></i> Primeni filtere
                </button>
                <a href="{{ route('listings.index') }}" 
                   class="block w-full text-center bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 font-semibold transition-colors">
                    <i class="fas fa-redo mr-2"></i> Resetuj
                </a>
            </div>
        </form>
    </div>
@endif

@push('scripts')
<script>
// Hero Layout Functions
@if($isHero)
// Location Autocomplete Data for Hero
const heroLocations = @json($locations ?? []);

function selectListingType(event, type) {
    event.preventDefault();
    
    const allBtns = document.querySelectorAll('.listing-type-btn');
    const input = document.getElementById('listingTypeInput');
    const clickedBtn = event.currentTarget;
    
    if (input.value === type) {
        clickedBtn.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
        clickedBtn.classList.add('border-gray-300', 'text-gray-700');
        input.value = '';
    } else {
        allBtns.forEach(btn => {
            btn.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
            btn.classList.add('border-gray-300', 'text-gray-700');
        });
        
        clickedBtn.classList.remove('border-gray-300', 'text-gray-700');
        clickedBtn.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
        input.value = type;
    }
}

// Location Autocomplete for Hero - Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    const heroLocationInput = document.getElementById('locationInput');
    const heroLocationDropdown = document.getElementById('locationDropdown');

    if (heroLocationInput && heroLocationDropdown && heroLocations) {
        console.log('Hero autocomplete initialized with', heroLocations.length, 'locations');
        
        heroLocationInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            if (searchTerm.length < 2) {
                heroLocationDropdown.classList.add('hidden');
                return;
            }
            
            // Filter locations (now all strings)
            const filtered = heroLocations.filter(loc => 
                loc && loc.toLowerCase().includes(searchTerm)
            );
            
            console.log('Filtered locations:', filtered.length);
            
            if (filtered.length === 0) {
                heroLocationDropdown.classList.add('hidden');
                return;
            }
            
            // Display results
            heroLocationDropdown.innerHTML = filtered.slice(0, 10).map(loc => 
                `<div class="px-4 py-3 hover:bg-blue-50 cursor-pointer location-item border-b border-gray-100 last:border-0" data-location="${loc}">
                    <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>${loc}
                </div>`
            ).join('');
            
            heroLocationDropdown.classList.remove('hidden');
            
            document.querySelectorAll('.location-item').forEach(item => {
                item.addEventListener('click', function() {
                    heroLocationInput.value = this.dataset.location;
                    heroLocationDropdown.classList.add('hidden');
                });
            });
        });

        document.addEventListener('click', function(e) {
            if (!heroLocationInput.contains(e.target) && !heroLocationDropdown.contains(e.target)) {
                heroLocationDropdown.classList.add('hidden');
            }
        });
    }
});
@endif

// Sidebar Layout Functions
@if($isSidebar)
// Location Autocomplete Data for Sidebar
const sidebarLocations = @json($locations ?? []);

function selectSidebarListingType(event, type) {
    event.preventDefault();
    
    const allBtns = document.querySelectorAll('.sidebar-type-btn');
    const input = document.getElementById('sidebarListingTypeInput');
    const clickedBtn = event.currentTarget;
    const form = document.getElementById('sidebarSearchForm');
    
    if (input.value === type) {
        clickedBtn.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
        clickedBtn.classList.add('border-gray-300', 'text-gray-700');
        input.value = '';
    } else {
        allBtns.forEach(btn => {
            btn.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
            btn.classList.add('border-gray-300', 'text-gray-700');
        });
        
        clickedBtn.classList.remove('border-gray-300', 'text-gray-700');
        clickedBtn.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
        input.value = type;
    }
    
    // Auto-submit form
    form.submit();
}

// Location Autocomplete for Sidebar - Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    const sidebarLocationInput = document.getElementById('sidebarLocationInput');
    const sidebarLocationDropdown = document.getElementById('sidebarLocationDropdown');

    if (sidebarLocationInput && sidebarLocationDropdown && sidebarLocations) {
        console.log('Sidebar autocomplete initialized with', sidebarLocations.length, 'locations');
        
        sidebarLocationInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            if (searchTerm.length < 2) {
                sidebarLocationDropdown.classList.add('hidden');
                return;
            }
            
            // Filter locations (now all strings)
            const filtered = sidebarLocations.filter(loc => 
                loc && loc.toLowerCase().includes(searchTerm)
            );
            
            if (filtered.length === 0) {
                sidebarLocationDropdown.classList.add('hidden');
                return;
            }
            
            // Display results
            sidebarLocationDropdown.innerHTML = filtered.slice(0, 8).map(loc => 
                `<div class="px-3 py-2 hover:bg-blue-50 cursor-pointer sidebar-location-item text-sm border-b border-gray-100 last:border-0" data-location="${loc}">
                    <i class="fas fa-map-marker-alt text-blue-600 mr-2 text-xs"></i>${loc}
                </div>`
            ).join('');
            
            sidebarLocationDropdown.classList.remove('hidden');
            
            document.querySelectorAll('.sidebar-location-item').forEach(item => {
                item.addEventListener('click', function() {
                    sidebarLocationInput.value = this.dataset.location;
                    sidebarLocationDropdown.classList.add('hidden');
                });
            });
        });

        document.addEventListener('click', function(e) {
            if (!sidebarLocationInput.contains(e.target) && !sidebarLocationDropdown.contains(e.target)) {
                sidebarLocationDropdown.classList.add('hidden');
            }
        });
    }
});
@endif
</script>
@endpush