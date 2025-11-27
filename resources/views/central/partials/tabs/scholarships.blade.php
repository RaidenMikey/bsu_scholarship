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
      :currentSort="request('sort_by', 'name')" 
      :currentOrder="request('sort_order', 'asc')"
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
        <!-- All Scholarships -->
        <div x-show="tab === 'scholarships'">
            @forelse($scholarshipsAll as $scholarship)
                @include('central.partials.components.scholarship-card', ['scholarship' => $scholarship])
            @empty
                <p class="text-center text-gray-500 dark:text-gray-400 col-span-full py-8">
                    No scholarships available.
                </p>
            @endforelse
            <div class="mt-8">
                {{ $scholarshipsAll->appends(['tab' => 'scholarships'])->links('vendor.pagination.custom') }}
            </div>
        </div>

        <!-- Private Scholarships -->
        <div x-show="tab === 'scholarships-private'">
            @forelse($scholarshipsPrivate as $scholarship)
                @include('central.partials.components.scholarship-card', ['scholarship' => $scholarship])
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="text-gray-400 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-green-500" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">No Private Scholarships</h3>
                    <p class="text-gray-500 dark:text-gray-500">There are currently no private scholarship programs available.</p>
                </div>
            @endforelse
            <div class="mt-8">
                {{ $scholarshipsPrivate->appends(['tab' => 'scholarships-private'])->links('vendor.pagination.custom') }}
            </div>
        </div>

        <!-- Government Scholarships -->
        <div x-show="tab === 'scholarships-government'">
            @forelse($scholarshipsGov as $scholarship)
                @include('central.partials.components.scholarship-card', ['scholarship' => $scholarship])
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="text-gray-400 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-orange-500" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">No Government Scholarships</h3>
                    <p class="text-gray-500 dark:text-gray-500">There are currently no government scholarship programs available.</p>
                </div>
            @endforelse
            <div class="mt-8">
                {{ $scholarshipsGov->appends(['tab' => 'scholarships-government'])->links('vendor.pagination.custom') }}
            </div>
        </div>
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
