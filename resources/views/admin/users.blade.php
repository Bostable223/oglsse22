@extends('layouts.app')

@section('title', 'Admin - Korisnici')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Upravljanje korisnicima</h1>
        <p class="text-gray-600 mt-2">Pregledajte i upravljajte svim korisnicima</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form action="{{ route('admin.users') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            <!-- Search -->
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Pretraži po imenu, email-u..." 
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">

            <!-- Role Filter -->
            <select name="role" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Sve uloge</option>
                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Korisnici</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administratori</option>
            </select>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Primeni
                </button>
                <a href="{{ route('admin.users') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    @if($users->count() > 0)
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Korisnik</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telefon</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grad</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Uloga</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Oglasi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registrovan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Akcije</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4 text-sm text-gray-600">
                            #{{ $user->id }}
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full mr-3">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600">
                            {{ $user->email }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600">
                            {{ $user->phone ?? '-' }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600">
                            {{ $user->city ?? '-' }}
                        </td>
                        <td class="px-4 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600">
                            {{ $user->listings_count }}
                        </td>
                        <td class="px-4 py-4">
                            @if($user->is_active)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Aktivan
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Deaktiviran
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600">
                            {{ $user->created_at->format('d.m.Y') }}
                        </td>
                       <td class="px-4 py-4">
    <div class="flex items-center gap-2">
        @if(!$user->isAdmin())
            <!-- Edit -->
            <a href="{{ route('admin.users.edit', $user->id) }}" 
               class="text-blue-600 hover:text-blue-700" 
               title="Izmeni">
                <i class="fas fa-edit"></i>
            </a>

            <!-- Reset Password -->
            <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST" class="inline-block"
                  onsubmit="return confirm('Da li ste sigurni da želite resetovati lozinku za {{ $user->name }}?')">
                @csrf
                <button type="submit" 
                        class="text-purple-600 hover:text-purple-700" 
                        title="Resetuj lozinku">
                    <i class="fas fa-key"></i>
                </button>
            </form>

            <!-- Toggle Active Status -->
            <form action="{{ route('admin.users.toggle-active', $user->id) }}" method="POST" class="inline-block">
                @csrf
                <button type="submit" 
                        class="{{ $user->is_active ? 'text-orange-600 hover:text-orange-700' : 'text-green-600 hover:text-green-700' }}" 
                        title="{{ $user->is_active ? 'Deaktiviraj' : 'Aktiviraj' }}">
                    <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                </button>
            </form>

            <!-- Delete User -->
            <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" class="inline-block" 
                  onsubmit="return confirm('Da li ste sigurni da želite obrisati ovog korisnika?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-700" title="Obriši">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        @else
            <span class="text-gray-400 text-xs">Admin</span>
        @endif
    </div>
</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 py-4 border-t border-gray-200">
            {{ $users->links() }}
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
        <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Nema korisnika</h3>
        <p class="text-gray-500">Pokušajte sa drugačijim filterima</p>
    </div>
    @endif
</div>
@endsection