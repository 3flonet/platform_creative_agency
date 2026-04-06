<x-mail::message>
# Thank You, {{ $inquiry->name }}! 🙏

We have received your message and will get back to you as soon as possible.

---

**Here's a summary of your inquiry:**

| Field | Details |
|-------|---------|
| **Name** | {{ $inquiry->name }} |
| **Email** | {{ $inquiry->email }} |
| **Phone** | {{ $inquiry->phone }} |
| **Subject** | {{ $inquiry->subject ?? 'General Inquiry' }} |

**Your Message:**
> {{ $inquiry->message }}

---

We typically respond within **1–2 business days**. If your matter is urgent, feel free to reach us directly.

<x-mail::button :url="config('app.url')">
Visit Our Website
</x-mail::button>

Warm regards,<br>
**{{ config('app.name') }} Team**
</x-mail::message>
