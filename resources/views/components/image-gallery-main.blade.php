@props(['images', 'title' => ''])

<div x-data="{
    isOpen: false,
    currentIndex: 0,
    images: {{ $images->map(fn($img) => [
        'full' => $img->url('large'),
        'medium' => $img->url('medium'),
        'thumbnail' => $img->thumbnailUrl()
    ])->toJson() }},
    
    openLightbox(index) {
        this.currentIndex = index;
        this.isOpen = true;
        document.body.style.overflow = 'hidden';
    },
    
    closeLightbox() {
        this.isOpen = false;
        document.body.style.overflow = '';
    },
    
    nextImage() {
        this.currentIndex = (this.currentIndex + 1) % this.images.length;
    },
    
    prevImage() {
        this.currentIndex = this.currentIndex === 0 ? this.images.length - 1 : this.currentIndex - 1;
    }
}">
    
    <!-- Main Image Display -->
    <div class="relative bg-gray-900 rounded-lg overflow-hidden cursor-pointer group mb-4"
         @click="openLightbox(currentIndex)">
        <div class="aspect-video">
            <img :src="images[currentIndex].medium" 
                 :alt="'{{ $title }} - Image ' + (currentIndex + 1)"
                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
        </div>
        
        <!-- Image Counter Badge -->
        <div class="absolute bottom-4 right-4 bg-black bg-opacity-70 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <i class="fas fa-images"></i>
            <span class="font-semibold"><span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span></span>
        </div>

        <!-- View Gallery Overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center">
            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-white px-6 py-3 rounded-lg text-gray-900 font-semibold">
                <i class="fas fa-search-plus mr-2"></i>
                Prikaži galeriju
            </div>
        </div>
    </div>

    <!-- Thumbnail Navigation -->
    @if($images->count() > 1)
        <div class="grid grid-cols-6 gap-2">
            @foreach($images as $index => $image)
                @if($index < 6)
                    <div @click="currentIndex = {{ $index }}" 
                         class="relative aspect-square overflow-hidden rounded-lg cursor-pointer group"
                         :class="currentIndex === {{ $index }} ? 'ring-2 ring-blue-500' : ''">
                        <img src="{{ $image->thumbnailUrl() }}" 
                             alt="Thumbnail {{ $index + 1 }}"
                             class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                        
                        @if($index === 5 && $images->count() > 6)
                            <div @click.stop="openLightbox(5)" 
                                 class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center text-white font-bold hover:bg-opacity-50 transition-all">
                                +{{ $images->count() - 6 }}
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    <!-- Lightbox Modal -->
    <div x-show="isOpen"
         x-cloak
         @keydown.escape.window="closeLightbox()"
         @keydown.arrow-left.window="prevImage()"
         @keydown.arrow-right.window="nextImage()"
         @click.self="closeLightbox()"
         class="fixed inset-0 z-[9999] bg-black bg-opacity-95 flex items-center justify-center p-4"
         style="display: none;">
        
        <!-- Close Button -->
        <button @click="closeLightbox()" 
                class="absolute top-4 right-4 z-50 w-12 h-12 flex items-center justify-center bg-white bg-opacity-20 hover:bg-opacity-30 rounded-full text-white transition-all">
            <i class="fas fa-times text-xl"></i>
        </button>

        <!-- Image Counter -->
        <div class="absolute top-4 left-4 z-50 bg-black bg-opacity-50 text-white px-4 py-2 rounded-full text-sm font-semibold">
            <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
        </div>

        <!-- Previous Button -->
        <button @click="prevImage()" 
                x-show="images.length > 1"
                class="absolute left-4 top-1/2 transform -translate-y-1/2 z-50 w-12 h-12 md:w-16 md:h-16 flex items-center justify-center bg-white bg-opacity-20 hover:bg-opacity-30 rounded-full text-white transition-all">
            <i class="fas fa-chevron-left text-xl md:text-2xl"></i>
        </button>

        <!-- Main Image -->
        <div class="relative max-w-full max-h-full">
            <img :src="images[currentIndex].full" 
                 :alt="'{{ $title }}'"
                 class="max-w-full max-h-[85vh] w-auto h-auto object-contain">
        </div>

        <!-- Next Button -->
        <button @click="nextImage()" 
                x-show="images.length > 1"
                class="absolute right-4 top-1/2 transform -translate-y-1/2 z-50 w-12 h-12 md:w-16 md:h-16 flex items-center justify-center bg-white bg-opacity-20 hover:bg-opacity-30 rounded-full text-white transition-all">
            <i class="fas fa-chevron-right text-xl md:text-2xl"></i>
        </button>

        <!-- Thumbnail Strip -->
        <div x-show="images.length > 1" 
             class="absolute bottom-4 left-1/2 transform -translate-x-1/2 z-50 max-w-full px-4">
            <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
                <template x-for="(img, index) in images" :key="index">
                    <div @click="currentIndex = index"
                         class="flex-shrink-0 w-16 h-16 md:w-20 md:h-20 rounded-lg overflow-hidden cursor-pointer border-2 transition-all"
                         :class="currentIndex === index ? 'border-blue-500 opacity-100' : 'border-transparent opacity-50 hover:opacity-75'">
                        <img :src="img.thumbnail" 
                             class="w-full h-full object-cover">
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>