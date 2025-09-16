@props(['type' => 'info', 'message'])

@php
$styles = [
    'success' => 'text-green-600 bg-green-100 border-green-400 dark:text-green-400 dark:bg-green-900 dark:border-green-600',
    'error' => 'text-red-600 bg-red-100 border-red-400 dark:text-red-400 dark:bg-red-900 dark:border-red-600',
    'warning' => 'text-orange-600 bg-orange-100 border-orange-400 dark:text-orange-400 dark:bg-orange-900 dark:border-orange-600',
    'info' => 'text-blue-600 bg-blue-100 border-blue-400 dark:text-blue-400 dark:bg-blue-900 dark:border-blue-600',
];

$icons = [
    'success' => '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>',
    'error' => '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>',
    'warning' => '<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>',
    'info' => '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>',
];
@endphp

<div x-data="{ show: true }" x-show="show" x-transition
     class="{{ $styles[$type] }} text-sm mb-4 border p-3 rounded-lg" role="alert">
  <div class="flex items-center">
    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
      {!! $icons[$type] !!}
    </svg>
    <span>{{ $message }}</span>
  </div>
</div>
