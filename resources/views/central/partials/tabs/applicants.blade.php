<div x-show="tab === 'applicants'" x-cloak x-data="{ showModal: false, showFormModal: false, selectedApp: null }">
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold text-bsu-red mb-6">All Scholarship Applicants</h1>

        @if ($applications->isEmpty())
            <p class="text-gray-600">No applications found.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300 shadow-lg rounded-lg">
                    <thead class="bg-bsu-red text-white">
                        <tr>
                            <th class="px-4 py-3 text-left">#</th>
                            <th class="px-4 py-3 text-left">Name</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-left">Scholarship</th>
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
                            <td class="px-4 py-2">{{ $application->created_at->format('M d, Y') }}</td>
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
                    <p><strong>Status:</strong> <span x-text="selectedApp.status"></span></p>
                    <p><strong>Applied At:</strong> <span x-text="new Date(selectedApp.created_at).toLocaleDateString()"></span></p>
                </div>
            </template>

            <div class="flex flex-col items-center mt-6 space-y-3">
                <button 
                    @click="showModal = false; showFormModal = true" 
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-2/3 text-center"
                >
                    View Application Form
                </button>
                <form method="POST" :action="'/applications/' + selectedApp.id + '/accept'" class="w-2/3">
                    @csrf
                    <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Accept
                    </button>
                </form>
                <form method="POST" :action="'/applications/' + selectedApp.id + '/reject'" class="w-2/3">
                    @csrf
                    <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Reject
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- View Application Form Modal -->
    <div 
        x-show="showFormModal" 
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        x-transition 
        x-cloak
    >
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-4xl relative overflow-y-auto max-h-[90vh]">
            <button @click="showFormModal = false" class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-2xl">
                &times;
            </button>

            <h2 class="text-2xl font-bold mb-4 text-bsu-red">Application Form</h2>

        </div>
    </div>
    
</div>
