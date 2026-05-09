<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengepul — Mango GDD</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',sans-serif;background:#f0f2f5;color:#1e2d24;display:flex;min-height:100vh}
        .sidebar{width:260px;min-height:100vh;background:linear-gradient(180deg,#1e293b 0%,#0f172a 100%);display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;z-index:100;box-shadow:4px 0 20px rgba(0,0,0,.18)}
        .sidebar-brand{padding:24px 20px 20px;border-bottom:1px solid rgba(255,255,255,.08)}
        .sidebar-brand .logo{display:flex;align-items:center;gap:10px}
        .sidebar-brand .logo-icon{width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#60a5fa,#3b82f6);display:flex;align-items:center;justify-content:center;font-size:20px}
        .sidebar-brand h1{color:#fff;font-size:15px;font-weight:700;line-height:1.2}
        .sidebar-brand span{color:#93c5fd;font-size:11px;font-weight:400}
        .sidebar-menu{flex:1;padding:16px 12px;overflow-y:auto}
        .menu-label{color:#64748b;font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:1.5px;padding:12px 10px 6px}
        .nav-item{display:flex;align-items:center;gap:12px;padding:11px 14px;border-radius:10px;color:#94a3b8;font-size:14px;font-weight:500;text-decoration:none;transition:all .2s;margin-bottom:2px;cursor:pointer}
        .nav-item:hover{background:rgba(255,255,255,.07);color:#fff}
        .nav-item.active{background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;box-shadow:0 4px 12px rgba(59,130,246,.4)}
        .nav-item .nav-icon{width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:15px;background:rgba(255,255,255,.06);flex-shrink:0}
        .nav-item.active .nav-icon{background:rgba(255,255,255,.2)}
        .sidebar-footer{padding:16px 12px;border-top:1px solid rgba(255,255,255,.07)}
        .logout-btn{display:flex;align-items:center;gap:12px;padding:11px 14px;border-radius:10px;color:#fca5a5;font-size:14px;font-weight:500;text-decoration:none;transition:all .2s;width:100%;background:none;border:none;cursor:pointer}
        .logout-btn:hover{background:rgba(239,68,68,.12);color:#f87171}
        .logout-btn .nav-icon{background:rgba(239,68,68,.12)}
        .main{margin-left:260px;flex:1;display:flex;flex-direction:column;min-height:100vh}
        .topnav{background:#fff;height:68px;display:flex;align-items:center;justify-content:space-between;padding:0 28px;box-shadow:0 1px 12px rgba(0,0,0,.06);position:sticky;top:0;z-index:50}
        .topnav-left h2{font-size:18px;font-weight:700;color:#1a2e24}
        .topnav-left p{font-size:12px;color:#7a9484;margin-top:1px}
        .topnav-right{display:flex;align-items:center;gap:16px}
        .notif-btn{width:40px;height:40px;border-radius:10px;background:#eff6ff;border:1px solid #bfdbfe;display:flex;align-items:center;justify-content:center;color:#2563eb;cursor:pointer;position:relative;transition:background .2s}
        .notif-btn:hover{background:#dbeafe}
        .notif-badge{position:absolute;top:6px;right:6px;width:8px;height:8px;background:#ef4444;border-radius:50%;border:2px solid #fff}
        .avatar-wrap{display:flex;align-items:center;gap:10px;cursor:pointer;padding:6px 10px;border-radius:12px;transition:background .2s}
        .avatar-wrap:hover{background:#f0f4f1}
        .avatar-circle{width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#60a5fa);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:14px}
        .avatar-wrap .info{line-height:1.3}
        .avatar-wrap .info strong{font-size:13px;color:#1a2e24}
        .avatar-wrap .info span{font-size:11px;color:#7a9484;display:block}
        .content{padding:28px;flex:1}
        .content-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px}
        .content-header h3{font-size:15px;font-weight:600;color:#1a2e24}
        .content-header span{font-size:13px;color:#7a9484}
        .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-bottom:28px}
        .stat-card{background:#fff;border-radius:16px;padding:22px;box-shadow:0 2px 12px rgba(0,0,0,.05);display:flex;align-items:center;gap:16px;transition:transform .2s,box-shadow .2s}
        .stat-card:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.08)}
        .stat-icon{width:54px;height:54px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0}
        .stat-icon.green{background:#dcfce7;color:#16a34a}
        .stat-icon.blue{background:#dbeafe;color:#2563eb}
        .stat-icon.orange{background:#ffedd5;color:#ea580c}
        .stat-icon.yellow{background:#fef9c3;color:#ca8a04}
        .stat-info label{font-size:12px;color:#7a9484;font-weight:500;display:block;margin-bottom:4px}
        .stat-info .value{font-size:28px;font-weight:700;color:#1a2e24;line-height:1}
        .stat-info .sub{font-size:11px;color:#a5b4a9;margin-top:4px}
        .panen-highlight{background:linear-gradient(135deg,#eff6ff,#dbeafe);border:1px solid #93c5fd;border-radius:14px;padding:16px 20px;margin-bottom:28px;display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap}
        .panen-highlight-left{display:flex;align-items:center;gap:14px}
        .panen-highlight i{font-size:28px;color:#2563eb}
        .panen-highlight-text h5{font-size:14px;font-weight:700;color:#1e3a5f}
        .panen-highlight-text p{font-size:12.5px;color:#1e40af;margin-top:3px}
        .btn-cta{display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:10px;font-size:13px;font-weight:600;border:none;cursor:pointer;transition:all .2s;text-decoration:none;background:#2563eb;color:#fff;box-shadow:0 2px 8px rgba(37,99,235,.3)}
        .btn-cta:hover{background:#1d4ed8;transform:translateY(-1px)}
        .table-card{background:#fff;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.05);overflow:hidden;margin-bottom:28px}
        .table-header{display:flex;justify-content:space-between;align-items:center;padding:20px 24px;border-bottom:1px solid #f0f4f1;flex-wrap:wrap;gap:12px}
        .table-header h4{font-size:16px;font-weight:700;color:#1a2e24}
        .table-controls{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
        .search-box{display:flex;align-items:center;gap:8px;background:#f5f7f5;border:1px solid #e0e8e3;border-radius:10px;padding:8px 14px}
        .search-box input{border:none;background:transparent;outline:none;font-size:13px;color:#1a2e24;width:180px}
        .search-box i{color:#7a9484;font-size:13px}
        .filter-select{border:1px solid #e0e8e3;background:#f5f7f5;border-radius:10px;padding:8px 14px;font-size:13px;color:#1a2e24;outline:none;cursor:pointer}
        table{width:100%;border-collapse:collapse}
        thead th{background:#f8faf8;color:#7a9484;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;padding:13px 18px;text-align:left;white-space:nowrap}
        tbody tr{border-bottom:1px solid #f0f4f1;transition:background .15s}
        tbody tr:last-child{border-bottom:none}
        tbody tr:hover{background:#f8faf8}
        td{padding:14px 18px;font-size:13.5px;color:#2d3b33;vertical-align:middle}
        .kebun-name{font-weight:600;color:#1a2e24}
        .kebun-sub{font-size:11.5px;color:#7a9484}
        .badge{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:600;white-space:nowrap}
        .badge-green{background:#dcfce7;color:#15803d}
        .badge-yellow{background:#fef9c3;color:#a16207}
        .badge-blue{background:#dbeafe;color:#1d4ed8}
        .badge-gray{background:#f1f5f1;color:#6b7d73}
        .badge-orange{background:#ffedd5;color:#c2410c}
        .gdd-wrap{min-width:160px}
        .gdd-label{display:flex;justify-content:space-between;font-size:11px;color:#7a9484;margin-bottom:5px}
        .gdd-label strong{color:#1a2e24;font-weight:600}
        .gdd-bar-bg{background:#e9f5ee;border-radius:20px;height:7px;overflow:hidden}
        .gdd-bar{border-radius:20px;height:100%;transition:width .6s ease}
        .gdd-bar.siap{background:linear-gradient(90deg,#16a34a,#4ade80)}
        .gdd-bar.hampir{background:linear-gradient(90deg,#ca8a04,#fbbf24)}
        .gdd-bar.belum{background:linear-gradient(90deg,#3b82f6,#60a5fa)}
        .empty-state{text-align:center;padding:60px 20px;color:#7a9484}
        .empty-state i{font-size:48px;margin-bottom:16px;opacity:.4}
        .empty-state p{font-size:14px}
        .chart-card{background:#fff;border-radius:16px;padding:24px;box-shadow:0 2px 12px rgba(0,0,0,.05)}
        .chart-card h4{font-size:15px;font-weight:700;color:#1a2e24;margin-bottom:18px}
        /* Notification Dropdown Styles */
        .notif-dropdown{position:absolute;top:50px;right:-10px;width:340px;background:#fff;border-radius:14px;box-shadow:0 10px 40px rgba(0,0,0,.15);border:1px solid #e2e8f0;display:none;flex-direction:column;z-index:100;overflow:hidden;transform-origin:top right;animation:slideDown .2s ease}
        @keyframes slideDown{from{opacity:0;transform:scale(.95) translateY(-10px)}to{opacity:1;transform:scale(1) translateY(0)}}
        .notif-header{padding:14px 18px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;background:#f8fafc}
        .notif-header h5{font-size:14px;font-weight:700;color:#0f172a}
        .notif-header form{margin:0;}
        .notif-header button{background:none;border:none;color:#2563eb;font-size:12px;font-weight:600;cursor:pointer;transition:color .2s}
        .notif-header button:hover{color:#1d4ed8}
        .notif-list{max-height:320px;overflow-y:auto;}
        .notif-item{padding:14px 18px;border-bottom:1px solid #f1f5f9;display:flex;gap:12px;transition:background .2s}
        .notif-item:last-child{border-bottom:none}
        .notif-item.unread{background:#eff6ff}
        .notif-item:hover{background:#f8fafc}
        .notif-icon{width:36px;height:36px;border-radius:50%;background:#dbeafe;color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0}
        .notif-content p{font-size:12.5px;color:#334155;line-height:1.4;margin-bottom:6px}
        .notif-content span{font-size:11px;color:#94a3b8}
        .notif-empty{padding:30px 20px;text-align:center;color:#94a3b8;font-size:13px}
        @media(max-width:1100px){.stats-grid{grid-template-columns:repeat(2,1fr)}}
        @media(max-width:700px){.sidebar{width:70px}.sidebar-brand h1,.sidebar-brand span,.nav-item span,.sidebar-footer .logout-text{display:none}.main{margin-left:70px}.stats-grid{grid-template-columns:1fr}}
    </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <a href="/pengepul/dashboard" style="text-decoration:none;">
        <div class="logo">
            <div class="logo-icon">🥭</div>
            <div>
                <h1>Mango GDD</h1>
                <span>Pengepul Portal</span>
            </div>
        </div>
        </a>
        <div style="margin-top:14px;padding-left:2px;">
            <div style="color:#e2e8f0;font-size:13px;font-weight:600;">{{ Auth::user()->name }}</div>
            <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;letter-spacing:.5px;margin-top:5px;background:rgba(96,165,250,.2);color:#60a5fa;">Pengepul</span>
        </div>
    </div>

    <nav class="sidebar-menu">
        <div class="menu-label">Menu Utama</div>
        <a href="/pengepul/dashboard" class="nav-item active">
            <div class="nav-icon"><i class="fa-solid fa-gauge-high"></i></div>
            <span>Dashboard</span>
        </a>
        <a href="/pengepul/dashboard#kebunTable" class="nav-item" onclick="document.getElementById('kebunTable')?.scrollIntoView({behavior:'smooth'})">
            <div class="nav-icon"><i class="fa-solid fa-chart-line"></i></div>
            <span>Monitoring Panen</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <div class="nav-icon" style="background:rgba(239,68,68,.12);color:#fca5a5;">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </div>
                <span class="logout-text">Keluar</span>
            </button>
        </form>
    </div>
</aside>

<!-- MAIN CONTENT -->
<div class="main">

    <!-- TOP NAV -->
    <header class="topnav">
        <div class="topnav-left">
            <h2>Dashboard Pengepul</h2>
            <p>Pantau status panen kebun dari seluruh petani</p>
        </div>
        <div class="topnav-right">
            <div style="position:relative;">
                @php $unreadCount = Auth::user()->unreadNotifications()->count(); @endphp
                <div class="notif-btn" onclick="document.getElementById('notifDropdown').style.display = document.getElementById('notifDropdown').style.display === 'flex' ? 'none' : 'flex'" title="Notifikasi">
                    <i class="fa-solid fa-bell" style="font-size:15px;"></i>
                    @if($unreadCount > 0)
                        <span class="notif-badge" style="display:flex;align-items:center;justify-content:center;width:14px;height:14px;font-size:9px;font-weight:bold;color:white;top:1px;right:1px;">{{ $unreadCount }}</span>
                    @endif
                </div>
                
                <div class="notif-dropdown" id="notifDropdown">
                    <div class="notif-header">
                        <h5>Notifikasi Panen</h5>
                        @if($unreadCount > 0)
                        <form action="{{ route('notifications.readAll') }}" method="POST">
                            @csrf
                            <button type="submit">Tandai Semua Dibaca</button>
                        </form>
                        @endif
                    </div>
                    <div class="notif-list">
                        @forelse(Auth::user()->notifications()->take(10)->get() as $notification)
                        <div class="notif-item {{ $notification->read_at ? '' : 'unread' }}">
                            <div class="notif-icon"><i class="fa-solid fa-truck-fast"></i></div>
                            <div class="notif-content" style="flex:1;">
                                <p>{{ $notification->data['pesan'] ?? 'Notifikasi baru' }}</p>
                                <div style="display:flex;justify-content:space-between;align-items:center;">
                                    <span>{{ $notification->created_at->diffForHumans() }}</span>
                                    @if(!$notification->read_at)
                                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST" style="margin:0;">
                                        @csrf
                                        <button type="submit" style="background:none;border:none;color:#2563eb;font-size:11px;font-weight:600;cursor:pointer;">Tandai Dibaca</button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="notif-empty">
                            <i class="fa-regular fa-bell-slash" style="font-size:24px;margin-bottom:8px;opacity:0.5;"></i>
                            <br>Belum ada info panen baru
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="avatar-wrap">
                <div class="avatar-circle">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <div class="info">
                    <strong>{{ Auth::user()->name }}</strong>
                    <span>Pengepul</span>
                </div>
            </div>
        </div>
    </header>

    <!-- CONTENT -->
    <div class="content">

        <div class="content-header">
            <div>
                <h3>Ringkasan Monitoring Panen</h3>
                <span>Data real-time dari seluruh kebun petani yang terdaftar</span>
            </div>
            <span style="font-size:12px;color:#7a9484;"><i class="fa-regular fa-clock" style="margin-right:5px;"></i>{{ now()->isoFormat('dddd, D MMMM Y') }}</span>
        </div>

        <!-- ALERT SIAP PANEN + CTA -->
        @if($siapPanen > 0)
        <div class="panen-highlight">
            <div class="panen-highlight-left">
                <i class="fa-solid fa-basket-shopping"></i>
                <div class="panen-highlight-text">
                    <h5>🎉 {{ $siapPanen }} Kebun Siap Panen Hari Ini!</h5>
                    <p>Ada potensi panen yang bisa Anda koordinasikan dengan petani sekarang.</p>
                </div>
            </div>
            <a href="#kebunTable" class="btn-cta" onclick="document.getElementById('kebunTable')?.scrollIntoView({behavior:'smooth'})">
                <i class="fa-solid fa-eye"></i> Lihat Kebun Siap Panen
            </a>
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
                <div class="stat-icon yellow"><i class="fa-solid fa-hourglass-half"></i></div>
                <div class="stat-info">
                    <label>Hampir Panen</label>
                    <div class="value">{{ $hampirPanen }}</div>
                    <div class="sub">GDD ≥ 70%</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange"><i class="fa-solid fa-basket-shopping"></i></div>
                <div class="stat-info">
                    <label>Potensi Panen Hari Ini</label>
                    <div class="value">{{ $siapPanen }}</div>
                    <div class="sub">GDD target tercapai</div>
                </div>
            </div>
        </div>

        <!-- CHART + TABLE GRID -->
        <div style="display:grid;grid-template-columns:1fr 320px;gap:24px;margin-bottom:28px;">

            <!-- TABLE CARD -->
            <div class="table-card" id="kebunTable" style="margin-bottom:0;">
                <div class="table-header">
                    <h4><i class="fa-solid fa-table-list" style="color:#2563eb;margin-right:8px;"></i>Monitoring Kebun Petani</h4>
                    <div class="table-controls">
                        <div style="display:flex; gap:8px; margin-right: 12px; padding-right: 12px; border-right: 1px solid #e0e8e3;">
                            <a href="{{ route('export.pengepul.csv') }}" class="btn-action" style="background:#10b981; color:#fff;" title="Unduh Excel/CSV">
                                <i class="fa-solid fa-file-csv"></i> Excel
                            </a>
                            <a href="{{ route('export.pengepul.pdf') }}" target="_blank" class="btn-action" style="background:#ef4444; color:#fff;" title="Cetak Rute PDF">
                                <i class="fa-solid fa-file-pdf"></i> PDF
                            </a>
                        </div>
                        <div class="search-box">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" id="searchInput" placeholder="Cari kebun / petani..." onkeyup="filterTable()">
                        </div>
                        <select class="filter-select" id="filterStatus" onchange="filterTable()">
                            <option value="">Semua Status</option>
                            <option value="Siap Panen">Siap Panen</option>
                            <option value="Hampir">Hampir Panen</option>
                            <option value="Berkembang">Sedang Berkembang</option>
                            <option value="Awal">Awal Pertumbuhan</option>
                        </select>
                    </div>
                </div>

                @if($allKebun->isEmpty())
                    <div class="empty-state">
                        <i class="fa-solid fa-seedling"></i>
                        <p>Belum ada kebun yang terdaftar di sistem.</p>
                    </div>
                @else
                <div style="overflow-x:auto;max-height:500px;overflow-y:auto;">
                    <table id="dataTable">
                        <thead><tr>
                            <th>#</th><th>Nama Kebun</th><th>Petani</th><th>Lokasi</th><th>Varietas</th><th>Progress GDD</th><th>Status</th>
                        </tr></thead>
                        <tbody>
                            @foreach($allKebun as $i => $k)
                                @php
                                    $fase = $k->fase;
                                    $faseName = $k->fase_otomatis;
                                    $progress = min(100, $k->gdd_progress);
                                    $total = $k->total_gdd;
                                    $target = $k->target_gdd;
                                    $siap = $progress >= 100;

                                    if ($progress < 30) { $statusLabel='Awal Pertumbuhan'; $statusBadge='badge-gray'; $barClass='belum'; }
                                    elseif ($progress < 70) { $statusLabel='Sedang Berkembang'; $statusBadge='badge-blue'; $barClass='belum'; }
                                    elseif ($progress < 100) { $statusLabel='Hampir Panen'; $statusBadge='badge-yellow'; $barClass='hampir'; }
                                    else { $statusLabel='Siap Panen'; $statusBadge='badge-green'; $barClass='siap'; }
                                @endphp
                                <tr data-status="{{ $statusLabel }}">
                                    <td style="color:#7a9484;font-size:12px;">{{ $i + 1 }}</td>
                                    <td>
                                        <div class="kebun-name">{{ $k->nama_kebun }}</div>
                                        <div class="kebun-sub">{{ $k->jumlah_pohon }} pohon</div>
                                    </td>
                                    <td>{{ $k->user->name ?? '-' }}</td>
                                    <td><i class="fa-solid fa-location-dot" style="color:#7a9484;margin-right:4px;font-size:11px;"></i>{{ $k->lokasi }}</td>
                                    <td>{{ $k->jenis_mangga }}</td>
                                    <td>
                                        <div class="gdd-wrap">
                                            <div class="gdd-label">
                                                <strong>{{ number_format($total, 1) }} / {{ $target }}</strong>
                                                <span>{{ $progress }}%</span>
                                            </div>
                                            <div class="gdd-bar-bg">
                                                <div class="gdd-bar {{ $barClass }}" style="width:{{ $progress }}%;"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            <!-- DOUGHNUT CHART -->
            <div class="chart-card" style="margin-bottom:0;height:fit-content;">
                <h4><i class="fa-solid fa-chart-pie" style="color:#2563eb;margin-right:8px;"></i>Distribusi Status</h4>
                <canvas id="statusChart" height="260"></canvas>
                <div style="margin-top:16px;font-size:12px;color:#64748b;line-height:1.8;">
                    <div><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#16a34a;margin-right:6px;"></span>Siap Panen: <strong>{{ $siapPanen }}</strong></div>
                    <div><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#eab308;margin-right:6px;"></span>Hampir Panen: <strong>{{ $hampirPanen }}</strong></div>
                    <div><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#3b82f6;margin-right:6px;"></span>Belum Panen: <strong>{{ $totalKebun - $siapPanen - $hampirPanen }}</strong></div>
                </div>
            </div>

        </div>

    </div><!-- /content -->
</div><!-- /main -->

<script>
function filterTable(){
    const search=document.getElementById('searchInput').value.toLowerCase();
    const status=document.getElementById('filterStatus').value.toLowerCase();
    document.querySelectorAll('#dataTable tbody tr').forEach(row=>{
        const text=row.innerText.toLowerCase();
        const rs=(row.dataset.status||'').toLowerCase();
        const ms=!status||rs.includes(status);
        row.style.display=(text.includes(search)&&ms)?'':'none';
    });
}

// Doughnut Chart
new Chart(document.getElementById('statusChart').getContext('2d'),{
    type:'doughnut',
    data:{
        labels:['Siap Panen','Hampir Panen','Belum Panen'],
        datasets:[{
            data:[{{ $siapPanen }},{{ $hampirPanen }},{{ $totalKebun - $siapPanen - $hampirPanen }}],
            backgroundColor:['#16a34a','#eab308','#3b82f6'],
            borderWidth:3,borderColor:'#fff',hoverOffset:8
        }]
    },
    options:{
        responsive:true,cutout:'65%',
        plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>c.label+': '+c.raw+' kebun'}}}
    }
});
</script>
</body>
</html>
