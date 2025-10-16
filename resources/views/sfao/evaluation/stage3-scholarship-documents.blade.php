@extends('sfao.layouts.evaluation')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Document Evaluation</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Stage 3: Evaluate Scholarship Required Documents</p>
            </div>
        </div>

        <!-- Student & Scholarship Info -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="h-16 w-16 rounded-full bg-bsu-red flex items-center justify-center">
                        <span class="text-xl font-bold text-white">
                            {{ strtoupper(substr($student->name, 0, 2)) }}
                        </span>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $student->name }}</h2>
                        <p class="text-gray-600 dark:text-gray-400">{{ $student->email }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-500">{{ $student->campus->name }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $scholarship->scholarship_name }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ $scholarship->scholarship_type }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-500">₱{{ number_format($scholarship->grant_amount, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Progress Indicator -->
        <div class="mb-8">
            <div class="flex items-center justify-center space-x-2">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold">✓</div>
                    <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Select Scholarship</span>
                </div>
                <div class="w-12 h-1 bg-green-500"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold">✓</div>
                    <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">SFAO Documents</span>
                </div>
                <div class="w-12 h-1 bg-bsu-red"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-bsu-red text-white rounded-full flex items-center justify-center text-sm font-bold">3</div>
                    <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Scholarship Documents</span>
                </div>
                <div class="w-12 h-1 bg-gray-300 dark:bg-gray-600"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400 rounded-full flex items-center justify-center text-sm font-bold">4</div>
                    <span class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400">Final Review</span>
                </div>
            </div>
        </div>

        @if($scholarshipDocuments->count() > 0)
            <!-- Scholarship Documents Evaluation Form -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Scholarship Required Documents</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Evaluate the student's submitted scholarship-specific documents</p>
                </div>
                
                <form action="{{ route('sfao.evaluation.scholarship-submit', ['user_id' => $student->id, 'scholarship_id' => $scholarship->id]) }}" method="POST" class="p-6">
                    @csrf
                    
                    <div class="space-y-6">
                        @foreach($scholarshipDocuments as $document)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h4 class="text-lg font-medium text-gray-900 dark:text-white">{{ $document->document_name }}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $document->description }}</p>
                                        @if($document->is_mandatory)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                Required
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                Optional
                                            </span>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <a href="{{ asset('storage/' . $document->file_path) }}" 
                                           target="_blank" 
                                           class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            View Document
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Evaluation Status
                                        </label>
                                        <select name="evaluations[{{ $document->id }}][status]" 
                                                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-bsu-red focus:border-bsu-red" required>
                                            <option value="">Select Status</option>
                                            <option value="approved" {{ $document->evaluation_status === 'approved' ? 'selected' : '' }}>✅ Approved</option>
                                            <option value="pending" {{ $document->evaluation_status === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                            <option value="rejected" {{ $document->evaluation_status === 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
                                        </select>
                                        <input type="hidden" name="evaluations[{{ $document->id }}][document_id]" value="{{ $document->id }}">
                                    </div>
                                </div>
                                
                                @if($document->evaluated_at)
                                    <div class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                                        Evaluated by: {{ $document->evaluator->name ?? 'Unknown' }} on {{ $document->evaluated_at->format('M d, Y g:i A') }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('sfao.evaluation.sfao-documents', ['user_id' => $student->id, 'scholarship_id' => $scholarship->id]) }}" 
                           class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            ← Back to SFAO Documents
                        </a>
                        
                        <button type="submit" 
                                class="px-6 py-2 bg-bsu-red text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-bsu-red focus:ring-offset-2">
                            Continue to Final Review →
                        </button>
                    </div>
                </form>
            </div>
        @else
            <!-- No Scholarship Documents -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center">
                <div class="text-gray-400 dark:text-gray-500 text-6xl mb-4">📄</div>
                <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">No Scholarship Documents Found</h3>
                <p class="text-gray-500 dark:text-gray-500">This student has not submitted any scholarship-specific documents for this scholarship.</p>
                <div class="mt-6 flex justify-center space-x-4">
                    <a href="{{ route('sfao.evaluation.sfao-documents', ['user_id' => $student->id, 'scholarship_id' => $scholarship->id]) }}" 
                       class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        ← Back to SFAO Documents
                    </a>
                    <a href="{{ route('sfao.evaluation.final', ['user_id' => $student->id, 'scholarship_id' => $scholarship->id]) }}" 
                       class="px-6 py-2 bg-bsu-red text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-bsu-red focus:ring-offset-2">
                        Continue to Final Review →
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
