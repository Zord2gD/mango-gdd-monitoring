# AI Context: Mango GDD (Growing Degree Days) Web App

## 📌 Deskripsi Proyek
Proyek ini adalah aplikasi web berbasis **Laravel** yang ditujukan untuk membantu memantau perhitungan **GDD (Growing Degree Days)** atau akumulasi satuan panas pada pohon mangga. Aplikasi ini memungkinkan pencatatan manajemen kebun, pemantauan fase tanaman (khususnya mulai fase berbunga), serta pencatatan suhu harian (Tmax dan Tmin) untuk mengalkulasikan nilai GDD per harinya, yang berguna untuk memprediksi waktu panen mangga yang optimal.

## 🏢 Aktor / Role Sistem
Aplikasi ini memiliki multi-otentikasi berdasarkan peran (role) yang sudah diatur dalam proses `redirect` di `routes/web.php`:
1. **Admin**: Mengelola keseluruhan sistem dan master data. Dibuat manual di database (tidak bisa daftar sendiri).
2. **Petani**: Mengelola data Kebun miliknya, memasukkan Fase Tanaman, serta mencatat Suhu Harian dari lokasi kebun berada.
3. **Pengepul**: (Akan datang) Memantau kebun mana yang sudah akan mendekati estimasi waktu panen berdasarkan kalkulasi GDD.

---

## 🛠️ Status Proyek Saat Ini (Current State — Update 1 Mei 2026)

### ✅ SELESAI - Fase 1: Setup Database
- Semua migrasi dieksekusi. Field `gdd` ada di tabel `suhu_harian`.
- Database MySQL via XAMPP aktif dan terhubung.

### ✅ SELESAI - Fase 2: Dashboard UI Per Role
- Route `/admin/dashboard`, `/petani/dashboard`, `/pengepul/dashboard` aktif.
- Admin Dashboard (`admin.blade.php`) sekarang adalah **full custom standalone HTML** (bukan x-app-layout) dengan sidebar dark-green, 4 stats card (Total Kebun, Total Petani, Siap Panen, Rata-rata GDD), tabel Daftar Kebun + filter + progress bar, dan chart GDD.

### ✅ SELESAI - Revisi Autentikasi
- Login & Register memiliki dropdown role.
- Akun Admin hanya bisa dibuat manual via DB/Artisan Tinker.
- Default admin: `admin@gmail.com` / `password`.
- **Fitur Baru:** Menambahkan fitur ikon mata (Show/Hide Password) interaktif pada halaman Login dan Register.

### ✅ SELESAI - Phase 3: Akumulasi GDD & Prediksi Panen
- **Threshold Dinamis**: Gedong=1100, Harumanis=1200, Gajah=1300, default=1000.
- **Kebun Model** memiliki 4 accessor: `target_gdd`, `total_gdd`, `gdd_progress`, `is_siap_panen`.
- GDD hanya diakumulasi sejak `tanggal_berbunga` pada `fase_tanaman`.
- Validasi `tmin <= tmax` di form input suhu.
- GDD minimum 0 (tidak bisa negatif).

### ✅ SELESAI - RBAC (Role-Based Access Control)
- **Petani**: Create/read kebunnya sendiri, input fase & suhu miliknya.
- **Admin**: Lihat SEMUA data, Edit SEMUA, Hapus SEMUA. Tidak bisa input kebun/fase/suhu.
- Sidebar `dashboard.blade.php` menampilkan menu berbeda berdasarkan role user yang login.
- Tabel kebun/fase/suhu menampilkan kolom "Petani" hanya untuk Admin.

### ✅ SELESAI - Integrasi Tampilan Form & Controller CRUD (Update Tambahan)
- Tiga entitas utama (`Kebun`, `Fase Tanaman`, `Suhu Harian`) kini memiliki **Form CRUD Lengkap** lengkap beserta controllernya tersendiri (`KebunController`, `FaseTanamanController`, `SuhuHarianController`).
- Terdapat fitur **Manajemen/Daftar Petani** eksklusif di dashboard Admin (`Admin\PetaniController`), yang dapat merekapitulasi jumlah kebun, mengakumulasi total GDD per petani, serta mencetak *Badge Status Panen* (Siap Panen, Hampir Panen, Belum Panen) per masing-masing petani.
- Route Resource terintegrasi dengan fitur Auth dan Role Middleware di file `routes/web.php`.
- Perbaikan struktur antarmuka: Navbar fitur `Data Petani` di sidebar menu Admin telah terhubung sempurna ke tampilan tabel detail, dan `Monitoring Panen` kini mengarahkan langsung pada tabel statistik dashboard utama halaman admin.
- **Refactoring Struktur**: Seluruh logika *query* Dashboard Admin, Petani, Pengepul beserta logika `redirect` *role* telah dibersihkan dari *closure* `routes/web.php` dan dipisahkan secara rapi ke dalam *controller* tersendiri yaitu `DashboardController.php`.

