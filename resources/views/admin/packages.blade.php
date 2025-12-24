@extends('layouts.app')

@section('title', 'Admin - Paketi')

@section('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Admin Panel', 'url' => route('admin.dashboard')],
        ['title' => 'Paketi', 'url' => route('admin.packages')]
    ]" />
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Upravljanje paketima</h1>
            <p class="text-gray-600 mt-2">Dodajte i upravljajte promocionim paketima</p>
        </div>
        <button onclick="openAddModal()" 
                class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
            <i class="fas fa-plus mr-2"></i> Novi paket
        </button>
    </div>

    <!-- Packages Grid by Type -->
    @if($packages->count() > 0)
        
        <!-- Top Packages -->
        @php
            $topPackages = $packages->where('type', 'top');
        @endphp
        
        @if($topPackages->count() > 0)
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
                <i class="fas fa-arrow-up text-blue-600"></i> Top Paketi
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($topPackages as $package)
                    @include('admin.partials.package-card', ['package' => $package])
                @endforeach
            </div>
        </div>
        @endif

        <!-- Featured Packages -->
        @php
            $featuredPackages = $packages->where('type', 'featured');
        @endphp
        
        @if($featuredPackages->count() > 0)
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
                <i class="fas fa-star text-yellow-600"></i> Istaknuti Paketi
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredPackages as $package)
                    @include('admin.partials.package-card', ['package' => $package])
                @endforeach
            </div>
        </div>
        @endif

    @else
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Nema paketa</h3>
            <p class="text-gray-500 mb-6">Dodajte prvi paket za promociju oglasa</p>
        </div>
    @endif
</div>

<!-- Add Package Modal -->
<div id="addPackageModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4 my-8">
        <h2 class="text-2xl font-bold mb-6">Dodaj novi paket</h2>
        <form action="{{ route('admin.packages.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Name -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Naziv paketa *</label>
                    <input type="text" name="name" required 
                           placeholder="7 dana - Top oglas"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tip paketa *</label>
                    <select name="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Izaberite tip</option>
                        <option value="top">Top (prikazan na vrhu)</option>
                        <option value="featured">Featured (istaknut)</option>
                    </select>
                </div>

                <!-- Duration -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trajanje (dana) *</label>
                    <input type="number" name="duration_days" required min="1" max="365" value="7"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Price -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cena *</label>
                    <input type="number" name="price" required min="0" step="0.01" value="500"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Currency -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Valuta *</label>
                    <select name="currency" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="RSD">RSD</option>
                        <option value="EUR">EUR</option>
                        <option value="USD">USD</option>
                    </select>
                </div>

                <!-- Order -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Redosled prikaza</label>
                    <input type="number" name="order" value="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Manji broj = viša pozicija</p>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Opis</label>
                    <textarea name="description" rows="3" 
                              placeholder="Kratak opis paketa..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <!-- Features -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Karakteristike paketa</label>
                    <div id="featuresContainer">
                        <div class="flex gap-2 mb-2">
                            <input type="text" name="features[]" 
                                   placeholder="Npr: Prikazuje se na vrhu liste"
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <button type="button" onclick="addFeatureField()" 
                                    class="px-4 py-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Dodajte prednosti ovog paketa</p>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeAddModal()" 
                        class="px-6 py-2 text-gray-600 hover:text-gray-800">
                    Otkaži
                </button>
                <button type="submit" 
                        class="bg-blue-600 text-white px-8 py-2 rounded-lg hover:bg-blue-700 font-semibold">
                    Dodaj paket
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Package Modal -->
<div id="editPackageModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4 my-8">
        <h2 class="text-2xl font-bold mb-6">Izmeni paket</h2>
        <form id="editPackageForm" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Name -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Naziv paketa *</label>
                    <input type="text" name="name" id="edit_name" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tip paketa *</label>
                    <select name="type" id="edit_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="top">Top (prikazan na vrhu)</option>
                        <option value="featured">Featured (istaknut)</option>
                    </select>
                </div>

                <!-- Duration -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trajanje (dana) *</label>
                    <input type="number" name="duration_days" id="edit_duration" required min="1" max="365"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Price -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cena *</label>
                    <input type="number" name="price" id="edit_price" required min="0" step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Currency -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Valuta *</label>
                    <select name="currency" id="edit_currency" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="RSD">RSD</option>
                        <option value="EUR">EUR</option>
                        <option value="USD">USD</option>
                    </select>
                </div>

                <!-- Order -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Redosled prikaza</label>
                    <input type="number" name="order" id="edit_order"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Opis</label>
                    <textarea name="description" id="edit_description" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <!-- Features -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Karakteristike paketa</label>
                    <div id="editFeaturesContainer">
                        <!-- Features will be populated by JavaScript -->
                    </div>
                    <button type="button" onclick="addEditFeatureField()" 
                            class="mt-2 px-4 py-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 text-sm">
                        <i class="fas fa-plus mr-1"></i> Dodaj karakteristiku
                    </button>
                </div>

                <!-- Active Status -->
                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="mr-2">
                        <span class="text-sm text-gray-700">Aktivan paket</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeEditModal()" 
                        class="px-6 py-2 text-gray-600 hover:text-gray-800">
                    Otkaži
                </button>
                <button type="submit" 
                        class="bg-blue-600 text-white px-8 py-2 rounded-lg hover:bg-blue-700 font-semibold">
                    Sačuvaj izmene
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Modal functions
function openAddModal() {
    document.getElementById('addPackageModal').classList.remove('hidden');
}

