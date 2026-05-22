<!DOCTYPE html>
<html lang="id">
<head>
    <title>Mango GDD System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f0f4f1; }
        .sidebar {
            width: 240px; min-height: 100vh;
            background: linear-gradient(180deg, #1b3a2d 0%, #14291f 100%);
            position: fixed; top: 0; left: 0; height: 100vh;
            display: flex; flex-direction: column;
            box-shadow: 4px 0 16px rgba(0,0,0,0.15);
            z-index: 100;
        }
        .sidebar-brand {
            padding: 20px 18px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar-brand .logo { display: flex; align-items: center; gap: 10px; }
        .sidebar-brand .logo-icon {
            width: 38px; height: 38px; border-radius: 10px;
            background: linear-gradient(135deg, #4ade80, #16a34a);
            display: flex; align-items: center; justify-content: center; font-size: 18px;
        }
        .sidebar-brand h5 { color: #fff; font-size: 14px; font-weight: 700; margin: 0; }
        .sidebar-brand small { color: #86efac; font-size: 11px; }

        .sidebar-menu { flex: 1; padding: 12px 10px; overflow-y: auto; }
        .menu-label { color: #5a7a67; font-size: 10px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 1.2px; padding: 12px 10px 5px; }
        .nav-link { display: flex; align-items: center; gap: 10px; padding: 10px 13px;
            border-radius: 9px; color: #a7c3b4; font-size: 13.5px; font-weight: 500;
            text-decoration: none; transition: all 0.2s; margin-bottom: 2px; }
        .nav-link:hover { background: rgba(255,255,255,0.07); color: #fff; }
        .nav-link.active { background: linear-gradient(135deg, #2d6a4f, #1e4d37);
            color: #fff; box-shadow: 0 3px 10px rgba(45,106,79,0.4); }
        .nav-link .icon { width: 32px; height: 32px; border-radius: 7px;
            background: rgba(255,255,255,0.06); display: flex;
            align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; }
        .nav-link.active .icon { background: rgba(255,255,255,0.15); }

        .sidebar-footer { padding: 12px 10px; border-top: 1px solid rgba(255,255,255,0.07); }
        .logout-btn { display: flex; align-items: center; gap: 10px; width: 100%;
            padding: 10px 13px; border-radius: 9px; background: none; border: none;
            color: #fca5a5; font-size: 13.5px; font-weight: 500; cursor: pointer; transition: all 0.2s; }
        .logout-btn:hover { background: rgba(239,68,68,0.12); color: #f87171; }

        .role-badge { display: inline-block; padding: 3px 10px; border-radius: 20px;
            font-size: 10px; font-weight: 700; letter-spacing: 0.5px; margin-top: 5px; }
        .role-admin { background: rgba(251,191,36,0.2); color: #fbbf24; }
        .role-petani { background: rgba(74,222,128,0.2); color: #4ade80; }
        .role-pengepul { background: rgba(96,165,250,0.2); color: #60a5fa; }

        .main-content { margin-left: 240px; padding: 30px; }
        .topbar { background: #fff; border-radius: 12px; padding: 16px 22px;
            margin-bottom: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex; align-items: center; justify-content: space-between; }
        .topbar h4 { margin: 0; font-size: 16px; font-weight: 700; color: #1a2e24; }
        .topbar small { color: #7a9484; font-size: 12px; }
    </style>
</head>
<body>

<div class="d-flex">
    <!-- ═══ SIDEBAR ═══ -->
    <div class="sidebar">
        <div class="sidebar-brand">
            @auth
            @php
                $dashboardUrl = match(Auth::user()->role) {
                    'admin'    => '/admin/dashboard',
                    'petani'   => '/petani/dashboard',
                    'pengepul' => '/pengepul/dashboard',
                    default    => '/dashboard',
                };
            @endphp
            <a href="{{ $dashboardUrl }}" style="text-decoration:none;">
            @endauth
            <div class="logo">
                <img src="/images/logoManggo.jpeg" alt="Mango GDD Logo"
                     style="height:42px;width:auto;flex-shrink:0;">
                <div>
                    <h5>Mango GDD</h5>
                    <small>Monitoring System</small>
                </div>
            </div>
            @auth
            </a>
            @endauth
            @auth
            <div class="mt-3 px-1">
                <div style="color:#d1fae5;font-size:13px;font-weight:600;">{{ Auth::user()->name }}</div>
                <span class="role-badge role-{{ Auth::user()->role }}">
                    {{ ucfirst(Auth::user()->role) }}
                </span>
            </div>

            {{-- WIDGET GDD HARI INI --}}
            @php
                $todayGdd = null;
                $todayDate = \Carbon\Carbon::today()->format('Y-m-d');
                if (Auth::user()->role === 'petani') {
                    $kebunIds = \App\Models\Kebun::where('user_id', Auth::id())->pluck('id');
                    $suhuToday = \App\Models\SuhuHarian::whereIn('kebun_id', $kebunIds)->where('tanggal', $todayDate)->first();
                    if($suhuToday) $todayGdd = $suhuToday->gdd;
                } else {
                    $suhuToday = \App\Models\SuhuHarian::where('tanggal', $todayDate)->first();
                    if($suhuToday) $todayGdd = $suhuToday->gdd;
                }
            @endphp
            <div style="margin: 20px 5px 5px; padding: 12px; background: rgba(255,255,255,0.06); border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); box-shadow: inset 0 2px 10px rgba(0,0,0,0.1);">
                <div style="font-size: 10px; color: #a7c3b4; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; font-weight: 700;">Akumulasi GDD Hari Ini</div>
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
            @endauth
        </div>

        <nav class="sidebar-menu">
            <div class="menu-label">Menu</div>

            @auth
                @if(Auth::user()->role === 'admin')
                    {{-- ── MENU ADMIN ── --}}
                    <a href="/admin/dashboard" class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                        <div class="icon"><i class="fa-solid fa-gauge-high"></i></div> Dashboard
                    </a>
                    <a href="/admin/petani" class="nav-link {{ request()->is('admin/petani*') ? 'active' : '' }}">
                        <div class="icon"><i class="fa-solid fa-users"></i></div> Data Petani
                    </a>
                    <a href="/kebun" class="nav-link {{ request()->is('kebun*') ? 'active' : '' }}">
                        <div class="icon"><i class="fa-solid fa-seedling"></i></div> Data Kebun
                    </a>
                    <a href="/fase" class="nav-link {{ request()->is('fase*') ? 'active' : '' }}">
                        <div class="icon"><i class="fa-solid fa-leaf"></i></div> Fase Tanaman
                    </a>
                    <a href="/suhu" class="nav-link {{ request()->is('suhu*') ? 'active' : '' }}">
                        <div class="icon"><i class="fa-solid fa-temperature-half"></i></div> Suhu Harian
                    </a>
                    <a href="/cuaca" class="nav-link {{ request()->is('cuaca*') ? 'active' : '' }}">
                        <div class="icon"><i class="fa-solid fa-cloud-sun"></i></div> Cuaca
                    </a>

                    <div class="menu-label" style="margin-top:10px;">Monitoring</div>
                    <a href="/admin/dashboard" class="nav-link {{ request()->is('admin/monitoring*') ? 'active' : '' }}">
                        <div class="icon"><i class="fa-solid fa-chart-line"></i></div> Monitoring Panen
                    </a>

                @elseif(Auth::user()->role === 'petani')
                    {{-- ── MENU PETANI ── --}}
                    <a href="/petani/dashboard" class="nav-link {{ request()->is('petani/dashboard') ? 'active' : '' }}">
                        <div class="icon"><i class="fa-solid fa-house"></i></div> Dashboard
                    </a>
                    <a href="/kebun" class="nav-link {{ request()->is('kebun*') ? 'active' : '' }}">
                        <div class="icon"><i class="fa-solid fa-seedling"></i></div> Kebun Saya
                    </a>
                    <a href="/fase" class="nav-link {{ request()->is('fase*') ? 'active' : '' }}">
                        <div class="icon"><i class="fa-solid fa-leaf"></i></div> Fase Tanaman
                    </a>
                    <a href="/suhu" class="nav-link {{ request()->is('suhu*') ? 'active' : '' }}">
                        <div class="icon"><i class="fa-solid fa-temperature-half"></i></div> Input Suhu
                    </a>
                    <a href="/cuaca" class="nav-link {{ request()->is('cuaca*') ? 'active' : '' }}">
                        <div class="icon"><i class="fa-solid fa-cloud-sun"></i></div> Cuaca
                    </a>

                @elseif(Auth::user()->role === 'pengepul')
                    {{-- ── MENU PENGEPUL ── --}}
                    <a href="/pengepul/dashboard" class="nav-link {{ request()->is('pengepul/dashboard') ? 'active' : '' }}">
                        <div class="icon"><i class="fa-solid fa-gauge-high"></i></div> Dashboard
                    </a>
                    <a href="/pengepul/dashboard#kebunTable" class="nav-link">
                        <div class="icon"><i class="fa-solid fa-chart-line"></i></div> Monitoring Panen
                    </a>
                @endif
            @endauth
        </nav>

        <div class="sidebar-footer">
            @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <div class="icon" style="background:rgba(239,68,68,0.12);color:#fca5a5;">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </div>
                    Keluar
                </button>
            </form>
            @endauth
        </div>
    </div>

    <!-- ═══ MAIN CONTENT ═══ -->
    <div class="main-content w-100">
        <div class="topbar">
            <div>
                <h4>
                    @if(request()->is('kebun*')) 📋 Manajemen Kebun
                    @elseif(request()->is('fase*')) 🌸 Fase Tanaman
                    @elseif(request()->is('suhu*')) 🌡️ Suhu Harian & GDD
                    @elseif(request()->is('cuaca*')) 🌤️ Monitoring Cuaca
                    @else 🥭 Mango GDD System
                    @endif
                </h4>
                <small>{{ now()->isoFormat('dddd, D MMMM Y') }}</small>
            </div>
            @auth
            <div style="color:#7a9484;font-size:13px;">
                <i class="fa-solid fa-user-circle" style="margin-right:5px;"></i>
                {{ Auth::user()->name }}
                <span class="badge ms-1
                    {{ Auth::user()->role === 'admin' ? 'bg-warning text-dark' :
                       (Auth::user()->role === 'petani' ? 'bg-success' : 'bg-primary') }}">
                    {{ ucfirst(Auth::user()->role) }}
                </span>
            </div>
            @endauth
        </div>

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>