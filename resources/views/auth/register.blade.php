@extends('auth.layout')

@section('title', 'Create Account')
@section('heading', 'Join Spartan Scholarship')
@section('subheading', 'Create your account to start your scholarship journey')
@section('container_width', 'max-w-4xl')

@section('content')
<div x-data="signupForm()">
  <!-- Progress Bar -->
  <div class="mb-8">
    <div class="flex justify-between mb-2">
      <template x-for="(step, index) in steps" :key="index">
        <div class="flex flex-col items-center w-1/4">
          <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-colors duration-300"
               :class="currentStep > index + 1 ? 'bg-green-500 text-white' : (currentStep === index + 1 ? 'bg-bsu-red text-white' : 'bg-gray-200 text-gray-500')">
            <span x-show="currentStep <= index + 1" x-text="index + 1"></span>
            <svg x-show="currentStep > index + 1" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          <span class="text-xs mt-1 text-center hidden sm:block" 
                :class="currentStep === index + 1 ? 'text-bsu-red font-semibold' : 'text-gray-500'"
                x-text="step"></span>
        </div>
      </template>
    </div>
    <div class="relative pt-1">
      <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-gray-200">
        <div :style="`width: ${(currentStep / steps.length) * 100}%`" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-bsu-red transition-all duration-500"></div>
      </div>
    </div>
  </div>

  <form method="POST" action="{{ url('/register') }}" id="registerForm" autocomplete="off" class="space-y-4" @submit.prevent="submitForm">
    @csrf
    <input type="hidden" name="role" value="student">

    <!-- Step 1: Personal Info -->
    <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b pb-2">Personal Information</h3>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-auth.input type="text" label="First Name" name="first_name" placeholder="First Name" required x-model="formData.first_name" />
        <x-auth.input type="text" label="Middle Name (Optional)" name="middle_name" placeholder="Middle Name" x-model="formData.middle_name" />
      </div>
      
      <x-auth.input type="text" label="Last Name" name="last_name" placeholder="Last Name" required x-model="formData.last_name" />
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-auth.input type="date" label="Birthdate" name="birthdate" required x-model="formData.birthdate" />
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sex <span class="text-red-500">*</span></label>
          <select name="sex" required x-model="formData.sex" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
            <option value="" disabled selected>Select Sex</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Step 2: Contact Info -->
    <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b pb-2">Contact Information</h3>
      
      <x-auth.input type="email" label="BSU Email" name="email" placeholder="example@g.batstate-u.edu.ph" pattern="^[a-zA-Z0-9._%+-]+@g\.batstate-u\.edu\.ph$" required x-model="formData.email">
        <p class="mt-1 text-xs text-gray-500">Must end with @g.batstate-u.edu.ph</p>
      </x-auth.input>
      
      <x-auth.input type="text" label="Contact Number" name="contact_number" placeholder="09123456789" required x-model="formData.contact_number" />
    </div>

    <!-- Step 3: Academic Info -->
    <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b pb-2">Academic Information</h3>
      
      <x-auth.input type="text" label="SR Code" name="sr_code" placeholder="XX-XXXXX" required x-model="formData.sr_code" />
      
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Education Level <span class="text-red-500">*</span></label>
        <select name="education_level" required x-model="formData.education_level" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
          <option value="" disabled selected>Select Level</option>
          <option value="Undergraduate">Undergraduate</option>
          <option value="Graduate School">Graduate School</option>
          <option value="Integrated School">Integrated School</option>
        </select>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">College / Department <span class="text-red-500">*</span></label>
        <select name="college" required x-model="formData.college" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
          <option value="" disabled selected>Select College</option>
          <option value="CICS">CICS</option>
          <option value="CTE">CTE</option>
          <option value="CABEIHM">CABEIHM</option>
          <option value="CAS">CAS</option>
        </select>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Program <span class="text-red-500">*</span></label>
        <select name="program" required x-model="formData.program" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
          <option value="" disabled selected>Select Program</option>
          @foreach (['BS Computer Science', 'BS Information Technology', 'BS Computer Engineering', 'BS Electronics Engineering', 'BS Civil Engineering', 'BS Mechanical Engineering', 'BS Electrical Engineering', 'BS Industrial Engineering', 'BS Accountancy', 'BS Business Administration', 'BS Tourism Management', 'BS Hospitality Management', 'BS Psychology', 'BS Education', 'BS Nursing', 'BS Medical Technology', 'BS Pharmacy', 'BS Biology', 'BS Chemistry', 'BS Mathematics', 'BS Physics', 'BS Environmental Science', 'BS Agriculture', 'BS Fisheries', 'BS Forestry', 'BS Architecture', 'BS Interior Design', 'BS Fine Arts', 'BS Communication', 'BS Social Work', 'BS Criminology', 'BS Political Science', 'BS History', 'BS Literature', 'BS Philosophy', 'BS Economics', 'BS Sociology', 'BS Anthropology'] as $program)
            <option value="{{ $program }}">{{ $program }}</option>
          @endforeach
        </select>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Year Level <span class="text-red-500">*</span></label>
        <select name="year_level" required x-model="formData.year_level" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
          <option value="" disabled selected>Select Year Level</option>
          <option value="1st Year">1st Year</option>
          <option value="2nd Year">2nd Year</option>
          <option value="3rd Year">3rd Year</option>
          <option value="4th Year">4th Year</option>
        </select>
      </div>
      
      <div class="mb-4">
        <label for="campus_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Campus <span class="text-red-500">*</span></label>
        <select id="campus_id" name="campus_id" required x-model="formData.campus_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
          <option value="" disabled selected>Select a campus</option>
          @foreach($campuses as $campus)
            <option value="{{ $campus->id }}">{{ $campus->name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <!-- Step 4: Scholarship Verification -->
    <div x-show="currentStep === 4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b pb-2">Scholarship Verification</h3>
      
      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Do you currently have an existing scholarship?</label>
        <div class="flex gap-4">
          <label class="inline-flex items-center">
            <input type="radio" name="has_scholarship" value="yes" x-model="formData.has_scholarship" class="form-radio text-red-600 focus:ring-red-500">
            <span class="ml-2 text-gray-700 dark:text-gray-300">Yes</span>
          </label>
          <label class="inline-flex items-center">
            <input type="radio" name="has_scholarship" value="no" x-model="formData.has_scholarship" class="form-radio text-red-600 focus:ring-red-500">
            <span class="ml-2 text-gray-700 dark:text-gray-300">No</span>
          </label>
        </div>
      </div>

      <div x-show="formData.has_scholarship === 'yes'" class="mt-4">
        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Select Scholarships:</h4>
        <div class="max-h-60 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-lg p-2 bg-white dark:bg-gray-800">
          @foreach($scholarships as $scholarship)
            <label class="flex items-center p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded cursor-pointer transition-colors">
              <input type="checkbox" value="{{ $scholarship->id }}" x-model="formData.selected_scholarships" class="form-checkbox text-red-600 focus:ring-red-500 rounded border-gray-300 dark:border-gray-500 dark:bg-gray-700">
              <span class="ml-2 text-sm text-gray-700 dark:text-gray-200">{{ $scholarship->scholarship_name }}</span>
            </label>
          @endforeach
        </div>
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
            Please select all scholarships you are currently enrolled in.
        </p>
      </div>
    </div>

    <!-- Step 5: Credentials -->
    <div x-show="currentStep === 5" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b pb-2">Credentials</h3>
      
      <x-auth.password-input label="Password" name="password" placeholder="Create a strong password" autocomplete="new-password" showStrength="true" required x-model="formData.password" />
      <x-auth.password-input label="Confirm Password" name="password_confirmation" placeholder="Confirm your password" autocomplete="new-password" required x-model="formData.password_confirmation" />
      
      <div class="mb-6 mt-4">
        <label class="inline-flex items-start text-sm text-gray-700 dark:text-gray-300">
          <input type="checkbox" name="terms" required x-model="formData.terms" class="mt-1 rounded text-red-600 focus:ring-red-500 dark:bg-gray-700">
          <span class="ml-2">
            I agree to the <a href="#" class="text-red-600 hover:underline dark:text-red-400">Terms of Service</a> and <a href="#" class="text-red-600 hover:underline dark:text-red-400">Privacy Policy</a>
          </span>
        </label>
      </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="flex justify-between mt-6">
      <button type="button" x-show="currentStep > 1" @click="prevStep" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">Back</button>
      <div class="flex-1"></div> <!-- Spacer -->
      <button type="button" x-show="currentStep < steps.length" @click="nextStep" class="px-4 py-2 bg-bsu-red text-white rounded-lg hover:bg-red-700 transition">Next</button>
      <button type="submit" x-show="currentStep === steps.length" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center" :disabled="isSubmitting">
        <span x-show="!isSubmitting">Create Account</span>
        <span x-show="isSubmitting" class="flex items-center">
          <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Processing...
        </span>
      </button>
    </div>
  </form>

  <div class="mt-4 text-center">
    <a href="{{ url('/login') }}" class="text-sm text-gray-600 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition-colors duration-200">Already have an account? Sign In</a>
  </div>
</div>
@endsection

@section('footer')
<a href="{{ url('/') }}" class="flex items-center gap-1 text-red-600 hover:underline text-sm dark:text-red-400">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
    </svg>
    Back to Homepage
</a>
@endsection

@push('scripts')
<script>
  function signupForm() {
    return {
      currentStep: 1,
      steps: ['Personal Info', 'Contact Info', 'Academic Info', 'Scholarship Verification', 'Credentials'],
      formData: {
        first_name: '',
        middle_name: '',
        last_name: '',
        birthdate: '',
        sex: '',
        email: '',
        contact_number: '',
        sr_code: '',
        education_level: '',
        college: '',
        program: '',
        year_level: '',
        campus_id: '',
        has_scholarship: '',
        selected_scholarships: [],
        password: '',
        password_confirmation: '',
        terms: false
      },
      isSubmitting: false,
      
      nextStep() {
        if (this.validateStep(this.currentStep)) {
          this.currentStep++;
        }
      },
      
      prevStep() {
        if (this.currentStep > 1) {
          this.currentStep--;
        }
      },
      
      validateStep(step) {
        // Basic validation logic
        if (step === 1) {
          if (!this.formData.first_name || !this.formData.last_name || !this.formData.birthdate || !this.formData.sex) {
            alert('Please fill in all required fields.');
            return false;
          }
        } else if (step === 2) {
          if (!this.formData.email || !this.formData.contact_number) {
            alert('Please fill in all required fields.');
            return false;
          }
          if (!this.formData.email.endsWith('@g.batstate-u.edu.ph')) {
            alert('Email must end with @g.batstate-u.edu.ph');
            return false;
          }
        } else if (step === 3) {
          if (!this.formData.sr_code || !this.formData.education_level || !this.formData.college || !this.formData.program || !this.formData.year_level || !this.formData.campus_id) {
            alert('Please fill in all required fields.');
            return false;
          }
        } else if (step === 4) {
          if (!this.formData.has_scholarship) {
            alert('Please answer the scholarship verification question.');
            return false;
          }
          if (this.formData.has_scholarship === 'yes' && this.formData.selected_scholarships.length === 0) {
            alert('Please select at least one scholarship.');
            return false;
          }
        } else if (step === 5) {
          if (!this.formData.password || !this.formData.password_confirmation) {
            alert('Please enter a password.');
            return false;
          }
          if (this.formData.password !== this.formData.password_confirmation) {
            alert('Passwords do not match.');
            return false;
          }
          if (!this.formData.terms) {
            alert('You must agree to the terms and privacy policy.');
            return false;
          }
        }
        return true;
      },
      
      init() {
        // Check if page was reloaded
        const navEntry = performance.getEntriesByType("navigation")[0];
        const isReload = navEntry ? navEntry.type === 'reload' : (performance.navigation.type === 1);

        if (isReload) {
          const savedData = sessionStorage.getItem('signupFormData');
          if (savedData) {
            this.formData = JSON.parse(savedData);
            // Ensure selected_scholarships is an array
            if (!Array.isArray(this.formData.selected_scholarships)) {
                this.formData.selected_scholarships = [];
            }
            // Restore current step if saved (optional, but good for UX)
            const savedStep = sessionStorage.getItem('signupCurrentStep');
            if (savedStep) {
              this.currentStep = parseInt(savedStep);
            }
          }
        } else {
          // If not a reload (e.g. first visit or navigated back), clear storage
          sessionStorage.removeItem('signupFormData');
          sessionStorage.removeItem('signupCurrentStep');
        }

        // Watch for changes and save to sessionStorage
        this.$watch('formData', (value) => {
          sessionStorage.setItem('signupFormData', JSON.stringify(value));
        });
        
        this.$watch('currentStep', (value) => {
          sessionStorage.setItem('signupCurrentStep', value);
        });
      },

      submitForm() {
        if (this.validateStep(5)) {
          this.isSubmitting = true;
          
          // Create hidden inputs for selected scholarships before submitting
          const form = document.getElementById('registerForm');
          
          // Remove any existing hidden inputs for selected_scholarships to avoid duplicates
          const existingInputs = form.querySelectorAll('input[name="selected_scholarships[]"]');
          existingInputs.forEach(input => input.remove());

          if (this.formData.has_scholarship === 'yes') {
              this.formData.selected_scholarships.forEach(id => {
                  const input = document.createElement('input');
                  input.type = 'hidden';
                  input.name = 'selected_scholarships[]';
                  input.value = id;
                  form.appendChild(input);
              });
          }

          // Clear storage on submit
          sessionStorage.removeItem('signupFormData');
          sessionStorage.removeItem('signupCurrentStep');
          form.submit();
        }
      }
    }
  }
</script>
@endpush
