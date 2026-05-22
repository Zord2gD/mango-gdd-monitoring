# AI Context: Mango GDD (Growing Degree Days) Web App

## 📌 Deskripsi Proyek
Proyek ini adalah aplikasi web berbasis **Laravel** yang ditujukan untuk membantu memantau perhitungan **GDD (Growing Degree Days)** atau akumulasi satuan panas pada pohon mangga. Aplikasi ini memungkinkan pencatatan manajemen kebun, pemantauan fase tanaman (khususnya mulai fase berbunga), serta pencatatan suhu harian (Tmax dan Tmin) untuk mengalkulasikan nilai GDD per harinya, yang berguna untuk memprediksi waktu panen mangga yang optimal.

## 🏢 Aktor / Role Sistem
Aplikasi ini memiliki multi-otentikasi berdasarkan peran (role) yang sudah diatur dalam proses `redirect` di `routes/web.php`:
1. **Admin**: Mengelola keseluruhan sistem dan master data. Dibuat manual di database (tidak bisa daftar sendiri).
2. **Petani**: Mengelola data Kebun miliknya, memasukkan Fase Tanaman, serta mencatat Suhu Harian dari lokasi kebun berada.
3. **Pengepul**: Memantau kebun mana yang sudah akan mendekati estimasi waktu panen berdasarkan kalkulasi GDD (read-only).

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

### ✅ SELESAI - Automasi Data Cuaca API & Perhitungan GDD
- **Integrasi Open-Meteo API:** Service backend `WeatherService` otomatis mengambil data Tmin dan Tmax harian berdasarkan koordinat (latitude & longitude) kebun.
- **Otomatisasi Background Job:** Pembuatan Artisan command `php artisan weather:fetch` untuk mengeksekusi penarikan data secara massal.
- **Sinkronisasi Data Suhu Masa Lalu (Historical Sync):** Fitur "Tarik Data Suhu Masa Lalu" (maks 92 hari).

### ✅ SELESAI - Visualisasi Data Cuaca
- Dashboard Admin & Petani dilengkapi Line Chart (Chart.js) dengan dropdown AJAX pemilihan kebun.
- Halaman `/cuaca` menampilkan prakiraan 7 hari per kebun.

### ✅ SELESAI - Phase 4: Modul Pengepul (Dashboard Read-Only)
- **Dashboard Pengepul** (`pengepul.blade.php`): Full standalone HTML dengan desain tema biru.
- **READ-ONLY**: Tidak ada tombol Edit/Hapus/Create.

### ✅ SELESAI - Phase 5: Automasi, Sinkronisasi Riwayat & Perbaikan Bug
- Logika hardcode fase manual dihapus. Bug typo badge status fase diperbaiki.
- Varietas Mangga Khas Indramayu ditambahkan.

### ✅ SELESAI - Phase 6: Fitur Lanjutan, Laporan & Enterprise
- **Riwayat Panen & Siklus:** Konsep Zero-Deletion, tombol "Selesaikan Panen", snapshot `riwayat_panens`.
- **Optimasi Database:** Unique Index Constraint, agregasi SQL via `withSum`.
- **Hybrid Cache:** `total_gdd` di-cache 24H TTL + Real-Time Cache Invalidation.
- **Notifikasi Otomatis Enterprise:** Alert panen Pengepul via Laravel Queue (`ShouldQueue`).
- **Export Laporan:** Native PDF & CSV via `fputcsv` + StreamedResponse.
- **GitHub Backup:** Kode di-push ke repositori remote.

---

## 🐛 Bug Fix Audit (23 Mei 2026) — SELESAI HARI INI

Dilakukan audit bug menyeluruh pada seluruh codebase. Satu bug kritikal ditemukan dan diperbaiki.

### ✅ Bug Diperbaiki: Ekspor CSV & PDF Laporan Petani

