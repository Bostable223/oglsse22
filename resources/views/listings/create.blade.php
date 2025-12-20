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

            <!-- Listing Type - Button Style -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tip oglasa *</label>
                <div class="flex gap-3">
                    <button type="button" onclick="selectListingType('sale')" 
                            class="listing-type-btn flex-1 px-6 py-3 border-2 rounded-lg font-semibold transition-all {{ old('listing_type', 'sale') == 'sale' ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-gray-300 bg-white text-gray-700 hover:border-gray-400' }}"
                            data-type="sale">
                        <i class="fas fa-tag mr-2"></i> Prodaja
                    </button>
                    <button type="button" onclick="selectListingType('rent')"
                            class="listing-type-btn flex-1 px-6 py-3 border-2 rounded-lg font-semibold transition-all {{ old('listing_type') == 'rent' ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-gray-300 bg-white text-gray-700 hover:border-gray-400' }}"
                            data-type="rent">
                        <i class="fas fa-home mr-2"></i> Izdavanje
                    </button>
                </div>
                <input type="hidden" name="listing_type" id="listingTypeInput" value="{{ old('listing_type', 'sale') }}">
                @error('listing_type')
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

            <!-- Price in EUR only -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cena *</label>
                <div class="relative">
                    <input type="number" name="price" value="{{ old('price') }}" required min="0" step="0.01"
                           placeholder="50000"
                           class="w-full px-4 py-2 pr-16 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('price') border-red-500 @enderror">
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600 font-semibold">EUR</div>
                </div>
                <input type="hidden" name="currency" value="EUR">
                @error('price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
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
                    $oldFeatures = old('features', []);
                @endphp
                
                @foreach($features as $feature)
                    <label class="flex items-center">
                        <input type="checkbox" name="features[]" value="{{ $feature }}" 
                               {{ in_array($feature, $oldFeatures) ? 'checked' : '' }}
                               class="mr-2 text-blue-600">
                        <span class="text-sm">{{ $feature }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Images with Drag & Drop Reordering -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-900">Fotografije</h2>
            <p class="text-sm text-gray-600 mb-4">Dodajte slike vaše nekretnine (maksimalno 5MB po slici). Prevucite slike da promenite redosled.</p>
            
            @if(session('validation_failed'))
                <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Molimo vas ponovo dodajte slike jer su se izgubile tokom validacije.
                    </p>
                </div>
            @endif
            
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

            <!-- Image Preview with Drag & Drop -->
            <div id="imagePreview" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4"></div>
            
            @if($errors->has('images.*') || $errors->has('images'))
                <p class="text-red-500 text-sm mt-2">
                    {{ $errors->first('images.*') ?? $errors->first('images') }}
                </p>
            @endif
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
// Listing Type Selection
function selectListingType(type) {
    document.getElementById('listingTypeInput').value = type;
    
    document.querySelectorAll('.listing-type-btn').forEach(btn => {
        if (btn.dataset.type === type) {
            btn.classList.remove('border-gray-300', 'bg-white', 'text-gray-700');
            btn.classList.add('border-blue-600', 'bg-blue-50', 'text-blue-700');
        } else {
            btn.classList.remove('border-blue-600', 'bg-blue-50', 'text-blue-700');
            btn.classList.add('border-gray-300', 'bg-white', 'text-gray-700');
        }
    });
}

// Image Preview with Drag & Drop Reordering
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('imageInput');
    const previewContainer = document.getElementById('imagePreview');
    let selectedFiles = [];

    imageInput.addEventListener('change', function(event) {
        const files = Array.from(event.target.files);
        if (files.length > 5) {
            alert('Možete dodati najviše 5 slika.');
            imageInput.value = '';
            selectedFiles = [];
            renderPreviews();
            return;
        }
        selectedFiles = files;
        renderPreviews();
    });

    function renderPreviews() {
        previewContainer.innerHTML = '';
        selectedFiles.forEach((file, index) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'relative group';
            wrapper.dataset.index = index;

            // Image
            const img = document.createElement('img');
            img.className = 'w-full h-32 object-cover rounded-lg';
            // Preview the image
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);

            wrapper.appendChild(img);

            // Label showing which number
            const labelDiv = document.createElement('div');
            labelDiv.className = 'absolute top-2 left-2 bg-blue-600 text-white px-2 py-1 rounded text-xs font-semibold pointer-events-none';
            labelDiv.textContent = index === 0 ? 'Glavna slika' : `Slika ${index + 1}`;
            wrapper.appendChild(labelDiv);

            // Remove button
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = '×';
            btn.title = 'Remove';
            btn.className = 'absolute top-2 right-2 bg-red-600 text-white w-7 h-7 rounded-full hover:bg-red-700 z-10';
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                selectedFiles.splice(index, 1);
                renderPreviews();
            });
            wrapper.appendChild(btn);

            // Make wrapper draggable
            wrapper.draggable = true;
            wrapper.addEventListener('dragstart', dragStart);
            wrapper.addEventListener('dragover', dragOver);
            wrapper.addEventListener('drop', drop);
            wrapper.addEventListener('dragend', dragEnd);

            previewContainer.appendChild(wrapper);
        });

        updateInputFiles();
    }

    let dragSrcIndex = null;

    function dragStart(e) {
        dragSrcIndex = Number(e.currentTarget.dataset.index);
        e.dataTransfer.effectAllowed = 'move';
    }

    function dragOver(e) {
        e.preventDefault(); // necessary for drop
    }

    function drop(e) {
        e.stopPropagation();
        const target = e.currentTarget;
        const dropIndex = Number(target.dataset.index);
        if (dragSrcIndex !== null && dropIndex !== dragSrcIndex) {
            // swap
            const temp = selectedFiles[dragSrcIndex];
            selectedFiles[dragSrcIndex] = selectedFiles[dropIndex];
            selectedFiles[dropIndex] = temp;
            renderPreviews();
        }
    }

    function dragEnd(e) {
        dragSrcIndex = null;
    }

    function updateInputFiles() {
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        imageInput.files = dataTransfer.files;
    }
});

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