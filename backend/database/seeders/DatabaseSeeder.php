<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use App\Models\OrderMessageTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@xpay.com'],
            [
                'telegram_id' => 0,
                'username' => 'xpay_admin',
                'password' => Hash::make('password'),
                'balance' => ['USD' => 0, 'SYP' => 0],
                'role' => 'admin',
                'is_banned' => false,
            ]
        );

        Setting::general();

        $templates = [
            [
                'key' => 'order_success',
                'title' => 'نجاح الطلب',
                'body' => '✅ تم تنفيذ طلبك بنجاح. رقم الطلب: {order_number} | المبلغ: {amount_usd} USD',
            ],
            [
                'key' => 'order_failed',
                'title' => 'فشل الطلب',
                'body' => '❌ تعذر تنفيذ الطلب رقم {order_number}. تم إرجاع الرصيد لمحفظتك.',
            ],
            [
                'key' => 'deposit_approved',
                'title' => 'قبول الإيداع',
                'body' => '✅ تم قبول الإيداع. المبلغ: {amount_usd} USD / {amount_syp} SYP',
            ],
            [
                'key' => 'deposit_rejected',
                'title' => 'رفض الإيداع',
                'body' => '❌ تم رفض الإيداع. يرجى التواصل مع الدعم عند الحاجة.',
            ],
        ];
        foreach ($templates as $tpl) {
            OrderMessageTemplate::query()->updateOrCreate(['key' => $tpl['key']], $tpl + ['active' => true]);
        }
    }
}
