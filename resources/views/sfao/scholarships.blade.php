<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SFAO Scholarships - BSU</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-100">

<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Available Scholarships</h1>
        <p class="text-gray-600">View and manage scholarships for your campus</p>
    </div>

    <!-- Campus Info -->
    @if(isset($sfaoCampus))
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="text-blue-800 font-medium">Managing scholarships for: {{ $sfaoCampus->name }}</span>
            </div>
        </div>
    @endif

    <!-- Scholarships Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($scholarships as $scholarship)
            <div class="bg-white rounded-xl shadow-lg border-2 border-gray-200 p-6 hover:shadow-xl transition">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-xl font-bold text-gray-900 line-clamp-2">
                        {{ $scholarship->scholarship_name }}
                    </h3>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                        @if($scholarship->is_active) bg-green-100 text-green-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ $scholarship->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <p class="text-gray-600 mb-4 line-clamp-3">
                    {{ Str::limit($scholarship->description, 100) }}
                </p>

                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Type:</span>
                        <span class="font-medium">{{ ucfirst($scholarship->scholarship_type) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Deadline:</span>
                        <span class="font-medium">{{ \Carbon\Carbon::parse($scholarship->submission_deadline)->format('M d, Y') }}</span>
                    </div>
                    @if($scholarship->grant_amount)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Amount:</span>
                            <span class="font-medium">₱{{ number_format($scholarship->grant_amount, 2) }}</span>
                        </div>
                    @endif
                    @if($scholarship->slots_available)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Slots:</span>
                            <span class="font-medium">{{ $scholarship->slots_available }}</span>
                        </div>
                    @endif
                </div>

                <div class="flex space-x-2">
                    <a href="{{ route('sfao.scholarships.show', $scholarship->id) }}" 
                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-3 rounded-lg text-sm font-medium transition">
                        👁️ View Details
                    </a>
                    <a href="{{ route('sfao.dashboard') }}?tab=applicants" 
                       class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-2 px-3 rounded-lg text-sm font-medium transition">
                        👥 View Applicants
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <div class="text-gray-400 text-6xl mb-4">📚</div>
                <h3 class="text-xl font-medium text-gray-900 mb-2">No scholarships available</h3>
                <p class="text-gray-600 mb-6">There are currently no scholarships available for your campus.</p>
                <a href="{{ route('sfao.dashboard') }}" 
                   class="bg-bsu-red hover:bg-bsu-redDark text-white px-6 py-3 rounded-lg font-medium transition">
                    Back to Dashboard
                </a>
            </div>
        @endforelse
    </div>
</div>

</body>
</html>
