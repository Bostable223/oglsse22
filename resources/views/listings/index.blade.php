@extends('layouts.app')

@section('title', 'Svi oglasi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Featured Listings Section -->
    @if(!request()->has('search') && !request()->has('category') && $featuredListings->count() > 0)
    <div class="mb-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-3xl font-bold text-gray-900">Istaknuti oglasi</h2>
            <span class="text-yellow-500"><i class="fas fa-star"></i> Premium</span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($featuredListings as $listing)
                @include('partials.listing-card', ['listing' => $listing, 'featured' => true])
            @endforeach
        </div>
    </div>
    @endif

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <h3 class="text-lg font-semibold mb-4">Pretraži i filtriraj</h3>
        
        <form action="{{ route('listings.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Category Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategorija</label>
                    <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Sve kategorije</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- City Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Grad</label>
                    <select name="city" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Svi gradovi</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                                {{ $city }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Listing Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tip</label>
                    <select name="listing_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Sve</option>
                        <option value="sale" {{ request('listing_type') == 'sale' ? 'selected' : '' }}>Prodaja</option>
                        <option value="rent" {{ request('listing_type') == 'rent' ? 'selected' : '' }}>Izdavanje</option>
                    </select>
                </div>

                <!-- Rooms Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sobe</label>
                    <select name="rooms" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Sve</option>
                        <option value="1" {{ request('rooms') == '1' ? 'selected' : '' }}>1</option>
                        <option value="2" {{ request('rooms') == '2' ? 'selected' : '' }}>2</option>
                        <option value="3" {{ request('rooms') == '3' ? 'selected' : '' }}>3</option>
                        <option value="4" {{ request('rooms') == '4' ? 'selected' : '' }}>4+</option>
                    </select>
                </div>

                <!-- Price Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cena od</label>
                    <input 
                        type="number" 
                        name="price_min" 
                        placeholder="0" 
                        value="{{ request('price_min') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cena do</label>
                    <input 
                        type="number" 
                        name="price_max" 
                        placeholder="∞" 
                        value="{{ request('price_max') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <!-- Area Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Površina od (m²)</label>
                    <input 
                        type="number" 
                        name="area_min" 
                        placeholder="0" 
                        value="{{ request('area_min') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Površina do (m²)</label>
                    <input 
                        type="number" 
                        name="area_max" 
                        placeholder="∞" 
                        value="{{ request('area_max') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                </div>
            </div>

            <div class="flex items-center gap-4 mt-6">
                <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-search mr-2"></i> Pretraži
                </button>
                <a href="{{ route('listings.index') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-times mr-2"></i> Obriši filtere
                </a>
            </div>
        </form>
    </div>

    <!-- Results Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                @if(request()->has('search'))
                    Rezultati pretrage
                @else
                    Svi oglasi
                @endif
            </h2>
            <p class="text-gray-600 mt-1">Pronađeno {{ $listings->total() }} oglasa</p>
        </div>

        <!-- Sort Dropdown -->
        <div>
            <form action="{{ route('listings.index') }}" method="GET" id="sortForm">
                @foreach(request()->except('sort') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                
                <select name="sort" onchange="document.getElementById('sortForm').submit()" 
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Najnovije</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Najstarije</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Cena: rastuće</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Cena: opadajuće</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Listings Grid -->
    @if($listings->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($listings as $listing)
                <div class="flex">
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
            <p class="text-gray-500 mb-6">Pokušajte sa drugačijim filterima ili pretragom</p>
            <a href="{{ route('listings.index') }}" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Pogledaj sve oglase
            </a>
        </div>
    @endif
</div>
@endsection