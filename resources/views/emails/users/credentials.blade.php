<x-mail::message>
# Hello, {{ $user->name }}!

Your admin account for **3FLO** has been successfully created. You can now access the admin dashboard to manage the platform contents.

**Login Details:**
- **Email:** {{ $user->email }}
- **Login URL:** [{{ $loginUrl }}]({{ $loginUrl }})

<x-mail::button :url="$loginUrl">
Login to Dashboard
</x-mail::button>

*Note: For security reasons, your password is not sent in this email. If you haven't received your password from the administrator, please use the "Forgot Password" feature on the login page.*

Thanks,<br>
{{ config('app.name') }} Team
</x-mail::message>
