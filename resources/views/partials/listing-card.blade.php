<div class="listing-card bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden flex flex-col h-full">
    <a href="{{ route('listings.show', $listing->slug) }}" class="flex flex-col h-full listing-card-link">
        <!-- Image - Fixed Height (Grid View) -->
        <div class="relative h-48 bg-gray-200 flex-shrink-0 listing-card-image">
            @if($listing->primaryImage)
                <img src="{{ listing_image($listing->primaryImage->image_path, 'thumbnail') }}" alt="{{ $listing->title }}" loading="lazy" class="w-full h-full object-cover">
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

            <!-- Favorite Button (Bottom Right) -->
            @auth
             <div class="absolute top-2 right-2 z-10" onclick="event.preventDefault(); event.stopPropagation();">
                <x-favorite-button :listing="$listing" size="default" />
            </div>
            @endauth
        </div>

        <!-- Content -->
        <div class="p-4 flex flex-col flex-grow listing-card-content">
{{-- Category --}}
            <div class="flex items-center text-xs text-gray-500 mb-2">
                @if($listing->category)
                    <span class="flex items-center">
                        @if($listing->category->icon)
                            <i class="{{ $listing->category->icon }} mr-1"></i>
                        @endif
                        {{ $listing->category->name }}
                    </span>
                @endif
                
                @if($listing->city)
                    <span class="mx-2">•</span>
                    <span class="flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ $listing->city }}
                    </span>
                @endif
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