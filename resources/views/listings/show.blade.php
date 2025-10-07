@extends('layouts.app')

@section('title', $listing->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main content --}}
        <div class="lg:col-span-2">
            {{-- Image Gallery --}}
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                @if($listing->images->count() > 0)
                    {{-- Main image --}}
                    <div class="relative">
                        <img 
                            id="mainImage"
                            src="{{ listing_image($listing->images->first()->image_path, 'large') }}"
                            alt="{{ $listing->title }}"
                            class="w-full h-96 object-cover cursor-pointer"
                            onclick="openLightbox(0)"
                        >
                        
                        {{-- Image counter --}}
                        <div class="absolute bottom-4 right-4 bg-black bg-opacity-60 text-white px-3 py-2 rounded">
                            <i class="fas fa-camera"></i> 
                            <span id="imageCounter">1</span> / {{ $listing->images->count() }}
                        </div>
                    </div>

                    {{-- Thumbnail strip --}}
                    @if($listing->images->count() > 1)
                        <div class="p-4 bg-gray-50">
                            <div class="grid grid-cols-6 gap-2">
                                @foreach($listing->images as $index => $image)
                                    <img 
                                        src="{{ listing_image($image->image_path, 'thumbnail') }}"
                                        alt="Slika {{ $index + 1 }}"
                                        class="w-full h-20 object-cover rounded cursor-pointer hover:opacity-75 transition thumbnail-image {{ $index === 0 ? 'ring-2 ring-blue-500' : '' }}"
                                        onclick="changeMainImage({{ $index }})"
                                        data-large="{{ listing_image($image->image_path, 'large') }}"
                                        data-index="{{ $index }}"
                                    >
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    <img 
                        src="{{ asset('images/no-image.jpg') }}" 
                        alt="Nema slike"
                        class="w-full h-96 object-cover"
                    >
                @endif
            </div>

            {{-- Listing details --}}
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $listing->title }}</h1>
                        <div class="flex items-center text-gray-600 space-x-4">
                            <span><i class="fas fa-map-marker-alt"></i> {{ $listing->location }}</span>
                            <span><i class="far fa-clock"></i> {{ time_ago($listing->created_at) }}</span>
                            <span><i class="far fa-eye"></i> {{ $listing->views }} pregleda</span>
                        </div>
                    </div>

                    {{-- Favorite button --}}
                    @auth
                        <form action="{{ route('listings.favorite', $listing) }}" method="POST">
                            @csrf
                            <button 
                                type="submit" 
                                class="flex items-center space-x-2 px-4 py-2 rounded-lg border {{ auth()->user()->favorites->contains($listing->id) ? 'bg-red-50 border-red-500 text-red-600' : 'bg-gray-50 border-gray-300 text-gray-600' }} hover:bg-red-100 transition"
                            >
                                <i class="fa{{ auth()->user()->favorites->contains($listing->id) ? 's' : 'r' }} fa-heart"></i>
                                <span>{{ auth()->user()->favorites->contains($listing->id) ? 'Ukloni iz omiljenih' : 'Dodaj u omiljene' }}</span>
                            </button>
                        </form>
                    @endauth
                </div>

                <div class="border-t pt-4">
                    <div class="text-4xl font-bold text-blue-600 mb-4">
                        {{ format_price($listing->price) }}
                    </div>

                    <div class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                        {{ $listing->category->name }}
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Opis</h2>
                <div class="text-gray-700 whitespace-pre-line">{{ $listing->description }}</div>
            </div>

            {{-- Action buttons for owner --}}
            @can('update', $listing)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Upravljaj oglasom</h3>
                    <div class="flex space-x-4">
                        <a 
                            href="{{ route('listings.edit', $listing) }}" 
                            class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition text-center"
                        >
                            <i class="fas fa-edit"></i> Izmeni oglas
                        </a>
                        <form action="{{ route('listings.destroy', $listing) }}" method="POST" class="flex-1" onsubmit="return confirm('Da li ste sigurni da želite da obrišete ovaj oglas?')">
                            @csrf
                            @method('DELETE')
                            <button 
                                type="submit" 
                                class="w-full bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition"
                            >
                                <i class="fas fa-trash"></i> Obriši oglas
                            </button>
                        </form>
                    </div>
                </div>
            @endcan
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            {{-- Contact card --}}
            <div class="bg-white rounded-lg shadow-md p-6 mb-6 sticky top-4">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Kontakt informacije</h3>
                
                <div class="flex items-center mb-4">
                    <img 
                        src="{{ avatar_image($listing->user->avatar) }}" 
                        alt="{{ $listing->user->name }}"
                        class="w-12 h-12 rounded-full mr-3"
                    >
                    <div>
                        <div class="font-semibold text-gray-800">{{ $listing->user->name }}</div>
                        <div class="text-sm text-gray-600">Član od {{ format_date($listing->user->created_at) }}</div>
                    </div>
                </div>

                @if($listing->contact_phone)
                    <a 
                        href="tel:{{ $listing->contact_phone }}" 
                        class="block w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition text-center mb-3"
                    >
                        <i class="fas fa-phone"></i> {{ $listing->contact_phone }}
                    </a>
                @endif

                <a 
                    href="mailto:{{ $listing->user->email }}" 
                    class="block w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition text-center"
                >
                    <i class="fas fa-envelope"></i> Pošalji poruku
                </a>
            </div>

            {{-- Safety tips --}}
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h4 class="font-bold text-gray-800 mb-2">
                    <i class="fas fa-shield-alt text-yellow-600"></i> Saveti za bezbednost
                </h4>
                <ul class="text-sm text-gray-700 space-y-2">
                    <li>• Sastanite se na javnom mestu</li>
                    <li>• Proverite proizvod pre plaćanja</li>
                    <li>• Nikada ne šaljite novac unapred</li>
                    <li>• Budite oprezni sa sumnjivim ponudama</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Related listings --}}
    @if($relatedListings->count() > 0)
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Slični oglasi</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedListings as $relatedListing)
                    @include('partials.listing-card', ['listing' => $relatedListing])
                @endforeach
            </div>
        </div>
    @endif
