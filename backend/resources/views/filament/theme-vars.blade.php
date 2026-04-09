@php
    $setting = \App\Models\Setting::general();
    $primary = $setting->primary_color ?? '#1E40AF';
    $secondary = $setting->secondary_color ?? '#111827';
    $cardStyle = $setting->card_style ?? 'rounded';
@endphp
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap');

    :root {
        --xpay-blue-1: #0052CC;
        --xpay-blue-2: #1E40AF;
        --xpay-orange-1: #F97316;
        --xpay-orange-2: #EA580C;
        --xpay-soft-white: #F9FAFB;
        --xpay-light-gray: #F3F4F6;
        --xpay-success: #22C55E;
        --xpay-error: #EF4444;
        --xpay-warning: #F59E0B;
        --xpay-dark-slate: #111827;
        --xpay-mid-gray: #6B7280;
        --xpay-border: #E5E7EB;
        --xpay-primary: {{ $primary }};
        --xpay-secondary: {{ $secondary }};
    }

    html { direction: rtl; }
    body, .fi-body, .fi-main, .fi-sidebar { font-family: 'Cairo', sans-serif !important; direction: rtl; }

    .fi-sidebar {
        background: linear-gradient(180deg, var(--xpay-dark-slate) 0%, #0B1220 100%);
        border-inline-end: 1px solid rgba(229,231,235,0.12);
    }
    .fi-sidebar-nav-item-button.fi-active {
        background: linear-gradient(90deg, var(--xpay-blue-2), var(--xpay-blue-1)) !important;
        color: #fff !important;
    }
    .fi-topbar {
        border-bottom: 1px solid var(--xpay-border);
        background: #ffffff;
    }
    .fi-btn-color-primary {
        --c-600: 30 64 175 !important;
        --c-500: 0 82 204 !important;
    }
    .fi-btn-color-success {
        --c-600: 34 197 94 !important;
        --c-500: 34 197 94 !important;
    }
    .fi-btn-color-danger {
        --c-600: 239 68 68 !important;
        --c-500: 239 68 68 !important;
    }
    .fi-badge-color-warning {
        --c-600: 245 158 11 !important;
    }

    .fi-card, .fi-section {
        border-radius: {{ $cardStyle === 'sharp' ? '0.25rem' : '1rem' }};
        border: 1px solid var(--xpay-border);
    }
</style>
<script>
    document.documentElement.setAttribute('dir', 'rtl');
</script>

