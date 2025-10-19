@php
  use Illuminate\Support\Facades\Session;
  use Illuminate\Support\Facades\Redirect;
  use App\Models\User;

  // Redirect to login if session has ended or role mismatch
  if (!Session::has('user_id') || session('role') !== 'sfao') {
    return redirect()->route('login');
  }

  $user = User::find(session('user_id'));

  if (!$user) {
    Session::flush();
    return redirect()->route('login');
  }
@endphp

<!DOCTYPE html>
<html lang="en"
  :class="{ 'dark': darkMode }"
  x-data="{
    sidebarOpen: false,
    tab: localStorage.getItem('sfaoTab') || '{{ $activeTab ?? "scholarships" }}',
    darkMode: localStorage.getItem('darkMode') === 'true',
    scholarshipsDropdownOpen: false
  }"
  x-init="
    $watch('darkMode', val => localStorage.setItem('darkMode', val));
    $watch('tab', val => localStorage.setItem('sfaoTab', val));
    
    // Handle dropdown states
    if (localStorage.getItem('sfaoTab') === 'scholarships' || localStorage.getItem('sfaoTab') === 'scholarships-internal' || localStorage.getItem('sfaoTab') === 'scholarships-external' || localStorage.getItem('sfaoTab') === 'scholarships-public' || localStorage.getItem('sfaoTab') === 'scholarships-government') {
      this.scholarshipsDropdownOpen = true;
    }
  ">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="Cache-Control" content="no-store" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="0" />
  <title>SFAO Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="{{ asset('favicon.ico') }}">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">

  <!-- Tailwind & Alpine.js -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/alpinejs" defer></script>

  <!-- Tailwind Custom Config -->
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            bsu: {
              red: '#b91c1c',
              redDark: '#991b1b',
              light: '#fef2f2'
            }
          }
        }
      }
    };
  </script>
</head>

<body class="bg-gray-100 dark:bg-gray-900 dark:text-white min-h-screen font-sans">
  <!-- Mobile Overlay -->
  <div class="md:hidden fixed inset-0 bg-black bg-opacity-50 z-40"
       x-show="sidebarOpen"
       x-transition.opacity
       @click="sidebarOpen = false"
       x-cloak></div>

  <!-- Sidebar -->
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
        <p class="text-sm text-gray-200">SFAO Staff</p>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="mt-6 space-y-2 px-4">
      <!-- Scholarships Dropdown -->
      <div class="space-y-1">
        <button @click="scholarshipsDropdownOpen = !scholarshipsDropdownOpen; tab = 'scholarships'; sidebarOpen = false"
                class="w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition flex items-center justify-between"
                :class="tab === 'scholarships' || tab === 'scholarships-internal' || tab === 'scholarships-external' || tab === 'scholarships-public' || tab === 'scholarships-government' ? 'bg-white text-bsu-red dark:bg-gray-200' : 'text-white dark:text-white'">
          <span>🎓 Scholarships</span>
          <svg class="w-4 h-4 transition-transform" :class="scholarshipsDropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        
        <!-- Dropdown Menu -->
        <div x-show="scholarshipsDropdownOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="ml-4 space-y-1">
          <button @click="tab = 'scholarships'; sidebarOpen = false"
                  class="w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition text-sm"
                  :class="tab === 'scholarships' ? 'bg-white text-bsu-red dark:bg-gray-200' : 'text-white dark:text-white'">
            📚 All Scholarships
          </button>
          <button @click="tab = 'scholarships-internal'; sidebarOpen = false"
                  class="w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition text-sm"
                  :class="tab === 'scholarships-internal' ? 'bg-white text-bsu-red dark:bg-gray-200' : 'text-white dark:text-white'">
            🔵 Internal
          </button>
          <button @click="tab = 'scholarships-external'; sidebarOpen = false"
                  class="w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition text-sm"
                  :class="tab === 'scholarships-external' ? 'bg-white text-bsu-red dark:bg-gray-200' : 'text-white dark:text-white'">
            🟣 External
          </button>
          <button @click="tab = 'scholarships-public'; sidebarOpen = false"
                  class="w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition text-sm"
                  :class="tab === 'scholarships-public' ? 'bg-white text-bsu-red dark:bg-gray-200' : 'text-white dark:text-white'">
            🟢 Public
          </button>
          <button @click="tab = 'scholarships-government'; sidebarOpen = false"
                  class="w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition text-sm"
                  :class="tab === 'scholarships-government' ? 'bg-white text-bsu-red dark:bg-gray-200' : 'text-white dark:text-white'">
            🟠 Government
          </button>
        </div>
      </div>

      <button @click="tab = 'applicants'; sidebarOpen = false"
              class="w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition"
              :class="tab === 'applicants' ? 'bg-white text-bsu-red dark:bg-gray-200' : 'text-white dark:text-white'">
        👥 Applicants
      </button>

      <button @click="tab = 'reports'; sidebarOpen = false"
              class="w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition"
              :class="tab === 'reports' ? 'bg-white text-bsu-red dark:bg-gray-200' : 'text-white dark:text-white'">
        📊 Reports
      </button>

      <button @click="tab = 'account'; sidebarOpen = false"
              class="w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition"
              :class="tab === 'account' ? 'bg-white text-bsu-red dark:bg-gray-200' : 'text-white dark:text-white'">
        ⚙️ Account
      </button>
    </nav>
  </aside>

  <!-- Mobile Top Bar -->
  <header class="md:hidden flex justify-between items-center bg-bsu-red text-white dark:bg-gray-800 px-4 py-3">
    <button @click="sidebarOpen = true">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>
    <span class="font-semibold text-lg">SFAO Dashboard</span>
  </header>

  <!-- Main Content -->
  <main class="md:ml-64 p-4 md:p-8 min-h-screen bg-white dark:bg-gray-900 transition-colors duration-300">

    <!-- Toasts -->
    @if (session('success'))
      <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition.opacity class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40">
        <div class="bg-white dark:bg-gray-800 border border-green-400 text-green-700 dark:text-green-300 px-6 py-5 rounded-xl shadow-xl flex items-center space-x-4">
          <svg class="w-10 h-10 text-green-500 animate-bounce" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
          <div class="text-lg font-medium">{{ session('success') }}</div>
        </div>
      </div>
    @endif

    @if ($errors->any())
      <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition.opacity class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40">
        <div class="bg-white dark:bg-gray-800 border border-red-400 text-red-700 dark:text-red-300 px-6 py-5 rounded-xl shadow-xl flex items-center space-x-4">
          <svg class="w-10 h-10 text-red-500 animate-pulse" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
          <div class="text-lg font-medium">{{ $errors->first() }}</div>
        </div>
      </div>
    @endif

    <!-- Tabs -->
    @include('sfao.partials.tabs.scholarships') <!-- Scholarship Lists -->
    @include('sfao.partials.tabs.applicants')   <!-- Applicants Lists -->
    @include('sfao.partials.tabs.reports')      <!-- Reports -->
    @include('sfao.partials.tabs.account')      <!-- Account -->
  </main>

  <!-- Fix Safari Back Cache Bug -->
  <script>
    window.addEventListener("pageshow", function (event) {
      if (event.persisted) {
        window.location.reload();
      }
    });
    
  </script>
</body>
</html>
