@props(['images', 'title' => ''])

<div x-data="{
    isOpen: false,
    currentIndex: 0,
    images: {{ $images->map(fn($img) => [
        'full' => $img->url('large'),
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
        if (this.currentIndex < this.images.length - 1) {
            this.currentIndex++;
        } else {
            this.currentIndex = 0;
        }
    },
    
    prevImage() {
        if (this.currentIndex > 0) {
            this.currentIndex--;
        } else {
            this.currentIndex = this.images.length - 1;
        }
    },
    
    goToImage(index) {
        this.currentIndex = index;
    }
}" 
class="image-gallery">
    
    <!-- Thumbnail Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($images as $index => $image)
            <div @click="openLightbox({{ $index }})" 
                 class="relative aspect-square overflow-hidden rounded-lg cursor-pointer group bg-gray-200">
                <img src="{{ $image->url('medium') }}" 
                     alt="{{ $title }} - Image {{ $index + 1 }}"
                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                
                <!-- Hover Overlay -->
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center">
                    <i class="fas fa-search-plus text-white text-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></i>
                </div>

                <!-- First Image Badge -->
                @if($index === 0)
                    <div class="absolute top-2 left-2 bg-blue-600 text-white text-xs px-2 py-1 rounded">
                        Glavna slika
                    </div>
                @endif
            </div>
        @endforeach
    </div>

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
        <div class="relative max-w-full max-h-full flex items-center justify-center">
            <img :src="images[currentIndex].full" 
                 :alt="'{{ $title }} - Image ' + (currentIndex + 1)"
                 class="max-w-full max-h-[85vh] w-auto h-auto object-contain">
        </div>

        <!-- Next Button -->
        <button @click="nextImage()" 
                x-show="images.length > 1"
                class="absolute right-4 top-1/2 transform -translate-y-1/2 z-50 w-12 h-12 md:w-16 md:h-16 flex items-center justify-center bg-white bg-opacity-20 hover:bg-opacity-30 rounded-full text-white transition-all">
            <i class="fas fa-chevron-right text-xl md:text-2xl"></i>
        </button>

        <!-- Thumbnail Strip (Bottom) -->
        <div x-show="images.length > 1" 
             class="absolute bottom-4 left-1/2 transform -translate-x-1/2 z-50 max-w-full px-4">
            <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
                <template x-for="(img, index) in images" :key="index">
                    <div @click="goToImage(index)"
                         class="flex-shrink-0 w-16 h-16 md:w-20 md:h-20 rounded-lg overflow-hidden cursor-pointer border-2 transition-all"
                         :class="currentIndex === index ? 'border-blue-500 opacity-100' : 'border-transparent opacity-50 hover:opacity-75'">
                        <img :src="img.thumbnail" 
                             :alt="'Thumbnail ' + (index + 1)"
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