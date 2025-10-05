<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Applications - BSU</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-100">

<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">My Applications</h1>
        <p class="text-gray-600">Track your scholarship applications</p>
    </div>

    <!-- Applications List -->
    <div class="space-y-6">
        @forelse($applications as $application)
            <div class="bg-white rounded-xl shadow-lg border-2 border-gray-200 p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">
                            {{ $application->scholarship->scholarship_name }}
                        </h3>
                        <p class="text-gray-600 mb-2">
                            {{ Str::limit($application->scholarship->description, 150) }}
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($application->status === 'approved') bg-green-100 text-green-800
                            @elseif($application->status === 'rejected') bg-red-100 text-red-800
                            @elseif($application->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($application->status === 'claimed') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($application->status) }}
                        </span>
                        <p class="text-sm text-gray-500 mt-1">
                            Applied {{ $application->created_at->format('M d, Y') }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <span class="text-sm text-gray-500">Application Type:</span>
                        <p class="font-medium">{{ ucfirst($application->type) }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Deadline:</span>
                        <p class="font-medium">{{ \Carbon\Carbon::parse($application->scholarship->submission_deadline)->format('M d, Y') }}</p>
                    </div>
                    @if($application->scholarship->grant_amount)
                        <div>
                            <span class="text-sm text-gray-500">Grant Amount:</span>
                            <p class="font-medium">₱{{ number_format($application->scholarship->grant_amount, 2) }}</p>
                        </div>
                    @endif
                </div>

                @if($application->status === 'approved')
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-green-800 font-medium">Congratulations! Your application has been approved.</span>
                        </div>
                    </div>
                @elseif($application->status === 'rejected')
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span class="text-red-800 font-medium">Unfortunately, your application was not approved.</span>
                        </div>
                    </div>
                @elseif($application->status === 'pending')
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-yellow-800 font-medium">Your application is being reviewed.</span>
                        </div>
                    </div>
                @endif

                <div class="flex space-x-2">
                    <a href="{{ route('student.dashboard') }}" 
                       class="bg-bsu-red hover:bg-bsu-redDark text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                        ← Back to Dashboard
                    </a>
                    @if($application->status === 'pending')
                        <form method="POST" action="{{ route('student.unapply') }}" class="inline">
                            @csrf
                            <input type="hidden" name="scholarship_id" value="{{ $application->scholarship_id }}">
                            <button type="submit" 
                                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition"
                                    onclick="return confirm('Are you sure you want to withdraw this application?')">
                                Withdraw Application
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <div class="text-gray-400 text-6xl mb-4">📝</div>
                <h3 class="text-xl font-medium text-gray-900 mb-2">No applications yet</h3>
                <p class="text-gray-600 mb-6">You haven't applied for any scholarships yet.</p>
                <a href="{{ route('student.dashboard') }}" 
                   class="bg-bsu-red hover:bg-bsu-redDark text-white px-6 py-3 rounded-lg font-medium transition">
                    Browse Scholarships
                </a>
            </div>
        @endforelse
    </div>
</div>

</body>
</html>
