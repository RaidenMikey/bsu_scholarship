<div x-show="tab === 'analytics'" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-cloak 
     x-data='sfaoStatisticsTab({ analytics: @json($analytics ?? []), campusOptions: @json($campusOptions) })'
     @tab-changed.window="handleTabChange($event.detail)">
    <div class="space-y-6">
        <!-- Header Section -->
        <!-- Header Section Removed -->

        <!-- Filter Controls -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
            <div class="flex flex-wrap gap-4 items-end">
                
                <!-- Student Type Filter (Global) -->
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider text-center">Student Type</label>
                    <div class="relative">
                        <select x-model="viewMode" @change="updateCharts()" 
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

                <!-- Department Filter (Global) -->
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider text-center">Department</label>
                    <div class="relative">
                        <select x-model="localFilters.department" 
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

                <!-- Program Filter (Global) -->
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider text-center">Program</label>
                    <div class="relative">
                    <select x-model="localFilters.program" 
                                :key="localFilters.department"
                                class="block w-full px-3 py-2 text-base border-red-500 dark:border-red-500 focus:outline-none focus:ring-bsu-red focus:border-bsu-red sm:text-sm rounded-full dark:bg-gray-700 dark:text-white text-center appearance-none"
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

                <!-- Time Period Filter -->
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider text-center">Academic Year</label>
                    <div class="relative">
                        <select x-model="filters.timePeriod" 
                                class="block w-full px-3 py-2 text-base border-red-500 dark:border-red-500 focus:outline-none focus:ring-bsu-red focus:border-bsu-red sm:text-sm rounded-full dark:bg-gray-700 dark:text-white text-center appearance-none"
                                style="border-width: 1px;">
                            <option value="all">All Time</option>
                            <template x-for="ay in academicYearOptions" :key="ay">
                                <option :value="ay" x-text="ay"></option>
                            </template>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-400">
                             <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>



                <!-- Reset Filters Icon -->


            </div>
            
            <!-- Global Legend Buttons (Row 2) -->
            <div class="mt-4 flex flex-wrap justify-between gap-4 w-full">
                 <!-- Applicants Mode Legend -->
                <template x-if="viewMode === 'applicants'">
                    <div class="flex flex-wrap justify-between w-full gap-2">
                        <!-- Approved -->
                         <button @click="chartLegend.approved = !chartLegend.approved"
                                :class="chartLegend.approved ? 'bg-green-500 text-white ring-2 ring-green-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 hover:bg-gray-200'"
                                class="flex-1 px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 focus:outline-none flex items-center justify-center shadow-sm">
                                <span class="w-2 h-2 rounded-full mr-2 bg-white" x-show="chartLegend.approved"></span>
                                Approved
                        </button>
                        <!-- Rejected -->
                        <button @click="chartLegend.rejected = !chartLegend.rejected"
                                :class="chartLegend.rejected ? 'bg-red-500 text-white ring-2 ring-red-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 hover:bg-gray-200'"
                                class="flex-1 px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 focus:outline-none flex items-center justify-center shadow-sm">
                                <span class="w-2 h-2 rounded-full mr-2 bg-white" x-show="chartLegend.rejected"></span>
                                Rejected
                        </button>
                        <!-- Pending -->
                         <button @click="chartLegend.pending = !chartLegend.pending"
                                :class="chartLegend.pending ? 'bg-yellow-500 text-white ring-2 ring-yellow-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 hover:bg-gray-200'"
                                class="flex-1 px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 focus:outline-none flex items-center justify-center shadow-sm">
                                <span class="w-2 h-2 rounded-full mr-2 bg-white" x-show="chartLegend.pending"></span>
                                Pending
                        </button>
                        <!-- In Progress -->
                        <button @click="chartLegend.inProgress = !chartLegend.inProgress"
                                :class="chartLegend.inProgress ? 'bg-blue-500 text-white ring-2 ring-blue-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 hover:bg-gray-200'"
                                class="flex-1 px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 focus:outline-none flex items-center justify-center shadow-sm">
                                <span class="w-2 h-2 rounded-full mr-2 bg-white" x-show="chartLegend.inProgress"></span>
                                In Progress
                        </button>
                    </div>
                </template>

                 <!-- Scholars Mode Legend -->
                 <template x-if="viewMode === 'scholars'">
                    <div class="flex flex-wrap justify-between w-full gap-2">
                        <!-- Old Scholars -->
                        <button @click="chartLegend.oldScholars = !chartLegend.oldScholars"
                                :class="chartLegend.oldScholars ? 'bg-green-500 text-white ring-2 ring-green-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 hover:bg-gray-200'"
                                class="flex-1 px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 focus:outline-none flex items-center justify-center shadow-sm">
                                <span class="w-2 h-2 rounded-full mr-2 bg-white" x-show="chartLegend.oldScholars"></span>
                                Old Scholars
                        </button>
                        <!-- New Scholars -->
                        <button @click="chartLegend.newScholars = !chartLegend.newScholars"
                                :class="chartLegend.newScholars ? 'bg-blue-500 text-white ring-2 ring-blue-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 hover:bg-gray-200'"
                                class="flex-1 px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 focus:outline-none flex items-center justify-center shadow-sm">
                                <span class="w-2 h-2 rounded-full mr-2 bg-white" x-show="chartLegend.newScholars"></span>
                                New Scholars
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- Analytics Charts Section -->
        
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mt-6">
            <!-- Header Section (Centered) -->
            <div class="text-center mb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white" x-text="getChartTitle()">Scholarship Status</h3>
            </div>


            <!-- Filters Section within Card -->
            <div class="flex flex-wrap items-end gap-4 mb-6 justify-center">
                 <!-- Scholarship Filter (Local) -->
                 <div class="flex-1 min-w-[300px] max-w-lg">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider text-center">Scholarship</label>
                    <div class="relative">
                        <select x-model="filters.scholarship" 
                                class="block w-full px-3 py-2 text-base border-red-500 dark:border-red-500 focus:outline-none focus:ring-bsu-red focus:border-bsu-red sm:text-sm rounded-full dark:bg-gray-700 dark:text-white text-center appearance-none"
                                style="border-width: 1px;">
                            <option value="all">All Scholarships</option>
                            <template x-for="scholarship in analyticsData.available_scholarships" :key="scholarship.id">
                                <option :value="String(scholarship.id)" x-text="scholarship.scholarship_name"></option>
                            </template>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-400">
                             <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>


            </div>

            <!-- Dynamic Summary Counts -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                 <!-- Total -->
                 <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 text-center border border-gray-100 dark:border-gray-600">
                     <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Total</p>
                     <p class="text-xl font-bold text-gray-900 dark:text-white" x-text="filteredData.counts?.total || 0"></p>
                 </div>
                 <!-- Approved -->
                 <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3 text-center border border-green-100 dark:border-green-800">
                     <p class="text-xs font-semibold text-green-600 dark:text-green-400 uppercase">Approved</p>
                     <p class="text-xl font-bold text-green-700 dark:text-green-300" x-text="filteredData.counts?.approved || 0"></p>
                 </div>
                 <!-- Rejected -->
                 <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-3 text-center border border-red-100 dark:border-red-800">
                     <p class="text-xs font-semibold text-red-600 dark:text-red-400 uppercase">Rejected</p>
                     <p class="text-xl font-bold text-red-700 dark:text-red-300" x-text="filteredData.counts?.rejected || 0"></p>
                 </div>
                 <!-- Active / Pending -->
                 <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-3 text-center border border-yellow-100 dark:border-yellow-800">
                     <p class="text-xs font-semibold text-yellow-600 dark:text-yellow-400 uppercase">Pending/In Progress</p>
                     <p class="text-xl font-bold text-yellow-700 dark:text-yellow-300" x-text="filteredData.counts?.active || 0"></p>
                 </div>
                 <!-- Rate -->
                 <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 text-center border border-blue-100 dark:border-blue-800">
                     <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase">Approval Rate</p>
                     <p class="text-xl font-bold text-blue-700 dark:text-blue-300" x-text="(filteredData.counts?.approvalRate || '0.0') + '%'"></p>
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
                <div x-show="chartStatus.department" class="h-full w-full">
                    <canvas id="sfaoDepartmentChart"></canvas>
                </div>
                <!-- No Data Message -->
                <div x-show="!chartStatus.department" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="text-center p-6 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white" 
                            x-text="viewMode === 'applicants' ? 'No Applicants Found' : 'No Scholars Found'">
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your filters.</p>
                    </div>
                </div>
            </div>

            <!-- Trend Graph (Integrated) -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <h4 class="text-md font-bold text-gray-900 dark:text-white mb-4 text-center">Trend Analysis</h4>
                <div class="relative h-64 w-full">
                    <div x-show="chartStatus.trend" class="h-full w-full">
                        <canvas id="sfaoTrendChart"></canvas>
                    </div>
                    <div x-show="!chartStatus.trend" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <p class="text-sm text-gray-500 dark:text-gray-400">No trend data available for this selection.</p>
                    </div>
                </div>
            </div>






        </div>

        <!-- Scholarship Comparison Graph (Outside Main Container) -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mt-6 mb-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 text-center">Scholarship Comparison (All Programs)</h3>
            <div class="relative h-96 w-full mb-6">
                 <div x-show="chartStatus.comparison" class="h-full w-full">
                    <canvas id="sfaoComparisonChart"></canvas>
                </div>
                 <!-- No Data Message -->
                 <div x-show="!chartStatus.comparison" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="text-center p-6 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No Comparison Data</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your filters.</p>
                    </div>
                </div>
            </div>
        </div>





        <!-- Key Metrics Overview -->
        <!-- Key Metrics Moved to Scholarship Status Container -->

        <!-- Detailed Department Statistics Table Removed -->
    </div>

</div>

    <!-- Analytics Data and Configuration passed from Controller -->
    <!-- The Alpine component 'sfaoStatisticsTab' is defined in pulic/js/sfao-script.js -->


