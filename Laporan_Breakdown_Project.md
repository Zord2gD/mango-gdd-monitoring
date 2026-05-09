# Laporan Proyek: Sistem Monitoring GDD Mangga

Berikut adalah kerangka konten yang bisa Anda gunakan untuk melanjutkan Bab 3 (Analisis & Perancangan Sistem) atau Bab 4 (Implementasi) pada laporan/skripsi Anda.

---

## 1. Status Fase Pengembangan Proyek (Saat Ini)

Proyek ini telah mencapai tahap akhir penyelesaian fungsionalitas inti (**Fase 6**), dengan rincian pencapaian sebagai berikut:

*   **Fase 1: Fondasi Sistem & Keamanan (Selesai)**
    *   Implementasi sistem Autentikasi (Login & Registrasi).
    *   Pembagian hak akses (*Role-Based Access Control*) menjadi 3 entitas: **Admin**, **Petani**, dan **Pengepul**.
*   **Fase 2: Manajemen Data Master (Selesai)**
    *   Sistem *Create, Read, Update, Delete* (CRUD) terintegrasi untuk pendataan **Kebun**, **Suhu Harian**, dan pencatatan **Fase Tanaman**.
*   **Fase 3: Logika Bisnis Inti (Selesai)**
    *   Implementasi algoritma perhitungan **Growing Degree Days (GDD)**.
    *   Penentuan *Target GDD* otomatis berdasarkan varietas mangga, dan perubahan *Fase Pertumbuhan Otomatis* berdasarkan persentase kematangan.
*   **Fase 4: Otomatisasi & Integrasi API (Selesai)**
    *   Integrasi dengan **Open-Meteo API** untuk menarik data suhu (Tmin & Tmax) secara otomatis.
    *   Implementasi *Cron Job / Task Scheduler* untuk sinkronisasi cuaca setiap hari pukul 06.00 WIB.
*   **Fase 5: Visualisasi & Antarmuka Pengguna (Selesai)**
    *   Pembuatan antarmuka *Dashboard* interaktif untuk Admin dan Petani.
    *   Implementasi grafik visual menggunakan **Chart.js** (Grafik tren GDD dan Prakiraan Cuaca 7 Hari).
*   **Fase 6: Fitur Lanjutan & Siklus Panen (Selesai)**
    *   Implementasi konsep **Siklus Panen Permanen (Zero-Deletion)**.
    *   Fitur Selesaikan Panen dan pencatatan **Riwayat Panen** (*Harvest History*) tanpa menghapus riwayat suhu lama.

---

## 2. Bahasa Pemrograman & Teknologi yang Digunakan

Sistem ini dikembangkan menggunakan tumpukan teknologi (*tech stack*) modern berbasis web:

*   **Bahasa Pemrograman (Backend):** PHP (Versi 8.x)
*   **Framework Utama:** Laravel (Versi 11.x) — *Menggunakan pola arsitektur MVC (Model-View-Controller)*.
*   **Database Management System:** MySQL / MariaDB (Berjalan pada server lokal XAMPP).
*   **Front-End & Antarmuka:** 
    *   HTML5 & CSS3
    *   Laravel Blade (Templating Engine)
    *   Tailwind CSS (Untuk halaman Autentikasi/Breeze)
    *   Vanilla CSS (Untuk kustomisasi *Dashboard* yang ringan dan dinamis)
*   **JavaScript & Visualisasi Data:**
    *   Vanilla JavaScript (ES6)
    *   **Chart.js** (Library pihak ketiga untuk pembuatan grafik batang *Horizontal* dan *Vertical*).
    *   FontAwesome (Library ikonografi).
*   **Layanan Pihak Ketiga (Third-party Service):** Open-Meteo API (Layanan RESTful API cuaca satelit).

---

## 3. Struktur Folder & File Utama Sistem

Berdasarkan standar framework Laravel (MVC), berikut adalah penjelasan struktur direktori dan file yang memegang peran vital dalam sistem ini:

### A. Direktori `app/Models/` (Data & Aturan Bisnis)
Berisi representasi tabel database dan relasi antar tabel (ORM).
*   `User.php` : Model untuk menyimpan data akun Admin, Petani, dan Pengepul.
*   `Kebun.php` : Model data lahan/kebun mangga. Berisi logika penentuan `Target GDD` dan aksesor `fase_otomatis`.
*   `SuhuHarian.php` : Model penyimpan riwayat suhu (Tmin, Tmax) dan kalkulasi harian GDD.
*   `FaseTanaman.php` : Model penentu awal siklus panen (menyimpan *tanggal berbunga*).
*   `RiwayatPanen.php` : Model arsip untuk menyimpan rekapitulasi panen (Siklus).

### B. Direktori `app/Http/Controllers/` (Logika Pengendali)
Bertugas menjembatani interaksi antara antarmuka (View) dan pengolahan data (Model).
*   `DashboardController.php` : Mengatur tampilan metrik *Dashboard* sesuai hak akses (*Role*) pengguna.
*   `KebunController.php` : Mengatur proses penambahan, pengubahan, dan penghapusan data kebun.
*   `WeatherController.php` : Mengatur logika penarikan data JSON dari Open-Meteo API dan menyimpannya ke database.
*   `RiwayatPanenController.php` : Mengatur penyimpanan data siklus panen, mereset target, tanpa menghapus data historis.

### C. Direktori `resources/views/` (Antarmuka Pengguna / UI)
Berisi file *Blade Templating* yang merender HTML untuk ditampilkan di *browser* pengguna.
*   `auth/` : Kumpulan halaman *Login*, *Register*, dan manajemen *Password*.
*   `dashboard/admin.blade.php` : Halaman panel kontrol utama khusus Administrator.
*   `dashboard/petani.blade.php` : Halaman khusus Petani untuk pemantauan target GDD dan panen.
*   `riwayat/index.blade.php` : Halaman pelaporan arsip histori hasil panen.
*   `layouts/` : Berisi kerangka struktur utama web (misal: kerangka *sidebar* dan navigasi atas).

### D. Direktori & File Inti Lainnya
*   `routes/web.php` : Jantung pengatur alamat URL web (Routing). Menentukan *Controller* mana yang dipanggil saat *user* mengakses URL tertentu.
*   `database/migrations/` : Kumpulan *blueprint* (skema) untuk membuat tabel database MySQL secara otomatis via *Command Line*.
*   `.env` : File konfigurasi rahasia sistem (berisi nama *database*, *username*, *password*, dll).
