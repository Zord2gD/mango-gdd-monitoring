@extends('layouts.dashboard')

@section('content')

<style>
    .weather-hero {
        background: linear-gradient(135deg, #0f766e 0%, #065f46 50%, #1e3a2f 100%);
        border-radius: 20px;
        padding: 32px;
        color: #fff;
        margin-bottom: 28px;
        position: relative;
        overflow: hidden;
    }
    .weather-hero::before {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(74,222,128,0.2) 0%, transparent 70%);
        border-radius: 50%;
    }
    .weather-hero::after {
        content: '';
        position: absolute;
        bottom: -40px;
        left: 30%;
        width: 150px;
        height: 150px;
        background: radial-gradient(circle, rgba(56,189,248,0.15) 0%, transparent 70%);
        border-radius: 50%;
    }
    .weather-hero h2 {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 6px;
        position: relative;
        z-index: 1;
    }
    .weather-hero p {
        font-size: 13px;
        color: #a7f3d0;
        position: relative;
        z-index: 1;
    }
    .weather-hero .api-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255,255,255,0.12);
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        color: #d1fae5;
        margin-top: 12px;
        backdrop-filter: blur(6px);
        position: relative;
        z-index: 1;
    }

    .weather-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: 22px;
        margin-bottom: 28px;
    }

    .weather-card {
        background: #fff;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 2px 16px rgba(0,0,0,0.06);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        border: 1px solid #f0f4f1;
    }
    .weather-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(0,0,0,0.1);
    }
    .weather-card-header {
        background: linear-gradient(135deg, #ecfdf5, #f0fdf4);
        padding: 20px 22px 16px;
        border-bottom: 1px solid #d1fae5;
    }
    .weather-card-header .kebun-name {
        font-size: 16px;
        font-weight: 700;
        color: #14532d;
        margin-bottom: 3px;
    }
    .weather-card-header .kebun-meta {
        font-size: 12px;
        color: #6b7d73;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .weather-card-body {
        padding: 20px 22px;
    }
    .weather-current {
        display: flex;
        align-items: center;
        gap: 18px;
        margin-bottom: 18px;
    }
    .weather-icon-big {
        font-size: 52px;
        line-height: 1;
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.08));
        animation: float 3s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }
    .weather-temp-main {
        font-size: 40px;
        font-weight: 700;
        color: #1a2e24;
        line-height: 1;
    }
    .weather-temp-main sup {
        font-size: 20px;
        font-weight: 500;
        color: #6b7d73;
    }
    .weather-desc {
        font-size: 13px;
        color: #4b5e52;
        font-weight: 500;
    }

    .weather-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 18px;
    }
    .weather-stat {
        background: #f8faf9;
        border-radius: 12px;
        padding: 12px;
        text-align: center;
    }
    .weather-stat .stat-icon {
        font-size: 18px;
        margin-bottom: 4px;
    }
    .weather-stat .stat-value {
        font-size: 16px;
        font-weight: 700;
        color: #1a2e24;
    }
    .weather-stat .stat-label {
        font-size: 10px;
        color: #7a9484;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .forecast-mini {
        display: flex;
        gap: 6px;
        overflow-x: auto;
        padding-bottom: 4px;
    }
    .forecast-day {
        flex-shrink: 0;
        background: #f0fdf4;
        border-radius: 12px;
        padding: 10px 12px;
        text-align: center;
        min-width: 72px;
        transition: background 0.2s;
    }
    .forecast-day:first-child {
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        border: 1px solid #86efac;
    }
    .forecast-day .fd-date {
        font-size: 10px;
        font-weight: 600;
        color: #6b7d73;
        margin-bottom: 4px;
    }
    .forecast-day .fd-icon {
        font-size: 20px;
        margin-bottom: 4px;
    }
    .forecast-day .fd-temps {
        font-size: 11px;
        font-weight: 600;
        color: #1a2e24;
    }
    .forecast-day .fd-temps span {
        color: #7a9484;
        font-weight: 400;
    }
    .forecast-day .fd-gdd {
        font-size: 9px;
        color: #16a34a;
        font-weight: 700;
        margin-top: 3px;
    }

    .weather-card-footer {
        padding: 12px 22px;
        border-top: 1px solid #f0f4f1;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .source-label {
        font-size: 10px;
        color: #a5b4a9;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .coord-label {
        font-size: 10px;
        color: #a5b4a9;
    }

    .no-coords-card {
        background: #fff;
        border-radius: 18px;
        padding: 28px;
        text-align: center;
        border: 2px dashed #d1d5db;
    }
    .no-coords-card .ncc-icon {
        font-size: 40px;
        margin-bottom: 12px;
        opacity: 0.4;
    }
    .no-coords-card p {
        color: #6b7d73;
        font-size: 13px;
        margin-bottom: 12px;
    }
    .no-coords-card .btn-set {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #2d6a4f;
        color: #fff;
        padding: 8px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.2s;
    }
    .no-coords-card .btn-set:hover {
        background: #1e4d37;
        color: #fff;
    }

    .loading-overlay {
        position: absolute;
        inset: 0;
        background: rgba(255,255,255,0.85);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        z-index: 10;
    }
    .spinner {
        width: 36px;
        height: 36px;
        border: 3px solid #e0e8e3;
        border-top-color: #2d6a4f;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<!-- Hero Section -->
<div class="weather-hero">
    <h2>🌤️ Monitoring Cuaca Kebun</h2>
    <p>Data cuaca real-time dan forecast 7 hari berdasarkan lokasi kebun — terintegrasi otomatis dengan API cuaca.</p>
    <div class="api-badge">
        <i class="fa-solid fa-satellite-dish"></i>
        Data dari Open-Meteo API — Gratis & Tanpa API Key
    </div>
</div>

@if(session('error'))
    <div class="alert alert-danger" style="border-radius:12px;margin-bottom:20px;">{{ session('error') }}</div>
@endif

<!-- Weather Grid -->
<div class="weather-grid">
    @forelse($kebuns as $kebun)
        @if($kebun->latitude && $kebun->longitude && isset($weatherData[$kebun->id]))
            @php $w = $weatherData[$kebun->id]; @endphp
            <div class="weather-card" id="weather-card-{{ $kebun->id }}">
                <div class="weather-card-header">
                    <div class="kebun-name">{{ $kebun->nama_kebun }}</div>
                    <div class="kebun-meta">
                        <i class="fa-solid fa-location-dot"></i>
                        {{ $kebun->lokasi }} — {{ $kebun->jenis_mangga }}
                        @if(Auth::user()->role === 'admin' && $kebun->user)
                            <span style="margin-left:6px;color:#a5b4a9;">| {{ $kebun->user->name }}</span>
                        @endif
                    </div>
                </div>
                <div class="weather-card-body">
                    <!-- Current Weather -->
                    @if($w['current'])
                        <div class="weather-current">
                            <div class="weather-icon-big">{{ $w['current']['weather_icon'] }}</div>
                            <div>
                                <div class="weather-temp-main">
                                    {{ round($w['current']['temperature']) }}<sup>°C</sup>
                                </div>
                                <div class="weather-desc">{{ $w['current']['weather_label'] }}</div>
                            </div>
                        </div>

                        <div class="weather-stats">
                            <div class="weather-stat">
                                <div class="stat-icon">🌡️</div>
                                <div class="stat-value">
                                    {{ $w['daily'][0]['tmin'] ?? '-' }}° / {{ $w['daily'][0]['tmax'] ?? '-' }}°
                                </div>
                                <div class="stat-label">Min / Max</div>
                            </div>
                            <div class="weather-stat">
                                <div class="stat-icon">💧</div>
                                <div class="stat-value">{{ $w['current']['humidity'] ?? '-' }}%</div>
                                <div class="stat-label">Kelembapan</div>
                            </div>
                            <div class="weather-stat">
                                <div class="stat-icon">💨</div>
                                <div class="stat-value">{{ $w['current']['wind_speed'] ?? '-' }}</div>
                                <div class="stat-label">Angin (km/h)</div>
                            </div>
                        </div>
                    @endif

                    <!-- 7-Day Mini Forecast -->
                    <div class="forecast-mini">
                        @foreach($w['daily'] as $i => $day)
                            <div class="forecast-day">
                                <div class="fd-date">
                                    {{ $i === 0 ? 'Hari Ini' : \Carbon\Carbon::parse($day['date'])->isoFormat('dd D') }}
                                </div>
                                <div class="fd-icon">{{ $day['weather_icon'] }}</div>
                                <div class="fd-temps">
                                    {{ $day['tmax'] }}° <span>{{ $day['tmin'] }}°</span>
                                </div>
                                <div class="fd-gdd">GDD: {{ $day['gdd'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="weather-card-footer">
                    <div class="source-label">
                        <i class="fa-solid fa-satellite-dish"></i>
                        Data suhu dari API cuaca
                    </div>
                    <div class="coord-label">
                        📍 {{ number_format($kebun->latitude, 4) }}, {{ number_format($kebun->longitude, 4) }}
                    </div>
                </div>
            </div>
        @elseif(!$kebun->latitude || !$kebun->longitude)
            <div class="no-coords-card">
                <div class="ncc-icon">📍</div>
                <div class="kebun-name" style="font-size:15px;font-weight:700;color:#1a2e24;margin-bottom:4px;">{{ $kebun->nama_kebun }}</div>
                <p>Kebun ini belum memiliki koordinat lokasi.<br>Tambahkan latitude & longitude untuk melihat data cuaca.</p>
                <a href="{{ route('kebun.edit', $kebun->id) }}" class="btn-set">
                    <i class="fa-solid fa-map-pin"></i> Set Koordinat
                </a>
            </div>
        @endif
    @empty
        <div class="no-coords-card" style="grid-column: 1 / -1;">
            <div class="ncc-icon">🌾</div>
            <p>Belum ada data kebun. Silakan tambahkan kebun terlebih dahulu.</p>
            @if(Auth::user()->role !== 'admin')
                <a href="{{ route('kebun.create') }}" class="btn-set">
                    <i class="fa-solid fa-plus"></i> Tambah Kebun
                </a>
            @endif
        </div>
    @endforelse
</div>

@endsection
