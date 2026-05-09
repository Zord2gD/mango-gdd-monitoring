@extends('layouts.dashboard')

@section('content')

<style>
    .suhu-form-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.06);
        overflow: hidden;
        max-width: 750px;
    }
    .suhu-form-header {
        background: linear-gradient(135deg, #fef2f2, #fff1f2);
        padding: 24px 28px;
        border-bottom: 1px solid #fecaca;
    }
    .suhu-form-header h2 {
        font-size: 20px;
        font-weight: 700;
        color: #7f1d1d;
        margin: 0 0 4px;
    }
    .suhu-form-header p {
        font-size: 13px;
        color: #991b1b;
        margin: 0;
    }
    .suhu-form-body { padding: 28px; }

    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #1a2e24;
        margin-bottom: 6px;
    }
    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid #d1d5db;
        border-radius: 10px;
        font-size: 14px;
        color: #1a2e24;
        background: #fff;
        transition: border-color 0.2s, box-shadow 0.2s;
        outline: none;
    }
    .form-group input:focus,
    .form-group select:focus {
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220,38,38,0.1);
    }
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    /* API Fetch Button */
    .api-fetch-section {
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        border: 1.5px solid #93c5fd;
        border-radius: 14px;
        padding: 18px 22px;
        margin-bottom: 24px;
        position: relative;
    }
    .api-fetch-title {
        font-size: 13px;
        font-weight: 700;
        color: #1e40af;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .api-fetch-desc {
        font-size: 12px;
        color: #3b82f6;
        margin-bottom: 12px;
    }
    .btn-api-fetch {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #fff;
        padding: 10px 22px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(37,99,235,0.3);
    }
    .btn-api-fetch:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(37,99,235,0.4);
    }
    .btn-api-fetch:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    .btn-api-fetch .spin {
        display: none;
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255,255,255,0.3);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
    }
    .btn-api-fetch.loading .spin { display: inline-block; }
    .btn-api-fetch.loading .icon-default { display: none; }
    @keyframes spin { to { transform: rotate(360deg); } }

    .api-result {
        margin-top: 14px;
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 500;
        display: none;
    }
    .api-result.success {
        display: block;
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }
    .api-result.error {
        display: block;
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    /* Weather Preview */
    .weather-preview {
        background: #f8faf9;
        border-radius: 12px;
        padding: 16px;
        margin-top: 12px;
        display: none;
    }
    .weather-preview.show { display: block; }
    .wp-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    .wp-row:last-child { margin-bottom: 0; }
    .wp-label { font-size: 12px; color: #6b7d73; }
    .wp-value { font-size: 13px; font-weight: 600; color: #1a2e24; }

    .divider { border: none; border-top: 1px dashed #e0e8e3; margin: 24px 0; }

    .btn-submit {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #16a34a, #15803d);
        color: #fff;
        padding: 12px 28px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(22,163,74,0.3);
    }
    .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(22,163,74,0.4);
    }
    .btn-back {
        color: #6b7d73;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        margin-left: 16px;
    }
    .btn-back:hover { color: #1a2e24; }
    .gdd-preview {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 10px;
        padding: 12px 16px;
        margin-top: 16px;
        font-size: 13px;
        color: #166534;
        font-weight: 600;
        display: none;
    }
    .gdd-preview.show { display: block; }
    .source-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 10px;
        color: #3b82f6;
        background: #eff6ff;
        padding: 3px 10px;
        border-radius: 20px;
        font-weight: 600;
        margin-left: 8px;
    }
</style>

<div class="suhu-form-card">
    <div class="suhu-form-header">
        <h2>🌡️ Input Suhu Harian</h2>
        <p>Catat suhu harian kebun Anda atau ambil data otomatis dari API cuaca.</p>
    </div>
    <div class="suhu-form-body">

        @if($errors->any())
            <div class="alert alert-danger" style="border-radius:10px;margin-bottom:20px;font-size:13px;">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('suhu.store') }}" method="POST" id="suhuForm">
            @csrf

            <div class="form-group">
                <label>Pilih Kebun</label>
                <select name="kebun_id" id="kebunSelect" class="form-control" required>
                    <option value="">-- Pilih Kebun --</option>
                    @foreach($kebun as $k)
                        <option value="{{ $k->id }}"
                            data-lat="{{ $k->latitude }}"
                            data-lon="{{ $k->longitude }}"
                            {{ old('kebun_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kebun }} ({{ $k->lokasi }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- API Auto-Fill Section -->
            <div class="api-fetch-section" id="apiFetchSection">
                <div class="api-fetch-title">
                    <i class="fa-solid fa-satellite-dish"></i>
                    Ambil Data Suhu dari API Cuaca
                </div>
                <div class="api-fetch-desc">
                    Otomatis mengambil Tmin & Tmax hari ini dari Open-Meteo berdasarkan koordinat kebun.
                </div>
                <button type="button" class="btn-api-fetch" id="btnApiFetch" onclick="fetchFromApi()">
                    <span class="icon-default"><i class="fa-solid fa-cloud-arrow-down"></i></span>
                    <span class="spin"></span>
                    Ambil Suhu dari API
                </button>
                <div class="api-result" id="apiResult"></div>
                <div class="weather-preview" id="weatherPreview">
                    <div class="wp-row">
                        <span class="wp-label">🌤️ Kondisi Cuaca</span>
                        <span class="wp-value" id="wpCondition">-</span>
                    </div>
                    <div class="wp-row">
                        <span class="wp-label">🌡️ Suhu Saat Ini</span>
                        <span class="wp-value" id="wpCurrent">-</span>
                    </div>
                    <div class="wp-row">
                        <span class="wp-label">💧 Kelembapan</span>
                        <span class="wp-value" id="wpHumidity">-</span>
                    </div>
                    <div class="wp-row">
                        <span class="wp-label">💨 Kecepatan Angin</span>
                        <span class="wp-value" id="wpWind">-</span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="tanggal" id="tanggalInput" value="{{ old('tanggal', date('Y-m-d')) }}" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tmin (°C) — Suhu Minimum</label>
                    <input type="number" step="0.1" name="tmin" id="tminInput" value="{{ old('tmin') }}" placeholder="22.5" required>
                </div>
                <div class="form-group">
                    <label>Tmax (°C) — Suhu Maksimum</label>
                    <input type="number" step="0.1" name="tmax" id="tmaxInput" value="{{ old('tmax') }}" placeholder="33.8" required>
                </div>
            </div>

            <div class="gdd-preview" id="gddPreview">
                <i class="fa-solid fa-fire"></i>
                Estimasi GDD Hari Ini: <span id="gddValue">0</span>
                <span class="source-badge" id="sourceBadge" style="display:none;">
                    <i class="fa-solid fa-satellite-dish"></i> Data dari API cuaca
                </span>
            </div>

            <hr class="divider">

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-save"></i> Simpan Suhu & GDD
            </button>
            <a href="{{ route('suhu.index') }}" class="btn-back">← Kembali</a>
        </form>
    </div>
</div>

<script>
// Calculate GDD preview when tmin/tmax change
const tminEl = document.getElementById('tminInput');
const tmaxEl = document.getElementById('tmaxInput');
const gddPrev = document.getElementById('gddPreview');
const gddVal = document.getElementById('gddValue');

function calcGddPreview() {
    const tmin = parseFloat(tminEl.value);
    const tmax = parseFloat(tmaxEl.value);
    if (!isNaN(tmin) && !isNaN(tmax)) {
        const gdd = Math.max(0, ((tmax + tmin) / 2) - 10).toFixed(2);
        gddVal.textContent = gdd;
        gddPrev.classList.add('show');
    }
}

tminEl.addEventListener('input', calcGddPreview);
tmaxEl.addEventListener('input', calcGddPreview);

// Fetch temperature from API
function fetchFromApi() {
    const kebunSelect = document.getElementById('kebunSelect');
    const selected = kebunSelect.options[kebunSelect.selectedIndex];
    const kebunId = kebunSelect.value;
    const apiResult = document.getElementById('apiResult');
    const btn = document.getElementById('btnApiFetch');
    const weatherPreview = document.getElementById('weatherPreview');
    const sourceBadge = document.getElementById('sourceBadge');

    if (!kebunId) {
        apiResult.className = 'api-result error';
        apiResult.textContent = '⚠️ Pilih kebun terlebih dahulu.';
        return;
    }

    const lat = selected.dataset.lat;
    const lon = selected.dataset.lon;
    if (!lat || !lon || lat === '' || lon === '') {
        apiResult.className = 'api-result error';
        apiResult.innerHTML = '⚠️ Kebun ini belum memiliki koordinat. <a href="/kebun/' + kebunId + '/edit" style="color:#1d4ed8;">Set koordinat →</a>';
        return;
    }

    // Start loading
    btn.classList.add('loading');
    btn.disabled = true;
    apiResult.className = 'api-result';
    apiResult.style.display = 'none';

    fetch('/api/cuaca/' + kebunId)
        .then(res => res.json())
        .then(data => {
            btn.classList.remove('loading');
            btn.disabled = false;

            if (data.error) {
                apiResult.className = 'api-result error';
                apiResult.textContent = '❌ ' + data.error;
                return;
            }

            // Auto-fill today's data
            const today = data.daily[0];
            if (today) {
                tminEl.value = today.tmin;
                tmaxEl.value = today.tmax;
                document.getElementById('tanggalInput').value = today.date;
                calcGddPreview();
                sourceBadge.style.display = 'inline-flex';
            }

            // Show current weather preview
            if (data.current) {
                document.getElementById('wpCondition').textContent =
                    data.current.weather_icon + ' ' + data.current.weather_label;
                document.getElementById('wpCurrent').textContent =
                    data.current.temperature + '°C';
                document.getElementById('wpHumidity').textContent =
                    data.current.humidity + '%';
                document.getElementById('wpWind').textContent =
                    data.current.wind_speed + ' km/h';
                weatherPreview.classList.add('show');
            }

            apiResult.className = 'api-result success';
            apiResult.textContent = '✅ Data suhu berhasil diambil dari API cuaca! Tmin: ' + today.tmin + '°C, Tmax: ' + today.tmax + '°C';
        })
        .catch(err => {
            btn.classList.remove('loading');
            btn.disabled = false;
            apiResult.className = 'api-result error';
            apiResult.textContent = '❌ Gagal menghubungi API. Coba lagi nanti.';
        });
}
</script>

@endsection