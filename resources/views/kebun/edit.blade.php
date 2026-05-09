@extends('layouts.dashboard')

@section('content')

<style>
    .form-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.06);
        overflow: hidden;
        max-width: 700px;
    }
    .form-card-header {
        background: linear-gradient(135deg, #fef9c3, #fefce8);
        padding: 24px 28px;
        border-bottom: 1px solid #fde68a;
    }
    .form-card-header h2 {
        font-size: 20px;
        font-weight: 700;
        color: #713f12;
        margin: 0 0 4px;
    }
    .form-card-header p {
        font-size: 13px;
        color: #92400e;
        margin: 0;
    }
    .form-card-body {
        padding: 28px;
    }
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
    .form-group label .optional {
        font-weight: 400;
        color: #a5b4a9;
        font-size: 11px;
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
        border-color: #ca8a04;
        box-shadow: 0 0 0 3px rgba(202,138,4,0.12);
    }
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .btn-geo {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #dbeafe;
        color: #1d4ed8;
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 6px;
    }
    .btn-geo:hover { background: #bfdbfe; }
    .btn-geo.loading { opacity: 0.6; pointer-events: none; }
    .geo-info {
        font-size: 11px;
        color: #7a9484;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .btn-submit {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #ca8a04, #eab308);
        color: #fff;
        padding: 12px 28px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(202,138,4,0.3);
    }
    .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(202,138,4,0.4);
    }
    .btn-back {
        color: #6b7d73;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        margin-left: 16px;
    }
    .btn-back:hover { color: #1a2e24; }
    .divider { border: none; border-top: 1px dashed #e0e8e3; margin: 24px 0; }
    .section-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #7a9484;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
</style>

<div class="form-card">
    <div class="form-card-header">
        <h2>✏️ Edit Kebun: {{ $kebun->nama_kebun }}</h2>
        <p>Perbarui informasi kebun Anda. Tambahkan koordinat untuk integrasi cuaca otomatis.</p>
    </div>
    <div class="form-card-body">

        @if($errors->any())
            <div class="alert alert-danger" style="border-radius:10px;margin-bottom:20px;font-size:13px;">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('kebun.update', $kebun->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="section-label">
                <i class="fa-solid fa-seedling"></i> Informasi Kebun
            </div>

            <div class="form-group">
                <label>Nama Kebun</label>
                <input type="text" name="nama_kebun" value="{{ old('nama_kebun', $kebun->nama_kebun) }}" required>
            </div>

            <div class="form-group">
                <label>Lokasi</label>
                <input type="text" name="lokasi" value="{{ old('lokasi', $kebun->lokasi) }}" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Jumlah Pohon</label>
                    <input type="number" name="jumlah_pohon" value="{{ old('jumlah_pohon', $kebun->jumlah_pohon) }}" min="1" required>
                </div>
                <div class="form-group">
                    <label>Jenis Mangga</label>
                    @php $isSiklusAktif = $kebun->fase && $kebun->fase->tanggal_berbunga; @endphp
                    <select name="jenis_mangga" {{ $isSiklusAktif ? 'disabled style=background:#f3f4f6;cursor:not-allowed;' : 'required' }}>
                        <option value="">-- Pilih Jenis --</option>
                        @php $jm = old('jenis_mangga', $kebun->jenis_mangga); @endphp
                        <optgroup label="Varietas Unggulan Indramayu">
                            <option value="Cengkir" {{ $jm == 'Cengkir' ? 'selected' : '' }}>Cengkir (Target: 1150 GDD)</option>
                            <option value="Dermayu" {{ $jm == 'Dermayu' ? 'selected' : '' }}>Dermayu (Target: 1100 GDD)</option>
                            <option value="Gedong Gincu" {{ $jm == 'Gedong Gincu' ? 'selected' : '' }}>Gedong Gincu (Target: 1100 GDD)</option>
                            <option value="Lalijiwa" {{ $jm == 'Lalijiwa' ? 'selected' : '' }}>Lalijiwa (Target: 1200 GDD)</option>
                        </optgroup>
                        <optgroup label="Varietas Populer Lainnya">
                            <option value="Gedong" {{ $jm == 'Gedong' ? 'selected' : '' }}>Gedong (Target: 1100 GDD)</option>
                            <option value="Harumanis" {{ $jm == 'Harumanis' ? 'selected' : '' }}>Harumanis (Target: 1200 GDD)</option>
                            <option value="Arum Manis" {{ $jm == 'Arum Manis' ? 'selected' : '' }}>Arum Manis (Target: 1200 GDD)</option>
                            <option value="Golek" {{ $jm == 'Golek' ? 'selected' : '' }}>Golek (Target: 1250 GDD)</option>
                            <option value="Manalagi" {{ $jm == 'Manalagi' ? 'selected' : '' }}>Manalagi (Target: 1150 GDD)</option>
                            <option value="Gajah" {{ $jm == 'Gajah' ? 'selected' : '' }}>Gajah (Target: 1300 GDD)</option>
                            <option value="Kweni" {{ $jm == 'Kweni' ? 'selected' : '' }}>Kweni (Target: 1100 GDD)</option>
                            <option value="Kopyor" {{ $jm == 'Kopyor' ? 'selected' : '' }}>Kopyor (Target: 1200 GDD)</option>
                        </optgroup>
                    </select>
                    @if($isSiklusAktif)
                        <input type="hidden" name="jenis_mangga" value="{{ $kebun->jenis_mangga }}">
                        <small style="color:#d97706;font-size:11px;display:block;margin-top:6px;">
                            <i class="fa-solid fa-lock"></i> Varietas dikunci karena siklus pertumbuhan sedang berjalan.
                        </small>
                    @endif
                </div>
            </div>

            <hr class="divider">

            <div class="section-label">
                <i class="fa-solid fa-map-location-dot"></i> Koordinat Lokasi
                <span class="optional" style="font-weight:400;font-size:10px;text-transform:none;letter-spacing:0;">(untuk integrasi API cuaca)</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Latitude <span class="optional">(opsional)</span></label>
                    <input type="number" step="any" name="latitude" id="latitude" value="{{ old('latitude', $kebun->latitude) }}" placeholder="-6.3271">
                </div>
                <div class="form-group">
                    <label>Longitude <span class="optional">(opsional)</span></label>
                    <input type="number" step="any" name="longitude" id="longitude" value="{{ old('longitude', $kebun->longitude) }}" placeholder="108.3240">
                </div>
            </div>

            <button type="button" class="btn-geo" id="btnGeo" onclick="detectLocation()">
                <i class="fa-solid fa-crosshairs"></i>
                <span id="geoText">📍 Deteksi Lokasi Otomatis</span>
            </button>
            <div class="geo-info" id="geoInfo" style="display:none;">
                <i class="fa-solid fa-check-circle" style="color:#16a34a;"></i>
                <span id="geoInfoText"></span>
            </div>

            <hr class="divider">

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-refresh"></i> Update Kebun
            </button>
            <a href="{{ route('kebun.index') }}" class="btn-back">← Kembali</a>
        </form>
    </div>
</div>

<script>
function detectLocation() {
    const btn = document.getElementById('btnGeo');
    const text = document.getElementById('geoText');
    const info = document.getElementById('geoInfo');
    const infoText = document.getElementById('geoInfoText');

    if (!navigator.geolocation) {
        alert('Geolocation tidak didukung oleh browser Anda.');
        return;
    }

    btn.classList.add('loading');
    text.textContent = '⏳ Mendeteksi lokasi...';

    navigator.geolocation.getCurrentPosition(
        function(position) {
            const lat = position.coords.latitude.toFixed(6);
            const lng = position.coords.longitude.toFixed(6);

            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;

            btn.classList.remove('loading');
            text.textContent = '📍 Deteksi Lokasi Otomatis';
            info.style.display = 'flex';
            infoText.textContent = `Lokasi terdeteksi: ${lat}, ${lng} (akurasi: ${Math.round(position.coords.accuracy)}m)`;
        },
        function(error) {
            btn.classList.remove('loading');
            text.textContent = '📍 Deteksi Lokasi Otomatis';
            alert('Gagal mendeteksi lokasi: ' + error.message);
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
}
</script>

@endsection