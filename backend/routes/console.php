<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('sync:products')->everySixHours();
Schedule::command('deposit:timeout')->hourly();
Schedule::command('report:daily')->dailyAt('00:00');
Schedule::command('provider:balance')->hourly();
Schedule::command('logs:clean')->weekly();
