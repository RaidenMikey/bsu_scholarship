@php
  use Illuminate\Support\Facades\Session;
  use Illuminate\Support\Facades\Redirect;
  use App\Models\User;

  // Redirect to login if session has ended
  if (!Session::has('user_id')) {
    return redirect()->route('login');
  }

  $user = User::find(session('user_id'));

  // If no user found, flush session and redirect
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
    rightSidebarOpen: false,
    tab: localStorage.getItem('activeTab') || 'scholarships',
    subTab: new URLSearchParams(window.location.search).get('type') || 'all',
    darkMode: localStorage.getItem('darkMode') === 'true',
    showLogoutModal: false
  }"
  x-init="
    // Set initial tab based on scholarship type parameter
    @if(isset($scholarshipType) && $scholarshipType !== 'all')
      subTab = '{{ $scholarshipType }}';
    @endif
    
    $watch('darkMode', val => localStorage.setItem('darkMode', val));
    $watch('tab', val => localStorage.setItem('activeTab', val));
    // subTab is now controlled by URL query param, so we don't need to watch/store it for scholarships
  ">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="Cache-Control" content="no-store" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Student Dashboard</title>
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
  <style>
    /* Custom scrollbar styling - minimized and subtle */
    [x-cloak] { display: none !important; }
    nav::-webkit-scrollbar {
      width: 6px;
    }
    nav::-webkit-scrollbar-track {
      background: transparent;
    }
    nav::-webkit-scrollbar-thumb {
      background: rgba(156, 163, 175, 0.3);
      border-radius: 3px;
    }
    nav::-webkit-scrollbar-thumb:hover {
      background: rgba(156, 163, 175, 0.5);
    }
    .dark nav::-webkit-scrollbar-thumb {
      background: rgba(156, 163, 175, 0.2);
    }
    .dark nav::-webkit-scrollbar-thumb:hover {
      background: rgba(156, 163, 175, 0.4);
    }
  </style>
</head>

