<div x-show="tab === 'scholarships'" 
     x-data="{
       openWarning: false,
       appliedScholarship: @js($scholarships->firstWhere('applied', true)),
       selected: null,
       open(scholarship) {
         if (this.appliedScholarship && !scholarship.applied) {
           this.selected = scholarship;
           this.openWarning = true;
           return;
         }
       }
     }">

  @if ($hasApplication)
    <h1 class="text-3xl font-bold text-bsu-red dark:text-bsu-red border-b-2 border-bsu-red pb-2 mb-6">
      Available Scholarships
    </h1>

    @if ($scholarships->count())
      <div class="bg-bsu-light dark:bg-gray-800 p-5 rounded-lg shadow-sm">
        <p class="text-gray-800 dark:text-gray-200 text-sm mb-4">
          Scholarships matched to your profile:
        </p>

        <div class="space-y-5">
          @foreach ($scholarships as $scholarship)
            <div class="relative rounded-lg shadow border-2 p-5 transition flex flex-col
                        {{ $scholarship->applied 
                          ? 'bg-gray-200 dark:bg-gray-800 border-gray-400' 
                          : 'bg-white dark:bg-gray-900 border-bsu-red hover:bg-bsu-light dark:hover:bg-gray-800' }}">

              <h3 class="text-lg font-bold {{ $scholarship->applied ? 'text-gray-700 dark:text-white' : 'text-bsu-red dark:text-white' }} mb-2">
                {{ $scholarship->scholarship_name }}
              </h3>

              <p class="text-sm text-gray-700 dark:text-gray-300 mb-3">
                {{ $scholarship->description }}
              </p>

              <!-- Matching Criteria Display -->
              @php
                $matchingCriteria = $scholarship->getMatchingCriteria($form);
              @endphp
              
              @if(count($matchingCriteria) > 0)
                <div class="mb-4">
                  <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">Matching Criteria:</h4>
                  <div class="flex flex-wrap gap-2">
                    @foreach($matchingCriteria as $criteria)
                      <div class="flex items-center space-x-1 px-3 py-1 rounded-full text-xs font-medium
                        {{ $criteria['matches'] 
                          ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' 
                          : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                        <span class="font-semibold">{{ $criteria['display_name'] }}:</span>
                        <span>{{ $criteria['student_value'] }}</span>
                        @if($criteria['matches'])
                          <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                          </svg>
                        @else
                          <svg class="w-3 h-3 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                          </svg>
                        @endif
                      </div>
                    @endforeach
                  </div>
                </div>
              @endif

              <!-- Scholarship Status and Priority -->
              <div class="mb-4 flex flex-wrap gap-2">
                @php
                  $statusBadge = $scholarship->getStatusBadge();
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusBadge['color'] }}">
                  {{ $statusBadge['text'] }}
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $scholarship->getScholarshipTypeBadgeColor() }}">
                  {{ ucfirst($scholarship->scholarship_type) }}
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $scholarship->getPriorityBadgeColor() }}">
                  {{ ucfirst($scholarship->priority_level) }} Priority
                </span>
                @if($scholarship->renewal_allowed)
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                    🔄 Renewable
                  </span>
                @endif
              </div>

              <!-- Scholarship Details -->
              <div class="text-sm text-gray-600 dark:text-gray-400 space-y-2 mb-4">
                <div class="flex justify-between items-center">
                  <span><strong>Submission Deadline:</strong></span>
                  <span class="font-semibold {{ $scholarship->getDaysUntilDeadline() <= 7 ? 'text-red-600' : 'text-gray-600' }}">
                    {{ $scholarship->submission_deadline?->format('M d, Y') }}
                    @if($scholarship->getDaysUntilDeadline() > 0)
                      <span class="text-xs">({{ $scholarship->getDaysUntilDeadline() }} days left)</span>
                    @elseif($scholarship->getDaysUntilDeadline() == 0)
                      <span class="text-xs text-red-600">(Today!)</span>
                    @else
                      <span class="text-xs text-red-600">(Expired)</span>
                    @endif
                  </span>
                </div>

                @if($scholarship->application_start_date)
                  <div class="flex justify-between items-center">
                    <span><strong>Application Opens:</strong></span>
                    <span class="{{ now()->gte($scholarship->application_start_date) ? 'text-green-600 font-semibold' : 'text-gray-600' }}">
                      {{ $scholarship->application_start_date?->format('M d, Y') }}
                      @if(now()->lt($scholarship->application_start_date))
                        <span class="text-xs">({{ now()->diffInDays($scholarship->application_start_date) }} days to go)</span>
                      @else
                        <span class="text-xs text-green-600">(Open)</span>
                      @endif
                    </span>
                  </div>
                @endif

                <div class="flex justify-between items-center">
                  <span><strong>Available Slots:</strong></span>
                  <span class="{{ $scholarship->isFull() ? 'text-red-600' : 'text-gray-600' }}">
                    @if($scholarship->slots_available === null)
                      <span class="text-green-600 font-semibold">Unlimited</span>
                    @else
                      {{ $scholarship->slots_available - $scholarship->getApplicationCount() }} / {{ $scholarship->slots_available }}
                      @if($scholarship->isFull())
                        <span class="text-xs text-red-600">(Full)</span>
                      @endif
                    @endif
                  </span>
                </div>

                @if($scholarship->grant_amount)
                  <div class="flex justify-between items-center">
                    <span><strong>Grant Amount:</strong></span>
                    <span class="font-semibold text-green-600">₱{{ number_format((float) $scholarship->grant_amount, 2) }}</span>
                  </div>
                @endif

                <!-- Application Statistics -->
                <div class="flex justify-between items-center">
                  <span><strong>Total Applications:</strong></span>
                  <span class="font-semibold">{{ $scholarship->getApplicationCount() }}</span>
                </div>

                @if($scholarship->eligibility_notes)
                  <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <p class="text-xs text-blue-800 dark:text-blue-200">
                      <strong>Additional Notes:</strong> {{ $scholarship->eligibility_notes }}
                    </p>
                  </div>
                @endif
              </div>

              <!-- Progress Bar for Slots -->
              @if($scholarship->slots_available)
                <div class="mb-4">
                  <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                    <span>Applications</span>
                    <span>{{ $scholarship->getApplicationCount() }} / {{ $scholarship->slots_available }}</span>
                  </div>
                  <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-bsu-red h-2 rounded-full transition-all duration-300 progress-bar" 
                         data-width="{{ $scholarship->getFillPercentage() }}"></div>
                  </div>
                </div>
              @endif

              <!-- Apply / Applied / Unapply Buttons -->
              <div class="mt-auto pt-4 flex justify-start">
                  @if($scholarship->applied)
                      <!-- Unapply Button (opens modal) -->
                      <button type="button" 
                              onclick="openUnapplyModal('{{ $scholarship->id }}')"
                              class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg shadow transition-colors">
                          ❌ Unapply
                      </button>
                  @else
                      <form method="GET" action="{{ route('student.upload-documents', ['scholarship_id' => $scholarship->id]) }}">
                          <button type="submit" 
                                  class="px-4 py-2 bg-bsu-red hover:bg-bsu-redDark text-white font-semibold rounded-lg shadow transition-colors">
                              🎓 Apply
                          </button>
                      </form>
                  @endif
              </div>

            </div>
          @endforeach
        </div>
      </div>
    @else
      <div class="bg-blue-50 border-l-4 border-blue-400 p-5 rounded-lg shadow-sm">
        <p class="text-blue-800 font-medium">
          No scholarships currently match your profile criteria.
        </p>
        <p class="mt-1 text-blue-700 text-sm">
          Keep improving to unlock more opportunities!
        </p>
      </div>
    @endif
  @else
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-5 rounded-lg shadow-sm flex items-start space-x-4">
      <svg class="w-6 h-6 text-yellow-500 flex-shrink-0 mt-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
      </svg>
      <div>
        <p class="text-yellow-800 font-medium">
          You have not yet submitted an application.
        </p>
        <p class="mt-1 text-yellow-700 text-sm">
          Please complete your application to view available scholarships.
          <a href="{{ route('student.upload-documents', ['scholarship_id' => 1]) }}" 
             class="underline text-bsu-red font-semibold hover:text-bsu-redDark">
              Click here to upload
          </a>
        </p>
      </div>
    </div>
  @endif

  <!-- Unapply Modal -->
  <div id="unapplyModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-lg shadow-lg p-6 w-96">
          <h2 class="text-lg font-semibold mb-4 text-gray-800">Confirm Unapply</h2>
          <p class="mb-6 text-gray-600">Are you sure you want to unapply from this scholarship?</p>

          <form method="POST" action="{{ route('student.unapply') }}">
              @csrf
              <input type="hidden" name="scholarship_id" id="unapplyScholarshipId">
              
              <div class="flex justify-end gap-3">
                  <button type="button" 
                          onclick="closeUnapplyModal()" 
                          class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg">
                      Cancel
                  </button>
                  <button type="submit" 
                          class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                      Yes, Unapply
                  </button>
              </div>
          </form>
      </div>
  </div>

  <!-- JS for Modal -->
  <script>
      function openUnapplyModal(scholarshipId) {
          document.getElementById('unapplyScholarshipId').value = scholarshipId;
          document.getElementById('unapplyModal').classList.remove('hidden');
      }

      function closeUnapplyModal() {
          document.getElementById('unapplyModal').classList.add('hidden');
      }

      // Set progress bar widths
      document.addEventListener('DOMContentLoaded', function() {
          const progressBars = document.querySelectorAll('.progress-bar');
          progressBars.forEach(bar => {
              const width = bar.getAttribute('data-width');
              bar.style.width = width + '%';
          });
      });
  </script>

  <!-- Warning Modal -->
  <div x-show="openWarning" x-cloak
       class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
       @click.self="openWarning = false">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-md space-y-4 text-center">
      <h2 class="text-lg font-bold text-bsu-red dark:text-white">Application Limit Reached</h2>
      <p class="text-gray-700 dark:text-gray-300">
        You are currently applying for 
        <strong x-text="appliedScholarship?.scholarship_name"></strong>.
        You can only apply for one scholarship at a time. Please wait for your current application to be validated before applying to another.
      </p>
      <button @click="openWarning = false"
              class="mt-4 px-4 py-2 bg-bsu-red hover:bg-bsu-redDark text-white font-semibold rounded-lg shadow">
        OK
      </button>
    </div>
  </div>
</div>