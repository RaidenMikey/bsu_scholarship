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
    darkMode: localStorage.getItem('darkMode') === 'true'
  }"
  x-init="
    $watch('darkMode', val => localStorage.setItem('darkMode', val));
    $watch('tab', val => localStorage.setItem('activeTab', val));
  ">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="Cache-Control" content="no-store" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="0" />
  <title>@yield('title', 'Student Dashboard')</title>
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

  @stack('styles')
</head>

<body class="bg-gray-100 dark:bg-gray-900 dark:text-white min-h-screen font-sans">
  <!-- Mobile Overlay -->
  <div class="md:hidden fixed inset-0 bg-black bg-opacity-50 z-40"
       x-show="sidebarOpen"
       x-transition.opacity
       @click="sidebarOpen = false"
       x-cloak></div>

  <!-- Sidebar -->
  @include('student.components.navigation.sidebar', ['user' => $user])

  <!-- Mobile Top Bar -->
  @include('student.components.navigation.header')

  <!-- Main Content -->
  <main class="md:ml-64 p-4 md:p-8 min-h-screen bg-white dark:bg-gray-900 transition-colors duration-300">
    <!-- Toasts for success and errors -->
    @include('student.components.ui.toasts')

    @yield('content')
  </main>

  <!-- Fix Safari Back Cache Bug -->
  <script>
    window.addEventListener("pageshow", function (event) {
      if (event.persisted) {
        window.location.reload();
      }
    });
  </script>

  @stack('scripts')
</body>
</html>

