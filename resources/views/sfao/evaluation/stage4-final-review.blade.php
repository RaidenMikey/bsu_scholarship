@extends('sfao.layouts.evaluation')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Document Evaluation</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Stage 4: Final Review & Application Decision</p>
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
                    </div>
                </div>
                <div class="text-right">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $scholarship->scholarship_name }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ $scholarship->scholarship_type }} • {{ ucfirst($scholarship->priority_level) }}</p>
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
                <div class="w-12 h-1 bg-green-500"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold">✓</div>
                    <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Scholarship Documents</span>
                </div>
                <div class="w-12 h-1 bg-bsu-red"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-bsu-red text-white rounded-full flex items-center justify-center text-sm font-bold">4</div>
                    <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Final Review</span>
                </div>
            </div>
        </div>

        <!-- Evaluation Summary -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Document Status Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Document Status</h3>
                <div class="space-y-3">
                    @php
                        $totalDocs = $evaluatedDocuments->count();
                        $approvedDocs = $evaluatedDocuments->where('evaluation_status', 'approved')->count();
                        $rejectedDocs = $evaluatedDocuments->where('evaluation_status', 'rejected')->count();
                        $pendingDocs = $evaluatedDocuments->where('evaluation_status', 'pending')->count();
                    @endphp
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Total Documents:</span>
                        <span class="font-semibold">{{ $totalDocs }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-green-600 dark:text-green-400">Approved:</span>
                        <span class="font-semibold text-green-600 dark:text-green-400">{{ $approvedDocs }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-red-600 dark:text-red-400">Rejected:</span>
                        <span class="font-semibold text-red-600 dark:text-red-400">{{ $rejectedDocs }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-yellow-600 dark:text-yellow-400">Pending:</span>
                        <span class="font-semibold text-yellow-600 dark:text-yellow-400">{{ $pendingDocs }}</span>
                    </div>
                </div>
            </div>

            <!-- Scholarship Requirements -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Scholarship Requirements</h3>
                <div class="space-y-3">
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white">Conditions:</h4>
                        <ul class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            @forelse($scholarship->conditions as $condition)
                                <li>• {{ $condition->getConditionDisplayName() }}: {{ $condition->getValueDisplayName() }}</li>
                            @empty
                                <li>No specific conditions</li>
                            @endforelse
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white">Required Documents:</h4>
                        <ul class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            @forelse($scholarship->requiredDocuments as $doc)
                                <li>• {{ $doc->document_name }} {{ $doc->is_mandatory ? '(Required)' : '(Optional)' }}</li>
                            @empty
                                <li>No specific documents required</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Application Status -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Application Status</h3>
                @if($application)
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Status:</span>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                {{ $application->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                   ($application->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                   'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200') }}">
                                {{ ucfirst($application->status) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Applied:</span>
                            <span class="font-semibold">{{ $application->created_at->format('M d, Y') }}</span>
                        </div>
                        @if($application->updated_at != $application->created_at)
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Last Updated:</span>
                                <span class="font-semibold">{{ $application->updated_at->format('M d, Y') }}</span>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400">No application found for this scholarship.</p>
                @endif
            </div>
        </div>

        <!-- Document Evaluation Details -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Document Evaluation Details</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Review all evaluated documents and their status.</p>
            </div>
            
            <div class="p-6">
                @if($evaluatedDocuments->count() > 0)
                    <div class="space-y-4">
                        @foreach($evaluatedDocuments as $document)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $document->document_name }}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $document->getDocumentCategoryDisplayName() }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-500">
                                            {{ $document->getFileTypeDisplayName() }} • {{ $document->getFileSizeFormatted() }}
                                        </p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" 
                                           class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition-colors">
                                            View Document
                                        </a>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $document->getEvaluationStatusBadgeColor() }}">
                                            {{ $document->getEvaluationStatusDisplayName() }}
                                        </span>
                                    </div>
                                </div>
                                
                                @if($document->evaluation_notes)
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 mb-3">
                                        <h5 class="text-sm font-medium text-gray-900 dark:text-white mb-1">Evaluation Notes:</h5>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $document->evaluation_notes }}</p>
                                    </div>
                                @endif
                                
                                @if($document->evaluated_at)
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        Evaluated by {{ $document->evaluator->name ?? 'Unknown' }} on {{ $document->evaluated_at->format('M d, Y g:i A') }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400 text-center py-8">No documents found for this scholarship.</p>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between items-center">
            <a href="{{ route('sfao.evaluation.sfao-documents', ['user_id' => $student->id, 'scholarship_id' => $scholarship->id]) }}" 
               class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                ← Back to Document Evaluation
            </a>
            
            <div class="flex space-x-4">
                @if($application)
                    @if($application->status === 'pending')
                        <form method="POST" action="{{ route('sfao.applications.reject', $application->id) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors"
                                    onclick="return confirm('Are you sure you want to reject this application?')">
                                Reject Application
                            </button>
                        </form>
                        
                        <form method="POST" action="{{ route('sfao.applications.approve', $application->id) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors"
                                    onclick="return confirm('Are you sure you want to approve this application?')">
                                Approve Application
                            </button>
                        </form>
                    @else
                        <span class="text-gray-500 dark:text-gray-400 px-6 py-2">
                            Application already {{ $application->status }}
                        </span>
                    @endif
                @else
                    <span class="text-gray-500 dark:text-gray-400 px-6 py-2">
                        No application found
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
