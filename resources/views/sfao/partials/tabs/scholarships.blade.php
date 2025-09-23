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

<div x-show="tab === 'scholarships'" x-transition x-cloak>
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
  <x-sorting-controls 
    :currentSort="request('sort_by', 'created_at')" 
    :currentOrder="request('sort_order', 'desc')"
    :baseUrl="route('sfao.dashboard')"
    role="sfao"
  />

  <!-- Scholarships Grid -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($scholarships as $scholarship)
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-bsu-redDark p-6 flex flex-col justify-between h-full min-h-[200px] hover:shadow-xl transition cursor-pointer scholarship-card"
           data-scholarship-id="{{ $scholarship->id }}">
        
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
            {{ Str::limit($scholarship->description, 100) }}
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
</div>

<script>
  // Handle scholarship card clicks and progress bars
  document.addEventListener('DOMContentLoaded', function() {
    // Set progress bar widths
    const progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(bar => {
      const width = bar.getAttribute('data-width');
      bar.style.width = width + '%';
    });

    // Handle scholarship card clicks
    const scholarshipCards = document.querySelectorAll('.scholarship-card');
    scholarshipCards.forEach(card => {
      card.addEventListener('click', function() {
        const scholarshipId = this.getAttribute('data-scholarship-id');
        window.location.href = `/sfao/scholarships/${scholarshipId}`;
      });
    });
  });
</script>
