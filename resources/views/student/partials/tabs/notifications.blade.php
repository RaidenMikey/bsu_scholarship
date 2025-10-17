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
             class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition-all duration-200 border-l-4 {{ $notification->is_read ? 'border-gray-300 dark:border-gray-600' : 'border-bsu-red' }} cursor-pointer"
             :class="{ 'opacity-75': isRead }"
             @click="openNotificationModal({{ $notification->id }})"
             data-notification-id="{{ $notification->id }}">
          
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
                        @elseif($notification->data['status'] === 'in_progress') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                        @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $notification->data['status'])) }}
                      </span>
                    @endif
                  </div>

                  <!-- SFAO Remarks -->
                  @if(isset($notification->data['remarks']) && !empty($notification->data['remarks']))
                    <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border-l-4 border-blue-400">
                      <div class="flex items-start">
                        <div class="flex-shrink-0">
                          <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                          </svg>
                        </div>
                        <div class="ml-3">
                          <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">SFAO Comments:</h4>
                          <p class="mt-1 text-sm text-blue-700 dark:text-blue-300">{{ $notification->data['remarks'] }}</p>
                        </div>
                      </div>
                    </div>
                  @endif

                  <!-- Document Status Details -->
                  @if(isset($notification->data['document_status']) && $notification->data['document_status'])
                    <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                      <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Document Status:</h4>
                      <div class="grid grid-cols-3 gap-2 text-xs">
                        @if($notification->data['document_status']['approved'] > 0)
                          <div class="flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            <span class="text-gray-600 dark:text-gray-400">{{ $notification->data['document_status']['approved'] }} Approved</span>
                          </div>
                        @endif
                        @if($notification->data['document_status']['pending'] > 0)
                          <div class="flex items-center">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                            <span class="text-gray-600 dark:text-gray-400">{{ $notification->data['document_status']['pending'] }} Pending</span>
                          </div>
                        @endif
                        @if($notification->data['document_status']['rejected'] > 0)
                          <div class="flex items-center">
                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                            <span class="text-gray-600 dark:text-gray-400">{{ $notification->data['document_status']['rejected'] }} Rejected</span>
                          </div>
                        @endif
                      </div>
                    </div>
                  @endif

                  <!-- Specific Document Lists -->
                  @if(isset($notification->data['pending_documents']) && count($notification->data['pending_documents']) > 0)
                    <div class="mt-2">
                      <span class="text-xs font-medium text-yellow-600 dark:text-yellow-400">⏳ Pending Documents:</span>
                      <div class="mt-1 flex flex-wrap gap-1">
                        @foreach($notification->data['pending_documents'] as $doc)
                          <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                            {{ $doc }}
                          </span>
                        @endforeach
                      </div>
                    </div>
                  @endif

                  @if(isset($notification->data['rejected_documents']) && count($notification->data['rejected_documents']) > 0)
                    <div class="mt-2">
                      <span class="text-xs font-medium text-red-600 dark:text-red-400">❌ Rejected Documents:</span>
                      <div class="mt-1 flex flex-wrap gap-1">
                        @foreach($notification->data['rejected_documents'] as $doc)
                          <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                            {{ $doc }}
                          </span>
                        @endforeach
                      </div>
                    </div>
                  @endif
                @endif

                <!-- Action Buttons -->
                <div class="mt-3 flex space-x-2">
                  @if($notification->type === 'scholarship_created' && isset($notification->data['scholarship_id']))
                    <a href="{{ route('student.scholarships') }}#scholarship-{{ $notification->data['scholarship_id'] }}" 
                       class="text-xs px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded transition">
                      View Scholarship
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
                 <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-200 mb-2">⏳ Pending Documents:</h4>
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
                 <h4 class="text-sm font-medium text-red-800 dark:text-red-200 mb-2">❌ Rejected Documents:</h4>
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
             <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">📚 Scholarship:</h4>
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

    init() {
      // Initialize data from HTML attributes
      const dataElement = document.querySelector('[data-notifications]');
      this.notifications = dataElement ? JSON.parse(dataElement.dataset.notifications) : [];
      this.unreadCount = parseInt(dataElement?.dataset.unreadCount || '0');
    },

    openNotificationModal(notificationId) {
      console.log('Opening modal for notification:', notificationId);
      
      // Mark as read when clicked
      this.markAsRead(notificationId);
      
      // Find notification data
      const notification = this.notifications.find(n => n.id === notificationId);
      console.log('Found notification:', notification);
      
      if (notification) {
        this.selectedNotification = notification;
        this.showModal = true;
        console.log('Modal should be visible now');
      } else {
        console.error('Notification not found with ID:', notificationId);
      }
    },

    closeModal() {
      this.showModal = false;
      this.selectedNotification = null;
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
