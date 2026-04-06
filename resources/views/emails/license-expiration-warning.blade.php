<x-mail::message>
    # Peringatan: Lisensi 3FLO Akan Kadaluarsa

    Halo {{ $customerName ?? 'Pengguna 3FLO' }},

    Kami ingin memberitahu bahwa lisensi 3FLO Anda ({{ $licenseKey }}) akan **kadaluarsa dalam {{ $daysRemaining }} hari**.

    ## Detail Lisensi

    - **Nomor Lisensi:** {{ $licenseKey }}
    - **Tanggal Kadaluarsa:** {{ $expiresAt }}
    @if($gracePeriodUntil)
    - **Grace Period Hingga:** {{ $gracePeriodUntil }}

    Anda masih memiliki waktu grace period untuk menggunakan sistem sebelum akses admin dikunci sepenuhnya.
    @endif

    ## Tindakan yang Diperlukan

    Untuk memastikan layanan Anda tidak terganggu, silakan segera perbarui lisensi Anda melalui dashboard LicenseHub atau hubungi layanan support kami.

    <x-mail::button :url="'https://license.3flo.net'" color="success">
        Buka LicenseHub
    </x-mail::button>

    ## Butuh Bantuan?

    Jika Anda memiliki pertanyaan atau membutuhkan bantuan, silakan hubungi tim support kami:

    <x-mail::button :url="'https://license.3flo.net/contact'" color="primary">
        Hubungi Support
    </x-mail::button>

    Terima kasih telah menggunakan 3FLO!

    **Tim 3FLO**
</x-mail::message>
