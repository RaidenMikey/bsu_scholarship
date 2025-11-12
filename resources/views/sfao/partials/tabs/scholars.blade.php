<div x-show="tab === 'scholars'" x-transition x-cloak class="px-4 py-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-bsu-red dark:text-red-400 mb-2">🎓 Scholars Under Your Campus</h2>
        <p class="text-gray-600 dark:text-gray-300">
          Students who have been accepted as scholars from {{ $sfaoCampus->name }}
          @if($sfaoCampus->extensionCampuses->count() > 0)
            and its extension campuses: {{ $sfaoCampus->extensionCampuses->pluck('name')->join(', ') }}
          @endif
        </p>
    </div>

    <!-- Sorting and Filtering Controls -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('sfao.dashboard') }}" class="space-y-4">
            <!-- Hidden field to maintain tab -->
            <input type="hidden" name="tab" value="scholars">
            
            <!-- Filter Row -->
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Sort:</label>
                    <select name="scholars_sort_by" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-bsu-red focus:border-bsu-red">
                        <option value="created_at" {{ ($scholarsSortBy ?? 'created_at') === 'created_at' ? 'selected' : '' }}>Date Added</option>
                        <option value="name" {{ ($scholarsSortBy ?? 'created_at') === 'name' ? 'selected' : '' }}>Name</option>
                        <option value="email" {{ ($scholarsSortBy ?? 'created_at') === 'email' ? 'selected' : '' }}>Email</option>
                        <option value="scholarship" {{ ($scholarsSortBy ?? 'created_at') === 'scholarship' ? 'selected' : '' }}>Scholarship</option>
                        <option value="status" {{ ($scholarsSortBy ?? 'created_at') === 'status' ? 'selected' : '' }}>Status</option>
                        <option value="type" {{ ($scholarsSortBy ?? 'created_at') === 'type' ? 'selected' : '' }}>Type</option>
                    </select>
                </div>

                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Order:</label>
                    <select name="scholars_sort_order" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-bsu-red focus:border-bsu-red">
                        <option value="asc" {{ ($scholarsSortOrder ?? 'desc') === 'asc' ? 'selected' : '' }}>Ascending</option>
                        <option value="desc" {{ ($scholarsSortOrder ?? 'desc') === 'desc' ? 'selected' : '' }}>Descending</option>
                    </select>
                </div>

                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Campus:</label>
                    <select name="campus_filter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-bsu-red focus:border-bsu-red">
                        @foreach($campusOptions as $campus)
                            <option value="{{ $campus['id'] }}" {{ ($campusFilter ?? 'all') == $campus['id'] ? 'selected' : '' }}>
                                {{ $campus['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Status:</label>
                    <select name="status_filter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-bsu-red focus:border-bsu-red">
                        <option value="all" {{ ($statusFilter ?? 'all') === 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="active" {{ ($statusFilter ?? 'all') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ ($statusFilter ?? 'all') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ ($statusFilter ?? 'all') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="completed" {{ ($statusFilter ?? 'all') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <div class="flex items-center space-x-2">
                    <button type="submit" class="bg-bsu-red text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700 transition-colors">
                        Apply
                    </button>
                    <a href="/sfao?tab=scholars" class="bg-gray-500 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-600 transition-colors">
                        Clear
                    </a>
                </div>
            </div>
        </form>
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
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Scholars</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $scholars->count() }}</p>
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
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $scholars->where('status', 'active')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">New Scholars</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $scholars->where('type', 'new')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Continuing</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $scholars->where('type', 'old')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($scholars->isEmpty())
        <div class="text-center py-12">
            <div class="text-gray-400 dark:text-gray-500 text-6xl mb-4">🎓</div>
            <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">No Scholars Found</h3>
            <p class="text-gray-500 dark:text-gray-500">No students have been accepted as scholars yet.</p>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-bsu-red text-white">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Scholar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Campus</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Scholarship</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Grants</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Total Received</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($scholars as $index => $scholar)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-bsu-red flex items-center justify-center">
                                                <span class="text-sm font-medium text-white">
                                                    {{ strtoupper(substr($scholar->user->name ?? 'N/A', 0, 2)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $scholar->user->name ?? 'Unknown Student' }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $scholar->user->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $scholar->user->campus->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $scholar->scholarship->scholarship_name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold {{ $scholar->type === 'new' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                        {{ ucfirst($scholar->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold {{ $scholar->isActive() ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                        {{ ucfirst($scholar->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $scholar->grant_count ?? 0 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $scholar->total_grant_received ? '₱' . number_format((float)$scholar->total_grant_received, 0) : '₱0' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

