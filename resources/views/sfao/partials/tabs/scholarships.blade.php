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
      <div x-show="tab === 'scholarships' || 
                   (tab === 'scholarships-internal' && '{{ $scholarship->scholarship_type }}' === 'internal') ||
                   (tab === 'scholarships-external' && '{{ $scholarship->scholarship_type }}' === 'external') ||
                   (tab === 'scholarships-public' && '{{ $scholarship->scholarship_type }}' === 'public') ||
                   (tab === 'scholarships-government' && '{{ $scholarship->scholarship_type }}' === 'government')"
           class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-bsu-redDark p-6 flex flex-col justify-between h-full min-h-[200px] hover:shadow-xl transition scholarship-card">
        
        <div>
          <div class="flex justify-between items-start mb-2">
            <h3 class="text-xl font-bold text-bsu-red dark:text-white">
              {{ $scholarship->scholarship_name }}
            </h3>
            @php
              $statusBadge = $scholarship->getStatusBadge();
            @endphp
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusBadge['color'] }}">
              {{ $statusBadge['text'] }}
            </span>
          </div>
          
          <p class="text-sm text-gray-600 dark:text-gray-300 mb-3 line-clamp-2">
            {{ \Illuminate\Support\Str::limit($scholarship->description, 100) }}
          </p>

          <!-- Priority and Renewal Badges -->
          <div class="flex flex-wrap gap-1 mb-3">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $scholarship->getScholarshipTypeBadgeColor() }}">
              {{ ucfirst($scholarship->scholarship_type) }}
            </span>
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $scholarship->getPriorityBadgeColor() }}">
              {{ ucfirst($scholarship->priority_level) }}
            </span>
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $scholarship->getGrantTypeBadgeColor() }}">
              {{ $scholarship->getGrantTypeDisplayName() }}
            </span>
            @if($scholarship->renewal_allowed)
              <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                🔄 Renewable
              </span>
            @endif
          </div>

          <!-- Key Information -->
          <div class="space-y-1 text-xs text-gray-600 dark:text-gray-400">
            <div class="flex justify-between">
              <span>Deadline:</span>
              <span class="font-semibold {{ $scholarship->getDaysUntilDeadline() <= 7 ? 'text-red-600' : '' }}">
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
              <div class="flex justify-between">
                <span>Opens:</span>
                <span class="{{ now()->gte($scholarship->application_start_date) ? 'text-green-600 font-semibold' : 'text-gray-600' }}">
                  {{ $scholarship->application_start_date?->format('M d, Y') }}
                </span>
              </div> 
            @endif
            
            @if($scholarship->grant_amount)
              <div class="flex justify-between">
                <span>Amount:</span>
                <span class="font-semibold text-green-600">₱{{ number_format((float) $scholarship->grant_amount, 0) }}</span>
              </div>
            @endif
            
            <div class="flex justify-between">
              <span>Status:</span>
              <span class="font-semibold {{ $scholarship->isAcceptingApplications() ? 'text-green-600' : 'text-red-600' }}">
                {{ $scholarship->isAcceptingApplications() ? 'Open' : 'Closed' }}
              </span>
            </div>
          </div>
        </div>

        <div class="mt-auto">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
              Applications: {{ $scholarship->applications_count }}
            </span>
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
              Slots: {{ $scholarship->slots_available ?? '∞' }}
            </span>
          </div>
          
          <!-- Progress Bar -->
          @if($scholarship->slots_available)
            <div class="mb-2">
              <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="bg-bsu-red h-2 rounded-full transition-all duration-300 progress-bar" 
                     data-width="{{ $scholarship->fill_percentage }}"></div>
              </div>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                {{ number_format((float) $scholarship->fill_percentage, 1) }}% filled
              </p>
            </div>
          @endif

          <!-- Days Remaining -->
          <div class="text-center">
            <span class="text-xs text-gray-500 dark:text-gray-400">
              @if($scholarship->getDaysUntilDeadline() > 0)
                {{ $scholarship->getDaysUntilDeadline() }} days left
              @else
                Deadline passed
              @endif
            </span>
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

  // Handle scholarship card clicks and progress bars
  document.addEventListener('DOMContentLoaded', function() {
    // Set progress bar widths
    const progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(bar => {
      const width = bar.getAttribute('data-width');
      bar.style.width = width + '%';
    });

  });
</script>
