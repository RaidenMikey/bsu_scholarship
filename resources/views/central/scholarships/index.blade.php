<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Scholarships Management - BSU</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-100">

<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Scholarships Management</h1>
        <p class="text-gray-600">Manage all scholarships in the system</p>
    </div>

    <!-- Actions -->
    <div class="mb-6 flex justify-between items-center">
        <div class="flex space-x-4">
            <a href="{{ route('central.scholarships.create') }}" 
               class="bg-bsu-red hover:bg-bsu-redDark text-white px-4 py-2 rounded-lg font-medium transition">
                ➕ Add New Scholarship
            </a>
        </div>
        
        <div class="flex space-x-2">
            <select class="border rounded-lg px-3 py-2" onchange="window.location.href = this.value">
                <option value="{{ route('central.dashboard') }}?sort_by=created_at&sort_order=desc" 
                        {{ request('sort_by') == 'created_at' && request('sort_order') == 'desc' ? 'selected' : '' }}>
                    Newest First
                </option>
                <option value="{{ route('central.dashboard') }}?sort_by=created_at&sort_order=asc"
                        {{ request('sort_by') == 'created_at' && request('sort_order') == 'asc' ? 'selected' : '' }}>
                    Oldest First
                </option>
                <option value="{{ route('central.dashboard') }}?sort_by=scholarship_name&sort_order=asc"
                        {{ request('sort_by') == 'scholarship_name' && request('sort_order') == 'asc' ? 'selected' : '' }}>
                    Name A-Z
                </option>
                <option value="{{ route('central.dashboard') }}?sort_by=scholarship_name&sort_order=desc"
                        {{ request('sort_by') == 'scholarship_name' && request('sort_order') == 'desc' ? 'selected' : '' }}>
                    Name Z-A
                </option>
            </select>
        </div>
    </div>

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
                </div>

                <div class="flex space-x-2">
                    <a href="{{ route('central.scholarships.edit', $scholarship->id) }}" 
                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-3 rounded-lg text-sm font-medium transition">
                        ✏️ Edit
                    </a>
                    <form method="POST" action="{{ route('central.scholarships.destroy', $scholarship->id) }}" 
                          class="flex-1" onsubmit="return confirm('Are you sure you want to delete this scholarship?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-3 rounded-lg text-sm font-medium transition">
                            🗑️ Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <div class="text-gray-400 text-6xl mb-4">📚</div>
                <h3 class="text-xl font-medium text-gray-900 mb-2">No scholarships found</h3>
                <p class="text-gray-600 mb-6">Get started by creating your first scholarship.</p>
                <a href="{{ route('central.scholarships.create') }}" 
                   class="bg-bsu-red hover:bg-bsu-redDark text-white px-6 py-3 rounded-lg font-medium transition">
                    Create Scholarship
                </a>
            </div>
        @endforelse
    </div>
</div>

</body>
</html>
