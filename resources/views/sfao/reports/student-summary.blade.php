@extends('layouts.focused')

@section('title', 'Student Summary Report')
@section('navbar-title', 'Student Summary Report')
@section('back-url', route('sfao.dashboard', ['tabs' => 'reports_student_summary']))
@section('back-text', 'Back to Reports')
@section('content-width', 'max-w-[95%] 2xl:max-w-full')

@section('content')
<div class="w-full" x-data="{ 
    studentType: '{{ $studentType }}',
    department: 'all',
    program: 'all',
    academicYear: 'all',
    scholarshipId: 'all',
    campusId: '{{ request('campus_id', 'all') }}',
    programs: {{ json_encode($programs) }},
    availablePrograms: [],
    
    init() {
        this.updateAvailablePrograms();
    },

    updateAvailablePrograms() {
        if (this.department === 'all') {
            // Flatten all programs
            this.availablePrograms = Object.values(this.programs).flat().sort();
            // Remove duplicates if any (though backend distinct handles it mostly, strict unique here is good)
            this.availablePrograms = [...new Set(this.availablePrograms)];
        } else {
            this.availablePrograms = this.programs[this.department] || [];
        }
        
        // Reset program if not in list
        if (this.program !== 'all' && !this.availablePrograms.includes(this.program)) {
            this.program = 'all';
        }
    },

    updateReport() {
        const container = document.getElementById('report-content-container');
        container.style.opacity = '0.5';
        
        const url = new URL('{{ route('sfao.reports.student-summary') }}');
        url.searchParams.set('student_type', this.studentType);
        url.searchParams.set('department', this.department);
        url.searchParams.set('program', this.program);
        url.searchParams.set('academic_year', this.academicYear);
        url.searchParams.set('scholarship_id', this.scholarshipId);
        url.searchParams.set('campus_id', this.campusId);

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.text())
        .then(html => {
            container.innerHTML = html;
            container.style.opacity = '1';
        });
    },
    
    printReport() {
        window.print();
    },
    exportToExcel() {
        const params = new URLSearchParams({
            student_type: this.studentType,
            department: this.department,
            program: this.program,
            academic_year: this.academicYear,
            scholarship_id: this.scholarshipId,
            campus_id: '{{ request()->get('campus_id', 'all') }}',
            export: 'excel'
        });
        window.location.href = `{{ route('sfao.reports.student-summary') }}?${params.toString()}`;
    }
}">

    <!-- Actions / Filter Bar (No-Print) -->
    <div class="mb-8 print:hidden">
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide">Report Filters</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                
                <!-- Student Type -->
                <div class="flex-1 min-w-[200px]">
                     <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider text-center">Student Type</label>
                     <div class="relative">
                        <select x-model="studentType" @change="updateReport()" class="block w-full px-3 py-2 text-base border-red-500 focus:outline-none focus:ring-bsu-red focus:border-bsu-red sm:text-sm rounded-full text-center appearance-none" style="border-width: 1px;">
                            <option value="applicants">Applicants</option>
                            <option value="scholars">Scholars</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                             <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Department -->
                <div class="flex-1 min-w-[200px]">
                     <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider text-center">Department</label>
                     <div class="relative">
                        <select x-model="department" @change="updateAvailablePrograms(); updateReport()" class="block w-full px-3 py-2 text-base border-red-500 focus:outline-none focus:ring-bsu-red focus:border-bsu-red sm:text-sm rounded-full text-center appearance-none" style="border-width: 1px;">
                            <option value="all">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->short_name }}">{{ $dept->short_name }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                             <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Program -->
                <div class="flex-1 min-w-[200px]">
                     <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider text-center">Program</label>
                     <div class="relative">
                        <select x-model="program" @change="updateReport()" class="block w-full px-3 py-2 text-base border-red-500 focus:outline-none focus:ring-bsu-red focus:border-bsu-red sm:text-sm rounded-full text-center appearance-none" style="border-width: 1px;">
                            <option value="all">All Programs</option>
                            <template x-for="prog in availablePrograms" :key="prog">
                                <option :value="prog" x-text="prog"></option>
                            </template>
                        </select>
                         <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                             <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
                
                 <!-- Academic Year -->
                <div class="flex-1 min-w-[200px]">
                     <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider text-center">Academic Year</label>
                     <div class="relative">
                        <select x-model="academicYear" @change="updateReport()" class="block w-full px-3 py-2 text-base border-red-500 focus:outline-none focus:ring-bsu-red focus:border-bsu-red sm:text-sm rounded-full text-center appearance-none" style="border-width: 1px;">
                            <option value="all">All Academic Years</option>
                            @foreach($academicYearOptions as $ay)
                                <option value="{{ $ay }}">{{ $ay }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                             <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Scholarship -->
                <div class="flex-1 min-w-[200px]">
                     <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider text-center">Scholarship</label>
                     <div class="relative">
                        <select x-model="scholarshipId" @change="updateReport()" class="block w-full px-3 py-2 text-base border-red-500 focus:outline-none focus:ring-bsu-red focus:border-bsu-red sm:text-sm rounded-full text-center appearance-none" style="border-width: 1px;">
                            <option value="all">All Scholarships</option>
                            @foreach($scholarships as $scholarship)
                                <option value="{{ $scholarship->id }}">{{ $scholarship->scholarship_name }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                             <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Button (Centered) -->
            <div class="flex justify-center mt-6 border-t border-gray-100 pt-4">
                 <button @click="exportToExcel()" class="inline-flex justify-center items-center px-8 py-2.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all transform hover:-translate-y-0.5">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export to Excel
                </button>
            </div>
        </div>
    </div>

    <!-- Report Paper View -->
    <div class="bg-white text-black p-8 shadow-lg print:shadow-none print:p-0 w-full mx-auto min-h-[210mm] overflow-hidden mb-8">
        
        <!-- Report Header -->
        <div class="text-center mb-8">
            <div class="flex items-center justify-center mb-4">
                 <img src="{{ asset('images/lugo.png') }}" alt="BSU Logo" class="h-24 w-24 object-contain">
            </div>
            <div class="mb-2">
                <h2 class="text-xl font-bold uppercase tracking-wider">Batangas State University</h2>
                <h3 class="text-lg font-semibold text-bsu-red uppercase">The National Engineering University</h3>
            </div>
            <div class="mt-6 mb-4">
                <h1 class="text-2xl font-bold uppercase underline decoration-2 underline-offset-4">Student Summary Report</h1>
            </div>
            
            <div class="text-sm space-y-1">
                <p><span class="font-semibold">Campus:</span> {{ $monitoredCampuses->count() > 1 && request('campus_id', 'all') == 'all' ? 'All Campuses' : $monitoredCampuses->first()->name }}</p>
                <p><span class="font-semibold">Generated on:</span> {{ now()->format('F d, Y') }}</p>
                <p><span class="font-semibold">Prepared by:</span> {{ $user->name }}</p>
            </div>
        </div>

        <!-- Report Content -->
        <div id="report-content-container" class="space-y-8 min-h-[200px]">
            @include('sfao.reports.partials.student-summary-table', ['reportData' => $reportData, 'studentType' => $studentType])
        </div>

        <!-- Footer Signatures (Placeholder for print) -->
        <div class="mt-16 break-inside-avoid hidden print:flex justify-between px-8">
            <div class="text-center">
                <div class="border-b border-black w-48 mb-2"></div>
                <p class="text-xs font-bold uppercase">Prepared By</p>
                <p class="text-xs">{{ $user->name }}</p>
            </div>
            <div class="text-center">
                <div class="border-b border-black w-48 mb-2"></div>
                <p class="text-xs font-bold uppercase">Certified Correct</p>
            </div>
            <div class="text-center">
                <div class="border-b border-black w-48 mb-2"></div>
                <p class="text-xs font-bold uppercase">Approved By</p>
            </div>
        </div>
    </div>
    
    <!-- Submission Form (No-Print) -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 border-t-4 border-t-bsu-red print:hidden w-full mx-auto overflow-hidden">
        <div class="p-6">
             <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
                <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-red-50">
                    <svg class="h-5 w-5 text-bsu-red" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                     <h3 class="text-lg font-bold text-gray-900">Submit Report to Central Office</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Submits the currently filtered report.</p>
                </div>
            </div>

            <form action="{{ route('sfao.reports.summary-submit') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="report_type" value="student_summary">
                <input type="hidden" name="campus_id" value="{{ request('campus_id', 'all') }}">
                
                <!-- Use alpine values to populate hidden fields before submit? -->
                <!-- We can't rely on Alpine binding to hidden input for standard form submit if outside x-data scope, but here inside. 
                     Wait, x-model on hidden input doesn't work well for standard submit unless value attr matches. 
                     Uses :value binding. 
                -->
                <input type="hidden" name="student_type" :value="studentType">
                <input type="hidden" name="department" :value="department">
                <input type="hidden" name="program" :value="program">
                <input type="hidden" name="academic_year" :value="academicYear">
                <input type="hidden" name="scholarship_id" :value="scholarshipId">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                         <label for="frequency" class="block text-sm font-bold text-gray-700 mb-2">Report Frequency</label>
                        <select name="frequency" id="frequency" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-bsu-red focus:border-bsu-red block w-full p-3 shadow-sm transition-colors hover:bg-white">
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="semi-annual">Semi-Annual</option>
                            <option value="annual">Annual</option>
                        </select>
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Additional Notes</label>
                        <textarea name="description" id="description" rows="1" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-bsu-red focus:border-bsu-red block w-full p-3 shadow-sm transition-colors hover:bg-white" placeholder="Optional remarks..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-transparent shadow-md px-8 py-2.5 bg-bsu-red text-sm font-semibold text-white hover:bg-bsu-redDark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bsu-red transition-all transform hover:-translate-y-0.5">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Submit Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @media print {
        @page {
            size: landscape;
            margin: 0.5cm;
        }
        body {
            background: white;
            color: black;
            -webkit-print-color-adjust: exact;
            width: 100%;
        }
        .print\:hidden {
            display: none !important;
        }
        .print\:flex {
            display: flex !important;
        }
        .print\:shadow-none {
            box-shadow: none !important;
        }
        .print\:p-0 {
            padding: 0 !important;
        }
        table {
            width: 100%;
        }
        #report-content-container {
             zoom: 85%;
        }
    }
</style>
@endsection
