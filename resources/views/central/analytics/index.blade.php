@php
  use Illuminate\Support\Facades\Session;
  use App\Models\User;

  if (!Session::has('user_id')) {
    Redirect::to('login')->send();
  }

  $user = User::with('campus')->find(session('user_id'));
  
  // Ensure we have $user before passing to view
  if (!$user) {
    Session::flush();
    Redirect::to('login')->send();
  }
@endphp

@extends('layouts.dashboard', ['user' => $user, 'title' => 'Central Dashboard'])

@section('styles')
  <style>
    /* Custom scrollbar styling - minimized and subtle */
    [x-cloak] { display: none !important; }
  </style>
@endsection

@section('sidebar-menu')
    @include('central.components.sidebar-menu')
@endsection

@section('navbar')
  <x-layout.navbar 
      title="Central Dashboard" 
      :user="$user" 
      :profile="true"
      :settings="false" 
      :logout="false" 
  />
@endsection

@section('content')
<div x-data="{
    tab: {{ json_encode(request()->query("tab")) }} || localStorage.getItem('activeTab') || 'all_scholarships',
    currentStatsCampus: 'all',
    showLogoutModal: false,
    initialParams: new URLSearchParams(window.location.search),
    
    getTabGroup(tab) {
        if (tab === 'all_scholarships' || tab === 'private_scholarships' || tab === 'government_scholarships') return 'scholarships';
        if (tab === 'sfao_reports') return 'reports';
        if (tab === 'all_statistics' || (tab.endsWith('_statistics') && tab !== 'all_statistics')) return 'statistics';
        if (tab === 'all_scholars' || tab === 'new_scholars' || tab === 'old_scholars') return 'scholars';
        if (tab === 'endorsed_applicants' || tab === 'rejected_applicants') return 'applications';
        if (tab === 'staff') return 'staff';
        return 'default';
    },

    normalizeTab(t) {
        const map = {
            'scholarships': 'all_scholarships',
            'scholarships-private': 'private_scholarships',
            'scholarships-government': 'government_scholarships',
            'scholars': 'all_scholars',
            'scholars-new': 'new_scholars',
            'scholars-old': 'old_scholars',
            'endorsed-applicants': 'endorsed_applicants',
            'rejected-applicants': 'rejected_applicants',
            'reports': 'sfao_reports',
            'statistics': 'all_statistics',
            'settings': 'account_settings'
        };
        return map[t] || t;
    },

    getGroupKeys(group) {
        const keys = {
            'scholarships': ['sort_by', 'sort_order', 'page_all', 'page_private', 'page_gov'],
            'reports': ['type', 'campus', 'sort', 'order', 'page_submitted', 'page_reviewed', 'page_approved', 'page_rejected', 'status'],
            'statistics': ['timePeriod', 'campus'],
            'scholars': ['page_scholars_all', 'page_scholars_new', 'page_scholars_old', 'sort_by', 'status', 'type'],
            'applications': ['sort_by', 'status_filter', 'campus_filter', 'scholarship_filter']
        };
        return keys[group] || [];
    },

    switchTab(newTab) {
        // 1. Identify Groups
        const currentGroup = this.getTabGroup(this.tab);
        const newGroup = this.getTabGroup(newTab);

        // 2. Save Current Group State
        if (currentGroup !== 'default') {
            const currentParams = new URLSearchParams(window.location.search);
            const groupKeys = this.getGroupKeys(currentGroup);
            const stateToSave = new URLSearchParams();
            
            groupKeys.forEach(key => {
                if (currentParams.has(key)) {
                    stateToSave.set(key, currentParams.get(key));
                }
            });
            localStorage.setItem('groupState_' + currentGroup, stateToSave.toString());
        }

        // 3. Restore New Group State
        let newParams = new URLSearchParams();
        if (newGroup !== 'default') {
            const savedState = localStorage.getItem('groupState_' + newGroup);
            if (savedState) {
                newParams = new URLSearchParams(savedState);
            }
        }
        
        // Always set the new tab
        newParams.set('tab', newTab);

        // Special Case: Remove 'campus' param for campus-specific statistics tabs
        if (newTab.endsWith('_statistics') && newTab !== 'all_statistics') {
            newParams.delete('campus');
        }

        // CLEANUP: Remove default values to keep URL clean
        const defaults = {
            'campus': 'all',
            'campus_filter': 'all',
            'timePeriod': 'all',
            'status_filter': 'all',
            'scholarship_filter': 'all',
            'sort_by': 'created_at',
            'sort_order': 'desc',
            'type': 'all',
            'status': 'all'
        };

        // Remove pagination parameters if they are page 1
        Array.from(newParams.keys()).forEach(key => {
             if (key.startsWith('page_') && newParams.get(key) === '1') {
                 newParams.delete(key);
             }
        });

        // Remove standard defaults
        for (const [key, def] of Object.entries(defaults)) {
            if (newParams.get(key) === def) {
                newParams.delete(key);
            }
        }

        // 4. Construct New URL
        const newUrl = new URL(window.location);
        newUrl.search = newParams.toString();

        // 5. Smart Switch Logic
        const initialParamsCopy = new URLSearchParams(this.initialParams);
        initialParamsCopy.delete('tab');
        
        const checkParams = new URLSearchParams(newParams);
        checkParams.delete('tab');

        if (checkParams.toString() === initialParamsCopy.toString() || newGroup === 'statistics') {
            this.tab = newTab;
            window.history.pushState({}, '', newUrl);
        } else {
            window.location.href = newUrl.toString();
        }
    }
}"
@change-stats-campus.window="currentStatsCampus = $event.detail"
x-init="
    $watch('tab', val => localStorage.setItem('activeTab', val));
    
    // Ensure initialParams is set correctly
    initialParams = new URLSearchParams(window.location.search);
    
    // Normalize tab for backward compatibility
    this.tab = this.normalizeTab(this.tab);
