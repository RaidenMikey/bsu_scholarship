<div class="space-y-6">
    
    <!-- Header with Sort/Filter (Optional, can be added later) -->
    
    <!-- Scholarships List -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        @forelse($scholarships as $scholarship)
            <div x-data 
                 x-show="subTab !== 'my_scholarships' && (subTab === 'all' || subTab === '{{ strtolower($scholarship->scholarship_type) }}')"
                 class="col-span-1"
            >
                @php
                    // Use the controller-passed variable for pending application check
                    // Logic: User has active application if controller says so, AND they are not applying to THIS scholarship (which would be 'applied' state)
                    $hasActiveApplication = ($hasPendingApplication ?? false) && 
                                            !($scholarship->is_scholar ?? false) && 
                                            !($scholarship->applied ?? false);
                                            
                     // Calculate fill percentage if not passed
                     $applicationsCount = $scholarship->applications_count ?? 0;
                     $slotsAvailable = $scholarship->slots_available ?? 1;
                     $fillPercentage = min(100, ($applicationsCount / $slotsAvailable) * 100);
                @endphp

                @include('central.partials.components.scholarship-card', [
                    'scholarship' => $scholarship,
                    'role' => 'student',
                    'hasActiveApplication' => $hasActiveApplication,
                    'fillPercentage' => $fillPercentage
                ])
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center py-12 text-center">
                <div class="bg-gray-100 p-4 rounded-full mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">No scholarships found</h3>
                <p class="text-gray-500">Try adjusting your filters or check back later.</p>
            </div>
        @endforelse

        <!-- Empty State for Private Scholarships -->
        <div x-show="subTab === 'private_scholarships' && {{ $privateScholarshipsCount }} === 0" 
             class="col-span-full flex flex-col items-center justify-center py-12 text-center" x-cloak>
            <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-full mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">No private scholarships available</h3>
            <p class="text-gray-500 dark:text-gray-400">There are currently no active private scholarships.</p>
        </div>

        <!-- Empty State for Government Scholarships -->
        <div x-show="subTab === 'government_scholarships' && {{ $governmentScholarshipsCount }} === 0" 
             class="col-span-full flex flex-col items-center justify-center py-12 text-center" x-cloak>
            <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-full mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">No government scholarships available</h3>
            <p class="text-gray-500 dark:text-gray-400">There are currently no active government scholarships.</p>
        </div>
        
        <!-- My Scholarships (Active) -->
        @if(isset($myScholarships) && $myScholarships->count() > 0)
             @foreach($myScholarships as $scholarship)
                <div x-show="subTab === 'my_scholarships'" class="col-span-full xl:col-span-1">
                    @include('central.partials.components.scholarship-card', [
                        'scholarship' => $scholarship,
                        'role' => 'student',
                        'hasActiveApplication' => false,
                        'fillPercentage' => 0
                    ])
                </div>
             @endforeach
        @endif
    </div>

    <!-- Pagination -->
    <div class="mt-8 flex justify-center">
        {{ $scholarships->appends(request()->query())->links('vendor.pagination.custom') }}
    </div>
</div>