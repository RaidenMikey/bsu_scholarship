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
            <span class="text-base font-medium mt-2">Add Scholarship</span>
        </a>

        <!-- Scholarships List -->
        @forelse($scholarships as $scholarship)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-bsu-redDark p-4 flex flex-col justify-between h-full min-h-[280px] hover:shadow-xl transition">

                <div>
                    <h3 class="text-lg font-bold text-bsu-red dark:text-white mb-2">
                        {{ $scholarship->scholarship_name }}
                    </h3>

                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-2 line-clamp-3">
                        {{ $scholarship->description }}
                    </p>
                </div>

                <div class="mt-2">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <strong>Document Submission Deadline:</strong>
                        {{ $scholarship->deadline ? \Carbon\Carbon::parse($scholarship->deadline)->format('M d, Y') : 'No deadline' }}
                    </p>

                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <strong>Slots:</strong>
                        @if (is_null($scholarship->slots_available))
                            Unlimited
                        @elseif ($scholarship->slots_available === 0)
                            Full
                        @else
                            {{ $scholarship->slots_available }}
                        @endif
                    </p>

                    @if ($scholarship->grant_amount)
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <strong>Grant Amount:</strong> ₱{{ number_format($scholarship->grant_amount, 2) }}
                        </p>
                    @endif

                    @if ($scholarship->renewal_allowed)
                        <span class="inline-block mt-2 text-xs bg-green-200 text-green-800 px-2 py-1 rounded">
                            Renewal Allowed
                        </span>
                    @endif

                    @if ($scholarship->is_active)
                        <span class="inline-block mt-2 text-xs bg-green-200 text-green-800 px-2 py-1 rounded">
                            Active
                        </span>
                    @else
                        <span class="inline-block mt-2 text-xs bg-gray-300 text-gray-800 px-2 py-1 rounded">
                            Closed
                        </span>
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