"
x-on:switch-tab.window="switchTab($event.detail)"
>

  <!-- Right Sidebar -->
  <aside class="fixed inset-y-0 right-0 w-64 bg-white dark:bg-gray-800 shadow-xl transform transition-transform duration-300 z-50 flex flex-col"
         :class="rightSidebarOpen ? 'translate-x-0' : 'translate-x-full'"
         @keydown.escape.window="rightSidebarOpen = false"
         x-cloak>
      <div class="h-full flex flex-col py-6 overflow-y-scroll custom-scrollbar">
        <div class="px-4 sm:px-6">
          <div class="flex items-start justify-between">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Profile</h2>
            <div class="ml-3 h-7 flex items-center">
              <button @click="rightSidebarOpen = false" class="bg-white dark:bg-gray-800 rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bsu-red">
                <span class="sr-only">Close panel</span>
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
        </div>
        <div class="mt-6 relative flex-1 px-4 sm:px-6">
          <!-- Profile Content -->
          <div class="flex flex-col items-center">
              <img src="{{ $user && $user->profile_picture ? asset('storage/profile_pictures/' . $user->profile_picture) . '?' . now()->timestamp : asset('images/default-avatar.png') }}"
                   alt="Profile Picture"
                   class="h-32 w-32 rounded-full border-4 border-gray-200 dark:border-gray-700 object-cover mb-4">
              <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $user?->name ?? 'Central User' }}</h3>
              <p class="text-sm text-bsu-red font-medium">Central Admin Staff</p>
              <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $user?->email ?? 'email@example.com' }}</p>
          </div>

          <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6 space-y-4">
              <button @click="switchTab('account_settings')" class="w-full flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  Account Settings
              </button>
              <button @click="showLogoutModal = true" class="w-full flex items-center px-4 py-3 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                  </svg>
                  Logout
              </button>
          </div>
        </div>
      </div>
  </aside>

    <!-- Toasts -->
    @if (session('success'))
      <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition.opacity class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40">
        <div class="bg-white dark:bg-gray-800 border border-green-400 text-green-700 dark:text-green-300 px-6 py-5 rounded-xl shadow-xl flex items-center space-x-4">
          <svg class="w-10 h-10 text-green-500 animate-bounce" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
          <div class="text-lg font-medium">{{ session('success') }}</div>
        </div>
      </div>
    @endif

    @if ($errors->any())
      <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition.opacity class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40">
        <div class="bg-white dark:bg-gray-800 border border-red-400 text-red-700 dark:text-red-300 px-6 py-5 rounded-xl shadow-xl flex items-center space-x-4">
          <svg class="w-10 h-10 text-red-500 animate-pulse" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
          <div class="text-lg font-medium">{{ $errors->first() }}</div>
        </div>
      </div>
    @endif

    <!-- Tabs -->
    @include('central.partials.tabs.scholarships')
    @include('central.partials.tabs.scholars', ['scholars' => $scholars])
    @include('central.partials.tabs.endorsed-applicants', ['endorsedApplicants' => $endorsedApplicants])
    @include('central.partials.tabs.rejected-applicants', ['rejectedApplicants' => $rejectedApplicants])
    @include('central.partials.tabs.reports')
    @include('central.partials.tabs.statistics')
    @include('central.partials.tabs.staff')
    @include('central.partials.tabs.settings')

    <!-- Logout Modal -->
    <div x-show="showLogoutModal" 
       x-cloak
       x-transition:enter="transition ease-out duration-200"
       x-transition:enter-start="opacity-0"
       x-transition:enter-end="opacity-100"
       x-transition:leave="transition ease-in duration-150"
       x-transition:leave-start="opacity-100"
       x-transition:leave-end="opacity-0"
       class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
       @click.self="showLogoutModal = false">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Confirm Logout</h3>
      <p class="text-gray-600 dark:text-gray-300 mb-6">Are you sure you want to logout?</p>
      <div class="flex justify-end gap-3">
        <button @click="showLogoutModal = false"
                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
          Cancel
        </button>
        <a href="{{ url('/logout') }}"
           onclick="localStorage.removeItem('activeTab'); localStorage.removeItem('groupState_scholarships'); localStorage.removeItem('groupState_reports'); localStorage.removeItem('groupState_statistics'); localStorage.removeItem('groupState_scholars'); localStorage.removeItem('groupState_applications');"
           class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
          Logout
        </a>
      </div>
    </div>
  </div>

</div>

<script>
    window.addEventListener("pageshow", function (event) {
      if (event.persisted) {
        window.location.reload();
      }
    });
</script>
@endsection
