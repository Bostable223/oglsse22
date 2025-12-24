@props(['listing', 'size' => 'default'])

@auth
    <button 
        data-listing-id="{{ $listing->id }}"
        data-favorited="{{ $listing->isFavoritedBy(Auth::user()) ? 'true' : 'false' }}"
        onclick="toggleFavorite(this)"
        class="favorite-btn transition-all duration-300
            @if($size === 'large')
                w-12 h-12 text-xl
            @elseif($size === 'small')
                w-8 h-8 text-sm
            @else
                w-10 h-10 text-base
            @endif
            flex items-center justify-center rounded-full
            {{ $listing->isFavoritedBy(Auth::user()) 
                ? 'bg-red-500 text-white hover:bg-red-600' 
                : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-300' }}"
        title="{{ $listing->isFavoritedBy(Auth::user()) ? 'Ukloni iz omiljenih' : 'Dodaj u omiljene' }}">
        <i class="fas fa-heart"></i>
    </button>
@else
    <a href="{{ route('login') }}" 
       class="favorite-btn transition-all duration-300
            @if($size === 'large')
                w-12 h-12 text-xl
            @elseif($size === 'small')
                w-8 h-8 text-sm
            @else
                w-10 h-10 text-base
            @endif
            flex items-center justify-center rounded-full bg-white text-gray-600 hover:bg-gray-100 border border-gray-300"
       title="Prijavite se da dodate u omiljene">
        <i class="far fa-heart"></i>
    </a>
@endauth