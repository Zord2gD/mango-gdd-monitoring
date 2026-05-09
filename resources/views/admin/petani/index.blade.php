@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-dark mb-0"><i class="fa-solid fa-users me-2 text-success"></i>Data Petani Terdaftar</h2>
            <p class="text-muted">Pantau daftar petani, jumlah kebun, dan akumulasi nilai GDD serta prediksi masa panen.</p>
        </div>
    </div>

    <!-- Statistik Singkat -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3 me-3">
                        <i class="fa-solid fa-users fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-1">Total Petani</h6>
                        <h3 class="mb-0 fw-bold">{{ $totalPetani }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-3 me-3">
                        <i class="fa-solid fa-user-check fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-1">Petani Aktif (Punya Kebun)</h6>
                        <h3 class="mb-0 fw-bold">{{ $petaniAktif }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-3 me-3">
                        <i class="fa-solid fa-basket-shopping fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-1">Petani Siap Panen</h6>
                        <h3 class="mb-0 fw-bold">{{ $petaniSiapPanen }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Petani -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-bold">Monitoring Petani</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted">
                        <tr>
                            <th class="ps-4">Nama Petani</th>
                            <th>Email</th>
                            <th class="text-center">Jumlah Kebun</th>
                            <th>Total GDD Akumulasi</th>
                            <th>Progress Keseluruhan</th>
                            <th class="text-center">Status Dominan</th>
                            <th>Bergabung Sejak</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($dataPetani as $p)
                            <tr>
                                <td class="ps-4 fw-medium text-dark">
                                    <i class="fa-solid fa-user-circle text-success me-2"></i>{{ $p->nama }}
                                </td>
                                <td>{{ $p->email }}</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary rounded-pill px-3">{{ $p->jumlah_kebun }}</span>
                                </td>
                                <td>
                                    <strong>{{ $p->total_gdd }}</strong> 
                                    <span class="text-muted" style="font-size: 0.85rem;">GDD</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="me-2" style="width: 40px; font-weight: 600;">{{ $p->progress }}%</span>
                                        <div class="progress w-100" style="height: 8px; border-radius: 10px;">
                                            @php
                                                $barColor = $p->progress >= 100 ? 'bg-success' : ($p->progress >= 75 ? 'bg-warning' : 'bg-primary');
                                            @endphp
                                            <div class="progress-bar {{ $barColor }}" role="progressbar" style="width: {{ $p->progress }}%" aria-valuenow="{{ $p->progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @php
                                        // Map the custom badge classes to bootstrap badge classes
                                        $bsBadge = 'bg-secondary';
                                        if ($p->status_badge === 'badge-green') $bsBadge = 'bg-success';
                                        elseif ($p->status_badge === 'badge-yellow') $bsBadge = 'bg-warning text-dark';
                                    @endphp
                                    <span class="badge {{ $bsBadge }} px-3 py-2 rounded-pill">{{ $p->status_panen }}</span>
                                </td>
                                <td class="text-muted" style="font-size: 0.9rem;">{{ $p->created_at }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-users-slash fa-3x mb-3 text-light"></i>
                                    <h5>Belum ada data petani yang mendaftar</h5>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
