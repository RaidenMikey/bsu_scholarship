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
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Scholarship Application') - BSU Scholarship System</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    @stack('head')
</head>
<body class="h-full bg-gray-50">
    <!-- Navigation Header -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo and Title -->
                <div class="flex items-center">
                    <a href="{{ route('student.dashboard') }}" class="flex items-center space-x-3">
                        <img src="{{ asset('images/Batangas_State_Logo.png') }}" alt="BSU Logo" class="h-8 w-8">
                        <div>
                            <h1 class="text-lg font-semibold text-gray-900">BSU Scholarship System</h1>
                            <p class="text-xs text-gray-500">Scholarship Application</p>
                        </div>
                    </a>
                </div>
                
                <!-- User Info -->
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                        <p class="text-xs text-gray-500">Student</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($user->profile_picture)
                            <img src="{{ asset('storage/profile_pictures/' . $user->profile_picture) }}" 
                                 alt="Profile" class="h-8 w-8 rounded-full object-cover">
                        @else
                            <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-600">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <p class="text-sm text-gray-500">
                    © {{ date('Y') }} Batangas State University. All rights reserved.
                </p>
                <div class="flex space-x-4">
                    <a href="{{ route('student.dashboard') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        ← Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </footer>

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
