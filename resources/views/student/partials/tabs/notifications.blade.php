<div x-cloak x-data="notificationData()" data-notifications="{{ json_encode($notifications) }}" data-unread-count="{{ $unreadCount }}" class="px-4 py-6">
  <!-- Header -->
  <div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
      <div class="flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        Notifications
      </div>
    </h2>
    <p class="text-gray-600 dark:text-gray-400">
      Stay updated with your scholarship applications and system updates
    </p>
  </div>

  <!-- Notification Stats -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <!-- Unread Stats -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 transition-transform hover:scale-[1.02] duration-200">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Unread Notifications</p>
          <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $unreadCount }}</p>
        </div>
        <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-xl">
          <svg class="w-8 h-8 text-bsu-red dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5v-5a7.5 7.5 0 1 0-15 0v5h5l-5 5-5-5h5v-5a7.5 7.5 0 1 1 15 0v5z"></path>
          </svg>
        </div>
      </div>
    </div>

    <!-- Total Stats -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 transition-transform hover:scale-[1.02] duration-200">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Received</p>
          <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $notifications->count() }}</p>
        </div>
        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
          <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
          </svg>
        </div>
      </div>
    </div>

    <!-- Recent Stats -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 transition-transform hover:scale-[1.02] duration-200">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Recent (7 Days)</p>
          <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $notifications->where('created_at', '>=', now()->subDays(7))->count() }}</p>
        </div>
        <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-xl">
          <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
      </div>
    </div>
  </div>

  <!-- Actions -->
  <div class="flex justify-end gap-4 mb-6">
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
      <!-- Empty State for Specific Filters -->
      <div x-show="filterType !== '' && filteredCount === 0" class="text-center py-12" x-cloak>
          <div class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
              <span x-show="filterType === 'scholarship_created'">No Notification for Scholarships</span>
              <span x-show="filterType === 'application_status'">No Notification for Status Updates</span>
              <span x-show="filterType === 'sfao_comment'">No Notification for Comments</span>
          </h3>
      </div>

      @foreach($notifications as $notification)
        <div x-data="{ isRead: {{ $notification->is_read ? 'true' : 'false' }} }" 
             x-show="filterType === '' || filterType === '{{ $notification->type }}'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="rounded-xl p-5 mb-3 transition-all duration-200 border border-gray-100 dark:border-gray-700 hover:shadow-md hover:-translate-y-0.5 cursor-pointer relative overflow-hidden group"
             :class="isRead ? 'bg-white dark:bg-gray-800' : 'bg-red-50 dark:bg-gray-800 dark:border-l-4 dark:border-l-bsu-red'"
             @click="openNotificationModal({{ $notification->id }})"
             data-notification-id="{{ $notification->id }}">
          
          <!-- Unread Indicator (Light Mode) -->
          <div x-show="!isRead" class="absolute left-0 top-0 bottom-0 w-1 bg-bsu-red dark:hidden"></div>
          
          <div class="flex items-start space-x-4">
            <!-- Notification Icon -->
            <div class="flex-shrink-0">
              <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl shadow-sm {{ $notification->color }} bg-white dark:bg-gray-700">
                {{ $notification->icon }}
              </div>
            </div>

            <!-- Notification Content -->
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-1">
                <h4 class="text-base font-semibold {{ $notification->is_read ? 'text-gray-600 dark:text-gray-300' : 'text-gray-900 dark:text-white' }}">
                  {{ $notification->title }}
                </h4>
                
                <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap ml-2">
                  {{ $notification->time_ago }}
                </span>
              </div>

              <p class="text-sm {{ $notification->is_read ? 'text-gray-500 dark:text-gray-400' : 'text-gray-700 dark:text-gray-300' }} line-clamp-2 mb-3">
                {{ $notification->message }}
              </p>

              <!-- Additional Data Display -->
              @if($notification->data)
                <div class="flex flex-wrap gap-2 mb-3">
                  @if(isset($notification->data['scholarship_name']))
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                      <svg class="mr-1.5 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                      </svg>
                      {{ $notification->data['scholarship_name'] }}
                    </span>
                  @endif
                  
                  @if(isset($notification->data['status']))
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium
                      @if($notification->data['status'] === 'approved') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                      @elseif($notification->data['status'] === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                      @elseif($notification->data['status'] === 'in_progress') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300
                      @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300
                      @endif">
                      {{ ucfirst(str_replace('_', ' ', $notification->data['status'])) }}
                    </span>
                  @endif
                </div>
              @endif

              <!-- Action Buttons -->
              <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700/50 mt-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                <div class="flex space-x-2">
                  @if($notification->type === 'scholarship_created' && isset($notification->data['scholarship_id']))
                    <a href="{{ route('student.scholarships') }}#scholarship-{{ $notification->data['scholarship_id'] }}" 
                       class="text-xs px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition shadow-sm"
                       @click.stop>
                      View Scholarship
                    </a>
                  @endif
                </div>

                <div class="flex space-x-2">
                  <button x-show="!isRead" 
                          @click.stop="markAsRead({{ $notification->id }})"
                          class="text-xs px-3 py-1.5 bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200 dark:border-gray-600 rounded-md transition shadow-sm flex items-center gap-1.5">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      Mark Read
                  </button>
                  
                  <button @click.stop="deleteNotification({{ $notification->id }})"
                          class="text-xs px-3 py-1.5 bg-white hover:bg-red-50 text-red-600 border border-gray-200 hover:border-red-200 dark:bg-gray-700 dark:hover:bg-red-900/30 dark:text-red-400 dark:border-gray-600 dark:hover:border-red-800 rounded-md transition shadow-sm flex items-center gap-1.5">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                      Delete
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
       @endforeach
     @endif
   </div>

   <!-- Notification Detail Modal -->
   <div x-show="showModal" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;">
     
     <!-- Backdrop -->
     <div class="fixed inset-0 bg-black bg-opacity-50" @click="closeModal()"></div>
     
     <!-- Modal -->
     <div class="flex min-h-full items-center justify-center p-4">
       <div x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
         
         <!-- Modal Header -->
         <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
           <div class="flex items-center space-x-3">
             <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg" 
                  :class="selectedNotification?.color">
               <span x-text="selectedNotification?.icon"></span>
             </div>
             <div>
               <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="selectedNotification?.title"></h3>
               <p class="text-sm text-gray-500 dark:text-gray-400" x-text="selectedNotification?.time_ago"></p>
             </div>
           </div>
           <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
             </svg>
           </button>
         </div>

         <!-- Modal Body -->
         <div class="p-6 max-h-96 overflow-y-auto">
           <!-- Title -->
           <div class="mb-6">
             <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Notification Details</h4>
             <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed" x-text="selectedNotification?.message"></p>
           </div>

           <!-- Remarks Section -->
           <div x-show="selectedNotification?.data?.remarks" class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border-l-4 border-blue-400">
             <div class="flex items-start">
               <div class="flex-shrink-0">
                 <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                   <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                 </svg>
               </div>
               <div class="ml-3">
                 <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-1">Remarks:</h4>
                 <p class="text-sm text-blue-700 dark:text-blue-300" x-text="selectedNotification?.data?.remarks"></p>
               </div>
             </div>
           </div>

           <!-- Evaluated By Section -->
           <div x-show="selectedNotification?.data?.evaluated_by" class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
             <div class="flex items-center">
               <div class="flex-shrink-0">
                 <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                   <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                 </svg>
               </div>
               <div class="ml-3">
                 <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-1">Evaluated By:</h4>
                 <p class="text-sm text-gray-700 dark:text-gray-300" x-text="selectedNotification?.data?.evaluated_by"></p>
               </div>
             </div>
           </div>

           <!-- Pending Documents - Only show if evaluation status is pending -->
           <div x-show="selectedNotification?.data?.status === 'pending' && selectedNotification?.data?.pending_documents?.length > 0" 
                class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border-l-4 border-yellow-400">
             <div class="flex items-start">
               <div class="flex-shrink-0">
                 <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                   <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                 </svg>
               </div>
               <div class="ml-3 flex-1">
                 <h4 class="flex items-center gap-2 text-sm font-medium text-yellow-800 dark:text-yellow-200 mb-2">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                     </svg>
                     Pending Documents:
                 </h4>
                 <p class="text-xs text-yellow-700 dark:text-yellow-300 mb-3">The following documents are still pending evaluation:</p>
                 <div class="space-y-2">
                   <template x-for="doc in selectedNotification?.data?.pending_documents" :key="doc">
                     <div class="flex items-center p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded">
                       <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                         <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                       </svg>
                       <span class="text-sm text-yellow-800 dark:text-yellow-200" x-text="doc"></span>
                     </div>
                   </template>
                 </div>
               </div>
             </div>
           </div>

           <!-- Rejected Documents - Only show if evaluation status is rejected -->
           <div x-show="selectedNotification?.data?.status === 'rejected' && selectedNotification?.data?.rejected_documents?.length > 0" 
                class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border-l-4 border-red-400">
             <div class="flex items-start">
               <div class="flex-shrink-0">
                 <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                   <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                 </svg>
               </div>
               <div class="ml-3 flex-1">
                 <h4 class="flex items-center gap-2 text-sm font-medium text-red-800 dark:text-red-200 mb-2">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                     </svg>
                     Rejected Documents:
                 </h4>
                 <p class="text-xs text-red-700 dark:text-red-300 mb-3">The following documents have been rejected and need to be resubmitted:</p>
                 <div class="space-y-2">
                   <template x-for="doc in selectedNotification?.data?.rejected_documents" :key="doc">
                     <div class="flex items-center p-2 bg-red-100 dark:bg-red-900/30 rounded">
                       <svg class="w-4 h-4 text-red-600 dark:text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                         <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                       </svg>
                       <span class="text-sm text-red-800 dark:text-red-200" x-text="doc"></span>
                     </div>
                   </template>
                 </div>
                 <div class="mt-3 p-2 bg-red-100 dark:bg-red-900/30 rounded">
                   <p class="text-xs text-red-700 dark:text-red-300">
                     <strong>Note:</strong> Please review the rejection reasons and resubmit the corrected documents.
                   </p>
                 </div>
               </div>
             </div>
           </div>

           <!-- Scholarship Information (if applicable) -->
           <div x-show="selectedNotification?.data?.scholarship_name" class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
             <h4 class="flex items-center gap-2 text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                 </svg>
                 Scholarship:
             </h4>
             <p class="text-sm text-blue-700 dark:text-blue-300" x-text="selectedNotification?.data?.scholarship_name"></p>
           </div>
         </div>

         <!-- Modal Footer -->
         <div class="flex items-center justify-between p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
           <div class="text-sm text-gray-500 dark:text-gray-400">
             <span x-show="!selectedNotification?.is_read" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 mr-2">
               New
             </span>
             <span x-text="`Received ${selectedNotification?.time_ago}`"></span>
           </div>
           <div class="flex space-x-3">
             <button @click="closeModal()" 
                     class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm font-medium transition">
               Close
             </button>
             <template x-if="selectedNotification?.type === 'scholarship_created' && selectedNotification?.data?.scholarship_id">
               <a :href="`/student/scholarships#scholarship-${selectedNotification.data.scholarship_id}`" 
                  class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                 View Scholarship
               </a>
             </template>
           </div>
         </div>
       </div>
     </div>
   </div>
 </div>

<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('notificationData', () => ({
    filterType: '',
    notifications: [],
    unreadCount: 0,
    selectedNotification: null,
    showModal: false,
    filteredCount: 0,

    init() {
      this.notifications = JSON.parse(this.$el.dataset.notifications);
      this.unreadCount = parseInt(this.$el.dataset.unreadCount);
      this.updateFilteredCount();

      // Listen for subtab-changed event from parent dashboard
      window.addEventListener('subtab-changed', (e) => {
        this.syncFilterWithSubTab(e.detail);
      });

      // Initial sync
      this.syncFilterWithSubTab(this.$parent.subTab);
    },

    updateFilteredCount() {
      if (this.filterType === '') {
        this.filteredCount = this.notifications.length;
      } else {
        this.filteredCount = this.notifications.filter(n => n.type === this.filterType).length;
      }
    },

    openNotificationModal(notificationId) {
      // Mark as read when clicked
      this.markAsRead(notificationId);
      
      // Find notification data
      const notification = this.notifications.find(n => n.id === notificationId);
      
      if (notification) {
        this.selectedNotification = notification;
        this.showModal = true;
      }
    },

    closeModal() {
      this.showModal = false;
      this.selectedNotification = null;
    },

    syncFilterWithSubTab(subTab) {
      // Remove the tab check to ensure filter updates even when switching tabs simultaneously
      // if (this.$parent.tab !== 'notifications') return;

      switch(subTab) {
        case 'scholarship_created':
          this.filterType = 'scholarship_created';
          break;
        case 'application_status':
          this.filterType = 'application_status';
          break;
        case 'sfao_comment':
          this.filterType = 'sfao_comment';
          break;
        default:
          this.filterType = '';
      }
      this.updateFilteredCount();
    },

    markAsRead(notificationId) {
      fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Content-Type': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const notification = this.notifications.find(n => n.id === notificationId);
          if (notification) {
            notification.is_read = true;
            // Update local state for immediate feedback
            const el = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (el) {
              el.__x.$data.isRead = true;
            }
            
            if (this.unreadCount > 0) {
              this.unreadCount--;
            }
          }
        }
      })
      .catch(error => console.error('Error:', error));
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
    },

    deleteNotification(notificationId) {
      if (!confirm('Are you sure you want to delete this notification?')) return;

      fetch(`/notifications/${notificationId}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const index = this.notifications.findIndex(n => n.id === notificationId);
          if (index > -1) {
             const wasUnread = !this.notifications[index].is_read;
             this.notifications.splice(index, 1);
             if (wasUnread) {
               this.unreadCount = Math.max(0, this.unreadCount - 1);
             }
             this.updateFilteredCount();
             
             // Remove element from DOM
             const el = document.querySelector(`[data-notification-id="${notificationId}"]`);
             if (el) el.remove();
          }
        }
      })
      .catch(error => {
        console.error('Error deleting notification:', error);
      });
    }
  }));
});
</script>
