<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Authentication') | Spartan Scholarship</title>
  <link rel="icon" href="{{ asset('favicon.ico') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])


  @stack('styles')
</head>

<body class="bg-gradient-to-br from-red-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 flex items-center justify-center min-h-screen p-4">
  <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-xl w-full @yield('container_width', 'max-w-md') border border-gray-200 dark:border-gray-700">
    <div class="flex flex-col items-center mb-6">
      <img src="{{ asset('images/Batangas_State_Logo.png') }}" alt="Batangas State University Logo" class="h-12 sm:h-14 mb-3">
      <h1 class="text-2xl font-bold text-gray-800 dark:text-white">@yield('heading')</h1>
      @hasSection('subheading')
        <p class="text-gray-600 dark:text-gray-300 text-center text-sm mt-2">@yield('subheading')</p>
      @endif
    </div>

    {{-- Flash Messages --}}
    @if(session('registered'))
      <x-auth.alert type="success" :message="session('registered')" />
    @endif
    @if(session('verified'))
      <x-auth.alert type="success" :message="'Email verified successfully! You can now log in.'" />
    @endif
    @if(session('logged_out'))
      <x-auth.alert type="info" :message="'You have been logged out successfully.'" />
    @endif
    @if(session('error'))
      <x-auth.alert type="error" :message="session('error')" />
    @endif
    @if(session('session_expired'))
      <x-auth.alert type="warning" :message="session('session_expired')" />
    @endif
    @if(session('message'))
      <x-auth.alert type="info" :message="session('message')" />
    @endif

    {{-- Form Errors --}}
    @if($errors->any())
      <x-auth.alert type="error" :message="$errors->first()" />
      
      @if(str_contains($errors->first(), 'email is not verified'))
        <div class="mt-2 text-center">
            <form method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <input type="hidden" name="email" value="{{ old('email') }}">
                <button type="submit" class="text-sm text-red-600 hover:underline font-medium">
                    Resend Verification Email
                </button>
            </form>
        </div>
      @endif
    @endif

    @yield('content')

    @hasSection('footer')
      <div class="mt-6 text-center">
        @yield('footer')
      </div>
    @endif
  </div>

  @stack('scripts')
</body>
</html>
