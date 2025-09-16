@extends('auth.layout')

@section('title', 'Create Account')
@section('heading', 'Join Spartan Scholarship')
@section('subheading', 'Create your account to start your scholarship journey')

@section('content')
<form method="POST" action="{{ url('/register') }}" id="registerForm" autocomplete="off" class="space-y-4">
  @csrf

  {{-- Full Name --}}
  <x-auth.input 
    type="text"
    label="Full Name"
    name="name"
    placeholder="Enter your full name"
    required
  />

  {{-- Email --}}
  <x-auth.input 
    type="email"
    label="School Email"
    name="email"
    placeholder="example@g.batstate-u.edu.ph"
    pattern="^[a-zA-Z0-9._%+-]+@g\.batstate-u\.edu\.ph$"
    ariaDescribedby="email-help"
    required
  >
    <p id="email-help" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
      Must be a valid BatState-U email address
    </p>
  </x-auth.input>

  {{-- Password with Strength Indicator --}}
  <x-auth.password-input 
    label="Password"
    name="password"
    placeholder="Create a strong password"
    autocomplete="new-password"
    showStrength="true"
    required
  />

  {{-- Confirm Password --}}
  <x-auth.password-input 
    label="Confirm Password"
    name="password_confirmation"
    placeholder="Confirm your password"
    autocomplete="new-password"
    required
  />

  {{-- Role (Hidden - Default to Student) --}}
  <input type="hidden" name="role" value="student">

  {{-- Branch Selection --}}
  <div class="mb-6">
    <label for="branch_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
      Branch <span class="text-red-500">*</span>
    </label>
    <select id="branch_id" name="branch_id" required
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors duration-200">
      <option value="" disabled selected>Select a branch</option>
      @foreach(\App\Models\Branch::all() as $branch)
        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
      @endforeach
    </select>
  </div>

  {{-- Terms and Conditions --}}
  <div class="mb-6">
    <label class="inline-flex items-start text-sm text-gray-700 dark:text-gray-300">
      <input type="checkbox" name="terms" required class="mt-1 rounded text-red-600 focus:ring-red-500 dark:bg-gray-700">
      <span class="ml-2">
        I agree to the 
        <a href="#" class="text-red-600 hover:underline dark:text-red-400">Terms of Service</a> 
        and 
        <a href="#" class="text-red-600 hover:underline dark:text-red-400">Privacy Policy</a>
      </span>
    </label>
  </div>

  {{-- Submit Button --}}
  <button type="submit" id="registerButton"
          class="w-full bg-red-600 hover:bg-red-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white py-2.5 rounded-lg font-semibold transition-all duration-200 text-sm">
    <span id="registerText">Create Account</span>
    <span id="registerSpinner" class="hidden">
      <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      Creating Account...
    </span>
  </button>

  {{-- Login Link --}}
  <div class="mt-4 text-center">
    <a href="{{ url('/login') }}" 
       class="text-sm text-gray-600 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition-colors duration-200">
      Already have an account? Sign In
    </a>
  </div>
</form>
@endsection

@section('footer')
<a href="{{ url('/') }}" class="text-red-600 hover:underline text-sm dark:text-red-400">
  ← Back to Homepage
</a>
@endsection

@push('scripts')
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

  // Password confirmation validation
  document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (confirmPassword && password !== confirmPassword) {
      this.setCustomValidity('Passwords do not match');
    } else {
      this.setCustomValidity('');
    }
  });
</script>
@endpush
