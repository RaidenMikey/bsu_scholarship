<div x-show="tab === 'applicants'" x-cloak x-data="{ showModal: false, showFormModal: false, selectedApp: null }">
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold text-bsu-red mb-6">SFAO-Approved Applicants</h1>
        <p class="text-gray-600 mb-6">These applications have been reviewed and approved by SFAO administrators.</p>

        @if ($applications->isEmpty())
            <div class="text-center py-12">
                <div class="text-gray-400 text-6xl mb-4">📋</div>
                <h3 class="text-xl font-medium text-gray-900 mb-2">No SFAO-approved applications</h3>
                <p class="text-gray-600">There are currently no applications that have been approved by SFAO administrators.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300 shadow-lg rounded-lg">
                    <thead class="bg-bsu-red text-white">
                        <tr>
                            <th class="px-4 py-3 text-left">#</th>
                            <th class="px-4 py-3 text-left">Name</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-left">Scholarship</th>
                            <th class="px-4 py-3 text-left">Applicant Type</th>
                            <th class="px-4 py-3 text-left">Grant Count</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Applied At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($applications as $index => $application)
                        <tr 
                            class="border-t hover:bg-gray-100 transition cursor-pointer"
                            @click="selectedApp = {{ $application->toJson() }}; showModal = true"
                        >
                            <td class="px-4 py-2">{{ $index + 1 }}</td>
                            <td class="px-4 py-2">{{ $application->user->name ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $application->user->email ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $application->scholarship->scholarship_name ?? '-' }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded {{ $application->getApplicantTypeBadgeColor() }}">
                                    {{ $application->getApplicantTypeDisplayName() }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded {{ $application->getGrantCountBadgeColor() }}">
                                    {{ $application->getGrantCountDisplay() }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $application->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                       ($application->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                       ($application->status === 'claimed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                                       'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200')) }}">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2">{{ $application->created_at?->format('M d, Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Applicant Details Modal -->
    <div 
        x-show="showModal" 
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        x-transition 
        x-cloak
    >
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-xl relative">
            <button @click="showModal = false" class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-2xl">
                &times;
            </button>

            <h2 class="text-2xl font-bold mb-4 text-bsu-red">Applicant Details</h2>

            <template x-if="selectedApp">
                <div class="space-y-3">
                    <p><strong>Name:</strong> <span x-text="selectedApp.user.name"></span></p>
                    <p><strong>Email:</strong> <span x-text="selectedApp.user.email"></span></p>
                    <p><strong>Scholarship:</strong> <span x-text="selectedApp.scholarship.scholarship_name"></span></p>
                    <p><strong>Applicant Type:</strong> 
                        <span x-text="selectedApp.type === 'new' ? 'New Applicant' : 'Continuing Applicant'" 
                              :class="selectedApp.type === 'new' ? 'text-blue-600 font-semibold' : 'text-green-600 font-semibold'">
                        </span>
                    </p>
                    <p><strong>Grant Count:</strong> 
                        <span x-text="selectedApp.grant_count <= 0 ? 'No grants received' : (selectedApp.grant_count === 1 ? '1st grant' : selectedApp.grant_count + 'th grant')" 
                              :class="selectedApp.grant_count <= 0 ? 'text-gray-600' : 'text-orange-600 font-semibold'">
                        </span>
                    </p>
                    <p><strong>Status:</strong> <span x-text="selectedApp.status"></span></p>
                    <p><strong>Applied At:</strong> <span x-text="new Date(selectedApp.created_at).toLocaleDateString()"></span></p>
                </div>
            </template>

            <div class="flex flex-col items-center mt-6 space-y-3">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 w-full">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-blue-800 font-medium">This application has been approved by SFAO</span>
                    </div>
                </div>
                <form method="POST" :action="'/central/applications/' + selectedApp.id + '/claim'" class="w-2/3">
                    @csrf
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Mark as Claimed
                    </button>
                </form>
            </div>
        </div>
    </div>

    
</div>
