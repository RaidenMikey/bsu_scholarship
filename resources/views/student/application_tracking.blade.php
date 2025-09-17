<div x-show="tab === 'tracking'" x-transition x-cloak class="px-4 py-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-bsu-red dark:text-red-400 mb-2">📊 Application Tracking</h2>
        <p class="text-gray-600 dark:text-gray-300">Monitor the progress of your scholarship applications</p>
    </div>

    @if($applicationTracking->isEmpty())
        <div class="text-center py-12">
            <div class="text-gray-400 dark:text-gray-500 text-6xl mb-4">📋</div>
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
                                        <span class="text-lg font-bold text-white">🎓</span>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $application->scholarship->scholarship_name }}
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Applied on {{ $application->created_at->format('M d, Y') }}
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
                                            <p class="text-xs text-green-600 dark:text-green-400">{{ $application->created_at->format('M d') }}</p>
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
                                        {{ $application->status === 'approved' ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-500' }}">
                                        @if($application->status === 'approved')
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
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
                                        @if($application->status === 'approved')
                                            <p class="text-xs text-green-600 dark:text-green-400">Completed</p>
                                        @else
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Pending</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Connector Line -->
                                <div class="flex-1 h-0.5 mx-4 
                                    {{ $application->status === 'approved' ? 'bg-green-500' : 'bg-gray-300' }}"></div>

                                <!-- Step 4: Final Decision -->
                                <div class="flex flex-col items-center">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-full 
                                        {{ $application->status === 'approved' || $application->status === 'rejected' ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-500' }}">
                                        @if($application->status === 'approved' || $application->status === 'rejected')
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
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
                                        @if($application->status === 'approved' || $application->status === 'rejected')
                                            <p class="text-xs text-green-600 dark:text-green-400">Completed</p>
                                        @else
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Pending</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Application Details -->
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Scholarship Details</h4>
                                <div class="space-y-1 text-sm text-gray-600 dark:text-gray-300">
                                    <p><span class="font-medium">Grant Amount:</span> ₱{{ number_format($application->scholarship->grant_amount, 2) }}</p>
                                    <p><span class="font-medium">Deadline:</span> {{ \Carbon\Carbon::parse($application->scholarship->deadline)->format('M d, Y') }}</p>
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
                                    <p><span class="font-medium">Last Updated:</span> {{ $application->updated_at->format('M d, Y h:i A') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 flex flex-wrap gap-3">
                            @if($application->status === 'pending')
                                <button class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-lg text-sm font-medium hover:bg-yellow-200 transition">
                                    ⏳ Under Review
                                </button>
                            @elseif($application->status === 'approved')
                                <button class="px-4 py-2 bg-green-100 text-green-800 rounded-lg text-sm font-medium hover:bg-green-200 transition">
                                    ✅ Congratulations!
                                </button>
                            @elseif($application->status === 'rejected')
                                <button class="px-4 py-2 bg-red-100 text-red-800 rounded-lg text-sm font-medium hover:bg-red-200 transition">
                                    ❌ Not Selected
                                </button>
                            @endif
                            
                            <a href="{{ route('student.scholarships') }}" 
                               class="px-4 py-2 bg-bsu-red text-white rounded-lg text-sm font-medium hover:bg-red-700 transition">
                                View Other Scholarships
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
