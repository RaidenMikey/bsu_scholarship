<!-- central/partials/tabs/scholarships.blade.php -->

<div x-show="tab === 'scholarships' || tab === 'scholarships-internal' || tab === 'scholarships-external' || tab === 'scholarships-public' || tab === 'scholarships-government'" x-transition x-cloak>

    <!-- Header with Type Filter -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    <span x-show="tab === 'scholarships'">📚 All Scholarships</span>
                    <span x-show="tab === 'scholarships-internal'">🔵 Internal Scholarships</span>
                    <span x-show="tab === 'scholarships-external'">🟣 External Scholarships</span>
                    <span x-show="tab === 'scholarships-public'">🟢 Public Scholarships</span>
                    <span x-show="tab === 'scholarships-government'">🟠 Government Scholarships</span>
                </h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    <span x-show="tab === 'scholarships'">Manage all scholarship programs</span>
                    <span x-show="tab === 'scholarships-internal'">Internal university scholarship programs</span>
                    <span x-show="tab === 'scholarships-external'">External partner scholarship programs</span>
                    <span x-show="tab === 'scholarships-public'">Public scholarship programs</span>
                    <span x-show="tab === 'scholarships-government'">Government scholarship programs</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Sorting Controls -->
    <x-sorting-controls 
      :currentSort="request('sort_by', 'created_at')" 
      :currentOrder="request('sort_order', 'desc')"
      :baseUrl="route('central.dashboard')"
      role="central"
    />

    <!-- Scholarships Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Add Button as Card -->
        <a href="{{ route('central.scholarships.create') }}"
            class="group cursor-pointer flex flex-col justify-center items-center bg-gradient-to-br from-red-50 to-red-100 dark:from-gray-700 dark:to-gray-600 hover:from-red-100 hover:to-red-200 dark:hover:from-gray-600 dark:hover:to-gray-500 text-bsu-red dark:text-bsu-red hover:text-bsu-redDark dark:hover:text-bsu-redDark text-4xl font-bold rounded-xl shadow-lg border-2 border-dashed border-bsu-red dark:border-bsu-red hover:border-bsu-redDark dark:hover:border-bsu-redDark p-6 transition-all duration-300 h-full min-h-[320px] hover:shadow-xl transform hover:-translate-y-1">
            <div class="group-hover:scale-110 transition-transform duration-200">
                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </div>
            <span class="text-lg font-semibold mt-2 group-hover:scale-105 transition-transform duration-200">Add New Scheme</span>
            <span class="text-sm text-bsu-red dark:text-bsu-red mt-1 opacity-75">Create a new scholarship program</span>
        </a>

        <!-- Scholarships List -->
        @forelse($scholarships as $scholarship)
        <div x-data="{ open: false }" 
             x-show="tab === 'scholarships' || 
                     (tab === 'scholarships-internal' && '{{ $scholarship->scholarship_type }}' === 'internal') ||
                     (tab === 'scholarships-external' && '{{ $scholarship->scholarship_type }}' === 'external') ||
                     (tab === 'scholarships-public' && '{{ $scholarship->scholarship_type }}' === 'public') ||
                     (tab === 'scholarships-government' && '{{ $scholarship->scholarship_type }}' === 'government')"
             class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-bsu-red p-6 hover:shadow-xl hover:border-bsu-redDark hover:shadow-bsu-red/20 transition-all duration-300 transform hover:-translate-y-1 group relative overflow-hidden"
             @if($scholarship->background_image)
             style="background-image: linear-gradient(rgba(255,255,255,0.7), rgba(255,255,255,0.7)), url('{{ $scholarship->getBackgroundImageUrl() }}'); background-size: cover; background-position: center;"
             @endif>

                <!-- Scholarship Content -->
                <div class="flex flex-col h-full">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-bsu-red dark:text-white group-hover:text-bsu-redDark dark:group-hover:text-bsu-red transition-colors duration-200">
                                {{ $scholarship->scholarship_name }}
                            </h3>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $scholarship->getScholarshipTypeBadgeColor() }} shadow-sm">
                                  {{ ucfirst($scholarship->scholarship_type) }}
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $scholarship->getPriorityBadgeColor() }} shadow-sm">
                                  {{ ucfirst($scholarship->priority_level) }} Priority
                                </span>
                            </div>
                        </div>
                        <!-- Dropdown Toggle Button -->
                        <button @click="open = !open" 
                                class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 text-gray-400 transition-transform" 
                                 :class="{ 'rotate-180': open }" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Basic Info -->
                    <div class="flex-1 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Deadline:</span>
                            <span class="text-sm font-semibold {{ $scholarship->getDaysUntilDeadline() <= 7 ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                                {{ $scholarship->submission_deadline?->format('M d, Y') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Amount:</span>
                            <span class="text-sm font-semibold text-green-600">
                                @if($scholarship->grant_amount)
                                    ₱{{ number_format((float) $scholarship->grant_amount, 0) }}
                                @else
                                    TBD
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Status:</span>
                            <span class="text-sm font-semibold {{ $scholarship->is_active ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $scholarship->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>

                    <!-- Dropdown Content -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform -translate-y-2"
                         class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 space-y-4">
                        
                        <!-- Description -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Description</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $scholarship->description }}</p>
                        </div>

                        <!-- Detailed Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Key Information</h4>
                                <div class="space-y-1">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Type:</span>
                                        <span class="font-medium">{{ ucfirst($scholarship->scholarship_type) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Priority:</span>
                                        <span class="font-medium">{{ ucfirst($scholarship->priority_level) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Renewable:</span>
                                        <span class="font-medium">{{ $scholarship->renewal_allowed ? 'Yes' : 'No' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Timeline & Amount</h4>
                                <div class="space-y-1">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Deadline:</span>
                                        <span class="font-medium">{{ $scholarship->submission_deadline?->format('M d, Y') }}</span>
                                    </div>
                                    @if($scholarship->application_start_date)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Application Opens:</span>
                                        <span class="font-medium">{{ $scholarship->application_start_date?->format('M d, Y') }}</span>
                                    </div>
                                    @endif
                                    @if($scholarship->grant_amount)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Grant Amount:</span>
                                        <span class="font-medium text-green-600 dark:text-green-400">₱{{ number_format((float) $scholarship->grant_amount, 2) }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Eligibility Notes -->
                        @if($scholarship->eligibility_notes)
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Eligibility Notes</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $scholarship->eligibility_notes }}</p>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-2 pt-2">
                            <a href="{{ route('central.scholarships.edit', $scholarship->id) }}" 
                               class="px-3 py-1 text-xs bg-bsu-red hover:bg-bsu-redDark text-white rounded transition-colors">
                                Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500 dark:text-gray-400 col-span-full">
                No scholarships available.
            </p>
        @endforelse
    </div>

    <!-- Empty State for Filtered Types -->
    <div x-show="tab === 'scholarships-internal' && !hasVisibleScholarships('internal')" 
         class="col-span-full text-center py-12">
        <div class="text-gray-400 dark:text-gray-500 text-6xl mb-4">🔵</div>
        <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">No Internal Scholarships</h3>
        <p class="text-gray-500 dark:text-gray-500">There are currently no internal scholarship programs available.</p>
    </div>

    <div x-show="tab === 'scholarships-external' && !hasVisibleScholarships('external')" 
         class="col-span-full text-center py-12">
        <div class="text-gray-400 dark:text-gray-500 text-6xl mb-4">🟣</div>
        <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">No External Scholarships</h3>
        <p class="text-gray-500 dark:text-gray-500">There are currently no external scholarship programs available.</p>
    </div>

    <div x-show="tab === 'scholarships-public' && !hasVisibleScholarships('public')" 
         class="col-span-full text-center py-12">
        <div class="text-gray-400 dark:text-gray-500 text-6xl mb-4">🟢</div>
        <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">No Public Scholarships</h3>
        <p class="text-gray-500 dark:text-gray-500">There are currently no public scholarship programs available.</p>
    </div>

    <div x-show="tab === 'scholarships-government' && !hasVisibleScholarships('government')" 
         class="col-span-full text-center py-12">
        <div class="text-gray-400 dark:text-gray-500 text-6xl mb-4">🟠</div>
        <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">No Government Scholarships</h3>
        <p class="text-gray-500 dark:text-gray-500">There are currently no government scholarship programs available.</p>
    </div>


    <!-- JavaScript for form handling -->
    <script>
        function confirmDelete(scholarshipName) {
            return confirm('⚠️ WARNING: This will permanently delete the scholarship "' + scholarshipName + '" and all associated applications. This action cannot be undone. Are you sure you want to proceed?');
        }

        // Function to check if there are visible scholarships of a specific type
        function hasVisibleScholarships(type) {
            const scholarships = document.querySelectorAll('[x-show*="' + type + '"]');
            return scholarships.length > 0;
        }

    </script>
</div>
