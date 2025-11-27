<div x-data="{
       openWarning: false,
       appliedScholarship: @js($scholarships->firstWhere('applied', true)),
       selected: null,
       hasActiveApplication: false,
       init() {
         this.hasActiveApplication = !!this.appliedScholarship && 
                                     this.appliedScholarship.status !== 'approved' && 
                                     this.appliedScholarship.status !== 'rejected';
       }
     }"
     @show-warning.window="
        openWarning = true;
     ">

  @if ($hasApplication)
    <!-- Header with Type Filter -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    <span x-show="subTab === 'all'" class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path d="M12 14l9-5-9-5-9 5 9 5z" />
                          <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                        </svg>
                        All Scholarships
                    </span>
                    <span x-show="subTab === 'private'" class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Private Scholarships
                    </span>
                    <span x-show="subTab === 'government'" class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                        </svg>
                        Government Scholarships
                    </span>
                </h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    <span x-show="subTab === 'all'">All available scholarship programs</span>
                    <span x-show="subTab === 'private'">Private scholarship programs</span>
                    <span x-show="subTab === 'government'">Government scholarship programs</span>
                </p>
            </div>
        </div>
    </div>

    @if ($scholarships->count())
      <!-- Sorting Controls -->
      <x-sorting-controls 
        :currentSort="request('sort_by', 'name')" 
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
            <div x-show="subTab === 'all' || 
                        (subTab === 'private' && '{{ $scholarship->scholarship_type }}' === 'private') ||
                        (subTab === 'government' && '{{ $scholarship->scholarship_type }}' === 'government')"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100">
                 
                 @php
                    $applicationsCount = $scholarship->getApplicationCount();
                    $slotsAvailable = $scholarship->slots_available;
                    $fillPercentage = $slotsAvailable > 0 ? min(100, ($applicationsCount / $slotsAvailable) * 100) : 0;
                    
                    // Check if student has an active application (not approved/rejected)
                    // This logic is also in x-data init, but we pass it down to component
                    $hasActiveApplication = $scholarships->firstWhere('applied', true) && 
                                            !in_array($scholarships->firstWhere('applied', true)->status, ['approved', 'rejected']);
                 @endphp

                 @include('central.partials.components.scholarship-card', [
                    'scholarship' => $scholarship,
                    'role' => 'student',
                    'hasActiveApplication' => $hasActiveApplication,
                    'fillPercentage' => $fillPercentage
                 ])
            </div>
          @endforeach
      </div>

      <!-- Pagination Links -->
      <div class="mt-8">
          {{ $scholarships->appends(request()->query())->links('vendor.pagination.custom') }}
      </div>

    @else
      <div class="text-center py-12">
        <div class="text-gray-400 mb-4">
          <svg class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">No Scholarships Found</h3>
        <p class="text-gray-500 dark:text-gray-500">There are no scholarships matching your criteria at the moment.</p>
      </div>
    @endif

  @else
    <!-- No Application Profile State -->
    <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-dashed border-gray-300 dark:border-gray-700">
      <div class="max-w-md mx-auto">
        <div class="text-yellow-500 mb-6">
          <svg class="h-20 w-20 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Application Profile Required</h3>
        <p class="text-gray-600 dark:text-gray-400 mb-6">
          You need to complete your initial application profile and upload required documents before you can view and apply for scholarships.
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