@extends('layouts.app')

@section('title', 'Izmeni oglas')

@section('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Dashboard', 'url' => route('dashboard.index')],
        ['title' => 'Moji oglasi', 'url' => route('dashboard.my-listings')],
        ['title' => 'Izmeni oglas', 'url' => route('listings.edit', $listing->slug)]
    ]" />
@endsection

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Izmeni oglas</h1>
        <p class="text-gray-600 mt-2">Ažurirajte podatke o vašoj nekretnini</p>
    </div>

    <form action="{{ route('listings.update', $listing->slug) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-sm p-6">
        @csrf
        @method('PUT')

        <!-- Existing Images -->
        @if($listing->images->count() > 0)
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-900">Trenutne slike</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($listing->images as $image)
                    <div class="relative">
                        <img src="{{ $image->thumbnailUrl() }}" alt="Slika" class="w-full h-32 object-cover rounded-lg">
                        @if($image->is_primary)
                            <div class="absolute top-2 left-2 bg-blue-600 text-white px-2 py-1 rounded text-xs">
                                Glavna
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            <p class="text-sm text-gray-500 mt-2">Nove slike će se dodati postojećim slikama</p>
        </div>
        @endif

        <!-- Basic Information -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-900">Osnovne informacije</h2>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategorija *</label>
                <select name="category_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $listing->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Naslov oglasa *</label>
                <input type="text" name="title" value="{{ old('title', $listing->title) }}" required 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Opis *</label>
                <textarea name="description" rows="6" required 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description', $listing->description) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tip oglasa *</label>
                <div class="flex gap-4">
                    <label class="flex items-center">
                        <input type="radio" name="listing_type" value="sale" {{ old('listing_type', $listing->listing_type) == 'sale' ? 'checked' : '' }} class="mr-2">
                        <span>Prodaja</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="listing_type" value="rent" {{ old('listing_type', $listing->listing_type) == 'rent' ? 'checked' : '' }} class="mr-2">
                        <span>Izdavanje</span>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cena *</label>
                    <input type="number" name="price" value="{{ old('price', $listing->price) }}" required min="0" step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Valuta *</label>
                    <select name="currency" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="RSD" {{ old('currency', $listing->currency) == 'RSD' ? 'selected' : '' }}>RSD</option>
                        <option value="EUR" {{ old('currency', $listing->currency) == 'EUR' ? 'selected' : '' }}>EUR</option>
                        <option value="USD" {{ old('currency', $listing->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Location -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-900">Lokacija</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Grad *</label>
                    <input type="text" name="city" id="cityInput" value="{{ old('city', $listing->city) }}" required 
                           autocomplete="off"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <div id="cityDropdown" class="hidden absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto"></div>
                </div>
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Opština</label>
                    <input type="text" name="municipality" id="municipalityInput" value="{{ old('municipality', $listing->municipality) }}" 
                           autocomplete="off"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <div id="municipalityDropdown" class="hidden absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto"></div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Adresa</label>
                <input type="text" name="address" value="{{ old('address', $listing->address) }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <!-- Property Details -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-900">Detalji nekretnine</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Površina (m²)</label>
                    <input type="number" name="area" value="{{ old('area', $listing->area) }}" min="0" step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Broj soba</label>
                    <input type="number" name="rooms" value="{{ old('rooms', $listing->rooms) }}" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Broj kupatila</label>
                    <input type="number" name="bathrooms" value="{{ old('bathrooms', $listing->bathrooms) }}" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sprat</label>
                    <input type="number" name="floor" value="{{ old('floor', $listing->floor) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ukupno spratova</label>
                    <input type="number" name="total_floors" value="{{ old('total_floors', $listing->total_floors) }}" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Godina gradnje</label>
                    <input type="number" name="year_built" value="{{ old('year_built', $listing->year_built) }}" min="1800" max="{{ date('Y') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- Features -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-900">Dodatne karakteristike</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @php
                    $features = [
                        'Parking', 'Garaža', 'Lift', 'Balkon', 'Terasa', 'Podrum',
                        'Klima', 'Centralno grejanje', 'Interfon', 'Video nadzor',
                        'Kablovska TV', 'Internet', 'Telefon', 'Obezbeđenje',
                        'Namešten', 'Polunamešteno', 'Renoviran', 'Novogradnja'
                    ];
                    $selectedFeatures = old('features', $listing->features ?? []);
                @endphp
                
                @foreach($features as $feature)
                    <label class="flex items-center">
                        <input type="checkbox" name="features[]" value="{{ $feature }}" 
                               {{ in_array($feature, $selectedFeatures) ? 'checked' : '' }}
                               class="mr-2 text-blue-600">
                        <span class="text-sm">{{ $feature }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Add New Images -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-900">Dodaj nove fotografije</h2>
            
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                <input type="file" name="images[]" multiple accept="image/*" 
                       class="hidden" id="imageInput"
                       onchange="previewImages(event)">
                <label for="imageInput" class="cursor-pointer">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                    <p class="text-gray-600">Kliknite da dodate nove slike</p>
                    <p class="text-sm text-gray-500 mt-2">JPG, PNG do 5MB</p>
                </label>
            </div>

            <div id="imagePreview" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4"></div>
        </div>

        <!-- Contact Information -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-900">Kontakt informacije</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ime kontakt osobe</label>
                    <input type="text" name="contact_name" value="{{ old('contact_name', $listing->contact_name) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Telefon *</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $listing->contact_phone) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" name="contact_email" value="{{ old('contact_email', $listing->contact_email) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
            <a href="{{ route('listings.show', $listing->slug) }}" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-times mr-2"></i> Otkaži
            </a>
            <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                <i class="fas fa-save mr-2"></i> Sačuvaj izmene
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function previewImages(event) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    const files = event.target.files;
    
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'relative';
            div.innerHTML = `
                <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg">
                <div class="absolute top-2 left-2 bg-green-600 text-white px-2 py-1 rounded text-xs">
                    Nova slika ${i + 1}
                </div>
            `;
            preview.appendChild(div);
        };
        
        reader.readAsDataURL(file);
    }
}
</script>
@endpush
@endsection