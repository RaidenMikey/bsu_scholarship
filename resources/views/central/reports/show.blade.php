<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Details - Central Administration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'bsu-red': '#DC143C',
                        'bsu-redDark': '#B71C1C',
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full">
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
                                Back to Reports
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

            <!-- Report Data -->
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

                <!-- Application Types -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-bsu-red">Application Types</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div class="flex justify-between items-center">
                                <dt class="text-sm font-medium text-gray-500">New Applications</dt>
                                <dd class="text-lg font-semibold text-blue-600">{{ $report->report_data['application_types']['new_applications'] ?? 0 }}</dd>
                            </div>
                            <div class="flex justify-between items-center">
                                <dt class="text-sm font-medium text-gray-500">Continuing Applications</dt>
                                <dd class="text-lg font-semibold text-green-600">{{ $report->report_data['application_types']['continuing_applications'] ?? 0 }}</dd>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="--progress-width: {{ $report->report_data['application_types']['new_percentage'] ?? 0 }}%; width: var(--progress-width);"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>New: {{ $report->report_data['application_types']['new_percentage'] ?? 0 }}%</span>
                                <span>Continuing: {{ $report->report_data['application_types']['continuing_percentage'] ?? 0 }}%</span>
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

            <!-- Student Statistics -->
            <div class="mt-6 bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-bsu-red">Student Statistics</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Students</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $report->report_data['student_stats']['total_students'] ?? 0 }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Students with Applications</dt>
                            <dd class="text-2xl font-semibold text-blue-600">{{ $report->report_data['student_stats']['students_with_applications'] ?? 0 }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Application Rate</dt>
                            <dd class="text-2xl font-semibold text-green-600">{{ $report->report_data['student_stats']['application_rate'] ?? 0 }}%</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Campus Analysis -->
            @if(isset($report->report_data['campus_analysis']) && count($report->report_data['campus_analysis']) > 0)
                <div class="mt-6 bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-bsu-red">Campus Performance Analysis</h3>
                        <p class="text-sm text-gray-600">Application volume and approval rates by campus</p>
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-bsu-red uppercase tracking-wider">Status</th>
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
                                                $badgeClass = $approvalRate >= 70 ? 'bg-green-100 text-green-800' : 
                                                              ($approvalRate >= 50 ? 'bg-yellow-100 text-yellow-800' : 
                                                              'bg-red-100 text-red-800');
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                                {{ $approvalRate }}%
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if(($campus['approval_rate'] ?? 0) >= 70)
                                                <span class="text-green-600 font-medium">Good</span>
                                            @elseif(($campus['approval_rate'] ?? 0) >= 50)
                                                <span class="text-yellow-600 font-medium">Fair</span>
                                            @else
                                                <span class="text-red-600 font-medium">Needs Attention</span>
                                            @endif
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
                <div class="mt-6 bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-bsu-red">Performance Insights & Recommendations</h3>
                        <p class="text-sm text-gray-600">AI-generated insights and recommendations for improvement</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blue-600 mb-2">{{ $report->report_data['performance_insights']['performance_score'] ?? 0 }}/100</div>
                                <div class="text-sm text-gray-600">Performance Score</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-green-600 mb-2">{{ $report->report_data['performance_insights']['overall_approval_rate'] ?? 0 }}%</div>
                                <div class="text-sm text-gray-600">Overall Approval Rate</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-purple-600 mb-2">{{ $report->report_data['performance_insights']['campus_consistency'] ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-600">Campus Consistency</div>
                            </div>
                        </div>

                        @if(isset($report->report_data['performance_insights']['warnings']) && count($report->report_data['performance_insights']['warnings']) > 0)
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                                <h4 class="text-sm font-medium text-red-800 mb-3">⚠️ Warnings & Issues</h4>
                                <ul class="space-y-2">
                                    @foreach($report->report_data['performance_insights']['warnings'] as $warning)
                                        <li class="text-sm text-red-700 flex items-start">
                                            <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $warning }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(isset($report->report_data['performance_insights']['recommendations']) && count($report->report_data['performance_insights']['recommendations']) > 0)
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-blue-800 mb-3">💡 Recommendations</h4>
                                <ul class="space-y-2">
                                    @foreach($report->report_data['performance_insights']['recommendations'] as $recommendation)
                                        <li class="text-sm text-blue-700 flex items-start">
                                            <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $recommendation }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
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
                            <option value="reviewed">Mark as Reviewed</option>
                            <option value="approved">Approve Report</option>
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