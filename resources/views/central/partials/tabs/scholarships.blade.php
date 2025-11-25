<!-- central/partials/tabs/scholarships.blade.php -->

<div x-show="tab === 'scholarships' || tab === 'scholarships-private' || tab === 'scholarships-government'" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-cloak>

    <!-- Header with Type Filter -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    <span x-show="tab === 'scholarships'" class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path d="M12 14l9-5-9-5-9 5 9 5z" />
                          <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                        </svg>
                        All Scholarships
                    </span>
                    <span x-show="tab === 'scholarships-private'" class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd" />
                        </svg>
                        Private Scholarships
                    </span>
                    <span x-show="tab === 'scholarships-government'" class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-500" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd" />
                        </svg>
                        Government Scholarships
                    </span>
                </h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    <span x-show="tab === 'scholarships'">Manage all scholarship programs</span>
                    <span x-show="tab === 'scholarships-private'">Private scholarship programs</span>
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

  <!-- Add Button as Card -->
  <div class="mb-8">
    <a href="{{ route('central.scholarships.create') }}"
        class="group cursor-pointer flex flex-col justify-center items-center bg-gradient-to-br from-red-50 to-red-100 dark:from-gray-700 dark:to-gray-600 hover:from-red-100 hover:to-red-200 dark:hover:from-gray-600 dark:hover:to-gray-500 text-bsu-red dark:text-bsu-red hover:text-bsu-redDark dark:hover:text-bsu-redDark text-2xl font-bold rounded-xl shadow-lg border-2 border-dashed border-bsu-red dark:border-bsu-red hover:border-bsu-redDark dark:hover:border-bsu-redDark p-4 transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1">
        <div class="group-hover:scale-110 transition-transform duration-200">
            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
        </div>
        <span class="text-base font-semibold group-hover:scale-105 transition-transform duration-200">Add New Scheme</span>
        <span class="text-xs text-bsu-red dark:text-bsu-red mt-1 opacity-75">Create a new scholarship program</span>
    </a>
  </div>

  <!-- Scholarships List -->
  <div>
        @forelse($scholarships as $scholarship)
        <div x-data="{ open: false }" 
             x-show="tab === 'scholarships' || 
                     (tab === 'scholarships-private' && '{{ $scholarship->scholarship_type }}' === 'private') ||
                     (tab === 'scholarships-government' && '{{ $scholarship->scholarship_type }}' === 'government')"
             class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-bsu-red p-6 hover:shadow-xl hover:border-bsu-redDark hover:shadow-bsu-red/20 transition-all duration-300 group relative overflow-hidden mb-8"
             @if($scholarship->background_image)
             style="background-image: linear-gradient(rgba(255,255,255,0.7), rgba(255,255,255,0.7)), url('{{ $scholarship->getBackgroundImageUrl() }}'); background-size: cover; background-position: center;"
             @endif>

                <!-- Scholarship Content -->
                <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                    <!-- Main Content -->
                    <div class="flex-1">
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
                                </div>
                            </div>
                        </div>

                        <!-- Description Preview -->
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-3 line-clamp-2">
                            {{ \Illuminate\Support\Str::limit($scholarship->description, 150) }}
                        </p>
                    </div>

                    <!-- Quick Info & Actions -->
                    <div class="flex flex-col sm:flex-row lg:flex-col gap-4 lg:min-w-[200px]">
                        <!-- Quick Stats -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-1 gap-3 text-sm">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Deadline:</span>
                                <div class="font-semibold {{ $scholarship->getDaysUntilDeadline() <= 7 ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                                    {{ $scholarship->submission_deadline?->format('M d, Y') }}
                                </div>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Amount:</span>
                                <div class="font-semibold text-green-600">
                                    @if($scholarship->grant_amount)
                                        ₱{{ number_format((float) $scholarship->grant_amount, 0) }}
                                    @else
                                        TBD
                                    @endif
                                </div>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Status:</span>
                                <div class="font-semibold {{ $scholarship->is_active ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $scholarship->is_active ? 'Active' : 'Inactive' }}
                                </div>
                            </div>
                        </div>

                        <!-- Slots Information -->
                        @if($scholarship->slots_available)
                        @php
                          $applicationsCount = $scholarship->applications()->count();
                          $slotsAvailable = $scholarship->slots_available;
                          $fillPercentage = min(100, ($applicationsCount / $slotsAvailable) * 100);
                        @endphp
                        <div class="mt-2">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Available Slots:</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $applicationsCount }} / {{ $slotsAvailable }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-bsu-red h-2 rounded-full transition-all duration-300" 
                                     data-width="{{ $fillPercentage }}"
                                     x-bind:style="'width: ' + $el.dataset.width + '%'"></div>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ number_format($fillPercentage, 1) }}% filled
                            </div>
                        </div>
                        @endif

                        <!-- Dropdown Toggle Button -->
                        <button @click="open = !open" 
                                class="flex items-center justify-center gap-2 px-4 py-2 bg-bsu-red hover:bg-bsu-redDark text-white rounded-lg transition-colors">
                            <span class="text-sm font-medium">View Details</span>
                            <svg class="w-4 h-4 transition-transform" 
                                 :class="{ 'rotate-180': open }" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
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
                        
                        <!-- Additional Eligibility Information -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Additional Information</h4>
                            <div class="space-y-2">
                                @if($scholarship->grant_type)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Grant Type:</span>
                                    <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $scholarship->grant_type)) }}</span>
                                </div>
                                @endif
                                @if($scholarship->slots_available)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Total Slots:</span>
                                    <span class="font-medium">{{ $scholarship->slots_available }}</span>
                                </div>
                                @endif
                                @if($scholarship->application_start_date)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Application Opens:</span>
                                    <span class="font-medium">{{ $scholarship->application_start_date?->format('M d, Y') }}</span>
                                </div>
                                @endif
                            </div>
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
                        <div class="flex gap-3 pt-2 w-full">
                            <div class="flex-1">
                                <a href="{{ route('central.scholarships.edit', $scholarship->id) }}" 
                                   class="block w-full px-4 py-3 text-sm bg-bsu-red hover:bg-bsu-redDark text-white rounded-lg transition-colors font-medium text-center">
                                    Edit
                                </a>
                            </div>
                            <div class="flex-1">
                                <form action="{{ route('central.scholarships.destroy', $scholarship->id) }}" 
                                      method="POST" 
                                      class="w-full"
                                      onsubmit="return confirmDelete('{{ $scholarship->scholarship_name }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-full px-4 py-3 text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-medium">
                                        Remove
                                    </button>
                                </form>
                            </div>
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
    <div x-show="tab === 'scholarships-private' && !hasVisibleScholarships('private')" 
         class="col-span-full text-center py-12">
        <div class="text-gray-400 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-green-500" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd" />
            </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">No Private Scholarships</h3>
        <p class="text-gray-500 dark:text-gray-500">There are currently no private scholarship programs available.</p>
    </div>

    <div x-show="tab === 'scholarships-government' && !hasVisibleScholarships('government')" 
         class="col-span-full text-center py-12">
        <div class="text-gray-400 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-orange-500" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd" />
            </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">No Government Scholarships</h3>
        <p class="text-gray-500 dark:text-gray-500">There are currently no government scholarship programs available.</p>
    </div>


    <!-- JavaScript for form handling -->
    <script>
        function confirmDelete(scholarshipName) {
            return confirm('WARNING: This will permanently delete the scholarship "' + scholarshipName + '" and all associated applications. This action cannot be undone. Are you sure you want to proceed?');
        }

        // Function to check if there are visible scholarships of a specific type
        function hasVisibleScholarships(type) {
            const scholarships = document.querySelectorAll('[x-show*="' + type + '"]');
            return scholarships.length > 0;
        }

    </script>
</div>
