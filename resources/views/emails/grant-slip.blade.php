@component('mail::message')
# Grant Release Notification

Dear {{ $scholar->user->name }},

We are pleased to inform you that your grant for **{{ $scholarship->scholarship_name }}** has been processed.

Please find the attached Grant Slip for your reference.

**Amount:** ₱{{ number_format($scholarship->grant_amount, 2) }}

If you have any questions, please contact the Scholarship and Financial Assistance Office.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
