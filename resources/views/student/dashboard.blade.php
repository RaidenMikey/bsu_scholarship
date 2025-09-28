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
    tab: localStorage.getItem('activeTab') || 'scholarships',
    subTab: localStorage.getItem('activeSubTab') || 'available',
    darkMode: localStorage.getItem('darkMode') === 'true',
    scholarshipsDropdownOpen: false
  }"
  x-init="
    $watch('darkMode', val => localStorage.setItem('darkMode', val));
    $watch('tab', val => localStorage.setItem('activeTab', val));
    $watch('subTab', val => localStorage.setItem('activeSubTab', val));
  ">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="Cache-Control" content="no-store" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="0" />
  <title>Student Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="{{ asset('images/Batangas_State_Logo.png') }}">
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
        <p class="text-sm text-gray-200">
          Student
        </p>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="mt-6 space-y-2 px-4">
      <!-- Scholarships Dropdown -->
      <div class="space-y-1">
        <button @click="scholarshipsDropdownOpen = !scholarshipsDropdownOpen; tab = 'scholarships'; sidebarOpen = false"
                class="w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition flex items-center justify-between"
                :class="tab === 'scholarships' ? 'bg-white text-bsu-red dark:bg-gray-200' : 'text-white dark:text-white'">
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
          <button @click="tab = 'scholarships'; subTab = 'available'; sidebarOpen = false"
                  class="w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition text-sm"
                  :class="(tab === 'scholarships' && subTab === 'available') ? 'bg-white text-bsu-red dark:bg-gray-200' : 'text-white dark:text-white'">
            📋 Available Scholarships
          </button>
          
          <button @click="tab = 'scholarships'; subTab = 'form'; sidebarOpen = false"
                  class="w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition text-sm"
                  :class="(tab === 'scholarships' && subTab === 'form') ? 'bg-white text-bsu-red dark:bg-gray-200' : 'text-white dark:text-white'">
            📝 Application Form
          </button>
        </div>
      </div>

      <button @click="tab = 'applications'; sidebarOpen = false"
              class="w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition"
              :class="tab === 'applications' ? 'bg-white text-bsu-red dark:bg-gray-200' : 'text-white dark:text-white'">
        📄 Applications
      </button>

      <button @click="tab = 'tracking'; sidebarOpen = false"
              class="w-full text-left px-4 py-2 rounded hover:bg-bsu-redDark dark:hover:bg-gray-700 transition"
              :class="tab === 'tracking' ? 'bg-white text-bsu-red dark:bg-gray-200' : 'text-white dark:text-white'">
        📊 Application Tracking
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

  <!-- Mobile Top Bar -->
  <header class="md:hidden flex justify-between items-center bg-bsu-red text-white dark:bg-gray-800 px-4 py-3">
    <button @click="sidebarOpen = true">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>
    <span class="font-semibold text-lg">Student Dashboard</span>
  </header>

  <!-- Main Content -->
  <main class="md:ml-64 p-4 md:p-8 min-h-screen bg-white dark:bg-gray-900 transition-colors duration-300">

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
    <!-- Scholarships Tab with Sub-tabs -->
    <div x-show="tab === 'scholarships'">
      <!-- Available Scholarships Sub-tab -->
      <div x-show="subTab === 'available'" x-transition>
        @include('student.partials.tabs.scholarships')
      </div>
      
      <!-- Application Form Sub-tab -->
      <div x-show="subTab === 'form'" x-transition>
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg p-6">
          <h1 class="text-3xl font-bold text-bsu-red dark:text-bsu-red border-b-2 border-bsu-red pb-2 mb-6">
            📝 Application Form
          </h1>
          
          <!-- Form Status Overview -->
          <div class="mb-8">
            @if($form)
              <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                  <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  <div>
                    <h3 class="font-semibold text-green-800 dark:text-green-200">Application Status: Complete</h3>
                    <p class="text-sm text-green-700 dark:text-green-300">Last updated: {{ $form->updated_at->format('M d, Y \a\t g:i A') }}</p>
                  </div>
                </div>
              </div>
            @else
              <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                  <svg class="w-6 h-6 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                  </svg>
                  <div>
                    <h3 class="font-semibold text-yellow-800 dark:text-yellow-200">Application Status: Incomplete</h3>
                    <p class="text-sm text-yellow-700 dark:text-yellow-300">Complete all sections below to view available scholarships.</p>
                  </div>
                </div>
              </div>
            @endif
          </div>

          <!-- Form Sections Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Personal Data Section -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border border-blue-200 dark:border-blue-700 rounded-lg p-6 hover:shadow-lg transition-shadow">
              <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mr-4">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200">Personal Data</h3>
                  <p class="text-sm text-blue-600 dark:text-blue-300">Basic information & contact</p>
                </div>
              </div>
              
              <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600 dark:text-gray-300">Name:</span>
                  <span class="font-medium text-gray-800 dark:text-gray-200">
                    @if($form && $form->first_name)
                      {{ $form->first_name }} {{ $form->last_name }}
                    @else
                      <span class="text-gray-400">Not provided</span>
                    @endif
                  </span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600 dark:text-gray-300">Age:</span>
                  <span class="font-medium text-gray-800 dark:text-gray-200">
                    @if($form && $form->age)
                      {{ $form->age }} years old
                    @else
                      <span class="text-gray-400">Not provided</span>
                    @endif
                  </span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600 dark:text-gray-300">Address:</span>
                  <span class="font-medium text-gray-800 dark:text-gray-200">
                    @if($form && $form->town_city)
                      {{ $form->town_city }}, {{ $form->province }}
                    @else
                      <span class="text-gray-400">Not provided</span>
                    @endif
                  </span>
                </div>
              </div>
              
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  @if($form && $form->first_name && $form->age && $form->town_city)
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-sm text-green-600 dark:text-green-400 font-medium">Complete</span>
                  @else
                    <svg class="w-5 h-5 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <span class="text-sm text-yellow-600 dark:text-yellow-400 font-medium">Incomplete</span>
                  @endif
                </div>
                <a href="{{ route('student.forms.application_form') }}#personal" 
                   class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                  Edit →
                </a>
              </div>
            </div>

            <!-- Academic Data Section -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border border-green-200 dark:border-green-700 rounded-lg p-6 hover:shadow-lg transition-shadow">
              <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mr-4">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-green-800 dark:text-green-200">Academic Data</h3>
                  <p class="text-sm text-green-600 dark:text-green-300">Program, grades & achievements</p>
                </div>
              </div>
              
              <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600 dark:text-gray-300">Program:</span>
                  <span class="font-medium text-gray-800 dark:text-gray-200">
                    @if($form && $form->program)
                      {{ $form->program }}
                    @else
                      <span class="text-gray-400">Not provided</span>
                    @endif
                  </span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600 dark:text-gray-300">Year Level:</span>
                  <span class="font-medium text-gray-800 dark:text-gray-200">
                    @if($form && $form->year_level)
                      {{ $form->year_level }}
                    @else
                      <span class="text-gray-400">Not provided</span>
                    @endif
                  </span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600 dark:text-gray-300">GWA:</span>
                  <span class="font-medium text-gray-800 dark:text-gray-200">
                    @if($form && $form->gwa)
                      {{ number_format($form->gwa, 2) }}
                    @else
                      <span class="text-gray-400">Not provided</span>
                    @endif
                  </span>
                </div>
              </div>
              
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  @if($form && $form->program && $form->year_level && $form->gwa)
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-sm text-green-600 dark:text-green-400 font-medium">Complete</span>
                  @else
                    <svg class="w-5 h-5 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <span class="text-sm text-yellow-600 dark:text-yellow-400 font-medium">Incomplete</span>
                  @endif
                </div>
                <a href="{{ route('student.forms.application_form') }}#academic" 
                   class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 text-sm font-medium">
                  Edit →
                </a>
              </div>
            </div>

            <!-- Family Data Section -->
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 border border-purple-200 dark:border-purple-700 rounded-lg p-6 hover:shadow-lg transition-shadow">
              <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center mr-4">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-purple-800 dark:text-purple-200">Family Data</h3>
                  <p class="text-sm text-purple-600 dark:text-purple-300">Parents & family information</p>
                </div>
              </div>
              
              <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600 dark:text-gray-300">Father:</span>
                  <span class="font-medium text-gray-800 dark:text-gray-200">
                    @if($form && $form->father_name)
                      {{ $form->father_name }}
                    @else
                      <span class="text-gray-400">Not provided</span>
                    @endif
                  </span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600 dark:text-gray-300">Mother:</span>
                  <span class="font-medium text-gray-800 dark:text-gray-200">
                    @if($form && $form->mother_name)
                      {{ $form->mother_name }}
                    @else
                      <span class="text-gray-400">Not provided</span>
                    @endif
                  </span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600 dark:text-gray-300">Siblings:</span>
                  <span class="font-medium text-gray-800 dark:text-gray-200">
                    @if($form && $form->siblings_count)
                      {{ $form->siblings_count }} siblings
                    @else
                      <span class="text-gray-400">Not provided</span>
                    @endif
                  </span>
                </div>
              </div>
              
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  @if($form && $form->father_name && $form->mother_name)
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-sm text-green-600 dark:text-green-400 font-medium">Complete</span>
                  @else
                    <svg class="w-5 h-5 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <span class="text-sm text-yellow-600 dark:text-yellow-400 font-medium">Incomplete</span>
                  @endif
                </div>
                <a href="{{ route('student.forms.application_form') }}#family" 
                   class="text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300 text-sm font-medium">
                  Edit →
                </a>
              </div>
            </div>

            <!-- Income Information Section -->
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 border border-orange-200 dark:border-orange-700 rounded-lg p-6 hover:shadow-lg transition-shadow">
              <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center mr-4">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-orange-800 dark:text-orange-200">Income Information</h3>
                  <p class="text-sm text-orange-600 dark:text-orange-300">Financial background</p>
                </div>
              </div>
              
              <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600 dark:text-gray-300">Monthly Allowance:</span>
                  <span class="font-medium text-gray-800 dark:text-gray-200">
                    @if($form && $form->monthly_allowance)
                      ₱{{ number_format($form->monthly_allowance, 2) }}
                    @else
                      <span class="text-gray-400">Not provided</span>
                    @endif
                  </span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600 dark:text-gray-300">Family Income:</span>
                  <span class="font-medium text-gray-800 dark:text-gray-200">
                    @if($form && $form->monthly_family_income_bracket)
                      {{ $form->monthly_family_income_bracket }}
                    @else
                      <span class="text-gray-400">Not provided</span>
                    @endif
                  </span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600 dark:text-gray-300">Other Sources:</span>
                  <span class="font-medium text-gray-800 dark:text-gray-200">
                    @if($form && $form->other_income_sources)
                      {{ Str::limit($form->other_income_sources, 20) }}
                    @else
                      <span class="text-gray-400">Not provided</span>
                    @endif
                  </span>
                </div>
              </div>
              
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  @if($form && $form->monthly_allowance && $form->monthly_family_income_bracket)
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-sm text-green-600 dark:text-green-400 font-medium">Complete</span>
                  @else
                    <svg class="w-5 h-5 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <span class="text-sm text-yellow-600 dark:text-yellow-400 font-medium">Incomplete</span>
                  @endif
                </div>
                <a href="{{ route('student.forms.application_form') }}#income" 
                   class="text-orange-600 hover:text-orange-800 dark:text-orange-400 dark:hover:text-orange-300 text-sm font-medium">
                  Edit →
                </a>
              </div>
            </div>

            <!-- House Profile Section -->
            <div class="bg-gradient-to-br from-teal-50 to-teal-100 dark:from-teal-900/20 dark:to-teal-800/20 border border-teal-200 dark:border-teal-700 rounded-lg p-6 hover:shadow-lg transition-shadow">
              <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-teal-500 rounded-lg flex items-center justify-center mr-4">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-teal-800 dark:text-teal-200">House Profile</h3>
                  <p class="text-sm text-teal-600 dark:text-teal-300">Living conditions & utilities</p>
                </div>
              </div>
              
              <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600 dark:text-gray-300">House Type:</span>
                  <span class="font-medium text-gray-800 dark:text-gray-200">
                    @if($form && $form->house_type)
                      {{ $form->house_type }}
                    @else
                      <span class="text-gray-400">Not provided</span>
                    @endif
                  </span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600 dark:text-gray-300">Ownership:</span>
                  <span class="font-medium text-gray-800 dark:text-gray-200">
                    @if($form && $form->house_ownership)
                      {{ $form->house_ownership }}
                    @else
                      <span class="text-gray-400">Not provided</span>
                    @endif
                  </span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600 dark:text-gray-300">Utilities:</span>
                  <span class="font-medium text-gray-800 dark:text-gray-200">
                    @if($form && $form->electricity_source)
                      {{ $form->electricity_source }}
                    @else
                      <span class="text-gray-400">Not provided</span>
                    @endif
                  </span>
                </div>
              </div>
              
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  @if($form && $form->house_type && $form->house_ownership && $form->electricity_source)
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-sm text-green-600 dark:text-green-400 font-medium">Complete</span>
                  @else
                    <svg class="w-5 h-5 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <span class="text-sm text-yellow-600 dark:text-yellow-400 font-medium">Incomplete</span>
                  @endif
                </div>
                <a href="{{ route('student.forms.application_form') }}#house" 
                   class="text-teal-600 hover:text-teal-800 dark:text-teal-400 dark:hover:text-teal-300 text-sm font-medium">
                  Edit →
                </a>
              </div>
            </div>

            <!-- Certification Section -->
            <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 border border-red-200 dark:border-red-700 rounded-lg p-6 hover:shadow-lg transition-shadow">
              <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center mr-4">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-red-800 dark:text-red-200">Certification</h3>
                  <p class="text-sm text-red-600 dark:text-red-300">Signature & verification</p>
                </div>
              </div>
              
              <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600 dark:text-gray-300">Signature:</span>
                  <span class="font-medium text-gray-800 dark:text-gray-200">
                    @if($form && $form->student_signature)
                      {{ $form->student_signature }}
                    @else
                      <span class="text-gray-400">Not provided</span>
                    @endif
                  </span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600 dark:text-gray-300">Date Signed:</span>
                  <span class="font-medium text-gray-800 dark:text-gray-200">
                    @if($form && $form->date_signed)
                      {{ $form->date_signed->format('M d, Y') }}
                    @else
                      <span class="text-gray-400">Not provided</span>
                    @endif
                  </span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600 dark:text-gray-300">Status:</span>
                  <span class="font-medium text-gray-800 dark:text-gray-200">
                    @if($form && $form->student_signature && $form->date_signed)
                      <span class="text-green-600 dark:text-green-400">Verified</span>
                    @else
                      <span class="text-yellow-600 dark:text-yellow-400">Pending</span>
                    @endif
                  </span>
                </div>
              </div>
              
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  @if($form && $form->student_signature && $form->date_signed)
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-sm text-green-600 dark:text-green-400 font-medium">Complete</span>
                  @else
                    <svg class="w-5 h-5 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <span class="text-sm text-yellow-600 dark:text-yellow-400 font-medium">Incomplete</span>
                  @endif
                </div>
                <a href="{{ route('student.forms.application_form') }}#certification" 
                   class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium">
                  Edit →
                </a>
              </div>
            </div>

          </div>

          <!-- Action Buttons -->
          <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('student.forms.application_form') }}" 
               class="inline-flex items-center px-6 py-3 bg-bsu-red hover:bg-bsu-redDark text-white font-semibold rounded-lg shadow hover:shadow-lg transition">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
              Open Full Application Form
            </a>
            
            @if($form)
              <a href="{{ route('student.print-application') }}" 
                 class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow hover:shadow-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print Application
              </a>
            @endif
          </div>
        </div>
      </div>
    </div>
    
    @include('student.partials.tabs.applications')
    @include('student.application_tracking')
    @include('student.partials.tabs.announcements')
    @include('student.partials.tabs.account')
  </main>

  <!-- Fix Safari Back Cache Bug -->
  <script>
    window.addEventListener("pageshow", function (event) {
      if (event.persisted) {
        window.location.reload();
      }
    });
    
    // Handle scholarships dropdown state
    document.addEventListener('alpine:init', () => {
      Alpine.data('dashboard', () => ({
        init() {
          // Open scholarships dropdown if scholarships tab is active
          if (this.tab === 'scholarships') {
            this.scholarshipsDropdownOpen = true;
          }
        }
      }));
    });
  </script>
</body>
</html>
