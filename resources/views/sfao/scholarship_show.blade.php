<h1 class="text-2xl font-bold mb-6">{{ $scholarship->name }}</h1>

<div class="mb-4 text-gray-700 dark:text-gray-300">
  Slots: {{ $scholarship->slots }} |
  Applied: {{ $scholarship->applications->count() }}
</div>

<table class="w-full border-collapse bg-white dark:bg-gray-800 shadow-md rounded-xl">
  <thead class="bg-gray-200 dark:bg-gray-700">
    <tr>
      <th class="p-3 text-left">#</th>
      <th class="p-3 text-left">Name</th>
      <th class="p-3 text-left">Email</th>
      <th class="p-3 text-left">Grant Count</th>
      <th class="p-3 text-left">Status</th>
      <th class="p-3 text-left">Actions</th>
    </tr>
  </thead>
  <tbody>
    @foreach($scholarship->applications as $i => $application)
      <tr class="border-b dark:border-gray-600">
        <td class="p-3">{{ $i+1 }}</td>
        <td class="p-3">{{ $application->user->name }}</td>
        <td class="p-3">{{ $application->user->email }}</td>
        <td class="p-3">
          <span class="inline-flex px-2 py-1 text-xs font-medium rounded {{ $application->getGrantCountBadgeColor() }}">
            {{ $application->getGrantCountDisplay() }}
          </span>
        </td>
        <td class="p-3">
          <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
            {{ $application->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
               ($application->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
               ($application->status === 'claimed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
               'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200')) }}">
            {{ ucfirst($application->status) }}
          </span>
        </td>
        <td class="p-3">
          <div class="flex space-x-2">
            @if($application->status === 'pending')
              <form method="POST" action="{{ route('sfao.applications.approve', $application->id) }}" class="inline">
                @csrf
                <button type="submit" class="text-green-600 hover:text-green-800 text-sm font-medium">
                  Approve
                </button>
              </form>
              <form method="POST" action="{{ route('sfao.applications.reject', $application->id) }}" class="inline">
                @csrf
                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                  Reject
                </button>
              </form>
            @elseif($application->status === 'approved')
              <form method="POST" action="{{ route('sfao.applications.claim', $application->id) }}" class="inline">
                @csrf
                <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                  Mark as Claimed
                </button>
              </form>
            @endif
          </div>
        </td>
      </tr>
    @endforeach
  </tbody>
</table>
