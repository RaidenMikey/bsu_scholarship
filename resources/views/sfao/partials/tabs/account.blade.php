<!-- Account Tab for SFAO -->
<div x-show="tab === 'account'" x-transition x-cloak class="px-4 py-6">

  <div class="bg-gray-100 dark:bg-gray-900 rounded-xl shadow-lg max-w-xl mx-auto p-6">

    <h1 class="text-3xl font-bold text-bsu-red dark:text-bsu-light mb-8 border-b-2 border-bsu-red pb-3 text-center">
      ⚙️ Account Settings
    </h1>

    <div class="space-y-8">
      <!-- Dark Mode Toggle -->
      <div class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
        <span class="text-base font-medium text-gray-800 dark:text-gray-200">🌙 Enable Dark Mode</span>
        <label class="relative inline-flex items-center cursor-pointer">
          <input type="checkbox" class="sr-only peer" x-model="darkMode">
          <div class="w-10 h-6 bg-gray-300 rounded-full peer dark:bg-gray-600 peer-checked:bg-bsu-red transition"></div>
          <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transform peer-checked:translate-x-4 transition"></div>
        </label>
      </div>

      <!-- Profile Picture Upload -->
      <form method="POST" action="{{ url('/upload-profile-picture/sfao') }}" enctype="multipart/form-data"
        class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow space-y-4">
        @csrf

        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
          📷 Upload ID Profile Picture
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
          📤 Upload & Save
        </button>
      </form>

      <!-- Change Password -->
      <form method="POST" action="{{ url('/sfao/change-password') }}"
            class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow space-y-4">
        @csrf

        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
          🔒 Change Password
        </h2>

        <input type="password" name="current_password" placeholder="Current Password" required
               class="w-full border rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring focus:ring-bsu-red">

        <input type="password" name="new_password" placeholder="New Password" required
               class="w-full border rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring focus:ring-bsu-red">

        <input type="password" name="new_password_confirmation" placeholder="Confirm New Password" required
               class="w-full border rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring focus:ring-bsu-red">

        <button type="submit"
                class="w-full bg-bsu-red hover:bg-bsu-redDark text-white font-semibold py-2 rounded-lg shadow hover:shadow-lg transition">
          🔑 Update Password
        </button>
      </form>

      <!-- Logout -->
      <div class="pt-4 text-center">
        <a href="{{ url('/logout') }}"
          onclick="localStorage.removeItem('activeTab')"
          class="inline-flex items-center justify-center gap-2 text-red-600 hover:text-red-800 font-semibold transition">
          🚪 Logout
        </a>
      </div>

    </div>

  </div>
</div>
