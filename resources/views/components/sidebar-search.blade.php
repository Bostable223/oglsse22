@props(['categories', 'allLocations'])

<div x-data="sidebarSearch()" class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-bold text-gray-900">Filteri</h3>
        <button type="button" 
                @click="resetFilters()"
                class="text-sm text-blue-600 hover:text-blue-700 font-medium">
            <i class="fas fa-redo mr-1"></i> Reset
        </button>
    </div>
    
    <form id="sidebar-search-form" method="GET" action="{{ route('listings.index') }}">
        
        <!-- Location -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-map-marker-alt text-blue-600 mr-1"></i>
                Lokacija
            </label>
            <input 
                type="text" 
                name="city" 
                x-model="filters.city"
                @input="debouncedSubmit()"
                placeholder="Grad..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
            >
        </div>

        <!-- Category -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-home text-blue-600 mr-1"></i>
                Tip nekretnine
            </label>
            <select name="category" 
                    x-model="filters.category"
                    @change="submitForm()"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                <option value="">Sve kategorije</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Transaction Type -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-exchange-alt text-blue-600 mr-1"></i>
                Transakcija
            </label>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" 
                        @click="toggleListingType('sale')"
                        :class="filters.listing_type === 'sale' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300'"
                        class="px-3 py-2 rounded-lg font-medium text-sm transition-all">
                    Prodaja
                </button>
                <button type="button" 
                        @click="toggleListingType('rent')"
                        :class="filters.listing_type === 'rent' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300'"
                        class="px-3 py-2 rounded-lg font-medium text-sm transition-all">
                    Izdavanje
                </button>
            </div>
            <input type="hidden" name="listing_type" x-model="filters.listing_type">
        </div>

        <!-- Price Range -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-dollar-sign text-blue-600 mr-1"></i>
                Cena
            </label>
            <div class="grid grid-cols-2 gap-2">
                <input 
                    type="number" 
                    name="price_min" 
                    x-model="filters.price_min"
                    @input="debouncedSubmit()"
                    placeholder="Min"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                >
                <input 
                    type="number" 
                    name="price_max" 
                    x-model="filters.price_max"
                    @input="debouncedSubmit()"
                    placeholder="Max"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                >
            </div>
        </div>

        <!-- Area -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-ruler-combined text-blue-600 mr-1"></i>
                Površina (m²)
            </label>
            <div class="grid grid-cols-2 gap-2">
                <input 
                    type="number" 
                    name="area_min" 
                    x-model="filters.area_min"
                    @input="debouncedSubmit()"
                    placeholder="Min"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                >
                <input 
                    type="number" 
                    name="area_max" 
                    x-model="filters.area_max"
                    @input="debouncedSubmit()"
                    placeholder="Max"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                >
            </div>
        </div>

        <!-- Rooms -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-door-open text-blue-600 mr-1"></i>
                Broj soba
            </label>
            <select name="rooms" 
                    x-model="filters.rooms"
                    @change="submitForm()"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                <option value="">Bilo koji</option>
                <option value="0.5">Garsonjera</option>
                <option value="1">1 soba</option>
                <option value="1.5">1.5 soba</option>
                <option value="2">2 sobe</option>
                <option value="2.5">2.5 soba</option>
                <option value="3">3 sobe</option>
                <option value="3.5">3.5 soba</option>
                <option value="4">4+ sobe</option>
            </select>
        </div>

        <!-- Advanced Filters Toggle -->
        <div class="mb-4">
            <button type="button" 
                    @click="showAdvanced = !showAdvanced"
                    class="flex items-center justify-between w-full text-left text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors">
                <span><i class="fas fa-sliders-h mr-2"></i>Dodatni filteri</span>
                <i class="fas" :class="showAdvanced ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
        </div>

        <!-- Advanced Filters -->
        <div x-show="showAdvanced" 
             x-collapse
             class="space-y-4 border-t border-gray-200 pt-4">
            
            <!-- Floor -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sprat</label>
                <select name="floor" 
                        x-model="filters.floor"
                        @change="submitForm()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    <option value="">Bilo koji</option>
                    <option value="0">Prizemlje</option>
                    <option value="1-3">1-3 sprat</option>
                    <option value="4-6">4-6 sprat</option>
                    <option value="7+">7+ sprat</option>
                </select>
            </div>

            <!-- Features -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Karakteristike</label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="features[]" 
                               value="Parking"
                               {{ in_array('Parking', request('features', [])) ? 'checked' : '' }}
                               class="mr-2 rounded text-blue-600 focus:ring-blue-500">
                        <span class="text-sm">Parking</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="features[]" 
                               value="Lift"
                               {{ in_array('Lift', request('features', [])) ? 'checked' : '' }}
                               class="mr-2 rounded text-blue-600 focus:ring-blue-500">
                        <span class="text-sm">Lift</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="features[]" 
                               value="Balkon"
                               {{ in_array('Balkon', request('features', [])) ? 'checked' : '' }}
                               class="mr-2 rounded text-blue-600 focus:ring-blue-500">
                        <span class="text-sm">Balkon</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="features[]" 
                               value="Garaža"
                               {{ in_array('Garaža', request('features', [])) ? 'checked' : '' }}
                               class="mr-2 rounded text-blue-600 focus:ring-blue-500">
                        <span class="text-sm">Garaža</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="features[]" 
                               value="Klima"
                               {{ in_array('Klima', request('features', [])) ? 'checked' : '' }}
                               class="mr-2 rounded text-blue-600 focus:ring-blue-500">
                        <span class="text-sm">Klima</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="features[]" 
                               value="Centralno grejanje"
                               {{ in_array('Centralno grejanje', request('features', [])) ? 'checked' : '' }}
                               class="mr-2 rounded text-blue-600 focus:ring-blue-500">
                        <span class="text-sm">Centralno grejanje</span>
                    </label>
                </div>
            </div>

            <!-- Submit Button for Features -->
            <button type="button"
                    @click="submitForm()"
                    class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium text-sm transition-colors">
                <i class="fas fa-check mr-2"></i>
                Primeni filtere
            </button>
        </div>

        <!-- Result Count -->
        <div class="mt-6 p-3 bg-blue-50 rounded-lg text-center">
            <span class="text-sm text-gray-600">Rezultata: </span>
            <span class="text-lg font-bold text-blue-600" x-text="resultCount"></span>
        </div>
    </form>
</div>

<script>
function sidebarSearch() {
    return {
        filters: {
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
        showAdvanced: {{ request()->hasAny(['floor', 'features']) ? 'true' : 'false' }},
        resultCount: {{ isset($listings) ? $listings->total() : 0 }},
        debounceTimer: null,

        toggleListingType(type) {
            // Toggle: if same type clicked, clear it; otherwise set it
            if (this.filters.listing_type === type) {
                this.filters.listing_type = '';
            } else {
                this.filters.listing_type = type;
            }
            
            // Submit immediately
            this.$nextTick(() => {
                this.submitForm();
            });
        },

        debouncedSubmit() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                this.submitForm();
            }, 800);
        },

        submitForm() {
            const form = document.getElementById('sidebar-search-form');
            
            // Remove empty inputs to keep URL clean
            const inputs = form.querySelectorAll('input[type="text"], input[type="number"], input[type="hidden"], select');
            inputs.forEach(input => {
                if (!input.value || input.value === '') {
                    input.setAttribute('data-was-disabled', input.disabled);
                    input.disabled = true;
                }
            });

            // Don't disable checkboxes - they need to maintain their checked state
            form.submit();
        },

        resetFilters() {
            window.location.href = '{{ route("listings.index") }}';
        }
    };
}
</script>