<div x-show="tab === 'statistics'" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-cloak 
     x-data="sfaoStatisticsTab()">
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Campus Analytics Dashboard</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Insights into scholarship applications and student performance for {{ $sfaoCampus->name }} and its extensions.
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">


                <!-- Department Filter -->
                <div class="space-y-2">
                    <label class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                        <svg class="w-4 h-4 mr-2 text-bsu-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Department
                    </label>
                    <select x-model="filters.department" 
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-bsu-red focus:border-bsu-red dark:bg-gray-700 dark:text-white">
                        <option value="all">All Departments</option>
                        <!-- Departments will be populated dynamically based on campus selection or all available -->
                        <template x-for="dept in availableDepartments" :key="dept.id">
                            <option :value="dept.short_name" x-text="dept.short_name + ' - ' + dept.name"></option>
                        </template>
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
                    <p class="text-sm text-gray-600 dark:text-gray-400" x-text="getCurrentFilterLabel()"></p>
                </div>
                <div class="h-64 flex items-center justify-center">
                    <canvas id="sfaoApplicationStatusChart"></canvas>
                </div>
            </div>

            <!-- Gender Distribution Chart -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="text-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Gender Distribution</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400" x-text="getCurrentFilterLabel()"></p>
                </div>
                <div class="h-64 flex items-center justify-center">
                    <canvas id="sfaoGenderChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Department Distribution Chart (Full Width) -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mt-6">
            <div class="text-center mb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Department Distribution</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400" x-text="getCurrentFilterLabel()"></p>
            </div>
            <div class="h-80 flex items-center justify-center">
                <canvas id="sfaoDepartmentChart"></canvas>
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

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Analytics Data -->
    <div id="sfao-analytics-data" data-analytics='@json($analytics ?? [])' style="display: none;"></div>
    
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('sfaoStatisticsTab', () => ({
                charts: {
                    applicationStatus: null,
                    department: null,
                    gender: null
                },
                analyticsData: JSON.parse(document.getElementById('sfao-analytics-data').dataset.analytics || '{}'),
                campusOptions: @json($campusOptions),
                filteredData: {},
                availableDepartments: [],
                filters: {
                    campus: localStorage.getItem('sfaoStatsCampus') || '{{ $defaultStatsCampus }}',
                    department: 'all',
                    timePeriod: 'all'
                },
                
                init() {
                    console.log('SFAO Statistics tab initialized');
                    console.log('Initial Campus Filter:', this.filters.campus);
                    console.log('Campus Departments Map:', this.analyticsData.campus_departments);
                    
                    this.availableDepartments = this.analyticsData.all_departments || [];
                    
                    // Initialize departments list based on current campus filter
                    this.updateDepartmentsList(this.filters.campus);
                    this.updateFilteredData();
                    
                    this.$watch('filters.campus', (value) => {
                        console.log('Campus filter changed to:', value);
                        this.updateDepartmentsList(value);
                        this.applyFilters();
                    });

                    this.$watch('filters.department', () => {
                        this.applyFilters();
                    });

                    this.$watch('filters.timePeriod', () => {
                        this.applyFilters();
                    });
                    
                    this.$nextTick(() => {
                        setTimeout(() => {
                            this.createAllCharts();
                        }, 100);
                    });

                    // Listen for sidebar campus selection
                    window.addEventListener('set-stats-filter', (e) => {
                        console.log('Received set-stats-filter event:', e.detail);
                        this.filters.campus = e.detail;
                        // No need to call applyFilters here because $watch('filters.campus') handles it
                    });
                },

                updateDepartmentsList(campusId) {
                    console.log('Updating departments for campus:', campusId);
                    
                    if (campusId === 'all') {
                        // All unique departments from all campuses
                        const allShortNames = [...new Set(Object.values(this.analyticsData.campus_departments || {}).flat())];
                        this.availableDepartments = (this.analyticsData.all_departments || []).filter(d => allShortNames.includes(d.short_name));
                    } else {
                        // Departments for specific campus
                        const campusShortNames = this.analyticsData.campus_departments[campusId] || [];
                        console.log('Short names for campus:', campusShortNames);
                        this.availableDepartments = (this.analyticsData.all_departments || []).filter(d => campusShortNames.includes(d.short_name));
                    }
                    console.log('Available Departments:', this.availableDepartments);
                    
                    // Reset department selection if invalid
                    if (this.filters.department !== 'all' && !this.availableDepartments.find(d => d.short_name === this.filters.department)) {
                        this.filters.department = 'all';
                    }
                },
                
                updateFilteredData() {
                    let data = JSON.parse(JSON.stringify(this.analyticsData)); // Deep copy
                    let allStudents = this.analyticsData.all_students_data || [];

                    // 1. Filter Department Stats based on Campus (Sidebar)
                    let allowedDepartments = [];
                    if (this.filters.campus === 'all') {
                        const allDepts = Object.values(this.analyticsData.campus_departments || {}).flat();
                        allowedDepartments = [...new Set(allDepts)];
                        // allStudents remains as is (all campuses under SFAO)
                    } else {
                        allowedDepartments = this.analyticsData.campus_departments[this.filters.campus] || [];
                        // Filter students by campus
                        allStudents = allStudents.filter(s => s.campus_id == this.filters.campus);
                    }

                    if (data.department_stats) {
                        data.department_stats = data.department_stats.filter(d => allowedDepartments.includes(d.name));
                    }

                    // 2. Filter by Department (Dropdown)
                    if (this.filters.department !== 'all') {
                        // Filter the stats list
                        data.department_stats = data.department_stats.filter(d => d.name === this.filters.department);
                        
                        // Filter students by department
                        allStudents = allStudents.filter(s => s.college === this.filters.department);
                        
                        // Update Global Metrics based on the selected department
                        const selectedDeptStats = this.analyticsData.department_stats.find(d => d.name === this.filters.department);
                        
                        if (selectedDeptStats) {
                            data.total_students = selectedDeptStats.total_students;
                            data.total_applications = selectedDeptStats.total_applications;
                            data.approved_applications = selectedDeptStats.approved_applications;
                            data.pending_applications = selectedDeptStats.pending_applications || 0;
                            data.rejected_applications = selectedDeptStats.rejected_applications || 0;
                            data.students_with_applications = selectedDeptStats.total_applications; // Approximation
                            data.approval_rate = selectedDeptStats.approval_rate;
                        } else {
                            data.total_students = 0;
                            data.total_applications = 0;
                            data.approved_applications = 0;
                            data.pending_applications = 0;
                            data.rejected_applications = 0;
                            data.approval_rate = 0;
                        }
                    }

                    // Calculate Gender Stats
                    const genderCounts = { Male: 0, Female: 0 };
                    allStudents.forEach(student => {
                        if (student.sex === 'Male') genderCounts.Male++;
                        else if (student.sex === 'Female') genderCounts.Female++;
                    });
                    data.genderStats = genderCounts;

                    this.filteredData = data;
                },

                applyFilters() {
                    this.updateFilteredData();
                    this.updateCharts();
                },
                
                getCurrentFilterLabel() {
                    let label = 'All Data';
                    if (this.filters.campus !== 'all') {
                        const campus = this.campusOptions.find(c => c.id == this.filters.campus);
                        label = 'Campus: ' + (campus ? campus.name : this.filters.campus);
                    }
                    if (this.filters.department !== 'all') {
                        label += (this.filters.campus === 'all' ? ' | ' : ', ') + 'Dept: ' + this.filters.department;
                    }
                    return label;
                },

                createAllCharts() {
                    this.createApplicationStatusChart();
                    this.createDepartmentChart();
                    this.createGenderChart();
                },
                
                updateCharts() {
                    this.updateApplicationStatusChart();
                    this.updateDepartmentChart();
                    this.updateGenderChart();
                },
                
                createApplicationStatusChart() {
                    const ctx = document.getElementById('sfaoApplicationStatusChart');
                    if (!ctx) return;
                    
                    if (this.charts.applicationStatus) {
                        this.charts.applicationStatus.destroy();
                    }
                    
                    const data = this.getChartDataForType('application_status');
                    
                    this.charts.applicationStatus = new Chart(ctx, {
                        type: 'pie',
                        data: data,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom' }
                            }
                        }
                    });
                },

                updateApplicationStatusChart() {
                    if (this.charts.applicationStatus) {
                        const data = this.getChartDataForType('application_status');
                        this.charts.applicationStatus.data = data;
                        this.charts.applicationStatus.update();
                    }
                },

                createDepartmentChart() {
                    const ctx = document.getElementById('sfaoDepartmentChart');
                    if (!ctx) return;
                    
                    if (this.charts.department) {
                        this.charts.department.destroy();
                    }

                    // Prepare data from filteredData.department_stats
                    const labels = (this.filteredData.department_stats || []).map(d => d.name);
                    const values = (this.filteredData.department_stats || []).map(d => d.total_applications);

                    this.charts.department = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Total Applications',
                                data: values,
                                backgroundColor: '#dd2222'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom' }
                            }
                        }
                    });
                },

                updateDepartmentChart() {
                    if (this.charts.department) {
                        const labels = (this.filteredData.department_stats || []).map(d => d.name);
                        const values = (this.filteredData.department_stats || []).map(d => d.total_applications);
                        
                        this.charts.department.data.labels = labels;
                        this.charts.department.data.datasets[0].data = values;
                        this.charts.department.update();
                    }
                },

                createGenderChart() {
                    const ctx = document.getElementById('sfaoGenderChart');
                    if (!ctx) return;

                    if (this.charts.gender) {
                        this.charts.gender.destroy();
                    }

                    this.charts.gender = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ['Male', 'Female'],
                            datasets: [{
                                data: [
                                    this.filteredData.genderStats?.Male || 0,
                                    this.filteredData.genderStats?.Female || 0
                                ],
                                backgroundColor: ['#3B82F6', '#EC4899'],
                                borderWidth: 0
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

                updateGenderChart() {
                    if (this.charts.gender) {
                        this.charts.gender.data.datasets[0].data = [
                            this.filteredData.genderStats?.Male || 0,
                            this.filteredData.genderStats?.Female || 0
                        ];
                        this.charts.gender.update();
                    }
                },

                getChartDataForType(type) {
                    if (type === 'application_status') {
                        return {
                            labels: ['Applied', 'Not Applied', 'Pending', 'Approved', 'Rejected'],
                            datasets: [{
                                data: [
                                    this.filteredData.students_with_applications || 0,
                                    (this.filteredData.total_students || 0) - (this.filteredData.students_with_applications || 0),
                                    this.filteredData.pending_applications || 0,
                                    this.filteredData.approved_applications || 0,
                                    this.filteredData.rejected_applications || 0
                                ],
                                backgroundColor: ['#3B82F6', '#6B7280', '#F59E0B', '#10B981', '#EF4444']
                            }]
                        };
                    }
                    return {};
                },
                
                clearFilters() {
                    this.filters.campus = 'all';
                    this.filters.department = 'all';
                    this.filters.timePeriod = 'all';
                },
                
                refreshAnalytics() {
                    window.location.reload();
                }
            }));
        });
    </script>
</div>
