<div x-show="tab === 'statistics'" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-cloak 
     x-data='sfaoStatisticsTab({ analytics: @json($analytics ?? []), campusOptions: @json($campusOptions) })'
     @tab-changed.window="handleTabChange($event.detail)">
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white" x-text="getStatisticsHeader()">All Statistics</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Insights into scholarship applications and student performance for {{ $sfaoCampus->name }} and its extensions.
                </p>
            </div>
        </div>

        <!-- Filter Controls -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
            <div class="flex flex-wrap gap-4 items-end">
                
                <!-- Scholarship Filter (Replaces Global Department Filter) -->
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider text-center">Scholarship</label>
                    <div class="relative">
                        <select x-model="filters.scholarship" 
                                class="block w-full px-3 py-2 text-base border-red-500 dark:border-red-500 focus:outline-none focus:ring-bsu-red focus:border-bsu-red sm:text-sm rounded-full dark:bg-gray-700 dark:text-white text-center appearance-none"
                                style="border-width: 1px;">
                            <option value="all">All Scholarships</option>
                            <template x-for="scholarship in analyticsData.available_scholarships" :key="scholarship.id">
                                <option :value="scholarship.id" x-text="scholarship.scholarship_name"></option>
                            </template>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-400">
                             <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Time Period Filter -->
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider text-center">Time Period</label>
                    <div class="relative">
                        <select x-model="filters.timePeriod" 
                                class="block w-full px-3 py-2 text-base border-red-500 dark:border-red-500 focus:outline-none focus:ring-bsu-red focus:border-bsu-red sm:text-sm rounded-full dark:bg-gray-700 dark:text-white text-center appearance-none"
                                style="border-width: 1px;">
                            <option value="all">All Time</option>
                            <option value="this_month">This Month</option>
                            <option value="last_3_months">Last 3 Months</option>
                            <option value="this_year">This Year</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-400">
                             <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>



                <!-- Reset Filters Icon -->
                <div class="flex flex-col items-center">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider text-center">Clear</label>
                    <button @click="clearFilters()" 
                            class="inline-flex items-center justify-center p-2 border border-red-500 rounded-full shadow-sm text-gray-500 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-400 dark:border-red-500 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bsu-red transition ease-in-out duration-150 h-[38px] w-[38px]"
                            title="Reset Filters">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        <!-- Analytics Charts Section -->
        
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mt-6">
            <!-- Header Section (Centered) -->
            <div class="text-center mb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Scholarship Status</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Application status distribution by department and programs.</p>
            </div>

            <!-- Dedicated Local Filters (Full Width, Evenly Sized, Horizontal) -->
            <div class="flex flex-wrap w-full gap-4 items-end mb-6">
                
                <!-- Department Filter -->
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider text-center">Department</label>
                    <div class="relative">
                        <select x-model="localFilters.department" 
                                @change="updateProgramList()"
                                class="block w-full px-3 py-2 text-base border-red-500 dark:border-red-500 focus:outline-none focus:ring-bsu-red focus:border-bsu-red sm:text-sm rounded-full dark:bg-gray-700 dark:text-white text-center appearance-none"
                                style="border-width: 1px;">
                            <option value="all">All</option>
                            <template x-for="dept in availableDepartments" :key="dept.id">
                                <option :value="dept.short_name" x-text="dept.short_name"></option>
                            </template>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-400">
                             <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Program Filter -->
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider text-center">Program</label>
                    <div class="relative">
                        <select x-model="localFilters.program" 
                                :disabled="localFilters.department === 'all'"
                                class="block w-full px-3 py-2 text-base border-red-500 dark:border-red-500 focus:outline-none focus:ring-bsu-red focus:border-bsu-red sm:text-sm rounded-full dark:bg-gray-700 dark:text-white text-center appearance-none disabled:opacity-50 disabled:cursor-not-allowed"
                                style="border-width: 1px;">
                            <option value="all">All</option>
                            <template x-for="prog in availablePrograms" :key="prog">
                                <option :value="prog" x-text="prog"></option>
                            </template>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-400">
                             <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Student Type Filter -->
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider text-center">Student Type</label>
                    <div class="relative">
                        <select x-model="viewMode" @change="createDepartmentChart()" 
                                class="block w-full px-3 py-2 text-base border-red-500 dark:border-red-500 focus:outline-none focus:ring-bsu-red focus:border-bsu-red sm:text-sm rounded-full dark:bg-gray-700 dark:text-white text-center appearance-none"
                                style="border-width: 1px;">
                            <option value="applicants">Applicants</option>
                            <option value="scholars">Scholars</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-400">
                             <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Clear Button -->
                <div class="flex-none flex flex-col items-center">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider text-center">Clear</label>
                    <button @click="clearLocalFilters()" 
                            class="inline-flex items-center justify-center p-2 border border-red-500 rounded-full shadow-sm text-gray-500 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-400 dark:border-red-500 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bsu-red transition ease-in-out duration-150 h-[38px] w-[38px]"
                            title="Reset Local Filters">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>
                </div>

            </div>
            <!-- Gender Distribution Bar (Moved here) -->
            <div class="h-auto w-full mb-6">
                <div class="w-full">
                    <div class="flex justify-between mb-2">
                            <div class="flex items-center space-x-1">
                                <span class="text-sm font-medium text-blue-600 dark:text-blue-400">Male</span>
                                <span class="text-sm font-bold text-blue-600 dark:text-blue-300" x-text="'(' + (filteredData.genderStats?.Male || 0).toLocaleString() + ')'"></span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <span class="text-sm font-bold text-pink-600 dark:text-pink-300" x-text="'(' + (filteredData.genderStats?.Female || 0).toLocaleString() + ')'"></span>
                                <span class="text-sm font-medium text-pink-600 dark:text-pink-400">Female</span>
                            </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-8 dark:bg-gray-700 overflow-hidden flex shadow-inner">
                        <div class="bg-blue-600 h-8 flex items-center justify-center text-xs font-bold text-white transition-all duration-1000 ease-out" 
                                :style="'width: ' + getGenderPercentage('male') + '%'">
                                <span x-show="getGenderPercentage('male') > 10" x-text="getGenderPercentage('male') + '%'"></span>
                        </div>
                        <div class="bg-pink-600 h-8 flex items-center justify-center text-xs font-bold text-white transition-all duration-1000 ease-out" 
                                :style="'width: ' + getGenderPercentage('female') + '%; background-color: #EC4899;'">
                                <span x-show="getGenderPercentage('female') > 10" x-text="getGenderPercentage('female') + '%'"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Container -->
            <div class="relative h-96 w-full mb-6">
                <canvas id="sfaoDepartmentChart"></canvas>
            </div>

            <!-- Custom Legend Container -->
            <div class="flex flex-wrap justify-center gap-6">
                <!-- Applicants Mode Legend -->
                <template x-if="viewMode === 'applicants'">
                    <div class="contents">
                        <!-- Approved -->
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="chartLegend.approved" class="form-checkbox h-4 w-4 text-green-500 rounded border-gray-300 focus:ring-green-500">
                            <span class="ml-2 flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                                <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                                Approved
                            </span>
                        </label>
                        <!-- Rejected -->
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="chartLegend.rejected" class="form-checkbox h-4 w-4 text-red-500 rounded border-gray-300 focus:ring-red-500">
                            <span class="ml-2 flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                                <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                                Rejected
                            </span>
                        </label>
                        <!-- Pending -->
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="chartLegend.pending" class="form-checkbox h-4 w-4 text-yellow-500 rounded border-gray-300 focus:ring-yellow-500">
                            <span class="ml-2 flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                                <span class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></span>
                                Pending
                            </span>
                        </label>
                    </div>
                </template>

                <!-- Scholars Mode Legend -->
                <template x-if="viewMode === 'scholars'">
                    <div class="contents">
                        <!-- New Scholars -->
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="chartLegend.newScholars" class="form-checkbox h-4 w-4 text-blue-500 rounded border-gray-300 focus:ring-blue-500">
                            <span class="ml-2 flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                                <span class="w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                                New Scholars
                            </span>
                        </label>
                        <!-- Old Scholars -->
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="chartLegend.oldScholars" class="form-checkbox h-4 w-4 text-green-500 rounded border-gray-300 focus:ring-green-500">
                            <span class="ml-2 flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                                <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                                Old Scholars
                            </span>
                        </label>


                    </div>
                </template>
            </div>
        </div>



        <!-- Key Metrics Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Applications -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Applications</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white" x-text="filteredData.total_applications || 0"></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approved Applications -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Approved Applications</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white" x-text="filteredData.approved_applications || 0"></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Applications -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Pending Applications</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white" x-text="filteredData.pending_applications || 0"></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approval Rate -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-bsu-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Approval Rate</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white" x-text="(filteredData.approval_rate || 0) + '%'"></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Department Statistics Table -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Department Statistics Summary</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Students</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Applications</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Approved</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Approval Rate</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="dept in filteredData.department_stats" :key="dept.name">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white" x-text="dept.name"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300" x-text="dept.total_students"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300" x-text="dept.total_applications"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300" x-text="dept.approved_applications"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300" x-text="dept.approval_rate + '%'"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

    <!-- Analytics Data and Configuration passed from Controller -->
    <!-- The Alpine component 'sfaoStatisticsTab' is defined in pulic/js/sfao-script.js -->


