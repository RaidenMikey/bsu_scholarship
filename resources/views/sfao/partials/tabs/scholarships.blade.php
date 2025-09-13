@php
  use App\Models\Scholarship;
  // eager load applications count
  $scholarships = Scholarship::withCount('applications')->get();
  
  // Calculate percentages for progress bars
  $scholarships->each(function($scholarship) {
    if($scholarship->slots_available && $scholarship->slots_available > 0) {
      $scholarship->fill_percentage = min(($scholarship->applications_count / $scholarship->slots_available) * 100, 100);
    } else {
      $scholarship->fill_percentage = 0;
    }
  });
@endphp

<div x-show="tab === 'scholarships'" x-transition x-cloak>
  <!-- Scholarships Grid -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($scholarships as $scholarship)
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-bsu-redDark p-6 flex flex-col justify-between h-full min-h-[200px] hover:shadow-xl transition cursor-pointer scholarship-card"
           data-scholarship-id="{{ $scholarship->id }}">
        
        <div>
          <h3 class="text-xl font-bold text-bsu-red dark:text-white mb-4">
            {{ $scholarship->scholarship_name }}
          </h3>
        </div>

        <div class="mt-auto">
          <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
              Slots: {{ $scholarship->slots_available ?? 'Unlimited' }}
            </span>
            <span class="text-sm font-medium text-bsu-red dark:text-red-400">
              {{ $scholarship->applications_count }} / {{ $scholarship->slots_available ?? '∞' }} Applied
            </span>
          </div>
          
          <!-- Progress Bar -->
          @if($scholarship->slots_available)
            <div class="mt-3">
              <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="bg-bsu-red h-2 rounded-full transition-all duration-300 progress-bar" 
                     data-width="{{ $scholarship->fill_percentage }}"></div>
              </div>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                {{ number_format($scholarship->fill_percentage, 1) }}% filled
              </p>
            </div>
          @endif
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
