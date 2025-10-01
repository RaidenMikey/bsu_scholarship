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
    <title>View Report - SFAO Dashboard</title>
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

    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto py-8">
            <!-- Header -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
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
                                    <span>{{ $report->getPeriodDisplayName() }}</span>
                                    <span>Created {{ $report->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="mt-1 text-sm text-white">
                                    @if($report->original_campus_selection === 'constituent_with_extensions')
                                        📍 Report for: {{ $report->campus->name }} + Extensions
                                    @else
                                        📍 Report for: {{ $report->campus->name ?? 'Unknown Campus' }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            @if($report->isDraft())
                                <a href="{{ route('sfao.reports.edit', $report->id) }}"
                                   class="inline-flex items-center px-6 py-3 border border-white/30 shadow-sm text-sm font-medium rounded-lg text-white bg-white/10 hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit Report
                                </a>
                                <form method="POST" action="{{ route('sfao.reports.submit', $report->id) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-sm font-medium rounded-lg text-bsu-red bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition duration-200 transform hover:scale-105">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                        Submit to Central
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                @if($report->description)
                <div class="px-6 py-4 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-bsu-red mb-2">Description</h3>
                    <p class="text-gray-700">{{ $report->description }}</p>
                </div>
                @endif

                @if($report->notes)
                <div class="px-6 py-4 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-bsu-red mb-2">Notes</h3>
                    <p class="text-sm text-gray-600">{{ $report->notes }}</p>
                </div>
                @endif

                @if($report->central_feedback)
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-bsu-red">
                                Central Administration Feedback
                            </h3>
                            <div class="mt-1 text-sm text-yellow-700">
                                <p>{{ $report->central_feedback }}</p>
                            </div>
                            @if($report->reviewed_at)
                                <div class="mt-2 text-xs text-yellow-600">
                                    Reviewed on {{ \Carbon\Carbon::parse($report->reviewed_at)->format('M d, Y H:i A') }}
                                    @if($report->reviewer)
                                        by {{ $report->reviewer->name }}
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Summary Statistics -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
                <div class="bg-bsu-red px-8 py-6">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-white">Summary Statistics</h3>
                            <p class="text-white">Overview of application data</p>
                        </div>
                    </div>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-bsu-red">Total Applications</p>
                                    <p class="text-3xl font-bold text-gray-900">{{ $report->report_data['summary']['total_applications'] ?? 0 }}</p>
                                </div>
                                <div class="w-12 h-12 bg-bsu-red rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-bsu-red">Approved</p>
                                    <p class="text-3xl font-bold text-gray-900">{{ $report->report_data['summary']['approved_applications'] ?? 0 }}</p>
                                </div>
                                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-bsu-red">Rejected</p>
                                    <p class="text-3xl font-bold text-gray-900">{{ $report->report_data['summary']['rejected_applications'] ?? 0 }}</p>
                                </div>
                                <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-bsu-red">Pending</p>
                                    <p class="text-3xl font-bold text-gray-900">{{ $report->report_data['summary']['pending_applications'] ?? 0 }}</p>
                                </div>
                                <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-bsu-red">Claimed</p>
                                    <p class="text-3xl font-bold text-gray-900">{{ $report->report_data['summary']['claimed_applications'] ?? 0 }}</p>
                                </div>
                                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-bsu-red">Approval Rate</p>
                                    <p class="text-3xl font-bold text-gray-900">{{ $report->report_data['summary']['approval_rate'] ?? 0 }}%</p>
                                </div>
                                <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Application Types -->
            @if(isset($report->report_data['application_types']))
            <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
                <div class="bg-bsu-red px-6 py-4">
                    <h3 class="text-lg font-medium text-white">Application Types</h3>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        <div class="flex justify-between items-center">
                            <dt class="text-sm font-medium text-gray-500">New Applications</dt>
                            <dd class="text-lg font-semibold text-bsu-red">{{ $report->report_data['application_types']['new_applications'] ?? 0 }}</dd>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-bsu-red h-2 rounded-full" style="--progress-width: {{ $report->report_data['application_types']['new_percentage'] ?? 0 }}%; width: var(--progress-width);"></div>
                        </div>
                        <div class="flex justify-between items-center">
                            <dt class="text-sm font-medium text-gray-500">Continuing Applications</dt>
                            <dd class="text-lg font-semibold text-bsu-red">{{ $report->report_data['application_types']['continuing_applications'] ?? 0 }}</dd>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gray-500 h-2 rounded-full" style="--progress-width: {{ $report->report_data['application_types']['continuing_percentage'] ?? 0 }}%; width: var(--progress-width);"></div>
                        </div>
                    </dl>
                </div>
            </div>
            @endif

            <!-- Campus Performance -->
            @if(isset($report->report_data['campus_analysis']))
            <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
                <div class="bg-bsu-red px-6 py-4">
                    <h3 class="text-lg font-medium text-white">Campus Performance Analysis</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-bsu-red uppercase tracking-wider">Campus</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-bsu-red uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-bsu-red uppercase tracking-wider">Approved</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-bsu-red uppercase tracking-wider">Rejected</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-bsu-red uppercase tracking-wider">Pending</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-bsu-red uppercase tracking-wider">Approval Rate</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($report->report_data['campus_analysis'] as $campus)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $campus['campus_name'] ?? 'Unknown Campus' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $campus['total_applications'] ?? 0 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">{{ $campus['approved_applications'] ?? 0 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">{{ $campus['rejected_applications'] ?? 0 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600">{{ $campus['pending_applications'] ?? 0 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ ($campus['approval_rate'] ?? 0) >= 70 ? 'bg-green-100 text-green-800' : 
                                           (($campus['approval_rate'] ?? 0) >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $campus['approval_rate'] ?? 0 }}%
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Scholarship Distribution -->
            @if(isset($report->report_data['by_scholarship']))
            <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
                <div class="bg-bsu-red px-6 py-4">
                    <h3 class="text-lg font-medium text-white">Scholarship Distribution</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-bsu-red uppercase tracking-wider">Scholarship</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-bsu-red uppercase tracking-wider">Total Applications</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-bsu-red uppercase tracking-wider">Approved</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-bsu-red uppercase tracking-wider">Rejected</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-bsu-red uppercase tracking-wider">Pending</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-bsu-red uppercase tracking-wider">Approval Rate</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($report->report_data['by_scholarship'] as $scholarship)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $scholarship['scholarship_name'] ?? 'Unknown Scholarship' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $scholarship['total'] ?? 0 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">{{ $scholarship['approved'] ?? 0 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">{{ $scholarship['rejected'] ?? 0 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600">{{ $scholarship['pending'] ?? 0 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ ($scholarship['approval_rate'] ?? 0) >= 70 ? 'bg-green-100 text-green-800' : 
                                           (($scholarship['approval_rate'] ?? 0) >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $scholarship['approval_rate'] ?? 0 }}%
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

                <!-- Performance Insights -->
                @if(isset($report->report_data['performance_insights']))
            <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
                <div class="bg-bsu-red px-6 py-4">
                    <h3 class="text-lg font-medium text-white">Performance Insights</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <h4 class="text-sm font-medium text-bsu-red mb-2">Overall Performance Score</h4>
                            <p class="text-2xl font-bold text-gray-900">{{ $report->report_data['performance_insights']['overall_score'] ?? 0 }}/100</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <h4 class="text-sm font-medium text-bsu-red mb-2">Campus Consistency</h4>
                            <p class="text-2xl font-bold text-gray-900">{{ $report->report_data['performance_insights']['consistency_score'] ?? 0 }}%</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <h4 class="text-sm font-medium text-bsu-red mb-2">Scholarship Utilization</h4>
                            <p class="text-2xl font-bold text-gray-900">{{ $report->report_data['performance_insights']['utilization_score'] ?? 0 }}%</p>
                        </div>
                    </div>

                    @if(isset($report->report_data['performance_insights']['warnings']) && count($report->report_data['performance_insights']['warnings']) > 0)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-bsu-red mb-2">⚠️ Areas of Concern</h4>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($report->report_data['performance_insights']['warnings'] as $warning)
                            <li class="text-sm text-yellow-800">{{ $warning }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if(isset($report->report_data['performance_insights']['recommendations']) && count($report->report_data['performance_insights']['recommendations']) > 0)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-4">
                        <h4 class="text-sm font-medium text-bsu-red mb-2">💡 Recommendations</h4>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($report->report_data['performance_insights']['recommendations'] as $recommendation)
                            <li class="text-sm text-green-800">{{ $recommendation }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Report Actions -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-6 py-4">
                    <h3 class="text-lg font-medium text-bsu-red">Report Actions</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Report generated on {{ $report->created_at->format('M d, Y H:i A') }}
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('sfao.dashboard') }}?tab=reports"
                               class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Dashboard
                            </a>
                            @if($report->isDraft())
                                <a href="{{ route('sfao.reports.edit', $report->id) }}"
                                   class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-bsu-red hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bsu-red transition duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit Report
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>