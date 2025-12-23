<div x-show="tab === 'sfao-reports'" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-cloak
     x-data="{
        activeReportTab: localStorage.getItem('sfao_report_status') || 'submitted',
        setReportStatus(status) {
            this.activeReportTab = status;
            localStorage.setItem('sfao_report_status', status);
        }
     }"
     x-init="$watch('activeReportTab', value => localStorage.setItem('sfao_report_status', value))">
    <div class="space-y-6">
        
        <!-- Filters (SFAO Style) -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end mb-4">
                <input type="hidden" name="tabs" value="sfao-reports">
                
                <!-- Scholarship Filter -->
                <div class="flex-1 min-w-[200px]">
                    <label for="scholarship" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider text-center">Scholarship</label>
                    <div class="relative">
                        <select name="scholarship_filter" id="scholarship" onchange="this.form.submit()" 
                                class="block w-full px-3 py-2 text-base border-red-500 dark:border-red-500 focus:outline-none focus:ring-bsu-red focus:border-bsu-red sm:text-sm rounded-full dark:bg-gray-700 dark:text-white text-center appearance-none cursor-pointer"
                                style="border-width: 1px;">
                            <option value="all" {{ request('scholarship_filter') == 'all' ? 'selected' : '' }}>All Scholarships</option>
                            @if(isset($scholarshipOptions))
                                @foreach($scholarshipOptions as $option)
                                    <option value="{{ $option['id'] }}" {{ request('scholarship_filter') == $option['id'] ? 'selected' : '' }}>
                                        {{ $option['name'] }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-400">
                             <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Campus Filter -->
                <div class="flex-1 min-w-[200px]">
                    <label for="campus" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider text-center">Campus</label>
                    <div class="relative">
                        <select name="campus" id="campus" onchange="this.form.submit()" 
                                class="block w-full px-3 py-2 text-base border-red-500 dark:border-red-500 focus:outline-none focus:ring-bsu-red focus:border-bsu-red sm:text-sm rounded-full dark:bg-gray-700 dark:text-white text-center appearance-none cursor-pointer"
                                style="border-width: 1px;">
                            <option value="all" {{ request('campus') == 'all' ? 'selected' : '' }}>All Campuses</option>
                            @foreach($campuses as $campus)
                                <option value="{{ $campus->id }}" {{ request('campus') == $campus->id ? 'selected' : '' }}>
                                    {{ $campus->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-400">
                             <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Academic Year Filter -->
                <div class="flex-1 min-w-[200px]">
                    <label for="academic_year" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider text-center">Academic Year</label>
                    <div class="relative">
                        <select name="academic_year" id="academic_year" onchange="this.form.submit()" 
                                class="block w-full px-3 py-2 text-base border-red-500 dark:border-red-500 focus:outline-none focus:ring-bsu-red focus:border-bsu-red sm:text-sm rounded-full dark:bg-gray-700 dark:text-white text-center appearance-none cursor-pointer"
                                style="border-width: 1px;">
                            <option value="all" {{ request('academic_year') == 'all' ? 'selected' : '' }}>All Years</option>
                            @isset($academicYearOptions)
                                @foreach($academicYearOptions as $option)
                                    <option value="{{ $option }}" {{ request('academic_year') == $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-400">
                             <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Card Style Filters (Legend/Status Toggles) -->
            <div class="flex flex-wrap justify-between gap-4 w-full">
                <!-- Submitted (Yellow) -->
                <button @click.prevent="setReportStatus('submitted')"
                        :class="activeReportTab === 'submitted' ? 'bg-yellow-500 text-white ring-2 ring-yellow-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 hover:bg-gray-200'"
                        class="flex-1 px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 focus:outline-none flex items-center justify-center shadow-sm">
                        <span class="w-2 h-2 rounded-full mr-2 bg-white" x-show="activeReportTab === 'submitted'"></span>
                        Submitted <span class="ml-1 text-xs opacity-75">({{ $reportStats['pending_reports'] ?? 0 }})</span>
                </button>

                <!-- Reviewed (Blue) -->
                <button @click.prevent="setReportStatus('reviewed')"
                        :class="activeReportTab === 'reviewed' ? 'bg-blue-500 text-white ring-2 ring-blue-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 hover:bg-gray-200'"
                        class="flex-1 px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 focus:outline-none flex items-center justify-center shadow-sm">
                        <span class="w-2 h-2 rounded-full mr-2 bg-white" x-show="activeReportTab === 'reviewed'"></span>
                        Reviewed <span class="ml-1 text-xs opacity-75">({{ $reportStats['reviewed_reports'] ?? 0 }})</span>
                </button>

                <!-- Accepted (Green) -->
                <button @click.prevent="setReportStatus('approved')"
                        :class="activeReportTab === 'approved' ? 'bg-green-500 text-white ring-2 ring-green-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 hover:bg-gray-200'"
                        class="flex-1 px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 focus:outline-none flex items-center justify-center shadow-sm">
                        <span class="w-2 h-2 rounded-full mr-2 bg-white" x-show="activeReportTab === 'approved'"></span>
                        Accepted <span class="ml-1 text-xs opacity-75">({{ $reportStats['approved_reports'] ?? 0 }})</span>
                </button>

                <!-- Rejected (Red) -->
                <button @click.prevent="setReportStatus('rejected')"
                        :class="activeReportTab === 'rejected' ? 'bg-red-500 text-white ring-2 ring-red-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 hover:bg-gray-200'"
                        class="flex-1 px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 focus:outline-none flex items-center justify-center shadow-sm">
                        <span class="w-2 h-2 rounded-full mr-2 bg-white" x-show="activeReportTab === 'rejected'"></span>
                        Rejected <span class="ml-1 text-xs opacity-75">({{ $reportStats['rejected_reports'] ?? 0 }})</span>
                </button>
            </div>
            
        </div>

        <!-- Reports by Category (Dynamic Content) -->
        <div class="space-y-8">
            <!-- Submitted Reports -->
            @if($reportsSubmitted->count() > 0)
            <div x-show="activeReportTab === 'submitted'" class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($reportsSubmitted as $report)
                    <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center">
                                    <a href="{{ route('central.reports.show', $report->id) }}" class="text-lg font-medium text-gray-900 dark:text-white hover:text-bsu-red">
                                        {{ $report->title }}
                                    </a>
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </div>
                                <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400 space-x-4">
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
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bsu-red">
                                    Review
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="bg-white dark:bg-gray-800 px-6 py-4 border-t border-gray-200 dark:border-gray-600 flex justify-center">
                    {{ $reportsSubmitted->links('vendor.pagination.custom') }}
                </div>
            </div>
            @endif

            <!-- Reviewed Reports -->
            @if($reportsReviewed->count() > 0)
            <div x-show="activeReportTab === 'reviewed'" class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($reportsReviewed as $report)
                    <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center">
                                    <a href="{{ route('central.reports.show', $report->id) }}" class="text-lg font-medium text-gray-900 dark:text-white hover:text-bsu-red">
                                        {{ $report->title }}
                                    </a>
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </div>
                                <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400 space-x-4">
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
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bsu-red">
                                    View
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="bg-white dark:bg-gray-800 px-6 py-4 border-t border-gray-200 dark:border-gray-600 flex justify-center">
                    {{ $reportsReviewed->links('vendor.pagination.custom') }}
                </div>
            </div>
            @endif

            <!-- Approved Reports -->
            @if($reportsApproved->count() > 0)
            <div x-show="activeReportTab === 'approved'" class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($reportsApproved as $report)
                    <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center">
                                    <a href="{{ route('central.reports.show', $report->id) }}" class="text-lg font-medium text-gray-900 dark:text-white hover:text-bsu-red">
                                        {{ $report->title }}
                                    </a>
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </div>
                                <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400 space-x-4">
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
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bsu-red">
                                    View
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="bg-white dark:bg-gray-800 px-6 py-4 border-t border-gray-200 dark:border-gray-600 flex justify-center">
                    {{ $reportsApproved->links('vendor.pagination.custom') }}
                </div>
            </div>
            @endif

            <!-- Rejected Reports -->
            @if($reportsRejected->count() > 0)
            <div x-show="activeReportTab === 'rejected'" class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($reportsRejected as $report)
                    <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center">
                                    <a href="{{ route('central.reports.show', $report->id) }}" class="text-lg font-medium text-gray-900 dark:text-white hover:text-bsu-red">
                                        {{ $report->title }}
                                    </a>
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </div>
                                <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400 space-x-4">
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
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bsu-red">
                                    View
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="bg-white dark:bg-gray-800 px-6 py-4 border-t border-gray-200 dark:border-gray-600 flex justify-center">
                    {{ $reportsRejected->links('vendor.pagination.custom') }}
                </div>
            </div>
            @endif

            <!-- No Reports Message -->
            @if($totalReports == 0)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No reports found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your filters or check back later.</p>
            </div>
            @endif
        </div>
    </div>
</div>
