/**
 * SFAO Dashboard Specific Scripts
 */

// Fix Safari Back Cache Bug
window.addEventListener("pageshow", function (event) {
    if (event.persisted) {
        window.location.reload();
    }
});

// SFAO Statistics Tab Component
window.sfaoStatisticsTab = function (config = {}) {
    // Store chart instances globally or in a scoped tracking object (non-reactive)
    const chartInstances = {
        department: null
    };

    return {
        viewMode: 'applicants',
        analyticsData: config.analytics || {},
        campusOptions: config.campusOptions || [],
        filteredData: {
            department_stats: [],
            genderStats: { Male: 0, Female: 0 },
            scholarshipStats: {}
        },
        availableDepartments: [],
        localFilters: {
            department: 'all',
            program: 'all'
        },
        chartLegend: {
            approved: true,
            newScholars: true,
            oldScholars: true,
            rejected: true,
            pending: true,
            nonScholars: true
        },
        availablePrograms: [],
        filters: {
            campus: 'all',
            scholarship: 'all',
            timePeriod: 'all'
        },

        init() {
            console.log('SFAO Statistics tab initializing...');

            // Fallback for DOM parsing if config not provided (Backwards Compatibility)
            if (Object.keys(this.analyticsData).length === 0) {
                const dataEl = document.getElementById('sfao-analytics-data');
                if (dataEl) {
                    try {
                        this.analyticsData = JSON.parse(dataEl.dataset.analytics || '{}');
                    } catch (e) { console.error(e); }
                }
            }
            if (this.campusOptions.length === 0 && window.sfaoCampusOptions) {
                this.campusOptions = window.sfaoCampusOptions;
            }

            // Initial Filters
            this.filters.campus = localStorage.getItem('sfaoStatsCampus') || 'all';

            this.availableDepartments = this.analyticsData.all_departments || [];

            // Initialize logic
            this.updateDepartmentsList(this.filters.campus);
            this.updateFilteredData();

            // Reactivity
            this.$watch('filters.campus', (value) => {
                this.updateDepartmentsList(value);
                this.applyFilters();
            });

            this.$watch('filters.scholarship', () => this.applyFilters());
            this.$watch('filters.timePeriod', () => this.applyFilters());
            this.$watch('viewMode', () => this.applyFilters()); // Watch Student Type (Applicants/Scholars)

            // Watch Local Filters for Scholarship Status Graph
            this.$watch('localFilters.department', (val) => {
                this.updateProgramList();
                this.createDepartmentChart();
            });
            this.$watch('localFilters.program', () => this.createDepartmentChart());
            this.$watch('chartLegend', () => this.createDepartmentChart(), { deep: true }); // Watch Legend Changes


            // Initial Chart Render Check
            this.$nextTick(() => {
                if (this.$el.offsetParent !== null) { // Check visibility
                    // Small delay to ensure layout is calculated
                    setTimeout(() => this.createAllCharts(), 150);
                }
            });

            // Listen for sidebar campus selection (Global Event)
            window.addEventListener('set-stats-filter', (e) => {
                this.filters.campus = e.detail;
            });

            // Watch for dark mode changes to update chart colors
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.attributeName === 'class') {
                        this.createAllCharts();
                    }
                });
            });
            observer.observe(document.documentElement, { attributes: true });
        },

        handleTabChange(newTab) {
            if (newTab === 'statistics') {
                console.log('Tab changed to statistics, attempting render...');
                // Delay to allow x-show transition to start/finish
                setTimeout(() => {
                    this.createAllCharts();
                }, 300);
            }
        },

        getTextColor() {
            return document.documentElement.classList.contains('dark') ? '#ffffff' : '#374151';
        },

        getGenderPercentage(gender) {
            const male = this.filteredData.genderStats?.Male || 0;
            const female = this.filteredData.genderStats?.Female || 0;
            const total = male + female;
            if (total === 0) return 0;
            return gender === 'male' ? ((male / total) * 100).toFixed(1) : ((female / total) * 100).toFixed(1);
        },

        updateDepartmentsList(campusId) {
            if (campusId === 'all') {
                // All unique departments from all campuses
                const allShortNames = [...new Set(Object.values(this.analyticsData.campus_departments || {}).flat())];
                this.availableDepartments = (this.analyticsData.all_departments || []).filter(d => allShortNames.includes(d.short_name));
            } else {
                // Departments for specific campus
                const campusShortNames = (this.analyticsData.campus_departments || {})[campusId] || [];
                this.availableDepartments = (this.analyticsData.all_departments || []).filter(d => campusShortNames.includes(d.short_name));
            }

            // Reset department selection if invalid
            if (this.filters.department !== 'all' && !this.availableDepartments.find(d => d.short_name === this.filters.department)) {
                this.filters.department = 'all';
            }
        },

        updateProgramList() {
            const dept = this.localFilters.department;
            if (dept === 'all') {
                this.availablePrograms = [];
                this.localFilters.program = 'all';
            } else {
                // Get programs for this college (department)
                const programsMap = this.analyticsData.department_programs || {};
                this.availablePrograms = programsMap[dept] || [];
                this.localFilters.program = 'all';
            }
        },

        updateFilteredData() {
            let data = JSON.parse(JSON.stringify(this.analyticsData)); // Deep copy
            let allStudents = this.analyticsData.all_students_data || [];
            let allApplications = this.analyticsData.all_applications_data || [];

            // 1. Filter Department Stats based on Campus (Sidebar)
            let allowedDepartments = [];
            if (this.filters.campus === 'all') {
                const allDepts = Object.values(this.analyticsData.campus_departments || {}).flat();
                allowedDepartments = [...new Set(allDepts)];
            } else {
                allowedDepartments = (this.analyticsData.campus_departments || {})[this.filters.campus] || [];
                // Filter students by campus
                allStudents = allStudents.filter(s => s.campus_id == this.filters.campus);
                // Filter applications by campus
                allApplications = allApplications.filter(a => a.campus_id == this.filters.campus);
            }

            if (data.department_stats) {
                data.department_stats = data.department_stats.filter(d => allowedDepartments.includes(d.name));
            }

            // 2. Filter by Global Scholarship
            if (this.filters.scholarship !== 'all') {
                const selectedScholarship = (this.analyticsData.available_scholarships || []).find(s => s.id == this.filters.scholarship);
                if (selectedScholarship) {
                    allApplications = allApplications.filter(item => item.scholarship_name === selectedScholarship.scholarship_name);
                }
            }

            // 3. Filter by Time Period
            if (this.filters.timePeriod !== 'all') {
                const now = new Date();
                allApplications = allApplications.filter(a => {
                    const date = new Date(a.created_at);
                    if (this.filters.timePeriod === 'this_month') return date.getMonth() === now.getMonth() && date.getFullYear() === now.getFullYear();
                    if (this.filters.timePeriod === 'last_3_months') return date >= new Date(now.setMonth(now.getMonth() - 3));
                    if (this.filters.timePeriod === 'this_year') return date.getFullYear() === now.getFullYear();
                    return true;
                });
            }

            // Calculate Gender Stats
            const genderCounts = { Male: 0, Female: 0 };
            const uniqueUsers = new Set();

            allApplications.forEach(app => {
                // For filtered data, we already filtered by Campus/Scholarship/Time.
                // Now filter by View Mode (Applicants vs Scholars)
                if (this.viewMode === 'scholars' && (app.status !== 'approved' || !app.scholar_id)) return;

                // Count unique users
                if (app.user_id && !uniqueUsers.has(app.user_id)) {
                    uniqueUsers.add(app.user_id);
                    if (app.sex && genderCounts[app.sex] !== undefined) {
                        genderCounts[app.sex]++;
                    }
                }
            });
            data.genderStats = genderCounts;

            // Calculate Scholarship Stats
            const scholarshipStats = {};
            allApplications.forEach(app => {
                const name = app.scholarship_name || 'Unknown';
                if (!scholarshipStats[name]) scholarshipStats[name] = { scholars: 0, nonScholars: 0 };
                app.status === 'approved' ? scholarshipStats[name].scholars++ : scholarshipStats[name].nonScholars++;
            });
            data.scholarshipStats = scholarshipStats;

            this.filteredData = data;
        },

        applyFilters() {
            this.updateFilteredData();
            this.updateCharts();
        },

        getStatisticsHeader() {
            if (this.filters.campus === 'all' || !this.campusOptions.length) return 'All Statistics';
            const campus = this.campusOptions.find(c => c.id == this.filters.campus);
            return (campus ? campus.name : 'All') + ' Statistics';
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
            console.log('Creating charts...');
            if (typeof Chart === 'undefined') {
                console.warn('Chart.js not loaded yet.');
                return;
            }
            this.createDepartmentChart();
        },

        updateCharts() {
            this.updateDepartmentChart();
        },

        createDepartmentChart() {
            // "Scholarship Department" Chart (re-purposed from Dept Logic for now mostly handled the Scholarship Status names in previous impl)
            // Wait, previous impl called it "sfaoDepartmentChart" but logic grouped by Scholarship Name.
            // Keeping this as is, since user didn't ask to change the big chart, only remove the *other* smaller "Scholarship Scholar Status" chart.

            const ctx = document.getElementById('sfaoDepartmentChart');
            if (!ctx) return;

            if (chartInstances.department) chartInstances.department.destroy();

            // Start with Globally filtered applications (Campus, Global Scholarship, Time)
            let rawData = this.filteredData.all_applications_data || [];

            // Apply Local Filters
            // 1. Local Department (Status removed)
            if (this.localFilters.department !== 'all') {
                rawData = rawData.filter(item => item.college === this.localFilters.department);
            }
            // 3. Local Program
            if (this.localFilters.program !== 'all') {
                rawData = rawData.filter(item => item.program === this.localFilters.program);
            }

            // Group by Scholarship Name
            const groupedData = {};
            rawData.forEach(item => {
                const name = item.scholarship_name || 'Unknown';
                if (!groupedData[name]) {
                    groupedData[name] = { pending: 0, approved: 0, rejected: 0, newScholars: 0, oldScholars: 0, nonScholars: 0 };
                }

                if (this.viewMode === 'applicants') {
                    if (item.status === 'pending') groupedData[name].pending++;
                    else if (item.status === 'approved') groupedData[name].approved++;
                    else if (item.status === 'rejected') groupedData[name].rejected++;
                } else {
                    // Scholars Mode
                    if (item.status === 'approved' && item.scholar_id) {
                        if (item.scholar_type === 'new') groupedData[name].newScholars++;
                        else groupedData[name].oldScholars++;
                    }
                }
            });

            const labels = Object.keys(groupedData).sort();
            const pendingData = labels.map(l => groupedData[l].pending);
            const approvedData = labels.map(l => groupedData[l].approved);
            const rejectedData = labels.map(l => groupedData[l].rejected);
            const newScholarsData = labels.map(l => groupedData[l].newScholars);
            const oldScholarsData = labels.map(l => groupedData[l].oldScholars);
            // nonScholars no longer used in Scholars mode but logic preserved in object structure for safety

            // Process labels for multi-line display to avoid diagonal rotation
            const processedLabels = labels.map(label => {
                const words = label.split(' ');
                const lines = [];
                let currentLine = [];

                words.forEach(word => {
                    // Start new line if current line has >= 2 words OR line length exceeds 15 chars
                    if (currentLine.length >= 2 || (currentLine.join(' ').length + word.length > 15)) {
                        lines.push(currentLine.join(' '));
                        currentLine = [];
                    }
                    currentLine.push(word);
                });
                if (currentLine.length > 0) lines.push(currentLine.join(' '));

                return lines;
            });

            let datasets = [];
            if (this.viewMode === 'applicants') {
                datasets = [
                    {
                        label: 'Approved',
                        data: approvedData,
                        backgroundColor: '#10B981',
                        hidden: !this.chartLegend.approved
                    },
                    {
                        label: 'Rejected',
                        data: rejectedData,
                        backgroundColor: '#EF4444',
                        hidden: !this.chartLegend.rejected
                    },
                    {
                        label: 'Pending',
                        data: pendingData,
                        backgroundColor: '#F59E0B',
                        hidden: !this.chartLegend.pending
                    }
                ];
            } else {
                datasets = [
                    {
                        label: 'New Scholars',
                        data: newScholarsData,
                        backgroundColor: '#3B82F6',
                        hidden: !this.chartLegend.newScholars
                    },
                    {
                        label: 'Old Scholars',
                        data: oldScholarsData,
                        backgroundColor: '#10B981',
                        hidden: !this.chartLegend.oldScholars
                    }
                ];
            }

            chartInstances.department = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: processedLabels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            ticks: {
                                color: this.getTextColor(),
                                autoSkip: false, // Ensure all labels are shown
                                maxRotation: 0,  // Force horizontal
                                minRotation: 0   // Force horizontal
                            },
                            stacked: true
                        },
                        y: {
                            ticks: {
                                color: this.getTextColor(),
                                stepSize: 1,
                                precision: 0
                            },
                            stacked: true
                        }
                    },
                    plugins: {
                        legend: { display: false }, // Use custom legend
                        tooltip: {
                            callbacks: {
                                title: function (context) {
                                    const label = context[0].label;
                                    return Array.isArray(label) ? label.join(' ') : label;
                                }
                            }
                        }
                    }
                }
            });
        },

        updateDepartmentChart() {
            if (chartInstances.department) {
                chartInstances.department.destroy(); // Simple destroy and recreate for reliability with complex data changes
                this.createDepartmentChart();
            }
        },

        createGenderChart() {
            const ctx = document.getElementById('sfaoGenderChart');
            if (!ctx) return;
            if (chartInstances.gender) chartInstances.gender.destroy();

            chartInstances.gender = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Male', 'Female'],
                    datasets: [{
                        data: [this.filteredData.genderStats?.Male || 0, this.filteredData.genderStats?.Female || 0],
                        backgroundColor: ['#3B82F6', '#EC4899'], borderWidth: 0
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { color: this.getTextColor() } } }
                }
            });
        },

        updateGenderChart() {
            if (chartInstances.gender) {
                chartInstances.gender.data.datasets[0].data = [
                    this.filteredData.genderStats?.Male || 0,
                    this.filteredData.genderStats?.Female || 0
                ];
                chartInstances.gender.update();
            } else {
                this.createGenderChart();
            }
        },

        createScholarshipStatusChart() {
            const ctx = document.getElementById('sfaoScholarshipTypeChart');
            if (!ctx) return;
            if (chartInstances.scholarshipType) chartInstances.scholarshipType.destroy();

            const rawLabels = Object.keys(this.filteredData.scholarshipStats || {});
            const labels = rawLabels.map(name => name.split(' '));
            const scholars = rawLabels.map(name => this.filteredData.scholarshipStats[name].scholars);
            const nonScholars = rawLabels.map(name => this.filteredData.scholarshipStats[name].nonScholars);

            chartInstances.scholarshipType = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Scholars', data: scholars, backgroundColor: '#10B981' },
                        { label: 'Non-Scholars', data: nonScholars, backgroundColor: '#EF4444' }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { color: this.getTextColor() } } },
                    scales: { x: { ticks: { color: this.getTextColor(), autoSkip: false } }, y: { ticks: { color: this.getTextColor() } } }
                }
            });
        },

        updateScholarshipStatusChart() {
            if (chartInstances.scholarshipType) {
                chartInstances.scholarshipType.destroy(); // Recreate to be safe with label changes
                this.createScholarshipStatusChart();
            }
        },

        clearFilters() {
            this.filters.campus = 'all';
            this.filters.department = 'all';
            this.filters.timePeriod = 'all';
        },

        clearLocalFilters() {
            this.localFilters.status = 'all';
            this.localFilters.department = 'all';
            this.localFilters.program = 'all';
            this.availablePrograms = [];
        }
    };
};

