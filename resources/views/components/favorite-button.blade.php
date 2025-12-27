{{-- resources/views/components/favorite-button.blade.php --}}
@props(['listing', 'size' => 'default'])

@php
    $isFavorited = auth()->check() ? $listing->isFavoritedBy(auth()->user()) : false;
    
    $sizeClasses = [
        'small' => 'w-8 h-8 text-sm',
        'default' => 'w-10 h-10 text-base',
        'large' => 'w-12 h-12 text-lg',
    ];
    
    $iconSize = [
        'small' => 'w-4 h-4',
        'default' => 'w-5 h-5',
        'large' => 'w-6 h-6',
    ];
@endphp

<button
    type="button"
    onclick="toggleFavorite(event, {{ $listing->id }})"
    data-listing-id="{{ $listing->id }}"
    data-favorited="{{ $isFavorited ? 'true' : 'false' }}"
    class="favorite-btn {{ $sizeClasses[$size] }} rounded-full bg-white/90 backdrop-blur-sm hover:bg-white shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center group border border-gray-200"
    aria-label="{{ $isFavorited ? 'Ukloni iz omiljenih' : 'Dodaj u omiljene' }}"
    title="{{ $isFavorited ? 'Ukloni iz omiljenih' : 'Dodaj u omiljene' }}"
>
    @auth
        <svg 
            class="favorite-icon {{ $iconSize[$size] }} transition-all duration-200"
            fill="{{ $isFavorited ? 'currentColor' : 'none' }}"
            stroke="currentColor"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                class="{{ $isFavorited ? 'text-red-500' : 'text-gray-600 group-hover:text-red-500' }}"
            />
        </svg>
    @else
        <svg 
            class="{{ $iconSize[$size] }} text-gray-600 group-hover:text-red-500 transition-colors duration-200"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
            />
        </svg>
    @endauth
</button>

@push('scripts')
<script>
function toggleFavorite(event, listingId) {
    event.preventDefault();
    event.stopPropagation();
    
    @guest
        // Redirect to login if not authenticated
        window.location.href = '{{ route("login") }}';
        return;
    @endguest
    
    const button = event.currentTarget;
    const icon = button.querySelector('.favorite-icon');
    const isFavorited = button.dataset.favorited === 'true';
    
    // Disable button during request
    button.disabled = true;
    button.style.opacity = '0.6';
    
    // Make AJAX request
    fetch(`/listings/${listingId}/favorite`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update button state
            button.dataset.favorited = data.favorited ? 'true' : 'false';
            
            // Update icon
            if (data.favorited) {
                icon.setAttribute('fill', 'currentColor');
                icon.querySelector('path').classList.remove('text-gray-600', 'group-hover:text-red-500');
                icon.querySelector('path').classList.add('text-red-500');
                button.setAttribute('aria-label', 'Ukloni iz omiljenih');
                button.setAttribute('title', 'Ukloni iz omiljenih');
            } else {
                icon.setAttribute('fill', 'none');
                icon.querySelector('path').classList.add('text-gray-600', 'group-hover:text-red-500');
                icon.querySelector('path').classList.remove('text-red-500');
                button.setAttribute('aria-label', 'Dodaj u omiljene');
                button.setAttribute('title', 'Dodaj u omiljene');
            }
            
            // Update favorites counter if it exists
            const counter = document.querySelector('.favorites-counter');
            if (counter) {
                counter.textContent = data.favorites_count;
            }
            
            // Add animation
            button.classList.add('scale-110');
            setTimeout(() => {
                button.classList.remove('scale-110');
            }, 200);
            
            // Show toast notification (optional)
            showToast(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Došlo je do greške. Pokušajte ponovo.', 'error');
    })
    .finally(() => {
        // Re-enable button
        button.disabled = false;
        button.style.opacity = '1';
    });
}

// Simple toast notification (optional - remove if you have your own)
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>
@endpush