</div>

{{-- Lightbox Modal --}}
<div id="lightbox" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden flex items-center justify-center">
    <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white text-4xl hover:text-gray-300">
        <i class="fas fa-times"></i>
    </button>

    <button onclick="previousImage()" class="absolute left-4 text-white text-4xl hover:text-gray-300">
        <i class="fas fa-chevron-left"></i>
    </button>

    <button onclick="nextImage()" class="absolute right-4 text-white text-4xl hover:text-gray-300">
        <i class="fas fa-chevron-right"></i>
    </button>

    <div class="max-w-6xl max-h-screen p-4">
        <img id="lightboxImage" src="" alt="" class="max-w-full max-h-screen object-contain">
        <div class="text-center text-white mt-4">
            <span id="lightboxCounter"></span>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const images = @json($listing->images->map(function($img) {
        return [
            'thumbnail' => listing_image($img->image_path, 'thumbnail'),
            'large' => listing_image($img->image_path, 'large')
        ];
    }));

    let currentImageIndex = 0;

    function changeMainImage(index) {
        currentImageIndex = index;
        document.getElementById('mainImage').src = images[index].large;
        document.getElementById('imageCounter').textContent = index + 1;

        // Update thumbnail borders
        document.querySelectorAll('.thumbnail-image').forEach((thumb, i) => {
            if (i === index) {
                thumb.classList.add('ring-2', 'ring-blue-500');
            } else {
                thumb.classList.remove('ring-2', 'ring-blue-500');
            }
        });
    }

    function openLightbox(index) {
        currentImageIndex = index;
        document.getElementById('lightbox').classList.remove('hidden');
        document.getElementById('lightbox').classList.add('flex');
        updateLightboxImage();
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        document.getElementById('lightbox').classList.add('hidden');
        document.getElementById('lightbox').classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    function nextImage() {
        currentImageIndex = (currentImageIndex + 1) % images.length;
        updateLightboxImage();
    }

    function previousImage() {
        currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
        updateLightboxImage();
    }

    function updateLightboxImage() {
        document.getElementById('lightboxImage').src = images[currentImageIndex].large;
        document.getElementById('lightboxCounter').textContent = 
            `${currentImageIndex + 1} / ${images.length}`;
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (!document.getElementById('lightbox').classList.contains('hidden')) {
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') previousImage();
            if (e.key === 'ArrowRight') nextImage();
        }
    });
</script>
@endpush
@endsection