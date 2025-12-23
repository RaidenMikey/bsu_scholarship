@component('mail::message')
# Grant Release Notification

Dear {{ $scholar->user->name }},

We are pleased to inform you that your grant for **{{ $scholarship->scholarship_name }}** is ready to be claimed.

Please take note of the following details for the grant release:

**Date:** {{ \Carbon\Carbon::parse($details['release_date'])->format('F d, Y') }}  
**Location:** {{ $details['location'] }}  
**Amount:** ₱{{ number_format($scholarship->grant_amount, 2) }}

**Instructions:**  
{{ $details['instructions'] }}

Please find the attached Grant Slip for your reference.

If you have any questions, please contact the Scholarship and Financial Assistance Office.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
