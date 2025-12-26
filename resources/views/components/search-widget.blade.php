@props([
    'layout' => 'hero', // 'hero' for homepage, 'compact' for listings page
    'categories' => [],
    'showAdvanced' => false
])

<div x-data="searchWidget()" 
     x-init="init()"
     class="{{ $layout === 'hero' ? 'w-full' : 'w-full' }}">
    
    <form action="{{ route('listings.index') }}" 
          method="GET" 
          @submit="saveSearch()"
          class="{{ $layout === 'hero' 
              ? 'bg-white rounded-2xl shadow-2xl p-6 w-full' 
              : 'bg-white rounded-lg shadow-sm p-4 border border-gray-200' }}">

        <!-- Main Search Row -->
        <div class="grid grid-cols-1 {{ $layout === 'hero' ? 'lg:grid-cols-4' : 'md:grid-cols-4' }} gap-4 mb-4">
            
            <!-- Location with Autocomplete -->
            <div class="relative">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-map-marker-alt text-blue-600 mr-1"></i>
                    Lokacija
                </label>
                <input 
                    type="text" 
                    name="city" 
                    x-model="search.city"
                    @input="searchLocations()"
                    @focus="showLocationDropdown = true"
                    placeholder="Grad, opština..."
                    autocomplete="off"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                
                <!-- Autocomplete Dropdown -->
                <div x-show="showLocationDropdown && filteredLocations.length > 0"
                     @click.away="showLocationDropdown = false"
                     x-transition
                     class="absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto">
                    <template x-for="location in filteredLocations" :key="location">
                        <div @click="selectLocation(location)"
                             class="px-4 py-2 hover:bg-blue-50 cursor-pointer flex items-center">
                            <i class="fas fa-map-marker-alt text-blue-600 mr-2 text-sm"></i>
                            <span x-text="location"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Property Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-home text-blue-600 mr-1"></i>
                    Tip nekretnine
                </label>
                <select name="category" 
                        x-model="search.category"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Sve kategorije</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Transaction Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-exchange-alt text-blue-600 mr-1"></i>
                    Transakcija
                </label>
                <div class="flex gap-2">
                    <button type="button" 
                            @click="toggleListingType('sale')"
                            :class="search.listing_type === 'sale' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300'"
                            class="flex-1 px-4 py-3 rounded-lg font-medium transition-all hover:shadow">
                        Prodaja
                    </button>
                    <button type="button" 
                            @click="toggleListingType('rent')"
                            :class="search.listing_type === 'rent' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300'"
                            class="flex-1 px-4 py-3 rounded-lg font-medium transition-all hover:shadow">
                        Izdavanje
                    </button>
                </div>
                <input type="hidden" name="listing_type" x-model="search.listing_type">
            </div>

            <!-- Search Button -->
            <div class="flex items-end">
                <button type="submit" 
                        class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold text-lg transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-search"></i>
                    <span>Pretraži</span>
                    <span x-show="resultCount !== null" 
                          x-text="`(${resultCount})`"
                          class="text-sm"></span>
                </button>
            </div>
        </div>

        <!-- Price Range Slider -->
        <div class="mb-4">
            <div class="flex items-center justify-between mb-2">
                <label class="text-sm font-medium text-gray-700">
                    <i class="fas fa-dollar-sign text-blue-600 mr-1"></i>
                    Cena
                </label>
                <span class="text-sm text-gray-600">
                    <span x-text="formatPrice(search.price_min)"></span> - 
                    <span x-text="formatPrice(search.price_max)"></span>
                </span>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <input type="number" 
                           name="price_min" 
                           x-model="search.price_min"
                           @input="debouncedCountResults()"
                           placeholder="Min"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <input type="number" 
                           name="price_max" 
                           x-model="search.price_max"
                           @input="debouncedCountResults()"
                           placeholder="Max"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- Advanced Filters Toggle -->
        <div class="border-t border-gray-200 pt-4">
            <button type="button" 
                    @click="showAdvancedFilters = !showAdvancedFilters"
                    class="flex items-center justify-between w-full text-left text-gray-700 hover:text-blue-600 transition-colors">
                <span class="font-medium">
                    <i class="fas fa-sliders-h mr-2"></i>
                    Dodatni filteri
                </span>
                <i class="fas" :class="showAdvancedFilters ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>

            <!-- Advanced Filters Content -->
            <div x-show="showAdvancedFilters" 
                 x-collapse
                 class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                
                <!-- Area Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Površina (m²)</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" 
                               name="area_min" 
                               x-model="search.area_min"
                               placeholder="Min"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        <input type="number" 
                               name="area_max" 
                               x-model="search.area_max"
                               placeholder="Max"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                </div>

                <!-- Rooms -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Broj soba</label>
                    <select name="rooms" 
                            x-model="search.rooms"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="">Bilo koji</option>
                        <option value="0.5">Garsonjera</option>
                        <option value="1">1 soba</option>
                        <option value="1.5">1.5 soba</option>
                        <option value="2">2 sobe</option>
                        <option value="2.5">2.5 soba</option>
                        <option value="3">3 sobe</option>
                        <option value="3.5">3.5 soba</option>
                        <option value="4">4+ soba</option>
                    </select>
                </div>

                <!-- Floor -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sprat</label>
                    <select name="floor" 
                            x-model="search.floor"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="">Bilo koji</option>
                        <option value="0">Prizemlje</option>
                        <option value="1-3">1-3 sprat</option>
                        <option value="4-6">4-6 sprat</option>
                        <option value="7+">7+ sprat</option>
                    </select>
                </div>

                <!-- Features Checkboxes -->
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Karakteristike</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="features[]" value="parking" 
                                   class="mr-2 rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-sm">Parking</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="features[]" value="lift" 
                                   class="mr-2 rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-sm">Lift</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="features[]" value="balcony" 
                                   class="mr-2 rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-sm">Balkon</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="features[]" value="garage" 
                                   class="mr-2 rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-sm">Garaža</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="features[]" value="furnished" 
                                   class="mr-2 rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-sm">Namešten</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="features[]" value="ac" 
                                   class="mr-2 rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-sm">Klima</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="features[]" value="heating" 
                                   class="mr-2 rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-sm">Centralno grejanje</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="features[]" value="terrace" 
                                   class="mr-2 rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-sm">Terasa</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200">
            <button type="button" 
                    @click="resetFilters()"
                    class="text-gray-600 hover:text-gray-900 font-medium text-sm">
                <i class="fas fa-redo mr-1"></i>
                Resetuj filtere
            </button>

            @auth
            <button type="button" 
                    @click="saveCurrentSearch()"
                    class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                <i class="fas fa-bookmark mr-1"></i>
                Sačuvaj pretragu
            </button>
            @endauth
        </div>
    </form>

    <!-- Recent Searches (Optional) -->
    @auth
    <div x-show="recentSearches.length > 0" class="mt-4">
        <div class="text-sm text-gray-600 mb-2">Nedavne pretrage:</div>
        <div class="flex flex-wrap gap-2">
            <template x-for="search in recentSearches.slice(0, 3)" :key="search.id">
                <button @click="loadSavedSearch(search)"
                        class="text-sm bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded-full transition-colors">
                    <i class="fas fa-clock mr-1"></i>
                    <span x-text="search.name"></span>
                </button>
            </template>
        </div>
    </div>
    @endauth