function closeAddModal() {
    document.getElementById('addPackageModal').classList.add('hidden');
}

function closeEditModal() {
    document.getElementById('editPackageModal').classList.add('hidden');
}

// Add feature field in Add modal
function addFeatureField() {
    const container = document.getElementById('featuresContainer');
    const div = document.createElement('div');
    div.className = 'flex gap-2 mb-2';
    div.innerHTML = `
        <input type="text" name="features[]" 
               placeholder="Dodajte karakteristiku"
               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        <button type="button" onclick="this.parentElement.remove()" 
                class="px-4 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">
            <i class="fas fa-minus"></i>
        </button>
    `;
    container.appendChild(div);
}

// Add feature field in Edit modal
function addEditFeatureField() {
    const container = document.getElementById('editFeaturesContainer');
    const div = document.createElement('div');
    div.className = 'flex gap-2 mb-2';
    div.innerHTML = `
        <input type="text" name="features[]" 
               placeholder="Dodajte karakteristiku"
               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        <button type="button" onclick="this.parentElement.remove()" 
                class="px-4 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">
            <i class="fas fa-minus"></i>
        </button>
    `;
    container.appendChild(div);
}

// Edit package function using data attributes
function editPackageFromData(button) {
    const id = button.dataset.packageId;
    const name = button.dataset.packageName;
    const description = button.dataset.packageDescription;
    const type = button.dataset.packageType;
    const duration = button.dataset.packageDuration;
    const price = button.dataset.packagePrice;
    const currency = button.dataset.packageCurrency;
    const order = button.dataset.packageOrder;
    const isActive = button.dataset.packageActive === '1';
    const features = JSON.parse(button.dataset.packageFeatures || '[]');
    
    // Populate form fields
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_type').value = type;
    document.getElementById('edit_duration').value = duration;
    document.getElementById('edit_price').value = price;
    document.getElementById('edit_currency').value = currency;
    document.getElementById('edit_order').value = order || 0;
    document.getElementById('edit_description').value = description || '';
    document.getElementById('edit_is_active').checked = isActive;
    
    // Populate features
    const container = document.getElementById('editFeaturesContainer');
    container.innerHTML = '';
    
    if (features && features.length > 0) {
        features.forEach(feature => {
            const div = document.createElement('div');
            div.className = 'flex gap-2 mb-2';
            const safeFeature = feature.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
            div.innerHTML = `
                <input type="text" name="features[]" value="${safeFeature}"
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <button type="button" onclick="this.parentElement.remove()" 
                        class="px-4 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">
                    <i class="fas fa-minus"></i>
                </button>
            `;
            container.appendChild(div);
        });
    }
    
    // Set form action and show modal
    document.getElementById('editPackageForm').action = `/admin/packages/${id}`;
    document.getElementById('editPackageModal').classList.remove('hidden');
}

// Close modals on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAddModal();
        closeEditModal();
    }
});

// Close modals on outside click
document.getElementById('addPackageModal').addEventListener('click', function(e) {
    if (e.target === this) closeAddModal();
});

document.getElementById('editPackageModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>
@endpush
@endsection