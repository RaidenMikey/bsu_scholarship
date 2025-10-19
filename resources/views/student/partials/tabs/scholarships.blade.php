<div x-data="{
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
    @php
      $currentType = $scholarshipType ?? 'all';
      $typeLabels = [
        'all' => ['title' => '🎓 All Scholarships', 'description' => 'All available scholarship programs'],
        'internal' => ['title' => '🏫 Internal Scholarships', 'description' => 'Internal university scholarship programs'],
        'external' => ['title' => '🌐 External Scholarships', 'description' => 'External partner scholarship programs'],
        'public' => ['title' => '🏛️ Public Scholarships', 'description' => 'Public scholarship programs'],
        'government' => ['title' => '🏛️ Government Scholarships', 'description' => 'Government scholarship programs']
      ];
      $currentLabel = $typeLabels[$currentType] ?? $typeLabels['all'];
    @endphp
    
    <!-- Header with Type Filter -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $currentLabel['title'] }}
                </h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    {{ $currentLabel['description'] }}
                </p>
            </div>
        </div>
    </div>

    @if ($scholarships->count())
      <!-- Sorting Controls -->
      <x-sorting-controls 
        :currentSort="request('sort_by', 'submission_deadline')" 
        :currentOrder="request('sort_order', 'asc')"
        :baseUrl="route('student.scholarships')"
        role="student"
      />

      <!-- Scholarships List -->
      <div class="space-y-8">
        <p class="text-gray-800 dark:text-gray-200 text-sm mb-4">
          Scholarships matched to your profile:
        </p>

          @foreach ($scholarships as $scholarship)
          <div x-data="{ open: false }" 
               class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-bsu-redDark p-6 hover:shadow-xl transition scholarship-card relative overflow-hidden
                      {{ $scholarship->applied ? 'opacity-75' : '' }}"
               @if($scholarship->background_image)
               style="background-image: linear-gradient(rgba(255,255,255,0.7), rgba(255,255,255,0.7)), url('{{ $scholarship->getBackgroundImageUrl() }}'); background-size: cover; background-position: center;"
               @endif>
        
        <!-- Scholarship Content -->
        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
          <!-- Main Content -->
          <div class="flex-1">
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
            </div>

            <!-- Description Preview -->
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-3 line-clamp-2">
              {{ \Illuminate\Support\Str::limit($scholarship->description, 150) }}
            </p>
          </div>

          <!-- Quick Info & Actions -->
          <div class="flex flex-col sm:flex-row lg:flex-col gap-4 lg:min-w-[200px]">
            <!-- Quick Stats -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-1 gap-3 text-sm">
              <div>
                <span class="text-gray-500 dark:text-gray-400">Deadline:</span>
                <div class="font-semibold {{ $scholarship->getDaysUntilDeadline() <= 7 ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                  {{ $scholarship->submission_deadline?->format('M d, Y') }}
                </div>
              </div>
              <div>
                <span class="text-gray-500 dark:text-gray-400">Amount:</span>
                <div class="font-semibold text-green-600">
                  @if($scholarship->grant_amount)
                    ₱{{ number_format((float) $scholarship->grant_amount, 0) }}
                  @else
                    TBD
                  @endif
                </div>
              </div>
              <div>
                <span class="text-gray-500 dark:text-gray-400">Status:</span>
                <div class="font-semibold {{ $scholarship->is_active ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                  {{ $scholarship->is_active ? 'Active' : 'Inactive' }}
                </div>
              </div>
            </div>

            <!-- Slots Information -->
            @if($scholarship->slots_available)
            @php
              $applicationsCount = $scholarship->getApplicationCount();
              $slotsAvailable = $scholarship->slots_available;
              $fillPercentage = min(100, ($applicationsCount / $slotsAvailable) * 100);
            @endphp
            <div class="mt-2">
              <div class="flex justify-between items-center mb-2">
                <span class="text-sm text-gray-600 dark:text-gray-400">Available Slots:</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">
                  {{ $applicationsCount }} / {{ $slotsAvailable }}
                </span>
              </div>
              <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="bg-bsu-redDark h-2 rounded-full transition-all duration-300" 
                     data-width="{{ $fillPercentage }}"
                     x-bind:style="'width: ' + $el.dataset.width + '%'"></div>
              </div>
              <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                {{ number_format($fillPercentage, 1) }}% filled
              </div>
            </div>
            @endif

            <!-- Application Status -->
            @if($scholarship->applied)
              <div class="flex items-center gap-2 px-4 py-2 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-lg">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-sm font-medium">Applied</span>
              </div>
            @endif

            <!-- Dropdown Toggle Button -->
            <button @click="open = !open" 
                    class="flex items-center justify-center gap-2 px-4 py-2 bg-bsu-redDark hover:bg-bsu-red text-white rounded-lg transition-colors">
                <span class="text-sm font-medium">View Details</span>
                <svg class="w-4 h-4 transition-transform" 
                     :class="{ 'rotate-180': open }" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <!-- Apply / Applied / Unapply Buttons -->
            <div>
                @if($scholarship->applied)
                    <!-- Unapply Button (opens modal) -->
                    <button type="button" 
                            onclick="openUnapplyModal('{{ $scholarship->id }}')"
                            class="w-full px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg shadow transition-colors">
                        ❌ Unapply
                    </button>
                @else
                    <form method="GET" action="{{ route('student.upload-documents', ['scholarship_id' => $scholarship->id]) }}">
                        <button type="submit" 
                                class="w-full px-4 py-2 bg-bsu-red hover:bg-bsu-redDark text-white font-semibold rounded-lg shadow transition-colors">
                            🎓 Apply
                        </button>
                    </form>
                @endif
            </div>
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
            <!-- Additional Eligibility Information -->
            <div>
              <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Additional Information</h4>
              <div class="space-y-2">
                @if($scholarship->grant_type)
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600 dark:text-gray-400">Grant Type:</span>
                  <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $scholarship->grant_type)) }}</span>
                </div>
                @endif
                @if($scholarship->slots_available)
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600 dark:text-gray-400">Total Slots:</span>
                  <span class="font-medium">{{ $scholarship->slots_available }}</span>
                </div>
                @endif
                @if($scholarship->application_start_date)
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600 dark:text-gray-400">Application Opens:</span>
                  <span class="font-medium">{{ $scholarship->application_start_date?->format('M d, Y') }}</span>
                </div>
                @endif
              </div>
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
                  <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Amount:</span>
                    <span class="font-medium text-green-600">
                      @if($scholarship->grant_amount)
                        ₱{{ number_format((float) $scholarship->grant_amount, 0) }}
                      @else
                        TBD
                      @endif
                    </span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Applications:</span>
                    <span class="font-medium">{{ $scholarship->getApplicationCount() }}</span>
                  </div>
                </div>
              </div>
            </div>

              <!-- Matching Criteria Display -->
              @php
                $matchingCriteria = $scholarship->getMatchingCriteria($form);
              @endphp
              
              @if(count($matchingCriteria) > 0)
              <div>
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Your Profile Match</h4>
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

            </div>
          </div>

          @endforeach
      </div>
    @else
      <div class="bg-blue-50 border-l-4 border-blue-400 p-5 rounded-lg shadow-sm">
        <p class="text-blue-800 font-medium">
          No {{ strtolower($currentLabel['title']) }} currently match your profile criteria.
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

      // Progress bars are now handled by Alpine.js x-bind:style
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