<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kebun;
use App\Models\SuhuHarian;
use App\Services\WeatherService;
use Illuminate\Support\Facades\Log;

class FetchWeatherData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather:fetch
                            {--kebun= : Fetch for a specific kebun ID only}
                            {--date=  : Specify date (Y-m-d), default today}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch daily weather data (Tmin, Tmax) from Open-Meteo API and calculate GDD for all farms';

    /**
     * Execute the console command.
     */
    public function handle(WeatherService $weatherService): int
    {
        $date = $this->option('date') ?: now()->format('Y-m-d');
        $kebunId = $this->option('kebun');

        $this->info("🌡️  Fetching weather data for date: {$date}");
        $this->newLine();

        // Get kebuns with coordinates
        $query = Kebun::whereNotNull('latitude')->whereNotNull('longitude');
        if ($kebunId) {
            $query->where('id', $kebunId);
        }
        $kebuns = $query->get();

        if ($kebuns->isEmpty()) {
            $this->warn('⚠️  No farms found with latitude/longitude coordinates.');
            return self::SUCCESS;
        }

        $this->info("📍 Found {$kebuns->count()} farm(s) with coordinates.");
        $this->newLine();

        $successCount = 0;
        $skipCount = 0;
        $errorCount = 0;

        foreach ($kebuns as $kebun) {
            $this->line("  Processing: {$kebun->nama_kebun} ({$kebun->lokasi})");

            // Fetch temperature from API
            $tempData = $weatherService->getTodayTemperature($kebun->latitude, $kebun->longitude);

            if (!$tempData) {
                $this->error("    ❌ API failed for {$kebun->nama_kebun}");
                Log::error('Weather fetch failed', [
                    'kebun_id' => $kebun->id,
                    'kebun'    => $kebun->nama_kebun,
                    'lat'      => $kebun->latitude,
                    'lon'      => $kebun->longitude,
                ]);
                $errorCount++;
                continue;
            }

            $tmin = $tempData['tmin'];
            $tmax = $tempData['tmax'];

            // Ensure tmin <= tmax
            if ($tmin > $tmax) {
                [$tmin, $tmax] = [$tmax, $tmin];
            }

            // Calculate GDD
            $gdd = max(0, round((($tmax + $tmin) / 2) - 10, 2));

            // Store to database using Upsert (Idempotency)
            SuhuHarian::upsert(
                [
                    ['kebun_id' => $kebun->id, 'tanggal'  => $date, 'tmin' => $tmin, 'tmax' => $tmax, 'gdd' => $gdd]
                ],
                ['kebun_id', 'tanggal'],
                ['tmin', 'tmax', 'gdd']
            );

            // Invalidate Cache to ensure UI gets the fresh GDD total
            \Illuminate\Support\Facades\Cache::forget("gdd_total_{$kebun->id}");

            // Notification Dispatch Logic
            if ($kebun->fase) {
                unset($kebun->total_gdd_db); // Ensure fresh calculation
                if ($kebun->is_siap_panen) {
                    $updated = \App\Models\FaseTanaman::where('id', $kebun->fase->id)
                        ->whereNull('notifikasi_terakhir_dikirim_at')
                        ->update(['notifikasi_terakhir_dikirim_at' => now()]);

                    if ($updated) {
                        $pengepulas = \App\Models\User::where('role', 'pengepul')->get();
                        \Illuminate\Support\Facades\Notification::send($pengepulas, new \App\Notifications\KebunSiapPanenNotification($kebun));
                        $this->info("    🔔 Notifikasi Siap Panen masuk Queue!");
                        Log::info("Notifikasi Siap Panen masuk Queue untuk Kebun ID: {$kebun->id} via Cron");
                    }
                }
            }

            $this->info("    ✅ Upserted: Tmin={$tmin}°C, Tmax={$tmax}°C, GDD={$gdd}");
            $successCount++;
        }

        $this->newLine();
        $this->info("═══════════════════════════════════════");
        $this->info("  ✅ Success: {$successCount}  ⏭️ Skipped: {$skipCount}  ❌ Errors: {$errorCount}");
        $this->info("═══════════════════════════════════════");

        Log::info('Weather fetch completed', [
            'date'    => $date,
            'success' => $successCount,
            'skipped' => $skipCount,
            'errors'  => $errorCount,
        ]);

        return self::SUCCESS;
    }
}
