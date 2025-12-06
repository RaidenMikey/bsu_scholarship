@php
    $user = \App\Models\User::find(session('user_id'));
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50"
    :class="{ 'dark': darkMode }"
    x-data="{ darkMode: localStorage.getItem('darkMode_{{ $user->id }}') === 'true' }"
    x-init="$watch('darkMode', val => localStorage.setItem('darkMode_{{ $user->id }}', val))">
<head>
    <script>
        if (localStorage.getItem('darkMode_{{ $user->id }}') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Details - Central Administration</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="h-full">
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto py-8">
            <!-- Header -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8 no-print">
                <div class="bg-bsu-red px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white">{{ $report->title }}</h2>
                                <div class="mt-2 flex items-center space-x-4 text-sm text-white">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                    <span>{{ $report->getReportTypeDisplayName() }}</span>
                                    <span>{{ $report->campus->name ?? 'Unknown Campus' }}</span>
                                    <span>{{ $report->getPeriodDisplayName() }}</span>
                                    <span>Submitted {{ $report->submitted_at ? $report->submitted_at->format('M d, Y') : $report->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="mt-1 text-sm text-white">
                                    Submitted by {{ $report->sfaoUser->name ?? 'Unknown User' }} ({{ $report->sfaoUser->email ?? 'No email' }})
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <button onclick="window.print()" class="bg-white/10 text-white px-4 py-2 rounded-md hover:bg-white/20 flex items-center transition-colors border border-white/30">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Print
                            </button>
                        
                            @if($report->status === 'submitted')
                                <button onclick="openReviewModal()" 
                                        class="inline-flex items-center px-6 py-3 border border-white/30 shadow-sm text-sm font-medium rounded-lg text-white bg-white/10 hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Review Report
                                </button>
                            @endif
                            <a href="{{ route('central.dashboard') }}" 
                               class="inline-flex items-center px-6 py-3 border border-white/30 shadow-sm text-sm font-medium rounded-lg text-white bg-white/10 hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
                
                @if($report->description)
                    <div class="px-8 py-4">
                        <p class="text-gray-700">{{ $report->description }}</p>
                    </div>
                @endif
                
                @if($report->notes)
                    <div class="px-8 py-4 border-t border-gray-200">
                        <h3 class="text-sm font-medium text-bsu-red mb-2">SFAO Notes</h3>
                        <p class="text-sm text-gray-600">{{ $report->notes }}</p>
                    </div>
                @endif
                
                @if($report->central_feedback)
                    <div class="px-8 py-4 border-t border-gray-200">
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">
                                        Central Admin Feedback
                                    </h3>
                                    <div class="mt-1 text-sm text-blue-700">
                                        {{ $report->central_feedback }}
                                    </div>
                                    @if($report->reviewed_at)
                                        <div class="mt-2 text-xs text-blue-600">
                                            Reviewed on {{ $report->reviewed_at->format('M d, Y \a\t g:i A') }}
                                            @if($report->reviewer)
                                                by {{ $report->reviewer->name }}
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            @php
                $data = $report->report_data;
                $details = $data['details'] ?? [];
            @endphp
            
            @if(isset($data['type']))
                {{-- === NEW SUMMARY REPORT FORMATS === --}}
                <div class="bg-white shadow-lg rounded-lg overflow-hidden p-8 print:shadow-none">
                    
                    {{-- STUDENT SUMMARY --}}
                    @if($data['type'] === 'student_summary')
                        @foreach($details as $campusData)
                            <div class="campus-section break-inside-avoid mb-12 last:mb-0">
                                <h2 class="text-2xl font-bold text-bsu-red border-b-2 border-bsu-red pb-2 mb-6 uppercase">
                                    {{ $campusData['campus_name'] ?? $campusData['campus'] ?? 'Unknown Campus' }}
                                </h2>

                                @if(isset($campusData['departments']) && count($campusData['departments']) > 0)
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                        @foreach($campusData['departments'] as $deptData)
                                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 break-inside-avoid shadow-sm">
                                                <h3 class="text-lg font-semibold text-gray-800 mb-3 border-b border-gray-300 pb-2">
                                                    {{ $deptData['department_name'] ?? 'Department' }}
                                                </h3>
                                                
                                                @if(isset($deptData['students']) && count($deptData['students']) > 0)
                                                    <table class="min-w-full text-sm">
                                                        <thead>
                                                            <tr class="text-left text-gray-500 border-b border-gray-200">
                                                                <th class="pb-2 font-medium">Applicant Name</th>
                                                                <th class="pb-2 font-medium text-right">Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-100">
                                                            @foreach($deptData['students'] as $student)
                                                                <tr>
                                                                    <td class="py-2 font-medium {{ ($student['sex'] ?? '') === 'Male' ? 'text-blue-600' : (($student['sex'] ?? '') === 'Female' ? 'text-pink-600' : 'text-gray-900') }}">
                                                                        {{ $student['name'] }}
                                                                    </td>
                                                                    <td class="py-2 text-right">
                                                                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                                            {{ in_array(strtolower($student['status'] ?? ''), ['accepted', 'approved', 'claimed']) ? 'bg-green-100 text-green-800' : 
                                                                            (in_array(strtolower($student['status'] ?? ''), ['rejected', 'disapproved']) ? 'bg-red-100 text-red-800' : 
                                                                            (strtolower($student['status'] ?? '') === 'not applied' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                                                            {{ ucfirst($student['status'] ?? 'Unknown') }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                @else
                                                    <div class="flex justify-center py-4">
                                                        <span class="text-gray-400 italic text-sm">No applicants</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach

                    {{-- SCHOLAR SUMMARY --}}
                    @elseif($data['type'] === 'scholar_summary')
                        @foreach($details as $campusData)
                            <div class="campus-section break-inside-avoid mb-12 last:mb-0">
                                 <h2 class="text-2xl font-bold text-bsu-red border-b-2 border-bsu-red pb-2 mb-6 uppercase">
                                    {{ $campusData['campus_name'] ?? 'Campus' }}
                                </h2>

                                @if((count($campusData['scholars'] ?? []) > 0) || (count($campusData['non_scholars'] ?? []) > 0))
                                    <div class="space-y-6">
                                        <!-- Scholars List -->
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-800 mb-3 pl-2 border-l-4 border-bsu-red">Scholars</h3>
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year Level</th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-200">
                                                        @forelse($campusData['scholars'] ?? [] as $scholar)
                                                            <tr>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $scholar['name'] }}</td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $scholar['department'] }}</td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $scholar['year_level'] }}</td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                                        {{ $scholar['status'] }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center italic">No scholars found.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Non-Scholars List -->
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-800 mb-3 pl-2 border-l-4 border-gray-400">Non-Scholars</h3>
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year Level</th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-200">
                                                        @forelse($campusData['non_scholars'] ?? [] as $student)
                                                            <tr>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $student['name'] }}</td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student['department'] }}</td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student['year_level'] }}</td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                                        {{ $student['status'] }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center italic">No non-scholars found.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                     <p class="text-gray-500 italic">No data found for this campus.</p>
                                @endif
                            </div>
                        @endforeach

                    {{-- GRANT SUMMARY --}}
                    @elseif($data['type'] === 'grant_summary')
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Status Distribution -->
                            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Application Status Distribution</h3>
                                <div class="space-y-4">
                                    @foreach($data['status_stats'] ?? [] as $status => $count)
                                        <div>
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="text-sm font-medium text-gray-700 capitalize">{{ str_replace('_', ' ', $status) }}</span>
                                                <span class="text-sm font-bold text-gray-900">{{ $count }}</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                @php
                                                    $total = ($data['total_grants'] ?? 0) > 0 ? $data['total_grants'] : 1;
                                                    $percent = ($count / $total) * 100;
                                                    $color = match($status) {
                                                        'approved', 'claimed' => 'bg-green-600',
                                                        'rejected' => 'bg-red-600',
                                                        'pending' => 'bg-yellow-500',
                                                        default => 'bg-bsu-red'
                                                    };
                                                @endphp
                                                <div class="{{ $color }} h-2.5 rounded-full" style="width: {{ $percent }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Type Distribution -->
                            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Scholarship Type Distribution</h3>
                                <div class="space-y-4">
                                    @foreach($data['type_stats'] ?? [] as $type => $count)
                                        <div>
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="text-sm font-medium text-gray-700 capitalize">{{ str_replace('_', ' ', $type) }}</span>
                                                <span class="text-sm font-bold text-gray-900">{{ $count }}</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                @php
                                                    $total = ($data['total_grants'] ?? 0) > 0 ? $data['total_grants'] : 1;
                                                    $percent = ($count / $total) * 100;
                                                @endphp
                                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $percent }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12">
                             <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Unknown Report Format</h3>
                            <p class="mt-1 text-sm text-gray-500">The report data format is not recognized.</p>
                        </div>
                    @endif
                </div>

            @else
                {{-- === FALLBACK: GENERAL / ANALYTICS REPORT (Original Layout) === --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Summary Statistics -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-bsu-red">Summary Statistics</h3>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Applications</dt>
                                    <dd class="text-2xl font-semibold text-gray-900">{{ $report->report_data['summary']['total_applications'] ?? 0 }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Approved</dt>
                                    <dd class="text-2xl font-semibold text-green-600">{{ $report->report_data['summary']['approved_applications'] ?? 0 }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Rejected</dt>
                                    <dd class="text-2xl font-semibold text-red-600">{{ $report->report_data['summary']['rejected_applications'] ?? 0 }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Pending</dt>
                                    <dd class="text-2xl font-semibold text-yellow-600">{{ $report->report_data['summary']['pending_applications'] ?? 0 }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Claimed</dt>
                                    <dd class="text-2xl font-semibold text-blue-600">{{ $report->report_data['summary']['claimed_applications'] ?? 0 }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Approval Rate</dt>
                                    <dd class="text-2xl font-semibold text-gray-900">{{ $report->report_data['summary']['approval_rate'] ?? 0 }}%</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                </div>

                <!-- Applications by Scholarship -->
                @if(isset($report->report_data['by_scholarship']) && count($report->report_data['by_scholarship']) > 0)
                    <div class="mt-6 bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-bsu-red">Applications by Scholarship</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-bsu-red uppercase tracking-wider">Scholarship</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-bsu-red uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-bsu-red uppercase tracking-wider">Approved</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-bsu-red uppercase tracking-wider">Rejected</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-bsu-red uppercase tracking-wider">Pending</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-bsu-red uppercase tracking-wider">Claimed</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($report->report_data['by_scholarship'] as $scholarship)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $scholarship['scholarship_name'] ?? 'Unknown Scholarship' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $scholarship['total'] ?? 0 }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                                {{ $scholarship['approved'] ?? 0 }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                                {{ $scholarship['rejected'] ?? 0 }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600">
                                                {{ $scholarship['pending'] ?? 0 }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600">
                                                {{ $scholarship['claimed'] ?? 0 }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
                
                <!-- Campus Analysis -->
                @if(isset($report->report_data['campus_analysis']) && count($report->report_data['campus_analysis']) > 0)
                    <div class="mt-6 bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-bsu-red">Campus Performance Analysis</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-bsu-red uppercase tracking-wider">Campus</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-bsu-red uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-bsu-red uppercase tracking-wider">Applications</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-bsu-red uppercase tracking-wider">Approved</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-bsu-red uppercase tracking-wider">Approval Rate</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($report->report_data['campus_analysis'] as $campus)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $campus['campus_name'] ?? 'Unknown Campus' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ ucfirst($campus['campus_type'] ?? 'Unknown') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $campus['total_applications'] ?? 0 }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                                {{ $campus['approved_applications'] ?? 0 }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @php
                                                    $approvalRate = $campus['approval_rate'] ?? 0;
                                                @endphp
                                                {{ $approvalRate }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Review Modal -->
    <div id="reviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Review Report</h3>
                    <button onclick="closeReviewModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form method="POST" action="{{ route('central.reports.review', $report->id) }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Review Status
                        </label>
                        <select name="status" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="approved">Approve Report</option>
                            <option value="rejected">Reject Report</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Feedback (Optional)
                        </label>
                        <textarea name="feedback" rows="3" 
                                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Add any feedback or comments..."></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeReviewModal()" 
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Submit Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function openReviewModal() {
        document.getElementById('reviewModal').classList.remove('hidden');
    }

    function closeReviewModal() {
        document.getElementById('reviewModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('reviewModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeReviewModal();
        }
    });
    </script>
</body>
</html>