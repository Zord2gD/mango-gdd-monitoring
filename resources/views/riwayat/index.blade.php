@extends('layouts.dashboard')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fa-solid fa-clock-rotate-left" style="color: #2d6a4f; margin-right: 10px;"></i>Riwayat Panen</h2>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 10px;">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header bg-white" style="border-bottom: 2px solid #28a745; padding: 16px 20px;">
        <h5 class="mb-0 fw-bold text-dark">Data Histori Panen</h5>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover mb-0" style="font-size: 14.5px;">
            <thead style="background: #f8f9fa;">
                <tr>
                    <th style="padding: 14px 20px; color: #6c757d; font-weight: 600;">#</th>
                    @if(Auth::user()->role === 'admin')
                        <th style="padding: 14px 20px; color: #6c757d; font-weight: 600;">Petani</th>
                    @endif
                    <th style="padding: 14px 20px; color: #6c757d; font-weight: 600;">Nama Kebun</th>
                    <th style="padding: 14px 20px; color: #6c757d; font-weight: 600;">Siklus (Berbunga - Panen)</th>
                    <th style="padding: 14px 20px; color: #6c757d; font-weight: 600;">Total GDD</th>
                    <th style="padding: 14px 20px; color: #6c757d; font-weight: 600;">Hasil Panen</th>
                    <th style="padding: 14px 20px; color: #6c757d; font-weight: 600;">Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat as $i => $r)
                    <tr style="border-bottom: 1px solid #f1f3f5;">
                        <td style="padding: 14px 20px; color: #adb5bd;">{{ $i + 1 }}</td>
                        @if(Auth::user()->role === 'admin')
                            <td style="padding: 14px 20px;">
                                <span class="badge bg-light text-dark border">
                                    <i class="fa-solid fa-user text-muted"></i> {{ $r->kebun->user->name ?? '—' }}
                                </span>
                            </td>
                        @endif
                        <td style="padding: 14px 20px; font-weight: 600; color: #1a2e24;">
                            <i class="fa-solid fa-seedling text-success me-1"></i> {{ $r->kebun->nama_kebun }}
                        </td>
                        <td style="padding: 14px 20px;">
                            <div style="font-size: 13px;">
                                <div class="text-muted">Mulai: {{ $r->tanggal_berbunga ? \Carbon\Carbon::parse($r->tanggal_berbunga)->isoFormat('D MMM Y') : '—' }}</div>
                                <div class="fw-bold text-dark">Panen: {{ \Carbon\Carbon::parse($r->tanggal_panen)->isoFormat('D MMM Y') }}</div>
                            </div>
                        </td>
                        <td style="padding: 14px 20px;">
                            <span class="badge bg-success" style="padding: 6px 10px; border-radius: 6px;">
                                {{ number_format($r->total_gdd, 1) }} GDD
                            </span>
                        </td>
                        <td style="padding: 14px 20px; font-weight: 700; color: #ea580c;">
                            {{ number_format($r->hasil_panen_kg, 1) }} Kg
                        </td>
                        <td style="padding: 14px 20px; color: #6b7280; font-size: 13px; max-width: 200px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="{{ $r->catatan }}">
                            {{ $r->catatan ?: '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fa-solid fa-box-open fa-3x mb-3 text-secondary" style="opacity: 0.3;"></i>
                            <p class="text-muted">Belum ada riwayat panen.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
