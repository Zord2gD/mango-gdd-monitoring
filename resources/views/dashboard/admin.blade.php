<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — Mango GDD Monitoring</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f0f4f1; color: #1e2d24; display: flex; min-height: 100vh; }

        /* ── SIDEBAR ── */
        .sidebar {
            width: 260px; min-height: 100vh;
            background: linear-gradient(180deg, #1b3a2d 0%, #14291f 100%);
            display: flex; flex-direction: column;
            position: fixed; top: 0; left: 0; height: 100vh;
            z-index: 100; box-shadow: 4px 0 20px rgba(0,0,0,0.18);
        }
        .sidebar-brand {
            padding: 24px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar-brand .logo { display: flex; align-items: center; gap: 10px; }
        .sidebar-brand .logo-icon {
            width: 40px; height: 40px; border-radius: 10px;
            background: linear-gradient(135deg, #4ade80, #16a34a);
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
        }
        .sidebar-brand h1 { color: #fff; font-size: 15px; font-weight: 700; line-height: 1.2; }
        .sidebar-brand span { color: #86efac; font-size: 11px; font-weight: 400; }

        .sidebar-menu { flex: 1; padding: 16px 12px; overflow-y: auto; }
        .menu-label { color: #6b7d73; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; padding: 12px 10px 6px; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 10px; color: #a7c3b4; font-size: 14px; font-weight: 500; text-decoration: none; transition: all 0.2s; margin-bottom: 2px; cursor: pointer; }
        .nav-item:hover { background: rgba(255,255,255,0.07); color: #fff; }
        .nav-item.active { background: linear-gradient(135deg, #2d6a4f, #1e4d37); color: #fff; box-shadow: 0 4px 12px rgba(45,106,79,0.4); }
        .nav-item .nav-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 15px; background: rgba(255,255,255,0.06); flex-shrink: 0; }
        .nav-item.active .nav-icon { background: rgba(255,255,255,0.15); }
        .sidebar-footer { padding: 16px 12px; border-top: 1px solid rgba(255,255,255,0.07); }
        .logout-btn { display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 10px; color: #fca5a5; font-size: 14px; font-weight: 500; text-decoration: none; transition: all 0.2s; width: 100%; background: none; border: none; cursor: pointer; }
        .logout-btn:hover { background: rgba(239,68,68,0.12); color: #f87171; }
        .logout-btn .nav-icon { background: rgba(239,68,68,0.12); }

        /* ── MAIN CONTENT ── */
        .main { margin-left: 260px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

        /* ── TOP NAV ── */
        .topnav {
            background: #fff; height: 68px; display: flex; align-items: center;
            justify-content: space-between; padding: 0 28px;
            box-shadow: 0 1px 12px rgba(0,0,0,0.06); position: sticky; top: 0; z-index: 50;
        }
        .topnav-left h2 { font-size: 18px; font-weight: 700; color: #1a2e24; }
        .topnav-left p { font-size: 12px; color: #7a9484; margin-top: 1px; }
        .topnav-right { display: flex; align-items: center; gap: 16px; }
        .notif-btn { width: 40px; height: 40px; border-radius: 10px; background: #f0faf4; border: 1px solid #d1fae5; display: flex; align-items: center; justify-content: center; color: #2d6a4f; cursor: pointer; position: relative; transition: background 0.2s; }
        .notif-btn:hover { background: #d1fae5; }
        .notif-badge { position: absolute; top: 6px; right: 6px; width: 8px; height: 8px; background: #ef4444; border-radius: 50%; border: 2px solid #fff; }
        .admin-avatar { display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 6px 10px; border-radius: 12px; transition: background 0.2s; }
        .admin-avatar:hover { background: #f0f4f1; }
        .avatar-circle { width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, #2d6a4f, #4ade80); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 14px; }
        .admin-avatar .info { line-height: 1.3; }
        .admin-avatar .info strong { font-size: 13px; color: #1a2e24; }
        .admin-avatar .info span { font-size: 11px; color: #7a9484; display: block; }

        /* ── CONTENT AREA ── */
        .content { padding: 28px; flex: 1; }
        .content-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .content-header h3 { font-size: 15px; font-weight: 600; color: #1a2e24; }
        .content-header span { font-size: 13px; color: #7a9484; }

        /* ── STATS CARDS ── */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 28px; }
        .stat-card { background: #fff; border-radius: 16px; padding: 22px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 16px; transition: transform 0.2s, box-shadow 0.2s; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
        .stat-icon { width: 54px; height: 54px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
        .stat-icon.green { background: #dcfce7; color: #16a34a; }
        .stat-icon.blue { background: #dbeafe; color: #2563eb; }
        .stat-icon.orange { background: #ffedd5; color: #ea580c; }
        .stat-icon.yellow { background: #fef9c3; color: #ca8a04; }
        .stat-info label { font-size: 12px; color: #7a9484; font-weight: 500; display: block; margin-bottom: 4px; }
        .stat-info .value { font-size: 28px; font-weight: 700; color: #1a2e24; line-height: 1; }
        .stat-info .sub { font-size: 11px; color: #a5b4a9; margin-top: 4px; }

        /* ── TABLE CARD ── */
        .table-card { background: #fff; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 28px; }
        .table-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #f0f4f1; flex-wrap: wrap; gap: 12px; }
        .table-header h4 { font-size: 16px; font-weight: 700; color: #1a2e24; }
        .table-controls { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .search-box { display: flex; align-items: center; gap: 8px; background: #f5f7f5; border: 1px solid #e0e8e3; border-radius: 10px; padding: 8px 14px; }
        .search-box input { border: none; background: transparent; outline: none; font-size: 13px; color: #1a2e24; width: 180px; }
        .search-box i { color: #7a9484; font-size: 13px; }
        .filter-select { border: 1px solid #e0e8e3; background: #f5f7f5; border-radius: 10px; padding: 8px 14px; font-size: 13px; color: #1a2e24; outline: none; cursor: pointer; }

        table { width: 100%; border-collapse: collapse; }
        thead th { background: #f8faf8; color: #7a9484; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; padding: 13px 18px; text-align: left; white-space: nowrap; }
        tbody tr { border-bottom: 1px solid #f0f4f1; transition: background 0.15s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #f8faf8; }
        td { padding: 14px 18px; font-size: 13.5px; color: #2d3b33; vertical-align: middle; }
        .kebun-name { font-weight: 600; color: #1a2e24; }
        .kebun-sub { font-size: 11.5px; color: #7a9484; }

        /* badges */
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; font-size: 11.5px; font-weight: 600; white-space: nowrap; }
        .badge-green { background: #dcfce7; color: #15803d; }
        .badge-yellow { background: #fef9c3; color: #a16207; }
        .badge-blue { background: #dbeafe; color: #1d4ed8; }
        .badge-gray { background: #f1f5f1; color: #6b7d73; }
        .badge-orange { background: #ffedd5; color: #c2410c; }

        /* GDD progress */
        .gdd-wrap { min-width: 160px; }
        .gdd-label { display: flex; justify-content: space-between; font-size: 11px; color: #7a9484; margin-bottom: 5px; }
        .gdd-label strong { color: #1a2e24; font-weight: 600; }
        .gdd-bar-bg { background: #e9f5ee; border-radius: 20px; height: 7px; overflow: hidden; }
        .gdd-bar { border-radius: 20px; height: 100%; transition: width 0.6s ease; }
        .gdd-bar.siap { background: linear-gradient(90deg, #16a34a, #4ade80); }
        .gdd-bar.hampir { background: linear-gradient(90deg, #ca8a04, #fbbf24); }
        .gdd-bar.belum { background: linear-gradient(90deg, #2d6a4f, #4ade80); }

        /* action buttons */
        .btn-action { display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s; text-decoration: none; }
        .btn-detail { background: #dbeafe; color: #1d4ed8; }
        .btn-detail:hover { background: #bfdbfe; }
        .btn-hapus { background: #fee2e2; color: #b91c1c; }
        .btn-hapus:hover { background: #fecaca; }

        /* empty state */
        .empty-state { text-align: center; padding: 60px 20px; color: #7a9484; }
        .empty-state i { font-size: 48px; margin-bottom: 16px; opacity: 0.4; }
        .empty-state p { font-size: 14px; }

        /* chart card */
        .chart-card { background: #fff; border-radius: 16px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); }
        .chart-card h4 { font-size: 15px; font-weight: 700; color: #1a2e24; margin-bottom: 18px; }

        /* highlight siap panen */
        .panen-highlight { background: linear-gradient(135deg, #f0fdf4, #dcfce7); border: 1px solid #bbf7d0; border-radius: 14px; padding: 16px 20px; margin-bottom: 28px; display: flex; align-items: center; gap: 14px; }
        .panen-highlight i { font-size: 28px; color: #16a34a; }
        .panen-highlight-text h5 { font-size: 14px; font-weight: 700; color: #14532d; }
        .panen-highlight-text p { font-size: 12.5px; color: #166534; margin-top: 3px; }

        @media (max-width: 1100px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 700px) {
            .sidebar { width: 70px; }
            .sidebar-brand h1, .sidebar-brand span, .nav-item span, .sidebar-footer .logout-text { display: none; }
            .main { margin-left: 70px; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- ═══ SIDEBAR ═══ -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="logo">
            <div class="logo-icon">🥭</div>
            <div>
                <h1>Mango GDD</h1>
                <span>Monitoring System</span>
            </div>
        </div>
    </div>

    <nav class="sidebar-menu">
        <div class="menu-label">Menu Utama</div>

        <a href="/admin/dashboard" class="nav-item active">
            <div class="nav-icon"><i class="fa-solid fa-gauge-high"></i></div>
            <span>Dashboard</span>
        </a>
        <a href="/kebun" class="nav-item">
            <div class="nav-icon"><i class="fa-solid fa-seedling"></i></div>
            <span>Data Kebun</span>
        </a>
        <a href="/fase" class="nav-item">
            <div class="nav-icon"><i class="fa-solid fa-leaf"></i></div>
            <span>Fase Tanaman</span>
        </a>
        <a href="/suhu" class="nav-item">
            <div class="nav-icon"><i class="fa-solid fa-temperature-half"></i></div>
            <span>Suhu Harian</span>
        </a>
        <a href="/cuaca" class="nav-item {{ request()->is('cuaca*') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fa-solid fa-cloud-sun"></i></div>
            <span>Cuaca</span>
        </a>

        <div class="menu-label" style="margin-top:12px;">Sistem</div>
        <a href="/admin/petani" class="nav-item {{ request()->is('admin/petani*') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fa-solid fa-users"></i></div>
            <span>Data Petani</span>
        </a>
        <a href="/admin/dashboard#kebunTable" class="nav-item">
            <div class="nav-icon"><i class="fa-solid fa-chart-line"></i></div>
            <span>Monitoring Panen</span>
        </a>
        <a href="{{ route('riwayat.index') }}" class="nav-item {{ request()->is('riwayat*') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
            <span>Riwayat Panen</span>
        </a>
    </nav>

    {{-- WIDGET GDD HARI INI --}}
    @php
        $todayGdd = null;
        $todayDate = \Carbon\Carbon::today()->format('Y-m-d');
        $suhuToday = \App\Models\SuhuHarian::where('tanggal', $todayDate)->first();
        if($suhuToday) $todayGdd = $suhuToday->gdd;
    @endphp
    <div style="margin: 0 12px 20px; padding: 12px; background: rgba(255,255,255,0.06); border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); box-shadow: inset 0 2px 10px rgba(0,0,0,0.1);">
        <div style="font-size: 10px; color: #a7c3b4; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; font-weight: 700;">Pendapatan GDD Hari Ini</div>
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #f59e0b, #d97706); display: flex; align-items: center; justify-content: center; color: white; font-size: 16px; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);">
                <i class="fa-solid fa-sun"></i>
            </div>
            <div>
                <div style="color: #fff; font-size: 20px; font-weight: 800; line-height: 1;">{{ $todayGdd !== null ? $todayGdd : '...' }}</div>
                <div style="color: #86efac; font-size: 10px; margin-top: 4px;"><i class="fa-solid fa-clock"></i> Update Otomatis</div>
            </div>
        </div>
    </div>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <div class="nav-icon" style="background:rgba(239,68,68,0.12);color:#fca5a5;">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </div>
                <span class="logout-text">Keluar</span>
            </button>
        </form>
    </div>
</aside>

<!-- ═══ MAIN CONTENT ═══ -->
<div class="main">

    <!-- TOP NAV -->
    <header class="topnav">
        <div class="topnav-left">
            <h2>Admin Dashboard</h2>
            <p>Selamat datang kembali, {{ Auth::user()->name }}</p>
        </div>
        <div class="topnav-right">
            <div class="notif-btn">
                <i class="fa-solid fa-bell" style="font-size:15px;"></i>
                @if($siapPanen > 0)
                    <span class="notif-badge"></span>
                @endif
            </div>
            <div class="admin-avatar">
                <div class="avatar-circle">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <div class="info">
                    <strong>{{ Auth::user()->name }}</strong>
                    <span>Administrator</span>
                </div>
            </div>
        </div>
    </header>

    <!-- CONTENT -->
    <div class="content">

        <div class="content-header">
            <div>
                <h3>Ringkasan Sistem</h3>
                <span>Data real-time dari seluruh kebun yang terdaftar</span>
            </div>
            <span style="font-size:12px;color:#7a9484;"><i class="fa-regular fa-clock" style="margin-right:5px;"></i>{{ now()->isoFormat('dddd, D MMMM Y') }}</span>
        </div>

        <!-- HIGHLIGHT SIAP PANEN -->
        @if($siapPanen > 0)
        <div class="panen-highlight">
            <i class="fa-solid fa-circle-check"></i>
            <div class="panen-highlight-text">
                <h5>🎉 {{ $siapPanen }} Kebun Siap Panen Hari Ini!</h5>
                <p>Beberapa kebun telah mencapai target akumulasi GDD. Segera koordinasikan dengan pengepul.</p>
            </div>
        </div>
        @endif

        <!-- STATS CARDS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa-solid fa-tree"></i></div>
                <div class="stat-info">
                    <label>Total Kebun</label>
                    <div class="value">{{ $totalKebun }}</div>
                    <div class="sub">Terdaftar di sistem</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa-solid fa-users"></i></div>
                <div class="stat-info">
                    <label>Total Petani</label>
                    <div class="value">{{ $totalPetani }}</div>
                    <div class="sub">Akun aktif petani</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange"><i class="fa-solid fa-basket-shopping"></i></div>
                <div class="stat-info">
                    <label>Kebun Siap Panen</label>
                    <div class="value">{{ $siapPanen }}</div>
                    <div class="sub">GDD target tercapai</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon yellow"><i class="fa-solid fa-temperature-high"></i></div>
                <div class="stat-info">
                    <label>Rata-rata GDD</label>
                    <div class="value">{{ number_format($avgGdd, 1) }}</div>
                    <div class="sub">Unit panas akumulasi</div>
                </div>
            </div>
        </div>

        <!-- TABLE CARD -->
        <div class="table-card">
            <div class="table-header">
                <h4><i class="fa-solid fa-table-list" style="color:#2d6a4f;margin-right:8px;"></i>Daftar Kebun</h4>
                <div class="table-controls">
                    <div style="display:flex; gap:8px; margin-right: 12px; padding-right: 12px; border-right: 1px solid #e0e8e3;">
                        <a href="{{ route('export.admin.csv') }}" class="btn-action" style="background:#10b981; color:#fff;" title="Unduh Excel/CSV">
                            <i class="fa-solid fa-file-csv"></i> Excel
                        </a>
                        <a href="{{ route('export.admin.pdf') }}" target="_blank" class="btn-action" style="background:#ef4444; color:#fff;" title="Cetak PDF">
                            <i class="fa-solid fa-file-pdf"></i> PDF
                        </a>
                    </div>
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="searchInput" placeholder="Cari kebun atau lokasi..." onkeyup="filterTable()">
                    </div>
                    <select class="filter-select" id="filterFase" onchange="filterTable()">
                        <option value="">Semua Fase</option>
                        <option value="berbunga">Berbunga</option>
                        <option value="buah_kecil">Buah Kecil</option>
                        <option value="pembesaran">Pembesaran</option>
                        <option value="matang">Matang</option>
                    </select>
                    <select class="filter-select" id="filterStatus" onchange="filterTable()">
                        <option value="">Semua Status</option>
                        <option value="Siap Panen">Siap Panen</option>
                        <option value="Hampir">Hampir Panen</option>
                        <option value="Belum">Belum Panen</option>
                    </select>
                </div>
            </div>

            @if($allKebun->isEmpty())
                <div class="empty-state">
                    <i class="fa-solid fa-seedling"></i>
                    <p>Belum ada kebun yang terdaftar di sistem.</p>
                </div>
            @else
            <div style="overflow-x:auto;">
                <table id="kebunTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Kebun</th>
                            <th>Lokasi</th>
                            <th>Jenis Mangga</th>
                            <th>Fase Tanaman</th>
                            <th>Progress GDD</th>
                            <th>Status Panen</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allKebun as $i => $k)
                            @php
                                $fase      = $k->fase;
                                $faseName  = $k->fase_otomatis;
                                $progress  = $k->gdd_progress;
                                $total     = $k->total_gdd;
                                $target    = $k->target_gdd;
                                $siap      = $k->is_siap_panen;
                                $hampir    = $progress >= 75 && !$siap;

                                $statusLabel = $siap ? 'Siap Panen' : ($hampir ? 'Hampir' : 'Belum');
                                $statusBadge = $siap ? 'badge-green' : ($hampir ? 'badge-yellow' : 'badge-gray');
                                $barClass    = $siap ? 'siap' : ($hampir ? 'hampir' : 'belum');

                                $faseBadge = match($faseName) {
                                    'Berbunga'                    => 'badge-yellow',
                                    'Pembentukan Buah (Pentil)'   => 'badge-blue',
                                    'Pembesaran Buah'             => 'badge-orange',
                                    'Pematangan'                  => 'badge-green',
                                    'Siap Panen'                  => 'badge-green',
                                    default                       => 'badge-gray',
                                };
                                $faseLabel = match($faseName) {
                                    'Berbunga'                    => '🌸 Berbunga',
                                    'Pembentukan Buah (Pentil)'   => '🍃 Buah Kecil',
                                    'Pembesaran Buah'             => '🥭 Pembesaran',
                                    'Pematangan'                  => '✅ Matang',
                                    'Siap Panen'                  => '✅ Siap Panen',
                                    default                       => '— Belum Input',
                                };
                            @endphp
                            <tr data-fase="{{ $faseName }}" data-status="{{ $statusLabel }}">
                                <td style="color:#7a9484;font-size:12px;">{{ $i + 1 }}</td>
                                <td>
                                    <div class="kebun-name">{{ $k->nama_kebun }}</div>
                                    <div class="kebun-sub">{{ $k->jumlah_pohon }} pohon</div>
                                </td>
                                <td>
                                    <i class="fa-solid fa-location-dot" style="color:#7a9484;margin-right:4px;font-size:11px;"></i>
                                    {{ $k->lokasi }}
                                </td>
                                <td>{{ $k->jenis_mangga }}</td>
                                <td><span class="badge {{ $faseBadge }}">{{ $faseLabel }}</span></td>
                                <td>
                                    <div class="gdd-wrap">
                                        <div class="gdd-label">
                                            <strong>{{ number_format($total, 1) }} / {{ $target }} GDD</strong>
                                            <span>{{ $progress }}%</span>
                                        </div>
                                        <div class="gdd-bar-bg">
                                            <div class="gdd-bar {{ $barClass }}" style="width:{{ $progress }}%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span></td>
                                <td>
                                    <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                        <a href="/kebun/{{ $k->id }}/edit" class="btn-action btn-detail">
                                            <i class="fa-solid fa-eye"></i> Detail
                                        </a>
                                        <form action="/kebun/{{ $k->id }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus kebun ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-action btn-hapus">
                                                <i class="fa-solid fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px; margin-bottom: 28px;">
            <!-- CHART GDD TREND -->
            <div class="chart-card" style="margin-bottom: 0;">
                <h4><i class="fa-solid fa-chart-area" style="color:#2d6a4f;margin-right:8px;"></i>Trend GDD Per Kebun</h4>
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="gddChart"></canvas>
                </div>
            </div>

            <!-- CHART SUHU HISTORIS -->
            <div class="chart-card" style="margin-bottom: 0;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
                    <h4 style="margin-bottom: 0;"><i class="fa-solid fa-temperature-half" style="color:#ea580c;margin-right:8px;"></i>Prakiraan Suhu (7 Hari)</h4>
                    <select id="kebunSelect" class="filter-select" style="max-width: 180px; padding: 4px 8px;">
                        <option value="">-- Pilih Kebun --</option>
                        @foreach($allKebun as $k)
                            @if($k->latitude && $k->longitude)
                                <option value="{{ $k->id }}">{{ $k->nama_kebun }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                @php
                    $hasCoordinates = $allKebun->contains(fn($k) => $k->latitude && $k->longitude);
                @endphp

                @if($hasCoordinates)
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="suhuChart"></canvas>
                    </div>
                    <div id="suhuInsights" style="display: flex; justify-content: space-between; margin-top: 15px; font-size: 13px; color: #4b5563; display: none; padding-top: 10px; border-top: 1px solid #e5e7eb;">
                        <div>🔥 Hari Terpanas: <strong id="infoTmax" style="color:#ef4444;">-</strong></div>
                        <div>❄️ Hari Terdingin: <strong id="infoTmin" style="color:#3b82f6;">-</strong></div>
                        <div>🌱 Prediksi GDD (7 Hari): <strong id="infoGdd" style="color:#16a34a;">-</strong></div>
                    </div>
                @else
                    <div style="height: 200px; display: flex; align-items: center; justify-content: center; flex-direction: column; color: #6b7280; text-align: center; background: #f9fafb; border-radius: 8px; border: 1px dashed #d1d5db;">
                        <i class="fa-solid fa-map-location-dot" style="font-size: 2rem; margin-bottom: 10px; color: #9ca3af;"></i>
                        <p style="font-size: 14px;">Grafik tidak tersedia.<br>Belum ada satupun kebun yang memiliki titik koordinat GPS.</p>
                    </div>
                @endif
            </div>
        </div>

    </div><!-- /content -->
</div><!-- /main -->

<script>
    // ─── Filter Table ───
    function filterTable() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const filterFase = document.getElementById('filterFase').value.toLowerCase();
        const filterStatus = document.getElementById('filterStatus').value.toLowerCase();
        const rows = document.querySelectorAll('#kebunTable tbody tr');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            const fase = (row.dataset.fase || '').toLowerCase();
            const status = (row.dataset.status || '').toLowerCase();

            const matchSearch = text.includes(search);
            const matchFase = filterFase === '' || fase === filterFase;
            const matchStatus = filterStatus === '' || status.includes(filterStatus.toLowerCase());

            row.style.display = matchSearch && matchFase && matchStatus ? '' : 'none';
        });
    }

    // ─── GDD Trend Chart ───
    const labels = @json($allKebun->pluck('nama_kebun'));
    const totalData = @json($allKebun->map(fn($k) => $k->total_gdd));
    const targetData = @json($allKebun->map(fn($k) => $k->target_gdd));

    const ctx = document.getElementById('gddChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Total GDD Tercapai',
                    data: totalData,
                    backgroundColor: 'rgba(45, 106, 79, 0.7)',
                    borderRadius: 8,
                    borderSkipped: false,
                },
                {
                    label: 'Target GDD',
                    data: targetData,
                    backgroundColor: 'rgba(212, 237, 218, 0.6)',
                    borderRadius: 8,
                    borderSkipped: false,
                    borderColor: '#86efac',
                    borderWidth: 1.5,
                }
            ]
        },
        options: {
            responsive: true,
            indexAxis: 'y', // Mengubah menjadi horizontal bar chart
            plugins: {
                legend: { position: 'top', labels: { font: { family: 'Inter', size: 12 }, color: '#4b5563' } },
                tooltip: { callbacks: { label: ctx => `${ctx.dataset.label}: ${ctx.parsed.x} GDD` } }
            },
            scales: {
                x: { beginAtZero: true, grid: { color: '#f0f4f1' }, ticks: { color: '#7a9484', font: { family: 'Inter' } } },
                y: { grid: { display: false }, ticks: { color: '#4b5563', font: { family: 'Inter' } } }
            }
        }
    });

    // ─── Suhu Cuaca Chart via AJAX ───
    let suhuChart;

    async function loadSuhu(kebunId) {
        if (!kebunId) return;

        const chartCanvas = document.getElementById('suhuChart');
        chartCanvas.style.opacity = '0.5';

        try {
            const res = await fetch(`/api/cuaca/${kebunId}`);
            const data = await res.json();
            
            if(data.error) {
                alert(data.error);
                chartCanvas.style.opacity = '1';
                return;
            }

            if (!data.dates || !data.tmax || !data.tmin) {
                throw new Error("Format API tidak sesuai");
            }

            if (data.dates.length !== data.tmax.length || data.tmax.length !== data.tmin.length) {
                throw new Error("Data array tidak sinkron");
            }

            updateChart(data.dates, data.tmax, data.tmin);

            // Update Insights
            const maxTemp = Math.max(...data.tmax);
            const minTemp = Math.min(...data.tmin);
            
            // Hitung GDD
            let totalGdd = 0;
            for(let i = 0; i < data.tmax.length; i++) {
                let tavg = (data.tmax[i] + data.tmin[i]) / 2;
                let gdd = tavg - 10; // Base temp 10
                if (gdd < 0) gdd = 0;
                totalGdd += gdd;
            }

            document.getElementById('infoTmax').textContent = maxTemp + '°C';
            document.getElementById('infoTmin').textContent = minTemp + '°C';
            document.getElementById('infoGdd').textContent = '+ ' + totalGdd.toFixed(2);
            document.getElementById('suhuInsights').style.display = 'flex';

        } catch(error) {
            console.error("Gagal memuat cuaca", error);
            alert("Gagal mengambil data suhu dari server");
        } finally {
            chartCanvas.style.opacity = '1';
        }
    }

    function updateChart(labels, tmax, tmin) {
        if (suhuChart) {
            suhuChart.destroy();
        }

        const ctx = document.getElementById('suhuChart').getContext('2d');

        suhuChart = new Chart(ctx, {
            type: 'bar', // Mengubah menjadi bar chart agar bagus saat horizontal
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Suhu Tertinggi (Tmax)',
                        data: tmax,
                        backgroundColor: 'rgba(239, 68, 68, 0.7)',
                        borderRadius: 4,
                    },
                    {
                        label: 'Suhu Terendah (Tmin)',
                        data: tmin,
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y', // Chart horizontal
                plugins: {
                    legend: { position: 'top', labels: { font: { family: 'Inter', size: 12 }, color: '#4b5563' } }
                },
                scales: {
                    x: { beginAtZero: false, suggestedMin: 15, suggestedMax: 40, grid: { color: '#f0f4f1' }, ticks: { color: '#7a9484' } },
                    y: { grid: { display: false }, ticks: { color: '#4b5563' } }
                }
            }
        });
    }

    document.getElementById('kebunSelect').addEventListener('change', function () {
        loadSuhu(this.value);
    });

    // Load kebun pertama jika tersedia
    const kebunSelect = document.getElementById('kebunSelect');
    if (kebunSelect.options.length > 1) {
        kebunSelect.selectedIndex = 1;
        loadSuhu(kebunSelect.value);
    }
</script>

</body>
</html>
