<div x-show="tab === 'qualified-applicants'" x-cloak x-data="{ showModal: false, showSelectionModal: false, selectedApplicants: [], selectAll: false }">
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold text-bsu-red dark:text-red-400 mb-6">Qualified Applicants</h1>
        <p class="text-gray-600 dark:text-gray-300 mb-6">Applicants who have been approved by SFAO and are ready for central selection to become scholars.</p>

        <!-- Filtering and Sorting Controls -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
            <form method="GET" action="{{ route('central.dashboard') }}" class="space-y-4">
                <input type="hidden" name="tab" value="qualified-applicants">
                <!-- Filter Row -->
                <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center">
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
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Sort by:</label>
                        <select name="sort_by" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-bsu-red focus:border-bsu-red">
                            <option value="name" {{ $sortBy == 'name' ? 'selected' : '' }}>Name</option>
                            <option value="campus" {{ $sortBy == 'campus' ? 'selected' : '' }}>Campus</option>
                            <option value="scholarship" {{ $sortBy == 'scholarship' ? 'selected' : '' }}>Scholarship</option>
                            <option value="date_approved" {{ $sortBy == 'date_approved' ? 'selected' : '' }}>Date Approved</option>
                        </select>
                    </div>

                    <div class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Order:</label>
                        <select name="sort_order" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-bsu-red focus:border-bsu-red">
                            <option value="asc" {{ $sortOrder == 'asc' ? 'selected' : '' }}>Ascending</option>
                            <option value="desc" {{ $sortOrder == 'desc' ? 'selected' : '' }}>Descending</option>
                        </select>
                    </div>

                    <div class="flex space-x-2">
                        <button type="submit" class="px-4 py-2 bg-bsu-red text-white rounded-md hover:bg-bsu-redDark transition-colors text-sm">
                            Apply Filters
                        </button>
                        <a href="{{ route('central.dashboard') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors text-sm">
                            Clear Filters
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Qualified</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $qualifiedApplicants ? $qualifiedApplicants->count() : 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Selected</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white" x-text="selectedApplicants.length">0</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending Selection</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white" x-text="({{ $qualifiedApplicants ? $qualifiedApplicants->count() : 0 }}) - selectedApplicants.length"></p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Campuses</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ count($campusOptions ?? []) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Selection Controls -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6" x-show="selectedApplicants.length > 0">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        <span x-text="selectedApplicants.length"></span> applicant(s) selected
                    </span>
                    <button @click="showSelectionModal = true" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors text-sm">
                        Select as Scholars
                    </button>
                </div>
                <button @click="selectedApplicants = []; selectAll = false" 
                        class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors text-sm">
                    Clear Selection
                </button>
            </div>
        </div>

        <!-- Applicants Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" 
                                       x-model="selectAll" 
                                       @change="selectAll ? selectedApplicants = {{ ($qualifiedApplicants ? $qualifiedApplicants->pluck('id')->toJson() : '[]') }} : selectedApplicants = []"
                                       class="rounded border-gray-300 text-bsu-red focus:ring-bsu-red">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Campus</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Scholarship</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date Approved</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($qualifiedApplicants ?? [] as $applicant)
                            @php
                                $application = $applicant->applications->first();
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" 
                                           value="{{ $applicant->id }}"
                                           x-model="selectedApplicants"
                                           class="rounded border-gray-300 text-bsu-red focus:ring-bsu-red">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <img class="h-10 w-10 rounded-full" 
                                                 src="{{ $applicant->profile_picture ? Storage::url($applicant->profile_picture) : asset('images/default-avatar.png') }}" 
                                                 alt="{{ $applicant->first_name }} {{ $applicant->last_name }}">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $applicant->first_name }} {{ $applicant->last_name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $applicant->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $applicant->campus->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $application ? $application->scholarship->scholarship_name : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $application && $application->updated_at ? $application->updated_at->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Approved by SFAO
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button @click="showModal = true; selectedApplicant = {{ $applicant->id }}" 
                                            class="text-bsu-red hover:text-bsu-redDark mr-3">
                                        View Details
                                    </button>
                                    <button @click="selectedApplicants = [...selectedApplicants, {{ $applicant->id }}]" 
                                            class="text-green-600 hover:text-green-800">
                                        Select
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No qualified applicants found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination - Not implemented yet -->
        {{-- @if($qualifiedApplicants && $qualifiedApplicants->hasPages())
            <div class="mt-6">
                {{ $qualifiedApplicants->links() }}
            </div>
        @endif --}}
    </div>

    <!-- Selection Confirmation Modal -->
    <div x-show="showSelectionModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900">
                    <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="mt-2 px-7 py-3">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Confirm Selection</h3>
                    <div class="mt-2 px-1 py-3">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Are you sure you want to select <span x-text="selectedApplicants.length"></span> applicant(s) as scholars?
                        </p>
                    </div>
                </div>
                <div class="items-center px-4 py-3">
                    <button @click="showSelectionModal = false; selectedApplicants = []" 
                            class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 mr-2">
                        Cancel
                    </button>
                    <button @click="showSelectionModal = false; // TODO: Implement scholar selection" 
                            class="px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        Confirm Selection
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
