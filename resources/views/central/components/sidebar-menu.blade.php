<div class="space-y-4">
    <!-- Scholarships Header -->
    <div class="space-y-1">
        <div class="px-4 py-2 text-sm font-semibold text-gray-300 uppercase tracking-wider">
            Scholarships
        </div>
        <button @click.prevent="$dispatch('switch-tab', 'all_scholarships')"
                class="w-full text-left px-4 py-2 rounded transition text-sm flex items-center gap-2 {{ request()->get('tab') === 'all_scholarships' || !request()->has('tab') ? 'bg-white text-bsu-red' : 'text-white hover:bg-white/10' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            All Scholarships
        </button>
        <button @click.prevent="$dispatch('switch-tab', 'private_scholarships')"
                class="w-full text-left px-4 py-2 rounded transition text-sm flex items-center gap-2 {{ request()->get('tab') === 'private_scholarships' ? 'bg-white text-bsu-red' : 'text-white hover:bg-white/10' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            Private Scholarships
        </button>
        <button @click.prevent="$dispatch('switch-tab', 'government_scholarships')"
                class="w-full text-left px-4 py-2 rounded transition text-sm flex items-center gap-2 {{ request()->get('tab') === 'government_scholarships' ? 'bg-white text-bsu-red' : 'text-white hover:bg-white/10' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
            </svg>
            Government Scholarships
        </button>
    </div>

    <!-- Scholars Header -->
    <div class="space-y-1">
        <div class="px-4 py-2 text-sm font-semibold text-gray-300 uppercase tracking-wider">
            Scholars
        </div>
        <button @click.prevent="$dispatch('switch-tab', 'all_scholars')"
                class="w-full text-left px-4 py-2 rounded transition text-sm flex items-center gap-2 {{ request()->get('tab') === 'all_scholars' ? 'bg-white text-bsu-red' : 'text-white hover:bg-white/10' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            All Scholars
        </button>
        <button @click.prevent="$dispatch('switch-tab', 'new_scholars')"
                class="w-full text-left px-4 py-2 rounded transition text-sm flex items-center gap-2 {{ request()->get('tab') === 'new_scholars' ? 'bg-white text-bsu-red' : 'text-white hover:bg-white/10' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            New Scholars
        </button>
        <button @click.prevent="$dispatch('switch-tab', 'old_scholars')"
                class="w-full text-left px-4 py-2 rounded transition text-sm flex items-center gap-2 {{ request()->get('tab') === 'old_scholars' ? 'bg-white text-bsu-red' : 'text-white hover:bg-white/10' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Old Scholars
        </button>
        <button @click.prevent="$dispatch('switch-tab', 'endorsed_applicants')"
                class="w-full text-left px-4 py-2 rounded transition text-sm flex items-center gap-2 {{ request()->get('tab') === 'endorsed_applicants' ? 'bg-white text-bsu-red' : 'text-white hover:bg-white/10' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Endorsed Applicants
        </button>
        <button @click.prevent="$dispatch('switch-tab', 'rejected_applicants')"
                class="w-full text-left px-4 py-2 rounded transition text-sm flex items-center gap-2 {{ request()->get('tab') === 'rejected_applicants' ? 'bg-white text-bsu-red' : 'text-white hover:bg-white/10' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Rejected Applicants
        </button>
    </div>

    <!-- Reports Header -->
    <div class="space-y-1">
        <div class="px-4 py-2 text-sm font-semibold text-gray-300 uppercase tracking-wider">
            Reports
        </div>
        <button @click.prevent="$dispatch('switch-tab', 'sfao_reports')"
                class="w-full text-left px-4 py-2 rounded transition text-sm flex items-center gap-2 {{ request()->get('tab') === 'sfao_reports' ? 'bg-white text-bsu-red' : 'text-white hover:bg-white/10' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            SFAO Reports
        </button>
    </div>

    <!-- Statistics Header -->
    <div class="space-y-1">
        <div class="px-4 py-2 text-sm font-semibold text-gray-300 uppercase tracking-wider">
            Statistics
        </div>
        <button @click.prevent="$dispatch('switch-tab', 'all_statistics'); $dispatch('change-stats-campus', 'all')"
                class="w-full text-left px-4 py-2 rounded transition text-sm flex items-center gap-2 {{ request()->get('tab') === 'all_statistics' ? 'bg-white text-bsu-red' : 'text-white hover:bg-white/10' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
            </svg>
            All Campus Statistics
        </button>

        @if(isset($monitoredCampuses))
        <!-- Dynamic Campus Statistics Tabs -->
        @foreach($monitoredCampuses as $campus)
        <button @click.prevent="$dispatch('switch-tab', '{{ strtolower(str_replace(' ', '_', $campus->name)) }}_statistics'); $dispatch('change-stats-campus', '{{ $campus->id }}')"
                class="w-full text-left px-4 py-2 rounded transition text-sm flex items-center gap-2 pl-8 {{ request()->get('tab') === strtolower(str_replace(' ', '_', $campus->name)).'_statistics' ? 'bg-white text-bsu-red' : 'text-white hover:bg-white/10' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            {{ $campus->name }}
        </button>
        @endforeach
        @endif
    </div>

    <!-- Manage Users Header -->
    <div class="space-y-1">
        <div class="px-4 py-2 text-sm font-semibold text-gray-300 uppercase tracking-wider">
            Manage Users
        </div>
        <button @click.prevent="$dispatch('switch-tab', 'staff')"
                class="w-full text-left px-4 py-2 rounded transition text-sm flex items-center gap-2 {{ request()->get('tab') === 'staff' ? 'bg-white text-bsu-red' : 'text-white hover:bg-white/10' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            SFAO
        </button>
    </div>
</div>
