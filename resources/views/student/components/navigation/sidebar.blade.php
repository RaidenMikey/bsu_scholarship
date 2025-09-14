<aside class="fixed inset-y-0 left-0 w-64 bg-bsu-red text-white dark:bg-gray-800 transform md:translate-x-0 transition-transform duration-300 z-50"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       @keydown.escape.window="sidebarOpen = false"
       x-cloak>
  <!-- Profile Info -->
  <div class="flex flex-col items-center mt-6">
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

  <!-- Navigation -->
  <nav class="mt-6 space-y-2 px-4">
    <button @click="tab = 'scholarships'; sidebarOpen = false"
            class="w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition"
            :class="tab === 'scholarships' ? 'bg-white text-bsu-red dark:bg-gray-200' : 'text-white dark:text-white'">
      🎓 Scholarships
    </button>

    <button @click="tab = 'applications'; sidebarOpen = false"
            class="w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition"
            :class="tab === 'applications' ? 'bg-white text-bsu-red dark:bg-gray-200' : 'text-white dark:text-white'">
      📄 Applications
    </button>

    <button @click="tab = 'announcements'; sidebarOpen = false"
            class="w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition"
            :class="tab === 'announcements' ? 'bg-white text-bsu-red dark:bg-gray-200' : 'text-white dark:text-white'">
      📢 Announcements
    </button>

    <button @click="tab = 'account'; sidebarOpen = false"
            class="w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition"
            :class="tab === 'account' ? 'bg-white text-bsu-red dark:bg-gray-200' : 'text-white dark:text-white'">
      ⚙️ Account
    </button>
  </nav>
</aside>

