@props(['categories', 'allLocations'])

<div class="bg-white rounded-2xl shadow-2xl p-6 w-full lg:max-w-md">
    <h3 class="text-2xl font-bold text-gray-900 mb-6">Pretraži nekretnine</h3>
    
    <form action="{{ route('listings.index') }}" method="GET" id="hero-search-form">
        <!-- Location with Autocomplete -->
        <div class="mb-4 relative" x-data="{ 
            search: '{{ request('city', '') }}', 
            locations: @js($allLocations),
            filtered: [],
            showDropdown: false,
            searchLocations() {
                if (this.search.length < 2) {
                    this.filtered = [];
                    return;
                }
                const term = this.search.toLowerCase();
                this.filtered = this.locations.filter(loc => 
                    loc.toLowerCase().includes(term)
                ).slice(0, 10);
                this.showDropdown = this.filtered.length > 0;
            },
            selectLocation(loc) {
                this.search = loc;
                this.showDropdown = false;
            }
        }">
            <label class="block text-sm font-medium text-gray-700 mb-2">Lokacija</label>
            <input 
                type="text" 
                name="city" 
                x-model="search"
                @input="searchLocations()"
                @focus="showDropdown = filtered.length > 0"
                placeholder="Grad, opština ili naselje"
                autocomplete="off"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
            <div x-show="showDropdown" 
                 @click.away="showDropdown = false"
                 x-transition
                 class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto">
                <template x-for="loc in filtered" :key="loc">
                    <div @click="selectLocation(loc)"
                         class="px-4 py-2 hover:bg-blue-50 cursor-pointer">
                        <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
                        <span x-text="loc"></span>
                    </div>
                </template>
            </div>
        </div>

        <!-- Property Type and Transaction Type in Same Row -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <!-- Property Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tip nekretnine</label>
                <select name="category" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Sve</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Listing Type (Rent/Sale Buttons) -->
            <div x-data="{ type: '{{ request('listing_type', '') }}' }">
                <label class="block text-sm font-medium text-gray-700 mb-2">Transakcija</label>
                <div class="flex gap-2">
                    <button type="button" 
                            @click="type = type === 'sale' ? '' : 'sale'"
                            :class="type === 'sale' ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700'"
                            class="flex-1 px-3 py-3 border-2 rounded-lg hover:border-blue-500 transition-colors text-center font-medium text-sm">
                        Prodaja
                    </button>
                    <button type="button" 
                            @click="type = type === 'rent' ? '' : 'rent'"
                            :class="type === 'rent' ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700'"
                            class="flex-1 px-3 py-3 border-2 rounded-lg hover:border-blue-500 transition-colors text-center font-medium text-sm">
                        Izdavanje
                    </button>
                </div>
                <input type="hidden" name="listing_type" :value="type">
            </div>
        </div>

        <!-- Price Range -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Min. Cena</label>
                <input 
                    type="number" 
                    name="price_min" 
                    value="{{ request('price_min') }}"
                    placeholder="0"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Max. Cena</label>
                <input 
                    type="number" 
                    name="price_max" 
                    value="{{ request('price_max') }}"
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
                    <option value="20" {{ request('area_min') == 20 ? 'selected' : '' }}>20+ m²</option>
                    <option value="40" {{ request('area_min') == 40 ? 'selected' : '' }}>40+ m²</option>
                    <option value="60" {{ request('area_min') == 60 ? 'selected' : '' }}>60+ m²</option>
                    <option value="80" {{ request('area_min') == 80 ? 'selected' : '' }}>80+ m²</option>
                    <option value="100" {{ request('area_min') == 100 ? 'selected' : '' }}>100+ m²</option>
                    <option value="150" {{ request('area_min') == 150 ? 'selected' : '' }}>150+ m²</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sobe</label>
                <select name="rooms" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Bilo koji</option>
                    <option value="0.5" {{ request('rooms') == 0.5 ? 'selected' : '' }}>0.5</option>
                    <option value="1" {{ request('rooms') == 1 ? 'selected' : '' }}>1</option>
                    <option value="1.5" {{ request('rooms') == 1.5 ? 'selected' : '' }}>1.5</option>
                    <option value="2" {{ request('rooms') == 2 ? 'selected' : '' }}>2</option>
                    <option value="2.5" {{ request('rooms') == 2.5 ? 'selected' : '' }}>2.5</option>
                    <option value="3" {{ request('rooms') == 3 ? 'selected' : '' }}>3</option>
                    <option value="3.5" {{ request('rooms') == 3.5 ? 'selected' : '' }}>3.5</option>
                    <option value="4" {{ request('rooms') == 4 ? 'selected' : '' }}>4+</option>
                </select>
            </div>
        </div>

        <!-- Search Button -->
        <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-lg hover:bg-blue-700 font-semibold text-lg transition-colors">
            <i class="fas fa-search mr-2"></i> Pretraži
        </button>
    </form>
</div>