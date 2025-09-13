<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Spartan Scholarship</title>
  <link rel="icon" type="image/png" href="{{ asset('images/Batangas_State_Logo.png') }}">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/alpinejs" defer></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

  <div class="bg-white p-8 px-4 rounded-lg shadow-lg w-full max-w-sm">
    <div class="flex flex-col items-center mb-6">
      <img src="{{ asset('images/Batangas_State_Logo.png') }}" alt="Logo" class="h-10 sm:h-12 mb-2">
      <h1 class="text-2xl font-bold text-gray-800">Login</h1>
    </div>

    {{-- Flash Messages --}}
    @if(session('registered'))
      <div class="text-green-600 mb-4 bg-green-100 border border-green-400 p-3 rounded text-sm" role="alert">
        <div class="flex items-center">
          <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
          </svg>
          {{ session('registered') }}
        </div>
      </div>
    @endif
    @if(session('error'))
      <div class="text-red-600 mb-4 bg-red-100 border border-red-400 p-3 rounded text-sm" role="alert">
        <div class="flex items-center">
          <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
          </svg>
          {{ session('error') }}
        </div>
      </div>
    @endif
    @if(session('session_expired'))
      <div class="text-orange-600 mb-4 bg-orange-100 border border-orange-400 p-3 rounded text-sm" role="alert">
        <div class="flex items-center">
          <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
          </svg>
          {{ session('session_expired') }}
        </div>
      </div>
    @endif

    <form method="POST" action="{{ url('/login') }}" id="loginForm" autocomplete="off">
      @csrf

      {{-- Email --}}
      <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700">School Email</label>
        <input type="email" id="email" name="email" required
               pattern="^[a-zA-Z0-9._%+-]+@g\.batstate-u\.edu\.ph$"
               placeholder="example@g.batstate-u.edu.ph"
               autocomplete="off"
               aria-describedby="email-help"
               class="mt-1 w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
        <p id="email-help" class="mt-1 text-xs text-gray-500">Must be a valid BatState-U email address</p>
      </div>

      {{-- Password --}}
      <div class="mb-4" x-data="{ showPassword: false }">
        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
        <div class="relative">
          <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required
                 autocomplete="new-password"
                 aria-describedby="password-toggle"
                 class="mt-1 w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 pr-10">
          <button type="button" @click="showPassword = !showPassword"
                  id="password-toggle"
                  aria-label="Toggle password visibility"
                  class="absolute inset-y-0 right-0 px-3 text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-red-500 rounded">
            <span x-text="showPassword ? 'Hide' : 'Show'" aria-hidden="true"></span>
          </button>
        </div>
      </div>

      {{-- Branch Selection --}}
      <div class="mb-4">
        <label for="branch_id" class="block text-sm font-medium text-gray-700">Select Branch</label>
        <select id="branch_id" name="branch_id" required
                class="mt-1 w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
          <option value="" disabled selected>-- Choose your branch --</option>
          @foreach(\App\Models\Branch::all() as $branch)
            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
          @endforeach
        </select>
      </div>

      {{-- Remember Me --}}
      <div class="flex items-center justify-between mb-6">
        <label class="inline-flex items-center text-sm text-gray-700">
          <input type="checkbox" name="remember" class="rounded text-red-600 focus:ring-red-500">
          <span class="ml-2">Remember me</span>
        </label>
        <a href="#" class="text-sm text-red-600 hover:underline">Forgot password?</a>
      </div>

      {{-- Submit --}}
      <button type="submit" id="loginButton"
              class="w-full bg-red-600 hover:bg-red-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white py-2 rounded-md font-semibold transition text-sm truncate">
        <span id="loginText">Log In</span>
        <span id="loginSpinner" class="hidden">
          <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Logging in...
        </span>
      </button>

      {{-- New Applicant --}}
      <div class="mt-4 text-center">
        <a href="{{ route('register') }}" 
           class="block text-center text-sm text-gray-500 hover:text-red-600 mt-4">
          New Applicant? Create Account
        </a>
      </div>
    </form>

    <div class="mt-6 text-center">
      <a href="{{ url('/') }}" class="text-red-600 hover:underline text-sm">← Back to Homepage</a>
    </div>
  </div>

  {{-- Form handling and navigation --}}
  <script>
    // Handle form submission with loading state
    document.getElementById('loginForm').addEventListener('submit', function() {
      const button = document.getElementById('loginButton');
      const text = document.getElementById('loginText');
      const spinner = document.getElementById('loginSpinner');
      
      button.disabled = true;
      text.classList.add('hidden');
      spinner.classList.remove('hidden');
    });

    // Clear form fields on back navigation
    window.addEventListener("pageshow", function (event) {
      if (event.persisted) {
        // Clear form and reload page for fresh state
        document.getElementById("loginForm").reset();
        window.location.reload();
      } else if (performance.getEntriesByType("navigation")[0].type === "back_forward") {
        // Just clear form for back navigation
        document.getElementById("loginForm").reset();
      }
    });
  </script>
</body>
</html>
