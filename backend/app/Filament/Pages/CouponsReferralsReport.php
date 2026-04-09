<?php

namespace App\Filament\Pages;

use App\Models\Coupon;
use App\Models\Referral;
use Filament\Pages\Page;

class CouponsReferralsReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'التقارير';
    protected static ?string $navigationLabel = 'تقرير القسائم والإحالات';
    protected static string $view = 'filament.pages.coupons-referrals-report';

    public function getSummary(): array
    {
        $coupons = Coupon::query()->count();
        $activeCoupons = Coupon::query()->where('active', true)->count();
        $usedCoupons = Coupon::query()->sum('used_count');

        $referrals = Referral::query()->count();
        $doneReferrals = Referral::query()->where('status', 'done')->count();
        $referrerRewards = (float) Referral::query()->sum('reward_referrer');
        $referredRewards = (float) Referral::query()->sum('reward_referred');

        return [
            'coupons' => (int) $coupons,
            'active_coupons' => (int) $activeCoupons,
            'used_coupons' => (int) $usedCoupons,
            'referrals' => (int) $referrals,
            'done_referrals' => (int) $doneReferrals,
            'referrer_rewards' => $referrerRewards,
            'referred_rewards' => $referredRewards,
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->check() && (auth()->user()->role ?? null) === 'admin';
    }
}

