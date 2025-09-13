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
      <th class="p-3 text-left">Status</th>
    </tr>
  </thead>
  <tbody>
    @foreach($scholarship->applications as $i => $application)
      <tr class="border-b dark:border-gray-600">
        <td class="p-3">{{ $i+1 }}</td>
        <td class="p-3">{{ $application->user->name }}</td>
        <td class="p-3">{{ $application->user->email }}</td>
        <td class="p-3">{{ ucfirst($application->status) }}</td>
      </tr>
    @endforeach
  </tbody>
</table>
