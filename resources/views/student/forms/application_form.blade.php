@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;

// Check if user is logged in
if (!Session::has('user_id')) {
    return redirect()->route('login');
}

// Get logged-in user
$user = User::find(session('user_id'));

// If user not found, clear session and redirect
if (!$user) {
    Session::flush();
    return redirect()->route('login');
}
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Scholarship Application Form</title>
  <link rel="icon" href="{{ asset('favicon.ico') }}">

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
      <section class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 mb-8 border border-gray-200 dark:border-gray-700 shadow-sm">
        <h2 class="text-3xl font-bold text-red-800 dark:text-red-400 mb-6 border-b-2 border-red-700 dark:border-red-500 pb-2">Personal Data</h2>
        
        <div class="space-y-5">
        <!-- Row 1: Last Name, First Name, Middle Name -->
        <div class="flex flex-wrap items-center gap-4">
          <div class="flex items-center gap-2 flex-1 min-w-[200px]">
            <label class="font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Last Name: <span class="text-red-500">*</span></label>
            <input type="text" name="last_name" required value="{{ old('last_name', $existingApplication->last_name ?? '') }}" class="flex-1 border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
          </div>
          <div class="flex items-center gap-2 flex-1 min-w-[200px]">
            <label class="font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">First Name: <span class="text-red-500">*</span></label>
            <input type="text" name="first_name" required value="{{ old('first_name', $existingApplication->first_name ?? '') }}" class="flex-1 border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
          </div>
          <div class="flex items-center gap-2 flex-1 min-w-[200px]">
            <label class="font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Middle Name:</label>
            <input type="text" name="middle_name" value="{{ old('middle_name', $existingApplication->middle_name ?? '') }}" class="flex-1 border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
          </div>
        </div>

        <!-- Row 2: Age, Sex, Civil Status -->
        <div class="flex flex-wrap items-center gap-4">
          <div class="flex items-center gap-2">
            <label class="font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Age:</label>
            <input type="number" name="age" id="age" value="{{ old('age', $existingApplication->age ?? '') }}" class="w-16 border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-gray-100 dark:bg-gray-600 dark:text-white transition-colors" readonly title="Age will be automatically calculated when birthdate is entered">
          </div>
          <div class="flex items-center gap-2 flex-1 min-w-[150px]">
            <label class="font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Sex:</label>
            <select name="sex" class="flex-1 border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
              <option value="">-- Select --</option>
              <option value="male" {{ old('sex', $existingApplication->sex ?? '') == 'male' ? 'selected' : '' }}>Male</option>
              <option value="female" {{ old('sex', $existingApplication->sex ?? '') == 'female' ? 'selected' : '' }}>Female</option>
            </select>
          </div>
          <div class="flex items-center gap-2 flex-1 min-w-[200px]">
            <label class="font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Civil Status:</label>
            <select name="civil_status" class="flex-1 border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
              <option value="">-- Select --</option>
              <option value="Single" {{ old('civil_status', $existingApplication->civil_status ?? '') == 'Single' ? 'selected' : '' }}>Single</option>
              <option value="Married" {{ old('civil_status', $existingApplication->civil_status ?? '') == 'Married' ? 'selected' : '' }}>Married</option>
              <option value="Widowed" {{ old('civil_status', $existingApplication->civil_status ?? '') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
              <option value="Divorced" {{ old('civil_status', $existingApplication->civil_status ?? '') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
              <option value="Separated" {{ old('civil_status', $existingApplication->civil_status ?? '') == 'Separated' ? 'selected' : '' }}>Separated</option>
            </select>
          </div>
        </div>

        <!-- Row 3: Birthdate (mm/dd/yyyy) and Birthplace -->
        <div class="flex flex-wrap items-center gap-4">
          <div class="flex items-center gap-2 flex-1 min-w-[250px]">
            <label class="font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Birthdate:</label>
            @php
              $birthdate = old('birthdate', optional($existingApplication)->birthdate?->format('Y-m-d') ?? '');
              $birthMonth = $birthdate ? date('m', strtotime($birthdate)) : '';
              $birthDay = $birthdate ? date('d', strtotime($birthdate)) : '';
              $birthYear = $birthdate ? date('Y', strtotime($birthdate)) : '';
            @endphp
            <div class="flex items-start gap-1">
              <div>
                <input type="number" id="birth_mm" name="birth_mm" min="1" max="12" placeholder="mm" value="{{ $birthMonth }}" class="w-12 border-b-2 border-gray-300 dark:border-gray-600 px-1 py-1 text-center focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors" maxlength="2">
                <label class="block text-xs text-gray-600 dark:text-gray-400 text-center mt-1">mm</label>
              </div>
              <span class="pt-1 text-gray-600 dark:text-gray-400">/</span>
              <div>
                <input type="number" id="birth_dd" name="birth_dd" min="1" max="31" placeholder="dd" value="{{ $birthDay }}" class="w-12 border-b-2 border-gray-300 dark:border-gray-600 px-1 py-1 text-center focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors" maxlength="2">
                <label class="block text-xs text-gray-600 dark:text-gray-400 text-center mt-1">dd</label>
              </div>
              <span class="pt-1 text-gray-600 dark:text-gray-400">/</span>
              <div>
                <input type="number" id="birth_yyyy" name="birth_yyyy" min="1900" max="2010" placeholder="yyyy" value="{{ $birthYear }}" class="w-16 border-b-2 border-gray-300 dark:border-gray-600 px-1 py-1 text-center focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors" maxlength="4">
                <label class="block text-xs text-gray-600 dark:text-gray-400 text-center mt-1">yyyy</label>
              </div>
              <input type="hidden" name="birthdate" id="birthdate" value="{{ $birthdate }}">
            </div>
          </div>
          <div class="flex items-center gap-2 flex-1 min-w-[200px]">
            <label class="font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Birthplace:</label>
            <input type="text" name="birthplace" value="{{ old('birthplace', $existingApplication->birthplace ?? '') }}" class="flex-1 border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
          </div>
        </div>

        <!-- Row 4: Email Address and Contact Number -->
        <div class="flex flex-wrap items-center gap-4">
          <div class="flex items-center gap-2 flex-1 min-w-[250px]">
            <label class="font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Email Address:</label>
            <input type="email" name="email" value="{{ old('email', $existingApplication->email ?? '') }}" class="flex-1 border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
          </div>
          <div class="flex items-center gap-2 flex-1 min-w-[200px]">
            <label class="font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Contact Number:</label>
            <input type="text" name="contact_number" value="{{ old('contact_number', $existingApplication->contact_number ?? '') }}" class="flex-1 border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
          </div>
        </div>

        <!-- Row 5: Permanent Home Address -->
        <div class="flex flex-wrap items-start gap-4">
          <label class="font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap pt-1">Permanent Home Address:</label>
          <div class="flex flex-wrap gap-4 flex-1">
            <div class="flex-1 min-w-[200px]">
              <input type="text" name="street_barangay" value="{{ old('street_barangay', $existingApplication->street_barangay ?? '') }}" class="w-full border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none text-center bg-white dark:bg-gray-700 dark:text-white transition-colors">
              <label class="block text-sm text-gray-600 dark:text-gray-400 text-center mt-1">Street / Barangay</label>
            </div>
            <div class="flex-1 min-w-[200px]">
              <input type="text" name="town_city" value="{{ old('town_city', $existingApplication->town_city ?? '') }}" class="w-full border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none text-center bg-white dark:bg-gray-700 dark:text-white transition-colors">
              <label class="block text-sm text-gray-600 dark:text-gray-400 text-center mt-1">Town / City / Municipality</label>
            </div>
            <div class="w-32 min-w-[120px]">
              <input type="text" name="province" value="{{ old('province', $existingApplication->province ?? '') }}" class="w-full border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none text-center bg-white dark:bg-gray-700 dark:text-white transition-colors">
              <label class="block text-sm text-gray-600 dark:text-gray-400 text-center mt-1">Province</label>
            </div>
          </div>
        </div>

        <!-- Row 6: Zip Code and Citizenship -->
        <div class="flex flex-wrap items-center gap-4">
          <div class="flex items-center gap-2">
            <label class="font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Zip Code:</label>
            <input type="text" name="zip_code" maxlength="4" pattern="\d{4}" value="{{ old('zip_code', $existingApplication->zip_code ?? '') }}" class="w-20 border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 text-center focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors" title="ZIP Code must be 4 digits">
          </div>
          <div class="flex items-center gap-2 flex-1 min-w-[200px]">
            <label class="font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Citizenship:</label>
            <input type="text" name="citizenship" value="{{ old('citizenship', $existingApplication->citizenship ?? '') }}" class="flex-1 border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
          </div>
        </div>

        <!-- Row 7: Type of Disability and Tribal Membership -->
        <div class="flex flex-wrap items-center gap-4">
          <div class="flex items-center gap-2 flex-1 min-w-[250px]">
            <label class="font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Type of Disability (if applicable):</label>
            <input type="text" name="disability" placeholder="If Applicable" value="{{ old('disability', $existingApplication->disability ?? '') }}" class="flex-1 border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
          </div>
          <div class="flex items-center gap-2 flex-1 min-w-[200px]">
            <label class="font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Tribal Membership:</label>
            <input type="text" name="tribe" value="{{ old('tribe', $existingApplication->tribe ?? '') }}" class="flex-1 border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
          </div>
        </div>
        </div>
      </section>

      <!-- Academic Data Section -->
      <section class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 mb-8 border border-gray-200 dark:border-gray-700 shadow-sm">
        <h2 class="text-3xl font-bold text-red-800 dark:text-red-400 mb-6 border-b-2 border-red-700 dark:border-red-500 pb-2">Academic Data</h2>
        
        <div class="space-y-5">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="md:col-span-1">
            <label class="block mb-1 font-medium text-gray-700 dark:text-gray-300">SR Code</label>
            <input type="text" name="sr_code" 
              value="{{ old('sr_code', $existingApplication->sr_code ?? '') }}"
              class="w-full border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
          </div>

          <div class="md:col-span-3">
            <label class="block mb-2 font-medium text-gray-700 dark:text-gray-300">Educational Level</label>
            <div class="flex flex-wrap items-center justify-between w-full">
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="education_level" value="Undergraduate" {{ old('education_level', $existingApplication->education_level ?? '') == 'Undergraduate' ? 'checked' : '' }} class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
                <span class="text-gray-700 dark:text-gray-300">Undergraduate</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="education_level" value="Graduate School" {{ old('education_level', $existingApplication->education_level ?? '') == 'Graduate School' ? 'checked' : '' }} class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
                <span class="text-gray-700 dark:text-gray-300">Graduate School</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="education_level" value="Integrated School" {{ old('education_level', $existingApplication->education_level ?? '') == 'Integrated School' ? 'checked' : '' }} class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
                <span class="text-gray-700 dark:text-gray-300">Integrated School</span>
              </label>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block mb-1 font-medium text-gray-700 dark:text-gray-300">Program</label>
            <select name="program" class="w-full border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
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
            <label class="block mb-1 font-medium text-gray-700 dark:text-gray-300">College/Department</label>
            <select name="college_department" class="w-full border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
                <option value="">-- Select College/Department --</option>
                <option value="CICS" {{ old('college_department', $existingApplication->college_department ?? '') == 'CICS' ? 'selected' : '' }}>CICS</option>
                <option value="CTE" {{ old('college_department', $existingApplication->college_department ?? '') == 'CTE' ? 'selected' : '' }}>CTE</option>
                <option value="CABEIHM" {{ old('college_department', $existingApplication->college_department ?? '') == 'CABEIHM' ? 'selected' : '' }}>CABEIHM</option>
                <option value="CAS" {{ old('college_department', $existingApplication->college_department ?? '') == 'CAS' ? 'selected' : '' }}>CAS</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block mb-1 font-medium text-gray-700 dark:text-gray-300">Grade/Year Level</label>
            <select name="year_level" class="w-full border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
                <option value="">-- Select Grade/Year Level --</option>
                <option value="1st Year" {{ old('year_level', $existingApplication->year_level ?? '') == '1st Year' ? 'selected' : '' }}>1st Year</option>
                <option value="2nd Year" {{ old('year_level', $existingApplication->year_level ?? '') == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                <option value="3rd Year" {{ old('year_level', $existingApplication->year_level ?? '') == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                <option value="4th Year" {{ old('year_level', $existingApplication->year_level ?? '') == '4th Year' ? 'selected' : '' }}>4th Year</option>
            </select>
          </div>
          <div>
            <label class="block mb-1 font-medium text-gray-700 dark:text-gray-300">Campus</label>
            <input type="text" name="campus" 
              value="{{ old('campus', $existingApplication->campus ?? '') }}"
              class="w-full border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
          </div>
          <div>
            <label class="block mb-1 font-medium text-gray-700 dark:text-gray-300">Previous GWA</label>
            <input type="number" name="previous_gwa" step="0.01" min="1.00" max="5.00" 
                   placeholder="0.00"
                   value="{{ old('previous_gwa', $existingApplication->previous_gwa ?? '') }}"
                   class="w-full border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
        </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block mb-1 font-medium text-gray-700 dark:text-gray-300">Honors Received</label>
            <input type="text" name="honors_received" placeholder="If any"
              value="{{ old('honors_received', $existingApplication->honors_received ?? '') }}"
              class="w-full border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
          </div>
          <div>
            <label class="block mb-1 font-medium text-gray-700 dark:text-gray-300">Units Enrolled</label>
            <input type="number" name="units_enrolled" min="1" max="30"
              value="{{ old('units_enrolled', $existingApplication->units_enrolled ?? '') }}"
              class="w-full border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block mb-1 font-medium text-gray-700 dark:text-gray-300">Scholarship Applied</label>
            <input type="text" name="scholarship_applied" 
              value="{{ old('scholarship_applied', $existingApplication->scholarship_applied ?? '') }}"
              class="w-full border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
          </div>
          <div>
            <label class="block mb-1 font-medium text-gray-700 dark:text-gray-300">Semester</label>
            <input type="text" name="semester" placeholder="e.g., 1st Semester"
              value="{{ old('semester', $existingApplication->semester ?? '') }}"
              class="w-full border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
          </div>
          <div>
            <label class="block mb-1 font-medium text-gray-700 dark:text-gray-300">Academic Year</label>
            <input type="text" name="academic_year" placeholder="e.g., 2025-2026"
              value="{{ old('academic_year', $existingApplication->academic_year ?? '') }}"
              class="w-full border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
          </div>
        </div>

        <div>
          <label class="block mb-2 font-medium text-gray-700 dark:text-gray-300">Do you have existing scholarships?</label>
          <div class="flex items-center gap-6 mt-1">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="radio" name="has_existing_scholarship" value="1" onchange="toggleScholarshipDetails()"
                {{ old('has_existing_scholarship', $existingApplication->has_existing_scholarship ?? '') == 1 ? 'checked' : '' }}
                class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
              <span class="text-gray-700 dark:text-gray-300">Yes</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="radio" name="has_existing_scholarship" value="0" onchange="toggleScholarshipDetails()"
                {{ old('has_existing_scholarship', $existingApplication->has_existing_scholarship ?? '') == 0 ? 'checked' : '' }}
                class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
              <span class="text-gray-700 dark:text-gray-300">No</span>
            </label>
          </div>
        </div>

        <div id="scholarship_details_container">
          <label class="block mb-1 font-medium text-gray-700 dark:text-gray-300">If Yes, please specify:</label>
          <input type="text" name="existing_scholarship_details" id="existing_scholarship_details"
            value="{{ old('existing_scholarship_details', $existingApplication->existing_scholarship_details ?? '') }}"
            class="w-full border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
        </div>
        </div>
      </section>

      <!-- Family Data Section -->
      <section class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 mb-8 border border-gray-200 dark:border-gray-700 shadow-sm">
        <h2 class="text-3xl font-bold text-red-800 dark:text-red-400 mb-6 border-b-2 border-red-700 dark:border-red-500 pb-2">Family Data</h2>
        
        <div class="space-y-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Father Section -->
        <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4">
          <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Father's Information</h3>

          <div class="space-y-3">
            <div class="flex items-center gap-4">
              <label class="font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap min-w-[140px]">Father Status:</label>
              <div class="flex items-center gap-6 flex-1">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" name="father_status" value="living" {{ old('father_status', $existingApplication->father_status ?? '') == 'living' ? 'checked' : '' }} class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
                  <span class="text-gray-700 dark:text-gray-300">Living</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" name="father_status" value="deceased" {{ old('father_status', $existingApplication->father_status ?? '') == 'deceased' ? 'checked' : '' }} class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
                  <span class="text-gray-700 dark:text-gray-300">Deceased</span>
                </label>
              </div>
            </div>

            <div class="flex items-center gap-4">
              <label class="font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap min-w-[140px]">Father's Name:</label>
              <input type="text" name="father_name" value="{{ old('father_name', $existingApplication->father_name ?? '') }}" class="flex-1 border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
            </div>

            <div class="flex items-center gap-4">
              <label class="font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap min-w-[140px]">Father's Address:</label>
              <input type="text" name="father_address" value="{{ old('father_address', $existingApplication->father_address ?? '') }}" class="flex-1 border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
            </div>

            <div class="flex items-center gap-4">
              <label class="font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap min-w-[140px]">Father's Contact:</label>
              <input type="text" name="father_contact" value="{{ old('father_contact', $existingApplication->father_contact ?? '') }}" class="flex-1 border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
            </div>

            <div class="flex items-center gap-4">
              <label class="font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap min-w-[140px]">Father's Occupation:</label>
              <input type="text" name="father_occupation" value="{{ old('father_occupation', $existingApplication->father_occupation ?? '') }}" class="flex-1 border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
            </div>
          </div>
        </div>

        <!-- Mother Section -->
        <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4">
          <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Mother's Information</h3>

          <div class="space-y-3">
            <div class="flex items-center gap-4">
              <label class="font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap min-w-[140px]">Mother Status:</label>
              <div class="flex items-center gap-6 flex-1">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" name="mother_status" value="living" {{ old('mother_status', $existingApplication->mother_status ?? '') == 'living' ? 'checked' : '' }} class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
                  <span class="text-gray-700 dark:text-gray-300">Living</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" name="mother_status" value="deceased" {{ old('mother_status', $existingApplication->mother_status ?? '') == 'deceased' ? 'checked' : '' }} class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
                  <span class="text-gray-700 dark:text-gray-300">Deceased</span>
                </label>
              </div>
            </div>

            <div class="flex items-center gap-4">
              <label class="font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap min-w-[140px]">Mother's Name:</label>
              <input type="text" name="mother_name" value="{{ old('mother_name', $existingApplication->mother_name ?? '') }}" class="flex-1 border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
            </div>

            <div class="flex items-center gap-4">
              <label class="font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap min-w-[140px]">Mother's Address:</label>
              <input type="text" name="mother_address" value="{{ old('mother_address', $existingApplication->mother_address ?? '') }}" class="flex-1 border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
            </div>

            <div class="flex items-center gap-4">
              <label class="font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap min-w-[140px]">Mother's Contact:</label>
              <input type="text" name="mother_contact" value="{{ old('mother_contact', $existingApplication->mother_contact ?? '') }}" class="flex-1 border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
            </div>

            <div class="flex items-center gap-4">
              <label class="font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap min-w-[140px]">Mother's Occupation:</label>
              <input type="text" name="mother_occupation" value="{{ old('mother_occupation', $existingApplication->mother_occupation ?? '') }}" class="flex-1 border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
            </div>
          </div>
        </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Estimated Gross Annual Income Section -->
          <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4">
            <label class="block font-medium mb-3 text-gray-700 dark:text-gray-300">Estimated gross annual income:</label>
            <div class="space-y-2">
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="estimated_gross_annual_income" value="not_over_250000" {{ old('estimated_gross_annual_income', $existingApplication->estimated_gross_annual_income ?? '') == 'not_over_250000' ? 'checked' : '' }} class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
                <span class="text-gray-700 dark:text-gray-300">Not over P 250,000.00</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="estimated_gross_annual_income" value="over_250000_not_over_400000" {{ old('estimated_gross_annual_income', $existingApplication->estimated_gross_annual_income ?? '') == 'over_250000_not_over_400000' ? 'checked' : '' }} class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
                <span class="text-gray-700 dark:text-gray-300">Over P 250,000 but not over P 400,000</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="estimated_gross_annual_income" value="over_400000_not_over_800000" {{ old('estimated_gross_annual_income', $existingApplication->estimated_gross_annual_income ?? '') == 'over_400000_not_over_800000' ? 'checked' : '' }} class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
                <span class="text-gray-700 dark:text-gray-300">Over P 400,000 but not over P 800,000</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="estimated_gross_annual_income" value="over_800000_not_over_2000000" {{ old('estimated_gross_annual_income', $existingApplication->estimated_gross_annual_income ?? '') == 'over_800000_not_over_2000000' ? 'checked' : '' }} class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
                <span class="text-gray-700 dark:text-gray-300">Over P 800,000 but not over P 2,000,000</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="estimated_gross_annual_income" value="over_2000000_not_over_8000000" {{ old('estimated_gross_annual_income', $existingApplication->estimated_gross_annual_income ?? '') == 'over_2000000_not_over_8000000' ? 'checked' : '' }} class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
                <span class="text-gray-700 dark:text-gray-300">Over P 2,000,000 but not over P 8,000,000</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="estimated_gross_annual_income" value="over_8000000" {{ old('estimated_gross_annual_income', $existingApplication->estimated_gross_annual_income ?? '') == 'over_8000000' ? 'checked' : '' }} class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
                <span class="text-gray-700 dark:text-gray-300">Over P 8,000,000</span>
              </label>
            </div>
          </div>

          <!-- Number of Siblings -->
          <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4">
            <label class="block font-medium mb-1 text-gray-700 dark:text-gray-300">Number of Siblings</label>
            <input type="number" name="siblings_count" value="{{ old('siblings_count', $existingApplication->siblings_count ?? '') }}" class="w-full border-b-2 border-gray-300 dark:border-gray-600 px-2 py-1 focus:border-red-500 dark:focus:border-red-600 focus:outline-none bg-white dark:bg-gray-700 dark:text-white transition-colors">
          </div>
        </div>
        </div>
      </section>

      <!-- Essay / Question Section -->
      <section class="mt-8">
        <h2 class="text-xl font-semibold text-bsu-red mb-6">PLEASE ANSWER THE FOLLOWING QUESTIONS IN YOUR OWN HANDWRITING</h2>
        <div>
          <label class="block font-medium mb-1">Reason for Applying</label>
          <textarea name="reason_for_applying" rows="5" 
            class="w-full border border-red-500 rounded-md px-3 py-2"
            placeholder="Please explain your reason for applying for this scholarship...">{{ old('reason_for_applying', $existingApplication->reason_for_applying ?? '') }}</textarea>
        </div>
      </section>

      <!-- Certification Section -->
      <section class="mt-8">
        <h2 class="text-xl font-semibold text-bsu-red mb-6">Certification</h2>
        <div class="space-y-4">
          <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-sm text-yellow-800">
              <strong>Note:</strong> By submitting this form, you certify that all information provided is true and accurate. 
              Any false information may result in disqualification from scholarship consideration.
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block font-medium mb-1">Student Signature</label>
              <input type="text" name="student_signature" placeholder="Type your full name as digital signature" value="{{ old('student_signature', $existingApplication->student_signature ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2">
              <p class="text-sm text-gray-500 mt-1">Type your full name as your digital signature</p>
            </div>

            <div>
              <label class="block font-medium mb-1">Date Signed</label>
              <input type="date" name="date_signed" value="{{ old('date_signed', optional($existingApplication)->date_signed?->format('Y-m-d') ?? date('Y-m-d')) }}" class="w-full border border-red-500 rounded-md px-3 py-2">
            </div>
          </div>

          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="font-semibold text-blue-800 mb-2">Declaration</h4>
            <p class="text-sm text-blue-700">
              I hereby declare that the information provided in this application form is true, complete, and accurate to the best of my knowledge. 
              I understand that any false information may result in the rejection of my application or termination of any scholarship granted.
            </p>
          </div>
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

      <!-- Print Button -->
      <button type="button" onclick="printApplication()"
        class="flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition duration-300">
        <!-- Print Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
        </svg>
        Print Application
      </button>
    </div>
  </div>

  <script>
    const birthMonthInput = document.getElementById('birth_mm');
    const birthDayInput = document.getElementById('birth_dd');
    const birthYearInput = document.getElementById('birth_yyyy');
    const birthdateHiddenInput = document.getElementById('birthdate');
    const ageInput = document.getElementById('age');

    function updateBirthdate() {
      const month = birthMonthInput.value;
      const day = birthDayInput.value;
      const year = birthYearInput.value;

      if (month && day && year && year.length === 4) {
        const monthNum = parseInt(month);
        const dayNum = parseInt(day);
        const yearNum = parseInt(year);
        
        // Basic validation
        if (monthNum >= 1 && monthNum <= 12 && dayNum >= 1 && dayNum <= 31 && yearNum >= 1900 && yearNum <= 2010) {
          const monthStr = month.padStart(2, '0');
          const dayStr = day.padStart(2, '0');
          const dateString = `${year}-${monthStr}-${dayStr}`;
          const birthDate = new Date(dateString);
          
          // Validate date (check if date is valid)
          if (birthDate.getFullYear() == yearNum && 
              (birthDate.getMonth() + 1) == monthNum && 
              birthDate.getDate() == dayNum) {
            birthdateHiddenInput.value = dateString;
            calculateAge(birthDate);
          } else {
            birthdateHiddenInput.value = '';
            ageInput.value = '';
          }
        } else {
          birthdateHiddenInput.value = '';
          ageInput.value = '';
        }
      } else {
        birthdateHiddenInput.value = '';
        ageInput.value = '';
      }
    }

    function calculateAge(birthDate) {
      const today = new Date();
      let age = today.getFullYear() - birthDate.getFullYear();
      const m = today.getMonth() - birthDate.getMonth();
      if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) age--;
      ageInput.value = age > 0 ? age : '';
    }

    // Add event listeners to birthdate inputs
    birthMonthInput.addEventListener('input', updateBirthdate);
    birthDayInput.addEventListener('input', updateBirthdate);
    birthYearInput.addEventListener('input', updateBirthdate);

    // Auto-advance to next field when max length is reached
    birthMonthInput.addEventListener('input', function() {
      if (this.value.length === 2 && parseInt(this.value) <= 12) {
        birthDayInput.focus();
      }
    });

    birthDayInput.addEventListener('input', function() {
      if (this.value.length === 2 && parseInt(this.value) <= 31) {
        birthYearInput.focus();
      }
    });

    // Pre-calculate age on page load if birthdate exists
    if (birthdateHiddenInput.value) {
      const birthDate = new Date(birthdateHiddenInput.value);
      calculateAge(birthDate);
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
    });

    // Print Application Function
    function printApplication() {
      // Open the PDF in a new window for printing
      window.open('{{ url("/student/print-application") }}', '_blank');
    }
  </script>
</body>
</html>
