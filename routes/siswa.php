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
});
