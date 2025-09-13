<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Account | Spartan Scholarship</title>
  <link rel="icon" type="image/png" href="{{ asset('images/Batangas_State_Logo.png') }}">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/alpinejs" defer></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

  <div class="bg-white p-8 px-4 rounded-lg shadow-lg w-full max-w-md">
    <div class="flex flex-col items-center mb-6">
      <img src="{{ asset('images/Batangas_State_Logo.png') }}" alt="Logo" class="h-10 sm:h-12 mb-2">
      <h1 class="text-2xl font-bold text-gray-800">Create Account</h1>
    </div>

    <form method="POST" action="{{ url('/register') }}" id="registerForm" autocomplete="off">
      @csrf

      {{-- Show errors --}}
      @if($errors->any())
        <div x-data="{ show: true }" x-show="show" x-transition
             class="text-red-600 text-sm mb-4 bg-red-100 border border-red-400 p-3 rounded" role="alert">
          <div class="flex items-center">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            {{ $errors->first() }}
          </div>
        </div>
      @endif

      {{-- Name --}}
      <div class="mb-4">
        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
        <input type="text" id="name" name="name" required
               placeholder="Enter your full name"
               class="mt-1 w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
      </div>

      {{-- Email --}}
      <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700">School Email</label>
        <input type="email" id="email" name="email" required
               pattern="^[a-zA-Z0-9._%+-]+@g\.batstate-u\.edu\.ph$"
               placeholder="example@g.batstate-u.edu.ph"
               aria-describedby="email-help"
               class="mt-1 w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
        <p id="email-help" class="mt-1 text-xs text-gray-500">Must be a valid BatState-U email address</p>
      </div>

      {{-- Password --}}
      <div class="mb-4" x-data="{ showPassword: false }">
        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
        <div class="relative">
          <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required
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

      {{-- Confirm Password --}}
      <div class="mb-4" x-data="{ showConfirm: false }">
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
        <div class="relative">
          <input :type="showConfirm ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" required
                 aria-describedby="confirm-toggle"
                 class="mt-1 w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 pr-10">
          <button type="button" @click="showConfirm = !showConfirm"
                  id="confirm-toggle"
                  aria-label="Toggle confirm password visibility"
                  class="absolute inset-y-0 right-0 px-3 text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-red-500 rounded">
            <span x-text="showConfirm ? 'Hide' : 'Show'" aria-hidden="true"></span>
          </button>
        </div>
      </div>

      {{-- Role (Hidden - Default to Student) --}}
      <input type="hidden" name="role" value="student">

      {{-- Branch --}}
      <div class="mb-6">
        <label for="branch_id" class="block text-sm font-medium text-gray-700">Branch</label>
        <select id="branch_id" name="branch_id" required
                class="mt-1 w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
          <option value="" disabled selected>Select a branch</option>
          @foreach(\App\Models\Branch::all() as $branch)
              <option value="{{ $branch->id }}">{{ $branch->name }}</option>
          @endforeach
        </select>
      </div>

      {{-- Submit --}}
      <button type="submit" id="registerButton"
              class="w-full bg-red-600 hover:bg-red-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white py-2 rounded-md font-semibold transition text-sm">
        <span id="registerText">Create Account</span>
        <span id="registerSpinner" class="hidden">
          <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Creating Account...
        </span>
      </button>
    </form>

    <div class="mt-6 text-center">
      <a href="{{ url('/login') }}" class="text-red-600 hover:underline text-sm">← Back to Login</a>
    </div>
  </div>

  {{-- Form handling --}}
  <script>
    // Handle form submission with loading state
    document.getElementById('registerForm').addEventListener('submit', function() {
      const button = document.getElementById('registerButton');
      const text = document.getElementById('registerText');
      const spinner = document.getElementById('registerSpinner');
      
      button.disabled = true;
      text.classList.add('hidden');
      spinner.classList.remove('hidden');
    });
  </script>
</body>
</html>
