<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
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
    }
}
