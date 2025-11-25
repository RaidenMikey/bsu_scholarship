<!-- Settings Tab -->
<div x-show="tab === 'settings'" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-cloak 
     class="px-4 py-6">

  <div class="bg-gray-100 dark:bg-gray-900 rounded-xl shadow-lg max-w-xl mx-auto p-6">

    <h1 class="text-3xl font-bold text-bsu-red dark:text-bsu-light mb-8 border-b-2 border-bsu-red pb-3 text-center">
      <div class="flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          Account Settings
      </div>
    </h1>

    <div class="space-y-8">
      <!-- Dark Mode Toggle -->
      <div class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
        <span class="flex items-center gap-2 text-base font-medium text-gray-800 dark:text-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
            Enable Dark Mode
        </span>
        <label class="relative inline-flex items-center cursor-pointer">
          <input type="checkbox" class="sr-only peer" x-model="darkMode">
          <div class="w-10 h-6 bg-gray-300 rounded-full peer dark:bg-gray-600 peer-checked:bg-bsu-red transition"></div>
          <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transform peer-checked:translate-x-4 transition"></div>
        </label>
      </div>

      <!-- Profile Picture Upload -->
      <form method="POST" action="{{ url('/upload-profile-picture/central') }}" enctype="multipart/form-data"
        class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow space-y-4">
        @csrf

        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
          <span class="flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Upload ID Profile Picture
          </span>
        </h2>

        <div class="flex items-center gap-4">
          <input type="file" name="profile_picture" accept="image/*"
                 @change="preview = URL.createObjectURL($event.target.files[0])"
                 required
                 class="flex-1 text-sm text-gray-900 bg-gray-50 border border-bsu-red rounded-lg cursor-pointer dark:bg-gray-700 dark:border-bsu-red dark:text-gray-100">

          <template x-if="preview">
              <img :src="preview" alt="Preview"
                   class="w-16 h-16 rounded-full object-cover border-2 border-bsu-red shadow-md dark:border-gray-500">
          </template>
        </div>

        <button type="submit"
                class="w-full bg-bsu-red hover:bg-bsu-redDark text-white font-semibold py-2 rounded-lg shadow hover:shadow-lg transition">
          <span class="flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
              </svg>
              Upload & Save
          </span>
        </button>
      </form>

      <!-- Change Display Name -->
      <form method="POST" action="{{ url('/central/update-name') }}"
            class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow space-y-4">
        @csrf

        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
          <span class="flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
              </svg>
              Change Display Name
          </span>
        </h2>

        <input type="text" name="new_name" placeholder="Enter new name" required
               class="w-full border rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring focus:ring-bsu-red">

        <button type="submit"
                class="w-full bg-bsu-red hover:bg-bsu-redDark text-white font-semibold py-2 rounded-lg shadow hover:shadow-lg transition">
          <span class="flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
              </svg>
              Save Name
          </span>
        </button>
      </form>

      <!-- Change Password -->
      <form method="POST" action="{{ url('/central/change-password') }}"
            class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow space-y-4">
        @csrf

        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
          <span class="flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
              Change Password
          </span>
        </h2>

        <input type="password" name="current_password" placeholder="Current Password" required
               class="w-full border rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring focus:ring-bsu-red">

        <input type="password" name="new_password" placeholder="New Password" required
               class="w-full border rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring focus:ring-bsu-red">

        <input type="password" name="new_password_confirmation" placeholder="Confirm New Password" required
               class="w-full border rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring focus:ring-bsu-red">

        <button type="submit"
                class="w-full bg-bsu-red hover:bg-bsu-redDark text-white font-semibold py-2 rounded-lg shadow hover:shadow-lg transition">
          <span class="flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
              </svg>
              Update Password
          </span>
        </button>
      </form>

      <!-- Logout -->
      <div class="pt-4 text-center">
        <a href="{{ url('/logout') }}"
          onclick="localStorage.removeItem('activeTab')"
          class="inline-flex items-center justify-center gap-2 text-red-600 hover:text-red-800 font-semibold transition">
          <span class="flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
              Logout
          </span>
        </a>
      </div>
    </div>

  </div>
</div>
