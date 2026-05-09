@extends('layouts.dashboard')

@section('content')

<style>
    .api-info-banner {
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        border: 1px solid #93c5fd;
        border-radius: 12px;
        padding: 14px 20px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 13px;
        color: #1e40af;
    }
    .api-info-banner i { font-size: 18px; }
    .source-badge-api {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 10px;
        padding: 2px 8px;
        border-radius: 12px;
        font-weight: 600;
        background: #dbeafe;
        color: #1d4ed8;
    }
    .source-badge-manual {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 10px;
        padding: 2px 8px;
        border-radius: 12px;
        font-weight: 600;
        background: #f3f4f6;
        color: #6b7280;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Data Suhu Harian</h2>
    <div class="d-flex gap-2">
        @if(Auth::user()->role !== 'admin')
            <a href="{{ route('cuaca.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="fa-solid fa-cloud-sun"></i> Lihat Cuaca
            </a>
            <a href="{{ route('suhu.create') }}" class="btn btn-success">+ Tambah Suhu</a>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="api-info-banner">
    <i class="fa-solid fa-satellite-dish"></i>
    <div>
        <strong>Integrasi API Cuaca Aktif</strong> — Data suhu dapat diambil otomatis dari Open-Meteo API.
        Sistem CRON berjalan setiap hari pukul 06:00 WIB untuk mengumpulkan Tmin & Tmax terbaru.
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-success text-white">Total GDD Akumulasi Per Kebun</div>
    <div class="card-body">
        @if(empty($statsGdd))
            <p class="text-muted mb-0">Belum ada data GDD. Pastikan Fase Tanaman (tanggal berbunga) sudah diinput.</p>
        @else
            <div class="row">
                @foreach($statsGdd as $stat)
                    <div class="col-md-4 mb-3">
                        <div class="border rounded p-3 bg-white h-100">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold" style="font-size: 14px;">{{ $stat['nama'] }}</span>
                                <span class="badge bg-{{ $stat['progress'] >= 100 ? 'success' : 'primary' }}" style="font-size: 11px;">
                                    {{ $stat['fase'] }}
                                </span>
                            </div>
                            <div class="progress mb-2" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $stat['progress'] }}%;"></div>
                            </div>
                            <div class="d-flex justify-content-between small text-muted mb-2">
                                <span>{{ number_format($stat['total'], 1) }} GDD</span>
                                <span>Target: {{ $stat['target'] }} GDD</span>
                            </div>
                            <button class="btn btn-sm btn-outline-primary w-100" onclick="syncData({{ $stat['id'] }})">
                                <i class="fa-solid fa-clock-rotate-left"></i> Tarik Data Suhu Masa Lalu
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@if($suhuGrouped->isEmpty())
    <div class="card mb-4 shadow-sm">
        <div class="card-body text-center text-muted py-5">
            <i class="fa-solid fa-temperature-empty fa-3x mb-3 text-secondary" style="opacity: 0.5;"></i>
            <p>Belum ada data suhu harian yang tercatat.</p>
        </div>
    </div>
@else
    @foreach($suhuGrouped as $kebunId => $suhuItems)
        @php 
            $kebunInfo = $suhuItems->first()->kebun; 
        @endphp
        <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-white d-flex align-items-center gap-2" style="border-bottom: 2px solid #28a745; padding: 16px 20px;">
                <i class="fa-solid fa-seedling" style="color: #28a745; font-size: 18px;"></i>
                <h5 class="mb-0 fw-bold text-dark">{{ $kebunInfo->nama_kebun }}</h5>
                @if(Auth::user()->role === 'admin')
                    <span class="badge bg-light text-dark border ms-2" style="font-weight: 500;">
                        <i class="fa-solid fa-user text-muted"></i> {{ $kebunInfo->user->name ?? '—' }}
                    </span>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size: 14.5px;">
                    <thead style="background: #f8f9fa;">
                        <tr>
                            <th style="padding: 14px 20px; color: #6c757d; font-weight: 600;">#</th>
                            <th style="padding: 14px 20px; color: #6c757d; font-weight: 600;">Tanggal</th>
                            <th style="padding: 14px 20px; color: #6c757d; font-weight: 600;">Tmin (°C)</th>
                            <th style="padding: 14px 20px; color: #6c757d; font-weight: 600;">Tmax (°C)</th>
                            <th style="padding: 14px 20px; color: #6c757d; font-weight: 600;">GDD Hari Ini</th>
                            <th style="padding: 14px 20px; color: #6c757d; font-weight: 600;">Sumber</th>
                            <th style="padding: 14px 20px; color: #6c757d; font-weight: 600;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suhuItems as $i => $s)
                            <tr style="border-bottom: 1px solid #f1f3f5;">
                                <td style="padding: 12px 20px; color: #adb5bd;">{{ $i + 1 }}</td>
                                <td style="padding: 12px 20px; font-weight: 500;">{{ \Carbon\Carbon::parse($s->tanggal)->isoFormat('D MMMM Y') }}</td>
                                <td style="padding: 12px 20px;">{{ $s->tmin }}</td>
                                <td style="padding: 12px 20px;">{{ $s->tmax }}</td>
                                <td style="padding: 12px 20px;">
                                    @if($s->gdd !== null)
                                        <span class="badge bg-{{ $s->gdd > 0 ? 'success' : 'secondary' }}" style="padding: 6px 10px; border-radius: 6px;">
                                            {{ number_format($s->gdd, 2) }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">—</span>
                                    @endif
                                </td>
                                <td style="padding: 12px 20px;">
                                    <span class="source-badge-api">
                                        <i class="fa-solid fa-satellite-dish"></i> API Cuaca
                                    </span>
                                </td>
                                <td style="padding: 12px 20px;">
                                    <form action="{{ route('suhu.destroy', $s->id) }}" method="POST" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm" style="border-radius: 6px;" onclick="return confirm('Yakin hapus data suhu ini?')">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@endif

<script>
function syncData(kebunId) {
    if (!confirm('Tarik data cuaca dari tanggal berbunga hingga hari ini? (Maksimal 92 hari terakhir)')) return;

    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyinkronkan...';
    btn.disabled = true;

    fetch('/api/cuaca/sync/' + kebunId, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            alert('Gagal: ' + data.error);
            btn.innerHTML = originalText;
            btn.disabled = false;
        } else {
            alert('Sukses! ' + data.message);
            window.location.reload();
        }
    })
    .catch(err => {
        alert('Terjadi kesalahan koneksi.');
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}
</script>

@endsection