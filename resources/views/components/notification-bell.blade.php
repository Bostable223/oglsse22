@auth
<div x-data="notificationBell()" 
     x-init="init()"
     class="relative">
    
    <!-- Bell Icon Button -->
    <button @click="toggleDropdown()"
            class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none">
        <i class="fas fa-bell text-xl"></i>
        
        <!-- Unread Badge -->
        <span x-show="unreadCount > 0"
              x-text="unreadCount > 99 ? '99+' : unreadCount"
              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full min-w-[20px] h-5 flex items-center justify-center px-1">
        </span>
    </button>

    <!-- Dropdown -->
    <div x-show="isOpen"
         @click.away="isOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50"
         style="display: none;">
        
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">
                Notifikacije
                <span x-show="unreadCount > 0" 
                      class="ml-2 text-sm text-gray-500"
                      x-text="`(${unreadCount})`"></span>
            </h3>
            <button @click="markAllAsRead()"
                    x-show="unreadCount > 0"
                    class="text-xs text-blue-600 hover:text-blue-700">
                Označi sve
            </button>
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
            <template x-if="notifications.length === 0">
                <div class="px-4 py-8 text-center text-gray-500">
                    <i class="fas fa-bell-slash text-4xl mb-2"></i>
                    <p>Nema notifikacija</p>
                </div>
            </template>

            <template x-for="notification in notifications" :key="notification.id">
                <div @click="handleNotificationClick(notification)"
                     class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors"
                     :class="{ 'bg-blue-50': !notification.is_read }">
                    
                    <div class="flex items-start gap-3">
                        <!-- Icon -->
                        <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center"
                             :class="{
                                'bg-green-100': notification.type === 'listing_approved',
                                'bg-red-100': notification.type === 'listing_rejected',
                                'bg-yellow-100': notification.type === 'package_expiring',
                                'bg-blue-100': notification.type === 'new_message'
                             }">
                            <i class="fas"
                               :class="{
                                   'fa-check-circle text-green-600': notification.type === 'listing_approved',
                                   'fa-times-circle text-red-600': notification.type === 'listing_rejected',
                                   'fa-clock text-yellow-600': notification.type === 'package_expiring',
                                   'fa-envelope text-blue-600': notification.type === 'new_message'
                               }"></i>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-gray-900" x-text="notification.title"></p>
                            <p class="text-sm text-gray-600 mt-1" x-text="notification.message"></p>
                            <p class="text-xs text-gray-400 mt-1" x-text="formatTime(notification.created_at)"></p>
                        </div>

                        <!-- Unread Dot -->
                        <div x-show="!notification.is_read"
                             class="w-2 h-2 bg-blue-600 rounded-full flex-shrink-0 mt-2"></div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Footer -->
        <div class="px-4 py-3 border-t border-gray-200 text-center">
            <a href="{{ route('notifications.index') }}"
               class="text-sm text-blue-600 hover:text-blue-700 font-semibold">
                Vidi sve notifikacije
            </a>
        </div>
    </div>
</div>

<script>
function notificationBell() {
    return {
        isOpen: false,
        notifications: [],
        unreadCount: {{ Auth::user()->unreadNotificationsCount() }},

        init() {
            this.loadNotifications();
            
            // Refresh every 60 seconds
            setInterval(() => {
                this.loadNotifications();
            }, 60000);
        },

        toggleDropdown() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.loadNotifications();
            }
        },

        async loadNotifications() {
            try {
                const response = await fetch('/api/notifications/recent');
                const data = await response.json();
                this.notifications = data.notifications;
                this.unreadCount = data.unread_count;
            } catch (error) {
                console.error('Error loading notifications:', error);
            }
        },

        async handleNotificationClick(notification) {
            if (!notification.is_read) {
                await this.markAsRead(notification.id);
            }
            window.location.href = notification.action_url || '/dashboard';
        },

        async markAsRead(id) {
            try {
                await fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                await this.loadNotifications();
            } catch (error) {
                console.error('Error marking as read:', error);
            }
        },

        async markAllAsRead() {
            try {
                await fetch('/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                await this.loadNotifications();
                showToast('Sve notifikacije su označene kao pročitane', 'success');
            } catch (error) {
                console.error('Error marking all as read:', error);
            }
        },

        formatTime(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);

            if (diffMins < 1) return 'Upravo sada';
            if (diffMins < 60) return `Pre ${diffMins} min`;
            if (diffHours < 24) return `Pre ${diffHours}h`;
            if (diffDays < 7) return `Pre ${diffDays} dana`;
            return date.toLocaleDateString('sr-RS');
        }
    };
}
</script>
@endauth