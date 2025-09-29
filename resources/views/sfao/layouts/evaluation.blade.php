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
<html lang="en" :class="{ 'dark': darkMode }" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SFAO Document Evaluation</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Batangas_State_Logo.png') }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    
    <!-- Tailwind Custom Config -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'bsu-red': '#8B0000',
                        'bsu-gold': '#FFD700',
                        'bsu-blue': '#1E3A8A',
                        'bsu-green': '#059669'
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo and Title -->
                <div class="flex items-center">
                    <img src="{{ asset('images/Batangas_State_Logo.png') }}" alt="BSU Logo" class="h-8 w-8 mr-3">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">SFAO Document Evaluation</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Student Financial Assistance Office</p>
                    </div>
                </div>
                
                <!-- User Info and Actions -->
                <div class="flex items-center space-x-4">
                    <!-- Dark Mode Toggle -->
                    <button @click="darkMode = !darkMode" 
                            class="p-2 text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400">
                        <svg x-show="!darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <svg x-show="darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </button>
                    
                    <!-- User Profile -->
                    <div class="flex items-center space-x-3">
                        <div class="h-8 w-8 rounded-full bg-bsu-red flex items-center justify-center">
                            <span class="text-sm font-bold text-white">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </span>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">SFAO Admin</p>
                        </div>
                    </div>
                    
                    <!-- Back to Dashboard -->
                    <a href="{{ route('sfao.dashboard') }}" 
                       class="inline-flex items-center px-4 py-2 bg-bsu-red border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        ← Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                <p>&copy; {{ date('Y') }} Batangas State University - Student Financial Assistance Office</p>
            </div>
        </div>
    </footer>
</body>
</html>
