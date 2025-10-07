@extends('layouts.app')

@section('title', 'Izmeni profil')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Izmeni profil</h1>
        <p class="text-gray-600 mt-2">Ažurirajte svoje podatke</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="text-center mb-6">
                    <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="w-32 h-32 rounded-full mx-auto mb-4">
                    <h3 class="font-semibold text-lg text-gray-900">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                </div>
                
                <div class="space-y-2">
                    <a href="#profile" class="block px-4 py-2 bg-blue-50 text-blue-600 rounded-lg font-semibold">
                        <i class="fas fa-user mr-2"></i> Osnovni podaci
                    </a>
                    <a href="#password" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-lock mr-2"></i> Promeni lozinku
                    </a>
                    <a href="{{ route('dashboard.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-arrow-left mr-2"></i> Nazad
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Profile Information -->
            <div class="bg-white rounded-lg shadow-sm p-6" id="profile">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Osnovni podaci</h2>
                
                <form action="{{ route('dashboard.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Avatar Upload -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Profilna slika</label>
                        <div class="flex items-center gap-4">
                            <img src="{{ $user->avatarUrl() }}" alt="Avatar" class="w-20 h-20 rounded-full" id="avatarPreview">
                            <div>
                                <input type="file" name="avatar" accept="image/*" class="hidden" id="avatarInput" onchange="previewAvatar(event)">
                                <label for="avatarInput" class="cursor-pointer bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 inline-block">
                                    <i class="fas fa-camera mr-2"></i> Promeni sliku
                                </label>
                                <p class="text-xs text-gray-500 mt-1">JPG, PNG maksimalno 2MB</p>
                            </div>
                        </div>
                        @error('avatar')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Name -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ime i prezime *</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Telefon</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                               placeholder="+381 11 123 4567"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- City -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Grad</label>
                        <input type="text" name="city" value="{{ old('city', $user->city) }}"
                               placeholder="Beograd"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('city')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bio -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">O meni</label>
                        <textarea name="bio" rows="4" 
                                  placeholder="Kratko predstavite sebe..."
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('bio', $user->bio) }}</textarea>
                        <p class="text-sm text-gray-500 mt-1">Maksimalno 500 karaktera</p>
                        @error('bio')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded-lg hover:bg-blue-700 font-semibold">
                            <i class="fas fa-save mr-2"></i> Sačuvaj izmene
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password -->
            <div class="bg-white rounded-lg shadow-sm p-6" id="password">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Promeni lozinku</h2>
                
                <form action="{{ route('dashboard.profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Current Password -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Trenutna lozinka *</label>
                        <input type="password" name="current_password" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('current_password') border-red-500 @enderror">
                        @error('current_password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nova lozinka *</label>
                        <input type="password" name="password" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                        <p class="text-sm text-gray-500 mt-1">Minimum 8 karaktera</p>
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Potvrdi novu lozinku *</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end">
                        <button type="submit" class="bg-green-600 text-white px-8 py-2 rounded-lg hover:bg-green-700 font-semibold">
                            <i class="fas fa-key mr-2"></i> Promeni lozinku
                        </button>
                    </div>
                </form>
            </div>

            <!-- Account Stats -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Statistika naloga</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="text-gray-500 text-sm mb-1">Ukupno oglasa</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $user->listings->count() }}</div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="text-gray-500 text-sm mb-1">Aktivni oglasi</div>
                        <div class="text-2xl font-bold text-green-600">{{ $user->activeListings->count() }}</div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="text-gray-500 text-sm mb-1">Omiljeni</div>
                        <div class="text-2xl font-bold text-red-600">{{ $user->favorites->count() }}</div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="text-gray-500 text-sm mb-1">Član od</div>
                        <div class="text-sm font-semibold text-gray-900">{{ $user->created_at->format('d.m.Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewAvatar(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}
</script>
@endpush
@endsection