<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KebunController;
use App\Http\Controllers\FaseTanamanController;
use App\Http\Controllers\SuhuHarianController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RiwayatPanenController;
use App\Http\Controllers\Admin\PetaniController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return redirect('/redirect');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // ── Profile (semua role yang login) ──────────────────────────────
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Notifications (semua role) ───────────────────────────────────
    Route::post('/notifications/read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    // ── Weather / Cuaca (semua role bisa lihat) ──────────────────────
    Route::get('/cuaca', [WeatherController::class, 'index'])->name('cuaca.index');
    Route::get('/api/cuaca/{kebun}', [WeatherController::class, 'fetchTemperature'])->name('api.cuaca.fetch');

    // ── Riwayat Panen: index untuk admin+petani ──────────────────────
    Route::get('/riwayat-panen', [RiwayatPanenController::class, 'index'])
        ->name('riwayat.index')
        ->middleware('role:admin,petani');

    // ── Admin Only ───────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::get('admin/petani', [PetaniController::class, 'index'])->name('admin.petani');

        Route::get('/export/admin/csv', [ExportController::class, 'adminCsv'])->name('export.admin.csv');
        Route::get('/export/admin/pdf', [ExportController::class, 'adminPdf'])->name('export.admin.pdf');
    });

    // ── Petani Only ──────────────────────────────────────────────────
    Route::middleware('role:petani')->group(function () {
        // Selesaikan panen (store riwayat) — hanya petani
        Route::post('/riwayat-panen', [RiwayatPanenController::class, 'store'])->name('riwayat.store');

        Route::get('/export/petani/csv', [ExportController::class, 'petaniCsv'])->name('export.petani.csv');
        Route::get('/export/petani/pdf', [ExportController::class, 'petaniPdf'])->name('export.petani.pdf');
    });

    // ── Pengepul Only ────────────────────────────────────────────────
    Route::middleware('role:pengepul')->group(function () {
        Route::get('/export/pengepul/csv', [ExportController::class, 'pengepulCsv'])->name('export.pengepul.csv');
        Route::get('/export/pengepul/pdf', [ExportController::class, 'pengepulPdf'])->name('export.pengepul.pdf');
    });

    // ── Admin + Petani Only (Pengepul TIDAK bisa akses) ─────────────
    Route::middleware('role:admin,petani')->group(function () {
        Route::resource('kebun', KebunController::class);
        Route::resource('fase', FaseTanamanController::class);
        Route::resource('suhu', SuhuHarianController::class);

        // Sync cuaca historis (butuh kebun milik sendiri)
        Route::post('/api/cuaca/sync/{kebun}', [WeatherController::class, 'syncHistorical'])->name('api.cuaca.sync');
    });
});

// ── Role-based Dashboard Redirects ──────────────────────────────────
Route::get('/redirect', [DashboardController::class, 'redirect'])->middleware(['auth']);
Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->middleware(['auth', 'role:admin']);
Route::get('/petani/dashboard', [DashboardController::class, 'petani'])->middleware(['auth', 'role:petani']);
Route::get('/pengepul/dashboard', [DashboardController::class, 'pengepul'])->middleware(['auth', 'role:pengepul']);

require __DIR__.'/auth.php';
