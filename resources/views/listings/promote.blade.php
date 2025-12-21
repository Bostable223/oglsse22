@extends('layouts.app')

@section('title', 'Promoviši oglas')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('dashboard.my-listings') }}" class="text-blue-600 hover:text-blue-700 mb-4 inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Nazad na moje oglase
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Promoviši oglas</h1>
        <p class="text-gray-600 mt-2">Povećajte vidljivost vašeg oglasa</p>
    </div>

    <!-- Current Listing Info -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <div class="flex items-start gap-4">
            <div class="w-24 h-24 bg-gray-200 rounded-lg flex-shrink-0">
                @if($listing->primaryImage)
                    <img src="{{ $listing->primaryImage->thumbnailUrl() }}" alt="{{ $listing->title }}" class="w-full h-full object-cover rounded-lg">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                        <i class="fas fa-image text-2xl"></i>
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $listing->title }}</h3>
                <div class="flex items-center gap-4 text-sm text-gray-600 mb-2">
                    <span><i class="fas fa-map-marker-alt text-red-500"></i> {{ $listing->city }}</span>
                    <span><i class="fas fa-tag text-blue-500"></i> {{ $listing->category->name }}</span>
                    <span class="font-bold text-blue-600">{{ $listing->formattedPrice() }}</span>
                </div>
                
                <!-- Current Package Status -->
                <div class="flex items-center gap-2 mt-3">
                    @if($listing->package_id)
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
                            <i class="fas fa-box mr-1"></i> {{ $listing->package->name }}
                        </span>
                        
                        @if($listing->is_top && $listing->isTopActive())
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-semibold">
                                <i class="fas fa-arrow-up mr-1"></i> Top do {{ $listing->top_until->format('d.m.Y') }}
                            </span>
                        @endif
                        
                        @if($listing->is_featured && $listing->isFeaturedActive())
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                                <i class="fas fa-star mr-1"></i> Featured do {{ $listing->featured_until->format('d.m.Y') }}
                            </span>
                        @endif
                    @else
                        <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-semibold">
                            <i class="fas fa-info-circle mr-1"></i> Bez promocije
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Available Upgrades -->
    @if($topPackages->count() > 0 || $featuredPackages->count() > 0)
        
        <!-- Info Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-600 text-xl mr-3 mt-1"></i>
                <div>
                    <h4 class="font-semibold text-blue-900 mb-1">Zašto promovisati oglas?</h4>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li><i class="fas fa-check text-blue-600 mr-2"></i> Vaš oglas se prikazuje na vrhu liste</li>
                        <li><i class="fas fa-check text-blue-600 mr-2"></i> Povećava se vidljivost i broj pregleda</li>
                        <li><i class="fas fa-check text-blue-600 mr-2"></i> Brža prodaja ili iznajmljivanje</li>
                        <li><i class="fas fa-check text-blue-600 mr-2"></i> Istaknuti oglasi privlače više pažnje</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Top Packages -->
        @if($topPackages->count() > 0)
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">
                <i class="fas fa-arrow-up text-blue-600"></i> Top Paketi
                <span class="text-sm font-normal text-gray-600 ml-2">- Prikazan na vrhu liste</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($topPackages as $package)
                    <form action="{{ route('listings.apply-promotion', $listing->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="package_id" value="{{ $package->id }}">
                        
                        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow overflow-hidden border-2 border-blue-200 hover:border-blue-500 cursor-pointer">
                            <!-- Header -->
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white p-6">
                                <div class="text-center">
                                    <div class="text-sm font-semibold uppercase tracking-wide mb-2">Top Paket</div>
                                    <h3 class="text-2xl font-bold mb-2">{{ $package->name }}</h3>
                                    <div class="text-3xl font-bold">
                                        {{ number_format($package->price, 0, ',', '.') }}
                                        <span class="text-lg">{{ $package->currency }}</span>
                                    </div>
                                    <div class="text-sm opacity-90 mt-2">{{ $package->duration_days }} dana</div>
                                </div>
                            </div>

                            <!-- Body -->
                            <div class="p-6">
                                @if($package->description)
                                    <p class="text-gray-600 text-sm mb-4">{{ $package->description }}</p>
                                @endif

                                @if($package->features && count($package->features) > 0)
                                    <ul class="space-y-2 mb-6">
                                        @foreach($package->features as $feature)
                                            <li class="flex items-start text-sm">
                                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                                <span class="text-gray-700">{{ $feature }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold transition-colors">
                                    <i class="fas fa-rocket mr-2"></i> Izaberi ovaj paket
                                </button>
                            </div>
                        </div>
                    </form>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Featured Packages -->
        @if($featuredPackages->count() > 0)
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">
                <i class="fas fa-star text-yellow-600"></i> Featured Paketi
                <span class="text-sm font-normal text-gray-600 ml-2">- Istaknut na početnoj strani</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredPackages as $package)
                    <form action="{{ route('listings.apply-promotion', $listing->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="package_id" value="{{ $package->id }}">
                        
                        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow overflow-hidden border-2 border-yellow-200 hover:border-yellow-500 cursor-pointer relative">
                            <!-- Popular Badge -->
                            @if($loop->index === 0)
                                <div class="absolute top-0 right-0 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg">
                                    NAJPOPULARNIJE
                                </div>
                            @endif

                            <!-- Header -->
                            <div class="bg-gradient-to-r from-yellow-500 to-orange-600 text-white p-6">
                                <div class="text-center">
                                    <div class="text-sm font-semibold uppercase tracking-wide mb-2">
                                        <i class="fas fa-star mr-1"></i> Featured
                                    </div>
                                    <h3 class="text-2xl font-bold mb-2">{{ $package->name }}</h3>
                                    <div class="text-3xl font-bold">
                                        {{ number_format($package->price, 0, ',', '.') }}
                                        <span class="text-lg">{{ $package->currency }}</span>
                                    </div>
                                    <div class="text-sm opacity-90 mt-2">{{ $package->duration_days }} dana</div>
                                </div>
                            </div>

                            <!-- Body -->
                            <div class="p-6">
                                @if($package->description)
                                    <p class="text-gray-600 text-sm mb-4">{{ $package->description }}</p>
                                @endif

                                @if($package->features && count($package->features) > 0)
                                    <ul class="space-y-2 mb-6">
                                        @foreach($package->features as $feature)
                                            <li class="flex items-start text-sm">
                                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                                <span class="text-gray-700">{{ $feature }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                                <button type="submit" class="w-full bg-gradient-to-r from-yellow-500 to-orange-600 text-white py-3 rounded-lg hover:from-yellow-600 hover:to-orange-700 font-semibold transition-colors shadow-md">
                                    <i class="fas fa-crown mr-2"></i> Izaberi ovaj paket
                                </button>
                            </div>
                        </div>
                    </form>
                @endforeach
            </div>
        </div>
        @endif

    @else
        <!-- No Upgrades Available -->
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <i class="fas fa-trophy text-6xl text-yellow-500 mb-4"></i>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Već imate najbolji paket!</h3>
            <p class="text-gray-600 mb-6">Vaš oglas je već maksimalno promovisan.</p>
            <a href="{{ route('dashboard.my-listings') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                <i class="fas fa-arrow-left mr-2"></i> Nazad na oglase
            </a>
        </div>
    @endif

</div>
@endsection