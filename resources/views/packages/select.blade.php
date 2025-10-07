@extends('layouts.app')

@section('title', 'Izaberite paket')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-8 text-center">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Izaberite paket za vaš oglas</h1>
        <p class="text-lg text-gray-600">Povećajte vidljivost vašeg oglasa i dobijte više pregleda</p>
    </div>

    <!-- Listing Preview -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <h3 class="text-sm text-gray-500 mb-2">Vaš oglas:</h3>
        <h2 class="text-2xl font-bold text-gray-900">{{ $listing->title }}</h2>
        <p class="text-gray-600 mt-1">{{ $listing->city }} • {{ $listing->category->name }}</p>
    </div>

    <!-- Packages Tabs -->
    <div class="mb-8" x-data="{ activeTab: 'featured' }">
        <!-- Tab Buttons -->
        <div class="flex justify-center gap-4 mb-8">
            <button @click="activeTab = 'featured'" 
                    :class="activeTab === 'featured' ? 'bg-yellow-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                    class="px-8 py-3 rounded-lg font-semibold shadow-sm transition-colors">
                <i class="fas fa-star mr-2"></i> Istaknuti Oglasi
            </button>
            <button @click="activeTab = 'top'" 
                    :class="activeTab === 'top' ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                    class="px-8 py-3 rounded-lg font-semibold shadow-sm transition-colors">
                <i class="fas fa-arrow-up mr-2"></i> Top Oglasi
            </button>
        </div>

        <!-- Featured Packages -->
        <div x-show="activeTab === 'featured'" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            @foreach($featuredPackages as $package)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow border-2 {{ $loop->index === 1 ? 'border-yellow-500 transform scale-105' : 'border-gray-200' }}">
                    @if($loop->index === 1)
                        <div class="bg-yellow-500 text-white text-center py-2 font-semibold text-sm">
                            <i class="fas fa-crown mr-1"></i> NAJPOPULARNIJE
                        </div>
                    @endif
                    
                    <div class="p-6">
                        <div class="text-center mb-6">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $package->duration_days }} Dana</h3>
                            <div class="text-4xl font-bold text-yellow-600 mb-2">{{ $package->formattedPrice() }}</div>
                            <p class="text-sm text-gray-600">{{ $package->description }}</p>
                        </div>

                        <ul class="space-y-3 mb-6">
                            @foreach($package->features as $feature)
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                    <span class="text-gray-700">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <form action="{{ route('listings.apply-package', $listing->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="package_id" value="{{ $package->id }}">
                            <button type="submit" class="w-full {{ $loop->index === 1 ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-yellow-400 hover:bg-yellow-500' }} text-white py-3 rounded-lg font-semibold transition-colors">
                                Izaberi paket
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Top Packages -->
        <div x-show="activeTab === 'top'" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            @foreach($topPackages as $package)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow border-2 {{ $loop->index === 1 ? 'border-blue-500 transform scale-105' : 'border-gray-200' }}">
                    @if($loop->index === 1)
                        <div class="bg-blue-500 text-white text-center py-2 font-semibold text-sm">
                            <i class="fas fa-crown mr-1"></i> NAJPOPULARNIJE
                        </div>
                    @endif
                    
                    <div class="p-6">
                        <div class="text-center mb-6">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $package->duration_days }} Dana</h3>
                            <div class="text-4xl font-bold text-blue-600 mb-2">{{ $package->formattedPrice() }}</div>
                            <p class="text-sm text-gray-600">{{ $package->description }}</p>
                        </div>

                        <ul class="space-y-3 mb-6">
                            @foreach($package->features as $feature)
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                    <span class="text-gray-700">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <form action="{{ route('listings.apply-package', $listing->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="package_id" value="{{ $package->id }}">
                            <button type="submit" class="w-full {{ $loop->index === 1 ? 'bg-blue-500 hover:bg-blue-600' : 'bg-blue-400 hover:bg-blue-500' }} text-white py-3 rounded-lg font-semibold transition-colors">
                                Izaberi paket
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Features Comparison -->
    <div class="bg-gradient-to-r from-blue-50 to-yellow-50 rounded-lg p-8 mb-8">
        <h3 class="text-2xl font-bold text-gray-900 text-center mb-6">Uporedite pakete</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Featured Package Features -->
            <div class="bg-white rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-yellow-100 p-3 rounded-full mr-4">
                        <i class="fas fa-star text-yellow-600 text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-gray-900">Istaknuti Oglasi</h4>
                        <p class="text-sm text-gray-600">Maksimalna vidljivost</p>
                    </div>
                </div>
                <ul class="space-y-2 text-gray-700">
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Premium značka na oglasu</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Prikaz na početnoj strani</li>
                    <li><i class="fas fa-check