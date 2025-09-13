<div x-show="tab === 'applications'" x-transition x-cloak class="px-4 py-6">
    <h2 class="text-2xl font-bold text-bsu-red mb-6">📋 Applicants with Uploaded Documents</h2>

    @if($students->isEmpty())
        <p class="text-gray-700">No applicants have uploaded documents yet.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow rounded-lg">
                <thead>
                    <tr class="bg-bsu-red text-white text-left">
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Last Uploaded</th>
                        <th class="px-4 py-3">Documents</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($students as $index => $student)
                        <tr>
                            <td class="px-4 py-3">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">{{ $student->name }}</td>
                            <td class="px-4 py-3">{{ $student->email }}</td>
                            <td class="px-4 py-3">
                                {{ \Carbon\Carbon::parse($student->last_uploaded)->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('sfao.viewDocuments', ['user_id' => $student->student_id]) }}"
                                   class="text-bsu-red hover:underline font-semibold">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