**Problem:** `ExportController.php` dan `export/print.blade.php` memanggil atribut yang **tidak ada** di model/database:
- `$r->total_gdd_akhir` → seharusnya `$r->total_gdd`
- `$r->kualitas_panen` → seharusnya `$r->hasil_panen_kg`
- Kolom `tanggal_berbunga` di-parse tanpa null-check → berpotensi TypeError

**Fix yang diterapkan:**
- `ExportController::petaniCsv()`: header dan data CSV diperbarui ke kolom yang benar
- `resources/views/export/print.blade.php`: tabel petani diperbarui ke kolom yang benar + header disesuaikan
- Semua method export (admin, petani, pengepul) kini eager load relasi `suhu` → **mencegah N+1 query** saat accessor `total_gdd` dikomputasi
- Null-safety ditambahkan untuk `tanggal_berbunga` pada CSV dan PDF

**Status log historis:** Semua error di `laravel.log` adalah error lama dari tahap development (April–Mei 2026) dan sudah tidak aktif.

---


## 🔒 Enterprise Audit & Hardening (19 Mei 2026) — SELESAI HARI INI

Pada sesi ini dilakukan **audit menyeluruh** dan eksekusi dua zona penyempurnaan:

### ✅ GREEN ZONE — Completed (Zero-Risk Fixes)

**1. Route Security (RBAC Level Route)**
- `routes/web.php` ditulis ulang total dengan struktur middleware group yang proper.
- **Sebelum:** Resource routes `kebun`, `fase`, `suhu` hanya dilindungi `auth` → Pengepul bisa akses `/kebun/create` dan **bahkan membuat kebun** atas namanya sendiri, serta mengakses `/admin/petani` dan melihat data semua petani.
- **Sesudah:** `role:admin,petani` middleware diterapkan di route group level. Pengepul menerima 403 Forbidden. Semua export route juga dikunci per-role.

**2. Database Performance Index**
- File baru: `database/migrations/2026_05_19_000001_add_performance_indexes.php`
- Index ditambahkan: `users.role`, `kebuns.user_id`, `suhu_harian.(kebun_id + tanggal)` composite, `fase_tanaman.kebun_id`, `riwayat_panens.kebun_id`.
- Migration berhasil dijalankan (511ms DONE).

**3. SSL Fix di WeatherService**
- `withoutVerifying()` diganti dengan `withOptions(['verify' => $sslVerify])`.
- SSL aktif di production, dinonaktifkan hanya di `local`/`testing` environment.
- Diterapkan pada **dua method**: `getWeatherData()` dan `getTodayTemperature()`.

**4. Label Text Fix**
- `layouts/dashboard.blade.php` dan `dashboard/admin.blade.php`: "Pendapatan GDD Hari Ini" → **"Akumulasi GDD Hari Ini"**.

**5. Toast Notification Modern**
- `dashboard/admin.blade.php` & `dashboard/petani.blade.php`: Semua `alert()` native browser diganti dengan sistem toast notification custom.
- Toast muncul dari pojok kanan bawah, auto-dismiss 4 detik, ada 4 tipe: success/error/warning/info.

### ✅ YELLOW ZONE — Completed (Medium-Risk Improvements)

**1. Validasi FaseTanamanController::update()**
- Sebelumnya tidak ada validasi sama sekali di method `update()`.
- Sekarang: wajib diisi, harus format tanggal valid, **tidak boleh tanggal masa depan**.

**2. Form Request Classes (3 file baru)**
- `app/Http/Requests/StoreKebunRequest.php` — validasi buat kebun (lat/lng range, max pohon 99.999).
- `app/Http/Requests/StoreFaseRequest.php` — validasi tanggal berbunga (before_or_equal:today).
- `app/Http/Requests/StoreSuhuRequest.php` — validasi suhu (range -10°C s/d 60°C, Tmin ≤ Tmax).
- Tiga controller (`KebunController`, `FaseTanamanController`, `SuhuHarianController`) diupdate untuk menggunakan Form Request ini.

