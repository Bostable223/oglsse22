@auth
<div x-data="{ open: false }" class="relative">
    <!-- Favorites Button -->
    <button @click="open = !open" 
            @click.away="open = false"
            class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none transition-colors group">
        
        <!-- Heart Icon -->
        <div class="relative">
            <i class="fas fa-heart text-xl"></i>
            
            <!-- Counter Badge -->
            <span id="favorites-count-badge"
                  class="absolute -top-2 -right-2 flex items-center justify-center min-w-[20px] h-5 px-1 text-xs font-bold text-white rounded-full transition-all duration-300
                         {{ Auth::user()->favorites()->count() > 0 ? 'bg-red-500' : 'bg-gray-400' }}"
                  style="{{ Auth::user()->favorites()->count() === 0 ? 'display: none;' : '' }}">
                {{ Auth::user()->favorites()->count() }}
            </span>
        </div>
    </button>

    <!-- Dropdown -->
    <div x-show="open"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50"
         style="display: none;">
        
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">
                Omiljeni oglasi
                <span class="ml-2 text-sm text-gray-500">({{ Auth::user()->favorites()->count() }})</span>
            </h3>
            <a href="{{ route('dashboard.favorites') }}" 
               class="text-xs text-blue-600 hover:text-blue-700 font-semibold">
                Vidi sve
            </a>
        </div>

        <!-- Favorites List Preview -->
        <div class="max-h-96 overflow-y-auto">
            @php
                $recentFavorites = Auth::user()->favorites()
                    ->with(['primaryImage', 'category'])
                    ->latest('favorites.created_at')
                    ->limit(5)
                    ->get();
            @endphp

            @if($recentFavorites->count() > 0)
                @foreach($recentFavorites as $listing)
                    <a href="{{ route('listings.show', $listing->slug) }}" 
                       class="flex items-center gap-3 px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        
                        <!-- Thumbnail -->
                        <div class="w-16 h-16 flex-shrink-0 rounded overflow-hidden bg-gray-200">
                            @if($listing->primaryImage)
                                <img src="{{ $listing->primaryImage->thumbnailUrl() }}" 
                                     alt="{{ $listing->title }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-sm text-gray-900 truncate">
                                {{ Str::limit($listing->title, 40) }}
                            </h4>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-map-marker-alt"></i>
                                {{ $listing->city }}
                            </p>
                            <p class="text-sm font-bold text-blue-600 mt-1">
                                {{ $listing->formattedPrice() }}
                            </p>
                        </div>

                        <!-- Remove Button -->
                        <button onclick="event.preventDefault(); event.stopPropagation(); removeFavoriteFromDropdown(this, {{ $listing->id }})"
                                class="flex-shrink-0 w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-500 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </a>
                @endforeach
            @else
                <div class="px-4 py-8 text-center text-gray-500">
                    <i class="fas fa-heart-broken text-4xl mb-2"></i>
                    <p class="text-sm">Nemate omiljenih oglasa</p>
                    <a href="{{ route('listings.index') }}" 
                       class="text-blue-600 hover:text-blue-700 text-sm font-semibold mt-2 inline-block">
                        Pregledaj oglase
                    </a>
                </div>
            @endif
        </div>

        <!-- Footer -->
        @if($recentFavorites->count() > 0)
            <div class="px-4 py-3 border-t border-gray-200 text-center">
                <a href="{{ route('dashboard.favorites') }}" 
                   class="text-sm text-blue-600 hover:text-blue-700 font-semibold">
                    Vidi sve omiljene ({{ Auth::user()->favorites()->count() }})
                </a>
            </div>
        @endif
    </div>
</div>

<script>
function removeFavoriteFromDropdown(button, listingId) {
    // Find the favorite button for this listing if it exists on the page
    const favoriteBtn = document.querySelector(`.favorite-btn[data-listing-id="${listingId}"]`);
    
    if (favoriteBtn) {
        // Use existing toggle function
        toggleFavorite(favoriteBtn);
    } else {
        // Call API directly
        removeFavoriteDirect(listingId, button);
    }
}

async function removeFavoriteDirect(listingId, button) {
    try {
        const response = await fetch(`/listings/${listingId}/favorite`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            // Remove the item from dropdown
            button.closest('a').remove();
            
            // Update counter
            updateFavoritesCounter(data.favorites_count);
            
            // Show toast
            if (typeof showToast === 'function') {
                showToast(data.message, 'info', 2000);
            }

            // If no favorites left, show empty state
            const container = button.closest('.max-h-96');
            if (container && container.querySelectorAll('a').length === 0) {
                container.innerHTML = `
                    <div class="px-4 py-8 text-center text-gray-500">
                        <i class="fas fa-heart-broken text-4xl mb-2"></i>
                        <p class="text-sm">Nemate omiljenih oglasa</p>
                    </div>
                `;
            }
        }
    } catch (error) {
        console.error('Error:', error);
        if (typeof showToast === 'function') {
            showToast('Došlo je do greške', 'error');
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endauth