<div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden h-full flex flex-col {{ $featured ?? false ? 'ring-2 ring-yellow-400' : '' }}">
    <a href="{{ route('listings.show', $listing->slug) }}" class="block flex-1 flex flex-col">
        <!-- Image -->
        <div class="relative h-48 bg-gray-200">
            @if($listing->primaryImage)
                <img src="{{ $listing->primaryImage->url() }}" alt="{{ $listing->title }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center text-gray-400">
                    <i class="fas fa-image text-4xl"></i>
                </div>
            @endif

            <!-- Featured Badge -->
            @if($listing->isFeaturedActive())
                <div class="absolute top-2 left-2 bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                    <i class="fas fa-star"></i> Istaknuto
                </div>
            @endif

            <!-- Top Listing Badge -->
            @if($listing->isTopActive())
                <div class="absolute top-2 left-2 bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-semibold">
                    <i class="fas fa-arrow-up"></i> Top
                </div>
            @endif

            <!-- Listing Type Badge -->
            <div class="absolute top-2 right-2 bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-semibold">
                {{ $listing->listing_type == 'sale' ? 'Prodaja' : 'Izdavanje' }}
            </div>

            <!-- Favorite Button (Bottom Right) -->
            @auth
                <form action="{{ route('listings.favorite', $listing->id) }}" method="POST" class="absolute bottom-2 right-2" onclick="event.stopPropagation()">
                    @csrf
                    <button type="submit" class="bg-white bg-opacity-90 hover:bg-opacity-100 p-2 rounded-full shadow-lg transition-all">
                        <i class="fas fa-heart {{ $listing->isFavoritedBy(auth()->user()) ? 'text-red-500' : 'text-gray-400' }} text-lg"></i>
                    </button>
                </form>
            @endauth
        </div>

        <!-- Content -->
        <div class="p-4 flex-1 flex flex-col">
            <!-- Category -->
            <div class="text-xs text-gray-500 mb-2">
                <i class="fas fa-tag"></i> {{ $listing->category->name }}
            </div>

            <!-- Title -->
            <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2 hover:text-blue-600 flex-grow">
                {{ $listing->title }}
            </h3>

            <!-- Location -->
            <div class="text-sm text-gray-600 mb-3">
                <i class="fas fa-map-marker-alt text-red-500"></i> 
                {{ $listing->city }}{{ $listing->municipality ? ', ' . $listing->municipality : '' }}
            </div>

            <!-- Details Row: Price, Area, Rooms -->
            <div class="flex items-center justify-between gap-2 text-sm text-gray-600 mb-3 pb-3 border-b border-gray-200">
                <!-- Price -->
                <div class="text-lg font-bold text-blue-600">
                    {{ number_format($listing->price, 0, ',', '.') }}
                    <span class="text-xs">{{ $listing->currency }}</span>
                </div>
                
                <!-- Area and Rooms -->
                <div class="flex items-center gap-3">
                    @if($listing->area)
                        <span class="flex items-center">
                            <i class="fas fa-ruler-combined mr-1"></i> {{ $listing->area }} m²
                        </span>
                    @endif
                    @if($listing->rooms)
                        <span class="flex items-center">
                            <i class="fas fa-bed mr-1"></i> {{ $listing->rooms }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </a>
</div>