<div x-show="tab === 'applications'" x-cloak>
  <div class="max-w-5xl mx-auto p-6 bg-white dark:bg-gray-800 shadow-xl rounded-2xl border-t-4 border-bsu-red transition duration-300">
    <h2 class="text-3xl font-bold text-bsu-red mb-6 border-b-2 border-bsu-redDark pb-2">🎓 My Scholarship Applications</h2>

    @if ($applications->isEmpty())
      <div class="text-center py-12">
        <p class="text-gray-600 dark:text-gray-300 text-lg">You have not applied to any scholarships yet.</p>
      </div>
    @else
      <ul class="divide-y divide-gray-200 dark:divide-gray-700">
        @foreach ($applications as $scholarship)
          <li class="py-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
              <div>
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white">
                  {{ $scholarship->scholarship_name }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                  {{ $scholarship->description }}
                </p>
              </div>
              <div class="mt-4 md:mt-0">
                <span class="inline-block px-4 py-1 text-sm font-medium rounded-full
                  @switch($scholarship->pivot->status)
                    @case('Approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @break
                    @case('Pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @break
                    @case('Rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @break
                    @default bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                  @endswitch
                ">
                  {{ $scholarship->pivot->status }}
                </span>
              </div>
            </div>
          </li>
        @endforeach
      </ul>
    @endif
  </div>
</div>
