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
            class="cursor-pointer flex flex-col justify-center items-center bg-gray-200 hover:bg-bsu-red hover:text-white text-bsu-red text-4xl font-bold rounded-xl shadow-lg border-2 border-bsu-redDark p-4 transition duration-300 h-full min-h-[280px]">
            +
            <span class="text-base font-medium mt-2">Add Scheme</span>
        </a>

        <!-- Scholarships List -->
        @forelse($scholarships as $scholarship)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-bsu-redDark p-4 flex flex-col justify-between h-full min-h-[280px] hover:shadow-xl transition">

                <div>
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-lg font-bold text-bsu-red dark:text-white">
                            {{ $scholarship->scholarship_name }}
                        </h3>
                        @php
                          $statusBadge = $scholarship->getStatusBadge();
                        @endphp
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusBadge['color'] }}">
                          {{ $statusBadge['text'] }}
                        </span>
                    </div>

                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-3 line-clamp-2">
                        {{ Str::limit($scholarship->description, 120) }}
                    </p>

                    <!-- Priority and Renewal Badges -->
                    <div class="flex flex-wrap gap-1 mb-3">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $scholarship->getScholarshipTypeBadgeColor() }}">
                          {{ ucfirst($scholarship->scholarship_type) }}
                        </span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $scholarship->getPriorityBadgeColor() }}">
                          {{ ucfirst($scholarship->priority_level) }} Priority
                        </span>
                        @if($scholarship->renewal_allowed)
                          <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            🔄 Renewable
                          </span>
                        @endif
                    </div>
                </div>

                <div class="mt-2 space-y-2">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600 dark:text-gray-400"><strong>Submission Deadline:</strong></span>
                        <span class="font-semibold {{ $scholarship->getDaysUntilDeadline() <= 7 ? 'text-red-600' : 'text-gray-600' }}">
                          {{ $scholarship->submission_deadline?->format('M d, Y') }}
                          @if($scholarship->getDaysUntilDeadline() > 0)
                            <span class="text-xs">({{ $scholarship->getDaysUntilDeadline() }}d left)</span>
                          @elseif($scholarship->getDaysUntilDeadline() == 0)
                            <span class="text-xs text-red-600">(Today!)</span>
                          @else
                            <span class="text-xs text-red-600">(Expired)</span>
                          @endif
                        </span>
                    </div>

                    @if($scholarship->application_start_date)
                      <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600 dark:text-gray-400"><strong>Application Opens:</strong></span>
                        <span class="{{ now()->gte($scholarship->application_start_date) ? 'text-green-600 font-semibold' : 'text-gray-600' }}">
                          {{ $scholarship->application_start_date?->format('M d, Y') }}
                          @if(now()->lt($scholarship->application_start_date))
                            <span class="text-xs">({{ now()->diffInDays($scholarship->application_start_date) }}d to go)</span>
                          @else
                            <span class="text-xs text-green-600">(Open)</span>
                          @endif
                        </span>
                      </div>
                    @endif

                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600 dark:text-gray-400"><strong>Applications:</strong></span>
                        <span class="{{ $scholarship->isFull() ? 'text-red-600' : 'text-gray-600' }}">
                          {{ $scholarship->getApplicationCount() }} / {{ $scholarship->slots_available ?? '∞' }}
                          @if($scholarship->isFull() && $scholarship->slots_available)
                            <span class="text-xs text-red-600">(Full)</span>
                          @endif
                        </span>
                    </div>

                    @if ($scholarship->grant_amount)
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600 dark:text-gray-400"><strong>Grant Amount:</strong></span>
                            <span class="font-semibold text-green-600">₱{{ number_format((float) $scholarship->grant_amount, 0) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600 dark:text-gray-400"><strong>Status:</strong></span>
                        <span class="font-semibold {{ $scholarship->isAcceptingApplications() ? 'text-green-600' : 'text-red-600' }}">
                          {{ $scholarship->isAcceptingApplications() ? 'Accepting Applications' : 'Closed' }}
                        </span>
                    </div>

                    @if($scholarship->eligibility_notes)
                      <div class="mt-2 p-2 bg-blue-50 dark:bg-blue-900/20 rounded text-xs">
                        <strong>Notes:</strong> {{ Str::limit($scholarship->eligibility_notes, 80) }}
                      </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="mt-4 flex justify-end space-x-2">
                    <a href="{{ route('central.scholarships.edit', $scholarship->id) }}"
                        class="px-3 py-1 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    <form action="{{ route('central.scholarships.destroy', $scholarship->id) }}" method="POST" class="inline"
                          onsubmit="return confirmDelete('{{ $scholarship->scholarship_name }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-3 py-1 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition duration-200 flex items-center"
                            onclick="this.form.submit();">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Remove
                        </button>
                    </form>
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
