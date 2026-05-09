<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\KebunController;
use App\Http\Controllers\FaseTanamanController;
use App\Http\Controllers\SuhuHarianController;
use App\Http\Controllers\WeatherController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return redirect('/redirect');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('kebun', KebunController::class);
    Route::resource('fase', FaseTanamanController::class);
    Route::resource('suhu', SuhuHarianController::class);
    Route::get('admin/petani', [\App\Http\Controllers\Admin\PetaniController::class, 'index'])->name('admin.petani');

    // Riwayat Panen routes
    Route::get('/riwayat-panen', [\App\Http\Controllers\RiwayatPanenController::class, 'index'])->name('riwayat.index');
    Route::post('/riwayat-panen', [\App\Http\Controllers\RiwayatPanenController::class, 'store'])->name('riwayat.store');

    // Weather / Cuaca routes
    Route::get('/cuaca', [WeatherController::class, 'index'])->name('cuaca.index');
    Route::get('/api/cuaca/{kebun}', [WeatherController::class, 'fetchTemperature'])->name('api.cuaca.fetch');
    Route::post('/api/cuaca/sync/{kebun}', [WeatherController::class, 'syncHistorical'])->name('api.cuaca.sync');

    // Notifications routes
    Route::post('/notifications/read/{id}', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    // Export routes
    Route::get('/export/admin/csv', [\App\Http\Controllers\ExportController::class, 'adminCsv'])->name('export.admin.csv')->middleware('role:admin');
    Route::get('/export/admin/pdf', [\App\Http\Controllers\ExportController::class, 'adminPdf'])->name('export.admin.pdf')->middleware('role:admin');
    
    Route::get('/export/petani/csv', [\App\Http\Controllers\ExportController::class, 'petaniCsv'])->name('export.petani.csv')->middleware('role:petani');
    Route::get('/export/petani/pdf', [\App\Http\Controllers\ExportController::class, 'petaniPdf'])->name('export.petani.pdf')->middleware('role:petani');
    
    Route::get('/export/pengepul/csv', [\App\Http\Controllers\ExportController::class, 'pengepulCsv'])->name('export.pengepul.csv')->middleware('role:pengepul');
    Route::get('/export/pengepul/pdf', [\App\Http\Controllers\ExportController::class, 'pengepulPdf'])->name('export.pengepul.pdf')->middleware('role:pengepul');
});

Route::get('/redirect', [\App\Http\Controllers\DashboardController::class, 'redirect'])->middleware(['auth']);
Route::get('/admin/dashboard', [\App\Http\Controllers\DashboardController::class, 'admin'])->middleware(['auth', 'role:admin']);
Route::get('/petani/dashboard', [\App\Http\Controllers\DashboardController::class, 'petani'])->middleware(['auth', 'role:petani']);
Route::get('/pengepul/dashboard', [\App\Http\Controllers\DashboardController::class, 'pengepul'])->middleware(['auth', 'role:pengepul']);

require __DIR__.'/auth.php';
