@php
    $setting = \App\Models\Setting::general();
    $primary = $setting->primary_color ?? '#16a34a';
    $secondary = $setting->secondary_color ?? '#0f172a';
    $cardStyle = $setting->card_style ?? 'rounded';
@endphp
<style>
    :root {
        --xpay-primary: {{ $primary }};
        --xpay-secondary: {{ $secondary }};
    }
    html { direction: rtl; }
    .fi-sidebar, .fi-main { direction: rtl; }
    .fi-card, .fi-section {
        border-radius: {{ $cardStyle === 'sharp' ? '0.25rem' : '1rem' }};
    }
</style>
<script>
    document.documentElement.setAttribute('dir', 'rtl');
</script>

