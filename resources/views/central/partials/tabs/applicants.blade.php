<div x-show="tab === 'applicants'" x-cloak x-data="{ showModal: false, showFormModal: false, selectedApp: null }">
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold text-bsu-red dark:text-red-400 mb-6">SFAO-Approved Applicants</h1>
        <p class="text-gray-600 dark:text-gray-300 mb-6">These applications have been reviewed and approved by SFAO administrators.</p>

        <!-- Filtering and Sorting Controls -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
            <form method="GET" action="{{ route('central.dashboard') }}" class="space-y-4">
                <!-- Filter Row -->
                <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center">
                    <div class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Status:</label>
                        <select name="status_filter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-bsu-red focus:border-bsu-red">
                            @foreach($statusOptions as $option)
                                <option value="{{ $option['value'] }}" {{ $statusFilter == $option['value'] ? 'selected' : '' }}>
                                    {{ $option['label'] }}
                                </option>
                            @endforeach
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
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Applicant Type:</label>
                        <select name="applicant_type_filter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-bsu-red focus:border-bsu-red">
                            @foreach($applicantTypeOptions as $option)
                                <option value="{{ $option['value'] }}" {{ $applicantTypeFilter == $option['value'] ? 'selected' : '' }}>
                                    {{ $option['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Sort Row -->
                <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                    <div class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Sort by:</label>
                        <select name="sort_by" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-bsu-red focus:border-bsu-red">
                            <option value="created_at" {{ $sortBy === 'created_at' ? 'selected' : '' }}>Applied Date</option>
                            <option value="name" {{ $sortBy === 'name' ? 'selected' : '' }}>Name</option>
                            <option value="email" {{ $sortBy === 'email' ? 'selected' : '' }}>Email</option>
                            <option value="scholarship" {{ $sortBy === 'scholarship' ? 'selected' : '' }}>Scholarship</option>
                            <option value="status" {{ $sortBy === 'status' ? 'selected' : '' }}>Status</option>
                            <option value="type" {{ $sortBy === 'type' ? 'selected' : '' }}>Applicant Type</option>
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
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Applications</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $applications->count() }}</p>
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
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Approved</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $applications->where('status', 'approved')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Claimed</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $applications->where('status', 'claimed')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">New Applicants</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $applications->where('type', 'new')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if ($applications->isEmpty())
            <div class="text-center py-12">
                <div class="text-gray-400 dark:text-gray-500 text-6xl mb-4">📋</div>
                <h3 class="text-xl font-medium text-gray-900 dark:text-gray-100 mb-2">No SFAO-approved applications</h3>
                <p class="text-gray-600 dark:text-gray-400">There are currently no applications that have been approved by SFAO administrators.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 shadow-lg rounded-lg">
                    <thead class="bg-bsu-red text-white">
                        <tr>
                            <th class="px-4 py-3 text-left">#</th>
                            <th class="px-4 py-3 text-left">Name</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-left">Scholarship</th>
                            <th class="px-4 py-3 text-left">Applicant Type</th>
                            <th class="px-4 py-3 text-left">Grant Count</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Applied At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($applications as $index => $application)
                        <tr 
                            class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 transition cursor-pointer"
                            @click="selectedApp = {{ $application->toJson() }}; showModal = true"
                        >
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $application->user->name ?? '-' }}</td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $application->user->email ?? '-' }}</td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $application->scholarship->scholarship_name ?? '-' }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded {{ $application->getApplicantTypeBadgeColor() }}">
                                    {{ $application->getApplicantTypeDisplayName() }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded {{ $application->getGrantCountBadgeColor() }}">
                                    {{ $application->getGrantCountDisplay() }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $application->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                       ($application->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                       ($application->status === 'claimed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                                       'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200')) }}">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $application->created_at?->format('M d, Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Applicant Details Modal -->
    <div 
        x-show="showModal" 
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        x-transition 
        x-cloak
    >
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-xl relative">
            <button @click="showModal = false" class="absolute top-2 right-2 text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 text-2xl">
                &times;
            </button>

            <h2 class="text-2xl font-bold mb-4 text-bsu-red dark:text-red-400">Applicant Details</h2>

            <template x-if="selectedApp">
                <div class="space-y-3 text-gray-900 dark:text-gray-100">
                    <p><strong class="text-gray-900 dark:text-gray-100">Name:</strong> <span x-text="selectedApp.user.name" class="text-gray-700 dark:text-gray-300"></span></p>
                    <p><strong class="text-gray-900 dark:text-gray-100">Email:</strong> <span x-text="selectedApp.user.email" class="text-gray-700 dark:text-gray-300"></span></p>
                    <p><strong class="text-gray-900 dark:text-gray-100">Scholarship:</strong> <span x-text="selectedApp.scholarship.scholarship_name" class="text-gray-700 dark:text-gray-300"></span></p>
                    <p><strong class="text-gray-900 dark:text-gray-100">Applicant Type:</strong> 
                        <span x-text="selectedApp.type === 'new' ? 'New Applicant' : 'Continuing Applicant'" 
                              :class="selectedApp.type === 'new' ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-green-600 dark:text-green-400 font-semibold'">
                        </span>
                    </p>
                    <p><strong class="text-gray-900 dark:text-gray-100">Grant Count:</strong> 
                        <span x-text="selectedApp.grant_count <= 0 ? 'No grants received' : (selectedApp.grant_count === 1 ? '1st grant' : selectedApp.grant_count + 'th grant')" 
                              :class="selectedApp.grant_count <= 0 ? 'text-gray-600 dark:text-gray-400' : 'text-orange-600 dark:text-orange-400 font-semibold'">
                        </span>
                    </p>
                    <p><strong class="text-gray-900 dark:text-gray-100">Status:</strong> <span x-text="selectedApp.status" class="text-gray-700 dark:text-gray-300"></span></p>
                    <p><strong class="text-gray-900 dark:text-gray-100">Applied At:</strong> <span x-text="new Date(selectedApp.created_at).toLocaleDateString()" class="text-gray-700 dark:text-gray-300"></span></p>
                </div>
            </template>

            <div class="flex flex-col items-center mt-6 space-y-3">
                <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4 w-full">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-blue-800 dark:text-blue-200 font-medium">This application has been approved by SFAO</span>
                    </div>
                </div>
                <form method="POST" :action="'/central/applications/' + selectedApp.id + '/claim'" class="w-2/3">
                    @csrf
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Mark as Claimed
                    </button>
                </form>
            </div>
        </div>
    </div>

    
</div>
