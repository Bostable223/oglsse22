@extends('layouts.app')

@section('title', 'Omiljeni oglasi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Omiljeni oglasi</h1>
        <p class="text-gray-600 mt-2">Oglasi koje ste sačuvali</p>
    </div>

    <!-- Favorites Grid -->
    @if($favorites->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($favorites as $listing)
                @include('partials.listing-card', ['listing' => $listing])
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $favorites->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <i class="fas fa-heart text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Nemate sačuvane oglase</h3>
            <p class="text-gray-500 mb-6">Počnite da dodajete oglase u omiljene</p>
            <a href="{{ route('listings.index') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                <i class="fas fa-search mr-2"></i> Pretraži oglase
            </a>
        </div>
    @endif
</div>
@endsection