</div>

<script>
function searchWidget() {
    return {
        search: {
            city: '{{ request("city", "") }}',
            category: '{{ request("category", "") }}',
            listing_type: '{{ request("listing_type", "") }}',
            price_min: '{{ request("price_min", "") }}',
            price_max: '{{ request("price_max", "") }}',
            area_min: '{{ request("area_min", "") }}',
            area_max: '{{ request("area_max", "") }}',
            rooms: '{{ request("rooms", "") }}',
            floor: '{{ request("floor", "") }}'
        },
        
        locations: @json($locations ?? []),
        filteredLocations: [],
        showLocationDropdown: false,
        showAdvancedFilters: {{ $showAdvanced ? 'true' : 'false' }},
        resultCount: null,
        recentSearches: [],
        debounceTimer: null,

        init() {
            this.loadRecentSearches();
            this.parseUrlParams();
        },

        parseUrlParams() {
            const params = new URLSearchParams(window.location.search);
            if (params.has('city')) this.search.city = params.get('city');
            if (params.has('category')) this.search.category = params.get('category');
            if (params.has('listing_type')) this.search.listing_type = params.get('listing_type');
        },

        searchLocations() {
            if (!this.search.city || this.search.city.length < 2) {
                this.filteredLocations = [];
                return;
            }

            const term = this.search.city.toLowerCase();
            this.filteredLocations = this.locations
                .filter(loc => loc.toLowerCase().includes(term))
                .slice(0, 10);
            
            this.showLocationDropdown = this.filteredLocations.length > 0;
        },

        selectLocation(location) {
            this.search.city = location;
            this.showLocationDropdown = false;
            this.debouncedCountResults();
        },

        toggleListingType(type) {
            this.search.listing_type = this.search.listing_type === type ? '' : type;
            this.debouncedCountResults();
        },

        debouncedCountResults() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                this.countResults();
            }, 500);
        },

        async countResults() {
            try {
                const params = new URLSearchParams();
                Object.keys(this.search).forEach(key => {
                    if (this.search[key]) {
                        params.append(key, this.search[key]);
                    }
                });

                const response = await fetch(`/api/listings/count?${params.toString()}`);
                const data = await response.json();
                this.resultCount = data.count;
            } catch (error) {
                console.error('Error counting results:', error);
            }
        },

        resetFilters() {
            this.search = {
                city: '',
                category: '',
                listing_type: '',
                price_min: '',
                price_max: '',
                area_min: '',
                area_max: '',
                rooms: '',
                floor: ''
            };
            this.resultCount = null;
        },

        formatPrice(price) {
            if (!price) return '0';
            return new Intl.NumberFormat('sr-RS').format(price);
        },

        loadRecentSearches() {
            const saved = localStorage.getItem('recentSearches');
            if (saved) {
                this.recentSearches = JSON.parse(saved);
            }
        },

        saveSearch() {
            const searchData = {
                id: Date.now(),
                name: this.generateSearchName(),
                params: {...this.search},
                timestamp: new Date().toISOString()
            };

            this.recentSearches.unshift(searchData);
            this.recentSearches = this.recentSearches.slice(0, 5); // Keep last 5
            localStorage.setItem('recentSearches', JSON.stringify(this.recentSearches));
        },

        generateSearchName() {
            let parts = [];
            if (this.search.listing_type) parts.push(this.search.listing_type === 'sale' ? 'Prodaja' : 'Izdavanje');
            if (this.search.category) parts.push('Kategorija');
            if (this.search.city) parts.push(this.search.city);
            return parts.length > 0 ? parts.join(' - ') : 'Pretraga';
        },

        async saveCurrentSearch() {
            // TODO: Save to database via API
            if (typeof showToast === 'function') {
                showToast('Pretraga je sačuvana!', 'success');
            }
            this.saveSearch();
        },

        loadSavedSearch(savedSearch) {
            this.search = {...savedSearch.params};
            this.$nextTick(() => {
                this.$el.querySelector('form').submit();
            });
        }
    };
}
</script>