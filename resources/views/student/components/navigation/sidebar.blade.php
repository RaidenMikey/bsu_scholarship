  <!-- Sidebar -->
  <aside class="fixed inset-y-0 left-0 w-64 bg-bsu-red text-white dark:bg-gray-800 transform md:translate-x-0 transition-transform duration-300 z-50 flex flex-col"
         :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
         @keydown.escape.window="sidebarOpen = false"
         x-cloak>
    <!-- Profile Info - Fixed at top -->
    <div class="flex flex-col items-center mt-6 flex-shrink-0">
      <img src="{{ $user && $user->profile_picture ? asset('storage/profile_pictures/' . $user->profile_picture) . '?' . now()->timestamp : asset('images/default-avatar.png') }}"
        alt="Profile Picture"
        class="h-16 w-16 rounded-full border-2 border-white object-cover">
      <div class="text-center mt-2">
        <h2 class="text-lg font-semibold">
          {{ $user?->name ?: explode('@', $user?->email)[0] }}
        </h2>
        <p class="text-sm text-gray-200">
          Student
        </p>
      </div>
    </div>

    <!-- Navigation - Scrollable -->
    <nav class="mt-6 px-4 pb-4 overflow-y-auto flex-1 space-y-4" style="scrollbar-width: thin; scrollbar-color: rgba(156, 163, 175, 0.3) transparent;">
      <!-- Scholarships Header -->
      <div class="space-y-1">
        <div class="px-4 py-2 text-sm font-semibold text-gray-200 uppercase tracking-wider">
          Scholarships
        </div>
        <button @click="tab = 'scholarships'; subTab = 'all'; sidebarOpen = false"
                class="w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition text-sm"
                :class="(tab === 'scholarships' && subTab === 'all') ? 'bg-white text-bsu-red dark:bg-gray-200' : 'text-white dark:text-white'">
          🎓 All Scholarships
        </button>
        <button @click="tab = 'scholarships'; subTab = 'private'; sidebarOpen = false"
                class="w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition text-sm"
                :class="(tab === 'scholarships' && subTab === 'private') ? 'bg-white text-bsu-red dark:bg-gray-200' : 'text-white dark:text-white'">
          🏛️ Private
        </button>
        <button @click="tab = 'scholarships'; subTab = 'government'; sidebarOpen = false"
                class="w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition text-sm"
                :class="(tab === 'scholarships' && subTab === 'government') ? 'bg-white text-bsu-red dark:bg-gray-200' : 'text-white dark:text-white'">
          🏛️ Government
        </button>
      </div>

      <!-- Application Forms Header -->
      <div class="space-y-1">
        <div class="px-4 py-2 text-sm font-semibold text-gray-200 uppercase tracking-wider">
          Application Forms
        </div>
        <button @click="tab = 'scholarships'; subTab = 'form'; sidebarOpen = false"
                class="w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition text-sm"
                :class="(tab === 'scholarships' && subTab === 'form') ? 'bg-white text-bsu-red dark:bg-gray-200' : 'text-white dark:text-white'">
          📝 SFAO Application Form
        </button>
        <button @click="tab = 'scholarships'; subTab = 'gvsreap_form'; sidebarOpen = false"
                class="w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition text-sm"
                :class="(tab === 'scholarships' && subTab === 'gvsreap_form') ? 'bg-white text-bsu-red dark:bg-gray-200' : 'text-white dark:text-white'">
          📝 GVSREAP Application Form
        </button>
      </div>

      <!-- Applications Header -->
      <div class="space-y-1">
        <div class="px-4 py-2 text-sm font-semibold text-gray-200 uppercase tracking-wider">
          Applications
        </div>
        <button @click="tab = 'applied-scholarships'; sidebarOpen = false"
                class="w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition text-sm"
                :class="tab === 'applied-scholarships' ? 'bg-white text-bsu-red dark:bg-gray-200' : 'text-white dark:text-white'">
          🎓 Applied Scholarships
        </button>
        <button @click="tab = 'application-tracking'; sidebarOpen = false"
                class="w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition text-sm"
                :class="tab === 'application-tracking' ? 'bg-white text-bsu-red dark:bg-gray-200' : 'text-white dark:text-white'">
          📊 Application Tracking
        </button>
      </div>

      <button @click="tab = 'notifications'; sidebarOpen = false"
              class="w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition relative"
              :class="tab === 'notifications' ? 'bg-white text-bsu-red dark:bg-gray-200' : 'text-white dark:text-white'">
        <span class="flex items-center justify-between">
          <span>🔔 Notifications</span>
          @if($unreadCount > 0)
            <span class="bg-red-500 text-white text-xs rounded-full px-2 py-1 min-w-[20px] text-center">
              {{ $unreadCount }}
            </span>
          @endif
        </span>
      </button>
    </nav>

    <!-- Settings Section - Fixed at bottom -->
    <div class="px-4 pb-4 flex-shrink-0 border-t border-bsu-redDark/30 dark:border-gray-700 pt-4">
      <div class="space-y-1">
        <div class="px-4 py-2 text-sm font-semibold text-gray-200 uppercase tracking-wider">
          Settings
        </div>
        <button @click="tab = 'account'; sidebarOpen = false"
                class="w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition text-sm"
                :class="tab === 'account' ? 'bg-white text-bsu-red dark:bg-gray-200' : 'text-white dark:text-white'">
          ⚙️ Account
        </button>
        <button @click="showLogoutModal = true"
                class="block w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition text-sm text-white dark:text-white">
          🚪 Logout
        </button>
      </div>
    </div>

  </aside>
