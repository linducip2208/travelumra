<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

\Illuminate\Support\Facades\Schedule::command('bookings:escalate-overdue')->hourly();
\Illuminate\Support\Facades\Schedule::command('notifications:send-reminders')->dailyAt('08:00');
\Illuminate\Support\Facades\Schedule::command('db:backup')->dailyAt('02:00');
\Illuminate\Support\Facades\Schedule::command('broadcasts:send')->everyFiveMinutes();
\Illuminate\Support\Facades\Schedule::command('queue:prune-failed --hours=168')->daily();
