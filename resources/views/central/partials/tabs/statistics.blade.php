<div x-show="tab === 'all_statistics' || tab.endsWith('_statistics')" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-cloak 
     x-data="statisticsTab()"
     @change-stats-campus.window="filters.campus = $event.detail; applyFilters()">
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Comprehensive Analytics Dashboard</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Complete insights into scholarship management, applications, reports, and campus performance across all campuses.
                </p>
            </div>

        </div>

        <!-- Filter Controls -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
            <div class="flex flex-wrap gap-4 items-end">
                
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
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Application Status Chart (Full Width) -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="text-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Scholarship Scholar Status</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400" x-text="getCurrentCampusName()"></p>
                </div>
                <div class="h-96 flex items-center justify-center">
                    <canvas id="scholarStatusChart" width="400" height="400"></canvas>
                </div>
                
                <!-- Scholar Status Data Display -->
                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex flex-wrap gap-4 text-sm justify-center">
                        <div class="flex items-center gap-2">
                             <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                            <span class="text-gray-600 dark:text-gray-400">New Scholars:</span>
                            <span class="font-medium dark:text-white" x-text="filteredData.new_scholars || 0"></span>
                        </div>
                        <div class="flex items-center gap-2">
                             <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                            <span class="text-gray-600 dark:text-gray-400">Old Scholars:</span>
                            <span class="font-medium dark:text-white" x-text="filteredData.old_scholars || 0"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gender Distribution Chart (Full Width) -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="text-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Gender Distribution of Scholars</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400" x-text="getCurrentCampusName()"></p>
                </div>
                
                <div class="h-32 flex flex-col justify-center px-8">
                    <div class="w-full">
                        <div class="flex justify-between mb-2">
                             <span class="text-sm font-medium text-blue-600 dark:text-blue-400">Male</span>
                             <span class="text-sm font-medium text-pink-600 dark:text-pink-400">Female</span>
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
                        <div class="flex justify-between mt-2 text-sm font-medium text-gray-600 dark:text-gray-300">
                             <span x-text="(filteredData.male_students || 0).toLocaleString() + ' Scholars'"></span>
                             <span x-text="(filteredData.female_students || 0).toLocaleString() + ' Scholars'"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Year Level Chart (Half Width) -->
            <!-- Department/Year Level Charts Removed -->

            <!-- Monthly Scholar Trend Chart Removed -->

             <!-- Campus Performance Comparison Chart (Full Width) -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow rounded-lg p-6">
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
            <!-- Total Students -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Students</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white" x-text="Number(filteredData.total_students || 0).toLocaleString()"></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Scholars -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">New Scholars</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white" x-text="Number(filteredData.new_scholars || 0).toLocaleString()"></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Old Scholars -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                             <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Old Scholars</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white" x-text="Number(filteredData.old_scholars || 0).toLocaleString()"></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overall Scholar Rate -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Overall Scholar Rate</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white" x-text="getScholarRate() + '%'"></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Statistics Charts -->

        <!-- Scholarship Performance Analysis Removed -->

        <!-- Detailed Statistics Table -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Campus Statistics Summary</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Campus</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Students</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Scholars</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Scholars Rate</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($analytics['campus_application_stats'] ?? [] as $campus)
                        @php
                            $totalScholars = ($campus['new_scholars'] ?? 0) + ($campus['old_scholars'] ?? 0);
                            $rate = ($campus['total_students'] ?? 0) > 0 ? round(($totalScholars / $campus['total_students']) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $campus['campus_name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ number_format($campus['total_students'] ?? 0) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ number_format($totalScholars) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $rate }}%</td>
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
            Alpine.data('statisticsTab', () => {
                // Private Chart Instances (Non-Reactive)
                const charts = {
                    scholarStatus: null,
                    // gender: null, // Removed
                    // yearLevel: null, // Removed
                    scholarStatus: null,
                    // gender: null, // Removed
                    // yearLevel: null, // Removed
                    // program: null, // Removed
                    // monthlyTrend: null, // Removed
                    campusComparison: null,
                    // scholarshipPerformance: null // Removed
                };

                return {
                    analyticsData: {},
                    filteredData: {},
                    filters: {
                        campus: 'all',
                        timePeriod: 'all'
                    },

                    init() {
                        console.log('Statistics Tab Initializing...');
                        try {
                            const rawData = document.getElementById('analytics-data').dataset.analytics;
                            this.analyticsData = rawData ? JSON.parse(rawData) : {};
                        } catch (e) {
                            console.error('Failed to parse analytics data:', e);
                            this.analyticsData = {};
                        }

                        // Set initial filters
                        this.filters.campus = 'all'; 

                        // Initial Data Process
                        this.updateFilteredData();

                        // Initialize Charts after DOM update
                        this.$nextTick(() => {
                            this.initCharts();
                        });

                        // Event Listeners
                        window.addEventListener('change-stats-campus', (e) => {
                            console.log('Campus changed to:', e.detail);
                            this.filters.campus = e.detail;
                            // Watcher will trigger applyFilters
                        });

                        // Watchers
                        this.$watch('filters.campus', () => this.applyFilters());
                        this.$watch('filters.timePeriod', () => this.applyFilters());

                        // Dark Mode Observer
                        const observer = new MutationObserver((mutations) => {
                            for(const m of mutations) {
                                if (m.attributeName === 'class') this.updateChartThemes();
                            }
                        });
                        observer.observe(document.documentElement, { attributes: true });
                    },

                    applyFilters() {
                        this.updateFilteredData();
                        this.$nextTick(() => {
                            this.updateCharts();
                        });
                    },

                    updateFilteredData() {
                        let data = { ...this.analyticsData };

                        if (this.filters.campus !== 'all') {
                            const campusStats = this.analyticsData.campus_application_stats || [];
                            const campusData = campusStats.find(c => c.campus_id == this.filters.campus);
                            
                            if (campusData) {
                                data = { ...campusData };
                            } else {
                                // Reset to empty if campus not found
                                data = this.getEmptyDataTemplate();
                            }
                        } else {
                            // Normalize global data
                            data.approval_rate = data.overall_approval_rate || 0;
                        }

                        console.log('Filtered Data Updated:', data);
                        this.filteredData = data;
                    },

                    getEmptyDataTemplate() {
                        return {
                            total_applications: 0,
                            total_students: 0,
                            approved_applications: 0,
                            pending_applications: 0,
                            rejected_applications: 0,
                            approval_rate: 0,
                            new_scholars: 0,
                            old_scholars: 0,
                            year_level_counts: [0, 0, 0, 0],
                            male_students: 0,
                            female_students: 0,
                            monthly_trends: {} 
                        };
                    },

                    // Chart Management
                    initCharts() {
                        this.renderScholarStatusChart();
                        // this.renderGenderChart(); // Removed
                        // this.renderYearLevelChart(); // Removed
                        this.renderScholarStatusChart();
                        // this.renderGenderChart(); // Removed
                        // this.renderYearLevelChart(); // Removed
                        // this.renderProgramChart(); // Removed
                        // this.renderMonthlyTrendChart(); // Removed
                        this.renderCampusComparisonChart();
                        // this.renderScholarshipPerformanceChart(); // Removed
                    },

                    updateCharts() {
                        // If charts exist, update them. If not, create them.
                        this.renderScholarStatusChart(true);
                        // this.renderGenderChart(true); // Removed
                        // this.renderYearLevelChart(true); // Removed
                        this.renderScholarStatusChart(true);
                        // this.renderGenderChart(true); // Removed
                        // this.renderYearLevelChart(true); // Removed
                        // this.renderProgramChart(true); // Removed
                        // this.renderMonthlyTrendChart(true); // Removed
                        this.renderCampusComparisonChart(true);
                        // this.renderScholarshipPerformanceChart(true); // Removed
                    },
                    
                    updateChartThemes() {
                        // Update colors for all existing charts
                        Object.values(charts).forEach(chart => {
                            if (chart) {
                                if (chart.options.scales?.x) {
                                    chart.options.scales.x.ticks.color = this.getTextColor();
                                    chart.options.scales.x.grid.color = this.getGridColor();
                                }
                                if (chart.options.scales?.y) {
                                    chart.options.scales.y.ticks.color = this.getTextColor();
                                    chart.options.scales.y.grid.color = this.getGridColor();
                                }
                                if (chart.options.plugins?.legend?.labels) {
                                    chart.options.plugins.legend.labels.color = this.getTextColor();
                                }
                                chart.update();
                            }
                        });
                    },

                    destroyChart(key) {
                        if (charts[key]) {
                            charts[key].destroy();
                            charts[key] = null;
                        }
                    },

                    // --- Specific Chart Renderers ---

                    renderScholarStatusChart(update = false) {
                        const ctx = document.getElementById('scholarStatusChart');
                        if (!ctx) return;
                        
                        const data = this.getScholarStatusData();
                        
                        if (charts.scholarStatus && update) {
                            charts.scholarStatus.data = data;
                            charts.scholarStatus.update();
                        } else {
                            this.destroyChart('scholarStatus');
                            charts.scholarStatus = new Chart(ctx, {
                                type: 'bar',
                                data: data,
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        x: { ticks: { color: this.getTextColor() }, grid: { color: this.getGridColor() } },
                                        y: { beginAtZero: true, ticks: { color: this.getTextColor() }, grid: { color: this.getGridColor() } }
                                    },
                                    plugins: {
                                        legend: { 
                                            position: 'bottom',
                                            labels: { color: this.getTextColor() } 
                                        },
                                        tooltip: {
                                            mode: 'index',
                                            intersect: false
                                        }
                                    }
                                }
                            });
                        }
                    },

                    // renderGenderChart removed

                    // renderProgramChart removed

                    // renderYearLevelChart removed

                    // renderMonthlyTrendChart removed

                    renderCampusComparisonChart(update = false) {
                        const ctx = document.getElementById('campusComparisonChart');
                        if (!ctx) return;
                        
                        const data = this.getCampusComparisonData();
                        
                        // If we are filtering by a specific campus, checking 'Comparison' creates a visual redundancy usually, 
                        // but we will keep showing all campuses for context OR show just adjacent?
                        // Usually 'Comparison' implies showing ALL regardless of filter.
                        // But let's keep it responsive.

                        if (charts.campusComparison && update) {
                            charts.campusComparison.data = data;
                            charts.campusComparison.update();
                        } else {
                            this.destroyChart('campusComparison');
                            charts.campusComparison = new Chart(ctx, {
                                type: 'bar',
                                data: data,
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        x: { ticks: { color: this.getTextColor() }, grid: { color: this.getGridColor() } },
                                        y: { beginAtZero: true, ticks: { color: this.getTextColor() }, grid: { color: this.getGridColor() } }
                                    },
                                    plugins: { legend: { display: false } }
                                }
                            });
                        }
                    },

                    // renderScholarshipPerformanceChart removed

                    // --- Data Getters ---

                    getScholarStatusData() {
                        const stats = this.filteredData.scholarship_scholar_stats || [];
                        
                        return {
                            labels: stats.map(s => s.name),
                            datasets: [
                                {
                                    label: 'New Scholars',
                                    data: stats.map(s => s.new),
                                    backgroundColor: '#3B82F6', // Blue
                                    borderWidth: 1
                                },
                                {
                                    label: 'Old Scholars',
                                    data: stats.map(s => s.old),
                                    backgroundColor: '#EF4444', // Red
                                    borderWidth: 1
                                }
                            ]
                        };
                    },

                    getGenderPercentage(gender) {
                        const male = this.filteredData.male_students || 0;
                        const female = this.filteredData.female_students || 0;
                        const total = male + female;
                        if (total === 0) return 0;
                        return gender === 'male' ? ((male / total) * 100).toFixed(1) : ((female / total) * 100).toFixed(1);
                    },

                    getScholarRate() {
                         const total = (this.filteredData.new_scholars || 0) + (this.filteredData.old_scholars || 0);
                         const students = this.filteredData.total_students || 0;
                         if (students === 0) return 0;
                         return ((total / students) * 100).toFixed(1);
                    },

                    getGenderData() {
                        const male = this.filteredData.male_students || 0;
                        const female = this.filteredData.female_students || 0;
                        return {
                            labels: ['Male', 'Female'],
                            datasets: [{
                                data: [male, female],
                                backgroundColor: ['#3B82F6', '#EC4899'],
                                borderWidth: 0
                            }]
                        };
                    },

                    getYearLevelData() {
                        const counts = this.filteredData.year_level_counts || [0, 0, 0, 0];
                        return {
                            labels: ['1st Year', '2nd Year', '3rd Year', '4th Year'],
                            datasets: [{
                                data: counts,
                                backgroundColor: ['#10B981', '#3B82F6', '#F59E0B', '#EF4444'],
                                borderWidth: 1
                            }]
                        };
                    },

                    // getProgramData removed

                    // getMonthlyTrendData removed

                    getCampusComparisonData() {
                        // Always show global comparison?
                        // Or if filtered, maybe highlight the selected one?
                        // For now, keep showing global comparison as it's a "Comparison" chart.
                        const names = this.analyticsData.campus_names || [];
                        const stats = this.analyticsData.campus_application_stats || [];
                        const data = stats.map(s => (s.new_scholars || 0) + (s.old_scholars || 0));

                        return {
                            labels: names,
                            datasets: [{
                                label: 'Total Scholars',
                                data: data,
                                backgroundColor: '#10B981',
                                borderWidth: 1
                            }]
                        };
                    },

                    // getScholarshipPerformanceData removed

                    // --- Utilities ---

                    getTextColor() {
                        return document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#4B5563';
                    },

                    getGridColor() {
                        return document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB';
                    },

                    getCurrentCampusName() {
                        if (this.filters.campus === 'all') return 'All Campuses';
                        return this.filteredData.campus_name || 'Selected Campus';
                    },
                    
                    getGenderCount(gender) {
                        return gender === 'Male' ? (this.filteredData.male_students || 0) : (this.filteredData.female_students || 0);
                    },

                    // getMonthlyComparison removed
                    


                    createFallbackChart() {} // No-op
                };
            });
        });
    </script>
</div>
