<div x-show="tab === 'statistics'" x-transition x-cloak x-data="statisticsTab()">
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
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Filter Controls</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Dropdown A: Analysis Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Analysis Type</label>
                    <select x-model="filters.analysisType" @change="updateChart()" 
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-bsu-red focus:border-bsu-red dark:bg-gray-700 dark:text-white">
                        <option value="application_status">Application Status</option>
                        <option value="application_type">Application Type</option>
                        <option value="gender">Gender</option>
                        <option value="year_level">Year Level</option>
                    </select>
                </div>

                <!-- Dropdown B: Campus Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Campus</label>
                    <select x-model="filters.campus" @change="updateChart()" 
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-bsu-red focus:border-bsu-red dark:bg-gray-700 dark:text-white">
                        <option value="all">All Campuses</option>
                        @if(isset($campuses) && is_array($campuses))
                            @foreach($campuses as $campus)
                            <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Additional Filters -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Time Period</label>
                    <select x-model="filters.timePeriod" @change="updateChart()" 
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-bsu-red focus:border-bsu-red dark:bg-gray-700 dark:text-white">
                        <option value="all">All Time</option>
                        <option value="this_month">This Month</option>
                        <option value="last_3_months">Last 3 Months</option>
                        <option value="this_year">This Year</option>
                    </select>
                </div>

                <!-- Scholarship Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Scholarship</label>
                    <select x-model="filters.scholarship" @change="updateChart()" 
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-bsu-red focus:border-bsu-red dark:bg-gray-700 dark:text-white">
                        <option value="all">All Scholarships</option>
                        @if(isset($scholarships) && is_array($scholarships))
                            @foreach($scholarships as $scholarship)
                            <option value="{{ $scholarship->id }}">{{ $scholarship->scholarship_name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Clear Filters -->
                <div class="flex items-end">
                    <button @click="clearFilters()" 
                            class="w-full px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition duration-150">
                        Clear Filters
                    </button>
                        </div>
                        </div>
        </div>

        <!-- Filtered Pie Chart Section -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="text-center mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2" x-text="getChartTitle()">Filtered Statistics</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400" x-text="getChartDescription()">Data changes based on selected filters</p>
            </div>
            <div class="h-96 flex items-center justify-center">
                <canvas id="filteredPieChart" width="400" height="400"></canvas>
            </div>
            <div class="mt-4 text-center">
                <div class="inline-flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400">
                    <span x-text="'Total: ' + (filteredData.total || 0)"></span>
                    <span x-show="filteredData.applied !== undefined" x-text="'Applied: ' + (filteredData.applied || 0)"></span>
                    <span x-show="filteredData.not_applied !== undefined" x-text="'Not Applied: ' + (filteredData.not_applied || 0)"></span>
                    <span x-show="filteredData.pending !== undefined" x-text="'Pending: ' + (filteredData.pending || 0)"></span>
                    <span x-show="filteredData.approved !== undefined" x-text="'Approved: ' + (filteredData.approved || 0)"></span>
                    <span x-show="filteredData.rejected !== undefined" x-text="'Rejected: ' + (filteredData.rejected || 0)"></span>
                    <span x-show="filteredData.new !== undefined" x-text="'New: ' + (filteredData.new || 0)"></span>
                    <span x-show="filteredData.renewal !== undefined" x-text="'Renewal: ' + (filteredData.renewal || 0)"></span>
                    <span x-show="filteredData.male !== undefined" x-text="'Male: ' + (filteredData.male || 0)"></span>
                    <span x-show="filteredData.female !== undefined" x-text="'Female: ' + (filteredData.female || 0)"></span>
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
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Monthly Trends -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Monthly Application Trends</h3>
                <div class="h-64">
                    <canvas id="monthlyTrendsChart"></canvas>
                </div>
            </div>

            <!-- Campus Performance -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Campus Performance Comparison</h3>
                <div class="h-64">
                    <canvas id="campusPerformanceChart"></canvas>
                </div>
            </div>
        </div>

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
                charts: {},
                analyticsData: JSON.parse(document.getElementById('analytics-data').dataset.analytics || '{}'),
                filteredData: {},
                filters: {
                    analysisType: 'application_status',
                    campus: 'all',
                    timePeriod: 'all',
                    scholarship: 'all'
                },
                
                init() {
                    console.log('Statistics tab initialized');
                    console.log('Analytics data:', this.analyticsData);
                    this.updateFilteredData();
                            this.initializeCharts();
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
                            data = {
                                total: campusData.total_students || 0,
                                applied: campusData.students_with_applications || 0,
                                not_applied: (campusData.total_students || 0) - (campusData.students_with_applications || 0),
                                pending: campusData.pending_applications || 0,
                                approved: campusData.approved_applications || 0,
                                rejected: campusData.rejected_applications || 0
                            };
                            break;
                        case 'application_type':
                            data = {
                                total: campusData.total_applications || 0,
                                new: campusData.new_applications || 0,
                                renewal: campusData.continuing_applications || 0
                            };
                            break;
                        case 'gender':
                            data = {
                                total: campusData.total_students || 0,
                                male: campusData.male_students || 0,
                                female: campusData.female_students || 0
                            };
                            break;
                        case 'year_level':
                            data = {
                                total: campusData.total_students || 0,
                                year_levels: campusData.year_level_counts || []
                            };
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
                
                updateChart() {
                    this.updateFilteredData();
                    this.createFilteredPieChart();
                },
                
                clearFilters() {
                    this.filters = {
                        analysisType: 'application_status',
                        campus: 'all',
                        timePeriod: 'all',
                        scholarship: 'all'
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
                    const ctx = document.getElementById('filteredPieChart');
                    if (!ctx) {
                        console.log('filteredPieChart canvas not found');
                        return;
                    }
                    
                    console.log('Creating filtered pie chart...');
                    
                    // Destroy existing chart
                    if (this.charts.filteredPie) {
                        this.charts.filteredPie.destroy();
                    }
                    
                    let chartData = this.getFilteredChartData();
                    console.log('Chart data:', chartData);
                    
                    this.charts.filteredPie = new Chart(ctx, {
                        type: 'pie',
                        data: chartData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
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
                                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                            return label + ': ' + value + ' (' + percentage + '%)';
                                        }
                                    }
                                }
                            }
                        }
                    });
                },
                
                getFilteredChartData() {
                    let labels = [];
                    let data = [];
                    let backgroundColor = [];
                    
                    const analysisType = this.filters.analysisType;
                    const campus = this.filters.campus;
                    
                    // Get campus-specific data if not 'all'
                    let campusData = null;
                    if (campus !== 'all') {
                        campusData = this.analyticsData.campus_application_stats?.find(c => c.campus_id == campus);
                    }
                    
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
                            
                        case 'application_type':
                            labels = ['New Applications', 'Renewal Applications'];
                            backgroundColor = ['#10B981', '#F59E0B'];
                            
                            if (campusData) {
                                data = [
                                    campusData.new_applications || 0,
                                    campusData.continuing_applications || 0
                                ];
                            } else {
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
                            labels = this.analyticsData.year_level_labels || ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'];
                            backgroundColor = ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6'];
                            
                            if (campusData) {
                                data = campusData.year_level_counts || [];
                            } else {
                                data = this.analyticsData.year_level_counts || [];
                            }
                            break;
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