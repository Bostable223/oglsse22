@extends('layouts.app')

@section('title', 'Admin - Izmeni korisnika')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('admin.users') }}" class="text-blue-600 hover:text-blue-700 mb-4 inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Nazad na korisnike
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Izmeni korisnika</h1>
        <p class="text-gray-600 mt-2">Ažurirajte informacije korisnika</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Sidebar - User Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                <div class="text-center mb-6">
                    <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" 
                         class="w-32 h-32 rounded-full mx-auto mb-4 border-4 border-gray-100">
                    <h3 class="font-semibold text-lg text-gray-900">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                </div>
                
                <div class="space-y-4 border-t border-gray-200 pt-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">ID:</span>
                        <span class="font-semibold text-gray-900">#{{ $user->id }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Uloga:</span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                            {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Oglasa:</span>
                        <span class="font-semibold text-gray-900">{{ $user->listings_count }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Registrovan:</span>
                        <span class="font-semibold text-gray-900">{{ $user->created_at->format('d.m.Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Status:</span>
                        @if($user->is_active)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Aktivan
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                Deaktiviran
                            </span>
                        @endif
                    </div>
                    @if($user->last_login_at)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Poslednja prijava:</span>
                        <span class="text-xs text-gray-900">{{ $user->last_login_at->diffForHumans() }}</span>
                    </div>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="mt-6 pt-6 border-t border-gray-200 space-y-2">
                    <a href="{{ route('dashboard.my-listings') }}?user={{ $user->id }}" 
                       class="block w-full text-center px-4 py-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 text-sm font-semibold">
                        <i class="fas fa-list mr-2"></i> Vidi oglase
                    </a>
                    
                    <form action="{{ route('admin.users.toggle-active', $user->id) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="w-full px-4 py-2 rounded-lg text-sm font-semibold
                                {{ $user->is_active ? 'bg-orange-100 text-orange-600 hover:bg-orange-200' : 'bg-green-100 text-green-600 hover:bg-green-200' }}">
                            <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }} mr-2"></i>
                            {{ $user->is_active ? 'Deaktiviraj' : 'Aktiviraj' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content - Edit Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Osnovni podaci</h2>
                
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Ime i prezime *
                        </label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email *
                        </label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Telefon
                        </label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                               placeholder="+381 11 123 4567"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- City -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Grad
                        </label>
                        <input type="text" name="city" value="{{ old('city', $user->city) }}"
                               placeholder="Beograd"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('city') border-red-500 @enderror">
                        @error('city')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bio -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            O korisniku
                        </label>
                        <textarea name="bio" rows="4" 
                                  placeholder="Biografija ili opis korisnika..."
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bio') border-red-500 @enderror">{{ old('bio', $user->bio) }}</textarea>
                        <p class="text-sm text-gray-500 mt-1">Maksimalno 500 karaktera</p>
                        @error('bio')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status Toggles -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h3 class="font-semibold text-gray-900 mb-4">Status naloga</h3>
                        
                        <div class="space-y-3">
                            <!-- Email Verified -->
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="is_verified" value="1" 
                                       {{ old('is_verified', $user->is_verified) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-3 text-sm">
                                    <span class="font-medium text-gray-900">Email verifikovan</span>
                                    <span class="block text-gray-500">Korisnik je potvrdio email adresu</span>
                                </span>
                            </label>

                            <!-- Active Status -->
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" 
                                       {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-3 text-sm">
                                    <span class="font-medium text-gray-900">Aktivan nalog</span>
                                    <span class="block text-gray-500">Korisnik može da se prijavi i koristi sistem</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.users') }}" 
                           class="px-6 py-2 text-gray-600 hover:text-gray-800 font-semibold">
                            Otkaži
                        </a>
                        <button type="submit" 
                                class="bg-blue-600 text-white px-8 py-2 rounded-lg hover:bg-blue-700 font-semibold">
                            <i class="fas fa-save mr-2"></i> Sačuvaj izmene
                        </button>
                    </div>
                </form>
            </div>

            <!-- Danger Zone -->
            <div class="bg-white rounded-lg shadow-sm p-6 mt-6 border-2 border-red-200">
                <h2 class="text-xl font-semibold text-red-600 mb-4">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Opasna zona
                </h2>
                <p class="text-gray-600 mb-4">
                    Nakon brisanja naloga, svi podaci korisnika će biti trajno uklonjeni.
                </p>
                
                @if($user->listings_count > 0)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-yellow-600 text-xl mr-3 mt-1"></i>
                            <div>
                                <p class="text-sm text-yellow-800">
                                    <strong>Upozorenje:</strong> Ovaj korisnik ima {{ $user->listings_count }} 
                                    {{ $user->listings_count === 1 ? 'oglas' : 'oglasa' }}.
                                    Svi oglasi će biti obrisani zajedno sa nalogom.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" 
                      onsubmit="return confirm('Da li ste SIGURNI da želite TRAJNO obrisati ovog korisnika i SVE njegove oglase? Ova akcija se NE MOŽE poništiti!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 font-semibold">
                        <i class="fas fa-trash mr-2"></i> Obriši korisnika
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection