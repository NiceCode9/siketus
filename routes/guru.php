<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Guru\GuruDashboardController::class, 'index'])->name('dashboard');

    // Jadwal Mengajar Guru
    Route::prefix('jadwal')->name('jadwal-guru.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Guru\JadwalGuruController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Guru\JadwalGuruController::class, 'show'])->name('show');
        Route::post('/set-reminder/{id}', [\App\Http\Controllers\Guru\JadwalGuruController::class, 'setReminder'])->name('set-reminder');
    });
    Route::get('/jadwal-export-pdf', [\App\Http\Controllers\Guru\JadwalGuruController::class, 'exportPdf'])->name('jadwal-export-pdf');

    // Absensi
    Route::prefix('absensi')->name('absensi.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Absensi\AbsensiController::class, 'index'])->name('index');
        Route::get('/create/{pertemuan}', [\App\Http\Controllers\Absensi\AbsensiController::class, 'create'])->name('create');
        Route::post('/store/{pertemuan}', [\App\Http\Controllers\Absensi\AbsensiController::class, 'store'])->name('store');
        Route::get('/edit/{pertemuan}', [\App\Http\Controllers\Absensi\AbsensiController::class, 'edit'])->name('edit');
        Route::put('/update/{pertemuan}', [\App\Http\Controllers\Absensi\AbsensiController::class, 'update'])->name('update');

        // NEW: Route untuk history dan detail
        Route::get('/history/{jadwal_id}', [\App\Http\Controllers\Absensi\AbsensiController::class, 'history'])->name('history');
        Route::get('/detail/{pertemuan}', [\App\Http\Controllers\Absensi\AbsensiController::class, 'detail'])->name('detail');
    });

    // KKM Management
    Route::prefix('kkm')->name('kkm.')->group(function () {
        Route::get('/', [App\Http\Controllers\Guru\KkmController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Guru\KkmController::class, 'create'])->name('create');
        Route::post('/store', [App\Http\Controllers\Guru\KkmController::class, 'store'])->name('store');
        Route::get('/show/{id}', [App\Http\Controllers\Guru\KkmController::class, 'show'])->name('show');
        Route::delete('/destroy/{id}', [App\Http\Controllers\Guru\KkmController::class, 'destroy'])->name('destroy');
        Route::get('/get-kkm-value', [App\Http\Controllers\Guru\KkmController::class, 'getKkmValue'])->name('get-kkm-value');
    });

    // Remidi Management
    Route::prefix('remidi')->name('remidi.')->group(function () {
        Route::get('/', [App\Http\Controllers\Guru\RemidiController::class, 'index'])->name('index');
        Route::get('/show/{id}', [App\Http\Controllers\Guru\RemidiController::class, 'show'])->name('show');
        Route::post('/input-nilai/{id}', [App\Http\Controllers\Guru\RemidiController::class, 'inputNilai'])->name('input-nilai');
        Route::post('/bulk-input-nilai', [App\Http\Controllers\Guru\RemidiController::class, 'bulkInputNilai'])->name('bulk-input-nilai');
        Route::get('/statistik', [App\Http\Controllers\Guru\RemidiController::class, 'statistik'])->name('statistik');
    });

    // Routes Penilaian
    Route::get('/penilaian', [\App\Http\Controllers\Guru\PenilaianController::class, 'index'])->name('penilaian.index');
    Route::get('/penilaian/create', [\App\Http\Controllers\Guru\PenilaianController::class, 'create'])->name('penilaian.create');
    Route::post('/penilaian', [\App\Http\Controllers\Guru\PenilaianController::class, 'store'])->name('penilaian.store');

    // Routes Riwayat Penilaian
    Route::get('/riwayat-penilaian/guru', [\App\Http\Controllers\RiwayatPenilaianController::class, 'guruIndex'])
        ->name('riwayat-penilaian.index');
    Route::get('/riwayat-penilaian/guru/{siswa}/detail', [\App\Http\Controllers\RiwayatPenilaianController::class, 'guruDetail'])
        ->name('riwayat-penilaian.detail');

    Route::get('/get-mapel-by-guru-kelas', [\App\Http\Controllers\Guru\PenilaianController::class, 'getMapelByGuruKelas'])->name('get-mapel-by-guru-kelas');
});
