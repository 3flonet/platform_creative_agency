@php
    $siteName = \App\Models\Setting::get('site_name', '3FLO Engine');
    $customerName = \App\Models\Setting::get('license_customer_name');
@endphp

<div style="padding: 1.5rem 2rem; display: flex; justify-content: flex-end; align-items: center; gap: 12px; opacity: 0.6;">
    <p style="font-size: 10px; font-weight: 600; color: #64748b; margin: 0; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 8px;">
        &copy; {{ date('Y') }} {{ $siteName }}
        
        @if($customerName)
            <span style="opacity: 0.3;">|</span>
            <span style="display: flex; align-items: center; gap: 6px; padding: 2px 8px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 9999px;">
                <span style="width: 4px; height: 4px; background: #10b981; border-radius: 50%;"></span>
                <span style="color: #10b981; font-weight: 800; font-size: 8px;">Licensed to: {{ $customerName }}</span>
            </span>
        @endif

        <span style="opacity: 0.3;">|</span>
        <a href="https://license.3flo.net/" target="_blank" style="text-decoration: none; font-weight: 900; color: #3b82f6; letter-spacing: 0.1em; font-size: 9px; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
            License Hub Powered
        </a>
    </p>
</div>
