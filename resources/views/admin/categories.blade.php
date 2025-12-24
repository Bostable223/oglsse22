@extends('layouts.app')

@section('title', 'Admin - Kategorije')

@section('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Admin Panel', 'url' => route('admin.dashboard')],
        ['title' => 'Kategorije', 'url' => route('admin.categories')]
    ]" />
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Upravljanje kategorijama</h1>
            <p class="text-gray-600 mt-2">Dodajte i upravljajte kategorijama</p>
        </div>
        <button onclick="document.getElementById('addCategoryModal').classList.remove('hidden')" 
                class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
            <i class="fas fa-plus mr-2"></i> Nova kategorija
        </button>
    </div>

    <!-- Categories Table -->
    @if($categories->count() > 0)
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ikona</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Naziv</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Broj oglasa</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Redosled</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Akcije</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($categories as $category)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-4 text-sm text-gray-600">#{{ $category->id }}</td>
                    <td class="px-4 py-4">
                        <i class="fas fa-{{ $category->icon ?? 'home' }} text-2xl text-blue-600"></i>
                    </td>
                    <td class="px-4 py-4 font-medium text-gray-900">{{ $category->name }}</td>
                    <td class="px-4 py-4 text-sm text-gray-600">{{ $category->slug }}</td>
                    <td class="px-4 py-4 text-sm text-gray-600">{{ $category->listings_count }}</td>
                    <td class="px-4 py-4 text-sm text-gray-600">{{ $category->order }}</td>
                    <td class="px-4 py-4">
                        @if($category->is_active)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktivan</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Neaktivan</span>
                        @endif
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-2">
                            <button onclick="editCategory({{ $category->id }}, '{{ $category->name }}', '{{ $category->description }}', '{{ $category->icon }}', {{ $category->order }}, {{ $category->is_active ? 'true' : 'false' }})" 
                                    class="text-blue-600 hover:text-blue-700" title="Izmeni">
                                <i class="fas fa-edit"></i>
                            </button>

                            @if($category->listings_count == 0)
                                <form action="{{ route('admin.categories.delete', $category->id) }}" method="POST" class="inline-block"
                                      onsubmit="return confirm('Da li ste sigurni?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700" title="Obriši">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-300" title="Ne može se obrisati - ima oglase">
                                    <i class="fas fa-trash"></i>
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
        <i class="fas fa-tags text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Nema kategorija</h3>
        <p class="text-gray-500 mb-6">Dodajte prvu kategoriju</p>
    </div>
    @endif
</div>

<!-- Add Category Modal -->
<div id="addCategoryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
        <h2 class="text-2xl font-bold mb-4">Dodaj novu kategoriju</h2>
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Naziv *</label>
                <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Opis</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Ikona (Font Awesome)</label>
                <input type="text" name="icon" placeholder="home" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Redosled</label>
                <input type="number" name="order" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addCategoryModal').classList.add('hidden')" 
                        class="px-4 py-2 text-gray-600 hover:text-gray-800">Otkaži</button>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Dodaj</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Category Modal -->
<div id="editCategoryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
        <h2 class="text-2xl font-bold mb-4">Izmeni kategoriju</h2>
        <form id="editCategoryForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Naziv *</label>
                <input type="text" name="name" id="edit_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Opis</label>
                <textarea name="description" id="edit_description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Ikona (Font Awesome)</label>
                <input type="text" name="icon" id="edit_icon" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Redosled</label>
                <input type="number" name="order" id="edit_order" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="mr-2">
                    <span class="text-sm text-gray-700">Aktivna kategorija</span>
                </label>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('editCategoryModal').classList.add('hidden')" 
                        class="px-4 py-2 text-gray-600 hover:text-gray-800">Otkaži</button>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Sačuvaj</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function editCategory(id, name, description, icon, order, isActive) {
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_description').value = description || '';
    document.getElementById('edit_icon').value = icon || '';
    document.getElementById('edit_order').value = order;
    document.getElementById('edit_is_active').checked = isActive;
    document.getElementById('editCategoryForm').action = `/admin/categories/${id}`;
    document.getElementById('editCategoryModal').classList.remove('hidden');
}
</script>
@endpush
@endsection