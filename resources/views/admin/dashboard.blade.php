@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Admin Panel</h1>
        <p class="text-gray-600 mt-2">Dobrodošli u administraciju</p>
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
                    <p class="text-gray-500 text-sm">Na čekanju</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $stats['pending_listings'] }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Ukupno korisnika</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['total_users'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-users text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Ukupno pregleda</p>
                    <p class="text-3xl font-bold text-purple-600 mt-2">{{ number_format($stats['total_views']) }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-eye text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <a href="{{ route('admin.listings') }}" class="bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="bg-white bg-opacity-20 p-3 rounded-full mr-4">
                    <i class="fas fa-list text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-lg">Upravljaj oglasima</h3>
                    <p class="text-blue-100 text-sm">Pregledaj i odobri oglase</p>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.users') }}" class="bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="bg-white bg-opacity-20 p-3 rounded-full mr-4">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-lg">Upravljaj korisnicima</h3>
                    <p class="text-green-100 text-sm">Pregled korisnika</p>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.categories') }}" class="bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="bg-white bg-opacity-20 p-3 rounded-full mr-4">
                    <i class="fas fa-tags text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-lg">Kategorije</h3>
                    <p class="text-purple-100 text-sm">Upravljaj kategorijama</p>
                </div>
            </div>
        </a>
        

                    <!-- Add this as a 4th card in the Quick Actions grid -->
            <a href="{{ route('admin.packages') }}" class="bg-gradient-to-r from-orange-600 to-orange-700 text-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="bg-white bg-opacity-20 p-3 rounded-full mr-4">
                        <i class="fas fa-box text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">Paketi</h3>
                        <p class="text-orange-100 text-sm">Upravljaj paketima</p>
                    </div>
                </div>
            </a>

                <a href="{{ route('admin.packages.analytics') }}" class="bg-gradient-to-r  from-purple-600 to-purple-700 text-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                    <div class="bg-white bg-opacity-20 p-3 rounded-full mr-4">
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">Analitika paketa</h3>
                        <p class="text-purple-100 text-sm">Pregled statistike</p>
                    </div>
                </div>
            </a>

    </div>

    <!-- Pending Listings -->
    @if($pendingListings->count() > 0)
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Oglasi na čekanju ({{ $pendingListings->count() }})</h2>
            <a href="{{ route('admin.listings', ['status' => 'pending']) }}" class="text-blue-600 hover:text-blue-700 text-sm">
                Vidi sve <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Oglas</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Korisnik</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategorija</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cena</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Datum</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Akcije</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($pendingListings as $listing)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4">
                            <div class="font-medium text-gray-900">{{ Str::limit($listing->title, 40) }}</div>
                            <div class="text-sm text-gray-500">{{ $listing->city }}</div>
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
                        <td class="px-4 py-4 text-sm text-gray-600">
                            {{ $listing->created_at->format('d.m.Y') }}
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('listings.show', $listing->slug) }}" class="text-blue-600 hover:text-blue-700" title="Pogledaj" target="_blank">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('admin.listings.approve', $listing->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-700" title="Odobri">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.listings.reject', $listing->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Da li ste sigurni?')">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-700" title="Odbij">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Recent Users -->
    @if($recentUsers->count() > 0)
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Novi korisnici</h2>
            <a href="{{ route('admin.users') }}" class="text-blue-600 hover:text-blue-700 text-sm">
                Vidi sve <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        <div class="space-y-4">
            @foreach($recentUsers as $user)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <div class="font-semibold text-gray-900">{{ $user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-600">{{ $user->listings_count }} oglasa</div>
                        <div class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection