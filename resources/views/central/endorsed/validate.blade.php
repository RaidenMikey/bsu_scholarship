@php
  use Illuminate\Support\Facades\Session;
  if (!Session::has('user_id') || session('role') !== 'central') {
    header('Location: ' . route('login'));
    exit;
  }
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Validate Endorsed Applicant</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 dark:text-white min-h-screen">

  <div class="max-w-6xl mx-auto p-4 md:p-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Validate Endorsed Applicant</h1>
      <a href="{{ route('central.dashboard') }}" class="px-4 py-2 text-sm bg-white hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg border border-gray-300 dark:border-gray-600 transition-colors">Back to Dashboard</a>
    </div>

    <!-- Applicant Profile Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <div class="text-sm text-gray-500 dark:text-gray-400">Student</div>
          <div class="text-xl font-semibold text-gray-900 dark:text-white">{{ $user->name }}</div>
          <div class="text-sm text-gray-600 dark:text-gray-300">{{ $user->email }}</div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
          <div>
            <div class="text-xs uppercase text-gray-500 dark:text-gray-400">Campus</div>
            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->campus->name ?? 'N/A' }}</div>
          </div>
          <div>
            <div class="text-xs uppercase text-gray-500 dark:text-gray-400">Program</div>
            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->form->program ?? 'N/A' }}</div>
          </div>
          <div>
            <div class="text-xs uppercase text-gray-500 dark:text-gray-400">Year Level</div>
            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->form->year_level ?? 'N/A' }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Scholarship & Application -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
      <div class="md:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="text-sm text-gray-500 dark:text-gray-400">Scholarship</div>
        <div class="text-xl font-semibold text-gray-900 dark:text-white">{{ $scholarship->scholarship_name }}</div>
        <div class="mt-3 grid grid-cols-2 gap-4 text-sm">
          <div>
            <div class="text-gray-500 dark:text-gray-400">Type</div>
            <div class="font-medium">{{ ucfirst($scholarship->scholarship_type) }}</div>
          </div>
          <div>
            <div class="text-gray-500 dark:text-gray-400">Priority</div>
            <div class="font-medium">{{ ucfirst($scholarship->priority_level) }}</div>
          </div>
          <div>
            <div class="text-gray-500 dark:text-gray-400">Grant Amount</div>
            <div class="font-medium">{{ $scholarship->grant_amount ? '₱' . number_format((float) $scholarship->grant_amount, 2) : 'TBD' }}</div>
          </div>
          <div>
            <div class="text-gray-500 dark:text-gray-400">Status</div>
            <div class="font-medium">{{ ucfirst($application->status) }}</div>
          </div>
        </div>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="text-sm text-gray-500 dark:text-gray-400">Academic</div>
        <div class="mt-2 grid grid-cols-2 gap-4 text-sm">
          <div>
            <div class="text-gray-500 dark:text-gray-400">GWA</div>
            <div class="font-medium">{{ $user->form->gwa ?? 'N/A' }}</div>
          </div>
          <div>
            <div class="text-gray-500 dark:text-gray-400">Allowance</div>
            <div class="font-medium">{{ isset($user->form->monthly_allowance) ? '₱' . number_format((float) $user->form->monthly_allowance, 2) : 'N/A' }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Documents -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Submitted Documents</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Size</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Evaluation</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($submittedDocuments as $doc)
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $doc->getDocumentCategoryDisplayName() }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $doc->document_name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $doc->getFileTypeDisplayName() }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $doc->getFileSizeFormatted() }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold {{ $doc->getEvaluationStatusBadgeColor() }}">
                    {{ $doc->getEvaluationStatusDisplayName() }}
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No submitted documents found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- Actions -->
    <div class="flex justify-end gap-3">
      <a href="{{ route('central.dashboard', ['tab' => 'endorsed-applicants']) }}" class="px-4 py-2 text-sm bg-white hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg border border-gray-300 dark:border-gray-600 transition-colors">Cancel</a>
    </div>
  </div>

</body>
</html>


