<div x-cloak data-applications="{{ json_encode($applicationTracking) }}">
  <div class="w-full mx-auto p-6 bg-white dark:bg-gray-800 shadow-xl rounded-2xl border-t-4 border-bsu-red transition duration-300">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6">
      <h2 class="text-3xl font-bold text-bsu-red mb-4 lg:mb-0 border-b-2 border-bsu-redDark pb-2">🎓 My Scholarship Applications</h2>
      
      <!-- Sorting Controls -->
      <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
        <div class="flex items-center space-x-2">
          <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Sort by:</label>
          <select x-model="sortBy" @change="sortApplications()" 
                  class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-bsu-red focus:border-bsu-red">
            <option value="created_at">Date Applied</option>
            <option value="scholarship_name">Scholarship Name</option>
            <option value="status">Status</option>
            <option value="grant_amount">Grant Amount</option>
            <option value="submission_deadline">Deadline</option>
          </select>
        </div>
        
        <div class="flex items-center space-x-2">
          <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Order:</label>
          <select x-model="sortOrder" @change="sortApplications()" 
                  class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-bsu-red focus:border-bsu-red">
            <option value="desc">Newest First</option>
            <option value="asc">Oldest First</option>
          </select>
        </div>
        
        <div class="flex items-center space-x-2">
          <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Filter:</label>
          <select x-model="statusFilter" @change="filterApplications()" 
                  class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-bsu-red focus:border-bsu-red">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="claimed">Claimed</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-3 bg-blue-100 dark:bg-blue-700 rounded-lg">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Applications</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white" x-text="applications.length"></p>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-3 bg-green-100 dark:bg-green-700 rounded-lg">
            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Approved</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white" x-text="applications.filter(app => app.status === 'approved').length"></p>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 dark:from-yellow-900 dark:to-yellow-800 rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-3 bg-yellow-100 dark:bg-yellow-700 rounded-lg">
            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white" x-text="applications.filter(app => app.status === 'pending').length"></p>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-3 bg-blue-100 dark:bg-blue-700 rounded-lg">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Claimed</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white" x-text="applications.filter(app => app.status === 'claimed').length"></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Applications Count -->
    <div class="mb-6">
      <p class="text-sm text-gray-600 dark:text-gray-400">
        Showing <span x-text="filteredApplications.length"></span> of <span x-text="applications.length"></span> applications
      </p>
    </div>

    @if ($applicationTracking->isEmpty())
      <div class="text-center py-12">
        <div class="w-24 h-24 mx-auto mb-4 text-gray-400">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Applications Yet</h3>
        <p class="text-gray-600 dark:text-gray-300 mb-4">You haven't applied to any scholarships yet.</p>
        <a href="{{ route('student.dashboard') }}" 
           class="inline-flex items-center px-4 py-2 bg-bsu-red hover:bg-bsu-redDark text-white font-medium rounded-lg transition">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Browse Scholarships
        </a>
      </div>
    @else
       <!-- Applications Grid -->
       <div x-data="applicationData()" x-init="init()" class="space-y-6">
         <template x-for="application in filteredApplications" :key="application.id">
          <div class="bg-gradient-to-r from-white to-gray-50 dark:from-gray-800 dark:to-gray-700 rounded-xl border border-gray-200 dark:border-gray-600 shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden">
            <!-- Application Header -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-600">
              <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div class="flex-1">
                  <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2" x-text="application.scholarship.scholarship_name"></h3>
                  <p class="text-gray-600 dark:text-gray-300 text-sm mb-3" x-text="application.scholarship.description"></p>
                  
                  <!-- Scholarship Details -->
                  <div class="flex flex-wrap gap-4 text-sm">
                    <div class="flex items-center text-gray-600 dark:text-gray-300">
                      <svg class="w-4 h-4 mr-1 text-bsu-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                      </svg>
                      <span x-text="application.scholarship.grant_amount_formatted"></span>
                    </div>
                    
                    <div class="flex items-center text-gray-600 dark:text-gray-300">
                      <svg class="w-4 h-4 mr-1 text-bsu-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                      </svg>
                      <span x-text="formatDate(application.scholarship.submission_deadline)"></span>
                    </div>
                    
                    <div class="flex items-center text-gray-600 dark:text-gray-300" x-show="application.scholarship.days_remaining !== null">
                      <svg class="w-4 h-4 mr-1 text-bsu-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      <span x-text="application.scholarship.days_remaining + ' days remaining'"></span>
                    </div>
                  </div>
                </div>
                
                <!-- Status and Type Badges -->
                <div class="flex flex-col sm:flex-row gap-2">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" 
                        :class="application.scholarship.status_badge.color" 
                        x-text="application.scholarship.status_badge.text"></span>
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" 
                </div>
              </div>
            </div>
            
            <!-- Application Footer -->
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-600">
              <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="text-sm text-gray-600 dark:text-gray-300">
                  <span class="font-medium">Applied:</span> <span x-text="formatDate(application.created_at)"></span>
                  <span class="mx-2">•</span>
                  <span class="font-medium">Last Updated:</span> <span x-text="formatDate(application.updated_at)"></span>
                </div>
                
              </div>
            </div>
          </div>
         </template>
       </div>
     @endif
   </div>

 </div>

<script>
function applicationData() {
  return {
    applications: [],
    filteredApplications: [],
    sortBy: 'created_at',
    sortOrder: 'desc',
    statusFilter: '',
    
    init() {
      // Get data from the data attribute
      const dataElement = document.querySelector('[data-applications]');
      this.applications = dataElement ? JSON.parse(dataElement.dataset.applications) : [];
      this.filteredApplications = [...this.applications];
      this.sortApplications();
    },
    
    sortApplications() {
      this.filteredApplications.sort((a, b) => {
        let aValue, bValue;
        
        switch(this.sortBy) {
          case 'scholarship_name':
            aValue = a.scholarship.scholarship_name.toLowerCase();
            bValue = b.scholarship.scholarship_name.toLowerCase();
            break;
          case 'status':
            aValue = a.status;
            bValue = b.status;
            break;
          case 'grant_amount':
            aValue = a.scholarship.grant_amount || 0;
            bValue = b.scholarship.grant_amount || 0;
            break;
          case 'submission_deadline':
            aValue = new Date(a.scholarship.submission_deadline);
            bValue = new Date(b.scholarship.submission_deadline);
            break;
          case 'type':
            aValue = a.type;
            bValue = b.type;
            break;
          default:
            aValue = new Date(a.created_at);
            bValue = new Date(b.created_at);
        }
        
        if (this.sortOrder === 'asc') {
          return aValue > bValue ? 1 : -1;
        } else {
          return aValue < bValue ? 1 : -1;
        }
      });
    },
    
    filterApplications() {
      if (this.statusFilter === '') {
        this.filteredApplications = [...this.applications];
      } else {
        this.filteredApplications = this.applications.filter(app => app.status === this.statusFilter);
      }
      this.sortApplications();
    },
    
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    },
    
  }
}
</script>
