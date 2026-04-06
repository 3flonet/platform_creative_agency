<x-mail::message>
# 📬 New Inquiry Received

You have a new contact form submission on **{{ config('app.name') }}**.

<x-mail::panel>
**{{ $inquiry->name }}** — {{ $inquiry->email }}
</x-mail::panel>

| Field | Details |
|-------|---------|
| **Name** | {{ $inquiry->name }} |
| **Email** | {{ $inquiry->email }} |
| **Phone** | {{ $inquiry->phone }} |
| **Subject** | {{ $inquiry->subject ?? 'General Inquiry' }} |
| **Submitted** | {{ $inquiry->created_at->format('d M Y, H:i') }} |

**Message:**
> {{ $inquiry->message }}

<x-mail::button :url="config('app.url') . '/admin/contact-inquiries'">
View in Admin Panel
</x-mail::button>

Regards,<br>
{{ config('app.name') }} System
</x-mail::message>
