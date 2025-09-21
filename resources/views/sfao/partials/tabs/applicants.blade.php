<div x-show="tab === 'applicants'" x-transition x-cloak class="px-4 py-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-bsu-red dark:text-red-400 mb-2">👥 Students Under Your Campus</h2>
        <p class="text-gray-600 dark:text-gray-300">
          Students from {{ $sfaoCampus->name }}
          @if($sfaoCampus->extensionCampuses->count() > 0)
            and its extension campuses: {{ $sfaoCampus->extensionCampuses->pluck('name')->join(', ') }}
          @endif
        </p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Students</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $students->count() }}</p>
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
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Applied</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $students->where('has_applications', true)->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">With Documents</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $students->where('has_documents', true)->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Not Applied</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $students->where('has_applications', false)->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($students->isEmpty())
        <div class="text-center py-12">
            <div class="text-gray-400 dark:text-gray-500 text-6xl mb-4">👥</div>
            <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">No Students Found</h3>
            <p class="text-gray-500 dark:text-gray-500">No students have registered in the system yet.</p>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-bsu-red text-white">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Application Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Documents</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Applied Scholarships</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Applicant Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Grant Count</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($students as $index => $student)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-bsu-red flex items-center justify-center">
                                                <span class="text-sm font-medium text-white">
                                                    {{ strtoupper(substr($student->name, 0, 2)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->name }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $student->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($student->has_applications)
                                        <div class="flex flex-col space-y-1">
                                            @foreach($student->application_status as $status)
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                    {{ $status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                                       ($status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                                       'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200') }}">
                                                    {{ ucfirst($status) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                            Not Applied
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($student->has_documents)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-sm text-green-600 dark:text-green-400 font-medium">
                                                {{ $student->documents_count }} uploaded
                                            </span>
                                        </div>
                                        @if($student->last_uploaded)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ \Carbon\Carbon::parse($student->last_uploaded)?->format('M d, Y') }}
                                            </div>
                                        @endif
                                    @else
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-sm text-red-600 dark:text-red-400 font-medium">No documents</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($student->has_applications && count($student->applied_scholarships) > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($student->applied_scholarships as $scholarship)
                                                <span class="inline-flex px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded">
                                                    {{ $scholarship }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500 dark:text-gray-400">None</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($student->has_applications && isset($student->applications_with_types))
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($student->applications_with_types as $app)
                                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded {{ $app['type_badge_color'] }}">
                                                    {{ $app['type_display'] }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500 dark:text-gray-400">None</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($student->has_applications && isset($student->applications_with_types))
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($student->applications_with_types as $app)
                                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded {{ $app['grant_count_badge_color'] }}">
                                                    {{ $app['grant_count_display'] }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500 dark:text-gray-400">None</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        @if($student->has_documents)
                                            <a href="{{ route('sfao.viewDocuments', $student->student_id) }}"
                                               class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                View Docs
                                            </a>
                                        @endif
                                        @if($student->has_applications)
                                            <a href="{{ route('sfao.scholarships.index') }}"
                                               class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                                View Apps
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