// SFAO Dashboard State (Main Layout)
window.sfaoDashboardState = function (config) {
    return {
        tab: 'statistics',
        statsCampus: localStorage.getItem('sfaoStatsCampus') || config.defaultStatsCampus,
        openDropdowns: { dashboard: false, scholarships: false, applicants: false, scholars: false, reports: false },

        urlMapping: {
            'dashboard': 'statistics',
            'analytics': 'statistics',
            'all_scholarships': 'scholarships',
            'private_scholarships': 'scholarships-private',
            'government_scholarships': 'scholarships-government',
            'all_applicants': 'applicants',
            'applicants_not_applied': 'applicants-not_applied',
            'applicants_in_progress': 'applicants-in_progress',
            'applicants_pending': 'applicants-pending',
            'applicants_approved': 'applicants-approved',
            'applicants_rejected': 'applicants-rejected',
            'all_scholars': 'scholars',
            'new_scholars': 'scholars-new',
            'old_scholars': 'scholars-old',
            'reports_student_summary': 'reports-student_summary',
            'reports_scholar_summary': 'reports-scholar_summary',
            'reports_grant_summary': 'reports-grant_summary',
            'account_settings': 'account'
        },

        init() {
            this.$watch('statsCampus', val => localStorage.setItem('sfaoStatsCampus', val));

            // Restore Dropdowns
            const savedDropdowns = localStorage.getItem(`sfaoDropdowns_${config.userId}`);
            if (savedDropdowns) {
                this.openDropdowns = JSON.parse(savedDropdowns);
            }

            // Watch Dropdowns
            this.$watch('openDropdowns', val => {
                localStorage.setItem(`sfaoDropdowns_${config.userId}`, JSON.stringify(val));
            });

            // Watch Tab Change
            this.$watch('tab', val => {
                localStorage.setItem('sfaoTab', val);
                this.updateUrl(val);
                this.syncDropdowns(val);
                this.$dispatch('tab-changed', val);
            });

            // Listen for Sidebar Tab Switch Events
            window.addEventListener('switch-tab', event => {
                this.tab = event.detail;
            });

            // Initialize from URL or LocalStorage
            const urlParams = new URLSearchParams(window.location.search);
            const urlTab = urlParams.get('tabs');

            if (urlTab && this.urlMapping[urlTab]) {
                this.tab = this.urlMapping[urlTab];
            } else {
                this.tab = localStorage.getItem('sfaoTab') || config.activeTab || 'statistics';
            }

            // Ensure correct dropdown is open
            if (!savedDropdowns) {
                this.syncDropdowns(this.tab);
            }

            this.updateUrl(this.tab);
        },

        updateUrl(currentTab) {
            const key = Object.keys(this.urlMapping).find(k => this.urlMapping[k] === currentTab);
            if (key) {
                const url = new URL(window.location);
                url.searchParams.set('tabs', key);
                url.searchParams.delete('tab');
                window.history.pushState({}, '', url);
            }
        },

        syncDropdowns(currentTab) {
            if (currentTab === 'statistics') this.openDropdowns.dashboard = true;
            else if (currentTab.startsWith('scholarships')) this.openDropdowns.scholarships = true;
            else if (currentTab.startsWith('applicants')) this.openDropdowns.applicants = true;
            else if (currentTab.startsWith('scholars')) this.openDropdowns.scholars = true;
            else if (currentTab.startsWith('reports')) this.openDropdowns.reports = true;
        }
    };
};