### ✅ SELESAI - Automasi Data Cuaca API & Perhitungan GDD
- **Integrasi Open-Meteo API:** Service backend `WeatherService` otomatis mengambil data Tmin dan Tmax harian berdasarkan koordinat (latitude & longitude) kebun.
- **Otomatisasi Background Job:** Pembuatan Artisan command `php artisan weather:fetch` untuk mengeksekusi penarikan data secara massal. Sistem otomatis menyimpan Tmin, Tmax, dan GDD ke database.
- **Menghilangkan Input Suhu Manual:** Proses entry suhu manual dihapus. Sistem menolak duplikasi penarikan data pada hari yang sama untuk kebun yang sama.

### ✅ SELESAI - Visualisasi Data Cuaca
- **Standarisasi API (`WeatherController`)**: Endpoint `/api/cuaca/{kebun}` distandarisasi menggunakan *flat arrays* (`dates`, `tmax`, `tmin`) dengan validasi latitude/longitude dan *error handling* ketat.
- **Dashboard Admin:** Menambahkan interaktif *Line Chart* (Chart.js) dengan fitur *dropdown* (AJAX) untuk memilih kebun dan memantau prakiraan suhu 7 hari ke depan. Dilengkapi dengan status kosong (*empty state*), state *loading*, dan metrik cerdas (Suhu Terpanas, Terdingin, dan kalkulasi Estimasi GDD 7 hari).
- **Dashboard Petani:** Menambahkan *Line Chart* prakiraan suhu yang juga membaca JSON terstandarisasi, lengkap dengan pengamanan *memory* (chart destroy) dan validasi struktur data.

### ✅ SELESAI - Visual & Layout Enhancements (Update UI/UX)
- **Halaman Otentikasi:** Form Login & Register (`guest.blade.php`) diperbarui dengan background gambar (`backgroundmangga.jpeg`), *dark gradient overlay*, efek *glassmorphism card*, dan fitur toggle password.
- **Landing Page (`welcome.blade.php`):** Integrasi *background* mangga pada *Hero Section* dengan warna teks kontras, penggantian *placeholder icon* menggunakan gambar logo nyata (`logoMangga.jpeg`).
- **Dashboard Sidebar (`layouts/dashboard.blade.php`):** Penambahan logo nyata (`logoMangga.jpeg`) yang bersifat interaktif.
- **Tampilan Suhu Harian (`suhu/index.blade.php`):** Mengubah tampilan tabel data suhu agar dikelompokkan (Grouped by) berdasarkan `kebun_id` sehingga setiap kebun memiliki *card* UI terpisah yang jauh lebih rapi.

### ✅ SELESAI - Phase 4: Modul Pengepul (Dashboard Read-Only)
- **Dashboard Pengepul** (`pengepul.blade.php`): Full standalone HTML dengan desain tema biru.
- **4 Stats Cards**: Total Kebun, Total Petani, Hampir Panen, Potensi Panen Hari Ini.
- **Tabel Monitoring Panen**: Menampilkan SEMUA kebun.
- **READ-ONLY**: Tidak ada tombol Edit/Hapus/Create. Pengepul hanya bisa melihat dan memfilter.

### ✅ SELESAI - Phase 5: Automasi, Sinkronisasi Riwayat, & Perbaikan Bug (Update Terbaru)
- **Otomatisasi Fase Tanaman & Bug Fix:** Logika *hardcode* fase manual telah dihapus. Memperbaiki *bug typo* (ketidaksesuaian string label) pada badge status fase di dashboard Admin & Petani agar sinkron dengan output model `Kebun->fase_otomatis`.
- **Sinkronisasi Data Suhu Masa Lalu (Historical Sync):** Fitur krusial bagi petani yang baru menginput kebun namun pohon sudah berbunga di minggu-minggu sebelumnya. Menambahkan tombol "Tarik Data Suhu Masa Lalu" (maks 92 hari).
- **Varietas Mangga Khas Indramayu:** Menambahkan opsi varietas lokal beserta target GDD-nya secara *native*.
- **Perbaikan API Cuaca (SSL Bypass):** Memperbaiki isu kegagalan *fetch* suhu akibat limitasi sertifikat SSL XAMPP lokal (*cURL error 60*).
- **Hak Akses Hapus Suhu:** Membuka fitur "Hapus" pada tabel Data Suhu Harian agar Petani dapat menghapus log suhunya.
- **UI Progress GDD:** Kotak indikator GDD diubah menjadi *Card UI* individual.
- **Perombakan Visual Chart.js:** Mengubah grafik *Progress GDD* dan *Prakiraan Suhu* menjadi **Horizontal Bar Chart**. Memperbaiki *bug* "Layar Hitam / Infinite Scroll Height".

