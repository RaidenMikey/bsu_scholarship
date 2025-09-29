@extends('sfao.layouts.evaluation')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Document Evaluation</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Stage 1: Select Scholarship to Evaluate</p>
            </div>
        </div>

        <!-- Student Info -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
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
        </div>

        <!-- Progress Indicator -->
        <div class="mb-8">
            <div class="flex items-center justify-center space-x-2">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-bsu-red text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                    <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Select Scholarship</span>
                </div>
                <div class="w-12 h-1 bg-gray-300 dark:bg-gray-600"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400 rounded-full flex items-center justify-center text-sm font-bold">2</div>
                    <span class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400">SFAO Documents</span>
                </div>
                <div class="w-12 h-1 bg-gray-300 dark:bg-gray-600"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400 rounded-full flex items-center justify-center text-sm font-bold">3</div>
                    <span class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400">Scholarship Documents</span>
                </div>
                <div class="w-12 h-1 bg-gray-300 dark:bg-gray-600"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400 rounded-full flex items-center justify-center text-sm font-bold">4</div>
                    <span class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400">Final Review</span>
                </div>
            </div>
        </div>

        @if($appliedScholarships->count() > 0)
            <!-- Applied Scholarships -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Applied Scholarships</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Select a scholarship to evaluate the student's submitted documents.</p>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($appliedScholarships as $scholarship)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between mb-3">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $scholarship->scholarship_name }}</h4>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $scholarship->getStatusBadge()['color'] }}">
                                        {{ $scholarship->getStatusBadge()['text'] }}
                                    </span>
                                </div>
                                
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ Str::limit($scholarship->description, 100) }}</p>
                                
                                <div class="space-y-2 mb-4">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Type:</span>
                                        <span class="font-medium">{{ ucfirst($scholarship->scholarship_type) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Priority:</span>
                                        <span class="font-medium">{{ ucfirst($scholarship->priority_level) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Grant Amount:</span>
                                        <span class="font-medium">₱{{ number_format($scholarship->grant_amount, 2) }}</span>
                                    </div>
                                </div>
                                
                                <a href="{{ route('sfao.evaluation.sfao-documents', ['user_id' => $student->id, 'scholarship_id' => $scholarship->id]) }}" 
                                   class="w-full bg-bsu-red text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors text-center block">
                                    Start Evaluation
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <!-- No Applications -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center">
                <div class="text-gray-400 dark:text-gray-500 text-6xl mb-4">📋</div>
                <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">No Applications Found</h3>
                <p class="text-gray-500 dark:text-gray-500">This student has not applied to any scholarships yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection
