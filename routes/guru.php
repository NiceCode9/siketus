<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('guru')->name('guru.')->group(function () {
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
        Route::get('/history/{jadwal_id}', [\App\Http\Controllers\Absensi\AbsensiController::class, 'history'])->name('history');
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
});