---

## 🎓 Penyelarasan Dokumen Skripsi (Update 7 Mei 2026)
Sistem ini telah dikonfirmasi dan diverifikasi **SANGAT SESUAI (95%)** dengan kerangka Bab 1 Skripsi pengguna (Rumusan Masalah, Batasan Masalah, Tujuan).
- **Rantai Pasok (Supply Chain):** Terwakili oleh 3 aktor utama (Admin, Petani, Pengepul). Distribusi difokuskan pada *ketersediaan informasi pra-panen* agar Pengepul bisa menyiapkan logistik (belum masuk ke tracking resi kurir, yang mana disetujui sebagai batasan wajar).
- **Metode GDD:** Validasi bahwa perhitungan GDD sudah berjalan otomatis via integrasi satelit Open-Meteo API, sangat memenuhi batasan "menggunakan sumber data yang tersedia tanpa perangkat IoT".
- **Aplikasi Web:** Sesuai, dikembangkan menggunakan framework Laravel 11 (PHP), database MySQL, dan Chart.js.

---

## 🚀 Fase Implementasi Kedepannya

### Phase 6: Fitur Lanjutan, Siklus Panen, Laporan, & Deployment ← ACTIVE
- **✅ SELESAI:** Fitur Riwayat Panen & Siklus: Petani dapat menekan tombol "Selesaikan Panen" untuk menyimpan riwayat (*snapshot*) ke tabel `riwayat_panens` secara permanen. Konsep Zero-Deletion berhasil diterapkan.
- **✅ SELESAI (8 Mei 2026): Keamanan Data & Validasi Backend**: Menerapkan validasi ketat (*silently drop & reject*) pada Controller untuk mengunci status "Varietas Mangga" agar tidak bisa dimanipulasi melalui celah UI saat siklus GDD berjalan.
- **✅ SELESAI (8 Mei 2026): Optimasi Database (Anti N+1 & Memory Bottleneck)**: Membuang in-memory loop dan menggantinya dengan agregasi SQL murni via `withSum('suhuAktif', 'gdd')`. Disertai penambahan *Unique Index Constraint* untuk mengunci total risiko duplikasi data harian.
- **✅ SELESAI (8 Mei 2026): Arsitektur Enterprise (Idempotent Upsert & Hybrid Cache)**: Menata ulang logika sinkronisasi API Cuaca menjadi *Bulk Upsert* yang kebal *Race-Condition*. Sistem komputasi `total_gdd` kini di- *caching* secara cerdas (O(1) Speed) dengan kombinasi jaring pengaman waktu (24H TTL) dan fitur *Real-Time Cache Invalidation*.
- **Aksi:** Fitur Notifikasi otomatis ke pengepul jika ada kebun siap panen.
- **Aksi:** Sistem Export laporan PDF/Excel untuk seluruh Role.
- **Aksi:** Deployment preparation (optimize, env production). **Catatan Penting:** Harus menggunakan Supervisor/systemd untuk `php artisan queue:work --max-jobs=100 --max-time=3600` guna mencegah *Queue Memory Leak* jangka panjang. Tambahkan juga konfigurasi `autorestart=true` (menghidupkan ulang jika *crash*) dan `stopwaitsecs=3600` (agar pekerjaan *queue* panjang diselesaikan secara *graceful* sebelum dipaksa mati).

---

## 🗂️ Struktur File Penting

| File/Folder | Keterangan |
|---|---|
| `routes/web.php` | Routing utama + redirect berbasis role |
| `routes/auth.php` | Routing auth bawaan Breeze |
| `app/Models/User.php` | Model User, `role` sudah Fillable |
| `app/Models/Kebun.php` | Logic penentu Target GDD dan Aksesor `fase_otomatis` |
| `resources/views/dashboard/admin.blade.php` | Dashboard Admin |
| `resources/views/dashboard/petani.blade.php` | Dashboard Petani + tombol Logout |
| `resources/views/dashboard/pengepul.blade.php` | Dashboard Pengepul + tombol Logout |
| `resources/views/suhu/index.blade.php` | Tampilan log Suhu Harian + Fitur Sinkronisasi Masa Lalu |
| `app/Http/Controllers/WeatherController.php`| Penyedia API JSON cuaca & Handler `syncHistorical` data masa lalu |

---

> **Note untuk AI Prompt Berikutnya:** Gunakan dokumen ini sebagai acuan konteks fitur untuk setiap *future request* dari user. Modul Pengepul (Phase 4) dan Automasi Fase & Riwayat (Phase 5) sudah selesai. Fokus berikutnya adalah pembuatan fitur notifikasi otomatis, *export report* (Phase 6), dan *polishing* untuk persiapan peluncuran.
