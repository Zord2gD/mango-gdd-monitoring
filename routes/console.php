<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks (CRON)
|--------------------------------------------------------------------------
|
| Fetch weather data daily at 06:00 WIB for all farms with coordinates.
| To activate, add this to crontab:
| * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
|
*/
Schedule::command('weather:fetch')->dailyAt('06:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/weather-fetch.log'));

// Enterprise Cleanup: Hapus notifikasi yang lebih tua dari 3 bulan agar DB tidak bengkak
Schedule::call(function () {
    $deleted = \Illuminate\Support\Facades\DB::table('notifications')
        ->where('created_at', '<', now()->subMonths(3))
        ->delete();
    
    if ($deleted > 0) {
        \Illuminate\Support\Facades\Log::info("Membuang {$deleted} notifikasi lama dari database (Cleanup Routine).");
    }
})->weeklyOn(0, '02:00')->timezone('Asia/Jakarta');
