<x-filament-panels::page>
    @php($s = $this->getSummary())
    <div class="grid gap-4 md:grid-cols-3">
        <x-filament::section>
            <x-slot name="heading">القسائم</x-slot>
            <div class="space-y-1 text-sm">
                <div>إجمالي القسائم: <b>{{ $s['coupons'] }}</b></div>
                <div>القسائم النشطة: <b>{{ $s['active_coupons'] }}</b></div>
                <div>إجمالي الاستخدام: <b>{{ $s['used_coupons'] }}</b></div>
            </div>
        </x-filament::section>
        <x-filament::section>
            <x-slot name="heading">الإحالات</x-slot>
            <div class="space-y-1 text-sm">
                <div>إجمالي الإحالات: <b>{{ $s['referrals'] }}</b></div>
                <div>إحالات مكتملة: <b>{{ $s['done_referrals'] }}</b></div>
            </div>
        </x-filament::section>
        <x-filament::section>
            <x-slot name="heading">المكافآت</x-slot>
            <div class="space-y-1 text-sm">
                <div>مكافآت المُحيل: <b>{{ number_format($s['referrer_rewards'], 2) }}</b></div>
                <div>مكافآت المُحال: <b>{{ number_format($s['referred_rewards'], 2) }}</b></div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>

