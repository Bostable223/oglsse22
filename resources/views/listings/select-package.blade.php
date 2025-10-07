@extends('layouts.app')

@section('title', 'Izaberite paket')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Izaberite paket za vaš oglas</h1>
        <p class="text-lg text-gray-600">Povećajte vidljivost vašeg oglasa i dobijte više kontakata</p>
    </div>

    <!-- Package Type Toggle -->
    <div class="flex justify-center mb-12">
        <div class="bg-gray-100 p-1 rounded-lg inline-flex">
            <button onclick="showPackageType('top')" id="topBtn" 
                    class="px-8 py-3 rounded-lg font-semibold transition-all bg-white text-blue-600 shadow">
                Top Oglasi
            </button>
            <button onclick="showPackageType('featured')" id="featuredBtn" 
                    class="px-8 py-3 rounded-lg font-semibold transition-all text-gray-600">
                Istaknuti Oglasi
            </button>
        </div>
    </div>

    <!-- Top Packages -->
    <div id="topPackages" class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
        @foreach($topPackages as $package)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-shadow border-2 border-gray-200 hover:border-blue-500 {{ $package->duration_days == 30 ? 'transform scale-105' : '' }}">
                @if($package->duration_days == 30)
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white text-center py-2 font-semibold text-sm">
                        🔥 NAJBOLJA PONUDA
                    </div>
                @endif
                
                <div class="p-8">
                    <!-- Package Name -->
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                            <i class="fas fa-arrow-up text-2xl text-blue-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $package->duration_days }} Dana</h3>
                        <p class="text-gray-600">Top Oglas</p>
                    </div>

                    <!-- Price -->
                    <div class="text-center mb-6 pb-6 border-b border-gray-200">
                        <div class="text-4xl font-bold text-gray-900">{{ $package->formattedPrice() }}</div>
                        <div class="text-sm text-gray-500 mt-1">za {{ $package->duration_days }} dana</div>
                    </div>

                    <!-- Features -->
                    <ul class="space-y-3 mb-8">
                        @foreach($package->features as $feature)
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span class="text-gray-700">{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <!-- Select Button -->
                    <form action="{{ route('listings.store-with-package') }}" method="POST">
                        @csrf
                        <input type="hidden" name="package_id" value="{{ $package->id }}">
                        <input type="hidden" name="listing_data" value="{{ json_encode(session('listing_draft', [])) }}">
                        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold transition-colors">
                            Izaberi ovaj paket
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Featured Packages -->
    <div id="featuredPackages" class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8 hidden">
        @foreach($featuredPackages as $package)
            <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-shadow border-2 border-yellow-400 hover:border-yellow-500 {{ $package->duration_days == 30 ? 'transform scale-105 ring-4 ring-yellow-400' : '' }}">
                @if($package->duration_days == 30)
                    <div class="bg-gradient-to-r from-yellow-500 to-orange-500 text-white text-center py-2 font-semibold text-sm">
                        ⭐ PREMIUM PAKET
                    </div>
                @endif
                
                <div class="p-8">
                    <!-- Package Name -->
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-yellow-100 rounded-full mb-4">
                            <i class="fas fa-star text-2xl text-yellow-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $package->duration_days }} Dana</h3>
                        <p class="text-gray-700 font-semibold">Istaknuti Oglas</p>
                    </div>

                    <!-- Price -->
                    <div class="text-center mb-6 pb-6 border-b border-yellow-300">
                        <div class="text-4xl font-bold text-gray-900">{{ $package->formattedPrice() }}</div>
                        <div class="text-sm text-gray-600 mt-1">za {{ $package->duration_days }} dana</div>
                    </div>

                    <!-- Features -->
                    <ul class="space-y-3 mb-8">
                        @foreach($package->features as $feature)
                            <li class="flex items-start">
                                <i class="fas fa-star text-yellow-500 mt-1 mr-3"></i>
                                <span class="text-gray-700 font-medium">{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <!-- Select Button -->
                    <form action="{{ route('listings.store-with-package') }}" method="POST">
                        @csrf
                        <input type="hidden" name="package_id" value="{{ $package->id }}">
                        <input type="hidden" name="listing_data" value="{{ json_encode(session('listing_draft', [])) }}">
                        <button type="submit" class="w-full bg-gradient-to-r from-yellow-500 to-orange-500 text-white py-3 rounded-lg hover:from-yellow-600 hover:to-orange-600 font-semibold transition-colors shadow-md">
                            Izaberi Premium Paket
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Free Option -->
    <div class="max-w-4xl mx-auto">
        <div class="bg-gray-50 rounded-xl p-8 border-2 border-gray-300">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Besplatno postavljanje</h3>
                    <p class="text-gray-600">Postavite oglas bez promocije</p>
                    <ul class="mt-4 space-y-2">
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check text-gray-400 mr-2"></i>
                            Obična lista oglasa
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check text-gray-400 mr-2"></i>
                            Standardna vidljivost
                        </li>
                    </ul>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-gray-900 mb-4">Besplatno</div>
                    <form action="{{ route('listings.store-with-package') }}" method="POST">
                        @csrf
                        <input type="hidden" name="listing_data" value="{{ json_encode(session('listing_draft', [])) }}">
                        <button type="submit" class="bg-gray-600 text-white px-8 py-3 rounded-lg hover:bg-gray-700 font-semibold">
                            Nastavi bez paketa
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparison Info -->
    <div class="mt-16 max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Zašto izabrati promociju?</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <div class="text-4xl mb-4">📈</div>
                <h3 class="font-semibold text-lg mb-2">Veća vidljivost</h3>
                <p class="text-gray-600 text-sm">Vaš oglas će videti mnogo više ljudi</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <div class="text-4xl mb-4">⚡</div>
                <h3 class="font-semibold text-lg mb-2">Brža prodaja</h3>
                <p class="text-gray-600 text-sm">Prodajte ili iznajmite brže</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <div class="text-4xl mb-4">💰</div>
                <h3 class="font-semibold text-lg mb-2">Više kontakata</h3>
                <p class="text-gray-600 text-sm">Dobijte više poziva i upita</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showPackageType(type) {
    const topBtn = document.getElementById('topBtn');
    const featuredBtn = document.getElementById('featuredBtn');
    const topPackages = document.getElementById('topPackages');
    const featuredPackages = document.getElementById('featuredPackages');
    
    if (type === 'top') {
        // Show top packages
        topPackages.classList.remove('hidden');
        featuredPackages.classList.add('hidden');
        
        // Update button styles
        topBtn.classList.add('bg-white', 'text-blue-600', 'shadow');
        topBtn.classList.remove('text-gray-600');
        featuredBtn.classList.remove('bg-white', 'text-blue-600', 'shadow');
        featuredBtn.classList.add('text-gray-600');
    } else {
        // Show featured packages
        topPackages.classList.add('hidden');
        featuredPackages.classList.remove('hidden');
        
        // Update button styles
        featuredBtn.classList.add('bg-white', 'text-blue-600', 'shadow');
        featuredBtn.classList.remove('text-gray-600');
        topBtn.classList.remove('bg-white', 'text-blue-600', 'shadow');
        topBtn.classList.add('text-gray-600');
    }
}
</script>
@endpush
@endsection