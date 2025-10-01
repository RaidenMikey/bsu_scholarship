<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central Reports - BSU Scholarship System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        :root {
            --bsu-red: #800000;
            --bsu-redDark: #660000;
        }
        .bg-bsu-red { background-color: var(--bsu-red); }
        .hover\:bg-bsu-redDark:hover { background-color: var(--bsu-redDark); }
        .focus\:ring-bsu-red:focus { --tw-ring-color: var(--bsu-red); }
        .text-bsu-red { color: var(--bsu-red); }
        .border-bsu-red { border-color: var(--bsu-red); }
        .bg-bsu-red\/10 { background-color: rgba(128, 0, 0, 0.1); }
        .border-bsu-red\/20 { border-color: rgba(128, 0, 0, 0.2); }
        [x-cloak] { display: none !important; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center">
                        <img src="{{ asset('images/Batangas_State_Logo.png') }}" alt="BSU Logo" class="h-12 w-auto mr-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Central Administration</h1>
                            <p class="text-sm text-gray-600">SFAO Reports Management</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('central.dashboard') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bsu-red">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-900">SFAO Reports</h2>
                <p class="mt-2 text-gray-600">Review and manage reports submitted by SFAO administrators across all campuses.</p>
            </div>

            <!-- Filters -->
            <div class="bg-white shadow rounded-lg p-6 mb-8">
                <form method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Status Filter -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-bsu-red focus:border-bsu-red">
                                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                                <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>

                        <!-- Type Filter -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Report Type</label>
                            <select name="type" id="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-bsu-red focus:border-bsu-red">
                                <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All Types</option>
                                <option value="monthly" {{ request('type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="quarterly" {{ request('type') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                <option value="annual" {{ request('type') == 'annual' ? 'selected' : '' }}>Annual</option>
                                <option value="custom" {{ request('type') == 'custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                        </div>

                        <!-- Campus Filter -->
                        <div>
                            <label for="campus" class="block text-sm font-medium text-gray-700">Campus</label>
                            <select name="campus" id="campus" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-bsu-red focus:border-bsu-red">
                                <option value="all" {{ request('campus') == 'all' ? 'selected' : '' }}>All Campuses</option>
                                @foreach($campuses as $campus)
                                    <option value="{{ $campus->id }}" {{ request('campus') == $campus->id ? 'selected' : '' }}>
                                        {{ $campus->name }} ({{ ucfirst($campus->type) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sort Filter -->
                        <div>
                            <label for="sort" class="block text-sm font-medium text-gray-700">Sort By</label>
                            <select name="sort" id="sort" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-bsu-red focus:border-bsu-red">
                                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Date Created</option>
                                <option value="submitted_at" {{ request('sort') == 'submitted_at' ? 'selected' : '' }}>Date Submitted</option>
                                <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title</option>
                                <option value="campus" {{ request('sort') == 'campus' ? 'selected' : '' }}>Campus</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <div class="flex space-x-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-bsu-red border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-bsu-redDark focus:bg-bsu-redDark active:bg-bsu-redDark focus:outline-none focus:ring-2 focus:ring-bsu-red focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                                </svg>
                                Apply Filters
                            </button>
                            <a href="{{ route('central.reports') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bsu-red">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Clear Filters
                            </a>
                        </div>
                        <div class="text-sm text-gray-500">
                            Showing {{ $reports->count() }} of {{ $totalReports }} reports
                        </div>
                    </div>
                </form>
            </div>

            <!-- Reports by Category -->
            <div class="space-y-8">
                <!-- Submitted Reports -->
                @if($reportsByStatus['submitted']->count() > 0)
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="bg-yellow-50 px-6 py-4 border-b border-yellow-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-medium text-yellow-800">Submitted Reports</h3>
                                    <p class="text-sm text-yellow-600">{{ $reportsByStatus['submitted']->count() }} reports awaiting review</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                {{ $reportsByStatus['submitted']->count() }} Pending
                            </span>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($reportsByStatus['submitted'] as $report)
                        <div class="px-6 py-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center">
                                        <a href="{{ route('central.reports.show', $report->id) }}" class="text-lg font-medium text-gray-900 hover:text-bsu-red">
                                            {{ $report->title }}
                                        </a>
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-500 space-x-4">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            {{ $report->campus->name }}
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ $report->getReportTypeDisplayName() }}
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Submitted {{ $report->submitted_at ? $report->submitted_at->format('M d, Y') : $report->created_at->format('M d, Y') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('central.reports.show', $report->id) }}" 
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bsu-red">
                                        Review
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Reviewed Reports -->
                @if($reportsByStatus['reviewed']->count() > 0)
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="bg-blue-50 px-6 py-4 border-b border-blue-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-medium text-blue-800">Reviewed Reports</h3>
                                    <p class="text-sm text-blue-600">{{ $reportsByStatus['reviewed']->count() }} reports under review</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ $reportsByStatus['reviewed']->count() }} Under Review
                            </span>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($reportsByStatus['reviewed'] as $report)
                        <div class="px-6 py-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center">
                                        <a href="{{ route('central.reports.show', $report->id) }}" class="text-lg font-medium text-gray-900 hover:text-bsu-red">
                                            {{ $report->title }}
                                        </a>
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-500 space-x-4">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            {{ $report->campus->name }}
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ $report->getReportTypeDisplayName() }}
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            Reviewed {{ $report->reviewed_at ? $report->reviewed_at->format('M d, Y') : 'Recently' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('central.reports.show', $report->id) }}" 
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bsu-red">
                                        View
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Approved Reports -->
                @if($reportsByStatus['approved']->count() > 0)
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="bg-green-50 px-6 py-4 border-b border-green-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-medium text-green-800">Approved Reports</h3>
                                    <p class="text-sm text-green-600">{{ $reportsByStatus['approved']->count() }} reports approved</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                {{ $reportsByStatus['approved']->count() }} Approved
                            </span>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($reportsByStatus['approved'] as $report)
                        <div class="px-6 py-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center">
                                        <a href="{{ route('central.reports.show', $report->id) }}" class="text-lg font-medium text-gray-900 hover:text-bsu-red">
                                            {{ $report->title }}
                                        </a>
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-500 space-x-4">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            {{ $report->campus->name }}
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ $report->getReportTypeDisplayName() }}
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Approved {{ $report->reviewed_at ? $report->reviewed_at->format('M d, Y') : 'Recently' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('central.reports.show', $report->id) }}" 
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bsu-red">
                                        View
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Rejected Reports -->
                @if($reportsByStatus['rejected']->count() > 0)
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="bg-red-50 px-6 py-4 border-b border-red-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-medium text-red-800">Rejected Reports</h3>
                                    <p class="text-sm text-red-600">{{ $reportsByStatus['rejected']->count() }} reports rejected</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                {{ $reportsByStatus['rejected']->count() }} Rejected
                            </span>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($reportsByStatus['rejected'] as $report)
                        <div class="px-6 py-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center">
                                        <a href="{{ route('central.reports.show', $report->id) }}" class="text-lg font-medium text-gray-900 hover:text-bsu-red">
                                            {{ $report->title }}
                                        </a>
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-500 space-x-4">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            {{ $report->campus->name }}
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ $report->getReportTypeDisplayName() }}
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Rejected {{ $report->reviewed_at ? $report->reviewed_at->format('M d, Y') : 'Recently' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('central.reports.show', $report->id) }}" 
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bsu-red">
                                        View
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- No Reports Message -->
                @if($reports->count() == 0)
                <div class="bg-white shadow rounded-lg p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No reports found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or check back later.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
