{{-- resources/views/admin/partials/package-card.blade.php --}}

<div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden border-2 
    {{ $package->is_active ? 'border-green-200' : 'border-gray-200' }}">
    
    <!-- Header with Type Badge -->
    <div class="p-4 {{ $package->type === 'featured' ? 'bg-gradient-to-r from-yellow-50 to-orange-50' : 'bg-gradient-to-r from-blue-50 to-indigo-50' }}">
        <div class="flex items-center justify-between mb-2">
            <span class="px-3 py-1 rounded-full text-xs font-bold
                {{ $package->type === 'featured' ? 'bg-yellow-500 text-white' : 'bg-blue-500 text-white' }}">
                @if($package->type === 'featured')
                    <i class="fas fa-star"></i> FEATURED
                @else
                    <i class="fas fa-arrow-up"></i> TOP
                @endif
            </span>
            
            @if(!$package->is_active)
                <span class="px-2 py-1 bg-red-500 text-white rounded-full text-xs font-semibold">
                    Neaktivan
                </span>
            @endif
        </div>
        
        <h3 class="text-xl font-bold text-gray-900">{{ $package->name }}</h3>
        <p class="text-sm text-gray-600 mt-1">{{ $package->duration_days }} dana</p>
    </div>

    <!-- Body -->
    <div class="p-4">
        <!-- Price -->
        <div class="mb-4">
            <div class="text-3xl font-bold text-gray-900">
                {{ number_format($package->price, 0, ',', '.') }}
                <span class="text-lg text-gray-500">{{ $package->currency }}</span>
            </div>
        </div>

        <!-- Description -->
        @if($package->description)
            <p class="text-sm text-gray-600 mb-4">{{ $package->description }}</p>
        @endif

        <!-- Features -->
        @if($package->features && count($package->features) > 0)
            <div class="mb-4">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Karakteristike:</p>
                <ul class="space-y-1">
                    @foreach($package->features as $feature)
                        <li class="text-sm text-gray-700">
                            <i class="fas fa-check text-green-500 mr-2"></i>{{ $feature }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Stats -->
        <div class="flex items-center justify-between py-3 border-t border-gray-200 mb-4">
            <div class="text-center">
                <div class="text-lg font-bold text-gray-900">{{ $package->listings_count }}</div>
                <div class="text-xs text-gray-500">Koristi se</div>
            </div>
            <div class="text-center">
                <div class="text-lg font-bold text-gray-900">{{ $package->order }}</div>
                <div class="text-xs text-gray-500">Redosled</div>
            </div>
            <div class="text-center">
                <div class="text-lg font-bold 
                    {{ $package->is_active ? 'text-green-600' : 'text-red-600' }}">
                    {{ $package->is_active ? 'DA' : 'NE' }}
                </div>
                <div class="text-xs text-gray-500">Aktivan</div>
            </div>
        </div>

        <!-- Actions -->
        <div class="grid grid-cols-3 gap-2">
            <!-- Edit -->
            <button type="button"
                    data-package-id="{{ $package->id }}"
                    data-package-name="{{ $package->name }}"
                    data-package-description="{{ $package->description }}"
                    data-package-type="{{ $package->type }}"
                    data-package-duration="{{ $package->duration_days }}"
                    data-package-price="{{ $package->price }}"
                    data-package-currency="{{ $package->currency }}"
                    data-package-order="{{ $package->order }}"
                    data-package-active="{{ $package->is_active ? '1' : '0' }}"
                    data-package-features="{{ json_encode($package->features ?? []) }}"
                    onclick="editPackageFromData(this)"
                    class="px-3 py-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 text-sm font-semibold">
                <i class="fas fa-edit"></i>
            </button>

            <!-- Toggle Active -->
            <form action="{{ route('admin.packages.toggle-active', $package->id) }}" method="POST" class="inline-block">
                @csrf
                <button type="submit" 
                        class="w-full px-3 py-2 rounded-lg text-sm font-semibold
                        {{ $package->is_active ? 'bg-orange-100 text-orange-600 hover:bg-orange-200' : 'bg-green-100 text-green-600 hover:bg-green-200' }}">
                    <i class="fas fa-{{ $package->is_active ? 'toggle-on' : 'toggle-off' }}"></i>
                </button>
            </form>

            <!-- Delete -->
            <form action="{{ route('admin.packages.delete', $package->id) }}" method="POST" 
                  onsubmit="return confirm('Da li ste sigurni? Paket se ne može obrisati ako se koristi.')" 
                  class="inline-block">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="w-full px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 text-sm font-semibold
                        {{ $package->listings_count > 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ $package->listings_count > 0 ? 'disabled' : '' }}>
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
</div>