@extends('layouts.app')

@section('title', 'Admin - Oglasi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Upravljanje oglasima</h1>
        <p class="text-gray-600 mt-2">Pregledaj, odobri i upravljaj oglasima</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form action="{{ route('admin.listings') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            
            <!-- Status Filter -->
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Svi statusi</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktivni</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Na čekanju</option>
                <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Prodato</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Odbijeni</option>
            </select>

            <!-- Category Filter -->
            <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Sve kategorije</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <!-- Search -->
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Pretraži..." 
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">

            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Primeni
                </button>
                <a href="{{ route('admin.listings') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Listings Table -->
    @if($listings->count() > 0)
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Oglas</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Korisnik</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategorija</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cena</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pregledi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Akcije</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($listings as $listing)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4 text-sm text-gray-600">
                            #{{ $listing->id }}
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-gray-200 rounded mr-3 flex-shrink-0">
                                    @if($listing->primaryImage)
                                        <img src="{{ $listing->primaryImage->thumbnailUrl() }}" alt="" class="w-full h-full object-cover rounded">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <i class="fas fa-image text-sm"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="font-medium text-gray-900 truncate">{{ Str::limit($listing->title, 30) }}</div>
                                    <div class="text-sm text-gray-500">{{ $listing->city }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-gray-900">{{ $listing->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $listing->user->email }}</div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600">
                            {{ $listing->category->name }}
                        </td>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-900">
                            {{ $listing->formattedPrice() }}
                        </td>
                        <td class="px-4 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($listing->status === 'active') bg-green-100 text-green-800
                                @elseif($listing->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($listing->status === 'rejected') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($listing->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600">
                            {{ $listing->views }}
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <!-- View -->
                                <a href="{{ route('listings.show', $listing->slug) }}" class="text-blue-600 hover:text-blue-700" title="Pogledaj" target="_blank">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <!-- Approve (if pending) -->
                                @if($listing->status === 'pending')
                                    <form action="{{ route('admin.listings.approve', $listing->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-700" title="Odobri">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif

                                <!-- Reject (if pending or active) -->
                                @if($listing->status === 'pending' || $listing->status === 'active')
                                    <form action="{{ route('admin.listings.reject', $listing->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Da li ste sigurni?')">
                                        @csrf
                                        <button type="submit" class="text-orange-600 hover:text-orange-700" title="Odbij">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                @endif

                                <!-- Toggle Featured -->
                                <form action="{{ route('admin.listings.toggle-featured', $listing->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="{{ $listing->is_featured ? 'text-yellow-600' : 'text-gray-400' }} hover:text-yellow-700" title="Istakni">
                                        <i class="fas fa-star"></i>
                                    </button>
                                </form>

                                <!-- Delete -->
                                <form action="{{ route('admin.listings.delete', $listing->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Da li ste sigurni da želite obrisati ovaj oglas?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700" title="Obriši">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 py-4 border-t border-gray-200">
            {{ $listings->links() }}
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
        <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Nema oglasa</h3>
        <p class="text-gray-500">Pokušajte sa drugačijim filterima</p>
    </div>
    @endif
</div>
@endsection