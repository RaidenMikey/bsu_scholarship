<!-- central/partials/tabs/scholarships.blade.php -->

<div x-show="tab === 'scholarships'" x-transition x-cloak>

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
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-bsu-red p-6 flex flex-col justify-between h-full min-h-[320px] hover:shadow-xl hover:border-bsu-redDark hover:shadow-bsu-red/20 transition-all duration-300 transform hover:-translate-y-1 group">

                <div>
                    <!-- Header Section -->
                    <div class="mb-4">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-xl font-bold text-bsu-red dark:text-white group-hover:text-bsu-redDark dark:group-hover:text-bsu-red transition-colors duration-200">
                                {{ $scholarship->scholarship_name }}
                            </h3>
                            @php
                              $statusBadge = $scholarship->getStatusBadge();
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusBadge['color'] }} shadow-sm">
                              {{ $statusBadge['text'] }}
                            </span>
                        </div>

                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed line-clamp-2">
                            {{ Str::limit($scholarship->description, 120) }}
                        </p>
                    </div>

                    <!-- Badges Section -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $scholarship->getScholarshipTypeBadgeColor() }} shadow-sm">
                          {{ ucfirst($scholarship->scholarship_type) }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $scholarship->getPriorityBadgeColor() }} shadow-sm">
                          {{ ucfirst($scholarship->priority_level) }} Priority
                        </span>
                        @if($scholarship->renewal_allowed)
                          <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-bsu-red/10 text-bsu-red border border-bsu-red/20 shadow-sm">
                            🔄 Renewable
                          </span>
                        @endif
                    </div>
                </div>

                <!-- Details Section -->
                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4 space-y-3 border border-red-100 dark:border-red-800">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Submission Deadline</span>
                        <span class="text-sm font-semibold {{ $scholarship->getDaysUntilDeadline() <= 7 ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                          {{ $scholarship->submission_deadline?->format('M d, Y') }}
                          @if($scholarship->getDaysUntilDeadline() > 0)
                            <span class="text-xs ml-1 {{ $scholarship->getDaysUntilDeadline() <= 7 ? 'text-red-500' : 'text-gray-500' }}">({{ $scholarship->getDaysUntilDeadline() }}d left)</span>
                          @elseif($scholarship->getDaysUntilDeadline() == 0)
                            <span class="text-xs ml-1 text-red-600 font-bold">(Today!)</span>
                          @else
                            <span class="text-xs ml-1 text-red-600">(Expired)</span>
                          @endif
                        </span>
                    </div>

                    @if($scholarship->application_start_date)
                      <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Application Opens</span>
                        <span class="text-sm font-semibold {{ now()->gte($scholarship->application_start_date) ? 'text-green-600' : 'text-gray-900 dark:text-white' }}">
                          {{ $scholarship->application_start_date?->format('M d, Y') }}
                          @if(now()->lt($scholarship->application_start_date))
                            <span class="text-xs ml-1 text-gray-500">({{ now()->diffInDays($scholarship->application_start_date) }}d to go)</span>
                          @else
                            <span class="text-xs ml-1 text-green-600">(Open)</span>
                          @endif
                        </span>
                      </div>
                    @endif

                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Applications</span>
                        <span class="text-sm font-semibold {{ $scholarship->isFull() ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                          {{ $scholarship->getApplicationCount() }} / {{ $scholarship->slots_available ?? '∞' }}
                          @if($scholarship->isFull() && $scholarship->slots_available)
                            <span class="text-xs ml-1 text-red-600 font-bold">(Full)</span>
                          @endif
                        </span>
                    </div>

                    @if ($scholarship->grant_amount)
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Grant Amount</span>
                            <span class="text-sm font-bold text-green-600">₱{{ number_format((float) $scholarship->grant_amount, 0) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Status</span>
                        <span class="text-sm font-semibold {{ $scholarship->isAcceptingApplications() ? 'text-green-600' : 'text-red-600' }}">
                          {{ $scholarship->isAcceptingApplications() ? 'Accepting Applications' : 'Closed' }}
                        </span>
                    </div>

                    @if($scholarship->eligibility_notes)
                      <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg border border-blue-200 dark:border-blue-800">
                        <p class="text-xs text-blue-800 dark:text-blue-200 font-medium">
                          <span class="font-semibold">Notes:</span> {{ Str::limit($scholarship->eligibility_notes, 80) }}
                        </p>
                      </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 pt-4 border-t border-bsu-red/30 dark:border-bsu-red/50">
                    <div class="flex gap-3 scholarship-action-buttons">
                        <!-- Edit Button -->
                        <a href="{{ route('central.scholarships.edit', $scholarship->id) }}"
                           class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-bsu-red hover:bg-bsu-redDark text-white text-sm font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200 transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-bsu-red focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </a>
                        
                        <!-- Remove Button -->
                        <form action="{{ route('central.scholarships.destroy', $scholarship->id) }}" method="POST" class="flex-1 m-0 p-0"
                              onsubmit="return confirmDelete('{{ $scholarship->scholarship_name }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-bsu-redDark hover:bg-red-800 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200 transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-bsu-redDark focus:ring-offset-2 border-0">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Remove
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500 dark:text-gray-400 col-span-full">
                No scholarships available.
            </p>
        @endforelse
    </div>

    <!-- JavaScript for form handling -->
    <script>
        function confirmDelete(scholarshipName) {
            return confirm('⚠️ WARNING: This will permanently delete the scholarship "' + scholarshipName + '" and all associated applications. This action cannot be undone. Are you sure you want to proceed?');
        }

        // Handle form submission with loading state
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('form[action*="destroy"]');
            
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const button = this.querySelector('button[type="submit"]');
                    if (button) {
                        button.disabled = true;
                        button.innerHTML = '<svg class="w-4 h-4 mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Deleting...';
                    }
                });
            });
        });
    </script>
</div>
