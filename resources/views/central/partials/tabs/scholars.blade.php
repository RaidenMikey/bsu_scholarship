<div x-show="tab === 'scholars' || tab === 'scholars-new' || tab === 'scholars-old'" x-cloak x-data="{ showModal: false, showFormModal: false, selectedScholar: null }">
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold text-bsu-red dark:text-red-400 mb-6" x-text="tab === 'scholars-new' ? 'New Scholars' : tab === 'scholars-old' ? 'Old Scholars' : 'Scholars'"></h1>
        <p class="text-gray-600 dark:text-gray-300 mb-6" x-text="tab === 'scholars-new' ? 'Students who have been accepted for scholarships but haven\'t received any grants yet.' : tab === 'scholars-old' ? 'Students who have been accepted for scholarships and have already received grants.' : 'Students who have been accepted for scholarships and their grant information.'"></p>

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
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400" x-text="tab === 'scholars-new' ? 'New Scholars' : tab === 'scholars-old' ? 'Old Scholars' : 'Total Scholars'"></p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white" x-text="tab === 'scholars-new' ? '{{ $scholars->where('type', 'new')->count() }}' : tab === 'scholars-old' ? '{{ $scholars->where('type', 'old')->count() }}' : '{{ $scholars->count() }}'"></p>
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
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Scholars</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white" x-text="tab === 'scholars-new' ? '{{ $scholars->where('type', 'new')->where('status', 'active')->count() }}' : tab === 'scholars-old' ? '{{ $scholars->where('type', 'old')->where('status', 'active')->count() }}' : '{{ $scholars->where('status', 'active')->count() }}'"></p>
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