<body class="bg-gray-100 dark:bg-gray-900 dark:text-white min-h-screen font-sans">


  <!-- Sidebar -->
  @include('student.components.navigation.sidebar', ['user' => $user, 'unreadCount' => $unreadCount])

  <!-- Logout Confirmation Modal -->
  @include('student.components.modals.logout')

  <!-- Main Header -->
  <header class="flex items-center justify-between px-8 py-4 bg-[#2f2f2f] dark:bg-gray-800 shadow-sm sticky top-0 z-30 border-b border-gray-700 transition-all duration-300"
          :class="{ 'ml-64': sidebarOpen, 'mr-64': rightSidebarOpen }">
    <!-- Branding -->
    <div class="flex items-center space-x-3">
        <button @click="sidebarOpen = true" class="text-white hover:text-gray-300 focus:outline-none mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <img src="{{ asset('images/Batangas_State_Logo.png') }}" alt="Logo" class="h-12 w-auto">
        <div class="text-white">
            <div class="text-base font-bold leading-tight">Batangas State University</div>
            <div class="text-sm font-light">The National Engineering University</div>
        </div>
    </div>

    <!-- Dark Mode Toggle & User Profile -->
    <div class="flex items-center gap-4">
        <!-- Dark Mode Toggle -->
        <button @click="darkMode = !darkMode" 
                class="p-2 rounded-full hover:bg-gray-700 transition-colors focus:outline-none"
                :title="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'">
            <!-- Sun Icon (for Dark Mode) -->
            <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <!-- Moon Icon (for Light Mode) -->
            <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>

        <!-- User Profile Icon -->
        <div class="relative">
        <button @click="rightSidebarOpen = true" class="flex items-center focus:outline-none">
            <img src="{{ $user && $user->profile_picture ? asset('storage/profile_pictures/' . $user->profile_picture) . '?' . now()->timestamp : asset('images/default-avatar.png') }}"
                 alt="Profile"
                 class="h-10 w-10 rounded-full border-2 border-white object-cover hover:border-bsu-red transition-colors">
        </button>
    </div>
  </header>

  <!-- Right Sidebar -->
  <aside class="fixed inset-y-0 right-0 w-64 bg-white dark:bg-gray-800 shadow-xl transform transition-transform duration-300 z-50 flex flex-col"
         :class="rightSidebarOpen ? 'translate-x-0' : 'translate-x-full'"
         x-cloak>
      <div class="h-full flex flex-col py-6 overflow-y-scroll">
        <div class="px-4 sm:px-6">
          <div class="flex items-start justify-between">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Profile</h2>
            <div class="ml-3 h-7 flex items-center">
              <button @click="rightSidebarOpen = false" class="bg-white dark:bg-gray-800 rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bsu-red">
                <span class="sr-only">Close panel</span>
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
        </div>
        <div class="mt-6 relative flex-1 px-4 sm:px-6">
          <!-- Profile Content -->
          <div class="flex flex-col items-center">
              <img src="{{ $user && $user->profile_picture ? asset('storage/profile_pictures/' . $user->profile_picture) . '?' . now()->timestamp : asset('images/default-avatar.png') }}"
                   alt="Profile Picture"
                   class="h-32 w-32 rounded-full border-4 border-gray-200 dark:border-gray-700 object-cover mb-4">
              <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $user?->name ?? 'Student User' }}</h3>
          </div>

          <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6 space-y-4">
              <button @click="tab = 'account'; $nextTick(() => rightSidebarOpen = false)" class="w-full flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  Account Settings
              </button>
              <button @click="showLogoutModal = true; $nextTick(() => rightSidebarOpen = false)" class="w-full flex items-center px-4 py-3 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                  </svg>
                  Logout
              </button>
          </div>
        </div>
      </div>
  </aside>

  <!-- Main Content -->
  <main class="p-4 md:p-8 min-h-screen bg-white dark:bg-gray-900 transition-all duration-300"
        :class="{ 'ml-64': sidebarOpen, 'mr-64': rightSidebarOpen }">

    <!-- Toasts for success and errors -->
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
    <!-- Scholarships Tab -->
    <!-- Scholarships Tab -->
    <div x-show="tab === 'scholarships'" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100">
      <!-- Scholarships Content -->
      <!-- Scholarships Content -->
      <div x-show="subTab !== 'form' && subTab !== 'gvsreap_form'" 
           x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="opacity-0 transform scale-95"
           x-transition:enter-end="opacity-100 transform scale-100">
        @include('student.partials.tabs.scholarships')
      </div>
      
      <!-- Application Form Sub-tab -->
      <div x-show="subTab === 'form'" 
           x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="opacity-0 transform scale-95"
           x-transition:enter-end="opacity-100 transform scale-100">
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg p-6">
          <h1 class="text-3xl font-bold text-bsu-red dark:text-bsu-red border-b-2 border-bsu-red pb-2 mb-6">
            <span class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                SFAO Application Form
            </span>
          </h1>
          
          <!-- Application Status & Progress -->
          @include('student.components.dashboard.application-status', ['form' => $form])

          <!-- Application Data Summary Cards -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
              <!-- Personal Data Card -->
              @include('student.components.dashboard.personal-data-card', ['form' => $form])

              <!-- Academic Data Card -->
              @include('student.components.dashboard.academic-data-card', ['form' => $form])

              <!-- Family Data Card -->
              @include('student.components.dashboard.family-data-card', ['form' => $form])

              <!-- Essay/Question Card -->
              @include('student.components.dashboard.essay-card', ['form' => $form])

              <!-- Certification Card -->
              @include('student.components.dashboard.certification-card', ['form' => $form])
          </div>

          <!-- Action Buttons -->
          <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('student.forms.application_form') }}" 
               class="inline-flex items-center px-6 py-3 bg-bsu-red hover:bg-bsu-redDark text-white font-semibold rounded-lg shadow hover:shadow-lg transition">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
              <span>Proceed to Application Form</span>
            </a>
            
            <a href="{{ route('student.print-application', ['type' => 'sfao']) }}" 
               class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 font-semibold rounded-lg shadow hover:shadow-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
              </svg>
              <span>Print Application</span>
            </a>
          </div>
        </div>
      </div>

      <!-- TDP Application Form Sub-tab -->
      <div x-show="subTab === 'gvsreap_form'" 
           x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="opacity-0 transform scale-95"
           x-transition:enter-end="opacity-100 transform scale-100">
        @include('student.partials.tabs.tdp-application-form')
      </div>
    </div>

    <!-- Applied Scholarships Tab -->
    <div x-show="tab === 'applied-scholarships'" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100">
      @include('student.partials.tabs.applications')
    </div>
    
    <!-- Application Tracking Tab -->
    <!-- Application Tracking Tab -->
    <div x-show="tab === 'application-tracking'" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100">
      @include('student.partials.application_tracking')
    </div>
    
    <!-- Notifications Tab -->
    <!-- Notifications Tab -->
    <div x-show="tab === 'notifications'" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100">
      @include('student.partials.tabs.notifications')
    </div>
    
    @include('student.partials.tabs.account')
  </main>

  <!-- Fix Safari Back Cache Bug -->
  <script>
    window.addEventListener("pageshow", function (event) {
      if (event.persisted) {
        window.location.reload();
      }
    });
    
    // Handle dropdown states
    document.addEventListener('alpine:init', () => {
      Alpine.data('dashboard', () => ({
        init() {
          // Open scholarships dropdown if scholarships tab is active
          if (this.tab === 'scholarships') {
            this.scholarshipsDropdownOpen = true;
          }
          // Open applications dropdown if applications tab is active
          if (this.tab === 'applied-scholarships' || this.tab === 'application-tracking') {
            this.applicationsDropdownOpen = true;
          }
        }
      }));
    });
  </script>
</body>
</html>
