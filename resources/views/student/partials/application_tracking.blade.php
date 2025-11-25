<div x-cloak class="px-4 py-6">
    <div class="mb-6">
        <h2 class="flex items-center gap-2 text-2xl font-bold text-bsu-red dark:text-red-400 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Application Tracking
        </h2>
        <p class="text-gray-600 dark:text-gray-300">Monitor the progress of your scholarship applications</p>
    </div>

    @if($applicationTracking->isEmpty())
        <div class="text-center py-12">
            <div class="text-gray-400 dark:text-gray-500 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">No Applications Found</h3>
            <p class="text-gray-500 dark:text-gray-500">You haven't applied to any scholarships yet.</p>
            <a href="{{ route('student.scholarships') }}" 
               class="inline-block mt-4 px-6 py-2 bg-bsu-red text-white rounded-lg hover:bg-red-700 transition">
                Browse Scholarships
            </a>
        </div>
    @else
        <!-- Application Progress Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Applications</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $applicationTracking->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $applicationTracking->where('status', 'pending')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Approved</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $applicationTracking->where('status', 'approved')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Rejected</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $applicationTracking->where('status', 'rejected')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Application Progress Timeline -->
        <div class="space-y-6">
            @foreach($applicationTracking as $application)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                    <div class="p-6">
                        <!-- Application Header -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="h-12 w-12 rounded-full bg-bsu-red flex items-center justify-center">
                                        <span class="text-lg font-bold text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                              <path d="M12 14l9-5-9-5-9 5 9 5z" />
                                              <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $application->scholarship->scholarship_name }}
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Applied on {{ $application->created_at?->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                    {{ $application->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                       ($application->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                       'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200') }}">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </div>
                        </div>

                        <!-- Progress Timeline -->
                        <div class="relative">
                            <div class="flex items-center justify-between">
                                <!-- Step 1: Application Submitted -->
                                <div class="flex flex-col items-center">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-full 
                                        {{ $application->created_at ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-500' }}">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="mt-2 text-center">
                                        <p class="text-xs font-medium text-gray-900 dark:text-white">Application</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Submitted</p>
                                        @if($application->created_at)
                                            <p class="text-xs text-green-600 dark:text-green-400">{{ $application->created_at?->format('M d') }}</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Connector Line -->
                                <div class="flex-1 h-0.5 mx-4 
                                    {{ $application->status !== 'pending' ? 'bg-green-500' : 'bg-gray-300' }}"></div>

                                <!-- Step 2: SFAO Review -->
                                <div class="flex flex-col items-center">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-full 
                                        {{ $application->status === 'approved' || $application->status === 'rejected' ? 'bg-green-500 text-white' : 
                                           ($application->status === 'pending' ? 'bg-yellow-500 text-white' : 'bg-gray-300 text-gray-500') }}">
                                        @if($application->status === 'approved' || $application->status === 'rejected')
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        @elseif($application->status === 'pending')
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="mt-2 text-center">
                                        <p class="text-xs font-medium text-gray-900 dark:text-white">SFAO</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Review</p>
                                        @if($application->status === 'approved' || $application->status === 'rejected')
                                            <p class="text-xs text-green-600 dark:text-green-400">Completed</p>
                                            @if($application->updated_at && $application->updated_at != $application->created_at)
                                                <p class="text-xs text-green-600 dark:text-green-400">{{ $application->updated_at->format('M d') }}</p>
                                            @endif
                                        @elseif($application->status === 'pending')
                                            <p class="text-xs text-yellow-600 dark:text-yellow-400">In Progress</p>
                                        @else
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Pending</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Connector Line -->
                                <div class="flex-1 h-0.5 mx-4 
                                    {{ $application->status === 'approved' ? 'bg-green-500' : 'bg-gray-300' }}"></div>

                                <!-- Step 3: Central Review -->
                                <div class="flex flex-col items-center">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-full 
                                        {{ $application->scholar_status === 'selected' ? 'bg-green-500 text-white' : 
                                           ($application->status === 'rejected' ? 'bg-red-500 text-white' : 'bg-gray-300 text-gray-500') }}">
                                        @if($application->scholar_status === 'selected')
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        @elseif($application->status === 'rejected')
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="mt-2 text-center">
                                        <p class="text-xs font-medium text-gray-900 dark:text-white">Central</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Review</p>
                                        @if($application->scholar_status === 'selected')
                                            <p class="text-xs text-green-600 dark:text-green-400">Completed</p>
                                            @if($application->scholar_selected_at)
                                                <p class="text-xs text-green-600 dark:text-green-400">{{ $application->scholar_selected_at->format('M d') }}</p>
                                            @endif
                                        @elseif($application->status === 'rejected')
                                            <p class="text-xs text-red-600 dark:text-red-400">Completed</p>
                                        @else
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Pending</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Connector Line -->
                                <div class="flex-1 h-0.5 mx-4 
                                    {{ $application->scholar_status === 'selected' || $application->status === 'rejected' ? 'bg-green-500' : 'bg-gray-300' }}"></div>

                                <!-- Step 4: Final Decision -->
                                <div class="flex flex-col items-center">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-full 
                                        {{ $application->scholar_status === 'selected' || $application->status === 'rejected' ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-500' }}">
                                        @if($application->scholar_status === 'selected')
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        @elseif($application->status === 'rejected')
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="mt-2 text-center">
                                        <p class="text-xs font-medium text-gray-900 dark:text-white">Final</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Decision</p>
                                        @if($application->scholar_status === 'selected')
                                            <p class="text-xs text-green-600 dark:text-green-400">Selected as Scholar</p>
                                        @elseif($application->status === 'rejected')
                                            <p class="text-xs text-red-600 dark:text-red-400">Not Selected</p>
                                        @else
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Pending</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Evaluation Status -->
                        @if($application->status === 'pending' || $application->status === 'approved' || $application->status === 'rejected' || $application->scholar_status === 'selected')
                        <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border-l-4 border-blue-400">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-800 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h4 class="text-sm font-medium text-blue-900 dark:text-blue-100">Evaluation Status</h4>
                                    <div class="mt-2 text-sm text-blue-800 dark:text-blue-200">
                                        @if($application->scholar_status === 'selected')
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span class="font-medium flex items-center gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                      <path d="M12 14l9-5-9-5-9 5 9 5z" />
                                                      <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                                                    </svg>
                                                    Selected as Scholar
                                                </span>
                                            </div>
                                            <p class="mt-1 text-xs text-blue-700 dark:text-blue-300">
                                                Selected on {{ $application->scholar_selected_at?->format('M d, Y \a\t h:i A') }}
                                            </p>
                                        @elseif($application->status === 'rejected')
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span class="font-medium flex items-center gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                    </svg>
                                                    Not Selected
                                                </span>
                                            </div>
                                            <p class="mt-1 text-xs text-blue-700 dark:text-blue-300">
                                                Final decision made on {{ $application->updated_at?->format('M d, Y \a\t h:i A') }}
                                            </p>
                                        @elseif($application->status === 'approved')
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span class="font-medium flex items-center gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                    SFAO Approved - Awaiting Central Review
                                                </span>
                                            </div>
                                            <p class="mt-1 text-xs text-blue-700 dark:text-blue-300">
                                                SFAO approved on {{ $application->updated_at?->format('M d, Y \a\t h:i A') }}. Central is reviewing for final selection.
                                            </p>
                                        @else
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-yellow-600 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="font-medium flex items-center gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Under SFAO Review
                                                </span>
                                            </div>
                                            <p class="mt-1 text-xs text-blue-700 dark:text-blue-300">
                                                SFAO is currently evaluating your application and documents
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Application Details -->
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Scholarship Details</h4>
                                <div class="space-y-1 text-sm text-gray-600 dark:text-gray-300">
                                    <p><span class="font-medium">Grant Amount:</span> ₱{{ number_format((float) $application->scholarship->grant_amount, 2) }}</p>
                                    <p><span class="font-medium">Deadline:</span> {{ \Carbon\Carbon::parse($application->scholarship->deadline)?->format('M d, Y') }}</p>
                                    <p><span class="font-medium">Minimum GWA:</span> {{ $application->scholarship->getGwaRequirement() ?? 'Not specified' }}</p>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Application Info</h4>
                                <div class="space-y-1 text-sm text-gray-600 dark:text-gray-300">
                                    <p><span class="font-medium">Application ID:</span> #{{ str_pad($application->id, 6, '0', STR_PAD_LEFT) }}</p>
                                    <p><span class="font-medium">Status:</span> 
                                        <span class="font-semibold 
                                            {{ $application->status === 'approved' ? 'text-green-600 dark:text-green-400' : 
                                               ($application->status === 'rejected' ? 'text-red-600 dark:text-red-400' : 
                                               'text-yellow-600 dark:text-yellow-400') }}">
                                            {{ ucfirst($application->status) }}
                                        </span>
                                    </p>
                                    <p><span class="font-medium">Submitted:</span> {{ $application->created_at?->format('M d, Y h:i A') }}</p>
                                    @if($application->status === 'approved' || $application->status === 'rejected')
                                        <p><span class="font-medium">SFAO Evaluated:</span> 
                                            <span class="text-green-600 dark:text-green-400 font-medium">
                                                {{ $application->updated_at?->format('M d, Y h:i A') }}
                                            </span>
                                        </p>
                                    @else
                                        <p><span class="font-medium">Last Updated:</span> {{ $application->updated_at?->format('M d, Y h:i A') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 flex flex-wrap gap-3">
                            @if($application->status === 'approved')
                                <button class="px-4 py-2 bg-green-100 text-green-800 rounded-lg text-sm font-medium hover:bg-green-200 transition">
                                    ✅ Congratulations!
                                </button>
                            @elseif($application->status === 'rejected')
                                <button class="px-4 py-2 bg-red-100 text-red-800 rounded-lg text-sm font-medium hover:bg-red-200 transition">
                                    ❌ Not Selected
                                </button>
                            @endif
                            
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
