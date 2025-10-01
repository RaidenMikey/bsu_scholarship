<div x-show="tab === 'statistics'" x-transition x-cloak>
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Analytics Dashboard</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Comprehensive analytics and insights for scholarship management across all campuses.
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

        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Reports Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-bsu-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Reports</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $analytics['total_reports'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submitted Reports Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Submitted Reports</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $analytics['submitted_reports'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approved Reports Card -->
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
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Approved Reports</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $analytics['approved_reports'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Reviews Card -->
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
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Pending Reviews</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $analytics['pending_reviews'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Reports Status Chart -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Reports Status Distribution</h3>
                <div class="h-64">
                    <canvas id="reportsStatusChart"></canvas>
                </div>
            </div>

            <!-- Monthly Reports Trend -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Monthly Reports Trend</h3>
                <div class="h-64">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Campus Performance Chart -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Campus Performance Analysis</h3>
            <div class="h-80">
                <canvas id="campusPerformanceChart"></canvas>
            </div>
        </div>

        <!-- Scholarship Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Scholarship Distribution -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Scholarship Distribution</h3>
                <div class="h-64">
                    <canvas id="scholarshipDistributionChart"></canvas>
                </div>
            </div>

            <!-- Application Trends -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Application Trends</h3>
                <div class="h-64">
                    <canvas id="applicationTrendsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Analytics Data -->
    <div id="analytics-data" data-analytics='@json($analytics ?? [])' style="display: none;"></div>
    
    <script>
        
        // Initialize charts when tab is active
        document.addEventListener('alpine:init', () => {
            Alpine.data('statisticsTab', () => ({
                charts: {},
                analyticsData: JSON.parse(document.getElementById('analytics-data').dataset.analytics || '{}'),
                
                init() {
                    this.$watch('tab', (value) => {
                        if (value === 'statistics') {
                            this.initializeCharts();
                        }
                    });
                },
                
                initializeCharts() {
                    // Small delay to ensure DOM is ready
                    setTimeout(() => {
                        this.createReportsStatusChart();
                        this.createMonthlyTrendChart();
                        this.createCampusPerformanceChart();
                        this.createScholarshipDistributionChart();
                        this.createApplicationTrendsChart();
                    }, 100);
                },
                
                createReportsStatusChart() {
                    const ctx = document.getElementById('reportsStatusChart');
                    if (!ctx) return;
                    
                    this.charts.reportsStatus = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Submitted', 'Approved', 'Rejected', 'Draft'],
                            datasets: [{
                                data: [
                                    this.analyticsData.submitted_reports || 0,
                                    this.analyticsData.approved_reports || 0,
                                    this.analyticsData.rejected_reports || 0,
                                    this.analyticsData.draft_reports || 0
                                ],
                                backgroundColor: [
                                    '#3B82F6', // Blue
                                    '#10B981', // Green
                                    '#EF4444', // Red
                                    '#F59E0B'  // Yellow
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                },
                
                createMonthlyTrendChart() {
                    const ctx = document.getElementById('monthlyTrendChart');
                    if (!ctx) return;
                    
                    this.charts.monthlyTrend = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: this.analyticsData.monthly_labels || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                            datasets: [{
                                label: 'Reports Submitted',
                                data: this.analyticsData.monthly_reports || [0, 0, 0, 0, 0, 0],
                                borderColor: '#DC2626',
                                backgroundColor: 'rgba(220, 38, 38, 0.1)',
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
                                label: 'Reports Submitted',
                                data: this.analyticsData.campus_reports || [0, 0, 0],
                                backgroundColor: '#DC2626'
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
                
                createScholarshipDistributionChart() {
                    const ctx = document.getElementById('scholarshipDistributionChart');
                    if (!ctx) return;
                    
                    this.charts.scholarshipDistribution = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: this.analyticsData.scholarship_types || ['Academic', 'Athletic', 'Need-based'],
                            datasets: [{
                                data: this.analyticsData.scholarship_counts || [0, 0, 0],
                                backgroundColor: ['#DC2626', '#3B82F6', '#10B981']
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                },
                
                createApplicationTrendsChart() {
                    const ctx = document.getElementById('applicationTrendsChart');
                    if (!ctx) return;
                    
                    this.charts.applicationTrends = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: this.analyticsData.monthly_labels || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                            datasets: [{
                                label: 'Applications',
                                data: this.analyticsData.monthly_applications || [0, 0, 0, 0, 0, 0],
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
                
                refreshAnalytics() {
                    // Add loading state
                    const refreshButton = this.$el.querySelector('button');
                    if (refreshButton) {
                        refreshButton.textContent = 'Refreshing...';
                    }
                    
                    // Simulate API call
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                }
            }));
        });
    </script>
</div>
