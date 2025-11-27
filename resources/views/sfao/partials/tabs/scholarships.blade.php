@php
  // Calculate percentages for progress bars based on campus-specific applications
  $scholarships->each(function($scholarship) {
    if($scholarship->slots_available && $scholarship->slots_available > 0) {
      $scholarship->fill_percentage = min(($scholarship->applications_count / $scholarship->slots_available) * 100, 100);
    } else {
      $scholarship->fill_percentage = 0;
    }
  });
@endphp

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
          <span x-show="tab === 'scholarships'">View all available scholarship programs</span>
          <span x-show="tab === 'scholarships-private'">Private scholarship programs</span>
          <span x-show="tab === 'scholarships-government'">Government scholarship programs</span>
        </p>
      </div>
    </div>
  </div>

  <!-- Campus Information -->
  <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
    <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-2">
      Managing Campus: {{ $sfaoCampus->name }}
    </h3>
    <p class="text-sm text-blue-600 dark:text-blue-300">
      @if($sfaoCampus->extensionCampuses->count() > 0)
        Including extension campuses: 
        {{ $sfaoCampus->extensionCampuses->pluck('name')->join(', ') }}
      @else
        No extension campuses under this constituent campus.
      @endif
    </p>
  </div>

  <!-- Sorting Controls -->
  @php
    $baseUrl = url('/sfao');
  @endphp
  <x-sorting-controls 
    :currentSort="request('sort_by', 'created_at')" 
    :currentOrder="request('sort_order', 'desc')"
    :baseUrl="$baseUrl"
    role="sfao"
  />

  <!-- Scholarships List -->
  <div>
    @forelse($scholarships as $scholarship)
      <div x-show="tab === 'scholarships' || 
                   (tab === 'scholarships-private' && '{{ $scholarship->scholarship_type }}' === 'private') ||
                   (tab === 'scholarships-government' && '{{ $scholarship->scholarship_type }}' === 'government')"
           x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="opacity-0 transform scale-95"
           x-transition:enter-end="opacity-100 transform scale-100">
           
           @include('central.partials.components.scholarship-card', [
              'scholarship' => $scholarship,
              'role' => 'sfao',
              'fillPercentage' => $scholarship->fill_percentage
           ])
      </div>
    @empty
      <div class="col-span-full text-center py-12">
        <div class="text-gray-400 mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path d="M12 14l9-5-9-5-9 5 9 5z" />
            <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
          </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">No Scholarships Available</h3>
        <p class="text-gray-500 dark:text-gray-500">There are currently no scholarships to display.</p>
      </div>
    @endforelse
  </div>

  <!-- Pagination Links -->
  <div class="mt-8">
      {{ $scholarships->links('vendor.pagination.custom') }}
  </div>

  <!-- Empty State for Filtered Types -->
  <div x-show="tab === 'scholarships-private' && !hasVisibleScholarships('private')" 
       class="col-span-full text-center py-12">
      <div class="text-green-500 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd" />
        </svg>
      </div>
      <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">No Private Scholarships</h3>
      <p class="text-gray-500 dark:text-gray-500">There are currently no private scholarship programs available.</p>
  </div>

  <div x-show="tab === 'scholarships-government' && !hasVisibleScholarships('government')" 
       class="col-span-full text-center py-12">
      <div class="text-orange-500 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd" />
        </svg>
      </div>
      <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">No Government Scholarships</h3>
      <p class="text-gray-500 dark:text-gray-500">There are currently no government scholarship programs available.</p>
  </div>

</div>

<script>
  // Function to check if there are visible scholarships of a specific type
  function hasVisibleScholarships(type) {
    const scholarships = document.querySelectorAll('[x-show*="' + type + '"]');
    return scholarships.length > 0;
  }
</script>
