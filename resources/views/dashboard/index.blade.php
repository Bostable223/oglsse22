@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Dashboard', 'url' => route('dashboard.index')]
    ]" />
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-gray-600 mt-2">Dobrodošli nazad, {{ auth()->user()->name }}!</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Ukupno oglasa</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_listings'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-list text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Aktivni oglasi</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['active_listings'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Ukupno pregleda</p>
                    <p class="text-3xl font-bold text-purple-600 mt-2">{{ $stats['total_views'] }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-eye text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Omiljeni</p>
                    <p class="text-3xl font-bold text-red-600 mt-2">{{ $stats['favorites_count'] }}</p>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <i class="fas fa-heart text-red-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <a href="{{ route('listings.create') }}" class="bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="bg-white bg-opacity-20 p-3 rounded-full mr-4">
                    <i class="fas fa-plus text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-lg">Novi oglas</h3>
                    <p class="text-blue-100 text-sm">Postavi nekretninu</p>
                </div>
            </div>
        </a>

        <a href="{{ route('dashboard.my-listings') }}" class="bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="bg-white bg-opacity-20 p-3 rounded-full mr-4">
                    <i class="fas fa-list text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-lg">Moji oglasi</h3>
                    <p class="text-green-100 text-sm">Upravljaj oglasima</p>
                </div>
            </div>
        </a>

        <a href="{{ route('dashboard.favorites') }}" class="bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="bg-white bg-opacity-20 p-3 rounded-full mr-4">
                    <i class="fas fa-heart text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-lg">Omiljeni</h3>
                    <p class="text-red-100 text-sm">Sačuvani oglasi</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Recent Listings -->
    @if($recentListings->count() > 0)
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Nedavni oglasi</h2>
            <a href="{{ route('dashboard.my-listings') }}" class="text-blue-600 hover:text-blue-700 text-sm">
                Vidi sve <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Oglas</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategorija</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cena</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pregledi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Akcije</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recentListings as $listing)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-gray-200 rounded mr-3 flex-shrink-0">
                                    @if($listing->primaryImage)
                                        <img src="{{ $listing->primaryImage->thumbnailUrl() }}" alt="{{ $listing->title }}" class="w-full h-full object-cover rounded">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $listing->title }}</p>
                                    <p class="text-xs text-gray-500">{{ $listing->city }}</p>
                                </div>
                            </div>
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
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($listing->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600">
                            <i class="fas fa-eye text-gray-400"></i> {{ $listing->views }}
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('listings.show', $listing->slug) }}" class="text-blue-600 hover:text-blue-700" title="Pogledaj">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('listings.edit', $listing->slug) }}" class="text-green-600 hover:text-green-700" title="Izmeni">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
        <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Nemate nijedan oglas</h3>
        <p class="text-gray-500 mb-6">Počnite sa postavljanjem vašeg prvog oglasa</p>
        <a href="{{ route('listings.create') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
            <i class="fas fa-plus mr-2"></i> Postavi oglas
        </a>
    </div>
    @endif
</div>
@endsection