<div x-data="toastManager()" 
     x-init="init()"
     @toast.window="addToast($event.detail)"
     class="fixed top-4 right-4 z-50 space-y-3 pointer-events-none">
    
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.show"
             x-transition:enter="transform transition ease-out duration-300"
             x-transition:enter-start="translate-x-full opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transform transition ease-in duration-200"
             x-transition:leave-start="translate-x-0 opacity-100"
             x-transition:leave-end="translate-x-full opacity-0"
             class="pointer-events-auto max-w-sm w-full bg-white rounded-lg shadow-lg border-l-4 overflow-hidden"
             :class="{
                'border-green-500': toast.type === 'success',
                'border-red-500': toast.type === 'error',
                'border-yellow-500': toast.type === 'warning',
                'border-blue-500': toast.type === 'info'
             }">
            
            <div class="p-4">
                <div class="flex items-start">
                    <!-- Icon -->
                    <div class="flex-shrink-0">
                        <template x-if="toast.type === 'success'">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                        </template>
                        <template x-if="toast.type === 'error'">
                            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                <i class="fas fa-times text-red-600"></i>
                            </div>
                        </template>
                        <template x-if="toast.type === 'warning'">
                            <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                                <i class="fas fa-exclamation text-yellow-600"></i>
                            </div>
                        </template>
                        <template x-if="toast.type === 'info'">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-info text-blue-600"></i>
                            </div>
                        </template>
                    </div>

                    <!-- Content -->
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-900" x-text="toast.message"></p>
                    </div>

                    <!-- Close Button -->
                    <button @click="removeToast(toast.id)"
                            class="ml-4 flex-shrink-0 inline-flex text-gray-400 hover:text-gray-500 focus:outline-none">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <!-- Progress Bar -->
                <div class="mt-2 w-full bg-gray-200 rounded-full h-1 overflow-hidden">
                    <div class="h-full rounded-full transition-all ease-linear"
                         :class="{
                            'bg-green-500': toast.type === 'success',
                            'bg-red-500': toast.type === 'error',
                            'bg-yellow-500': toast.type === 'warning',
                            'bg-blue-500': toast.type === 'info'
                         }"
                         :style="`width: ${toast.progress}%; transition-duration: ${toast.duration}ms`"></div>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function toastManager() {
    return {
        toasts: [],
        nextId: 1,

        init() {
            // Prevent duplicate initialization
            if (window.toastInitialized) {
                return;
            }
            window.toastInitialized = true;

            // Check for Laravel flash messages on page load
            @if(session('success'))
                this.addToast({
                    message: @json(session('success')),
                    type: 'success'
                });
            @endif

            @if(session('error'))
                this.addToast({
                    message: @json(session('error')),
                    type: 'error'
                });
            @endif

            @if(session('warning'))
                this.addToast({
                    message: @json(session('warning')),
                    type: 'warning'
                });
            @endif

            @if(session('info'))
                this.addToast({
                    message: @json(session('info')),
                    type: 'info'
                });
            @endif

            @if($errors->any())
                this.addToast({
                    message: @json($errors->first()),
                    type: 'error'
                });
            @endif
        },

        addToast(options) {
            const duration = options.duration || 5000;
            const toast = {
                id: this.nextId++,
                message: options.message,
                type: options.type || 'info',
                show: false,
                progress: 100,
                duration: duration
            };

            this.toasts.push(toast);

            // Trigger show animation
            this.$nextTick(() => {
                const currentToast = this.toasts.find(t => t.id === toast.id);
                if (currentToast) {
                    currentToast.show = true;
                    
                    // Start progress bar animation
                    setTimeout(() => {
                        currentToast.progress = 0;
                    }, 50);
                    
                    // Auto remove after duration
                    setTimeout(() => {
                        this.removeToast(toast.id);
                    }, duration);
                }
            });
        },

        removeToast(id) {
            const toast = this.toasts.find(t => t.id === id);
            if (toast) {
                toast.show = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 300);
            }
        }
    };
}

// Global function to show toast from anywhere
window.showToast = function(message, type = 'info', duration = 5000) {
    window.dispatchEvent(new CustomEvent('toast', {
        detail: { message, type, duration }
    }));
};
</script>