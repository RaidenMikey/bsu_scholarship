<!-- Application Forms Tab -->
<div x-show="tab === 'all-app-forms'" x-cloak>
    <div class="container mx-auto px-4 py-8">
        
        <!-- Main Card container -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
            
            <!-- Header with BatState-U branding -->
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-bsu-red flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-white">Application Forms</h2>
                    <p class="text-sm text-white/80 mt-1">Manage downloadable resources for your campus</p>
                </div>
                
                <!-- Only show header button if table is NOT empty -->
                @if($forms->isNotEmpty())
                    <button @click="$dispatch('switch-tab', 'up-app-form')" class="px-4 py-2 bg-white text-bsu-red text-sm font-bold rounded-lg hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Upload New Form
                    </button>
                @endif
            </div>

            <!-- Card Body -->
            <div class="p-6">
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 rounded-lg flex items-center gap-2">
                        <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-lg flex items-center gap-2">
                        <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                @if($forms->isEmpty())
                    <!-- Empty State -->
                    <div class="flex flex-col items-center justify-center py-16 text-center">
                        <div class="w-24 h-24 bg-gray-50 dark:bg-gray-700/50 rounded-full flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">No Application Forms</h3>
                        <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto mb-8">
                            You haven't uploaded any application forms yet. Upload a form to make it available for students to download.
                        </p>
                        <button @click="$dispatch('switch-tab', 'up-app-form')" class="px-6 py-3 bg-bsu-red hover:bg-red-700 text-white font-medium rounded-lg transition shadow-md hover:shadow-lg flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Upload First Form
                        </button>
                    </div>
                @else
                    <!-- Table -->
                    <div class="overflow-x-auto border border-gray-100 dark:border-gray-700 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Form Information</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Downloads</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Uploaded</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($forms as $form)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-red-50 dark:bg-red-900/20 rounded-lg flex items-center justify-center text-bsu-red">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $form->form_name }}</div>
                                                    <div class="text-xs text-gray-500 mt-0.5">{{ strtoupper($form->file_type) }} • {{ $form->getFileSize() }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                                {{ $form->form_type ?? 'General' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white bg-blue-50 dark:bg-blue-900/20 px-2 py-1 rounded-md text-blue-700 dark:text-blue-300">
                                                {{ $form->download_count }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-white">{{ $form->created_at->format('M d, Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $form->uploader->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end gap-3">
                                                <a href="{{ route('application-forms.download', $form->id) }}" class="text-gray-500 hover:text-bsu-red dark:text-gray-400 dark:hover:text-red-400 transition" title="Download">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                </a>
                                                <form action="{{ route('sfao.application-forms.destroy', $form->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this form?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition" title="Delete">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($forms->hasPages())
                        <div class="mt-6">
                            {{ $forms->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
