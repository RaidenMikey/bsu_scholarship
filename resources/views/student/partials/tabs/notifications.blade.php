<div x-cloak x-data="notificationData()" data-notifications="{{ json_encode($notifications) }}" data-unread-count="{{ $unreadCount }}" class="px-4 py-6">
  <!-- Header -->
  <div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
      🔔 Notifications
    </h2>
    <p class="text-gray-600 dark:text-gray-400">
      Stay updated with your scholarship applications and system updates
    </p>
  </div>

  <!-- Notification Stats -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
      <div class="flex items-center">
        <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg">
          <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5v-5a7.5 7.5 0 1 0-15 0v5h5l-5 5-5-5h5v-5a7.5 7.5 0 1 1 15 0v5z"></path>
          </svg>
        </div>
        <div class="ml-4">
          <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Unread</p>
          <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $unreadCount }}</p>
        </div>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
      <div class="flex items-center">
        <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
          <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <div class="ml-4">
          <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total</p>
          <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $notifications->count() }}</p>
        </div>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
      <div class="flex items-center">
        <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
          <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <div class="ml-4">
          <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Recent</p>
          <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $notifications->where('created_at', '>=', now()->subDays(7))->count() }}</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Filter and Actions -->
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div class="flex flex-wrap gap-2">
      <button @click="filterType = ''" 
              :class="filterType === '' ? 'bg-bsu-red text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
              class="px-4 py-2 rounded-lg text-sm font-medium transition">
        All
      </button>
      <button @click="filterType = 'scholarship_created'" 
              :class="filterType === 'scholarship_created' ? 'bg-green-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
              class="px-4 py-2 rounded-lg text-sm font-medium transition">
        🎓 Scholarships
      </button>
      <button @click="filterType = 'sfao_comment'" 
              :class="filterType === 'sfao_comment' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
              class="px-4 py-2 rounded-lg text-sm font-medium transition">
        💬 Comments
      </button>
      <button @click="filterType = 'application_status'" 
              :class="filterType === 'application_status' ? 'bg-purple-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
              class="px-4 py-2 rounded-lg text-sm font-medium transition">
        📋 Status
      </button>
    </div>

    <div class="flex gap-2">
      <button @click="markAllAsRead()" 
              class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm font-medium transition">
        Mark All Read
      </button>
      <button @click="refreshNotifications()" 
              class="px-4 py-2 bg-bsu-red hover:bg-bsu-redDark text-white rounded-lg text-sm font-medium transition">
        Refresh
      </button>
    </div>
  </div>

  <!-- Notifications List -->
  <div class="space-y-4">
    @if($notifications->isEmpty())
      <div class="text-center py-12">
        <div class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
          <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5v-5a7.5 7.5 0 1 0-15 0v5h5l-5 5-5-5h5v-5a7.5 7.5 0 1 1 15 0v5z"></path>
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No notifications yet</h3>
        <p class="text-gray-600 dark:text-gray-400">You'll receive notifications about scholarships, application updates, and comments here.</p>
      </div>
    @else
      @foreach($notifications as $notification)
        <div x-data="{ isRead: {{ $notification->is_read ? 'true' : 'false' }} }" 
             x-show="filterType === '' || filterType === '{{ $notification->type }}'"
             class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition-all duration-200 border-l-4 {{ $notification->is_read ? 'border-gray-300 dark:border-gray-600' : 'border-bsu-red' }}"
             :class="{ 'opacity-75': isRead }">
          
          <div class="p-4">
            <div class="flex items-start space-x-3">
              <!-- Notification Icon -->
              <div class="flex-shrink-0">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg {{ $notification->color }}">
                  {{ $notification->icon }}
                </div>
              </div>

              <!-- Notification Content -->
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                  <h4 class="text-sm font-medium {{ $notification->is_read ? 'text-gray-500 dark:text-gray-400' : 'text-gray-900 dark:text-white' }}">
                    {{ $notification->title }}
                  </h4>
                  
                  <div class="flex items-center space-x-2">
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                      {{ $notification->time_ago }}
                    </span>
                    
                    @if(!$notification->is_read)
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                        New
                      </span>
                    @endif
                  </div>
                </div>

                <p class="mt-1 text-sm {{ $notification->is_read ? 'text-gray-500 dark:text-gray-400' : 'text-gray-700 dark:text-gray-300' }}">
                  {{ $notification->message }}
                </p>

                <!-- Additional Data Display -->
                @if($notification->data)
                  <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    @if(isset($notification->data['scholarship_name']))
                      <span class="inline-flex items-center px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 mr-2">
                        📚 {{ $notification->data['scholarship_name'] }}
                      </span>
                    @endif
                    
                    @if(isset($notification->data['status']))
                      <span class="inline-flex items-center px-2 py-1 rounded 
                        @if($notification->data['status'] === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                        @elseif($notification->data['status'] === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                        @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                        @endif">
                        {{ ucfirst($notification->data['status']) }}
                      </span>
                    @endif
                  </div>
                @endif

                <!-- Action Buttons -->
                <div class="mt-3 flex space-x-2">
                  @if(!$notification->is_read)
                    <button @click="markAsRead({{ $notification->id }})" 
                            class="text-xs px-3 py-1 bg-bsu-red hover:bg-bsu-redDark text-white rounded transition">
                      Mark as Read
                    </button>
                  @endif
                  
                  @if($notification->type === 'scholarship_created' && isset($notification->data['scholarship_id']))
                    <a href="{{ route('student.scholarships') }}#scholarship-{{ $notification->data['scholarship_id'] }}" 
                       class="text-xs px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded transition">
                      View Scholarship
                    </a>
                  @endif
                  
                  @if($notification->type === 'application_status' && isset($notification->data['application_id']))
                    <a href="{{ route('student.dashboard') }}#application-tracking" 
                       class="text-xs px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white rounded transition">
                      View Application
                    </a>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    @endif
  </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('notificationData', () => ({
    filterType: '',
    notifications: [],
    unreadCount: 0,

    init() {
      // Initialize data from HTML attributes
      const dataElement = document.querySelector('[data-notifications]');
      this.notifications = dataElement ? JSON.parse(dataElement.dataset.notifications) : [];
      this.unreadCount = parseInt(dataElement?.dataset.unreadCount || '0');
    },

    markAsRead(notificationId) {
      fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Update local state
          const notification = this.notifications.find(n => n.id === notificationId);
          if (notification) {
            notification.is_read = true;
            this.unreadCount = Math.max(0, this.unreadCount - 1);
          }
          
          // Update UI
          const element = document.querySelector(`[data-notification-id="${notificationId}"]`);
          if (element) {
            element.classList.add('opacity-75');
            element.classList.remove('border-bsu-red');
            element.classList.add('border-gray-300');
          }
        }
      })
      .catch(error => {
        console.error('Error marking notification as read:', error);
      });
    },

    markAllAsRead() {
      fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Update all notifications to read
          this.notifications.forEach(notification => {
            notification.is_read = true;
          });
          this.unreadCount = 0;
          
          // Reload page to reflect changes
          window.location.reload();
        }
      })
      .catch(error => {
        console.error('Error marking all notifications as read:', error);
      });
    },

    refreshNotifications() {
      window.location.reload();
    }
  }));
});
</script>
