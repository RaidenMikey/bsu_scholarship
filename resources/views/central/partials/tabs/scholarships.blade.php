<!-- central/partials/tabs/scholarships.blade.php -->

<div x-show="tab === 'scholarships'" x-transition x-cloak>

    <!-- Controls -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-8 space-y-4 md:space-y-0">
        <!-- Sort -->
        <div>
            <label for="sort" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Sort By:</label>
            <select id="sort"
                class="ml-2 border rounded-lg px-3 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring focus:ring-bsu-red">
                <option value="name">Scholarship Name</option>
                <option value="deadline">Deadline</option>
            </select>
        </div>

        <!-- Filter -->
        <div>
            <label for="filter" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Filter:</label>
            <select id="filter"
                class="ml-2 border rounded-lg px-3 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring focus:ring-bsu-red">
                <option value="all">All</option>
                <option value="active">Active Scholarships</option>
                <option value="closed">Closed Scholarships</option>
            </select>
        </div>
    </div>

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
                        class="px-3 py-1 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                        Edit
                    </a>
                    <form action="{{ route('central.scholarships.destroy', $scholarship->id) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this scholarship?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-3 py-1 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">
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
</div>
