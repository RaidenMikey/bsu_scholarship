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
          Scholarships matched to your GWA ({{ $gwa }}):
        </p>

        <div class="space-y-5">
          @foreach ($scholarships as $scholarship)
            <div class="relative rounded-lg shadow border-2 p-5 transition
                        {{ $scholarship->applied 
                          ? 'bg-gray-200 dark:bg-gray-800 border-gray-400' 
                          : 'bg-white dark:bg-gray-900 border-bsu-red hover:bg-bsu-light dark:hover:bg-gray-800' }}">

              <h3 class="text-lg font-bold {{ $scholarship->applied ? 'text-gray-700 dark:text-white' : 'text-bsu-red dark:text-white' }} mb-2">
                {{ $scholarship->scholarship_name }}
              </h3>

              <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                {{ $scholarship->description }}
              </p>

              <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1 mb-12">
                <p><strong>Required GWA:</strong> {{ $scholarship->minimum_gwa }}</p>
                <p><strong>Deadline:</strong> {{ \Carbon\Carbon::parse($scholarship->deadline)->format('M d, Y') }}</p>
                <p><strong>Slots:</strong> {{ $scholarship->slots_available ?? 0 }}</p>

                @if($scholarship->grant_amount)
                  <p><strong>Grant Amount:</strong> ₱{{ number_format($scholarship->grant_amount, 2) }}</p>
                @endif

                <p><strong>Renewal Allowed:</strong> {{ $scholarship->renewal_allowed ? 'Yes' : 'No' }}</p>
              </div>

              <!-- Apply / Applied / Unapply Buttons -->
              <div class="absolute bottom-4 left-4">
                  @if($scholarship->applied)
                      <!-- Unapply Button (opens modal) -->
                      <button type="button" 
                              onclick="openUnapplyModal('{{ $scholarship->id }}')"
                              class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg shadow">
                          ❌ Unapply
                      </button>
                  @else
                      <form method="GET" action="{{ route('student.upload-documents', ['scholarship_id' => $scholarship->id]) }}">
                          <button type="submit" 
                                  class="px-4 py-2 bg-bsu-red hover:bg-bsu-redDark text-white font-semibold rounded-lg shadow">
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
          No scholarships currently match your GWA ({{ $gwa }}).
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