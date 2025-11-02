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
      <section>
        <h2 class="text-3xl font-bold text-red-800 mb-6 border-b-2 border-red-700 pb-2">Personal Data</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
              <label class="block font-semibold text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
              <input type="text" name="last_name" required value="{{ old('last_name', $existingApplication->last_name ?? '') }}" class="w-full border border-red-500 px-3 py-2 rounded-md">
            </div>
            <div>
              <label class="block font-semibold text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
              <input type="text" name="first_name" required value="{{ old('first_name', $existingApplication->first_name ?? '') }}" class="w-full border border-red-500 px-3 py-2 rounded-md">
            </div>
            <div>
              <label class="block font-semibold text-gray-700 mb-1">Middle Name</label>
              <input type="text" name="middle_name" value="{{ old('middle_name', $existingApplication->middle_name ?? '') }}" class="w-full border border-red-500 px-3 py-2 rounded-md">
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
            <label class="block font-semibold text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $existingApplication->email ?? '') }}" class="w-full border border-red-500 px-3 py-2 rounded-md">
          </div>

          <div>
            <label class="block font-semibold text-gray-700 mb-1">Contact Number</label>
            <input type="text" name="contact_number" value="{{ old('contact_number', $existingApplication->contact_number ?? '') }}" class="w-full border border-red-500 px-3 py-2 rounded-md">
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
            <label class="block font-semibold text-gray-700 mb-1">Citizenship</label>
            <input type="text" name="citizenship" value="{{ old('citizenship', $existingApplication->citizenship ?? '') }}" class="w-full border border-red-500 px-3 py-2 rounded-md">
          </div>

          <div>
            <label class="block font-semibold text-gray-700 mb-1">Disability</label>
            <input type="text" name="disability" placeholder="If Applicable" value="{{ old('disability', $existingApplication->disability ?? '') }}" class="w-full border border-red-500 px-3 py-2 rounded-md">
          </div>

          <div>
            <label class="block font-semibold text-gray-700 mb-1">Tribe</label>
            <input type="text" name="tribe" value="{{ old('tribe', $existingApplication->tribe ?? '') }}" class="w-full border border-red-500 px-3 py-2 rounded-md">
          </div>
        </div>
      </section>

      <!-- Academic Data Section -->
      <section class="mt-8">
        <h2 class="text-xl font-semibold text-bsu-red mb-6">Academic Data</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block mb-1 font-medium text-gray-700">SR Code</label>
            <input type="text" name="sr_code" 
              value="{{ old('sr_code', $existingApplication->sr_code ?? '') }}"
              class="w-full border border-red-500 rounded-md p-2">
          </div>

          <div>
            <label class="block mb-1 font-medium text-gray-700">Educational Level</label>
            <select name="education_level" class="w-full border border-red-500 rounded-md p-2">
                <option value="">-- Select Educational Level --</option>
                <option value="Undergraduate" {{ old('education_level', $existingApplication->education_level ?? '') == 'Undergraduate' ? 'selected' : '' }}>Undergraduate</option>
                <option value="Graduate School" {{ old('education_level', $existingApplication->education_level ?? '') == 'Graduate School' ? 'selected' : '' }}>Graduate School</option>
                <option value="Integrated School" {{ old('education_level', $existingApplication->education_level ?? '') == 'Integrated School' ? 'selected' : '' }}>Integrated School</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
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
            <select name="college_department" class="w-full border border-red-500 rounded-md p-2">
                <option value="">-- Select College/Department --</option>
                <option value="CICS" {{ old('college_department', $existingApplication->college_department ?? '') == 'CICS' ? 'selected' : '' }}>CICS (College of Information and Computing Sciences)</option>
                <option value="CTE" {{ old('college_department', $existingApplication->college_department ?? '') == 'CTE' ? 'selected' : '' }}>CTE (College of Teacher Education)</option>
                <option value="CABEIHM" {{ old('college_department', $existingApplication->college_department ?? '') == 'CABEIHM' ? 'selected' : '' }}>CABEIHM (College of Accountancy, Business, Economics and International Hospitality Management)</option>
                <option value="CAS" {{ old('college_department', $existingApplication->college_department ?? '') == 'CAS' ? 'selected' : '' }}>CAS (College of Arts and Sciences)</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
          <div>
            <label class="block mb-1 font-medium text-gray-700">Grade/Year Level</label>
            <select name="year_level" class="w-full border border-red-500 rounded-md p-2">
                <option value="">-- Select Grade/Year Level --</option>
                <option value="1st Year" {{ old('year_level', $existingApplication->year_level ?? '') == '1st Year' ? 'selected' : '' }}>1st Year</option>
                <option value="2nd Year" {{ old('year_level', $existingApplication->year_level ?? '') == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                <option value="3rd Year" {{ old('year_level', $existingApplication->year_level ?? '') == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                <option value="4th Year" {{ old('year_level', $existingApplication->year_level ?? '') == '4th Year' ? 'selected' : '' }}>4th Year</option>
                <option value="5th Year" {{ old('year_level', $existingApplication->year_level ?? '') == '5th Year' ? 'selected' : '' }}>5th Year</option>
            </select>
          </div>
          <div>
            <label class="block mb-1 font-medium text-gray-700">Campus</label>
            <input type="text" name="campus" 
              value="{{ old('campus', $existingApplication->campus ?? '') }}"
              class="w-full border border-red-500 rounded-md p-2">
          </div>
          <div>
            <label class="block mb-1 font-medium text-gray-700">Previous GWA</label>
            <input type="number" name="previous_gwa" step="0.01" min="1.00" max="5.00" 
                   placeholder="0.00"
                   value="{{ old('previous_gwa', $existingApplication->previous_gwa ?? '') }}"
                   class="w-full border border-red-500 rounded-md p-2">
        </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block mb-1 font-medium text-gray-700">Honors Received</label>
            <input type="text" name="honors_received" placeholder="If any"
              value="{{ old('honors_received', $existingApplication->honors_received ?? '') }}"
              class="w-full border border-red-500 rounded-md p-2">
          </div>
          <div>
            <label class="block mb-1 font-medium text-gray-700">Units Enrolled</label>
            <input type="number" name="units_enrolled" min="1" max="30"
              value="{{ old('units_enrolled', $existingApplication->units_enrolled ?? '') }}"
              class="w-full border border-red-500 rounded-md p-2">
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
          <div>
            <label class="block mb-1 font-medium text-gray-700">Scholarship Applied</label>
            <input type="text" name="scholarship_applied" 
              value="{{ old('scholarship_applied', $existingApplication->scholarship_applied ?? '') }}"
              class="w-full border border-red-500 rounded-md p-2">
          </div>
          <div>
            <label class="block mb-1 font-medium text-gray-700">Semester</label>
            <input type="text" name="semester" placeholder="e.g., 1st Semester"
              value="{{ old('semester', $existingApplication->semester ?? '') }}"
              class="w-full border border-red-500 rounded-md p-2">
          </div>
          <div>
            <label class="block mb-1 font-medium text-gray-700">Academic Year</label>
            <input type="text" name="academic_year" placeholder="e.g., 2025-2026"
              value="{{ old('academic_year', $existingApplication->academic_year ?? '') }}"
              class="w-full border border-red-500 rounded-md p-2">
          </div>
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

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block font-medium mb-1">Father Status</label>
              <select name="father_status" class="w-full border border-red-500 rounded-md px-3 py-2">
                <option value="">-- Select Status --</option>
                <option value="living" {{ old('father_status', $existingApplication->father_status ?? '') == 'living' ? 'selected' : '' }}>Living</option>
                <option value="deceased" {{ old('father_status', $existingApplication->father_status ?? '') == 'deceased' ? 'selected' : '' }}>Deceased</option>
              </select>
            </div>

            <div>
              <label class="block font-medium mb-1">Father's Name</label>
              <input type="text" name="father_name" value="{{ old('father_name', $existingApplication->father_name ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2">
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block font-medium mb-1">Father's Address</label>
              <input type="text" name="father_address" value="{{ old('father_address', $existingApplication->father_address ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2">
            </div>

            <div>
              <label class="block font-medium mb-1">Father's Contact</label>
              <input type="text" name="father_contact" value="{{ old('father_contact', $existingApplication->father_contact ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2">
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block font-medium mb-1">Father's Occupation</label>
              <input type="text" name="father_occupation" value="{{ old('father_occupation', $existingApplication->father_occupation ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2">
            </div>

            <div>
              <label class="block font-medium mb-1">Father's Income Bracket</label>
              <input type="text" name="father_income_bracket" value="{{ old('father_income_bracket', $existingApplication->father_income_bracket ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2">
            </div>
          </div>
        </div>

        <!-- Mother Section -->
        <div class="border border-gray-300 rounded-lg p-4 mb-6">
          <h3 class="text-lg font-semibold text-gray-700 mb-2">Mother's Information</h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block font-medium mb-1">Mother Status</label>
              <select name="mother_status" class="w-full border border-red-500 rounded-md px-3 py-2">
                <option value="">-- Select Status --</option>
                <option value="living" {{ old('mother_status', $existingApplication->mother_status ?? '') == 'living' ? 'selected' : '' }}>Living</option>
                <option value="deceased" {{ old('mother_status', $existingApplication->mother_status ?? '') == 'deceased' ? 'selected' : '' }}>Deceased</option>
              </select>
            </div>

            <div>
              <label class="block font-medium mb-1">Mother's Name</label>
              <input type="text" name="mother_name" value="{{ old('mother_name', $existingApplication->mother_name ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2">
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block font-medium mb-1">Mother's Address</label>
              <input type="text" name="mother_address" value="{{ old('mother_address', $existingApplication->mother_address ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2">
            </div>

            <div>
              <label class="block font-medium mb-1">Mother's Contact</label>
              <input type="text" name="mother_contact" value="{{ old('mother_contact', $existingApplication->mother_contact ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2">
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block font-medium mb-1">Mother's Occupation</label>
              <input type="text" name="mother_occupation" value="{{ old('mother_occupation', $existingApplication->mother_occupation ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2">
            </div>

            <div>
              <label class="block font-medium mb-1">Mother's Income Bracket</label>
              <input type="text" name="mother_income_bracket" value="{{ old('mother_income_bracket', $existingApplication->mother_income_bracket ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2">
            </div>
          </div>
        </div>

        <div class="mb-4">
          <label class="block font-medium mb-1">Number of Siblings</label>
          <input type="number" name="siblings_count" value="{{ old('siblings_count', $existingApplication->siblings_count ?? '') }}" class="w-full border border-red-500 rounded-md px-3 py-2">
        </div>
      </section>

      <!-- Essay / Question Section -->
      <section class="mt-8">
        <h2 class="text-xl font-semibold text-bsu-red mb-6">Essay / Question</h2>
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
