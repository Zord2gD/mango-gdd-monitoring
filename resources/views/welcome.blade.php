<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MangoGDD - Prediksi Panen Presisi</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #fafafa; color: #111827; }
        .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.4); }
        .floating { animation: floating 6s ease-in-out infinite; }
        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }
        .green-glow { position: absolute; width: 300px; height: 300px; background: rgba(34, 197, 94, 0.15); filter: blur(80px); border-radius: 50%; z-index: -1; }
    </style>
</head>
<body class="antialiased overflow-x-hidden">

    <!-- ── NAVBAR ── -->
    <nav class="fixed w-full z-50 transition-all duration-300 bg-white/90 backdrop-blur-md border-b border-gray-100">
        <div class="w-full mx-auto px-6 lg:px-16 xl:px-24">
            <div class="flex justify-between items-center h-20">
                <!-- Logo (Teks + Ikon Daun sementara sesuai persetujuan) -->
                <div class="flex-shrink-0 flex items-center gap-2 cursor-pointer">
                    <img src="/images/logoManggo.jpeg" alt="MangoGDD Logo"
                         class="h-9 w-auto">
                    <span class="font-bold text-xl tracking-tight text-gray-900">Mango<span class="text-brand-600">GDD</span></span>
                </div>

                <!-- Nav Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#" class="text-brand-600 font-semibold text-sm border-b-2 border-brand-600 pb-1">Home</a>
                    <a href="#fitur" class="text-gray-500 hover:text-gray-900 font-medium text-sm transition">Fitur</a>
                    <a href="#carakerja" class="text-gray-500 hover:text-gray-900 font-medium text-sm transition">Cara Kerja</a>
                    <a href="#sistem" class="text-gray-500 hover:text-gray-900 font-medium text-sm transition">Sistem</a>
                </div>

                <!-- Auth Buttons -->
                <div class="flex items-center space-x-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-gray-700 font-medium text-sm hover:text-brand-600 transition">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 font-medium text-sm hover:text-brand-600 transition">Login</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-brand-600 text-white px-5 py-2.5 rounded-full font-semibold text-sm hover:bg-brand-700 shadow-lg shadow-brand-500/30 transition transform hover:-translate-y-0.5">Sign Up</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- ── HERO SECTION ── -->
    <div class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <!-- Background Image & Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="/images/backgroundmangga.jpeg" alt="Kebun Mangga" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-gray-900/95 via-gray-900/80 to-transparent"></div>
            <div class="absolute inset-0 bg-brand-900/30 mix-blend-multiply"></div>
        </div>

        <!-- Glowing Orbs -->
        <div class="green-glow top-20 left-20 opacity-40 z-0"></div>
        <div class="green-glow bottom-0 right-20 opacity-30 z-0" style="background: rgba(34, 197, 94, 0.2);"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                
                <!-- Hero Text -->
                <div>
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-black/40 backdrop-blur-md border border-white/20 text-white text-xs font-bold mb-6 shadow-lg">
                        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse shadow-[0_0_8px_rgba(74,222,128,0.8)]"></span>
                        Sistem Berbasis SaaS
                    </div>
                    <h1 class="text-5xl lg:text-6xl font-extrabold text-white leading-[1.1] tracking-tight mb-6 drop-shadow-lg">
                        Prediksi Panen <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-400 to-green-300 drop-shadow-sm">Presisi</span> dengan GDD
                    </h1>
                    <p class="text-lg text-gray-300 leading-relaxed mb-10 max-w-lg drop-shadow-md">
                        Maksimalkan produksi kebun mangga Anda dengan teknologi akumulasi unit panas harian (Growing Degree Days) untuk estimasi waktu panen yang terotomatisasi.
                    </p>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('register') }}" class="bg-brand-500 text-white px-8 py-3.5 rounded-full font-bold text-sm hover:bg-brand-600 shadow-xl shadow-brand-500/40 transition transform hover:-translate-y-1">
                            Mulai Gratis Sekarang
                        </a>
                        <!-- Tombol Lihat Demo dihapus sesuai permintaan -->
                    </div>
                </div>

                <!-- Hero Mockup (Dashboard Preview) -->
                <div class="relative lg:ml-10 floating">
                    <!-- Base Card -->
                    <div class="bg-white rounded-3xl shadow-2xl p-6 border border-gray-100 relative z-10 w-full max-w-lg mx-auto">
                        
                        <!-- Mockup Header -->
                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center gap-3">
                                <img src="/images/logoManggo.jpeg" alt="Logo"
                                     class="h-10 w-auto">
                                <div>
                                    <h4 class="font-bold text-gray-900 text-sm">Kebun Utama Blok A</h4>
                                    <p class="text-xs text-gray-400">Mangga Arumanis • Tegal</p>
                                </div>
                            </div>
                            <span class="bg-brand-100 text-brand-700 px-3 py-1 rounded-full text-[10px] font-bold">FASE PEMBESARAN</span>
                        </div>

                        <!-- Mockup Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide mb-1">Total GDD</p>
                                <h3 class="text-2xl font-black text-gray-800">1,240 <span class="text-sm font-medium text-gray-400">GDD</span></h3>
                            </div>
                            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide mb-1">Estimasi Panen</p>
                                <h3 class="text-2xl font-black text-gray-800">14 <span class="text-sm font-medium text-gray-400">Hari</span></h3>
                            </div>
                        </div>

                        <!-- Mockup Progress -->
                        <div class="mb-2">
                            <div class="flex justify-between text-xs mb-2">
                                <span class="font-bold text-gray-700">Progress Pertumbuhan</span>
                                <span class="font-bold text-brand-600">82%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-brand-400 to-brand-600 h-2.5 rounded-full" style="width: 82%"></div>
                            </div>
                        </div>

                        <!-- Floating elements over mockup -->
                        <div class="absolute -bottom-6 -left-8 glass-card rounded-2xl p-4 shadow-xl flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xs"><i class="fa-solid fa-check"></i></div>
                            <span class="text-xs font-bold text-gray-800">Data API Cuaca Real-time</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ── FITUR (Kekuatan Data) ── -->
    <div id="fitur" class="py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Kekuatan Data di Genggaman</h2>
                <p class="text-gray-500">Sistem berbasis awan (cloud) yang mengintegrasikan data lingkungan dan biologi tanaman untuk hasil panen optimal.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Feature 1 -->
                <div class="p-8 rounded-3xl bg-white border border-gray-100 shadow-sm hover:shadow-xl transition duration-300 group">
                    <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-xl mb-6 group-hover:bg-brand-600 group-hover:text-white transition">
                        <i class="fa-solid fa-satellite-dish"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Monitoring Cerdas</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Catat kebun, fase berbunga, dan jenis mangga Anda di satu dashboard, kapanpun di mana saja.</p>
                </div>
                <!-- Feature 2 -->
                <div class="p-8 rounded-3xl bg-white border border-gray-100 shadow-sm hover:shadow-xl transition duration-300 group">
                    <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-xl mb-6 group-hover:bg-brand-600 group-hover:text-white transition">
                        <i class="fa-solid fa-calculator"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Prediksi GDD</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Algoritma menghitung otomatis akumulasi suhu (panas) setiap hari secara presisi.</p>
                </div>
                <!-- Feature 3 -->
                <div class="p-8 rounded-3xl bg-white border border-gray-100 shadow-sm hover:shadow-xl transition duration-300 group">
                    <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-xl mb-6 group-hover:bg-brand-600 group-hover:text-white transition">
                        <i class="fa-regular fa-file-lines"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Laporan Terpadu</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Semua rangkuman data mudah di-export untuk dianalisa ke dalam format dokumen yang rapi.</p>
                </div>
                <!-- Feature 4 -->
                <div class="p-8 rounded-3xl bg-white border border-gray-100 shadow-sm hover:shadow-xl transition duration-300 group">
                    <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-xl mb-6 group-hover:bg-brand-600 group-hover:text-white transition">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Insight Spesifik</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Melihat kebun mana yang sudah masuk masa siap panen secara transparan.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ── CARA KERJA (Sederhana & Terstruktur) ── -->
    <div id="carakerja" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-20">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Sederhana & Terstruktur</h2>
                <p class="text-gray-500">Hanya butuh beberapa langkah untuk mengubah kebun Anda menjadi lahan digital yang presisi.</p>
            </div>

            <div class="relative">
                <!-- Connecting Line -->
                <div class="hidden lg:block absolute top-8 left-0 w-full h-0.5 bg-gray-200 z-0"></div>
                <div class="hidden lg:block absolute top-8 left-0 w-3/4 h-0.5 bg-brand-500 z-0 opacity-20"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 relative z-10">
                    <!-- Step 1 -->
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-white shadow-md border border-gray-100 text-brand-600 flex items-center justify-center text-xl mb-6 font-black">
                            <i class="fa-regular fa-keyboard"></i>
                        </div>
                        <p class="text-[10px] font-bold text-brand-600 uppercase tracking-wider mb-2">Langkah 1</p>
                        <h4 class="text-lg font-bold text-gray-900 mb-2">Input Data</h4>
                        <p class="text-xs text-gray-500">Daftarkan kebun & set parameter fase tanaman.</p>
                    </div>
                    <!-- Step 2 -->
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-white shadow-md border border-gray-100 text-brand-600 flex items-center justify-center text-xl mb-6 font-black">
                            <i class="fa-solid fa-cloud-arrow-down"></i>
                        </div>
                        <p class="text-[10px] font-bold text-brand-600 uppercase tracking-wider mb-2">Langkah 2</p>
                        <h4 class="text-lg font-bold text-gray-900 mb-2">Sinkron Cuaca</h4>
                        <p class="text-xs text-gray-500">Sistem menarik suhu harian secara otomatis (Open-Meteo).</p>
                    </div>
                    <!-- Step 3 -->
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-white shadow-md border border-gray-100 text-brand-600 flex items-center justify-center text-xl mb-6 font-black">
                            <i class="fa-solid fa-chart-column"></i>
                        </div>
                        <p class="text-[10px] font-bold text-brand-600 uppercase tracking-wider mb-2">Langkah 3</p>
                        <h4 class="text-lg font-bold text-gray-900 mb-2">Monitor Real-time</h4>
                        <p class="text-xs text-gray-500">Kalkulasi GDD terjadi dalam grafik interaktif.</p>
                    </div>
                    <!-- Step 4 -->
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-brand-600 shadow-lg shadow-brand-500/40 text-white flex items-center justify-center text-xl mb-6 font-black transform scale-110">
                            <i class="fa-solid fa-basket-shopping"></i>
                        </div>
                        <p class="text-[10px] font-bold text-brand-600 uppercase tracking-wider mb-2">Langkah 4</p>
                        <h4 class="text-lg font-bold text-gray-900 mb-2">Notifikasi Panen</h4>
                        <p class="text-xs text-gray-500">Pemberitahuan saat target panen tercapai (100% GDD).</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── SHOWCASE (Pusat Kendali) ── -->
    <div id="sistem" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center bg-gray-50 rounded-[40px] overflow-hidden shadow-sm border border-gray-100">
                
                <!-- Left Content -->
                <div class="lg:w-2/5 p-12 lg:p-16 bg-gradient-to-br from-brand-700 to-brand-900 text-white h-full relative overflow-hidden">
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-white opacity-5 rounded-full"></div>
                    
                    <h3 class="text-3xl font-bold mb-6">Pusat Kendali</h3>
                    <p class="text-brand-100 text-sm leading-relaxed mb-10 opacity-90">
                        Di dashboard Anda, grafik dan metrik ini menjadi sumber kebenaran tunggal untuk setiap pengambilan keputusan kapan masa panen akan tiba.
                    </p>
                    
                    <div class="space-y-4">
                        <div class="flex items-center gap-4 bg-white/10 rounded-xl p-4 backdrop-blur-sm">
                            <i class="fa-solid fa-mobile-screen text-xl opacity-80"></i>
                            <span class="font-medium text-sm">Akses dari HP</span>
                        </div>
                        <div class="flex items-center gap-4 bg-white/10 rounded-xl p-4 backdrop-blur-sm">
                            <i class="fa-solid fa-lock text-xl opacity-80"></i>
                            <span class="font-medium text-sm">Privasi Data per Petani</span>
                        </div>
                    </div>
                </div>

                <!-- Right Graphic Mockup -->
                <div class="lg:w-3/5 p-8 lg:p-16 w-full">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 w-full">
                        <div class="flex justify-between items-center mb-8 border-b border-gray-100 pb-4">
                            <h4 class="font-bold text-gray-800 text-lg">Prakiraan Pertumbuhan GDD</h4>
                            <div class="px-3 py-1 bg-brand-50 text-brand-700 rounded-full text-xs font-bold">7 Hari Terakhir</div>
                        </div>

                        <!-- Bar Chart Mockup (CSS only for visual representation) -->
                        <div class="flex items-end gap-3 h-48 mb-6">
                            <div class="w-1/6 bg-brand-100 rounded-t-md h-[20%] hover:bg-brand-200 transition"></div>
                            <div class="w-1/6 bg-brand-200 rounded-t-md h-[35%] hover:bg-brand-300 transition"></div>
                            <div class="w-1/6 bg-brand-300 rounded-t-md h-[50%] hover:bg-brand-400 transition"></div>
                            <div class="w-1/6 bg-brand-400 rounded-t-md h-[60%] hover:bg-brand-500 transition"></div>
                            <div class="w-1/6 bg-brand-500 rounded-t-md h-[75%] hover:bg-brand-600 transition"></div>
                            <div class="w-1/6 bg-brand-700 rounded-t-md h-[100%] shadow-lg transition"></div>
                        </div>
                        <div class="flex justify-between text-[10px] font-bold text-gray-400">
                            <span>H-5</span><span>H-4</span><span>H-3</span><span>H-2</span><span>H-1</span><span class="text-gray-800">HARI INI</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- 4 Bottom Icons Benefits -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mt-16 text-center border-t border-gray-100 pt-16">
                <div>
                    <i class="fa-solid fa-circle-check text-brand-600 text-2xl mb-4"></i>
                    <h5 class="font-bold text-gray-900 text-sm mb-2">Lebih Akurat</h5>
                    <p class="text-xs text-gray-500">GDD lebih akurat daripada kalender konvensional.</p>
                </div>
                <div>
                    <i class="fa-solid fa-bolt text-brand-600 text-2xl mb-4"></i>
                    <h5 class="font-bold text-gray-900 text-sm mb-2">Kurangi Risiko</h5>
                    <p class="text-xs text-gray-500">Cegah panen prematur/terlambat yang menurunkan kualitas.</p>
                </div>
                <div>
                    <i class="fa-solid fa-microchip text-brand-600 text-2xl mb-4"></i>
                    <h5 class="font-bold text-gray-900 text-sm mb-2">Otomatisasi Penuh</h5>
                    <p class="text-xs text-gray-500">API Cuaca bekerja di balik layar setiap hari 24/7.</p>
                </div>
                <div>
                    <i class="fa-solid fa-arrow-up-right-dots text-brand-600 text-2xl mb-4"></i>
                    <h5 class="font-bold text-gray-900 text-sm mb-2">Skalabilitas Bisnis</h5>
                    <p class="text-xs text-gray-500">Cocok untuk petani skala kecil hingga sistem *supply chain*.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ── CTA BANNER ── -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
        <div class="bg-gradient-to-r from-brand-600 to-brand-500 rounded-3xl p-12 md:p-16 text-center relative overflow-hidden shadow-2xl shadow-brand-500/20">
            <!-- Decorative circles -->
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white opacity-10 rounded-full"></div>
            <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-white opacity-10 rounded-full"></div>
            
            <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-6 relative z-10">
                Siap Maksimalkan Hasil Kebun Anda?
            </h2>
            <p class="text-brand-100 text-sm md:text-base max-w-2xl mx-auto mb-10 relative z-10">
                Bergabunglah dengan ratusan petani progresif lain yang telah mentransformasi cara mengelola kebun mereka menggunakan data presisi MangoGDD.
            </p>
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4 relative z-10">
                <a href="{{ route('register') }}" class="w-full sm:w-auto bg-white text-brand-600 font-bold px-8 py-3.5 rounded-full hover:bg-gray-50 transition shadow-lg">
                    Daftar Sekarang — Gratis
                </a>
                <a href="#" class="w-full sm:w-auto border-2 border-white/30 text-white font-bold px-8 py-3.5 rounded-full hover:bg-white/10 transition">
                    Hubungi Admin
                </a>
            </div>
        </div>
    </div>

    <!-- ── FOOTER ── -->
    <footer class="bg-gray-50 pt-16 pb-8 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                
                <!-- Brand & Desc -->
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2 mb-6">
                        <img src="/images/logoManggo.jpeg" alt="MangoGDD Logo"
                             class="h-8 w-auto">
                        <span class="font-bold text-lg text-gray-900">Mango<span class="text-brand-600">GDD</span></span>
                    </div>
                    <p class="text-xs text-gray-500 leading-relaxed max-w-sm mb-6">
                        Platform web-based terintegrasi yang memberdayakan rantai pasok mangga di Indonesia menggunakan metodologi akumulasi derajat hari (GDD).
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 hover:bg-brand-600 hover:text-white transition"><i class="fa-brands fa-twitter text-xs"></i></a>
                        <a href="#" class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 hover:bg-brand-600 hover:text-white transition"><i class="fa-brands fa-instagram text-xs"></i></a>
                        <a href="#" class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 hover:bg-brand-600 hover:text-white transition"><i class="fa-brands fa-github text-xs"></i></a>
                    </div>
                </div>

                <!-- Links 1 -->
                <div>
                    <h4 class="font-bold text-gray-900 text-xs uppercase tracking-wider mb-6">Sistem</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ route('login') }}" class="text-xs text-gray-500 hover:text-brand-600">Login Petani</a></li>
                        <li><a href="{{ route('login') }}" class="text-xs text-gray-500 hover:text-brand-600">Login Pengepul</a></li>
                        <li><a href="{{ route('register') }}" class="text-xs text-gray-500 hover:text-brand-600">Daftar Akun Baru</a></li>
                        <li><a href="#" class="text-xs text-gray-500 hover:text-brand-600">API Integrasi</a></li>
                    </ul>
                </div>

                <!-- Links 2 -->
                <div>
                    <h4 class="font-bold text-gray-900 text-xs uppercase tracking-wider mb-6">Informasi</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-xs text-gray-500 hover:text-brand-600">Tentang GDD</a></li>
                        <li><a href="#" class="text-xs text-gray-500 hover:text-brand-600">Cara Perhitungan</a></li>
                        <li><a href="#" class="text-xs text-gray-500 hover:text-brand-600">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="text-xs text-gray-500 hover:text-brand-600">Bantuan</a></li>
                    </ul>
                </div>

            </div>

            <!-- Copyright -->
            <div class="border-t border-gray-200 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-[11px] text-gray-400">
                    &copy; {{ date('Y') }} Mango GDD System. Hak Cipta Dilindungi Undang-Undang.
                </p>
                <div class="flex gap-4 text-[11px] text-gray-400">
                    <span>Versi 1.0.0</span>
                    <span>Tugas Akhir</span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
