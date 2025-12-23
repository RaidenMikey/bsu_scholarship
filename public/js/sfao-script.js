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
        department: null,
        gender: null,
        scholarshipType: null,
        comparison: null,
        trend: null
    };

    return {
        viewMode: 'applicants',
        analyticsData: config.analytics || {},
        campusOptions: config.campusOptions || [],
        academicYearOptions: [], // Dynamic list
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
            inProgress: true,
            nonScholars: true
        },
        chartStatus: { // Track if charts have data
            department: true,
            comparison: true,
            trend: true
        },
        availablePrograms: [],
        filters: {
            campus: 'all',
            scholarship: 'all',
            timePeriod: 'all'
        },

        init() {
            try {


                // Fallback for DOM parsing if config not provided (Backwards Compatibility)
                if (Object.keys(this.analyticsData).length === 0) {
                    const dataEl = document.getElementById('sfao-analytics-data');
                    if (dataEl) {
                        try {
                            this.analyticsData = JSON.parse(dataEl.dataset.analytics || '{}');
                        } catch (e) { console.error('Error parsing inline analytics data', e); }
                    }
                }
                if (this.campusOptions.length === 0 && window.sfaoCampusOptions) {
                    this.campusOptions = window.sfaoCampusOptions;
                }

                // Initial Filters
                this.filters.campus = localStorage.getItem('sfaoStatsCampus') || 'all';

                // Persist ViewMode (Student Type)
                const savedViewMode = localStorage.getItem('sfao_view_mode');
                if (savedViewMode && ['applicants', 'scholars'].includes(savedViewMode)) {
                    this.viewMode = savedViewMode;
                }
                this.$watch('viewMode', (val) => {
                    localStorage.setItem('sfao_view_mode', val);
                    this.applyFilters();
                });

                // Persistence Block Removed (Moved to end of init)
                this.$watch('filters.scholarship', (val) => {
                    localStorage.setItem('sfao_scholarship_filter', String(val));
                    this.applyFilters();
                });



                this.availableDepartments = this.analyticsData.all_departments || [];

                // Initialize logic
                this.updateDepartmentsList(this.filters.campus);
                this.generateAcademicYears(); // Populate AY options
                this.updateProgramList();

                // DEFERRED RESTORATION:
                // Move the restoration logic here, at the END of init, and wrap in nextTick.
                // This ensures all data (available_scholarships) is ready and no other logic overrides it.
                this.$nextTick(() => {
                    const savedScholarship = localStorage.getItem('sfao_scholarship_filter');

                    if (savedScholarship !== null) {
                        let val = savedScholarship.replace(/^"|"$/g, '');
                        if (val !== 'all') {
                            val = String(val);
                        }

                        // Validation: Check if ID exists in available list
                        const exists = val === 'all' || (this.analyticsData.available_scholarships || []).some(s => String(s.id) === val);

                        if (exists) {
                            this.filters.scholarship = val;
                        } else {
                            this.filters.scholarship = 'all';
                        }
                    } else {
                        // Default for new users
                        // Logic: If NO history, maybe default to first scholarship? Or keep 'all'?
                        // User wanted to keep 'all' if selected.
                        const available = this.analyticsData.available_scholarships || [];
                        if (available.length > 0) {
                            this.filters.scholarship = String(available[0].id);
                        }
                    }
                    // Apply filters AFTER setting the restored value
                    this.applyFilters();
                });

                // Standard Initial Data Load (will be refreshed by applyFilters inside nextTick anyway)
                // this.updateFilteredData(); // Commented out to avoid double render, let nextTick handle it.

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
                    this.applyFilters(); // Recalculate ALL stats
                });
                this.$watch('localFilters.program', () => this.applyFilters()); // Recalculate ALL stats
                this.$watch('chartLegend', () => {
                    this.applyFilters();
                }, { deep: true }); // Watch Legend Changes


                // Initial Chart Render Check
                this.$nextTick(() => {
                    // FORCE creation without visibility check.
                    // The ResizeObserver in createAllCharts will handle the "waiting for dimension" part.
                    // This fixes the bug where charts didn't load if tab content wasn't immediately fully visible.
                    setTimeout(() => this.createAllCharts(), 300);
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
            } catch (error) {
                console.error('CRITICAL ERROR in SFAO Statistics Tab init:', error);
            }
        },

        handleTabChange(newTab) {
            // Always try to render charts if switching to analytics
            if (newTab === 'analytics') {

                // Force a resize event to trigger observers
                window.dispatchEvent(new Event('resize'));

                // Use requestAnimationFrame for smoother UI check
                requestAnimationFrame(() => {
                    setTimeout(() => {
                        this.createAllCharts();
                    }, 100); // Reduce delay slightly, rely on retries if needed
                });
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


            const programsMap = this.analyticsData.department_programs || {};


            if (dept === 'all') {
                // Get ALL programs from ALL departments
                const allPrograms = new Set();
                Object.values(programsMap).forEach(progList => {
                    if (Array.isArray(progList)) {
                        progList.forEach(p => allPrograms.add(p));
                    }
                });

                this.availablePrograms = Array.from(allPrograms).sort();
                this.localFilters.program = 'all';
            } else {
                // Get programs for this college (department)
                const rawProgs = programsMap[dept] || [];
                // Ensure it's a plain array to avoid Proxy complications in the view
                this.availablePrograms = Array.from(rawProgs);


                // ONLY reset if the current selection is no longer valid
                if (this.localFilters.program !== 'all' && !this.availablePrograms.includes(this.localFilters.program)) {
                    this.localFilters.program = 'all';
                }
            }
        },

        generateAcademicYears() {
            const allApps = this.analyticsData.all_applications_data || [];
            const years = new Set();
            allApps.forEach(a => {
                if (!a.created_at) return;
                const date = new Date(a.created_at);
                const year = date.getFullYear();
                const month = date.getMonth(); // 0-11
                // AY Logic: If Month >= 7 (August), AY = Year-(Year+1). Else (Year-1)-Year.
                // Adjusted: Typically AY starts Aug/Sept. Let's use Aug (Index 7).
                const startYear = month >= 7 ? year : year - 1;
                years.add(`${startYear}-${startYear + 1}`);
            });
            // Also add current year if empty
            if (years.size === 0) {
                const now = new Date();
                const curY = now.getMonth() >= 7 ? now.getFullYear() : now.getFullYear() - 1;
                years.add(`${curY}-${curY + 1}`);
            }
            this.academicYearOptions = Array.from(years).sort().reverse();
            // Default to 'all' or possibly the latest AY? Keeping 'all' as default.
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

            // 2. Filter by Scholarship (MANDATORY single selection now)
            // Even if 'all' is passed somehow, we try to grab the first one, or filter nothing if list empty (but UI prevents this)
            if (this.filters.scholarship && this.filters.scholarship !== 'all') {
                const selectedScholarship = (this.analyticsData.available_scholarships || []).find(s => s.id == this.filters.scholarship);
                if (selectedScholarship) {
                    allApplications = allApplications.filter(item => item.scholarship_name === selectedScholarship.scholarship_name);
                }
            } else {
                // Fallback if somehow still 'all' (though option removed)
                // We don't filter here, but UI should have forced a selection. 
                // If we strictly want to prevent 'all', we could force filter to first item here too.
            }

            // 3. Filter by Time Period (Academic Year)
            if (this.filters.timePeriod !== 'all') {
                allApplications = allApplications.filter(a => {
                    if (!a.created_at) return false;
                    const date = new Date(a.created_at);
                    const year = date.getFullYear();
                    const month = date.getMonth();
                    const startYear = month >= 7 ? year : year - 1;
                    const ay = `${startYear}-${startYear + 1}`;
                    return ay === this.filters.timePeriod;
                });
            }

            // 4. Apply Local Filters (Moved from createDepartmentChart)
            // Filter by Department
            if (this.localFilters.department !== 'all') {
                allApplications = allApplications.filter(item => item.college === this.localFilters.department);
            }
            // Filter by Program
            if (this.localFilters.program !== 'all') {
                allApplications = allApplications.filter(item => item.program === this.localFilters.program);
            }

            // Calculate Gender Stats
            const genderCounts = { Male: 0, Female: 0 };
            const uniqueUsers = new Set();

            allApplications.forEach(app => {
                // For filtered data, we already filtered by Campus/Scholarship/Time.
                // Now filter by View Mode (Applicants vs Scholars)
                if (this.viewMode === 'scholars' && (app.status !== 'approved' || !app.scholar_id)) return;

                // CRITICAL FIX: Applicants View should EXCLUDE existing scholars to match the Tab list
                // Renamed to is_global_scholar to avoid cache/naming conflicts
                const isScholarVal = Number(app.is_global_scholar);
                if (this.viewMode === 'applicants' && isScholarVal > 0) return;

                // CRITICAL FIX: Ensure Gender Stats Matches Graph Display
                if (this.viewMode === 'applicants') {
                    if (!['pending', 'approved', 'rejected', 'in_progress'].includes(app.status)) return;
                    // Respect Chart Legend (Button Toggles)
                    if (app.status === 'pending' && !this.chartLegend.pending) return;
                    if (app.status === 'approved' && !this.chartLegend.approved) return;
                    if (app.status === 'rejected' && !this.chartLegend.rejected) return;
                    if (app.status === 'in_progress' && !this.chartLegend.inProgress) return;
                } else {
                    // Scholars View Mode
                    // Respect Chart Legend for Scholars (New/Old)
                    if (app.status === 'approved' && app.scholar_id) {
                        const isNew = app.scholar_type === 'new'; // Assuming scholar_type available
                        if (isNew && !this.chartLegend.newScholars) return;
                        if (!isNew && !this.chartLegend.oldScholars) return;
                    }
                }

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
            // Calculate Scholarship Stats (Counting Unique Users)
            const scholarshipStats = {};
            // Iterate allApplications to aggregate unique users per scholarship/status
            allApplications.forEach(app => {
                // Skip if excluded by View Mode
                if (this.viewMode === 'scholars' && (app.status !== 'approved' || !app.scholar_id)) return;

                const isScholarVal2 = Number(app.is_global_scholar);
                if (this.viewMode === 'applicants' && isScholarVal2 > 0) return;

                const name = app.scholarship_name || 'Unknown';
                if (!scholarshipStats[name]) {
                    scholarshipStats[name] = {
                        scholars: new Set(),
                        nonScholars: new Set()
                    };
                }
                // Categorize
                if (app.status === 'approved' && app.scholar_id) {
                    scholarshipStats[name].scholars.add(app.user_id);
                } else {
                    scholarshipStats[name].nonScholars.add(app.user_id);
                }
            });

            // Flatten Sets to Counts
            const finalScholarshipStats = {};
            Object.keys(scholarshipStats).forEach(key => {
                finalScholarshipStats[key] = {
                    scholars: scholarshipStats[key].scholars.size,
                    nonScholars: scholarshipStats[key].nonScholars.size
                };
            });

            data.scholarshipStats = finalScholarshipStats;

            // CRITICAL FIX: Update the available raw data for the Department Chart to use the FILTERED list
            data.all_applications_data = allApplications;

            // Calculate Dynamic Summary Counts (Total, Approved, Rejected, Rate) based on FILTERED data
            const summaryCounts = {
                total: 0,
                approved: 0,
                rejected: 0,
                approvalRate: 0,
                inProgress: 0
            };

            // Use the already filtered 'allApplications' for these counts
            // Note: 'allApplications' here is already filtered by Campus, Scholarship, Time, Dept, Program.
            // But we must also check ViewMode if we want these cards to match the Applicant/Scholar view?
            // User request implies "Total Applications", so usually Applicants view.
            // If ViewMode is Scholars, "Pending" doesn't make sense.
            // Let's stick to counting based on the current filtered set, respecting ViewMode for consistency.

            allApplications.forEach(app => {
                // Check View Mode filters (already checking inside the loop above? No, allApplications is pre-view-mode logic in some parts)
                // actually allApplications is filtered by Campus/Scholarship/Time/Dept/Program.
                // It is NOT yet filtered by ViewMode (that happens inside the gender/scholarship loop separately).
                // So we must apply ViewMode logic here too.

                if (this.viewMode === 'scholars' && (app.status !== 'approved' || !app.scholar_id)) return;
                const isScholarVal = Number(app.is_global_scholar);
                if (this.viewMode === 'applicants' && isScholarVal > 0) return;

                // For Applicants View, we usually only care about active statuses
                if (this.viewMode === 'applicants' && !['pending', 'approved', 'rejected', 'in_progress'].includes(app.status)) return;

                summaryCounts.total++;

                if (app.status === 'approved') summaryCounts.approved++;
                if (app.status === 'rejected') summaryCounts.rejected++;
                if (app.status === 'in_progress') summaryCounts.inProgress++;
                if (app.status === 'pending') summaryCounts.pending++;
            });

            // Calculate "Active" (Remaining) to ensure Total = Approved + Rejected + Active
            // This usually covers Pending + In Progress
            summaryCounts.active = summaryCounts.total - summaryCounts.approved - summaryCounts.rejected;

            if (summaryCounts.total > 0) {
                summaryCounts.approvalRate = ((summaryCounts.approved / summaryCounts.total) * 100).toFixed(1);
            } else {
                summaryCounts.approvalRate = '0.0';
            }

            // Assign to data.counts (creating it if missing)
            data.counts = summaryCounts;

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

        getChartTitle() {
            if (this.filters.campus === 'all') {
                return 'Scholarship Status (Campus Comparison)';
            }
            // Find campus name
            const campus = this.campusOptions.find(c => c.id == this.filters.campus);
            const name = campus ? campus.name : 'Department Comparison';
            return `Scholarship Status (${name} - Departments)`;
        },

        createAllCharts() {

            if (typeof Chart === 'undefined') {
                console.error('Chart.js not loaded yet. Retrying in 500ms...');
                setTimeout(() => this.createAllCharts(), 500);
                return;
            }
            const ctx = document.getElementById('sfaoDepartmentChart');
            if (!ctx) {
                console.error('Canvas sfaoDepartmentChart not found in DOM');
                // Retry in case DOM insertion is slow or x-show transition is still running logic
                setTimeout(() => this.createAllCharts(), 500);
                return;
            }

            // CHECK if canvas is visible or has dimensions
            if (ctx.clientWidth === 0 || ctx.clientHeight === 0) {
                console.warn('Canvas found but has 0 dimensions (hidden?). Retrying in 200ms...');
                setTimeout(() => this.createAllCharts(), 200);
                return;
            }



            // Clean up old instances if somehow they exist but we are re-entering context
            // Note: chartInstances is global to this closure (function scope)

            // Auto-resize observer to handle x-show transitions
            const container = ctx.parentElement;

            // Disconnect old observer if exists (need to track it if we want to be strict, but for now just New one)
            // Ideally we should track the observer too.

            const ro = new ResizeObserver(() => {
                if (chartInstances.department && ctx.getBoundingClientRect().width > 0) {
                    chartInstances.department.resize();
                } else if (!chartInstances.department && ctx.getBoundingClientRect().width > 0) {
                    // Late binding create if it became visible just now
                    this.createDepartmentChart();
                }
            });
            ro.observe(container);

            this.createDepartmentChart();
            this.createComparisonChart();
            this.createTrendChart();
            this.createGenderChart();
            this.createScholarshipStatusChart();
        },

        updateCharts() {
            this.updateDepartmentChart();
            this.updateComparisonChart();
            this.updateTrendChart();
            this.updateGenderChart();
            this.updateScholarshipStatusChart();
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
            // Apply Local Filters

            // NOTE: Local Filters are now applied in updateFilteredData, so rawData is ALREADY filtered by Dept/Program.
            // We keep the logging but remove the redundant filter to prevent double-filtering (though harmless here, it's cleaner).



            // Group by Department OR Campus based on View
            const groupedData = {};

            // MAP Campus IDs to Names
            const campusMap = {};
            this.campusOptions.forEach(c => {
                if (c.id !== 'all') campusMap[c.id] = c.name;
            });

            // Logic:
            // If filters.campus === 'all', we want to compare CAMPUSES (e.g. Alangilan vs Pablo Borbon)
            // If filters.campus !== 'all', we want to compare DEPARTMENTS (e.g. CICS vs CAS) within that campus.

            const isComparisonMode = (this.filters.campus === 'all');

            // Initialize Groups to ensure 0-data bars appear if desired (Optional, maybe skip for cleaner chart)
            // For now, let's just populate from data found to avoid empty bars clutter.

            rawData.forEach(item => {
                // CRITICAL: Filter out Scholars if in Applicants Mode
                const isGlobalScholar = Number(item.is_global_scholar);
                if (this.viewMode === 'applicants' && isGlobalScholar > 0) return;

                // Determine Group Key
                let groupKey = 'Unknown';
                if (isComparisonMode) {
                    groupKey = campusMap[item.campus_id] || 'Other';
                } else {
                    groupKey = item.college || 'No Dept';
                }

                if (!groupedData[groupKey]) {
                    groupedData[groupKey] = {
                        pending: new Set(),
                        approved: new Set(),
                        rejected: new Set(),
                        newScholars: new Set(),
                        oldScholars: new Set(),
                        inProgress: new Set()
                    };
                }

                if (this.viewMode === 'applicants') {
                    if (item.status === 'pending') groupedData[groupKey].pending.add(item.user_id);
                    else if (item.status === 'approved') groupedData[groupKey].approved.add(item.user_id);
                    else if (item.status === 'rejected') groupedData[groupKey].rejected.add(item.user_id);
                    else if (item.status === 'in_progress') groupedData[groupKey].inProgress.add(item.user_id);
                } else {
                    // Scholars Mode
                    if (item.status === 'approved' && item.scholar_id) {
                        if (item.scholar_type === 'new') groupedData[groupKey].newScholars.add(item.user_id);
                        else groupedData[groupKey].oldScholars.add(item.user_id);
                    }
                }
            });

            // Filter labels based on VISIBLE data (legend state)
            const allLabels = Object.keys(groupedData).sort();

            const labels = allLabels.filter(name => {
                const data = groupedData[name];
                let visibleCount = 0;

                if (this.viewMode === 'applicants') {
                    if (this.chartLegend.approved) visibleCount += data.approved.size;
                    if (this.chartLegend.pending) visibleCount += data.pending.size;
                    if (this.chartLegend.rejected) visibleCount += data.rejected.size;
                    if (this.chartLegend.inProgress) visibleCount += data.inProgress.size;
                } else {
                    if (this.chartLegend.newScholars) visibleCount += data.newScholars.size;
                    if (this.chartLegend.oldScholars) visibleCount += data.oldScholars.size;
                }
                return visibleCount > 0;
            });
            const pendingData = labels.map(l => groupedData[l].pending.size);
            const approvedData = labels.map(l => groupedData[l].approved.size);
            const rejectedData = labels.map(l => groupedData[l].rejected.size);
            const inProgressData = labels.map(l => groupedData[l].inProgress.size);
            const newScholarsData = labels.map(l => groupedData[l].newScholars.size);
            const oldScholarsData = labels.map(l => groupedData[l].oldScholars.size);
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

            // Update Chart Status (Force Reactivity)
            // CRITICAL: Ensure we actually have data points > 0
            const hasData = labels.some(l => {
                let count = 0;
                if (groupedData[l]) {
                    count += groupedData[l].pending.size + groupedData[l].approved.size + groupedData[l].rejected.size + groupedData[l].inProgress.size + groupedData[l].newScholars.size + groupedData[l].oldScholars.size;
                }
                return count > 0;
            });

            this.chartStatus = { ...this.chartStatus, department: labels.length > 0 && hasData };
            this.chartStatus = { ...this.chartStatus, department: labels.length > 0 && hasData };

            // If no data, destroy chart and return
            if (labels.length === 0 || !hasData) {
                if (chartInstances.department) {
                    chartInstances.department.destroy();
                    chartInstances.department = null;
                }
                return;
            }

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
                    },
                    {
                        label: 'In Progress',
                        data: inProgressData,
                        backgroundColor: '#3B82F6',
                        hidden: !this.chartLegend.inProgress
                    }
                ];
            } else {
                datasets = [
                    {
                        label: 'Old Scholars',
                        data: oldScholarsData,
                        backgroundColor: '#10B981',
                        hidden: !this.chartLegend.oldScholars
                    },
                    {
                        label: 'New Scholars',
                        data: newScholarsData,
                        backgroundColor: '#3B82F6',
                        hidden: !this.chartLegend.newScholars
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
                            stacked: isComparisonMode // Stacked only for Campus Comparison
                        },
                        y: {
                            ticks: { color: this.getTextColor(), beginAtZero: true, precision: 0 },
                            stacked: isComparisonMode // Stacked only for Campus Comparison
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




        createTrendChart() {
            const ctx = document.getElementById('sfaoTrendChart');
            if (!ctx) return;
            if (chartInstances.trend) chartInstances.trend.destroy();

            // 1. Get Base Data (Respecting Campus/Department/Program/Time)
            // Note: We use the existing filteredData because it already respects Global Filters (Campus, Year, etc.)
            // But checking the previous implementation, filteredData might already account for local filters too?
            // Let's check updateFilteredData() logic. It usually applies ALL filters.
            // However, for this specific chart, we WANT to respect the "Scholarship Status" container's LOCAL scholarship filter.
            // AND we want to respect the Global Campus Filter (Overview vs Specific).

            // To be safe and precise, let's start from 'analyticsData.all_applications_data' and re-apply filters manually
            // This ensures we have full control over which "Scholarship" filter to use.

            let rawData = this.analyticsData.all_applications_data || [];


            // A. Global Campus Filter
            if (this.filters.campus !== 'all') {
                rawData = rawData.filter(a => a.campus_id == this.filters.campus);
            }

            // B. Global Academic Year (Time Period)
            if (this.filters.timePeriod !== 'all') {
                rawData = rawData.filter(a => {
                    if (!a.created_at) return false;
                    const date = new Date(a.created_at);
                    const year = date.getFullYear();
                    const month = date.getMonth();
                    const startYear = month >= 7 ? year : year - 1;
                    const ay = `${startYear}-${startYear + 1}`;
                    return ay === this.filters.timePeriod;
                });
            }

            // C. Local Scholarship Filter
            if (this.filters.scholarship && this.filters.scholarship !== 'all') {
                const selectedScholarship = (this.analyticsData.available_scholarships || []).find(s => String(s.id) === String(this.filters.scholarship));
                if (selectedScholarship) {
                    rawData = rawData.filter(a => a.scholarship_name === selectedScholarship.scholarship_name);
                }
            }



            // D. Department & Program (Global/Local context - typically ignored for Scholarship Status unless explicitly set?)
            // The requirement says "Overview ... total of all campus". It implies standard global filters apply.
            if (this.localFilters.department !== 'all') {
                rawData = rawData.filter(item => item.college === this.localFilters.department);
            }
            if (this.localFilters.program !== 'all') {
                rawData = rawData.filter(item => item.program === this.localFilters.program);
            }


            // Group by Time Unit (Month) given the filtered dataset
            const groupedData = {};
            const timeLabels = new Set();

            const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

            rawData.forEach(item => {
                if (!item.created_at) return;

                // View Mode Logic 
                if (this.viewMode === 'scholars' && (item.status !== 'approved' || !item.scholar_id)) return;

                const isScholarVal = Number(item.is_global_scholar);
                if (this.viewMode === 'applicants' && isScholarVal > 0) return;
                if (this.viewMode === 'applicants' && !['pending', 'approved', 'rejected', 'in_progress'].includes(item.status)) return;

                const date = new Date(item.created_at);
                const year = date.getFullYear();
                const month = date.getMonth();
                const key = `${year}-${String(month + 1).padStart(2, '0')}`; // YYYY-MM
                const label = `${monthNames[month]} ${year}`;

                timeLabels.add(JSON.stringify({ key, label }));

                if (!groupedData[key]) {
                    groupedData[key] = {
                        pending: 0,
                        approved: 0,
                        rejected: 0,
                        in_progress: 0,
                        new: 0,
                        old: 0
                    };
                }

                // Count by Status
                if (this.viewMode === 'applicants') {
                    if (item.status === 'pending') groupedData[key].pending++;
                    else if (item.status === 'approved') groupedData[key].approved++;
                    else if (item.status === 'rejected') groupedData[key].rejected++;
                    else if (item.status === 'in_progress') groupedData[key].in_progress++;
                } else {
                    // For scholars, we separate by New vs Old (based on scholar_type if available or just count total approved)
                    // Reverting to Status-based separation for Scholars might mean "New" vs "Old" lines.
                    // The user asked for "Approved, Rejected, Pending, In Progress" lines for application trend.
                    // If viewMode is 'scholars', these statuses don't apply similarly (all are Approved).
                    // So for Scholars, we keep "New" vs "Old".
                    if (item.status === 'approved' && item.scholar_id) {
                        if (item.scholar_type === 'new') groupedData[key].new++;
                        else groupedData[key].old++;
                    }
                }
            });

            // Sort Time Labels
            const sortedLabels = Array.from(timeLabels).map(l => JSON.parse(l)).sort((a, b) => a.key.localeCompare(b.key));
            const chartLabels = sortedLabels.map(l => l.label);
            const timeKeys = sortedLabels.map(l => l.key);

            // Update Chart Status
            this.chartStatus = { ...this.chartStatus, trend: timeKeys.length > 0 };

            if (timeKeys.length === 0) {
                if (chartInstances.trend) {
                    chartInstances.trend.destroy();
                    chartInstances.trend = null;
                }
                return;
            }

            // Define Datasets based on View Mode
            let datasets = [];

            if (this.viewMode === 'applicants') {
                datasets = [
                    {
                        label: 'Approved',
                        data: timeKeys.map(k => groupedData[k].approved),
                        borderColor: '#10B981', // Green
                        backgroundColor: '#10B981',
                        tension: 0.3,
                        hidden: !this.chartLegend.approved
                    },
                    {
                        label: 'Rejected',
                        data: timeKeys.map(k => groupedData[k].rejected),
                        borderColor: '#EF4444', // Red
                        backgroundColor: '#EF4444',
                        tension: 0.3,
                        hidden: !this.chartLegend.rejected
                    },
                    {
                        label: 'Pending',
                        data: timeKeys.map(k => groupedData[k].pending),
                        borderColor: '#F59E0B', // Orange
                        backgroundColor: '#F59E0B',
                        tension: 0.3,
                        hidden: !this.chartLegend.pending
                    },
                    {
                        label: 'In Progress',
                        data: timeKeys.map(k => groupedData[k].in_progress),
                        borderColor: '#3B82F6', // Blue
                        backgroundColor: '#3B82F6',
                        tension: 0.3,
                        hidden: !this.chartLegend.inProgress
                    }
                ];
            } else {
                // Scholars View
                datasets = [
                    {
                        label: 'Old Scholars',
                        data: timeKeys.map(k => groupedData[k].old),
                        borderColor: '#10B981',
                        backgroundColor: '#10B981',
                        tension: 0.3,
                        hidden: !this.chartLegend.oldScholars
                    },
                    {
                        label: 'New Scholars',
                        data: timeKeys.map(k => groupedData[k].new),
                        borderColor: '#3B82F6',
                        backgroundColor: '#3B82F6',
                        tension: 0.3,
                        hidden: !this.chartLegend.newScholars
                    }
                ];
            }

            chartInstances.trend = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                color: this.getTextColor()
                            }
                        },
                        x: {
                            ticks: {
                                color: this.getTextColor()
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false // We use the custom legend buttons above
                        },
                        tooltip: { mode: 'index', intersect: false }
                    }
                }
            });
        },

        updateTrendChart() {
            this.createTrendChart();
        },

        createComparisonChart() {
            const ctx = document.getElementById('sfaoComparisonChart');
            if (!ctx) return;
            if (chartInstances.comparison) chartInstances.comparison.destroy();

            // Source: All applications filtered by Campus/Dept/Time but NOT Scholarship
            let rawData = this.analyticsData.all_applications_data || [];

            // Apply Campus/Time filters manually.
            // INTENTIONALLY SKIPPING Department and Program filters to keep this separate.

            // 1. Campus
            if (this.filters.campus !== 'all') {
                rawData = rawData.filter(a => a.campus_id == this.filters.campus);
            }
            // 2. Time (Academic Year)
            if (this.filters.timePeriod !== 'all') {
                const now = new Date();
                rawData = rawData.filter(a => {
                    if (!a.created_at) return false;
                    const date = new Date(a.created_at);
                    const year = date.getFullYear();
                    const month = date.getMonth();
                    const startYear = month >= 7 ? year : year - 1;
                    const ay = `${startYear}-${startYear + 1}`;
                    return ay === this.filters.timePeriod;
                });
            }

            // 3. Dept
            if (this.localFilters.department !== 'all') {
                rawData = rawData.filter(item => item.college === this.localFilters.department);
            }
            // 4. Program
            if (this.localFilters.program !== 'all') {
                rawData = rawData.filter(item => item.program === this.localFilters.program);
            }

            // Group by Scholarship Name (Counting Unique Applicants per Status)
            const groupedData = {};
            rawData.forEach(item => {
                // View Mode Filter
                if (this.viewMode === 'scholars' && (item.status !== 'approved' || !item.scholar_id)) return;

                const isScholarVal = Number(item.is_global_scholar);
                if (this.viewMode === 'applicants' && isScholarVal > 0) return;
                // Graph only shows: Pending, Approved, Rejected, AND NOW In Progress.
                if (this.viewMode === 'applicants') {
                    if (!['pending', 'approved', 'rejected', 'in_progress'].includes(item.status)) return;
                }

                const name = item.scholarship_name || 'Unknown';
                if (!groupedData[name]) {
                    groupedData[name] = {
                        pending: new Set(),
                        approved: new Set(),
                        rejected: new Set(),
                        newScholars: new Set(),
                        oldScholars: new Set(),
                        inProgress: new Set()
                    };
                }

                if (this.viewMode === 'applicants') {
                    if (item.status === 'pending') groupedData[name].pending.add(item.user_id);
                    else if (item.status === 'approved') groupedData[name].approved.add(item.user_id);
                    else if (item.status === 'rejected') groupedData[name].rejected.add(item.user_id);
                    else if (item.status === 'in_progress') groupedData[name].inProgress.add(item.user_id);
                } else {
                    // Scholars Mode
                    if (item.status === 'approved' && item.scholar_id) {
                        if (item.scholar_type === 'new') groupedData[name].newScholars.add(item.user_id);
                        else groupedData[name].oldScholars.add(item.user_id);
                    }
                }
            });

            // Filter labels based on VISIBLE data (legend state)
            const allLabels = Object.keys(groupedData).sort();

            const labels = allLabels.filter(name => {
                const data = groupedData[name];
                let visibleCount = 0;

                if (this.viewMode === 'applicants') {
                    if (this.chartLegend.approved) visibleCount += data.approved.size;
                    if (this.chartLegend.pending) visibleCount += data.pending.size;
                    if (this.chartLegend.rejected) visibleCount += data.rejected.size;
                    if (this.chartLegend.inProgress) visibleCount += data.inProgress.size;
                } else {
                    if (this.chartLegend.newScholars) visibleCount += data.newScholars.size;
                    if (this.chartLegend.oldScholars) visibleCount += data.oldScholars.size;
                }
                return visibleCount > 0;
            });

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

            // Update Chart Status (Force Reactivity)
            this.chartStatus = { ...this.chartStatus, comparison: labels.length > 0 };
            this.chartStatus = { ...this.chartStatus, comparison: labels.length > 0 };

            // If no data, destroy chart and return
            if (labels.length === 0) {
                if (chartInstances.comparison) {
                    chartInstances.comparison.destroy();
                    chartInstances.comparison = null;
                }
                return;
            }

            // Build Datasets
            let datasets = [];
            if (this.viewMode === 'applicants') {
                const pendingData = labels.map(l => groupedData[l].pending.size);
                const approvedData = labels.map(l => groupedData[l].approved.size);
                const rejectedData = labels.map(l => groupedData[l].rejected.size);
                const inProgressData = labels.map(l => groupedData[l].inProgress.size);

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
                    },
                    {
                        label: 'In Progress',
                        data: inProgressData,
                        backgroundColor: '#3B82F6',
                        hidden: !this.chartLegend.inProgress
                    }
                ];
            } else {
                const newScholarsData = labels.map(l => groupedData[l].newScholars.size);
                const oldScholarsData = labels.map(l => groupedData[l].oldScholars.size);
                datasets = [
                    {
                        label: 'Old Scholars',
                        data: oldScholarsData,
                        backgroundColor: '#10B981',
                        hidden: !this.chartLegend.oldScholars
                    },
                    {
                        label: 'New Scholars',
                        data: newScholarsData,
                        backgroundColor: '#3B82F6',
                        hidden: !this.chartLegend.newScholars
                    }
                ];
            }


            chartInstances.comparison = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: processedLabels, // Scholarship Names (Split into lines)
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            ticks: {
                                color: this.getTextColor(),
                                autoSkip: false,
                                maxRotation: 0,
                                minRotation: 0
                            },
                            stacked: true
                        },
                        y: {
                            ticks: { color: this.getTextColor(), beginAtZero: true, precision: 0 },
                            stacked: true
                        }
                    },
                    plugins: {
                        legend: { display: false }, // Use custom legend
                        tooltip: {
                            enabled: true,
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

        updateComparisonChart() {
            this.createComparisonChart();
        },

        updateDepartmentChart() {
            this.createDepartmentChart();
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
        tab: 'analytics',
        statsCampus: localStorage.getItem('sfaoStatsCampus') || config.defaultStatsCampus,
        campusList: config.campusList || [],
        openDropdowns: { dashboard: false, scholarships: false, applicants: false, scholars: false, reports: false },

        urlMapping: {
            'overview': 'analytics',
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
            'reports_applicant_summary': 'reports-applicant_summary',
            'reports_scholar_summary': 'reports-scholar_summary',
            'reports_grant_summary': 'reports-grant_summary',
            'account_settings': 'account'
        },

        init() {
            // Watch Stats Campus Change
            this.$watch('statsCampus', val => {
                localStorage.setItem('sfaoStatsCampus', val);
                // Ensure filtered data updates in the child component if needed, 
                // but checking tab state to update URL explicitly is key.
                this.updateUrl(this.tab);
            });

            // Restore Dropdowns
            const savedDropdowns = localStorage.getItem(`sfaoDropdowns_${config.userId}`);
            if (savedDropdowns) {
                try {
                    this.openDropdowns = JSON.parse(savedDropdowns);
                } catch (e) { console.error('Error parsing dropdown state', e); }
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

            // Listen for Sidebar Stats Filter Events
            window.addEventListener('set-stats-filter', event => {
                this.statsCampus = event.detail;
            });

            // Initialize from URL or LocalStorage
            const urlParams = new URLSearchParams(window.location.search);
            const urlTab = urlParams.get('tabs');

            // Check if URL tab matches a campus slug
            let matchedCampus = null;
            if (this.campusList && this.campusList.length > 0) {
                matchedCampus = this.campusList.find(c => c.slug === urlTab);
            }

            if (urlTab && this.urlMapping[urlTab]) {
                this.tab = this.urlMapping[urlTab];
            } else if (matchedCampus) {
                this.tab = 'analytics';
                this.statsCampus = matchedCampus.id;
            } else {
                // Fallback favoring Analytics ("Overview") if no specific tab
                let savedTab = localStorage.getItem('sfaoTab');
                // Ensure valid tab
                this.tab = savedTab || config.activeTab || 'analytics';

                // Cleanup legacy values
                if (this.tab === 'statistics' || this.tab === 'dashboard') this.tab = 'analytics';
            }

            // Ensure correct dropdown is open
            if (!savedDropdowns) {
                this.syncDropdowns(this.tab);
            }

            this.updateUrl(this.tab);
        },

        updateUrl(currentTab) {
            let key = null;

            if (currentTab === 'analytics') {
                if (this.statsCampus === 'all' || !this.statsCampus) {
                    key = 'overview';
                } else {
                    const campus = this.campusList.find(c => c.id == this.statsCampus);
                    key = campus ? campus.slug : 'overview';
                }
            } else {
                key = Object.keys(this.urlMapping).find(k => this.urlMapping[k] === currentTab);
            }

            if (key) {
                const url = new URL(window.location);
                url.searchParams.set('tabs', key);
                url.searchParams.delete('tab');
                window.history.pushState({}, '', url);
            }
        },

        syncDropdowns(currentTab) {
            if (currentTab === 'analytics') this.openDropdowns.dashboard = true;
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
            scholarship: localStorage.getItem('sfaoScholarsScholarship') || 'all',
            type: localStorage.getItem('sfaoScholarsType') || 'all'
        },
        counts: config.counts || {},
        campusOptions: config.campusOptions || [],
        sfaoCampusName: config.sfaoCampusName || '',
        extensionCampuses: config.extensionCampuses || [],
        selectedScholars: [],
        selectAll: false,
        showMarkAsModal: false,
        selectedScholarId: null,
        selectedScholarName: '',

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
            this.$watch('filters.scholarship', (value) => {
                localStorage.setItem('sfaoScholarsScholarship', value);
                this.fetchScholars();
            });
            this.$watch('filters.type', (value) => {
                localStorage.setItem('sfaoScholarsType', value);
                this.fetchScholars();
            });

            if (this.filters.scholarship !== 'all' || this.filters.campus !== 'all' || this.filters.type !== 'all') {
                this.fetchScholars();
            }
        },

        fetchScholars() {
            const params = new URLSearchParams({
                tab: 'scholars',
                scholars_sort_by: this.filters.sort_by,
                scholars_sort_order: this.filters.sort_order,
                campus_filter: this.filters.campus,
                scholarship_filter: this.filters.scholarship,
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
            this.filters.scholarship = 'all';
            this.filters.type = 'all';
        },

        toggleSelectAll() {
            if (this.selectAll) {
                // Get all eligible scholar IDs from the page
                const checkboxes = document.querySelectorAll('input[type="checkbox"][\\@change*="toggleScholar"]');
                this.selectedScholars = Array.from(checkboxes).map(cb => {
                    const match = cb.getAttribute('@change').match(/toggleScholar\((\d+)\)/);
                    return match ? parseInt(match[1]) : null;
                }).filter(id => id !== null);
            } else {
                this.selectedScholars = [];
            }
        },

        toggleScholar(scholarId) {
            const index = this.selectedScholars.indexOf(scholarId);
            if (index > -1) {
                this.selectedScholars.splice(index, 1);
            } else {
                this.selectedScholars.push(scholarId);
            }
            // Update selectAll state
            const totalCheckboxes = document.querySelectorAll('input[type="checkbox"][\\@change*="toggleScholar"]').length;
            this.selectAll = this.selectedScholars.length === totalCheckboxes && totalCheckboxes > 0;
        },

        isScholarSelected(scholarId) {
            return this.selectedScholars.includes(scholarId);
        },

        getSelectedCount() {
            return this.selectedScholars.length;
        },

        async bulkMarkClaimed() {
            if (this.selectedScholars.length === 0) {
                alert('Please select at least one scholar.');
                return;
            }

            if (!confirm(`Are you sure you want to mark ${this.selectedScholars.length} scholar(s) as claimed? This will update their grant history.`)) {
                return;
            }

            try {
                const response = await fetch(config.routeUrl.replace('/sfao', '/sfao/scholars/bulk-mark-claimed'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        scholar_ids: this.selectedScholars
                    })
                });

                const data = await response.json();

                if (data.success) {
                    alert(`Successfully marked ${data.success_count} scholar(s) as claimed.${data.skipped_count > 0 ? ` ${data.skipped_count} scholar(s) were skipped (already claimed).` : ''}`);
                    this.selectedScholars = [];
                    this.selectAll = false;
                    this.fetchScholars();
                } else {
                    alert(data.message || 'An error occurred while processing the request.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while marking scholars as claimed.');
            }
        },

        openMarkAsModal(scholarId, scholarName) {
            this.selectedScholarId = scholarId;
            this.selectedScholarName = scholarName;
            this.showMarkAsModal = true;
            document.body.style.overflow = 'hidden';
        },

        async markScholarAs(action) {
            if (!this.selectedScholarId) {
                alert('No scholar selected.');
                return;
            }

            const actionText = action === 'claimed' ? 'claimed' : 'disqualified';
            if (!confirm(`Are you sure you want to mark this scholar as ${actionText}?`)) {
                return;
            }

            try {
                const url = action === 'claimed'
                    ? config.routeUrl.replace('/sfao', `/sfao/scholars/${this.selectedScholarId}/mark-claimed`)
                    : config.routeUrl.replace('/sfao', `/sfao/scholars/${this.selectedScholarId}/mark-disqualified`);

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (data.success || response.ok) {
                    alert(data.message || `Scholar marked as ${actionText} successfully.`);
                    this.showMarkAsModal = false;
                    document.body.style.overflow = '';
                    this.fetchScholars();
                } else {
                    alert(data.message || `Failed to mark scholar as ${actionText}.`);
                }
            } catch (error) {
                console.error('Error:', error);
                alert(`An error occurred while marking scholar as ${actionText}.`);
            }
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

