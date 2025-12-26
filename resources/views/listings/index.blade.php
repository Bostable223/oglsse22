@extends('layouts.app')

@section('title', 'Svi oglasi')

@section('breadcrumbs')
    @php
        $breadcrumbItems = [['title' => 'Oglasi', 'url' => route('listings.index')]];
        
        if(isset($selectedCategory)) {
            $breadcrumbItems[] = ['title' => $selectedCategory->name, 'url' => route('listings.index', ['category' => $selectedCategory->id])];
        }
    @endphp
    <x-breadcrumbs :items="$breadcrumbItems" />
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- LEFT SIDEBAR - Search Filters -->
        <div class="lg:col-span-1">
            <x-sidebar-search :categories="$categories" :allLocations="$allLocations" :listings="$listings" />
        </div>

        <!-- RIGHT CONTENT - Listings -->
        <div class="lg:col-span-3">
            
            <!-- Results Header -->
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">
                    {{ $listings->total() }} {{ $listings->total() == 1 ? 'oglas' : 'oglasa' }}
                </h2>

                <!-- Sort Dropdown -->
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600">Sortiraj:</label>
                    <select onchange="window.location.href=this.value" 
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="{{ route('listings.index', array_merge(request()->except('sort'), ['sort' => 'newest'])) }}" 
                                {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>
                            Najnovije
                        </option>
                        <option value="{{ route('listings.index', array_merge(request()->except('sort'), ['sort' => 'price_asc'])) }}" 
                                {{ request('sort') === 'price_asc' ? 'selected' : '' }}>
                            Cena: Rastuća
                        </option>
                        <option value="{{ route('listings.index', array_merge(request()->except('sort'), ['sort' => 'price_desc'])) }}" 
                                {{ request('sort') === 'price_desc' ? 'selected' : '' }}>
                            Cena: Opadajuća
                        </option>
                        <option value="{{ route('listings.index', array_merge(request()->except('sort'), ['sort' => 'area_desc'])) }}" 
                                {{ request('sort') === 'area_desc' ? 'selected' : '' }}>
                            Najveća površina
                        </option>
                    </select>
                </div>
            </div>

            <!-- Active Filters Pills -->
            @if(request()->hasAny(['city', 'category', 'listing_type', 'price_min', 'price_max', 'features', 'rooms', 'floor']))
            <div class="mb-6 flex flex-wrap gap-2">
                @if(request('city'))
                    <span class="inline-flex items-center gap-2 bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                        <i class="fas fa-map-marker-alt"></i>
                        {{ request('city') }}
                        <a href="{{ route('listings.index', request()->except('city')) }}" class="hover:text-blue-900">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif

                @if(request('category') && isset($categories))
                    @php $cat = $categories->find(request('category')); @endphp
                    @if($cat)
                    <span class="inline-flex items-center gap-2 bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                        <i class="fas fa-home"></i>
                        {{ $cat->name }}
                        <a href="{{ route('listings.index', request()->except('category')) }}" class="hover:text-blue-900">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                    @endif
                @endif

                @if(request('listing_type'))
                    <span class="inline-flex items-center gap-2 bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                        {{ request('listing_type') === 'sale' ? 'Prodaja' : 'Izdavanje' }}
                        <a href="{{ route('listings.index', request()->except('listing_type')) }}" class="hover:text-blue-900">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif

                @if(request('rooms'))
                    <span class="inline-flex items-center gap-2 bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                        <i class="fas fa-door-open"></i>
                        {{ request('rooms') }} {{ request('rooms') == 1 ? 'soba' : 'sobe' }}
                        <a href="{{ route('listings.index', request()->except('rooms')) }}" class="hover:text-blue-900">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif

                @if(request('floor'))
                    <span class="inline-flex items-center gap-2 bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                        <i class="fas fa-building"></i>
                        Sprat: {{ request('floor') }}
                        <a href="{{ route('listings.index', request()->except('floor')) }}" class="hover:text-blue-900">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif

                @if(request('price_min') || request('price_max'))
                    <span class="inline-flex items-center gap-2 bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                        <i class="fas fa-euro-sign"></i>
                        @if(request('price_min') && request('price_max'))
                            {{ number_format(request('price_min')) }} - {{ number_format(request('price_max')) }}
                        @elseif(request('price_min'))
                            Od {{ number_format(request('price_min')) }}
                        @else
                            Do {{ number_format(request('price_max')) }}
                        @endif
                        <a href="{{ route('listings.index', request()->except(['price_min', 'price_max'])) }}" class="hover:text-blue-900">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif

                @if(request('features'))
                    @foreach((array)request('features') as $feature)
                        <span class="inline-flex items-center gap-2 bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                            <i class="fas fa-check"></i>
                            {{ $feature }}
                            @php
                                $remainingFeatures = array_diff((array)request('features'), [$feature]);
                                $newParams = request()->except('features');
                                if (!empty($remainingFeatures)) {
                                    $newParams['features'] = array_values($remainingFeatures);
                                }
                            @endphp
                            <a href="{{ route('listings.index', $newParams) }}" class="hover:text-blue-900">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endforeach
                @endif

                <a href="{{ route('listings.index') }}" 
                   class="inline-flex items-center gap-2 text-red-600 hover:text-red-700 px-3 py-1 text-sm font-semibold">
                    <i class="fas fa-times-circle"></i>
                    Ukloni sve
                </a>
            </div>
            @endif

            <!-- Listings Grid -->
            @if($listings->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
                    @foreach($listings as $listing)
                        @include('partials.listing-card', ['listing' => $listing])
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $listings->links() }}
                </div>
            @else
                <!-- No Results -->
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Nema rezultata</h3>
                    <p class="text-gray-500 mb-6">Pokušajte sa drugačijim filterima ili resetujte pretragu</p>
                    <a href="{{ route('listings.index') }}" 
                       class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                        Resetuj filtere
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection