@extends('layouts.app')

@section('title', 'Pretraga nekretnina')

@section('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Početna', 'url' => route('home')],
        ['title' => 'Pretraga', 'url' => route('listings.index')],
    ]" />
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">
            @if($selectedCategory)
                {{ $selectedCategory->name }}
            @else
                Pretraga nekretnina
            @endif
        </h1>
        <p class="text-gray-600 mt-2">
            Pronađeno {{ $listings->total() }} rezultata
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        {{-- Left Sidebar - Search Filters --}}
        <aside class="lg:col-span-1">
            <x-search-widget 
                layout="sidebar" 
                :categories="$categories"
                :locations="$allLocations" />
        </aside>

        {{-- Main Content - Listings Grid --}}
        <main class="lg:col-span-3">
            
            {{-- Sort Bar --}}
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                <form action="{{ route('listings.index') }}" method="GET" class="flex items-center justify-between">
                    {{-- Preserve all current filters --}}
                    @foreach(request()->except('sort') as $key => $value)
                        @if(is_array($value))
                            @foreach($value as $v)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach

                    <div class="flex items-center gap-2">
                        <i class="fas fa-sort text-gray-500"></i>
                        <span class="text-sm text-gray-600 font-medium">Sortiraj po:</span>
                    </div>
                    
                    <select name="sort" 
                            onchange="this.form.submit()"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Najnovije</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Najstarije</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Cena: Najniža</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Cena: Najviša</option>
                        <option value="area_desc" {{ request('sort') == 'area_desc' ? 'selected' : '' }}>Površina: Najveća</option>
                    </select>
                </form>
            </div>

            {{-- Active Filters Display --}}
            @if(request()->hasAny(['category', 'city', 'listing_type', 'price_min', 'price_max', 'area_min', 'area_max', 'rooms', 'floor', 'features']))
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-blue-900 mb-2">Aktivni filteri:</h3>
                        <div class="flex flex-wrap gap-2">
                            @if(request('category'))
                                @php $cat = $categories->find(request('category')); @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-600 text-white">
                                    {{ $cat->name }}
                                    <a href="{{ route('listings.index', request()->except('category')) }}" class="ml-2 hover:text-blue-200">×</a>
                                </span>
                            @endif
                            
                            @if(request('city'))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-600 text-white">
                                    {{ request('city') }}
                                    <a href="{{ route('listings.index', request()->except('city')) }}" class="ml-2 hover:text-blue-200">×</a>
                                </span>
                            @endif
                            
                            @if(request('listing_type'))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-600 text-white">
                                    {{ request('listing_type') == 'sale' ? 'Prodaja' : 'Izdavanje' }}
                                    <a href="{{ route('listings.index', request()->except('listing_type')) }}" class="ml-2 hover:text-blue-200">×</a>
                                </span>
                            @endif
                            
                            @if(request('price_min') || request('price_max'))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-600 text-white">
                                    Cena: {{ request('price_min', 0) }} - {{ request('price_max', '∞') }}
                                    <a href="{{ route('listings.index', request()->except(['price_min', 'price_max'])) }}" class="ml-2 hover:text-blue-200">×</a>
                                </span>
                            @endif
                            
                            @if(request('area_min') || request('area_max'))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-600 text-white">
                                    Površina: {{ request('area_min', 0) }} - {{ request('area_max', '∞') }} m²
                                    <a href="{{ route('listings.index', request()->except(['area_min', 'area_max'])) }}" class="ml-2 hover:text-blue-200">×</a>
                                </span>
                            @endif
                            
                            @if(request('rooms'))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-600 text-white">
                                    {{ request('rooms') }} soba
                                    <a href="{{ route('listings.index', request()->except('rooms')) }}" class="ml-2 hover:text-blue-200">×</a>
                                </span>
                            @endif
                            
                            @if(request('features'))
                                @foreach(request('features') as $feature)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-600 text-white">
                                        {{ $feature }}
                                        <a href="{{ route('listings.index', array_merge(request()->except('features'), ['features' => array_diff(request('features', []), [$feature])])) }}" class="ml-2 hover:text-blue-200">×</a>
                                    </span>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('listings.index') }}" 
                       class="text-sm text-blue-600 hover:text-blue-700 font-semibold whitespace-nowrap ml-4">
                        Obriši sve
                    </a>
                </div>
            </div>
            @endif

            {{-- Listings Grid --}}
            @if($listings->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($listings as $listing)
                        @include('partials.listing-card', ['listing' => $listing])
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $listings->links() }}
                </div>
            @else
                {{-- No Results --}}
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Nema rezultata</h3>
                    <p class="text-gray-500 mb-6">Pokušajte sa drugačijim filterima</p>
                    <a href="{{ route('listings.index') }}" 
                       class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                        <i class="fas fa-redo mr-2"></i> Resetuj pretragu
                    </a>
                </div>
            @endif
        </main>
    </div>
</div>
@endsection