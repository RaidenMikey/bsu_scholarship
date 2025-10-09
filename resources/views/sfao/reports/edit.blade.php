@php
  use Illuminate\Support\Facades\Session;
  use Illuminate\Support\Facades\Redirect;
  use App\Models\User;

  // Redirect to login if session has ended or role mismatch
  if (!Session::has('user_id') || session('role') !== 'sfao') {
    return redirect()->route('login');
  }

  $user = User::find(session('user_id'));

  if (!$user) {
    Session::flush();
    return redirect()->route('login');
  }
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Report - SFAO Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .bg-bsu-red { background-color: #dc2626; }
        .text-bsu-red { color: #dc2626; }
        .border-bsu-red { border-color: #dc2626; }
        .focus\:ring-bsu-red:focus { --tw-ring-color: #dc2626; }
        .focus\:border-bsu-red:focus { border-color: #dc2626; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-bsu-red text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-xl font-bold">BSU Scholarship System</h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm">Welcome, {{ $user->name }}</span>
                    <a href="/sfao" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium">
                        ← Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="min-h-screen bg-gray-50">
        <div class="max-w-4xl mx-auto py-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-bsu-red rounded-full mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-bsu-red mb-2">Edit Report</h2>
                <p class="text-lg text-gray-600">
                    Modify your draft report before submission
                </p>
            </div>

            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="bg-bsu-red px-8 py-6">
                    <h3 class="text-xl font-semibold text-white">Report Information</h3>
                    <p class="text-white mt-1">Update the details below to modify your report</p>
                </div>

                <form method="POST" action="{{ route('sfao.reports.update', $report->id) }}" class="p-8 space-y-8">
                    @csrf
                    @method('PUT')
                    
                    <!-- Report Type -->
                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-bsu-red rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div>
                                <label for="report_type" class="block text-sm font-semibold text-bsu-red">
                                    Report Type <span class="text-red-500">*</span>
                                </label>
                                <p class="text-sm text-gray-500">Choose the type of report you want to create</p>
                            </div>
                        </div>
                        <select name="report_type" id="report_type" required 
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-bsu-red focus:border-bsu-red transition duration-200">
                            <option value="">Select Report Type</option>
                            <option value="monthly" {{ old('report_type', $report->report_type) == 'monthly' ? 'selected' : '' }}>📅 Monthly Report</option>
                            <option value="quarterly" {{ old('report_type', $report->report_type) == 'quarterly' ? 'selected' : '' }}>📊 Quarterly Report</option>
                            <option value="annual" {{ old('report_type', $report->report_type) == 'annual' ? 'selected' : '' }}>📈 Annual Report</option>
                            <option value="custom" {{ old('report_type', $report->report_type) == 'custom' ? 'selected' : '' }}>⚙️ Custom Report</option>
                        </select>
                        @error('report_type')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Campus Selection -->
                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-bsu-red rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div>
                                <label for="campus_id" class="block text-sm font-semibold text-bsu-red">
                                    Campus to Report On <span class="text-red-500">*</span>
                                </label>
                                <p class="text-sm text-gray-500">Select which campus you want to generate a report for</p>
                            </div>
                        </div>
                        <select name="campus_id" id="campus_id" required 
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-bsu-red focus:border-bsu-red transition duration-200">
                            <option value="">Select Campus</option>
                            @foreach($campusOptions as $campusOption)
                                <option value="{{ $campusOption->id }}" {{ old('campus_id', $report->original_campus_selection ?? $report->campus_id) == $campusOption->id ? 'selected' : '' }}>
                                    @if($campusOption->type === 'constituent_with_extensions')
                                        🏫 {{ $campusOption->name }} (Constituent + Extensions)
                                    @else
                                        🏫 {{ $campusOption->name }} ({{ ucfirst($campusOption->type) }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('campus_id')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Title -->
                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-bsu-red rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                            <div>
                                <label for="title" class="block text-sm font-semibold text-bsu-red">
                                    Report Title <span class="text-red-500">*</span>
                                </label>
                                <p class="text-sm text-gray-500">Give your report a descriptive title</p>
                            </div>
                        </div>
                        <input type="text" name="title" id="title" required value="{{ old('title', $report->title) }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-bsu-red focus:border-bsu-red transition duration-200"
                               placeholder="e.g., Scholarship Applications Report - January 2025">
                        @error('title')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-bsu-red rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <label for="description" class="block text-sm font-semibold text-bsu-red">
                                    Description
                                </label>
                                <p class="text-sm text-gray-500">Provide a brief overview of the report</p>
                            </div>
                        </div>
                        <textarea name="description" id="description" rows="4"
                                  class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-bsu-red focus:border-bsu-red transition duration-200 resize-none"
                                  placeholder="Brief description of the report...">{{ old('description', $report->description) }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Report Period -->
                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center mb-6">
                            <div class="w-10 h-10 bg-bsu-red rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-bsu-red">Report Period</h3>
                                <p class="text-sm text-gray-500">Define the time range for your report</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="report_period_start" class="block text-sm font-medium text-bsu-red mb-2">
                                    Start Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="report_period_start" id="report_period_start" required 
                                       value="{{ old('report_period_start', $report->report_period_start ? \Carbon\Carbon::parse($report->report_period_start)->format('Y-m-d') : '') }}"
                                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-bsu-red focus:border-bsu-red transition duration-200">
                                @error('report_period_start')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            <div>
                                <label for="report_period_end" class="block text-sm font-medium text-bsu-red mb-2">
                                    End Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="report_period_end" id="report_period_end" required 
                                       value="{{ old('report_period_end', $report->report_period_end ? \Carbon\Carbon::parse($report->report_period_end)->format('Y-m-d') : '') }}"
                                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-bsu-red focus:border-bsu-red transition duration-200">
                                @error('report_period_end')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Campus Info -->
                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-bsu-red rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-bsu-red">Campus Information</h3>
                                <p class="text-sm text-gray-500">Your campus details for this report</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <p class="text-sm font-medium text-gray-500">Campus Name</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $campus->name }}</p>
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <p class="text-sm font-medium text-gray-500">Campus Type</p>
                                <p class="text-lg font-semibold text-gray-900">{{ ucfirst($campus->type) }}</p>
                            </div>
                            @if($campus->extensionCampuses->count() > 0)
                            <div class="bg-white rounded-lg p-4 border border-gray-200 md:col-span-2">
                                <p class="text-sm font-medium text-gray-500">Extension Campuses</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $campus->extensionCampuses->pluck('name')->join(', ') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-bsu-red rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </div>
                            <div>
                                <label for="notes" class="block text-sm font-semibold text-bsu-red">
                                    Additional Notes
                                </label>
                                <p class="text-sm text-gray-500">Any additional information or comments</p>
                            </div>
                        </div>
                        <textarea name="notes" id="notes" rows="4"
                                  class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-bsu-red focus:border-bsu-red transition duration-200 resize-none"
                                  placeholder="Any additional notes or comments...">{{ old('notes', $report->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Submit Options -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-bsu-red rounded-lg flex items-center justify-center mr-4">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <input type="checkbox" name="submit_immediately" id="submit_immediately" value="1"
                                               class="h-5 w-5 text-bsu-red focus:ring-bsu-red border-gray-300 rounded"
                                               {{ old('submit_immediately', $report->status === 'submitted' ? '1' : '0') == '1' ? 'checked' : '' }}>
                                    </div>
                                    <div class="ml-3">
                                        <label for="submit_immediately" class="text-sm font-semibold text-bsu-red">
                                            Submit immediately to Central Administration
                                        </label>
                                        <p class="text-sm text-gray-600 mt-1">
                                            If unchecked, the report will be saved as a draft that you can edit and submit later.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                All fields marked with * are required
                            </div>
                            <div class="flex items-center space-x-4">
                                <a href="/sfao"
                                   class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Cancel
                                </a>
                                <button type="submit"
                                        class="inline-flex items-center px-8 py-3 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-bsu-red hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bsu-red transition duration-200 transform hover:scale-105">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Update Report
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const reportTypeSelect = document.getElementById('report_type');
        const startDateInput = document.getElementById('report_period_start');
        const endDateInput = document.getElementById('report_period_end');

        // Set default dates based on report type
        reportTypeSelect.addEventListener('change', function() {
            const now = new Date();
            const currentYear = now.getFullYear();
            const currentMonth = now.getMonth();
            let startDate, endDate;

            switch(this.value) {
                case 'monthly':
                    startDate = new Date(currentYear, currentMonth, 1);
                    endDate = new Date(currentYear, currentMonth + 1, 0);
                    break;
                case 'quarterly':
                    const quarter = Math.floor(currentMonth / 3);
                    startDate = new Date(currentYear, quarter * 3, 1);
                    endDate = new Date(currentYear, (quarter + 1) * 3, 0);
                    break;
                case 'annual':
                    startDate = new Date(currentYear, 0, 1);
                    endDate = new Date(currentYear, 11, 31);
                    break;
                default:
                    return;
            }

            startDateInput.value = startDate.toISOString().split('T')[0];
            endDateInput.value = endDate.toISOString().split('T')[0];
        });

        // Set default title based on report type and period
        function updateTitle() {
            const reportType = reportTypeSelect.value;
            const startDate = startDateInput.value;
            const endDate = endDateInput.value;

            if (reportType && startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                const startStr = start.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                const endStr = end.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

                let title = '';
                switch(reportType) {
                    case 'monthly':
                        title = `Monthly Scholarship Report - ${startStr}`;
                        break;
                    case 'quarterly':
                        title = `Quarterly Scholarship Report - ${startStr}`;
                        break;
                    case 'annual':
                        title = `Annual Scholarship Report - ${start.getFullYear()}`;
                        break;
                    case 'custom':
                        title = `Scholarship Report - ${startStr} to ${endStr}`;
                        break;
                }

                document.getElementById('title').value = title;
            }
        }
        
        reportTypeSelect.addEventListener('change', updateTitle);
        startDateInput.addEventListener('change', updateTitle);
        endDateInput.addEventListener('change', updateTitle);
    });
    </script>
</body>
</html>