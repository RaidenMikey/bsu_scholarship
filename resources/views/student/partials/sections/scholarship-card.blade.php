@props([
    'scholarship',
    'applied' => false,
    'gwa' => null
])

<x-student.ui.card 
    :variant="$applied ? 'applied' : 'default'"
    class="relative"
>
    <x-slot name="title">
        {{ $scholarship->scholarship_name }}
    </x-slot>
    
    <x-slot name="subtitle">
        {{ $scholarship->description }}
    </x-slot>

    <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1 mb-12">
        <p><strong>Required GWA:</strong> {{ $scholarship->minimum_gwa }}</p>
        <p><strong>Deadline:</strong> {{ \Carbon\Carbon::parse($scholarship->deadline)->format('M d, Y') }}</p>
        <p><strong>Slots:</strong> {{ $scholarship->slots_available ?? 0 }}</p>

        @if($scholarship->grant_amount)
            <p><strong>Grant Amount:</strong> ₱{{ number_format($scholarship->grant_amount, 2) }}</p>
        @endif

        <p><strong>Renewal Allowed:</strong> {{ $scholarship->renewal_allowed ? 'Yes' : 'No' }}</p>
    </div>

    <!-- Action Buttons -->
    <div class="absolute bottom-4 left-4">
        @if($applied)
            <x-student.ui.button 
                variant="secondary" 
                onclick="openUnapplyModal('{{ $scholarship->id }}')"
            >
                ❌ Unapply
            </x-student.ui.button>
        @else
            <form method="GET" action="{{ route('student.upload-documents', ['scholarship_id' => $scholarship->id]) }}">
                <x-student.ui.button type="submit">
                    🎓 Apply
                </x-student.ui.button>
            </form>
        @endif
    </div>
</x-student.ui.card>