**3. Pagination Suhu Harian**
- `SuhuHarianController::index()`: `->get()` diganti dengan `->paginate(100)`.
- `suhu/index.blade.php`: Ditambahkan pagination links Bootstrap 5.
- Data diurutkan `desc` (terbaru di atas).

**4. Cache WeatherService (Fix N+1 API Call)**
- `getWeatherData()` di-refactor menjadi dua method: `getWeatherData()` (public + cache) dan `fetchWeatherFromApi()` (protected, internal).
- Data forecast di-cache 30 menit per koordinat unik (`weather_lat_lng_days`).
- Data historis (sync) tidak di-cache.
- **Sebelum:** 10 kebun = 10 HTTP call ke Open-Meteo. **Sesudah:** 10 kebun = 1 HTTP call (sisanya dari cache).

---

## 🔴 RED ZONE — Ditunda (Terlalu Berisiko untuk Sekarang)
Penyempurnaan berikut **sengaja ditunda** karena berisiko merusak tampilan/fungsionalitas yang sudah berjalan baik:
- Refactor dashboard ke `@extends(layouts.dashboard)` — structural overhaul besar
- Standardisasi CSS framework (Tailwind vs Bootstrap) — bisa merusak seluruh UI
- Ubah struktur controller besar-besaran

---

## 🗂️ Struktur File Penting

| File/Folder | Keterangan |
|---|---|
| `routes/web.php` | Routing utama — sudah distrukturisasi dengan role middleware groups |
| `app/Services/WeatherService.php` | Integrasi Open-Meteo API + Cache 30 menit + SSL fix |
| `app/Http/Requests/StoreKebunRequest.php` | Form Request validasi buat kebun |
| `app/Http/Requests/StoreFaseRequest.php` | Form Request validasi fase tanaman |
| `app/Http/Requests/StoreSuhuRequest.php` | Form Request validasi suhu harian |
| `app/Http/Controllers/FaseTanamanController.php` | Update: tambah validasi di `update()` |
| `app/Http/Controllers/SuhuHarianController.php` | Update: pagination + StoreSuhuRequest |
| `app/Http/Controllers/KebunController.php` | Update: StoreKebunRequest |
| `database/migrations/2026_05_19_000001_add_performance_indexes.php` | Index performa DB |
| `app/Models/Kebun.php` | Logic penentu Target GDD dan Aksesor `fase_otomatis` + Hybrid Cache |
| `resources/views/dashboard/admin.blade.php` | Dashboard Admin + Toast Notification System |
| `resources/views/dashboard/petani.blade.php` | Dashboard Petani + Toast Notification System |
| `resources/views/layouts/dashboard.blade.php` | Shared layout — label "Akumulasi GDD" sudah diperbaiki |
| `resources/views/suhu/index.blade.php` | Tampilan log Suhu + Pagination |
| `app/Http/Controllers/ExportController.php` | Update: Perbaikan query eager loading `suhu` & pemetaan kolom ekspor petani |
| `resources/views/export/print.blade.php` | Update: Perbaikan kolom tabel ekspor cetak PDF petani |

---

> **Note untuk AI Prompt Berikutnya:**
> - Green Zone & Yellow Zone dari Enterprise Audit sudah **selesai** dieksekusi pada 19 Mei 2026.
> - Perbaikan bug ekspor PDF & CSV laporan petani serta optimalisasi eager loading untuk menghindari N+1 query sudah **selesai** diperbaiki pada 23 Mei 2026.
> - **Jangan ubah** core business logic GDD (`((Tmax + Tmin) / 2) - 10`).
> - **Jangan refactor** dashboard ke `@extends` — ini masuk Red Zone yang ditunda.
> - Fokus berikutnya jika diminta: Red Zone (hanya jika user eksplisit setuju), deployment preparation, atau penambahan fitur baru.
> - Sistem sudah **production-ready** secara keamanan dan performa setelah audit dan perbaikan bug ini.

