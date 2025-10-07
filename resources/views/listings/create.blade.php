@extends('layouts.app')

@section('title', 'Postavi novi oglas')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Postavi novi oglas</h1>
        <p class="text-gray-600 mt-2">Popunite sve podatke o vašoj nekretnini</p>
    </div>

    <form action="{{ route('listings.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-sm p-6">
        @csrf

        <!-- Basic Information -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-900">Osnovne informacije</h2>
            
            <!-- Category -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategorija *</label>
                <select name="category_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('category_id') border-red-500 @enderror">
                    <option value="">Izaberite kategoriju</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Title -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Naslov oglasa *</label>
                <input type="text" name="title" value="{{ old('title') }}" required 
                       placeholder="npr. Dvosoban stan u centru Beograda"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror">
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Opis *</label>
                <textarea name="description" rows="6" required 
                          placeholder="Detaljno opišite vašu nekretninu..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                <p class="text-sm text-gray-500 mt-1">Minimum 50 karaktera</p>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Listing Type -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tip oglasa *</label>
                <div class="flex gap-4">
                    <label class="flex items-center">
                        <input type="radio" name="listing_type" value="sale" {{ old('listing_type', 'sale') == 'sale' ? 'checked' : '' }} class="mr-2">
                        <span>Prodaja</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="listing_type" value="rent" {{ old('listing_type') == 'rent' ? 'checked' : '' }} class="mr-2">
                        <span>Izdavanje</span>
                    </label>
                </div>
                @error('listing_type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Price -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cena *</label>
                    <input type="number" name="price" value="{{ old('price') }}" required min="0" step="0.01"
                           placeholder="50000"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('price') border-red-500 @enderror">
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Valuta *</label>
                    <select name="currency" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="RSD" {{ old('currency', 'RSD') == 'RSD' ? 'selected' : '' }}>RSD (Dinar)</option>
                        <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR (Evro)</option>
                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD (Dolar)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Location -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-900">Lokacija</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Grad *</label>
                    <input type="text" name="city" id="cityInput" value="{{ old('city') }}" required 
                           placeholder="Beograd"
                           autocomplete="off"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('city') border-red-500 @enderror">
                    <div id="cityDropdown" class="hidden absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto"></div>
                    @error('city')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Opština</label>
                    <input type="text" name="municipality" id="municipalityInput" value="{{ old('municipality') }}" 
                           placeholder="Vračar"
                           autocomplete="off"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <div id="municipalityDropdown" class="hidden absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto"></div>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Adresa</label>
                <input type="text" name="address" value="{{ old('address') }}" 
                       placeholder="Bulevar kralja Aleksandra 123"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <!-- Property Details -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-900">Detalji nekretnine</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Površina (m²)</label>
                    <input type="number" name="area" value="{{ old('area') }}" min="0" step="0.01"
                           placeholder="65"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Broj soba</label>
                    <input type="number" name="rooms" value="{{ old('rooms') }}" min="0"
                           placeholder="3"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Broj kupatila</label>
                    <input type="number" name="bathrooms" value="{{ old('bathrooms') }}" min="0"
                           placeholder="1"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sprat</label>
                    <input type="number" name="floor" value="{{ old('floor') }}"
                           placeholder="3"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ukupno spratova</label>
                    <input type="number" name="total_floors" value="{{ old('total_floors') }}" min="0"
                           placeholder="5"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Godina gradnje</label>
                    <input type="number" name="year_built" value="{{ old('year_built') }}" min="1800" max="{{ date('Y') }}"
                           placeholder="2015"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- Features -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-900">Dodatne karakteristike</h2>
            <p class="text-sm text-gray-600 mb-4">Izaberite karakteristike koje vaša nekretnina poseduje</p>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @php
                    $features = [
                        'Parking', 'Garaža', 'Lift', 'Balkon', 'Terasa', 'Podrum',
                        'Klima', 'Centralno grejanje', 'Interfon', 'Video nadzor',
                        'Kablovska TV', 'Internet', 'Telefon', 'Obezbeđenje',
                        'Namešten', 'Polunamešteno', 'Renoviran', 'Novogradnja'
                    ];
                @endphp
                
                @foreach($features as $feature)
                    <label class="flex items-center">
                        <input type="checkbox" name="features[]" value="{{ $feature }}" 
                               {{ in_array($feature, old('features', [])) ? 'checked' : '' }}
                               class="mr-2 text-blue-600">
                        <span class="text-sm">{{ $feature }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Images -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-900">Fotografije</h2>
            <p class="text-sm text-gray-600 mb-4">Dodajte slike vaše nekretnine (maksimalno 5MB po slici)</p>
            
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                <input type="file" name="images[]" multiple accept="image/*" 
                       class="hidden" id="imageInput"
                       onchange="previewImages(event)">
                <label for="imageInput" class="cursor-pointer">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                    <p class="text-gray-600">Kliknite da izaberete slike ili ih prevucite ovde</p>
                    <p class="text-sm text-gray-500 mt-2">JPG, PNG do 5MB</p>
                </label>
            </div>

            <!-- Image Preview -->
            <div id="imagePreview" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4"></div>
            
            @error('images.*')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Contact Information -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-900">Kontakt informacije</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ime kontakt osobe</label>
                    <input type="text" name="contact_name" value="{{ old('contact_name', auth()->user()->name) }}"
                           placeholder="Ime i prezime"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Telefon *</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', auth()->user()->phone) }}" required
                           placeholder="+381 11 123 4567"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('contact_phone') border-red-500 @enderror">
                    @error('contact_phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" name="contact_email" value="{{ old('contact_email', auth()->user()->email) }}"
                       placeholder="email@example.com"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
            <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-times mr-2"></i> Otkaži
            </a>
            <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                <i class="fas fa-check mr-2"></i> Objavi oglas
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Image Preview Function
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
                <div class="absolute top-2 left-2 bg-blue-600 text-white px-2 py-1 rounded text-xs">
                    ${i === 0 ? 'Glavna slika' : 'Slika ' + (i + 1)}
                </div>
            `;
            preview.appendChild(div);
        };
        
        reader.readAsDataURL(file);
    }
}

// Location Autocomplete Data
const cities = [
    'Beograd', 'Novi Sad', 'Niš', 'Kragujevac', 'Subotica', 'Zrenjanin', 'Pančevo', 
    'Čačak', 'Kruševac', 'Kraljevo', 'Novi Pazar', 'Smederevo', 'Leskovac', 'Užice',
    'Vranje', 'Zaječar', 'Šabac', 'Sombor', 'Požarevac', 'Pirot', 'Valjevo', 'Kikinda',
    'Sremska Mitrovica', 'Jagodina', 'Vršac', 'Bor', 'Prokuplje', 'Negotin'
];

const municipalities = {
    'Beograd': [
        'Stari Grad', 'Vračar', 'Savski Venac', 'Palilula', 'Zvezdara', 'Novi Beograd',
        'Zemun', 'Čukarica', 'Rakovica', 'Voždovac', 'Grocka', 'Surčin', 'Barajevo',
        'Lazarevac', 'Mladenovac', 'Obrenovac', 'Sopot'
    ],
    'Novi Sad': [
        'Novi Sad - centar', 'Petrovaradin', 'Liman', 'Detelinara', 'Grbavica', 'Telep',
        'Bistrica', 'Adamovićevo Naselje', 'Satellite', 'Novo Naselje'
    ],
    'Niš': [
        'Medijana', 'Pantelej', 'Crveni Krst', 'Palilula', 'Niška Banja'
    ]
};

// City Autocomplete
const cityInput = document.getElementById('cityInput');
const cityDropdown = document.getElementById('cityDropdown');

cityInput.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase().trim();
    
    if (searchTerm.length < 1) {
        cityDropdown.classList.add('hidden');
        return;
    }
    
    const filtered = cities.filter(city => 
        city.toLowerCase().includes(searchTerm)
    );
    
    if (filtered.length === 0) {
        cityDropdown.classList.add('hidden');
        return;
    }
    
    cityDropdown.innerHTML = filtered.map(city => 
        `<div class="px-4 py-2 hover:bg-blue-50 cursor-pointer city-item" data-city="${city}">
            <i class="fas fa-city text-blue-600 mr-2"></i>${city}
        </div>`
    ).join('');
    
    cityDropdown.classList.remove('hidden');
    
    document.querySelectorAll('.city-item').forEach(item => {
        item.addEventListener('click', function() {
            cityInput.value = this.dataset.city;
            cityDropdown.classList.add('hidden');
            
            // Clear municipality when city changes
            document.getElementById('municipalityInput').value = '';
        });
    });
});

// Municipality Autocomplete
const municipalityInput = document.getElementById('municipalityInput');
const municipalityDropdown = document.getElementById('municipalityDropdown');

municipalityInput.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase().trim();
    const selectedCity = cityInput.value;
    
    if (searchTerm.length < 1) {
        municipalityDropdown.classList.add('hidden');
        return;
    }
    
    // Get municipalities for selected city
    let municipalityList = municipalities[selectedCity] || [];
    
    // If no specific city selected, show all municipalities
    if (!selectedCity) {
        municipalityList = Object.values(municipalities).flat();
    }
    
    const filtered = municipalityList.filter(m => 
        m.toLowerCase().includes(searchTerm)
    );
    
    if (filtered.length === 0) {
        municipalityDropdown.classList.add('hidden');
        return;
    }
    
    municipalityDropdown.innerHTML = filtered.map(municipality => 
        `<div class="px-4 py-2 hover:bg-blue-50 cursor-pointer municipality-item" data-municipality="${municipality}">
            <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>${municipality}
        </div>`
    ).join('');
    
    municipalityDropdown.classList.remove('hidden');
    
    document.querySelectorAll('.municipality-item').forEach(item => {
        item.addEventListener('click', function() {
            municipalityInput.value = this.dataset.municipality;
            municipalityDropdown.classList.add('hidden');
        });
    });
});

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (!cityInput.contains(e.target) && !cityDropdown.contains(e.target)) {
        cityDropdown.classList.add('hidden');
    }
    if (!municipalityInput.contains(e.target) && !municipalityDropdown.contains(e.target)) {
        municipalityDropdown.classList.add('hidden');
    }
});
</script>
@endpush
@endsection