// SFAO Scholarships Filter
window.sfaoScholarshipsFilter = function (routeUrl) {
    return {
        filters: {
            sort_by: localStorage.getItem('sfaoScholarshipsSortBy') || 'name',
            sort_order: localStorage.getItem('sfaoScholarshipsSortOrder') || 'asc',
            type: localStorage.getItem('sfaoScholarshipsType') || 'all'
        },

        init() {
            this.$watch('filters.sort_by', (value) => {
                localStorage.setItem('sfaoScholarshipsSortBy', value);
                this.fetchScholarships();
            });
            this.$watch('filters.sort_order', (value) => {
                localStorage.setItem('sfaoScholarshipsSortOrder', value);
                this.fetchScholarships();
            });
            this.$watch('filters.type', (value) => {
                localStorage.setItem('sfaoScholarshipsType', value);
                this.fetchScholarships();
            });

            this.updatePaginationLinks();

            if (this.filters.type !== 'all') {
                this.fetchScholarships();
            }
        },

        fetchScholarships(page = 1) {
            const params = new URLSearchParams({
                tab: 'scholarships',
                sort_by: this.filters.sort_by,
                sort_order: this.filters.sort_order,
                type_filter: this.filters.type,
                page_scholarships: page
            });

            fetch(`${routeUrl}?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('scholarships-list-container').innerHTML = data.html;
                    this.updatePaginationLinks();
                })
                .catch(error => console.error('Error fetching scholarships:', error));
        },

        updatePaginationLinks() {
            const container = document.getElementById('scholarships-list-container');
            if (!container) return;
            const links = container.querySelectorAll('a.page-link');
            links.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const url = new URL(link.href);
                    const page = url.searchParams.get('page_scholarships') || 1;
                    this.fetchScholarships(page);
                });
            });
        },

        resetFilters() {
            this.filters.sort_by = 'name';
            this.filters.sort_order = 'asc';
            this.filters.type = 'all';
        },

        getHeaderTitle() {
            if (this.filters.type === 'private') {
                return 'Private Scholarships';
            } else if (this.filters.type === 'government') {
                return 'Government Scholarships';
            }
            return 'All Scholarships';
        },

        getHeaderDescription() {
            if (this.filters.type === 'private') {
                return 'Private scholarship programs';
            } else if (this.filters.type === 'government') {
                return 'Government scholarship programs';
            }
            return 'View all available scholarship programs';
        },

        handleTabChange(tab) {
            if (tab === 'scholarships') {
                if (this.filters.type !== 'all') {
                    this.filters.type = 'all';
                }
            } else if (tab.startsWith('scholarships-')) {
                const type = tab.replace('scholarships-', '');
                if (this.filters.type !== type) {
                    this.filters.type = type;
                }
            }
        }
    };
};

// SFAO Applicants Filter
window.sfaoApplicantsFilter = function (config) {
    return {
        filters: {
            sort_by: localStorage.getItem('sfaoApplicantsSortBy') || 'name',
            sort_order: localStorage.getItem('sfaoApplicantsSortOrder') || 'asc',
            campus: localStorage.getItem('sfaoApplicantsCampus') || 'all',
            status: 'all'
        },
        counts: config.counts || {},
        campusOptions: config.campusOptions || [],
        sfaoCampusName: config.sfaoCampusName || '',
        extensionCampuses: config.extensionCampuses || [],
        currentTab: 'applicants',
        showModal: false,
        selectedApplicant: null,

        init() {
            this.$watch('filters.sort_by', (value) => {
                localStorage.setItem('sfaoApplicantsSortBy', value);
                this.fetchApplicants();
            });
            this.$watch('filters.sort_order', (value) => {
                localStorage.setItem('sfaoApplicantsSortOrder', value);
                this.fetchApplicants();
            });
            this.$watch('filters.campus', (value) => {
                localStorage.setItem('sfaoApplicantsCampus', value);
                this.fetchApplicants();
            });
            this.$watch('filters.status', (value) => {
                this.fetchApplicants();
            });

            this.updatePaginationLinks();

            document.addEventListener('open-applicant-modal', (e) => {
                this.openModal(e.detail);
            });
        },

        openModal(applicant) {
            this.selectedApplicant = applicant;
            this.showModal = true;
            document.body.style.overflow = 'hidden';
        },

        closeModal() {
            this.showModal = false;
            this.selectedApplicant = null;
            document.body.style.overflow = '';
        },

        formatDate(dateString) {
            if (!dateString) return 'N/A';
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString(undefined, options);
        },

        fetchApplicants(page = 1) {
            const params = new URLSearchParams({
                tab: this.currentTab,
                sort_by: this.filters.sort_by,
                sort_order: this.filters.sort_order,
                campus_filter: this.filters.campus,
                status_filter: this.filters.status,
                page_applicants: page
            });

            fetch(`${config.routeUrl}?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.html) {
                        const container = document.getElementById('applicants-list-container');
                        if (container) container.innerHTML = data.html;
                    }
                    if (data.counts) {
                        this.counts = data.counts;
                    }
                    this.updatePaginationLinks();
                })
                .catch(error => console.error('Error fetching applicants:', error));
        },

        updatePaginationLinks() {
            const container = document.getElementById('applicants-list-container');
            if (!container) return;

            const links = container.querySelectorAll('a.page-link');
            links.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const url = new URL(link.href);
                    const page = url.searchParams.get('page_applicants') || 1;
                    this.fetchApplicants(page);
                });
            });
        },

        resetFilters() {
            this.filters.sort_by = 'name';
            this.filters.sort_order = 'asc';
            this.filters.campus = 'all';
            this.filters.status = 'all';
        },

        getHeaderTitle() {
            let title = 'All Applicants';
            let campusName = 'All';

            if (this.filters.campus !== 'all') {
                const campus = this.campusOptions.find(c => c.id == this.filters.campus);
                if (campus) campusName = campus.name;
            }

            if (this.currentTab === 'applicants') {
                title = campusName === 'All' ? 'All Applicants' : `${campusName} Applicants`;
            } else if (this.currentTab === 'applicants-not_applied') {
                title = campusName === 'All' ? 'Not Applied Students' : `${campusName} - Not Applied`;
            } else if (this.currentTab.startsWith('applicants-')) {
                const status = this.currentTab.replace('applicants-', '').replace('_', ' ');
                const statusLabel = status.charAt(0).toUpperCase() + status.slice(1);
                title = campusName === 'All' ? statusLabel : `${campusName} - ${statusLabel}`;
            }

            return title;
        },

        getHeaderDescription() {
            let desc = '';
            let campusName = this.sfaoCampusName;

            if (this.currentTab === 'applicants') {
                desc = `All students with applications from ${campusName}`;
            } else if (this.currentTab === 'applicants-not_applied') {
                desc = `Students who have not submitted any applications`;
            } else {
                const status = this.currentTab.replace('applicants-', '').replace('_', ' ');
                desc = `Students with ${status} applications from ${campusName}`;
            }

            if (this.extensionCampuses.length > 0) {
                desc += ` and its extensions`;
            }
            return desc;
        },

        handleTabChange(tab) {
            this.currentTab = tab;

            if (tab === 'applicants') {
                this.filters.status = 'all';
            } else if (tab.startsWith('applicants-')) {
                this.filters.status = tab.replace('applicants-', '');
            }

            this.fetchApplicants();
        }
    };
};

