@props(['items' => []])

@if(count($items) > 0)
<nav class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <ol class="flex items-center space-x-2 text-sm">
            <!-- Home Link (Always present) -->
            <li class="flex items-center">
                <a href="{{ route('home') }}" 
                   class="text-gray-500 hover:text-gray-700 transition-colors flex items-center">
                    <i class="fas fa-home mr-1"></i>
                    <span>Početna</span>
                </a>
            </li>

            <!-- Dynamic Breadcrumb Items -->
            @foreach($items as $index => $item)
                <li class="flex items-center">
                    <!-- Separator -->
                    <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>

                    @if($loop->last)
                        <!-- Last item (current page) - No link -->
                        <span class="text-gray-900 font-semibold">
                            {{ $item['title'] }}
                        </span>
                    @else
                        <!-- Intermediate items - With link -->
                        <a href="{{ $item['url'] }}" 
                           class="text-gray-500 hover:text-gray-700 transition-colors">
                            {{ $item['title'] }}
                        </a>
                    @endif
                </li>
            @endforeach
        </ol>
    </div>
</nav>
@endif