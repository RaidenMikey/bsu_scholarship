@php
    // Pre-calculate counts for AlpineJS logic
    $counts = [
        'all' => $notifications->count(),
        'scholarship_created' => $notifications->where('type', 'scholarship_created')->count(),
        'application_status' => $notifications->where('type', 'application_status')->count(),
        'sfao_comment' => $notifications->where('type', 'sfao_comment')->count(),
    ];
@endphp

<div class="space-y-8" x-data="{ 
    counts: {{ json_encode($counts) }},
    currentFilter: 'all',
    init() {
        this.$watch('subTab', val => this.currentFilter = val || 'all');
        this.currentFilter = this.subTab || 'all';
    },
    markAsRead(id) {
        fetch('{{ route('notifications.mark-read', ':id') }}'.replace(':id', id), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        });
    },
    markAsUnread(id) {
        fetch('{{ route('notifications.mark-unread', ':id') }}'.replace(':id', id), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        });
    }
}">
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col sm:flex-row justify-between items-center gap-6 relative overflow-hidden group">
         <!-- BSU Red Decoration -->
        <div class="absolute top-0 right-0 w-2 h-full bg-bsu-red"></div>

        <div class="relative z-10">
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight font-sans">
                Notifications
            </h2>
            <p class="text-base text-gray-500 dark:text-gray-400 mt-1 font-medium">
                Manage your alerts and updates
            </p>
        </div>
        
        @if($unreadCount > 0)
            <button 
                @click="
                    fetch('{{ route('notifications.mark-all-read') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            $dispatch('notifications-read-all');
                            window.location.reload(); 
                        }
                    })
                "
                class="relative z-10 group flex items-center px-6 py-2.5 bg-gray-900 text-white rounded-lg text-sm font-semibold hover:bg-gray-800 transition-all duration-300 shadow-sm hover:shadow-md"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2.5 text-gray-400 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Mark all as read
            </button>
        @endif
    </div>

    <!-- Notifications List -->
    <div class="min-h-[400px]">
        @if($notifications->count() > 0)
            <div class="space-y-4">
                @foreach($notifications as $notification)
                    <!-- Dynamic styling based on type -->
                    @php
                        // Slightly more muted icons to fit the Red/White/Black theme
                        $iconStyle = match($notification->type) {
                            'scholarship_created' => 'bg-gray-100 text-bsu-red',
                            'application_status' => 'bg-gray-100 text-gray-800', 
                            'sfao_comment' => 'bg-gray-100 text-gray-800',
                            default => 'bg-gray-100 text-gray-600'
                        };
                    @endphp

                    <div 
                        x-show="currentFilter === 'all' || currentFilter === '{{ $notification->type }}'"
                        class="group relative p-6 rounded-lg transition-all duration-200 overflow-hidden"
                         x-data="{ 
                            is_read_local: {{ $notification->is_read ? 'true' : 'false' }},
                            type: '{{ $notification->type }}',
                            id: '{{ $notification->id }}',
                            toggleReadStatus() {
                                if (this.is_read_local) {
                                    this.is_read_local = false;
                                    markAsUnread(this.id);
                                } else {
                                    this.is_read_local = true;
                                    markAsRead(this.id);
                                    $dispatch('notification-read', { type: this.type });
                                }
                            }
                        }"
                        :class="!is_read_local 
                            ? 'bg-white dark:bg-gray-800 border-l-4 border-bsu-red border-y border-r border-gray-100 shadow-md transform translate-x-1' 
                            : 'bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700'"
                    >
                         <!-- Content Grid -->
                        <div class="flex items-start gap-5 relative z-10">
                            <!-- Icon Container -->
                            <div class="flex-shrink-0 mt-0.5">
                                <span class="inline-flex items-center justify-center h-12 w-12 rounded-lg {{ $iconStyle }} text-xl">
                                    {!! $notification->icon !!}
                                </span>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0 pt-1">
                                <div class="flex items-center justify-between mb-2 flex-wrap gap-3">
                                    <h3 class="text-lg font-bold transition-colors leading-tight" 
                                        :class="!is_read_local ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400'">
                                        {{ $notification->title }}
                                    </h3>
                                    <span class="text-xs font-semibold text-gray-500 flex items-center bg-gray-200/50 px-2 py-1 rounded">
                                        {{ $notification->time_ago }}
                                    </span>
                                </div>
                                
                                <div class="prose prose-sm dark:prose-invert max-w-none">
                                    <p class="whitespace-pre-wrap leading-relaxed transition-colors"
                                       :class="!is_read_local ? 'text-gray-800 dark:text-gray-300' : 'text-gray-500 dark:text-gray-500'">{{ $notification->message }}</p>
                                </div>
                                
                                <!-- Optional: Small badge for type (Simplified) -->
                                <div class="mt-3 flex items-center gap-2">
                                     @if($notification->type === 'scholarship_created')
                                        <span class="text-xs font-bold text-bsu-red uppercase tracking-wider">Scholarship</span>
                                     @elseif($notification->type === 'application_status')
                                        <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">Status</span>
                                     @elseif($notification->type === 'sfao_comment')
                                        <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">SFAO</span>
                                     @endif
                                </div>
                                
                                <!-- Action Buttons (Themed) -->
                                <div class="mt-5 flex items-center justify-end">
                                    <button 
                                        @click.stop="toggleReadStatus()"
                                        class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-md transition-all duration-200 shadow-sm"
                                        :class="!is_read_local 
                                            ? 'bg-bsu-red text-white hover:bg-red-700 hover:shadow-md'
                                            : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 hover:text-gray-900'"
                                    >
                                        <template x-if="!is_read_local">
                                            <span class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Mark as Read
                                            </span>
                                        </template>
                                        <template x-if="is_read_local">
                                            <span class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                </svg>
                                                Mark as Unread
                                            </span>
                                        </template>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Empty States (Preserved & Themed) -->
            <div x-show="counts.all === 0" x-cloak class="flex flex-col items-center justify-center py-20 px-4 text-center">
                 <div class="bg-gray-100 p-6 rounded-full mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900">All caught up!</h3>
                <p class="text-gray-500 mt-2 max-w-sm">You have no new notifications.</p>
            </div>
             <!-- ... (Specific Empty States using Gray/Red/Black theme) ... -->
             <div x-show="currentFilter === 'scholarship_created' && counts.scholarship_created === 0" x-cloak class="flex flex-col items-center justify-center py-20 px-4 text-center">
                <div class="bg-red-50 p-6 rounded-full mb-4 border border-red-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-bsu-red" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900">No New Scholarships</h3>
                <p class="text-gray-500 mt-2 max-w-sm">No announcements at this time.</p>
            </div>
             
             <!-- Other empty states... similar simplified styling -->

        @else
            <!-- Global Empty State -->
             <div class="flex flex-col items-center justify-center py-24 px-4 text-center">
                <div class="bg-gray-100 p-8 rounded-full mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">All caught up!</h3>
                <p class="text-gray-500 mt-3">You have no notifications.</p>
            </div>
        @endif
    </div>
</div>
