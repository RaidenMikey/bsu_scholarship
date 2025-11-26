<div x-data="{ open: false }" 
     class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-bsu-red p-6 hover:shadow-xl hover:border-bsu-redDark hover:shadow-bsu-red/20 transition-all duration-300 group relative overflow-hidden mb-8"
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
                        <h3 class="text-xl font-bold text-bsu-red dark:text-white group-hover:text-bsu-redDark dark:group-hover:text-bsu-red transition-colors duration-200">
                            {{ $scholarship->scholarship_name }}
                        </h3>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $scholarship->getScholarshipTypeBadgeColor() }} shadow-sm">
                              {{ ucfirst($scholarship->scholarship_type) }}
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
                  $applicationsCount = $scholarship->applications()->count();
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
                        <div class="bg-bsu-red h-2 rounded-full transition-all duration-300" 
                             data-width="{{ $fillPercentage }}"
                             x-bind:style="'width: ' + $el.dataset.width + '%'"></div>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ number_format($fillPercentage, 1) }}% filled
                    </div>
                </div>
                @endif

                <!-- Dropdown Toggle Button -->
                <button @click="open = !open" 
                        class="flex items-center justify-center gap-2 px-4 py-2 bg-bsu-red hover:bg-bsu-redDark text-white rounded-lg transition-colors">
                    <span class="text-sm font-medium">View Details</span>
                    <svg class="w-4 h-4 transition-transform" 
                         :class="{ 'rotate-180': open }" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
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
                
                <!-- Extended Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Column 1: Key Info & Eligibility -->
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Key Information</h4>
                            <div class="space-y-1">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Grant Type:</span>
                                    <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $scholarship->grant_type ?? 'N/A')) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Priority Level:</span>
                                    <span class="font-medium">{{ ucfirst($scholarship->priority_level ?? 'N/A') }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Renewable:</span>
                                    <span class="font-medium">{{ $scholarship->renewal_allowed ? 'Yes' : 'No' }}</span>
                                </div>
                                @if($scholarship->application_start_date)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">App Opens:</span>
                                    <span class="font-medium">{{ $scholarship->application_start_date->format('M d, Y') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Eligibility Criteria</h4>
                            <div class="space-y-1">
                                @if($scholarship->min_gwa)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Minimum GWA:</span>
                                    <span class="font-medium">{{ $scholarship->min_gwa }}</span>
                                </div>
                                @endif
                                @if($scholarship->max_annual_income)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Max Income:</span>
                                    <span class="font-medium">₱{{ number_format($scholarship->max_annual_income, 2) }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Column 2: Requirements -->
                    <div>
                        @if($scholarship->requiredDocuments->count() > 0)
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Required Documents</h4>
                        <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-300 space-y-1">
                            @foreach($scholarship->requiredDocuments as $document)
                                <li>{{ $document->document_name }}</li>
                            @endforeach
                        </ul>
                        @else
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Requirements</h4>
                        <p class="text-sm text-gray-500 italic">No specific documents listed.</p>
                        @endif
                    </div>

                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('central.scholarships.edit', $scholarship->id) }}" 
                       class="inline-flex justify-center items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bsu-red transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </a>
                    <form action="{{ route('central.scholarships.destroy', $scholarship->id) }}" method="POST" class="inline-block" onsubmit="return confirmDelete('{{ $scholarship->scholarship_name }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex justify-center items-center px-4 py-2 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg text-sm font-medium text-red-700 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Remove
                        </button>
                    </form>
                </div>
        </div>
</div>
