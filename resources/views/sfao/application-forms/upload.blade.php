<!-- Upload Application Form Tab -->
<div x-show="tab === 'up-app-form'" x-cloak>
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Upload Application Form</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Upload a new application form for students in your campus</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form action="{{ route('sfao.application-forms.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-6">
                <label for="form_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Form Name <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="form_name" 
                       name="form_name" 
                       value="{{ old('form_name') }}"
                       required
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-bsu-red focus:border-transparent dark:bg-gray-700 dark:text-white"
                       placeholder="e.g., SFAO Application Form">
                @error('form_name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="form_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Form Type
                </label>
                <select id="form_type" 
                        name="form_type"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-bsu-red focus:border-transparent dark:bg-gray-700 dark:text-white">
                    <option value="">Select Type (Optional)</option>
                    <option value="SFAO Application Form" {{ old('form_type') == 'SFAO Application Form' ? 'selected' : '' }}>SFAO Application Form</option>
                    <option value="TDP Application Form" {{ old('form_type') == 'TDP Application Form' ? 'selected' : '' }}>TDP Application Form</option>
                    <option value="Scholarship Application" {{ old('form_type') == 'Scholarship Application' ? 'selected' : '' }}>Scholarship Application</option>
                    <option value="Renewal Form" {{ old('form_type') == 'Renewal Form' ? 'selected' : '' }}>Renewal Form</option>
                    <option value="Other" {{ old('form_type') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('form_type')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Description
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="3"
                          class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-bsu-red focus:border-transparent dark:bg-gray-700 dark:text-white"
                          placeholder="Brief description of this form...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Upload File <span class="text-red-500">*</span>
                </label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg hover:border-bsu-red transition">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 dark:text-gray-400">
                            <label for="file" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-bsu-red hover:text-red-700 focus-within:outline-none">
                                <span>Upload a file</span>
                                <input id="file" name="file" type="file" class="sr-only" required accept=".pdf,.doc,.docx,.xls,.xlsx">
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            PDF, DOC, DOCX, XLS, XLSX up to 10MB
                        </p>
                    </div>
                </div>
                @error('file')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                <div class="flex">
                    <svg class="h-5 w-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <div class="text-sm text-blue-700 dark:text-blue-300">
                        <p class="font-medium">Campus: {{ $user->campus->name }}</p>
                        <p class="mt-1">This form will only be visible to students in your campus.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" @click="$dispatch('switch-tab', 'all-app-forms')" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-bsu-red text-white rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Upload Form
                </button>
            </div>
        </form>
    </div>
</div>
</div>
