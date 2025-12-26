@auth
<a href="{{ route('dashboard.favorites') }}" 
   class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none transition-colors group">
    
    <!-- Heart Icon -->
    <div class="relative">
        <i class="fas fa-heart text-xl"></i>
        
        <!-- Counter Badge -->
        <span id="favorites-count-badge"
              data-count="{{ Auth::user()->unreadNotificationsCount() }}"
              class="absolute -top-2 -right-2 flex items-center justify-center min-w-[20px] h-5 px-1 text-xs font-bold text-white rounded-full transition-all duration-300
                     {{ Auth::user()->favorites()->count() > 0 ? 'bg-red-500' : 'bg-gray-400' }}"
              style="{{ Auth::user()->favorites()->count() === 0 ? 'display: none;' : '' }}">
            {{ Auth::user()->favorites()->count() }}
        </span>
    </div>

    <!-- Tooltip -->
    <span class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 text-xs font-medium text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap pointer-events-none">
        Omiljeni oglasi
    </span>
</a>
@endauth