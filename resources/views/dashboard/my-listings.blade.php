@extends('layouts.app')

@section('title', 'Moji oglasi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Moji oglasi</h1>
        <p class="text-gray-600 mt-2">Upravljajte svojim oglasima</p>
    </div>

    <!-- Filter and Sort -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form action="{{ route('dashboard.my-listings') }}" method="GET" class="flex items-center gap-4">
            <!-- Status Filter -->
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Svi statusi</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktivni</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Na čekanju</option>
                <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Prodato</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Isteklo</option>
            </select>

            <!-- Sort -->
            <select name="sort" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Najnovije</option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Najstarije</option>
                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Cena: rastuće</option>
                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Cena: opadajuće</option>
                <option value="views" {{ request('sort') == 'views' ? 'selected' : '' }}>Najviše pregleda</option>
            </select>

            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Primeni
            </button>

            <a href="{{ route('dashboard.my-listings') }}" class="text-gray-600 hover:text-gray-800">
                Resetuj
            </a>
        </form>
    </div>

    <!-- Listings Grid -->
    @if($listings->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($listings as $listing)
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                    <!-- Image -->
                    <div class="relative h-48 bg-gray-200">
                        @if($listing->primaryImage)
                            <img src="{{ $listing->primaryImage->url() }}" alt="{{ $listing->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <i class="fas fa-image text-4xl"></i>
                            </div>
                        @endif

                        <!-- Status Badge -->
                        <div class="absolute top-2 right-2 px-3 py-1 rounded-full text-xs font-semibold
                            @if($listing->status === 'active') bg-green-500 text-white
                            @elseif($listing->status === 'pending') bg-yellow-500 text-white
                            @elseif($listing->status === 'sold') bg-gray-500 text-white
                            @else bg-red-500 text-white
                            @endif">
                            {{ ucfirst($listing->status) }}
                        </div>

                        @if($listing->isFeaturedActive())
                            <div class="absolute top-12 left-2 bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                <i class="fas fa-star"></i> Featured
                            </div>
                        @elseif($listing->isTopActive())
                            <div class="absolute top-12 left-2 bg-blue-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                <i class="fas fa-arrow-up"></i> Top
                            </div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <!-- Category -->
                        <div class="text-xs text-gray-500 mb-2">
                            <i class="fas fa-tag"></i> {{ $listing->category->name }}
                        </div>

                        <!-- Title -->
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                            {{ $listing->title }}
                        </h3>

                        <!-- Location -->
                        <div class="text-sm text-gray-600 mb-3">
                            <i class="fas fa-map-marker-alt text-red-500"></i> 
                            {{ $listing->city }}
                        </div>

                        <!-- Price -->
                        <div class="text-xl font-bold text-blue-600 mb-3">
                            {{ $listing->formattedPrice() }}
                        </div>

                        <!-- Stats -->
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-4 pb-4 border-b">
                            <span><i class="fas fa-eye"></i> {{ $listing->views }} pregleda</span>
                            <span><i class="fas fa-clock"></i> {{ $listing->created_at->diffForHumans() }}</span>
                        </div>

                       <!-- Actions -->
                    <div class="flex flex-col gap-2">
                        <div class="grid grid-cols-3 gap-2">
                            <a href="{{ route('listings.show', $listing->slug) }}" 
                            class="text-center px-3 py-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 text-sm font-semibold">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('listings.edit', $listing->slug) }}" 
                            class="text-center px-3 py-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 text-sm font-semibold">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('listings.destroy', $listing->slug) }}" method="POST" 
                                onsubmit="return confirm('Da li ste sigurni?')" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-full px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 text-sm font-semibold">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                        
                        <!-- Promote Button -->
                        @if($listing->status === 'active')
                            <a href="{{ route('listings.promote', $listing->id) }}" 
                            class="w-full text-center px-3 py-2 bg-gradient-to-r from-yellow-400 to-orange-500 text-white rounded-lg hover:from-yellow-500 hover:to-orange-600 text-sm font-bold shadow-md">
                                <i class="fas fa-rocket mr-1"></i> 
                                @if($listing->package_id)
                                    Nadogradi
                                @else
                                    Promoviši
                                @endif
                            </a>
                        @endif
                    </div>
    </div>
</div>
@endforeach

        <!-- Pagination -->
        <div class="mt-8">
            {{ $listings->links() }}
        </div>
        @else
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Nemate oglase
                @if(request('status'))
                    sa ovim statusom
                @endif
            </h3>
            <p class="text-gray-500 mb-6">Počnite sa postavljanjem vašeg prvog oglasa</p>
            <a href="{{ route('listings.create') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                <i class="fas fa-plus mr-2"></i> Postavi oglas
            </a>
        </div>
    @endif
</div>
@endsection
