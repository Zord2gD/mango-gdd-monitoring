<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kebun;
use App\Services\WeatherService;
use Illuminate\Support\Facades\Auth;

class WeatherController extends Controller
{
    protected WeatherService $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    /**
     * Display weather overview for all kebuns (with coordinates).
     */
    public function index()
    {
        if (Auth::user()->role === 'admin') {
            $kebuns = Kebun::with('user')->get();
        } else {
            $kebuns = Kebun::where('user_id', Auth::id())->get();
        }

        // Fetch weather for each kebun that has coordinates
        $weatherData = [];
        foreach ($kebuns as $kebun) {
            if ($kebun->latitude && $kebun->longitude) {
                $data = $this->weatherService->getWeatherData($kebun->latitude, $kebun->longitude);
                if ($data) {
                    $weatherData[$kebun->id] = $data;
                }
            }
        }

        return view('cuaca.index', compact('kebuns', 'weatherData'));
    }

    /**
     * API endpoint: Get today's temperature for a specific kebun.
     * Used by AJAX auto-fill in suhu create form.
     */
    public function fetchTemperature($kebunId)
    {
        $kebun = Kebun::findOrFail($kebunId);

        // Authorization check
        if (Auth::user()->role !== 'admin' && $kebun->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$kebun->latitude || !$kebun->longitude || $kebun->latitude == 0 || $kebun->longitude == 0) {
            return response()->json([
                'error' => 'Koordinat tidak tersedia'
            ], 400);
        }

        $data = $this->weatherService->getWeatherData($kebun->latitude, $kebun->longitude, 7);

        if (!$data) {
            return response()->json([
                'error' => 'Gagal mengambil data dari API cuaca. Silakan coba beberapa saat lagi.'
            ], 503);
        }

        $dates = array_column($data['daily'], 'date');
        $tmax  = array_column($data['daily'], 'tmax');
        $tmin  = array_column($data['daily'], 'tmin');

        return response()->json([
            'success'  => true,
            'kebun'    => $kebun->nama_kebun,
            'lokasi'   => $kebun->lokasi,
            'current'  => $data['current'],
            'daily'    => $data['daily'], // Added this so JS can read data.daily[0]
            'dates'    => $dates,
            'tmax'     => $tmax,
            'tmin'     => $tmin,
        ]);
    }

    /**
     * API endpoint: Sync historical data based on tanggal_berbunga.
     */
    public function syncHistorical($kebunId)
    {
        $kebun = Kebun::with('fase')->findOrFail($kebunId);

        // Authorization check
        if (Auth::user()->role !== 'admin' && $kebun->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$kebun->latitude || !$kebun->longitude) {
            return response()->json(['error' => 'Koordinat kebun belum diatur.'], 400);
        }

        if (!$kebun->fase || !$kebun->fase->tanggal_berbunga) {
            return response()->json(['error' => 'Tanggal berbunga belum diatur.'], 400);
        }

        $tanggalBerbunga = \Carbon\Carbon::parse($kebun->fase->tanggal_berbunga);
        $today = \Carbon\Carbon::today();

        if ($tanggalBerbunga->isAfter($today)) {
            return response()->json(['error' => 'Tanggal berbunga tidak valid (di masa depan).'], 400);
        }

        $diffDays = $tanggalBerbunga->diffInDays($today);
        
        if ($diffDays <= 0) {
            return response()->json(['message' => 'Tidak ada data masa lalu untuk disinkronkan. Tanggal berbunga adalah hari ini.'], 200);
        }

        // Open-Meteo free API max past_days is 92.
        $pastDays = min($diffDays, 92);

        $data = $this->weatherService->getWeatherData($kebun->latitude, $kebun->longitude, 1, $pastDays);

        if (!$data || empty($data['daily'])) {
            return response()->json(['error' => 'Gagal mengambil data riwayat dari API cuaca.'], 503);
        }

        $upsertData = [];
        $syncedCount = 0;
        foreach ($data['daily'] as $day) {
            $date = \Carbon\Carbon::parse($day['date']);
            
            // Only sync data between tanggal_berbunga and today
            if ($date->between($tanggalBerbunga, $today)) {
                $upsertData[] = [
                    'kebun_id' => $kebun->id,
                    'tanggal'  => $day['date'],
                    'tmin'     => $day['tmin'],
                    'tmax'     => $day['tmax'],
                    'gdd'      => $day['gdd'],
                ];
                $syncedCount++;
            }
        }

        if (!empty($upsertData)) {
            \App\Models\SuhuHarian::upsert(
                $upsertData,
                ['kebun_id', 'tanggal'],
                ['tmin', 'tmax', 'gdd']
            );

            \Illuminate\Support\Facades\Log::info("Data suhu historis berhasil di-upsert untuk Kebun ID: {$kebun->id}. Total data: {$syncedCount}");
            \Illuminate\Support\Facades\Cache::forget("gdd_total_{$kebun->id}");

            // Notification Dispatch Logic
            unset($kebun->total_gdd_db); // Ensure accessor triggers Cache/DB to get fresh calculation
            if ($kebun->is_siap_panen) {
                $updated = \App\Models\FaseTanaman::where('id', $kebun->fase->id)
                    ->whereNull('notifikasi_terakhir_dikirim_at')
                    ->update(['notifikasi_terakhir_dikirim_at' => now()]);

                if ($updated) {
                    $pengepulas = \App\Models\User::where('role', 'pengepul')->get();
                    \Illuminate\Support\Facades\Notification::send($pengepulas, new \App\Notifications\KebunSiapPanenNotification($kebun));
                    \Illuminate\Support\Facades\Log::info("Notifikasi Siap Panen masuk Queue untuk Kebun ID: {$kebun->id}");
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil sinkronisasi $syncedCount data suhu hari sebelumnya."
        ]);
    }
}