// SFAO Scholars Filter
window.sfaoScholarsFilter = function (config) {
    return {
        filters: {
            sort_by: localStorage.getItem('sfaoScholarsSortBy') || 'created_at',
            sort_order: localStorage.getItem('sfaoScholarsSortOrder') || 'desc',
            campus: localStorage.getItem('sfaoScholarsCampus') || 'all',
            status: localStorage.getItem('sfaoScholarsStatus') || 'all',
            type: localStorage.getItem('sfaoScholarsType') || 'all'
        },
        counts: config.counts || {},
        campusOptions: config.campusOptions || [],
        sfaoCampusName: config.sfaoCampusName || '',
        extensionCampuses: config.extensionCampuses || [],

        init() {
            this.$watch('filters.sort_by', (value) => {
                localStorage.setItem('sfaoScholarsSortBy', value);
                this.fetchScholars();
            });
            this.$watch('filters.sort_order', (value) => {
                localStorage.setItem('sfaoScholarsSortOrder', value);
                this.fetchScholars();
            });
            this.$watch('filters.campus', (value) => {
                localStorage.setItem('sfaoScholarsCampus', value);
                this.fetchScholars();
            });
            this.$watch('filters.status', (value) => {
                localStorage.setItem('sfaoScholarsStatus', value);
                this.fetchScholars();
            });
            this.$watch('filters.type', (value) => {
                localStorage.setItem('sfaoScholarsType', value);
                this.fetchScholars();
            });

            if (this.filters.status !== 'all' || this.filters.campus !== 'all' || this.filters.type !== 'all') {
                this.fetchScholars();
            }
        },

        fetchScholars() {
            const params = new URLSearchParams({
                tab: 'scholars',
                scholars_sort_by: this.filters.sort_by,
                scholars_sort_order: this.filters.sort_order,
                campus_filter: this.filters.campus,
                status_filter: this.filters.status,
                type_filter: this.filters.type
            });

            fetch(`${config.routeUrl}?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('scholars-list-container');
                    if (container) container.innerHTML = data.html;
                    if (data.counts) this.counts = data.counts;
                })
                .catch(error => console.error('Error fetching scholars:', error));
        },

        resetFilters() {
            this.filters.sort_by = 'created_at';
            this.filters.sort_order = 'desc';
            this.filters.campus = 'all';
            this.filters.status = 'all';
            this.filters.type = 'all';
        },

        getHeaderTitle() {
            let title = 'All Scholars';
            let campusName = 'All';

            if (this.filters.campus !== 'all') {
                const campus = this.campusOptions.find(c => c.id == this.filters.campus);
                if (campus) campusName = campus.name;
            }

            let typeLabel = 'Scholars';
            if (this.filters.type === 'new') typeLabel = 'New Scholars';
            else if (this.filters.type === 'old') typeLabel = 'Old Scholars';

            title = campusName === 'All' ? (this.filters.type === 'all' ? 'All Scholars' : typeLabel) : `${campusName} - ${typeLabel}`;

            return title;
        },

        getHeaderDescription() {
            let desc = '';
            let campusName = this.sfaoCampusName;

            if (this.filters.type === 'all') {
                desc = `All students who have been accepted as scholars from ${campusName}`;
            } else if (this.filters.type === 'new') {
                desc = `Scholars who have not yet received any grant from ${campusName}`;
            } else if (this.filters.type === 'old') {
                desc = `Continuing scholars with one or more grants from ${campusName}`;
            }

            if (this.extensionCampuses.length > 0) {
                desc += ` and its extension campuses: ${this.extensionCampuses.join(', ')}`;
            }
            return desc;
        },

        handleTabChange(tab) {
            if (tab === 'scholars') {
                if (this.filters.type !== 'all') {
                    this.filters.type = 'all';
                }
            } else if (tab.startsWith('scholars-')) {
                const type = tab.replace('scholars-', '');
                if (this.filters.type !== type) {
                    this.filters.type = type;
                }
            }
        }
    };
};

