<div x-show="tab === 'statistics'" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-cloak 
     x-data="statisticsTab()">
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Comprehensive Analytics Dashboard</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Complete insights into scholarship management, applications, reports, and campus performance across all campuses.
                </p>
            </div>
            <div class="mt-4 sm:mt-0">
                <button @click="refreshAnalytics()" 
                        class="inline-flex items-center px-4 py-2 bg-bsu-red border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-bsu-redDark focus:bg-bsu-redDark active:bg-bsu-redDark focus:outline-none focus:ring-2 focus:ring-bsu-red focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh Data
                </button>
            </div>
        </div>

        <!-- Filter Controls -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Filter Controls</h3>
                <div class="flex space-x-3">
                <button @click="clearFilters()" 
                        class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition duration-150 text-sm">
                    Clear All Filters
                </button>
                    <button @click="applyFilters()" 
                            class="px-4 py-2 bg-bsu-red text-white rounded-md hover:bg-bsu-redDark transition duration-150 text-sm">
                        Apply Filters
                </button>
            </div>
                </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Campus Filter -->
                <div class="space-y-2">
                    <label class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                        <svg class="w-4 h-4 mr-2 text-bsu-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Campus
                    </label>
                    <select x-model="filters.campus" 
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-bsu-red focus:border-bsu-red dark:bg-gray-700 dark:text-white">
                        <option value="all">All Campuses</option>
                        @if(isset($campuses))
                            @foreach($campuses as $campus)
                            <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Time Period Filter -->
                <div class="space-y-2">
                    <label class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                        <svg class="w-4 h-4 mr-2 text-bsu-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Time Period
                    </label>
                    <select x-model="filters.timePeriod" 
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-bsu-red focus:border-bsu-red dark:bg-gray-700 dark:text-white">
                        <option value="all">All Time</option>
                        <option value="this_month">This Month</option>
                        <option value="last_3_months">Last 3 Months</option>
                        <option value="this_year">This Year</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Analytics Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Application Status Chart -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="text-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Application Status</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400" x-text="getCurrentCampusName()"></p>
                        </div>
            <div class="h-96 flex items-center justify-center">
                    <canvas id="applicationStatusChart" width="400" height="400"></canvas>
                        </div>
                
                <!-- Application Status Data Display -->
                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex flex-wrap gap-4 text-sm justify-center">
                        <div class="flex items-center gap-2">
                            <span class="text-blue-600 dark:text-blue-400">Applied:</span>
                            <span class="font-medium" x-text="getChartDataForType('application_status').datasets[0].data[0] || 0"></span>
                    </div>
                        <div class="flex items-center gap-2">
                            <span class="text-gray-600 dark:text-gray-400">Not Applied:</span>
                            <span class="font-medium" x-text="getChartDataForType('application_status').datasets[0].data[1] || 0"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-orange-600 dark:text-orange-400">Pending:</span>
                            <span class="font-medium" x-text="getChartDataForType('application_status').datasets[0].data[2] || 0"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-green-600 dark:text-green-400">Approved:</span>
                            <span class="font-medium" x-text="getChartDataForType('application_status').datasets[0].data[3] || 0"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-red-600 dark:text-red-400">Rejected:</span>
                            <span class="font-medium" x-text="getChartDataForType('application_status').datasets[0].data[4] || 0"></span>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Gender Chart -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="text-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Gender Distribution</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400" x-text="getCurrentCampusName()"></p>
                </div>
                <div class="h-96 flex items-center justify-center">
                    <canvas id="genderChart" width="400" height="400"></canvas>
                </div>
                
                <!-- Gender Data Display -->
                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex flex-wrap gap-4 text-sm justify-center">
                        <div class="flex items-center gap-2">
                            <span class="text-blue-600 dark:text-blue-400">Male:</span>
                            <span class="font-medium" x-text="getChartDataForType('gender').datasets[0].data[0] || 0"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-pink-600 dark:text-pink-400">Female:</span>
                            <span class="font-medium" x-text="getChartDataForType('gender').datasets[0].data[1] || 0"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Year Level Chart -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="text-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Year Level Distribution</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400" x-text="getCurrentCampusName()"></p>
                </div>
                <div class="h-96 flex items-center justify-center">
                    <canvas id="yearLevelChart" width="400" height="400"></canvas>
                </div>
                
                <!-- Year Level Data Display -->
                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex flex-wrap gap-4 text-sm justify-center">
                        <div class="flex items-center gap-2">
                            <span class="text-green-600 dark:text-green-400">1st Year:</span>
                            <span class="font-medium" x-text="getChartDataForType('year_level').datasets[0].data[0] || 0"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-blue-600 dark:text-blue-400">2nd Year:</span>
                            <span class="font-medium" x-text="getChartDataForType('year_level').datasets[0].data[1] || 0"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-orange-600 dark:text-orange-400">3rd Year:</span>
                            <span class="font-medium" x-text="getChartDataForType('year_level').datasets[0].data[2] || 0"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-red-600 dark:text-red-400">4th Year:</span>
                            <span class="font-medium" x-text="getChartDataForType('year_level').datasets[0].data[3] || 0"></span>
                        </div>
                    </div>
                </div>
            </div>
            </div>

        <!-- Additional Analytics Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Monthly Application Trend Chart -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="text-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Monthly Application Trend</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400" x-text="getCurrentCampusName()"></p>
                </div>
                
                <div class="h-96 flex items-center justify-center">
                    <canvas id="monthlyTrendChart" width="400" height="400"></canvas>
                </div>
                
                <!-- Data Insights Section -->
                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">📊 Month-to-Month Comparison</h4>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Current Month -->
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border">
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                <span class="w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                                <span x-text="getMonthlyComparison().currentMonthName"></span>
                            </h5>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Total Applications:</span>
                                    <span class="font-medium" x-text="getMonthlyComparison().current.total"></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Approved:</span>
                                    <span class="font-medium text-green-600" x-text="getMonthlyComparison().current.approved"></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Rejected:</span>
                                    <span class="font-medium text-red-600" x-text="getMonthlyComparison().current.rejected"></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Pending:</span>
                                    <span class="font-medium text-orange-600" x-text="getMonthlyComparison().current.pending"></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Approval Rate:</span>
                                    <span class="font-medium" x-text="getMonthlyComparison().current.approvalRate + '%'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Previous Month -->
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border">
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                <span class="w-3 h-3 bg-gray-400 rounded-full mr-2"></span>
                                <span x-text="getMonthlyComparison().previousMonthName"></span>
                            </h5>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Total Applications:</span>
                                    <span class="font-medium" x-text="getMonthlyComparison().previous.total"></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Approved:</span>
                                    <span class="font-medium text-green-600" x-text="getMonthlyComparison().previous.approved"></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Rejected:</span>
                                    <span class="font-medium text-red-600" x-text="getMonthlyComparison().previous.rejected"></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Pending:</span>
                                    <span class="font-medium text-orange-600" x-text="getMonthlyComparison().previous.pending"></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Approval Rate:</span>
                                    <span class="font-medium" x-text="getMonthlyComparison().previous.approvalRate + '%'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Summary Metrics -->
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div class="text-center">
                                <div class="text-gray-600 dark:text-gray-400 mb-1">Volume Change</div>
                                <div class="font-semibold" :class="getMonthlyComparison().volumeChange >= 0 ? 'text-green-600' : 'text-red-600'" 
                                     x-text="getMonthlyComparison().volumeChange + '%'"></div>
                            </div>
                            <div class="text-center">
                                <div class="text-gray-600 dark:text-gray-400 mb-1">Processing Rate</div>
                                <div class="font-semibold" x-text="getMonthlyComparison().processingRate + '%'"></div>
                            </div>
                            <div class="text-center">
                                <div class="text-gray-600 dark:text-gray-400 mb-1">Status</div>
                                <div class="font-semibold" :class="getMonthlyComparison().status === 'Good' ? 'text-green-600' : 
                                      getMonthlyComparison().status === 'Warning' ? 'text-orange-600' : 'text-red-600'" 
                                     x-text="getMonthlyComparison().status"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campus Performance Comparison Chart -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="text-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Campus Performance Comparison</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Application rates across campuses</p>
                </div>
                <div class="h-96 flex items-center justify-center">
                    <canvas id="campusComparisonChart" width="400" height="400"></canvas>
                </div>
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
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $analytics['total_applications'] ?? 0 }}</dd>
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
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $analytics['approved_applications'] ?? 0 }}</dd>
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
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $analytics['pending_applications'] ?? 0 }}</dd>
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
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Overall Approval Rate</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $analytics['overall_approval_rate'] ?? 0 }}%</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Statistics Charts -->

        <!-- Scholarship Performance -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Scholarship Performance Analysis</h3>
            <div class="h-64">
                <canvas id="scholarshipPerformanceChart"></canvas>
            </div>
        </div>

        <!-- Detailed Statistics Table -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Campus Statistics Summary</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Campus</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Students</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Applications</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Approved</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Approval Rate</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($analytics['campus_application_stats'] ?? [] as $campus)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $campus['campus_name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $campus['total_students'] ?? 0 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $campus['total_applications'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $campus['approved_applications'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $campus['approval_rate'] }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Analytics Data -->
    <div id="analytics-data" data-analytics='@json($analytics ?? [])' style="display: none;"></div>
    
    <script>
        // Fallback initialization for charts
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, checking for Chart.js...');
            if (typeof Chart !== 'undefined') {
                console.log('Chart.js is available');
                // Try to create a simple chart as fallback
                setTimeout(() => {
                    const ctx = document.getElementById('filteredPieChart');
                    if (ctx && !ctx.chart) {
                        console.log('Creating fallback chart...');
                        new Chart(ctx, {
                            type: 'pie',
                            data: {
                                labels: ['Sample Data'],
                                datasets: [{
                                    data: [100],
                                    backgroundColor: ['#3B82F6']
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false
                            }
                        });
                    }
                }, 500);
            } else {
                console.log('Chart.js not loaded yet');
            }
        });
        
        // Initialize charts when tab is active
        document.addEventListener('alpine:init', () => {
            Alpine.data('statisticsTab', () => ({
                charts: {
                    applicationStatus: null,
                    gender: null,
                    yearLevel: null,
                    monthlyTrend: null,
                    campusComparison: null,
                    scholarshipPerformance: null
                },
                analyticsData: JSON.parse(document.getElementById('analytics-data').dataset.analytics || '{}'),
                filteredData: {},
                previousCampus: 'all', // Track previous campus for transition detection
                filters: {
                    analysisType: 'application_status',
                    campus: 'all',
                    timePeriod: 'all'
                },
                
                init() {
                    console.log('Statistics tab initialized');
                    console.log('Analytics data:', this.analyticsData);
                    this.updateFilteredData();
                    
                    // Watch for campus filter changes to update charts
                    this.$watch('filters.campus', () => {
                        console.log('Campus filter changed, updating charts...');
                        this.$nextTick(() => {
                            // Update MAT chart
                            this.createMonthlyTrendChart();
                            // M2M comparison will update automatically since it uses this.filters.campus
                        });
                    });
                    
                    // Initialize all charts after data is loaded
                    this.$nextTick(() => {
                        setTimeout(() => {
                            this.createAllCharts();
                            this.createScholarshipPerformanceChart();
                        }, 100);
                    });
                },
                
                updateFilteredData() {
                    // Get filtered data based on current selections
                    this.filteredData = this.getFilteredData();
                },
                
                getFilteredData() {
                    let data = {
                        total: 0,
                        applied: 0,
                        not_applied: 0,
                        pending: 0,
                        approved: 0,
                        rejected: 0,
                        new: 0,
                        renewal: 0,
                        male: 0,
                        female: 0,
                        year_levels: {}
                    };
                    
                    // Apply campus filter
                    let campusData = this.analyticsData;
                    if (this.filters.campus !== 'all') {
                        // Filter by specific campus
                        campusData = this.getCampusData(this.filters.campus);
                    }
                    
                    // Apply analysis type
                    switch(this.filters.analysisType) {
                        case 'application_status':
                            if (campusData) {
                                data = {
                                    total: campusData.total_students || 0,
                                    applied: campusData.students_with_applications || 0,
                                    not_applied: (campusData.total_students || 0) - (campusData.students_with_applications || 0),
                                    pending: campusData.pending_applications || 0,
                                    approved: campusData.approved_applications || 0,
                                    rejected: campusData.rejected_applications || 0
                                };
                            } else {
                                data = {
                                    total: this.analyticsData.total_students || 0,
                                    applied: this.analyticsData.students_with_applications || 0,
                                    not_applied: (this.analyticsData.total_students || 0) - (this.analyticsData.students_with_applications || 0),
                                    pending: this.analyticsData.pending_applications || 0,
                                    approved: this.analyticsData.approved_applications || 0,
                                    rejected: this.analyticsData.rejected_applications || 0
                                };
                            }
                            break;
                        case 'gender':
                            if (campusData) {
                                data = {
                                    total: campusData.total_students || 0,
                                    male: campusData.male_students || 0,
                                    female: campusData.female_students || 0
                                };
                            } else {
                                data = {
                                    total: this.analyticsData.total_students || 0,
                                    male: this.analyticsData.male_students || 0,
                                    female: this.analyticsData.female_students || 0
                                };
                            }
                            break;
                        case 'year_level':
                            if (campusData) {
                                data = {
                                    total: campusData.total_students || 0,
                                    year_levels: campusData.year_level_counts || []
                                };
                            } else {
                                data = {
                                    total: this.analyticsData.total_students || 0,
                                    year_levels: this.analyticsData.year_level_counts || []
                                };
                            }
                            break;
                    }
                    
                    return data;
                },
                
                getCampusData(campusId) {
                    // Get data for specific campus
                    const campusStats = this.analyticsData.campus_application_stats || [];
                    const campus = campusStats.find(c => c.campus_id == campusId);
                    return campus || {};
                },
                
                getChartTitle() {
                    let title = 'Student Analysis';
                    let campusName = this.getCampusName();
                    if (campusName) {
                        title += ' - ' + campusName;
                    }
                    return title;
                },
                
                getChartDescription() {
                    let desc = 'Showing ' + this.filters.analysisType.replace('_', ' ') + ' data';
                    let campusName = this.getCampusName();
                    if (campusName) {
                        desc += ' for ' + campusName;
                    }
                    return desc;
                },
                
                getCampusName() {
                    if (this.filters.campus === 'all') return null;
                    const campusStats = this.analyticsData.campus_application_stats || [];
                    const campus = campusStats.find(c => c.campus_id == this.filters.campus);
                    return campus ? campus.campus_name : null;
                },
                
                applyFilters() {
                    console.log('🔥 Apply Filters button clicked!');
                    console.log('Current filters:', this.filters);
                    
                    // Show loading state
                    this.showLoadingState();
                    
                    // Fetch fresh data with current filters
                    this.fetchFilteredData().then(() => {
                        console.log('Data updated, creating all charts...');
                        
                        // Wait for data to be fully processed
                        this.$nextTick(() => {
                            setTimeout(() => {
                                console.log('Creating all charts with filters:', this.filters);
                                this.createAllCharts();
                                this.hideLoadingState();
                            }, 200);
                        });
                    }).catch(error => {
                        console.error('Error updating data:', error);
                        // Still try to create charts with existing data
                        this.$nextTick(() => {
                            setTimeout(() => {
                                console.log('Creating charts with existing data');
                                this.createAllCharts();
                                this.hideLoadingState();
                            }, 200);
                        });
                    });
                },
                
                showLoadingState() {
                    // Add loading indicator to Apply Filters button
                    const button = document.querySelector('[\\@click="applyFilters()"]');
                    if (button) {
                        button.disabled = true;
                        button.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Applying...';
                    }
                },
                
                hideLoadingState() {
                    // Remove loading indicator from Apply Filters button
                    const button = document.querySelector('[\\@click="applyFilters()"]');
                    if (button) {
                        button.disabled = false;
                        button.innerHTML = 'Apply Filters';
                    }
                },
                
                createAllCharts() {
                    console.log('Creating all charts...');
                    this.createApplicationStatusChart();
                    this.createGenderChart();
                    this.createYearLevelChart();
                    this.createMonthlyTrendChart();
                    this.createCampusComparisonChart();
                },
                
                createApplicationStatusChart() {
                    const ctx = document.getElementById('applicationStatusChart');
                    if (!ctx) return;
                    
                    // Destroy existing chart
                    if (this.charts.applicationStatus) {
                        this.charts.applicationStatus.destroy();
                        this.charts.applicationStatus = null;
                    }
                    
                    // Get data for application status
                    const chartData = this.getChartDataForType('application_status');
                    
                    this.charts.applicationStatus = new Chart(ctx, {
                        type: 'pie',
                        data: chartData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: { duration: 300 },
                            plugins: {
                                legend: { position: 'bottom' },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.label || '';
                                            let value = context.parsed;
                                            let total = context.dataset.data.reduce((a, b) => a + (typeof b === 'number' ? b : 0), 0);
                                            let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                            return label + ': ' + value + ' (' + percentage + '%)';
                                        }
                                    }
                                }
                            }
                        }
                    });
                },
                
                createGenderChart() {
                    const ctx = document.getElementById('genderChart');
                    if (!ctx) return;
                    
                    // Destroy existing chart
                    if (this.charts.gender) {
                        this.charts.gender.destroy();
                        this.charts.gender = null;
                    }
                    
                    // Get data for gender
                    const chartData = this.getChartDataForType('gender');
                    
                    this.charts.gender = new Chart(ctx, {
                        type: 'pie',
                        data: chartData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: { duration: 300 },
                            plugins: {
                                legend: { position: 'bottom' },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.label || '';
                                            let value = context.parsed;
                                            let total = context.dataset.data.reduce((a, b) => a + (typeof b === 'number' ? b : 0), 0);
                                            let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                            return label + ': ' + value + ' (' + percentage + '%)';
                                        }
                                    }
                                }
                            }
                        }
                    });
                },
                
                createYearLevelChart() {
                    const ctx = document.getElementById('yearLevelChart');
                    if (!ctx) return;
                    
                    // Destroy existing chart
                    if (this.charts.yearLevel) {
                        this.charts.yearLevel.destroy();
                        this.charts.yearLevel = null;
                    }
                    
                    // Get data for year level
                    const chartData = this.getChartDataForType('year_level');
                    
                    this.charts.yearLevel = new Chart(ctx, {
                        type: 'pie',
                        data: chartData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: { duration: 300 },
                            plugins: {
                                legend: { position: 'bottom' },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.label || '';
                                            let value = context.parsed;
                                            let total = context.dataset.data.reduce((a, b) => a + (typeof b === 'number' ? b : 0), 0);
                                            let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                            return label + ': ' + value + ' (' + percentage + '%)';
                                        }
                                    }
                                }
                            }
                        }
                    });
                },
                
                getChartDataForType(analysisType) {
                    let labels = [];
                    let data = [];
                    let backgroundColor = [];
                    
                    const campus = this.filters.campus;
                    const campusData = campus !== 'all' ? 
                        this.analyticsData.campus_application_stats?.find(c => c.campus_id == campus) : null;
                    
                    switch (analysisType) {
                        case 'application_status':
                            labels = ['Applied', 'Not Applied', 'Pending', 'Approved', 'Rejected'];
                            backgroundColor = ['#3B82F6', '#6B7280', '#F59E0B', '#10B981', '#EF4444'];
                            if (campusData) {
                                data = [
                                    campusData.total_applications || 0,
                                    (campusData.total_students || 0) - (campusData.total_applications || 0),
                                    campusData.pending_applications || 0,
                                    campusData.approved_applications || 0,
                                    campusData.rejected_applications || 0
                                ];
                            } else {
                                data = [
                                    this.analyticsData.total_applications || 0,
                                    (this.analyticsData.total_students || 0) - (this.analyticsData.total_applications || 0),
                                    this.analyticsData.pending_applications || 0,
                                    this.analyticsData.approved_applications || 0,
                                    this.analyticsData.rejected_applications || 0
                                ];
                            }
                            break;
                            
                            
                        case 'gender':
                            labels = ['Male', 'Female'];
                            backgroundColor = ['#3B82F6', '#EC4899'];
                            if (campusData) {
                                data = [
                                    campusData.male_students || 0,
                                    campusData.female_students || 0
                                ];
                            } else {
                                data = [
                                    this.analyticsData.male_students || 0,
                                    this.analyticsData.female_students || 0
                                ];
                            }
                            break;
                            
                        case 'year_level':
                            if (campusData) {
                                labels = campusData.year_level_labels || ['1st Year', '2nd Year', '3rd Year', '4th Year'];
                                data = campusData.year_level_counts || [];
                            } else {
                                labels = this.analyticsData.year_level_labels || ['1st Year', '2nd Year', '3rd Year', '4th Year'];
                                data = this.analyticsData.year_level_counts || [];
                            }
                            backgroundColor = ['#10B981', '#3B82F6', '#F59E0B', '#EF4444'];
                            break;
                    }
                    
                    // Ensure we have valid data
                    if (!data || data.length === 0 || data.every(val => val === 0)) {
                        labels = ['No Data Available'];
                        data = [1];
                        backgroundColor = ['#6B7280'];
                    }
                    
                    return {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: backgroundColor,
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    };
                },
                
                createMonthlyTrendChart() {
                    const ctx = document.getElementById('monthlyTrendChart');
                    if (!ctx) return;
                    
                    // Destroy existing chart
                    if (this.charts.monthlyTrend) {
                        this.charts.monthlyTrend.destroy();
                        this.charts.monthlyTrend = null;
                    }
                    
                    // Get monthly trend data
                    const chartData = this.getMonthlyTrendData();
                    
                    this.charts.monthlyTrend = new Chart(ctx, {
                        type: 'line',
                        data: chartData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: { duration: 300 },
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            plugins: {
                                legend: { 
                                    position: 'top',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20
                                    }
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    titleColor: 'white',
                                    bodyColor: 'white',
                                    borderColor: 'rgba(255, 255, 255, 0.1)',
                                    borderWidth: 1
                                }
                            },
                            scales: {
                                x: {
                                    display: true,
                                    title: {
                                        display: true,
                                        text: 'Month',
                                        font: {
                                            weight: 'bold'
                                        }
                                    },
                                    grid: {
                                        display: false
                                    }
                                },
                                y: {
                                    display: true,
                                    title: {
                                        display: true,
                                        text: 'Number of Applications',
                                        font: {
                                            weight: 'bold'
                                        }
                                    },
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.1)'
                                    }
                                }
                            }
                        }
                    });
                },
                
                createCampusComparisonChart() {
                    const ctx = document.getElementById('campusComparisonChart');
                    if (!ctx) return;
                    
                    // Destroy existing chart
                    if (this.charts.campusComparison) {
                        this.charts.campusComparison.destroy();
                        this.charts.campusComparison = null;
                    }
                    
                    // Get campus comparison data
                    const chartData = this.getCampusComparisonData();
                    
                    this.charts.campusComparison = new Chart(ctx, {
                        type: 'bar',
                        data: chartData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: { duration: 300 },
                            plugins: {
                                legend: { position: 'top' },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                }
                            },
                            scales: {
                                x: {
                                    display: true,
                                    title: {
                                        display: true,
                                        text: 'Campus'
                                    }
                                },
                                y: {
                                    display: true,
                                    title: {
                                        display: true,
                                        text: 'Number of Applications'
                                    },
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                },
                
                getMonthlyTrendData() {
                    // Use real monthly data from analyticsData
                    // Backend provides: monthly_labels, monthly_applications, monthly_approved_applications, monthly_rejected_applications
                    const monthlyLabels = this.analyticsData.monthly_labels || [];
                    const monthlyApplications = this.analyticsData.monthly_applications || [];
                    const monthlyApprovedApplications = this.analyticsData.monthly_approved_applications || [];
                    const monthlyRejectedApplications = this.analyticsData.monthly_rejected_applications || [];
                    
                    // Check if we have campus-specific data
                    const campus = this.filters.campus;
                    let campusData = null;
                    
                    if (campus !== 'all') {
                        // Get campus-specific monthly data
                        campusData = this.analyticsData.campus_application_stats?.find(c => c.campus_id == campus);
                    }
                    
                    // Use campus-specific data if available, otherwise use global data
                    let finalMonthlyApplications, finalMonthlyApprovedApplications, finalMonthlyRejectedApplications;
                    
                    if (campusData && campusData.monthly_trends) {
                        // Use campus-specific monthly trends
                        const campusMonthlyData = campusData.monthly_trends;
                        finalMonthlyApplications = monthlyLabels.map(month => {
                            const monthKey = month.toLowerCase();
                            return campusMonthlyData[monthKey]?.total_applications || 0;
                        });
                        finalMonthlyApprovedApplications = monthlyLabels.map(month => {
                            const monthKey = month.toLowerCase();
                            return campusMonthlyData[monthKey]?.approved_applications || 0;
                        });
                        finalMonthlyRejectedApplications = monthlyLabels.map(month => {
                            const monthKey = month.toLowerCase();
                            return campusMonthlyData[monthKey]?.rejected_applications || 0;
                        });
                    } else {
                        // Use global monthly data
                        finalMonthlyApplications = monthlyApplications;
                        finalMonthlyApprovedApplications = monthlyApprovedApplications;
                        finalMonthlyRejectedApplications = monthlyRejectedApplications;
                    }
                    
                    // Calculate pending applications (total - approved - rejected)
                    const monthlyPendingApplications = finalMonthlyApplications.map((total, index) => {
                        const approved = finalMonthlyApprovedApplications[index] || 0;
                        const rejected = finalMonthlyRejectedApplications[index] || 0;
                        return Math.max(0, total - approved - rejected);
                    });
                    
                    return {
                        labels: monthlyLabels,
                        datasets: [
                            {
                                label: 'Total Applications',
                                data: finalMonthlyApplications,
                                borderColor: '#3B82F6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                borderWidth: 3,
                                fill: false,
                                tension: 0.4
                            },
                            {
                                label: 'Approved',
                                data: finalMonthlyApprovedApplications,
                                borderColor: '#10B981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                borderWidth: 2,
                                fill: false,
                                tension: 0.4
                            },
                            {
                                label: 'Rejected',
                                data: finalMonthlyRejectedApplications,
                                borderColor: '#EF4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                borderWidth: 2,
                                fill: false,
                                tension: 0.4
                            },
                            {
                                label: 'Pending',
                                data: monthlyPendingApplications,
                                borderColor: '#F59E0B',
                                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                borderWidth: 2,
                                fill: false,
                                tension: 0.4
                            }
                        ]
                    };
                },
                
                getMonthlyComparison() {
                    // Get current and previous month data from real analytics data
                    // Backend provides arrays: monthly_labels, monthly_applications, monthly_approved_applications, monthly_rejected_applications
                    const monthlyLabels = this.analyticsData.monthly_labels || [];
                    const monthlyApplications = this.analyticsData.monthly_applications || [];
                    const monthlyApprovedApplications = this.analyticsData.monthly_approved_applications || [];
                    const monthlyRejectedApplications = this.analyticsData.monthly_rejected_applications || [];
                    
                    // Check if we have campus-specific data
                    const campus = this.filters.campus;
                    let campusData = null;
                    
                    if (campus !== 'all') {
                        // Get campus-specific monthly data
                        campusData = this.analyticsData.campus_application_stats?.find(c => c.campus_id == campus);
                    }
                    
                    // Use campus-specific data if available, otherwise use global data
                    let currentData, previousData;
                    
                    if (campusData && campusData.monthly_trends) {
                        // Use campus-specific monthly trends
                        const campusMonthlyData = campusData.monthly_trends;
                        const currentMonthKey = monthlyLabels[monthlyLabels.length - 1]?.toLowerCase();
                        const previousMonthKey = monthlyLabels[monthlyLabels.length - 2]?.toLowerCase();
                        
                        currentData = {
                            total_applications: campusMonthlyData[currentMonthKey]?.total_applications || 0,
                            approved_applications: campusMonthlyData[currentMonthKey]?.approved_applications || 0,
                            rejected_applications: campusMonthlyData[currentMonthKey]?.rejected_applications || 0,
                            pending_applications: 0
                        };
                        
                        previousData = {
                            total_applications: campusMonthlyData[previousMonthKey]?.total_applications || 0,
                            approved_applications: campusMonthlyData[previousMonthKey]?.approved_applications || 0,
                            rejected_applications: campusMonthlyData[previousMonthKey]?.rejected_applications || 0,
                            pending_applications: 0
                        };
                    } else {
                        // Use global monthly data
                        currentData = {
                            total_applications: monthlyApplications[monthlyApplications.length - 1] || 0,
                            approved_applications: monthlyApprovedApplications[monthlyApprovedApplications.length - 1] || 0,
                            rejected_applications: monthlyRejectedApplications[monthlyRejectedApplications.length - 1] || 0,
                            pending_applications: 0
                        };
                        
                        previousData = {
                            total_applications: monthlyApplications[monthlyApplications.length - 2] || 0,
                            approved_applications: monthlyApprovedApplications[monthlyApprovedApplications.length - 2] || 0,
                            rejected_applications: monthlyRejectedApplications[monthlyRejectedApplications.length - 2] || 0,
                            pending_applications: 0
                        };
                    }
                    
                    // Calculate pending applications
                    currentData.pending_applications = Math.max(0, currentData.total_applications - currentData.approved_applications - currentData.rejected_applications);
                    previousData.pending_applications = Math.max(0, previousData.total_applications - previousData.approved_applications - previousData.rejected_applications);
                    
                    // Get month names
                    const currentMonthName = monthlyLabels[monthlyLabels.length - 1] || 'Current Month';
                    const previousMonthName = monthlyLabels[monthlyLabels.length - 2] || 'Previous Month';
                    
                    // Calculate metrics for current month
                    const currentApprovalRate = currentData.total_applications > 0 ? 
                        Math.round((currentData.approved_applications / currentData.total_applications) * 100) : 0;
                    
                    const previousApprovalRate = previousData.total_applications > 0 ? 
                        Math.round((previousData.approved_applications / previousData.total_applications) * 100) : 0;
                    
                    const processingRate = currentData.total_applications > 0 ? 
                        Math.round(((currentData.approved_applications + currentData.rejected_applications) / currentData.total_applications) * 100) : 0;
                    
                    const volumeChange = previousData.total_applications > 0 ? 
                        Math.round(((currentData.total_applications - previousData.total_applications) / previousData.total_applications) * 100) : 0;
                    
                    // Determine status
                    let status = 'Good';
                    if (currentApprovalRate < 40 || processingRate < 70) {
                        status = 'Critical';
                    } else if (currentApprovalRate < 60 || processingRate < 85 || volumeChange < -30) {
                        status = 'Warning';
                    }
                    
                    return {
                        currentMonthName: currentMonthName,
                        previousMonthName: previousMonthName,
                        current: {
                            total: currentData.total_applications,
                            approved: currentData.approved_applications,
                            rejected: currentData.rejected_applications,
                            pending: currentData.pending_applications,
                            approvalRate: currentApprovalRate
                        },
                        previous: {
                            total: previousData.total_applications,
                            approved: previousData.approved_applications,
                            rejected: previousData.rejected_applications,
                            pending: previousData.pending_applications,
                            approvalRate: previousApprovalRate
                        },
                        processingRate: processingRate,
                        volumeChange: volumeChange,
                        status: status
                    };
                },
                
                getCurrentCampusName() {
                    // Return current campus name based on filter
                    if (this.filters.campus === 'all') {
                        return 'All Campuses';
                    }
                    
                    const campusData = this.analyticsData.campus_application_stats?.find(c => c.campus_id == this.filters.campus);
                    return campusData?.campus_name || 'Unknown Campus';
                },
                
                getCampusComparisonData() {
                    // Get campus data from analyticsData
                    const campusStats = this.analyticsData.campus_application_stats || [];
                    
                    const labels = campusStats.map(campus => campus.campus_name || 'Unknown Campus');
                    const applications = campusStats.map(campus => campus.total_applications || 0);
                    const students = campusStats.map(campus => campus.total_students || 0);
                    
                    return {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Applications',
                                data: applications,
                                backgroundColor: '#3B82F6',
                                borderColor: '#1D4ED8',
                                borderWidth: 1
                            },
                            {
                                label: 'Total Students',
                                data: students,
                                backgroundColor: '#10B981',
                                borderColor: '#059669',
                                borderWidth: 1
                            }
                        ]
                    };
                },
                
                
                
                
                async fetchFilteredData() {
                    try {
                        const response = await fetch('/central/analytics/filtered', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                filters: this.filters
                            })
                        });
                        
                        if (response.ok) {
                            const data = await response.json();
                            this.analyticsData = data.analytics;
                            console.log('Updated analytics data:', this.analyticsData);
                            // Wait a bit for data to be processed
                            setTimeout(() => {
                            this.createFilteredPieChart();
                            }, 200);
                        } else {
                            console.error('Failed to fetch filtered data');
                            this.createFilteredPieChart();
                        }
                    } catch (error) {
                        console.error('Error fetching filtered data:', error);
                        this.createFilteredPieChart();
                    }
                },
                
                clearFilters() {
                    this.filters = {
                        analysisType: 'application_status',
                        campus: 'all',
                        timePeriod: 'all'
                    };
                    this.updateChart();
                },
                
                initializeCharts() {
                    console.log('Initializing charts...');
                    setTimeout(() => {
                        console.log('Creating charts after timeout...');
                        this.createFilteredPieChart();
                        this.createMonthlyTrendsChart();
                        this.createCampusPerformanceChart();
                        this.createScholarshipPerformanceChart();
                    }, 100);
                },
                
                
                createFilteredPieChart() {
                    console.log('🔥 createFilteredPieChart() called!');
                    const ctx = document.getElementById('filteredPieChart');
                    if (!ctx) {
                        console.log('filteredPieChart canvas not found');
                        return;
                    }
                    
                    console.log('Creating filtered pie chart...');
                    console.log('Current campus:', this.filters.campus);
                    console.log('Current analytics data:', this.analyticsData);
                    
                    // Destroy existing chart
                    if (this.charts.filteredPie) {
                        try {
                        this.charts.filteredPie.destroy();
                        } catch (error) {
                            console.log('Error destroying chart:', error);
                        }
                        this.charts.filteredPie = null;
                    }
                    
                    // Get chart data
                        let chartData = this.getFilteredChartData();
                    console.log('Chart data for campus', this.filters.campus, ':', chartData);
                        
                        // Validate chart data
                        if (!chartData || !chartData.labels || !chartData.datasets) {
                            console.error('Invalid chart data:', chartData);
                        this.createFallbackChart(ctx);
                            return;
                        }
                        
                    // Check if data is valid
                    const totalData = chartData.datasets[0].data.reduce((sum, val) => sum + (typeof val === 'number' ? val : 0), 0);
                    console.log('Total data for campus', this.filters.campus, ':', totalData);
                    
                    if (totalData === 0) {
                        console.log('No data available, creating fallback chart');
                        this.createFallbackChart(ctx);
                        return;
                    }
                    
                    try {
                        // Create chart
                        this.charts.filteredPie = new Chart(ctx, {
                            type: 'pie',
                            data: chartData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                                animation: {
                                    duration: 300,
                                    easing: 'easeInOutQuart'
                                },
                            plugins: {
                                legend: {
                                        position: 'bottom',
                                        labels: {
                                            padding: 20,
                                            usePointStyle: true
                                        }
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                let label = context.label || '';
                                                let value = context.parsed;
                                                let total = context.dataset.data.reduce((a, b) => a + (typeof b === 'number' ? b : 0), 0);
                                                let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                                return label + ': ' + value + ' (' + percentage + '%)';
                                            }
                                        }
                                    }
                                }
                            }
                        });
                        
                        console.log('Chart created successfully for campus:', this.filters.campus);
                    } catch (error) {
                        console.error('Error creating chart:', error);
                        this.createFallbackChart(ctx);
                    }
                },
                
                
                getOverallChartData() {
                    const analysisType = this.filters.analysisType;
                    let labels = [];
                    let data = [];
                    let backgroundColor = [];
                    
                    switch (analysisType) {
                        case 'application_status':
                            labels = ['Applied', 'Not Applied', 'Pending', 'Approved', 'Rejected'];
                            backgroundColor = ['#3B82F6', '#6B7280', '#F59E0B', '#10B981', '#EF4444'];
                            data = [
                                this.analyticsData.total_applications || 0,
                                (this.analyticsData.total_students || 0) - (this.analyticsData.total_applications || 0),
                                this.analyticsData.pending_applications || 0,
                                this.analyticsData.approved_applications || 0,
                                this.analyticsData.rejected_applications || 0
                            ];
                            break;
                        case 'gender':
                            labels = ['Male', 'Female'];
                            backgroundColor = ['#3B82F6', '#EC4899'];
                            data = [
                                this.analyticsData.male_students || 0,
                                this.analyticsData.female_students || 0
                            ];
                            break;
                        case 'year_level':
                            labels = this.analyticsData.year_level_labels || ['1st Year', '2nd Year', '3rd Year', '4th Year'];
                            data = this.analyticsData.year_level_counts || [];
                            backgroundColor = ['#10B981', '#3B82F6', '#F59E0B', '#EF4444'];
                            break;
                    }
                    
                    // Ensure we have valid data
                    if (!data || data.length === 0 || data.every(val => val === 0)) {
                        labels = ['No Data Available'];
                        data = [1];
                        backgroundColor = ['#6B7280'];
                    }
                    
                    return {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: backgroundColor,
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    };
                },
                
                createFallbackChart(ctx) {
                    console.log('Creating fallback chart');
                    
                    // Destroy existing chart
                    if (this.charts.filteredPie) {
                        try {
                            this.charts.filteredPie.destroy();
                        } catch (error) {
                            console.log('Error destroying chart in fallback:', error);
                        }
                        this.charts.filteredPie = null;
                    }
                    
                    // Clear canvas
                    const context = ctx.getContext('2d');
                    context.clearRect(0, 0, ctx.width, ctx.height);
                    
                    try {
                        this.charts.filteredPie = new Chart(ctx, {
                            type: 'pie',
                            data: {
                                labels: ['No Data Available'],
                                datasets: [{
                                    data: [1],
                                    backgroundColor: ['#6B7280'],
                                    borderWidth: 2,
                                    borderColor: '#ffffff'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                animation: {
                                    duration: 300,
                                    easing: 'easeInOutQuart'
                                },
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            padding: 20,
                                            usePointStyle: true
                                        }
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                return 'No Data Available';
                                            }
                                        }
                                    }
                                }
                            }
                        });
                        console.log('Fallback chart created successfully');
                    } catch (error) {
                        console.error('Error creating fallback chart:', error);
                    }
                },
                
                getFilteredChartData() {
                    let labels = [];
                    let data = [];
                    let backgroundColor = [];
                    
                    const analysisType = this.filters.analysisType;
                    const campus = this.filters.campus;
                    const timePeriod = this.filters.timePeriod;
                    
                    console.log('Getting filtered chart data for:', { analysisType, campus, timePeriod });
                    console.log('Available campus data:', this.analyticsData.campus_application_stats);
                    
                    // Get campus-specific data if not 'all'
                    let campusData = null;
                    if (campus !== 'all') {
                        console.log('Looking for campus data for campus ID:', campus);
                        console.log('Available campus data:', this.analyticsData.campus_application_stats);
                        
                        campusData = this.analyticsData.campus_application_stats?.find(c => c.campus_id == campus);
                        console.log('Campus data found for campus', campus, ':', campusData);
                        
                        // If no campus data found, log available campuses
                        if (!campusData) {
                            console.log('No data found for campus ID:', campus);
                            console.log('Available campus IDs:', this.analyticsData.campus_application_stats?.map(c => c.campus_id));
                            console.log('Falling back to overall data for campus:', campus);
                        } else {
                            // Validate campus data structure
                            if (!campusData.total_applications && !campusData.total_students && 
                                !campusData.approved_applications && !campusData.pending_applications) {
                                console.log('Campus data exists but is empty, using overall data');
                                campusData = null;
                            }
                        }
                    } else {
                        console.log('Using overall data for all campuses');
                    }
                    
                    // Log current filters for debugging
                    console.log('Current filters:', {
                        analysisType,
                        campus,
                        timePeriod
                    });
                    
                    switch (analysisType) {
                        case 'application_status':
                            labels = ['Applied', 'Not Applied', 'Pending', 'Approved', 'Rejected'];
                            backgroundColor = ['#3B82F6', '#6B7280', '#F59E0B', '#10B981', '#EF4444'];
                            
                            if (campusData) {
                                console.log('Using campus-specific data for campus', campus, ':', campusData);
                                // Use campus-specific application status data
                                data = [
                                    campusData.total_applications || 0,
                                    (campusData.total_students || 0) - (campusData.total_applications || 0),
                                    campusData.pending_applications || 0,
                                    campusData.approved_applications || 0,
                                    campusData.rejected_applications || 0
                                ];
                                console.log('Campus-specific data calculated:', data);
                            } else {
                                console.log('Using overall data for campus', campus);
                                // Use overall application status data
                                data = [
                                    this.analyticsData.total_applications || 0,
                                    (this.analyticsData.total_students || 0) - (this.analyticsData.total_applications || 0),
                                    this.analyticsData.pending_applications || 0,
                                    this.analyticsData.approved_applications || 0,
                                    this.analyticsData.rejected_applications || 0
                                ];
                                console.log('Overall data calculated:', data);
                            }
                            break;
                            
                        case 'application_type':
                            labels = ['New', 'Continuing'];
                            backgroundColor = ['#10B981', '#F59E0B'];
                            
                            if (campusData) {
                                // Use campus-specific application type data
                                data = [
                                    campusData.new_applications || 0,
                                    campusData.continuing_applications || 0
                                ];
                            } else {
                                // Use overall application type data
                                data = [
                                    this.analyticsData.new_applications || 0,
                                    this.analyticsData.continuing_applications || 0
                                ];
                            }
                            break;
                            
                        case 'gender':
                            labels = ['Male', 'Female'];
                            backgroundColor = ['#3B82F6', '#EC4899'];
                            
                            if (campusData) {
                                // Use campus-specific gender data
                                data = [
                                    campusData.male_students || 0,
                                    campusData.female_students || 0
                                ];
                            } else {
                                // Use overall gender data
                                data = [
                                    this.analyticsData.male_students || 0,
                                    this.analyticsData.female_students || 0
                                ];
                            }
                            break;
                            
                        case 'year_level':
                            if (campusData) {
                                // Use campus-specific year level data
                                labels = campusData.year_level_labels || ['1st Year', '2nd Year', '3rd Year', '4th Year'];
                                data = campusData.year_level_counts || [];
                            } else {
                                // Use overall year level data
                                labels = this.analyticsData.year_level_labels || ['1st Year', '2nd Year', '3rd Year', '4th Year'];
                                data = this.analyticsData.year_level_counts || [];
                            }
                            backgroundColor = ['#10B981', '#3B82F6', '#F59E0B', '#EF4444'];
                            break;
                    }
                    
                    // Ensure we have valid data
                    if (!data || data.length === 0 || data.every(val => val === 0)) {
                        console.log('No valid data found, using fallback');
                        labels = ['No Data Available'];
                        data = [1];
                        backgroundColor = ['#6B7280'];
                    } else {
                        // Ensure labels and data arrays have the same length
                        if (labels.length !== data.length) {
                            console.warn('Labels and data length mismatch, adjusting...');
                            const minLength = Math.min(labels.length, data.length);
                            labels = labels.slice(0, minLength);
                            data = data.slice(0, minLength);
                            backgroundColor = backgroundColor.slice(0, minLength);
                        }
                        
                        // Ensure all data values are numbers
                        data = data.map(val => typeof val === 'number' ? val : 0);
                    }
                    
                    console.log('Final chart data for campus', campus, ':', {
                        analysisType: analysisType,
                        campus: campus,
                        campusData: campusData,
                        labels: labels,
                        data: data,
                        backgroundColor: backgroundColor,
                        usingCampusData: !!campusData
                    });
                    
                    return {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: backgroundColor,
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    };
                },
                
                
                createMonthlyTrendsChart() {
                    const ctx = document.getElementById('monthlyTrendsChart');
                    if (!ctx) return;
                    
                    this.charts.monthlyTrends = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: this.analyticsData.monthly_labels || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                            datasets: [{
                                label: 'Applications',
                                data: this.analyticsData.monthly_applications || [0, 0, 0, 0, 0, 0],
                                borderColor: '#3B82F6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4
                            }, {
                                label: 'Approved',
                                data: this.analyticsData.monthly_approved_applications || [0, 0, 0, 0, 0, 0],
                                borderColor: '#10B981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                },
                
                createCampusPerformanceChart() {
                    const ctx = document.getElementById('campusPerformanceChart');
                    if (!ctx) return;
                    
                    this.charts.campusPerformance = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: this.analyticsData.campus_names || ['Campus A', 'Campus B', 'Campus C'],
                            datasets: [{
                                label: 'Total Applications',
                                data: this.analyticsData.campus_application_stats?.map(c => c.total_applications) || [0, 0, 0],
                                backgroundColor: '#3B82F6'
                            }, {
                                label: 'Approved Applications',
                                data: this.analyticsData.campus_application_stats?.map(c => c.approved_applications) || [0, 0, 0],
                                backgroundColor: '#10B981'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                },
                
                createScholarshipPerformanceChart() {
                    const ctx = document.getElementById('scholarshipPerformanceChart');
                    if (!ctx) return;
                    
                    this.charts.scholarshipPerformance = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: this.analyticsData.scholarship_performance?.map(s => s.name.substring(0, 15)) || ['Scholarship A', 'Scholarship B'],
                            datasets: [{
                                label: 'Applications',
                                data: this.analyticsData.scholarship_performance?.map(s => s.total_applications) || [0, 0],
                                backgroundColor: '#3B82F6'
                            }, {
                                label: 'Approved',
                                data: this.analyticsData.scholarship_performance?.map(s => s.approved_applications) || [0, 0],
                                backgroundColor: '#10B981'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                },
                
                refreshAnalytics() {
                    const refreshButton = this.$el.querySelector('button');
                    if (refreshButton) {
                        refreshButton.textContent = 'Refreshing...';
                    }
                    
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                }
            }));
        });
    </script>
</div>