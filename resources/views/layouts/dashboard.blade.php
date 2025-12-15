@props(['user', 'title' => 'Dashboard'])

<!DOCTYPE html>
<html lang="en" 
    :class="{ 'dark': darkMode }" 
    x-data="{ 
        sidebarOpen: localStorage.getItem('sidebarOpen') === 'true', 
        rightSidebarOpen: localStorage.getItem('rightSidebarOpen') === 'true', 
        darkMode: localStorage.getItem('darkMode_{{ $user->id }}') === 'true',
        isDesktop: window.innerWidth >= 768,
        showLogoutModal: false
    }"
    x-init="
        $watch('darkMode', val => localStorage.setItem('darkMode_{{ $user->id }}', val));
        $watch('sidebarOpen', val => localStorage.setItem('sidebarOpen', val));
        $watch('rightSidebarOpen', val => localStorage.setItem('rightSidebarOpen', val));
    "
    @resize.window="isDesktop = window.innerWidth >= 768"
>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/sfao-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <!-- Styles Stack -->
    @stack('styles')

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/sfao-script.js') }}"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    
    <script>
        // Immediately apply dark mode preference to prevent FOUC
        if (localStorage.getItem('darkMode_{{ $user->id }}') === 'true' || (!('darkMode_{{ $user->id }}' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
          document.documentElement.classList.add('dark');
        } else {
          document.documentElement.classList.remove('dark');
        }
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
        /* Custom scrollbar styling - minimized and subtle */
        nav::-webkit-scrollbar { width: 6px; }
        nav::-webkit-scrollbar-track { background: transparent; }
        nav::-webkit-scrollbar-thumb { background: rgba(156, 163, 175, 0.3); border-radius: 3px; }
        nav::-webkit-scrollbar-thumb:hover { background: rgba(156, 163, 175, 0.5); }
        .dark nav::-webkit-scrollbar-thumb { background: rgba(156, 163, 175, 0.2); }
        .dark nav::-webkit-scrollbar-thumb:hover { background: rgba(156, 163, 175, 0.4); }
    </style>
    
    <!-- Scripts Stack -->
    @stack('scripts')
</head>

<body class="bg-gray-100 dark:bg-gray-900 dark:text-white min-h-screen font-sans transition-colors duration-300">

    <!-- Global Sidebar Wrapper -->
    <x-layout.sidebar-wrapper :user="$user">
        <!-- Sidebar Navigation Content -->
        @yield('sidebar-menu')
    </x-layout.sidebar-wrapper>

    <!-- Global Navbar -->
    <!-- Note: We use the layout navbar but allow yielding customized settings if needed, 
         or just use standard. The page usually defines the navbar title. 
         Actually, the navbar is usually INSIDE the main content area in your design (floating or top),
         BUT in the Focused layout it was at the top. 
         In Dashboard views (SFAO/Student), the navbar is part of the "Main Content" column 
         to be pushed by the sidebar. -->
         
    <!-- Global Navbar -->
    @yield('navbar')
         
    <!-- Main Content Wrapper -->
    <main class="min-h-screen transition-all duration-300"
          :class="{ 'md:ml-64': sidebarOpen, 'md:mr-64': rightSidebarOpen }"
          :style="isDesktop && rightSidebarOpen ? 'margin-right: 16rem;' : ''">
          
        <!-- Scrollable Content Area -->
        <div class="p-4 md:p-8">
            @yield('content')
        </div>
    </main>
    
    <!-- Global Modals -->
    <x-modals.logout />
    
    <!-- Page Specific Modals -->
    @yield('modals')

</body>
</html>
