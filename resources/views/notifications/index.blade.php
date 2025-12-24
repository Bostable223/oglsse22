@extends('layouts.app')

@section('title', 'Notifikacije')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Notifikacije</h1>
            <p class="text-gray-600 mt-2">Vaše notifikacije i obaveštenja</p>
        </div>
        @if(Auth::user()->unreadNotificationsCount() > 0)
        <form action="{{ route('notifications.read-all') }}" method="POST">
            @csrf
            <button type="submit" class="text-blue-600 hover:text-blue-700 font-semibold">
                Označi sve kao pročitano
            </button>
        </form>
        @endif
    </div>

    @if($notifications->count() > 0)
        <div class="space-y-4">
            @foreach($notifications as $notification)
                <div class="bg-white rounded-lg shadow-sm border-l-4 overflow-hidden
                    {{ $notification->is_read ? 'border-gray-300' : 'border-blue-500 bg-blue-50' }}">
                    
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <!-- Icon -->
                            <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0
                                @if($notification->color === 'green') bg-green-100
                                @elseif($notification->color === 'red') bg-red-100
                                @elseif($notification->color === 'yellow') bg-yellow-100
                                @elseif($notification->color === 'blue') bg-blue-100
                                @else bg-gray-100
                                @endif">
                                <i class="fas {{ $notification->icon }} text-xl
                                    @if($notification->color === 'green') text-green-600
                                    @elseif($notification->color === 'red') text-red-600
                                    @elseif($notification->color === 'yellow') text-yellow-600
                                    @elseif($notification->color === 'blue') text-blue-600
                                    @else text-gray-600
                                    @endif"></i>
                            </div>

                            <!-- Content -->
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 mb-1">{{ $notification->title }}</h3>
                                <p class="text-gray-700 mb-2">{{ $notification->message }}</p>
                                <p class="text-sm text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2">
                                @if($notification->action_url)
                                    <a href="{{ $notification->action_url }}" 
                                       onclick="event.preventDefault(); document.getElementById('read-{{ $notification->id }}').submit();"
                                       class="text-blue-600 hover:text-blue-700 text-sm font-semibold">
                                        Vidi
                                    </a>
                                    <form id="read-{{ $notification->id }}" 
                                          action="{{ route('notifications.read', $notification->id) }}" 
                                          method="POST" class="hidden">
                                        @csrf
                                    </form>
                                @endif

                                <form action="{{ route('notifications.delete', $notification->id) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Obrisati notifikaciju?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <i class="fas fa-bell-slash text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Nema notifikacija</h3>
            <p class="text-gray-500">Ovde ćete videti sve vaše notifikacije</p>
        </div>
    @endif
</div>
@endsection