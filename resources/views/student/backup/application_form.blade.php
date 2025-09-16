@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;

// Check if user is logged in
if (!Session::has('user_id')) {
    header('Location: ' . route('login'));
    exit;
}

// Get logged-in user
$user = User::find(session('user_id'));

// If user not found, clear session and redirect
if (!$user) {
    Session::flush();
    header('Location: ' . route('login'));
    exit;
}

// Prevent browser caching to block back button after logout
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1
header('Pragma: no-cache'); // HTTP 1.0
header('Expires: 0'); // Proxies
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Scholarship Application Form</title>
  <link rel="icon" type="image/png" href="{{ asset('images/Batangas_State_Logo.png') }}">

  <script src="https://cdn.tailwindcss.com"></script>
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
    }
  </script>
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-white font-sans py-10 px-4">
  <div class="max-w-5xl mx-auto bg-white dark:bg-gray-800 shadow-md rounded-xl p-8">
    
    <a href="{{ url('/student') }}"
      class="inline-block mb-6 text-sm text-white bg-bsu-red hover:bg-bsu-redDark px-4 py-2 rounded transition">
      ← Back to Dashboard
    </a>

    <h1 class="text-3xl font-bold text-center text-bsu-red dark:text-bsu-light mb-8">
      Application Form for Student Scholarship / Financial Assistance
    </h1>

    <form action="{{ url('/student/submit-application') }}" method="POST" id="mainForm" class="space-y-10">
      @csrf

      <!-- Personal Data Section -->
      <section>
        <h2 class="text-3xl font-bold text-red-800 mb-6 border-b-2 border-red-700 pb-2">Personal Data</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
              <label class="block font-semibold text-gray-700 mb-1">Last Name</label>
              <input type="text" name="last_name" value="{{ old('last_name', $existingApplication->last_name ?? '') }}" class="w-full border border-red-500 px-3 py-2 rounded-md">
            </div>
            <div>
              <label class="block font-semibold text-gray-700 mb-1">First Name</label>
              <input type="text" name="first_name" value="{{ old('first_name', $existingApplication->first_name ?? '') }}" class="w-full border border-red-500 px-3 py-2 rounded-md">
            </div>
            <div>
              <label class="block font-semibold text-gray-700 mb-1">Middle Name</label>
              <input type="text" name="middle_name" value="{{ old('middle_name', $existingApplication->middle_name ?? '') }}" class="w-full border border-red-500 px-3 py-2 rounded-md">
            </div>
          </div>

          <div class="md:col-span-2 grid grid-cols-7 gap-4">
            <div class="col-span-2">
              <label class="block font-semibold text-gray-700 mb-1">Street / Barangay</label>
              <input type="text" name="street_barangay" 
                    value="{{ old('street_barangay', $existingApplication->street_barangay ?? '') }}" 
                    class="w-full border border-red-500 px-3 py-2 rounded-md">
            </div>

            <div class="col-span-2">
              <label class="block font-semibold text-gray-700 mb-1">Town / City / Municipality</label>
              <input type="text" name="town_city" 
                    value="{{ old('town_city', $existingApplication->town_city ?? '') }}" 
                    class="w-full border border-red-500 px-3 py-2 rounded-md">
            </div>

            <div class="col-span-2">
              <label class="block font-semibold text-gray-700 mb-1">Province</label>
              <input type="text" name="province" 
                    value="{{ old('province', $existingApplication->province ?? '') }}" 
                    class="w-full border border-red-500 px-3 py-2 rounded-md">
            </div>

            <div class="col-span-1">
              <label class="block font-semibold text-gray-700 mb-1">ZIP Code</label>
              <input type="text" name="zip_code" maxlength="4" pattern="\d{4}" 
                  value="{{ old('zip_code', $existingApplication->zip_code ?? '') }}" 
                  class="w-full border border-red-500 px-3 py-2 rounded-md text-center"
                  title="ZIP Code must be 4 digits">
            </div>
          </div>

          <div>
            <label class="block font-semibold text-gray-700 mb-1">Age</label>
            <input type="number" name="age" id="age"
              value="{{ old('age', $existingApplication->age ?? '') }}"
              class="w-full border border-red-500 px-3 py-2 rounded-md" readonly>
          </div>

          <div>
            <label class="block font-semibold text-gray-700 mb-1">Sex</label>
            <input type="text" name="sex" value="{{ old('sex', $existingApplication->sex ?? '') }}" class="w-full border border-red-500 px-3 py-2 rounded-md">
          </div>

          <div>
            <label class="block font-semibold text-gray-700 mb-1">Civil Status</label>
            <input type="text" name="civil_status" value="{{ old('civil_status', $existingApplication->civil_status ?? '') }}" class="w-full border border-red-500 px-3 py-2 rounded-md">
          </div>

          <div>
            <label class="block font-semibold text-gray-700 mb-1">Birthdate</label>
            <input type="date" name="birthdate" id="birthdate"
              value="{{ old('birthdate', optional($existingApplication)->birthdate?->format('Y-m-d') ?? '') }}"
              class="w-full border border-red-500 px-3 py-2 rounded-md">
          </div>

          <div>
            <label class="block font-semibold text-gray-700 mb-1">Birthplace</label>
            <input type="text" name="birthplace" value="{{ old('birthplace', $existingApplication->birthplace ?? '') }}" class="w-full border border-red-500 px-3 py-2 rounded-md">
          </div>

          <div>
            <label class="block font-semibold text-gray-700 mb-1">Disability</label>
            <input type="text" name="disability" value="{{ old('disability', $existingApplication->disability ?? '') }}" class="w-full border border-red-500 px-3 py-2 rounded-md">
          </div>

          <div>
            <label class="block font-semibold text-gray-700 mb-1">Tribe</label>
            <input type="text" name="tribe" value="{{ old('tribe', $existingApplication->tribe ?? '') }}" class="w-full border border-red-500 px-3 py-2 rounded-md">
          </div>

          <div>
            <label class="block font-semibold text-gray-700 mb-1">Citizenship</label>
            <input type="text" name="citizenship" value="{{ old('citizenship', $existingApplication->citizenship ?? '') }}" class="w-full border border-red-500 px-3 py-2 rounded-md">
          </div>

          <div>
            <label class="block font-semibold text-gray-700 mb-1">Birth Order</label>
            <input type="text" name="birth_order" value="{{ old('birth_order', $existingApplication->birth_order ?? '') }}" class="w-full border border-red-500 px-3 py-2 rounded-md">
          </div>

          <div>
            <label class="block font-semibold text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $existingApplication->email ?? '') }}" class="w-full border border-red-500 px-3 py-2 rounded-md">
          </div>

          <div>
            <label class="block font-semibold text-gray-700 mb-1">Telephone</label>
            <input type="text" name="telephone" value="{{ old('telephone', $existingApplication->telephone ?? '') }}" class="w-full border border-red-500 px-3 py-2 rounded-md">
          </div>

          <div>
            <label class="block font-semibold text-gray-700 mb-1">Religion</label>
            <input type="text" name="religion" value="{{ old('religion', $existingApplication->religion ?? '') }}" class="w-full border border-red-500 px-3 py-2 rounded-md">
          </div>
        </div>
      </section>

      <!-- Academic Data Section -->
      <section class="mt-8">
        <h2 class="text-xl font-semibold text-bsu-red mb-6">Academic Data</h2>

        <div class="mb-4">
          <label class="block mb-1 font-medium text-gray-700">High School Type</label>
          <input type="text" name="highschool_type" placeholder="Public or Private"
            value="{{ old('highschool_type', $existingApplication->highschool_type ?? '') }}"
            class="w-full border border-red-500 rounded-md p-2">
        </div>

        <div class="mb-4">
          <label class="block mb-1 font-medium text-gray-700">Monthly Allowance</label>
          <input type="number" name="monthly_allowance" placeholder="₱"
            value="{{ old('monthly_allowance', $existingApplication->monthly_allowance ?? '') }}"
            class="w-full border border-red-500 rounded-md p-2">
        </div>

        <div class="mb-4">
          <label class="block mb-1 font-medium text-gray-700">Living Arrangement</label>
          <input type="text" name="living_arrangement" placeholder="e.g., With parents, boarding house"
            value="{{ old('living_arrangement', $existingApplication->living_arrangement ?? '') }}"
            class="w-full border border-red-500 rounded-md p-2">
        </div>

        <div class="mb-4">
          <label class="block mb-1 font-medium text-gray-700">Mode of Transportation</label>
          <input type="text" name="transportation" placeholder="e.g., Jeep, Tricycle"
            value="{{ old('transportation', $existingApplication->transportation ?? '') }}"
            class="w-full border border-red-500 rounded-md p-2">
        </div>

        <div class="mb-4">
          <label class="block mb-1 font-medium text-gray-700">Educational Level</label>
          <input type="text" name="education_level" placeholder="e.g., Undergraduate"
            value="{{ old('education_level', $existingApplication->education_level ?? '') }}"
            class="w-full border border-red-500 rounded-md p-2">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block mb-1 font-medium text-gray-700">Program</label>
            <input type="text" name="program"
              value="{{ old('program', $existingApplication->program ?? '') }}"
              class="w-full border border-red-500 rounded-md p-2">
          </div>

          <div>
            <label class="block mb-1 font-medium text-gray-700">College</label>
            <input type="text" name="college"
              value="{{ old('college', $existingApplication->college ?? '') }}"
              class="w-full border border-red-500 rounded-md p-2">
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
          <div>
            <label class="block mb-1 font-medium text-gray-700">Year Level</label>
            <input type="text" name="year_level"
              value="{{ old('year_level', $existingApplication->year_level ?? '') }}"
              class="w-full border border-red-500 rounded-md p-2">
          </div>
          <div>
            <label class="block mb-1 font-medium text-gray-700">Campus</label>
            <input type="text" name="campus"
              value="{{ old('campus', $existingApplication->campus ?? '') }}"
              class="w-full border border-red-500 rounded-md p-2">
          </div>
          <div>
            <label class="block mb-1 font-medium text-gray-700">GWA</label>
            <select name="gwa" class="w-full border border-red-500 rounded-md p-2">
                <option value="">-- Select GWA --</option>
                @foreach (['1.00','1.25','1.50','1.75','2.00','2.25','2.50','2.75','3.00','5.00'] as $gwa)
                    <option value="{{ $gwa }}" 
                        {{ old('gwa', $existingApplication->gwa ?? '') == $gwa ? 'selected' : '' }}>
                        {{ $gwa }}
                    </option>
                @endforeach
            </select>
        </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
          <div>
            <label class="block mb-1 font-medium text-gray-700">Honors Received</label>
            <input type="text" name="honors"
              value="{{ old('honors', $existingApplication->honors ?? '') }}"
              class="w-full border border-red-500 rounded-md p-2">
          </div>
          <div>
            <label class="block mb-1 font-medium text-gray-700">Units Enrolled</label>
            <input type="text" name="units_enrolled"
              value="{{ old('units_enrolled', $existingApplication->units_enrolled ?? '') }}"
              class="w-full border border-red-500 rounded-md p-2">
          </div>
        </div>

        <div class="mt-4">
          <label class="block mb-1 font-medium text-gray-700">Academic Year</label>
          <input type="text" name="academic_year" placeholder="e.g., 2025-2026"
            value="{{ old('academic_year', $existingApplication->academic_year ?? '') }}"
            class="w-full border border-red-500 rounded-md p-2">
        </div>

        <div class="mt-6">
          <label class="block mb-1 font-medium text-gray-700">Do you have existing scholarships?</label>
          <div class="flex items-center gap-6 mt-1">
            <label class="flex items-center gap-2">
              <input type="radio" name="has_existing_scholarship" value="1"
                {{ old('has_existing_scholarship', $existingApplication->has_existing_scholarship ?? '') == 1 ? 'checked' : '' }}>
              <span>Yes</span>
            </label>
            <label class="flex items-center gap-2">
              <input type="radio" name="has_existing_scholarship" value="0"
                {{ old('has_existing_scholarship', $existingApplication->has_existing_scholarship ?? '') == 0 ? 'checked' : '' }}>
              <span>No</span>
            </label>
          </div>
        </div>

        <div class="mt-4">
          <label class="block mb-1 font-medium text-gray-700">If yes, provide scholarship details</label>
          <input type="text" name="existing_scholarship_details"
            value="{{ old('existing_scholarship_details', $existingApplication->existing_scholarship_details ?? '') }}"
            class="w-full border border-red-500 rounded-md p-2">
        </div>
      </section>

      <!-- Family Data Section -->
      <section>
        <h2 class="text-xl font-semibold text-bsu-red mb-4">Family Data</h2>

        <!-- Father Section -->
        <div class="border border-gray-300 rounded-lg p-4 mb-6">
          <h3 class="text-lg font-semibold text-gray-700 mb-2">Father's Information</h3>

          <label class="block font-medium mb-1">Is Father Living?</label>
          <select name="father_living" class="w-full border border-red-500 rounded-md px-3 py-2 mb-4">
            <option value="1" {{ old('father_living', $existingApplication->father_living ?? '') == 1 ? 'selected' : '' }}>Yes</option>
            <option value="0" {{ old('father_living', $existingApplication->father_living ?? '') == 0 ? 'selected' : '' }}>No</option>
          </select>

          <label class="block font-medium mb-1">Father's Name</label>
          <input type="text" name="father_name" value="{{ old('father_name', $existingApplication->father_name ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2 mb-4">

          <label class="block font-medium mb-1">Father's Age</label>
          <input type="number" name="father_age" value="{{ old('father_age', $existingApplication->father_age ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2 mb-4">

          <label class="block font-medium mb-1">Father's Residence</label>
          <input type="text" name="father_residence" value="{{ old('father_residence', $existingApplication->father_residence ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2 mb-4">

          <label class="block font-medium mb-1">Father's Education</label>
          <input type="text" name="father_education" value="{{ old('father_education', $existingApplication->father_education ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2 mb-4">

          <label class="block font-medium mb-1">Father's Contact</label>
          <input type="text" name="father_contact" value="{{ old('father_contact', $existingApplication->father_contact ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2 mb-4">

          <label class="block font-medium mb-1">Father's Occupation</label>
          <input type="text" name="father_occupation" value="{{ old('father_occupation', $existingApplication->father_occupation ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2 mb-4">

          <label class="block font-medium mb-1">Father's Company</label>
          <input type="text" name="father_company" value="{{ old('father_company', $existingApplication->father_company ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2 mb-4">

          <label class="block font-medium mb-1">Father's Company Address</label>
          <input type="text" name="father_company_address" value="{{ old('father_company_address', $existingApplication->father_company_address ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2 mb-4">

          <label class="block font-medium mb-1">Father's Employment Status</label>
          <input type="text" name="father_employment_status" value="{{ old('father_employment_status', $existingApplication->father_employment_status ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2">
        </div>

        <!-- Mother Section -->
        <div class="border border-gray-300 rounded-lg p-4">
          <h3 class="text-lg font-semibold text-gray-700 mb-2">Mother's Information</h3>

          <label class="block font-medium mb-1">Is Mother Living?</label>
          <select name="mother_living" class="w-full border border-red-500 rounded-md px-3 py-2 mb-4">
            <option value="1" {{ old('mother_living', $existingApplication->mother_living ?? '') == 1 ? 'selected' : '' }}>Yes</option>
            <option value="0" {{ old('mother_living', $existingApplication->mother_living ?? '') == 0 ? 'selected' : '' }}>No</option>
          </select>

          <label class="block font-medium mb-1">Mother's Name</label>
          <input type="text" name="mother_name" value="{{ old('mother_name', $existingApplication->mother_name ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2 mb-4">

          <label class="block font-medium mb-1">Mother's Age</label>
          <input type="number" name="mother_age" value="{{ old('mother_age', $existingApplication->mother_age ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2 mb-4">

          <label class="block font-medium mb-1">Mother's Residence</label>
          <input type="text" name="mother_residence" value="{{ old('mother_residence', $existingApplication->mother_residence ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2 mb-4">

          <label class="block font-medium mb-1">Mother's Education</label>
          <input type="text" name="mother_education" value="{{ old('mother_education', $existingApplication->mother_education ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2 mb-4">

          <label class="block font-medium mb-1">Mother's Contact</label>
          <input type="text" name="mother_contact" value="{{ old('mother_contact', $existingApplication->mother_contact ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2 mb-4">

          <label class="block font-medium mb-1">Mother's Occupation</label>
          <input type="text" name="mother_occupation" value="{{ old('mother_occupation', $existingApplication->mother_occupation ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2 mb-4">

          <label class="block font-medium mb-1">Mother's Company</label>
          <input type="text" name="mother_company" value="{{ old('mother_company', $existingApplication->mother_company ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2 mb-4">

          <label class="block font-medium mb-1">Mother's Company Address</label>
          <input type="text" name="mother_company_address" value="{{ old('mother_company_address', $existingApplication->mother_company_address ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2 mb-4">

          <label class="block font-medium mb-1">Mother's Employment Status</label>
          <input type="text" name="mother_employment_status" value="{{ old('mother_employment_status', $existingApplication->mother_employment_status ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2">
        </div>
      </section>
    </form>
    
    <!-- Buttons container -->
    <div class="flex justify-between mt-6">
      <!-- Submit Button (targets mainForm) -->
      <button form="mainForm" type="submit"
        class="bg-bsu-red text-white px-6 py-2 rounded hover:bg-red-700 transition duration-300">
        Submit Application
      </button>

      <!-- Download Button (separate form) -->
      <form method="POST" action="{{ url('/application-form/download') }}">
        @csrf
        <input type="hidden" name="form_id" value="{{ $existingApplication?->id ?? '' }}">
        <button type="submit"
          class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300">
          <!-- Download Icon -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
              d="M3 14.5a.5.5 0 01.5-.5h13a.5.5 0 010 1h-13a.5.5 0 01-.5-.5zm5.354-3.354a.5.5 0 01.708 0L10 12.293V3.5a.5.5 0 011 0v8.793l.938-.938a.5.5 0 11.707.707l-2 2a.5.5 0 01-.707 0l-2-2a.5.5 0 010-.707z"
              clip-rule="evenodd" />
          </svg>
          Download
        </button>
      </form>
    </div>
  <style>
    .form-input {
      @apply border rounded px-4 py-2 w-full bg-white dark:bg-gray-700 dark:text-white;
    }
  </style>
  <script>
    const birthdateInput = document.getElementById('birthdate');
    const ageInput = document.getElementById('age');

    function calculateAge(birthDate) {
      const today = new Date();
      let age = today.getFullYear() - birthDate.getFullYear();
      const m = today.getMonth() - birthDate.getMonth();
      if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) age--;
      return age;
    }

    birthdateInput.addEventListener('input', () => {
      if (birthdateInput.value) {
        const birthDate = new Date(birthdateInput.value);
        ageInput.value = calculateAge(birthDate);
      } else {
        ageInput.value = '';
      }
    });

    // Optional: pre-fill age on page load
    if (birthdateInput.value) {
      const birthDate = new Date(birthdateInput.value);
      ageInput.value = calculateAge(birthDate);
    }
  </script>
</body>
</html>