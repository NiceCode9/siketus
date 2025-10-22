<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('siswa')->name('siswa.')->group(function () {
    // Jadwal Kelas Siswa
    Route::prefix('jadwal')->name('jadwal.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Siswa\JadwalSiswaController::class, 'index'])->name('index');
        Route::get('/show/{id}', [\App\Http\Controllers\Siswa\JadwalSiswaController::class, 'show'])->name('show');
        Route::post('/set-reminder', [\App\Http\Controllers\Siswa\JadwalSiswaController::class, 'setReminder'])->name('set-reminder');

        // Download & Export
        Route::get('/download-pdf', [\App\Http\Controllers\Siswa\JadwalSiswaController::class, 'downloadPdf'])->name('download-pdf');
        Route::get('/export-calendar', [\App\Http\Controllers\Siswa\JadwalSiswaController::class, 'exportCalendar'])->name('export-calendar');
    });

    Route::prefix('penilaian')->name('penilaian.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Siswa\PenilaianSiswaController::class, 'index'])->name('index');
    });

    // Routes Riwayat Penilaian - SISWA
    Route::get('/riwayat-penilaian/siswa', [\App\Http\Controllers\RiwayatPenilaianController::class, 'siswaIndex'])
        ->name('riwayat-penilaian.siswa.index');
    Route::get('/riwayat-penilaian/siswa/mapel/{mapel}/detail', [\App\Http\Controllers\RiwayatPenilaianController::class, 'siswaDetailMapel'])
        ->name('riwayat-penilaian.siswa.detail-mapel');
});
