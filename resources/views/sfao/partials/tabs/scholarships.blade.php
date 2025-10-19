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
          <span x-show="tab === 'scholarships'">View all available scholarship programs</span>
          <span x-show="tab === 'scholarships-internal'">Internal university scholarship programs</span>
          <span x-show="tab === 'scholarships-external'">External partner scholarship programs</span>
          <span x-show="tab === 'scholarships-public'">Public scholarship programs</span>
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

  <!-- Scholarships Grid -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($scholarships as $scholarship)
      <div x-data="{ open: false }" 
           x-show="tab === 'scholarships' || 
                   (tab === 'scholarships-internal' && '{{ $scholarship->scholarship_type }}' === 'internal') ||
                   (tab === 'scholarships-external' && '{{ $scholarship->scholarship_type }}' === 'external') ||
                   (tab === 'scholarships-public' && '{{ $scholarship->scholarship_type }}' === 'public') ||
                   (tab === 'scholarships-government' && '{{ $scholarship->scholarship_type }}' === 'government')"
           class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-bsu-redDark p-6 hover:shadow-xl transition scholarship-card relative overflow-hidden"
           @if($scholarship->background_image)
           style="background-image: linear-gradient(rgba(255,255,255,0.7), rgba(255,255,255,0.7)), url('{{ $scholarship->getBackgroundImageUrl() }}'); background-size: cover; background-position: center;"
           @endif>
        
        <!-- Scholarship Content -->
        <div class="flex flex-col h-full">
          <!-- Header -->
          <div class="flex justify-between items-start mb-3">
            <div class="flex-1">
              <h3 class="text-xl font-bold text-bsu-red dark:text-white">
                {{ $scholarship->scholarship_name }}
              </h3>
              <div class="flex items-center gap-2 mt-2">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $scholarship->getScholarshipTypeBadgeColor() }}">
                  {{ ucfirst($scholarship->scholarship_type) }}
                </span>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $scholarship->getPriorityBadgeColor() }}">
                  {{ ucfirst($scholarship->priority_level) }}
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
          </div>
        </div>
      </div>
    @empty
      <div class="col-span-full text-center py-12">
        <div class="text-gray-400 dark:text-gray-500 text-6xl mb-4">🎓</div>
        <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">No Scholarships Available</h3>
        <p class="text-gray-500 dark:text-gray-500">There are currently no scholarships to display.</p>
      </div>
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

</div>

<script>
  // Function to check if there are visible scholarships of a specific type
  function hasVisibleScholarships(type) {
    const scholarships = document.querySelectorAll('[x-show*="' + type + '"]');
    return scholarships.length > 0;
  }


</script>
