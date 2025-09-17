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
              class="w-full border border-red-500 px-3 py-2 rounded-md bg-gray-100" readonly
              title="Age will be automatically calculated when birthdate is entered">
            <small class="text-gray-500 text-xs">Age will be automatically calculated when birthdate is entered</small>
          </div>

          <div>
            <label class="block font-semibold text-gray-700 mb-1">Sex</label>
            <select name="sex" class="w-full border border-red-500 px-3 py-2 rounded-md">
                <option value="">-- Select Sex --</option>
                <option value="male" {{ old('sex', $existingApplication->sex ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('sex', $existingApplication->sex ?? '') == 'female' ? 'selected' : '' }}>Female</option>
            </select>
          </div>

          <div>
            <label class="block font-semibold text-gray-700 mb-1">Civil Status</label>
            <select name="civil_status" class="w-full border border-red-500 px-3 py-2 rounded-md">
                <option value="">-- Select Civil Status --</option>
                <option value="Single" {{ old('civil_status', $existingApplication->civil_status ?? '') == 'Single' ? 'selected' : '' }}>Single</option>
                <option value="Married" {{ old('civil_status', $existingApplication->civil_status ?? '') == 'Married' ? 'selected' : '' }}>Married</option>
                <option value="Widowed" {{ old('civil_status', $existingApplication->civil_status ?? '') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                <option value="Divorced" {{ old('civil_status', $existingApplication->civil_status ?? '') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                <option value="Separated" {{ old('civil_status', $existingApplication->civil_status ?? '') == 'Separated' ? 'selected' : '' }}>Separated</option>
            </select>
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
            <input type="text" name="disability" placeholder="If Applicable" value="{{ old('disability', $existingApplication->disability ?? '') }}" class="w-full border border-red-500 px-3 py-2 rounded-md">
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
            <select name="birth_order" class="w-full border border-red-500 px-3 py-2 rounded-md">
                <option value="">-- Select Birth Order --</option>
                <option value="First Born" {{ old('birth_order', $existingApplication->birth_order ?? '') == 'First Born' ? 'selected' : '' }}>First Born</option>
                <option value="Middle Born" {{ old('birth_order', $existingApplication->birth_order ?? '') == 'Middle Born' ? 'selected' : '' }}>Middle Born</option>
                <option value="Last Born" {{ old('birth_order', $existingApplication->birth_order ?? '') == 'Last Born' ? 'selected' : '' }}>Last Born</option>
            </select>
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
          <select name="highschool_type" class="w-full border border-red-500 rounded-md p-2">
              <option value="">-- Select High School Type --</option>
              @foreach (['Public', 'Private'] as $type)
                  <option value="{{ $type }}" 
                      {{ old('highschool_type', $existingApplication->highschool_type ?? '') == $type ? 'selected' : '' }}>
                      {{ $type }}
                  </option>
              @endforeach
          </select>
        </div>

        <div class="mb-4">
          <label class="block mb-1 font-medium text-gray-700">Monthly Allowance</label>
          <input type="number" name="monthly_allowance" placeholder="₱"
            value="{{ old('monthly_allowance', $existingApplication->monthly_allowance ?? '') }}"
            class="w-full border border-red-500 rounded-md p-2">
        </div>

        <div class="mb-4">
          <label class="block mb-1 font-medium text-gray-700">Living Arrangement</label>
          <select name="living_arrangement" class="w-full border border-red-500 rounded-md p-2" onchange="handleLivingArrangementChange(this)">
              <option value="">-- Select Living Arrangement --</option>
              <option value="Living with Parents" {{ old('living_arrangement', $existingApplication->living_arrangement ?? '') == 'Living with Parents' ? 'selected' : '' }}>Living with Parents</option>
              <option value="Living with Relatives" {{ old('living_arrangement', $existingApplication->living_arrangement ?? '') == 'Living with Relatives' ? 'selected' : '' }}>Living with Relatives</option>
              <option value="Owned House" {{ old('living_arrangement', $existingApplication->living_arrangement ?? '') == 'Owned House' ? 'selected' : '' }}>Owned House</option>
              <option value="Boarding House" {{ old('living_arrangement', $existingApplication->living_arrangement ?? '') == 'Boarding House' ? 'selected' : '' }}>Boarding House</option>
              <option value="Apartment" {{ old('living_arrangement', $existingApplication->living_arrangement ?? '') == 'Apartment' ? 'selected' : '' }}>Apartment</option>
              <option value="Others">Others (Please specify)</option>
          </select>
          <input type="text" name="living_arrangement_other" placeholder="Please specify" 
            value="{{ old('living_arrangement_other', $existingApplication->living_arrangement_other ?? '') }}"
            class="w-full border border-red-500 rounded-md p-2 mt-2 hidden" id="living_arrangement_other">
        </div>

        <div class="mb-4">
          <label class="block mb-1 font-medium text-gray-700">Mode of Transportation</label>
          <select name="transportation" class="w-full border border-red-500 rounded-md p-2" onchange="handleTransportationChange(this)">
              <option value="">-- Select Transportation --</option>
              <option value="Public Transportation" {{ old('transportation', $existingApplication->transportation ?? '') == 'Public Transportation' ? 'selected' : '' }}>Public Transportation</option>
              <option value="Own Vehicle" {{ old('transportation', $existingApplication->transportation ?? '') == 'Own Vehicle' ? 'selected' : '' }}>Own Vehicle</option>
              <option value="School Service" {{ old('transportation', $existingApplication->transportation ?? '') == 'School Service' ? 'selected' : '' }}>School Service</option>
              <option value="Others">Others (Please specify)</option>
          </select>
          <input type="text" name="transportation_other" placeholder="Please specify" 
            value="{{ old('transportation_other', $existingApplication->transportation_other ?? '') }}"
            class="w-full border border-red-500 rounded-md p-2 mt-2 hidden" id="transportation_other">
        </div>

        <div class="mb-4">
          <label class="block mb-1 font-medium text-gray-700">Educational Level</label>
          <select name="education_level" class="w-full border border-red-500 rounded-md p-2">
              <option value="">-- Select Educational Level --</option>
              <option value="Undergraduate" {{ old('education_level', $existingApplication->education_level ?? '') == 'Undergraduate' ? 'selected' : '' }}>Undergraduate</option>
              <option value="Graduate School" {{ old('education_level', $existingApplication->education_level ?? '') == 'Graduate School' ? 'selected' : '' }}>Graduate School</option>
              <option value="Integrated / Laboratory School" {{ old('education_level', $existingApplication->education_level ?? '') == 'Integrated / Laboratory School' ? 'selected' : '' }}>Integrated / Laboratory School</option>
          </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block mb-1 font-medium text-gray-700">Program</label>
            <select name="program" class="w-full border border-red-500 rounded-md p-2">
                <option value="">-- Select Program --</option>
                @foreach (['BS Computer Science', 'BS Information Technology', 'BS Computer Engineering', 'BS Electronics Engineering', 'BS Civil Engineering', 'BS Mechanical Engineering', 'BS Electrical Engineering', 'BS Industrial Engineering', 'BS Accountancy', 'BS Business Administration', 'BS Tourism Management', 'BS Hospitality Management', 'BS Psychology', 'BS Education', 'BS Nursing', 'BS Medical Technology', 'BS Pharmacy', 'BS Biology', 'BS Chemistry', 'BS Mathematics', 'BS Physics', 'BS Environmental Science', 'BS Agriculture', 'BS Fisheries', 'BS Forestry', 'BS Architecture', 'BS Interior Design', 'BS Fine Arts', 'BS Communication', 'BS Social Work', 'BS Criminology', 'BS Political Science', 'BS History', 'BS Literature', 'BS Philosophy', 'BS Economics', 'BS Sociology', 'BS Anthropology'] as $program)
                    <option value="{{ $program }}" 
                        {{ old('program', $existingApplication->program ?? '') == $program ? 'selected' : '' }}>
                        {{ $program }}
                    </option>
                @endforeach
            </select>
          </div>

          <div>
            <label class="block mb-1 font-medium text-gray-700">College/Department</label>
            <select name="college" class="w-full border border-red-500 rounded-md p-2">
                <option value="">-- Select College/Department --</option>
                <option value="CICS" {{ old('college', $existingApplication->college ?? '') == 'CICS' ? 'selected' : '' }}>CICS (College of Information and Computing Sciences)</option>
                <option value="CTE" {{ old('college', $existingApplication->college ?? '') == 'CTE' ? 'selected' : '' }}>CTE (College of Teacher Education)</option>
                <option value="CABEIHM" {{ old('college', $existingApplication->college ?? '') == 'CABEIHM' ? 'selected' : '' }}>CABEIHM (College of Accountancy, Business, Economics and International Hospitality Management)</option>
                <option value="CAS" {{ old('college', $existingApplication->college ?? '') == 'CAS' ? 'selected' : '' }}>CAS (College of Arts and Sciences)</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
          <div>
            <label class="block mb-1 font-medium text-gray-700">Grade/Year Level</label>
            <select name="year_level" class="w-full border border-red-500 rounded-md p-2">
                <option value="">-- Select Grade/Year Level --</option>
                <option value="First Year" {{ old('year_level', $existingApplication->year_level ?? '') == 'First Year' ? 'selected' : '' }}>First Year</option>
                <option value="Second Year" {{ old('year_level', $existingApplication->year_level ?? '') == 'Second Year' ? 'selected' : '' }}>Second Year</option>
                <option value="Third Year" {{ old('year_level', $existingApplication->year_level ?? '') == 'Third Year' ? 'selected' : '' }}>Third Year</option>
                <option value="Fourth Year" {{ old('year_level', $existingApplication->year_level ?? '') == 'Fourth Year' ? 'selected' : '' }}>Fourth Year</option>
            </select>
          </div>
          <div>
            <label class="block mb-1 font-medium text-gray-700">Campus</label>
            <select name="campus" class="w-full border border-red-500 rounded-md p-2">
                <option value="">-- Select Campus --</option>
                @foreach (['BatStateU Alangilan', 'BatStateU Main', 'BatStateU Lipa', 'BatStateU Malvar', 'BatStateU Lemery', 'BatStateU San Juan', 'BatStateU Lobo', 'BatStateU Rosario', 'BatStateU Balayan', 'BatStateU Calaca', 'BatStateU Calatagan', 'BatStateU Mabini', 'BatStateU Nasugbu', 'BatStateU Tuy'] as $campus)
                    <option value="{{ $campus }}" 
                        {{ old('campus', $existingApplication->campus ?? '') == $campus ? 'selected' : '' }}
                        {{ (auth()->user()->campus_id ?? '') == $campus ? 'selected' : '' }}>
                        {{ $campus }}
                    </option>
                @endforeach
            </select>
          </div>
          <div>
            <label class="block mb-1 font-medium text-gray-700">GWA</label>
            <input type="number" name="gwa" step="0.01" min="1.00" max="5.00" 
                   placeholder="0.00"
                   value="{{ old('gwa', $existingApplication->gwa ?? '') }}"
                   class="w-full border border-red-500 rounded-md p-2">
        </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
          <div>
            <label class="block mb-1 font-medium text-gray-700">Honors Received</label>
            <input type="text" name="honors" placeholder="If any"
              value="{{ old('honors', $existingApplication->honors ?? '') }}"
              class="w-full border border-red-500 rounded-md p-2">
          </div>
          <div>
            <label class="block mb-1 font-medium text-gray-700">Units Enrolled</label>
            <input type="number" name="units_enrolled" min="1" max="30"
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
              <input type="radio" name="has_existing_scholarship" value="1" onchange="toggleScholarshipDetails()"
                {{ old('has_existing_scholarship', $existingApplication->has_existing_scholarship ?? '') == 1 ? 'checked' : '' }}>
              <span>Yes</span>
            </label>
            <label class="flex items-center gap-2">
              <input type="radio" name="has_existing_scholarship" value="0" onchange="toggleScholarshipDetails()"
                {{ old('has_existing_scholarship', $existingApplication->has_existing_scholarship ?? '') == 0 ? 'checked' : '' }}>
              <span>No</span>
            </label>
          </div>
        </div>

        <div class="mt-4" id="scholarship_details_container">
          <label class="block mb-1 font-medium text-gray-700">If yes, provide scholarship details</label>
          <input type="text" name="existing_scholarship_details" id="existing_scholarship_details"
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

    // Handle Living Arrangement "Others" option
    function handleLivingArrangementChange(select) {
      const otherInput = document.getElementById('living_arrangement_other');
      if (select.value === 'Others') {
        otherInput.classList.remove('hidden');
        otherInput.required = true;
      } else {
        otherInput.classList.add('hidden');
        otherInput.required = false;
        otherInput.value = '';
      }
    }

    // Handle Transportation "Others" option
    function handleTransportationChange(select) {
      const otherInput = document.getElementById('transportation_other');
      if (select.value === 'Others') {
        otherInput.classList.remove('hidden');
        otherInput.required = true;
      } else {
        otherInput.classList.add('hidden');
        otherInput.required = false;
        otherInput.value = '';
      }
    }

    // Handle existing scholarship checkbox
    function toggleScholarshipDetails() {
      const yesRadio = document.querySelector('input[name="has_existing_scholarship"][value="1"]');
      const detailsContainer = document.getElementById('scholarship_details_container');
      const detailsInput = document.getElementById('existing_scholarship_details');
      
      if (yesRadio.checked) {
        detailsContainer.style.display = 'block';
        detailsInput.disabled = false;
        detailsInput.required = true;
      } else {
        detailsContainer.style.display = 'none';
        detailsInput.disabled = true;
        detailsInput.required = false;
        detailsInput.value = '';
      }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
      toggleScholarshipDetails();
      
      // Check if living arrangement is "Others"
      const livingArrangementSelect = document.querySelector('select[name="living_arrangement"]');
      if (livingArrangementSelect && livingArrangementSelect.value === 'Others') {
        handleLivingArrangementChange(livingArrangementSelect);
      }
      
      // Check if transportation is "Others"
      const transportationSelect = document.querySelector('select[name="transportation"]');
      if (transportationSelect && transportationSelect.value === 'Others') {
        handleTransportationChange(transportationSelect);
      }
    });
  </script>
</body>
</html>