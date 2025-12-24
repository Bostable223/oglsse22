<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Oglasi') - Classified Listings</title>
    
    <!-- Tailwind CSS CDN for styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-blue-600">
                        <i class="fas fa-home"></i> Oglasi Se
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="flex items-center space-x-4">
                    @auth
                        <!-- Post Listing Button -->
                        <a href="{{ route('listings.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center">
                            <i class="fas fa-plus mr-2"></i> Postavi oglas
                        </a>

                            <!-- ADD THIS: Notification Bell -->
                            
                                <x-notification-bell />
                            

                        <!-- User Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900">
                                <img src="{{ auth()->user()->avatarUrl() }}" alt="Avatar" class="w-8 h-8 rounded-full">
                                <span class="hidden md:block">{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down text-sm"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" @click.away="open = false" 
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
                                <a href="{{ route('dashboard.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                                </a>
                                
                                <a href="{{ route('dashboard.my-listings') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-list mr-2"></i> Moji oglasi
                                </a>
                                <a href="{{ route('dashboard.favorites') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-heart mr-2"></i> Omiljeni
                                </a>
                                <a href="{{ route('dashboard.profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i> Profil
                                </a>
                                
                                @if(auth()->user()->isAdmin())
                                    <hr class="my-2">
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-blue-600 hover:bg-gray-100">
                                        <i class="fas fa-shield-alt mr-2"></i> Admin Panel
                                    </a>
                                @endif

                                <hr class="my-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Odjavi se
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">Prijavi se</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Registruj se
                        </a>
                    @endauth
                </div>
            </div>
        </div>

    <!-- Flash Messages -->
    <x-toast />

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-bold mb-4">O nama</h3>
                    <p class="text-gray-400">Vaš pouzdan partner za pronalaženje nekretnina u Srbiji.</p>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Kategorije</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('listings.index') }}" class="hover:text-white">Stanovi</a></li>
                        <li><a href="{{ route('listings.index') }}" class="hover:text-white">Kuće</a></li>
                        <li><a href="{{ route('listings.index') }}" class="hover:text-white">Zemljište</a></li>
                        <li><a href="{{ route('listings.index') }}" class="hover:text-white">Poslovni prostor</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Linkovi</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">O nama</a></li>
                        <li><a href="#" class="hover:text-white">Kontakt</a></li>
                        <li><a href="#" class="hover:text-white">Uslovi korišćenja</a></li>
                        <li><a href="#" class="hover:text-white">Privatnost</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Kontakt</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><i class="fas fa-phone mr-2"></i> +381 11 123 4567</li>
                        <li><i class="fas fa-envelope mr-2"></i> info@oglasi.rs</li>
                        <li><i class="fas fa-map-marker-alt mr-2"></i> Beograd, Srbija</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 Classified Listings. Sva prava zadržana.</p>
            </div>
        </div>
    </footer>

    <!-- Alpine.js for dropdown functionality -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    

@stack('scripts')

</body>
</html>