<div class="px-4 py-4 space-y-1">
    <div class="text-xs font-semibold text-gray-300 uppercase tracking-wider mb-2 px-2">Menu</div>
    
    <!-- Dashboard / Scholarships -->
    <a href="{{ route('student.dashboard', ['tab' => 'scholarships']) }}" 
       class="flex items-center px-4 py-3 text-white hover:bg-white/10 rounded-lg transition-colors group {{ request()->routeIs('student.dashboard') && request()->get('tab') !== 'applied-scholarships' && request()->get('tab') !== 'notifications' ? 'bg-white/20' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
        </svg>
        <span>Scholarships</span>
    </a>

    <!-- My Applications -->
    <a href="{{ route('student.applications') }}" 
       class="flex items-center px-4 py-3 text-white hover:bg-white/10 rounded-lg transition-colors group {{ request()->routeIs('student.applications') ? 'bg-white/20' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <span>My Applications</span>
    </a>

    <!-- Notifications -->
    <a href="{{ route('student.dashboard', ['tab' => 'notifications']) }}" 
       class="flex items-center px-4 py-3 text-white hover:bg-white/10 rounded-lg transition-colors group {{ request()->get('tab') === 'notifications' ? 'bg-white/20' : '' }}">
       <div class="relative">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <!-- Badge via Alpine or Backend -->
            @if(isset($unreadCount) && $unreadCount > 0)
            <span class="absolute -top-1 -right-0 block h-2.5 w-2.5 rounded-full bg-yellow-400 ring-2 ring-bsu-red"></span>
            @endif
       </div>
        <span>Notifications</span>
        @if(isset($unreadCount) && $unreadCount > 0)
        <span class="ml-auto bg-yellow-400 text-bsu-red py-0.5 px-2 rounded-full text-xs font-bold">{{ $unreadCount }}</span>
        @endif
    </a>
</div>
