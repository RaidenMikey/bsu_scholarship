<div x-show="tab === 'scholars' || tab === 'scholars-new' || tab === 'scholars-old' || tab === 'endorsed-applicants'" x-cloak x-data="{ 
    showModal: false, 
    showFormModal: false, 
    selectedScholar: null,
    showValidationModal: false,
    selectedApplication: null,
    validationDecision: '',
    validationRemarks: '',
    validateApplicant(applicationId) {
        // Find the application data
        const application = this.getApplicationData(applicationId);
        if (application) {
            this.selectedApplication = application;
            this.showValidationModal = true;
        }
    },
    getApplicationData(applicationId) {
        // This would typically fetch data via AJAX
        // For now, we'll return mock data structure
        return {
            id: applicationId,
            student: {
                name: 'John Doe',
                email: 'john.doe@example.com',
                profile_pic: '/images/default-avatar.png',
                campus: 'Main Campus',
                program: 'Bachelor of Science in Computer Science',
                year_level: '3rd Year',
                gwa: 1.25,
                student_id: '2021-12345'
            },
            scholarship: {
                name: 'Academic Excellence Scholarship',
                grant_amount: 25000,
                scholarship_type: 'Internal'
            },
            matching_conditions: [
                { name: 'GWA Requirement', required: '1.50 or better', student_value: '1.25', matches: true },
                { name: 'Year Level', required: '2nd Year or higher', student_value: '3rd Year', matches: true },
                { name: 'Program', required: 'STEM Programs', student_value: 'Computer Science', matches: true },
                { name: 'Campus', required: 'Main Campus only', student_value: 'Main Campus', matches: true }
            ],
            documents: [
                { name: 'Form 137', status: 'approved', uploaded_at: '2024-01-15' },
                { name: 'Grades', status: 'approved', uploaded_at: '2024-01-15' },
                { name: 'Application Form', status: 'approved', uploaded_at: '2024-01-15' },
                { name: 'Recommendation Letter', status: 'approved', uploaded_at: '2024-01-16' }
            ],
            sfao_remarks: 'Student shows excellent academic performance and meets all requirements. Strong recommendation for selection.',
            application_date: '2024-01-15',
            sfao_approved_date: '2024-01-20'
        };
    },
    confirmValidation() {
        if (this.validationDecision && confirm('Are you sure you want to ' + this.validationDecision + ' this applicant?')) {
            // Here you would make an AJAX call to validate the applicant
            alert('Applicant validation functionality will be implemented here.');
            this.showValidationModal = false;
        } else if (!this.validationDecision) {
            alert('Please select a validation decision.');
        }
    },
    closeValidationModal() {
        this.showValidationModal = false;
        this.selectedApplication = null;
        this.validationDecision = '';
        this.validationRemarks = '';
    }
}">
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold text-bsu-red dark:text-red-400 mb-6" x-text="tab === 'scholars-new' ? 'New Scholars' : tab === 'scholars-old' ? 'Old Scholars' : tab === 'endorsed-applicants' ? 'Endorsed Applicants' : 'Scholars'"></h1>
        <p class="text-gray-600 dark:text-gray-300 mb-6" x-text="tab === 'scholars-new' ? 'Students who have been accepted for scholarships but haven\'t received any grants yet.' : tab === 'scholars-old' ? 'Students who have been accepted for scholarships and have already received grants.' : tab === 'endorsed-applicants' ? 'Applicants approved by SFAO and ready for scholar selection.' : 'Students who have been accepted for scholarships and their grant information.'"></p>

        <!-- Filtering and Sorting Controls -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
            <form method="GET" action="{{ route('central.dashboard') }}" class="space-y-4">
                <!-- Filter Row -->
                <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center">
                    <div class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Status:</label>
                        <select name="status_filter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-bsu-red focus:border-bsu-red">
                            <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>All Status</option>
                            <option value="active" {{ $statusFilter == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $statusFilter == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ $statusFilter == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="completed" {{ $statusFilter == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>

                    <div class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Campus:</label>
                        <select name="campus_filter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-bsu-red focus:border-bsu-red">
                            <option value="all" {{ $campusFilter == 'all' ? 'selected' : '' }}>All Campuses</option>
                            @foreach($campusOptions as $campus)
                                <option value="{{ $campus['id'] }}" {{ $campusFilter == $campus['id'] ? 'selected' : '' }}>
                                    {{ $campus['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Scholarship:</label>
                        <select name="scholarship_filter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-bsu-red focus:border-bsu-red">
                            <option value="all" {{ $scholarshipFilter == 'all' ? 'selected' : '' }}>All Scholarships</option>
                            @foreach($scholarshipOptions as $scholarship)
                                <option value="{{ $scholarship['id'] }}" {{ $scholarshipFilter == $scholarship['id'] ? 'selected' : '' }}>
                                    {{ $scholarship['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Scholar Type:</label>
                        <select name="applicant_type_filter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-bsu-red focus:border-bsu-red">
                            <option value="all" {{ $applicantTypeFilter == 'all' ? 'selected' : '' }}>All Types</option>
                            <option value="new" {{ $applicantTypeFilter == 'new' ? 'selected' : '' }}>New Scholars</option>
                            <option value="old" {{ $applicantTypeFilter == 'old' ? 'selected' : '' }}>Old Scholars</option>
                        </select>
                    </div>
                </div>

                <!-- Sort Row -->
                <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                    <div class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Sort by:</label>
                        <select name="sort_by" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-bsu-red focus:border-bsu-red">
                            <option value="created_at" {{ $sortBy === 'created_at' ? 'selected' : '' }}>Created Date</option>
                            <option value="name" {{ $sortBy === 'name' ? 'selected' : '' }}>Name</option>
                            <option value="email" {{ $sortBy === 'email' ? 'selected' : '' }}>Email</option>
                            <option value="scholarship" {{ $sortBy === 'scholarship' ? 'selected' : '' }}>Scholarship</option>
                            <option value="status" {{ $sortBy === 'status' ? 'selected' : '' }}>Status</option>
                            <option value="type" {{ $sortBy === 'type' ? 'selected' : '' }}>Scholar Type</option>
                        </select>
                    </div>

                    <div class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Order:</label>
                        <select name="sort_order" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-bsu-red focus:border-bsu-red">
                            <option value="asc" {{ $sortOrder === 'asc' ? 'selected' : '' }}>Ascending</option>
                            <option value="desc" {{ $sortOrder === 'desc' ? 'selected' : '' }}>Descending</option>
                        </select>
                    </div>

                    <div class="flex items-center space-x-2">
                        <button type="submit" class="bg-bsu-red text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700 transition-colors">
                            Apply Filters
                        </button>
                        <a href="{{ route('central.dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-600 transition-colors">
                            Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400" x-text="tab === 'scholars-new' ? 'New Scholars' : tab === 'scholars-old' ? 'Old Scholars' : tab === 'endorsed-applicants' ? 'Endorsed Applicants' : 'Total Scholars'"></p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white" x-text="tab === 'scholars-new' ? '{{ $scholars->where('type', 'new')->count() }}' : tab === 'scholars-old' ? '{{ $scholars->where('type', 'old')->count() }}' : tab === 'endorsed-applicants' ? '{{ $endorsedApplicants->count() }}' : '{{ $scholars->count() }}'"></p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400" x-text="tab === 'endorsed-applicants' ? 'Ready for Selection' : 'Active Scholars'"></p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white" x-text="tab === 'scholars-new' ? '{{ $scholars->where('type', 'new')->where('status', 'active')->count() }}' : tab === 'scholars-old' ? '{{ $scholars->where('type', 'old')->where('status', 'active')->count() }}' : tab === 'endorsed-applicants' ? '{{ $endorsedApplicants->where('status', 'approved')->count() }}' : '{{ $scholars->where('status', 'active')->count() }}'"></p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4" x-show="tab === 'scholars' || tab === 'scholars-new'">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">New Scholars</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $scholars->where('type', 'new')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4" x-show="tab === 'scholars' || tab === 'scholars-old'">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Old Scholars</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $scholars->where('type', 'old')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="tab === 'scholars' && {{ $scholars->isEmpty() ? 'true' : 'false' }}" class="text-center py-12">
            <div class="text-gray-400 dark:text-gray-500 text-6xl mb-4">🎓</div>
            <h3 class="text-xl font-medium text-gray-900 dark:text-gray-100 mb-2">No Scholars Found</h3>
            <p class="text-gray-600 dark:text-gray-400">There are currently no scholars in the system.</p>
        </div>
        
        <div x-show="tab === 'scholars-new' && {{ $scholars->where('type', 'new')->isEmpty() ? 'true' : 'false' }}" class="text-center py-12">
            <div class="text-gray-400 dark:text-gray-500 text-6xl mb-4">🆕</div>
            <h3 class="text-xl font-medium text-gray-900 dark:text-gray-100 mb-2">No New Scholars Found</h3>
            <p class="text-gray-600 dark:text-gray-400">There are currently no new scholars in the system.</p>
        </div>
        
        <div x-show="tab === 'scholars-old' && {{ $scholars->where('type', 'old')->isEmpty() ? 'true' : 'false' }}" class="text-center py-12">
            <div class="text-gray-400 dark:text-gray-500 text-6xl mb-4">👴</div>
            <h3 class="text-xl font-medium text-gray-900 dark:text-gray-100 mb-2">No Old Scholars Found</h3>
            <p class="text-gray-600 dark:text-gray-400">There are currently no old scholars in the system.</p>
        </div>
        
        <div x-show="(tab === 'scholars' && !{{ $scholars->isEmpty() ? 'true' : 'false' }}) || (tab === 'scholars-new' && !{{ $scholars->where('type', 'new')->isEmpty() ? 'true' : 'false' }}) || (tab === 'scholars-old' && !{{ $scholars->where('type', 'old')->isEmpty() ? 'true' : 'false' }})" class="overflow-x-auto">
                <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 shadow-lg rounded-lg">
                    <thead class="bg-bsu-red text-white">
                        <tr>
                            <th class="px-4 py-3 text-left">#</th>
                            <th class="px-4 py-3 text-left">Name</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-left">Scholarship</th>
                            <th class="px-4 py-3 text-left">Scholar Type</th>
                            <th class="px-4 py-3 text-left">Grant Count</th>
                            <th class="px-4 py-3 text-left">Total Received</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Started</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($scholars as $index => $scholar)
                        <tr 
                            class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 transition cursor-pointer"
                            @click="selectedScholar = {{ $scholar->toJson() }}; showModal = true"
                            x-show="tab === 'scholars' || 
                                     (tab === 'scholars-new' && '{{ $scholar->type }}' === 'new') ||
                                     (tab === 'scholars-old' && '{{ $scholar->type }}' === 'old')"
                        >
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $scholar->user->name ?? '-' }}</td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $scholar->user->email ?? '-' }}</td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $scholar->scholarship->scholarship_name ?? '-' }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded {{ $scholar->type === 'new' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                                    {{ $scholar->type === 'new' ? 'New Scholar' : 'Old Scholar' }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded {{ $scholar->grant_count > 0 ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' }}">
                                    {{ $scholar->grant_count > 0 ? $scholar->grant_count . ' grants' : 'No grants' }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">₱{{ number_format($scholar->total_grant_received, 2) }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $scholar->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                       ($scholar->status === 'inactive' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' : 
                                       ($scholar->status === 'suspended' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' :
                                       'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200')) }}">
                                    {{ ucfirst($scholar->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $scholar->scholarship_start_date?->format('M d, Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        <!-- Endorsed Applicants Table -->
        <div x-show="tab === 'endorsed-applicants'" class="overflow-x-auto">
            @if($endorsedApplicants->isEmpty())
                <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-medium text-gray-900 dark:text-gray-100 mb-2">No Endorsed Applicants Found</h3>
                    <p class="text-gray-600 dark:text-gray-400">There are currently no applicants endorsed by SFAO for scholar selection.</p>
                </div>
            @else
                <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 shadow-lg rounded-lg">
                    <thead class="bg-bsu-red text-white">
                        <tr>
                            <th class="px-4 py-3 text-left">#</th>
                            <th class="px-4 py-3 text-left">Name</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-left">Scholarship</th>
                            <th class="px-4 py-3 text-left">Campus</th>
                            <th class="px-4 py-3 text-left">Application Date</th>
                            <th class="px-4 py-3 text-left">SFAO Approval</th>
                            <th class="px-4 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($endorsedApplicants as $index => $application)
                        <tr class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100 font-medium">
                                {{ $application->user->name ?? 'Unknown' }}
                            </td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">
                                {{ $application->user->email ?? 'No email' }}
                            </td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $application->scholarship->scholarship_name ?? 'Unknown Scholarship' }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">
                                {{ $application->user->campus->name ?? 'Unknown Campus' }}
                            </td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">
                                {{ $application->created_at?->format('M d, Y') ?? 'Unknown' }}
                            </td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Approved
                                </span>
                            </td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">
                                <button @click="validateApplicant({{ $application->id }})" 
                                        class="inline-flex items-center px-4 py-2 bg-bsu-red hover:bg-red-700 text-white text-sm font-medium rounded-lg transition duration-200 shadow-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Validate Applicant
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- Applicant Validation Modal -->
    <div x-show="showValidationModal" 
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
         @click.self="closeValidationModal()">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="bg-bsu-red text-white px-6 py-4 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold">Validate Applicant</h2>
                    <button @click="closeValidationModal()" 
                            class="text-white hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Content -->
            <div class="p-6" x-show="selectedApplication">
                <!-- Student Profile Section -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-bsu-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Student Profile
                    </h3>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                        <div class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-6">
                            <!-- Profile Picture -->
                            <div class="flex-shrink-0">
                                <img x-bind:src="selectedApplication?.student?.profile_pic || '/images/default-avatar.png'" 
                                     x-bind:alt="selectedApplication?.student?.name"
                                     class="w-24 h-24 rounded-full object-cover border-4 border-bsu-red">
                            </div>
                            
                            <!-- Student Information -->
                            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="selectedApplication?.student?.name"></h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400" x-text="selectedApplication?.student?.email"></p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400" x-text="'Student ID: ' + selectedApplication?.student?.student_id"></p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-sm"><span class="font-medium text-gray-700 dark:text-gray-300">Campus:</span> <span x-text="selectedApplication?.student?.campus"></span></p>
                                    <p class="text-sm"><span class="font-medium text-gray-700 dark:text-gray-300">Program:</span> <span x-text="selectedApplication?.student?.program"></span></p>
                                    <p class="text-sm"><span class="font-medium text-gray-700 dark:text-gray-300">Year Level:</span> <span x-text="selectedApplication?.student?.year_level"></span></p>
                                    <p class="text-sm"><span class="font-medium text-gray-700 dark:text-gray-300">GWA:</span> <span class="font-semibold text-bsu-red" x-text="selectedApplication?.student?.gwa"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Scholarship Information -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-bsu-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        Scholarship Information
                    </h3>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="selectedApplication?.scholarship?.name"></h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400" x-text="selectedApplication?.scholarship?.scholarship_type"></p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-green-600" x-text="'₱' + selectedApplication?.scholarship?.grant_amount?.toLocaleString()"></p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Grant Amount</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Application Date</p>
                                <p class="font-medium" x-text="selectedApplication?.application_date"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Matching Conditions -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-bsu-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Eligibility Matching
                    </h3>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <template x-for="condition in selectedApplication?.matching_conditions" :key="condition.name">
                                <div class="flex items-center justify-between p-3 rounded-lg" 
                                     :class="condition.matches ? 'bg-green-100 dark:bg-green-900/20' : 'bg-red-100 dark:bg-red-900/20'">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900 dark:text-white" x-text="condition.name"></p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400" x-text="'Required: ' + condition.required"></p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400" x-text="'Student: ' + condition.student_value"></p>
                                    </div>
                                    <div class="ml-4">
                                        <template x-if="condition.matches">
                                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </template>
                                        <template x-if="!condition.matches">
                                            <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Submitted Documents -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-bsu-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Submitted Documents
                    </h3>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <template x-for="document in selectedApplication?.documents" :key="document.name">
                                <div class="flex items-center justify-between p-3 rounded-lg bg-white dark:bg-gray-600">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <template x-if="document.status === 'approved'">
                                                <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </template>
                                            <template x-if="document.status === 'pending'">
                                                <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </template>
                                            <template x-if="document.status === 'rejected'">
                                                <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </template>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white" x-text="document.name"></p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400" x-text="'Uploaded: ' + document.uploaded_at"></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full"
                                              :class="document.status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                                      document.status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                                                      'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'"
                                              x-text="document.status"></span>
                                        <button class="text-bsu-red hover:text-red-700 text-sm font-medium">
                                            View
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- SFAO Remarks -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-bsu-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                        SFAO Remarks
                    </h3>
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 border-l-4 border-blue-400">
                        <p class="text-gray-800 dark:text-gray-200" x-text="selectedApplication?.sfao_remarks"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                            SFAO Approved on: <span x-text="selectedApplication?.sfao_approved_date"></span>
                        </p>
                    </div>
                </div>

                <!-- Central Validation Decision -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-bsu-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Central Validation Decision
                    </h3>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Decision</label>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="radio" x-model="validationDecision" value="approve" 
                                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Approve as Scholar</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="validationDecision" value="reject" 
                                               class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Reject Application</span>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Central Remarks</label>
                                <textarea x-model="validationRemarks" 
                                          rows="4" 
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-bsu-red focus:border-bsu-red dark:bg-gray-600 dark:text-white"
                                          placeholder="Enter your remarks about this applicant..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4">
                    <button @click="closeValidationModal()" 
                            class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition duration-200">
                        Cancel
                    </button>
                    <button @click="confirmValidation()" 
                            class="px-6 py-2 bg-bsu-red hover:bg-red-700 text-white rounded-lg transition duration-200">
                        Confirm Decision
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scholar Details Modal -->
    <div 
        x-show="showModal" 
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        x-transition 
        x-cloak
    >
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-2xl relative">
            <button @click="showModal = false" class="absolute top-2 right-2 text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 text-2xl">
                &times;
            </button>

            <h2 class="text-2xl font-bold mb-4 text-bsu-red dark:text-red-400">Scholar Details</h2>

            <template x-if="selectedScholar">
                <div class="space-y-4 text-gray-900 dark:text-gray-100">
                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p><strong class="text-gray-900 dark:text-gray-100">Name:</strong> <span x-text="selectedScholar.user.name" class="text-gray-700 dark:text-gray-300"></span></p>
                            <p><strong class="text-gray-900 dark:text-gray-100">Email:</strong> <span x-text="selectedScholar.user.email" class="text-gray-700 dark:text-gray-300"></span></p>
                            <p><strong class="text-gray-900 dark:text-gray-100">Campus:</strong> <span x-text="selectedScholar.user.campus.name" class="text-gray-700 dark:text-gray-300"></span></p>
                        </div>
                        <div>
                            <p><strong class="text-gray-900 dark:text-gray-100">Scholarship:</strong> <span x-text="selectedScholar.scholarship.scholarship_name" class="text-gray-700 dark:text-gray-300"></span></p>
                            <p><strong class="text-gray-900 dark:text-gray-100">Scholar Type:</strong> 
                                <span x-text="selectedScholar.type === 'new' ? 'New Scholar' : 'Old Scholar'" 
                                      :class="selectedScholar.type === 'new' ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-green-600 dark:text-green-400 font-semibold'">
                                </span>
                            </p>
                            <p><strong class="text-gray-900 dark:text-gray-100">Status:</strong> 
                                <span x-text="selectedScholar.status" 
                                      :class="selectedScholar.status === 'active' ? 'text-green-600 dark:text-green-400 font-semibold' : 'text-gray-600 dark:text-gray-400'">
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Grant Information -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h3 class="text-lg font-semibold mb-3 text-gray-900 dark:text-gray-100">Grant Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p><strong class="text-gray-900 dark:text-gray-100">Grant Count:</strong> 
                                    <span x-text="selectedScholar.grant_count" class="text-orange-600 dark:text-orange-400 font-semibold"></span>
                                </p>
                                <p><strong class="text-gray-900 dark:text-gray-100">Total Received:</strong> 
                                    <span x-text="'₱' + parseFloat(selectedScholar.total_grant_received).toLocaleString('en-PH', {minimumFractionDigits: 2})" class="text-green-600 dark:text-green-400 font-semibold"></span>
                                </p>
                            </div>
                            <div>
                                <p><strong class="text-gray-900 dark:text-gray-100">Scholarship Start:</strong> 
                                    <span x-text="new Date(selectedScholar.scholarship_start_date).toLocaleDateString()" class="text-gray-700 dark:text-gray-300"></span>
                                </p>
                                <p><strong class="text-gray-900 dark:text-gray-100">Scholarship End:</strong> 
                                    <span x-text="selectedScholar.scholarship_end_date ? new Date(selectedScholar.scholarship_end_date).toLocaleDateString() : 'Ongoing'" class="text-gray-700 dark:text-gray-300"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Grant History -->
                    <div x-show="selectedScholar.grant_history && selectedScholar.grant_history.length > 0">
                        <h3 class="text-lg font-semibold mb-3 text-gray-900 dark:text-gray-100">Grant History</h3>
                        <div class="space-y-2">
                            <template x-for="(grant, index) in selectedScholar.grant_history" :key="index">
                                <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-3">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="font-medium text-blue-900 dark:text-blue-100" x-text="'Grant #' + grant.grant_number"></p>
                                            <p class="text-sm text-blue-700 dark:text-blue-300" x-text="grant.description"></p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-blue-900 dark:text-blue-100" x-text="'₱' + parseFloat(grant.amount).toLocaleString('en-PH', {minimumFractionDigits: 2})"></p>
                                            <p class="text-sm text-blue-700 dark:text-blue-300" x-text="new Date(grant.date).toLocaleDateString()"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div x-show="selectedScholar.notes">
                        <h3 class="text-lg font-semibold mb-3 text-gray-900 dark:text-gray-100">Notes</h3>
                        <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-3">
                            <p class="text-gray-800 dark:text-gray-200" x-text="selectedScholar.notes"></p>
                        </div>
                    </div>
                </div>
            </template>

            <div class="flex flex-col items-center mt-6 space-y-3">
                <div class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg p-4 w-full">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-green-800 dark:text-green-200 font-medium">This student is an active scholar</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

