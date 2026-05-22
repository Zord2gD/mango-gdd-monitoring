<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WeatherService
{
    /**
     * Base URL for Open-Meteo API
     */
    protected string $baseUrl = 'https://api.open-meteo.com/v1/forecast';

    /**
     * WMO Weather Code descriptions (for weather icons/labels)
     */
    protected array $weatherCodes = [
        0  => ['label' => 'Cerah',              'icon' => '☀️'],
        1  => ['label' => 'Cerah Berawan',       'icon' => '🌤️'],
        2  => ['label' => 'Berawan Sebagian',    'icon' => '⛅'],
        3  => ['label' => 'Mendung',             'icon' => '☁️'],
        45 => ['label' => 'Berkabut',            'icon' => '🌫️'],
        48 => ['label' => 'Kabut Tebal',         'icon' => '🌫️'],
        51 => ['label' => 'Gerimis Ringan',      'icon' => '🌦️'],
        53 => ['label' => 'Gerimis Sedang',      'icon' => '🌦️'],
        55 => ['label' => 'Gerimis Lebat',       'icon' => '🌧️'],
        61 => ['label' => 'Hujan Ringan',        'icon' => '🌧️'],
        63 => ['label' => 'Hujan Sedang',        'icon' => '🌧️'],
        65 => ['label' => 'Hujan Lebat',         'icon' => '🌧️'],
        71 => ['label' => 'Salju Ringan',        'icon' => '🌨️'],
        73 => ['label' => 'Salju Sedang',        'icon' => '🌨️'],
        75 => ['label' => 'Salju Lebat',         'icon' => '❄️'],
        80 => ['label' => 'Hujan Lokal Ringan',  'icon' => '🌦️'],
        81 => ['label' => 'Hujan Lokal Sedang',  'icon' => '🌧️'],
        82 => ['label' => 'Hujan Lokal Lebat',   'icon' => '⛈️'],
        95 => ['label' => 'Badai Petir',         'icon' => '⛈️'],
        96 => ['label' => 'Badai + Hujan Es',    'icon' => '⛈️'],
        99 => ['label' => 'Badai Berat',         'icon' => '⛈️'],
    ];

    /**
     * Get current weather + 7-day daily forecast for a location.
     *
     * @param float $latitude
     * @param float $longitude
     * @param int   $forecastDays
     * @return array|null  Returns structured data or null on failure
     */
    public function getWeatherData(float $latitude, float $longitude, int $forecastDays = 7, int $pastDays = 0): ?array
    {
        // Untuk data forecast biasa (bukan historis), gunakan cache 30 menit.
        // Ini mencegah N+1 API call saat halaman /cuaca dibuka dengan banyak kebun.
        if ($pastDays === 0) {
            $cacheKey = sprintf('weather_%.4f_%.4f_%d', $latitude, $longitude, $forecastDays);
            return Cache::remember($cacheKey, 1800, function () use ($latitude, $longitude, $forecastDays, $pastDays) {
                return $this->fetchWeatherFromApi($latitude, $longitude, $forecastDays, $pastDays);
            });
        }

        // Data historis (sync) tidak di-cache karena bersifat satu kali
        return $this->fetchWeatherFromApi($latitude, $longitude, $forecastDays, $pastDays);
    }

    /**
     * Internal method: lakukan HTTP call ke Open-Meteo API.
     */
    protected function fetchWeatherFromApi(float $latitude, float $longitude, int $forecastDays, int $pastDays): ?array
    {
        try {
            $sslVerify = ! in_array(config('app.env'), ['local', 'testing']);
            $response = Http::withOptions(['verify' => $sslVerify])->timeout(10)->get($this->baseUrl, [
                'latitude'      => $latitude,
                'longitude'     => $longitude,
                'daily'         => 'temperature_2m_max,temperature_2m_min,precipitation_sum,weathercode',
                'current'       => 'temperature_2m,relative_humidity_2m,wind_speed_10m,weathercode',
                'timezone'      => 'Asia/Jakarta',
                'forecast_days' => $forecastDays,
                'past_days'     => $pastDays,
            ]);

            if ($response->failed()) {
                Log::warning('Open-Meteo API request failed', [
                    'status'    => $response->status(),
                    'latitude'  => $latitude,
                    'longitude' => $longitude,
                ]);
                return null;
            }

            $data = $response->json();

            return $this->formatResponse($data);
        } catch (\Exception $e) {
            Log::error('Open-Meteo API exception', [
                'message'   => $e->getMessage(),
                'latitude'  => $latitude,
                'longitude' => $longitude,
            ]);
            return null;
        }
    }

    /**
     * Get only today's Tmin and Tmax for a location.
     *
     * @param float $latitude
     * @param float $longitude
     * @return array|null  ['tmin' => float, 'tmax' => float, 'date' => string] or null
     */
    public function getTodayTemperature(float $latitude, float $longitude): ?array
    {
        try {
            $sslVerify = ! in_array(config('app.env'), ['local', 'testing']);
            $response = Http::withOptions(['verify' => $sslVerify])->timeout(10)->get($this->baseUrl, [
                'latitude'      => $latitude,
                'longitude'     => $longitude,
                'daily'         => 'temperature_2m_max,temperature_2m_min',
                'timezone'      => 'Asia/Jakarta',
                'forecast_days' => 1,
            ]);

            if ($response->failed()) {
                Log::warning('Open-Meteo API (today) failed', [
                    'status' => $response->status(),
                ]);
                return null;
            }

            $data = $response->json();
            $daily = $data['daily'] ?? null;

            if (!$daily || empty($daily['time'])) {
                return null;
            }

            return [
                'date' => $daily['time'][0],
                'tmin' => round($daily['temperature_2m_min'][0], 1),
                'tmax' => round($daily['temperature_2m_max'][0], 1),
            ];
        } catch (\Exception $e) {
            Log::error('Open-Meteo API (today) exception', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Format full API response into a clean structure.
     */
    protected function formatResponse(array $data): array
    {
        $result = [
            'latitude'  => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'timezone'  => $data['timezone'] ?? 'Asia/Jakarta',
            'current'   => null,
            'daily'     => [],
        ];

        // Current weather
        if (isset($data['current'])) {
            $code = $data['current']['weathercode'] ?? 0;
            $result['current'] = [
                'temperature'      => $data['current']['temperature_2m'] ?? null,
                'humidity'         => $data['current']['relative_humidity_2m'] ?? null,
                'wind_speed'       => $data['current']['wind_speed_10m'] ?? null,
                'weathercode'      => $code,
                'weather_label'    => $this->getWeatherLabel($code),
                'weather_icon'     => $this->getWeatherIcon($code),
            ];
        }

        // Daily forecast
        if (isset($data['daily'])) {
            $daily = $data['daily'];
            $count = count($daily['time'] ?? []);

            for ($i = 0; $i < $count; $i++) {
                $code = $daily['weathercode'][$i] ?? 0;
                $tmin = round($daily['temperature_2m_min'][$i] ?? 0, 1);
                $tmax = round($daily['temperature_2m_max'][$i] ?? 0, 1);
                $gdd  = max(0, round((($tmax + $tmin) / 2) - 10, 2));

                $result['daily'][] = [
                    'date'            => $daily['time'][$i],
                    'tmin'            => $tmin,
                    'tmax'            => $tmax,
                    'precipitation'   => round($daily['precipitation_sum'][$i] ?? 0, 1),
                    'weathercode'     => $code,
                    'weather_label'   => $this->getWeatherLabel($code),
                    'weather_icon'    => $this->getWeatherIcon($code),
                    'gdd'             => $gdd,
                ];
            }
        }

        return $result;
    }

    /**
     * Get human-readable label for a WMO weather code.
     */
    public function getWeatherLabel(int $code): string
    {
        return $this->weatherCodes[$code]['label'] ?? 'Tidak Diketahui';
    }

    /**
     * Get emoji icon for a WMO weather code.
     */
    public function getWeatherIcon(int $code): string
    {
        return $this->weatherCodes[$code]['icon'] ?? '🌡️';
    }
}
