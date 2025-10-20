<?php

use App\Http\Controllers\GuruKelasController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resources([
        'tahun-akademik' => \App\Http\Controllers\TahunAkademikController::class,
        'jurusan' => \App\Http\Controllers\JurusanController::class,
        'kegiatan-keagamaan' => \App\Http\Controllers\KegiatanKeagamaanController::class,
        'kedisiplinan' => \App\Http\Controllers\KedisiplinanController::class,
        'guru' => \App\Http\Controllers\GuruController::class,
        'kelas' => \App\Http\Controllers\KelasController::class,
        'siswa' => \App\Http\Controllers\SiswaController::class,
        'mapel' => \App\Http\Controllers\MapelController::class,
        'jenis-ujian' => \App\Http\Controllers\JenisUjianController::class,
        'kalender' => \App\Http\Controllers\Absensi\KalenderAkademikController::class,
        // 'jadwal' => \App\Http\Controllers\Absensi\JadwalPelajaranController::class,
    ]);

    Route::prefix('jadwal')->name('jadwal.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Absensi\JadwalPelajaranController::class, 'index'])->name('index');
        Route::get('/get-kelas', [\App\Http\Controllers\Absensi\JadwalPelajaranController::class, 'getKelas'])->name('get-kelas');
        Route::get('/get-guru-kelas', [\App\Http\Controllers\Absensi\JadwalPelajaranController::class, 'getGuruKelas'])->name('get-guru-kelas');
        Route::get('/create', [\App\Http\Controllers\Absensi\JadwalPelajaranController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Absensi\JadwalPelajaranController::class, 'store'])->name('store');
        Route::get('/{jadwal}', [\App\Http\Controllers\Absensi\JadwalPelajaranController::class, 'show'])->name('show');
        Route::get('/{jadwal}/edit', [\App\Http\Controllers\Absensi\JadwalPelajaranController::class, 'edit'])->name('edit');
        Route::put('/{jadwal}', [\App\Http\Controllers\Absensi\JadwalPelajaranController::class, 'update'])->name('update');
        Route::delete('/{jadwal}', [\App\Http\Controllers\Absensi\JadwalPelajaranController::class, 'destroy'])->name('destroy');

        // Export
        Route::get('/export/pdf', [\App\Http\Controllers\Absensi\JadwalPelajaranController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/export/excel', [\App\Http\Controllers\Absensi\JadwalPelajaranController::class, 'exportExcel'])->name('export-excel');
    });

    Route::controller(\App\Http\Controllers\GuruMapelController::class)->group(function () {
        Route::get('guru-mapel', 'index')->name('guru-mapel.index');
        Route::post('guru-mapel', 'store')->name('guru-mapel.store');
        Route::get('guru-mapel/{id}/edit', 'edit')->name('guru-mapel.edit');
        Route::put('guru-mapel/{id}', 'update')->name('guru-mapel.update');
        Route::delete('guru-mapel/{id}', 'destroy')->name('guru-mapel.destroy');
    });

    // Tambahkan routes berikut ke file routes/web.php

    Route::prefix('guru-kelas')->name('guru-kelas.')->group(function () {
        Route::get('/', [GuruKelasController::class, 'index'])->name('index');
        Route::get('/data', [GuruKelasController::class, 'getData'])->name('getData');
        Route::post('/', [GuruKelasController::class, 'store'])->name('store');
        Route::get('/{id}', [GuruKelasController::class, 'show'])->name('show');
        Route::put('/{id}', [GuruKelasController::class, 'update'])->name('update');
        Route::delete('/{id}', [GuruKelasController::class, 'destroy'])->name('destroy');
    });

    // Management Pertemuan
    Route::prefix('pertemuan')->name('pertemuan.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Absensi\PertemuanController::class, 'index'])->name('index');
        Route::post('/generate', [\App\Http\Controllers\Absensi\PertemuanController::class, 'generate'])->name('generate');
        Route::delete('/reset', [\App\Http\Controllers\Absensi\PertemuanController::class, 'resetPertemuan'])->name('reset');
        Route::get('/list', [\App\Http\Controllers\Absensi\PertemuanController::class, 'list'])->name('list');
    });

    // Absensi
    Route::prefix('absensi')->name('absensi.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Absensi\AbsensiController::class, 'index'])->name('index');
        Route::get('/create/{pertemuan}', [\App\Http\Controllers\Absensi\AbsensiController::class, 'create'])->name('create');
        Route::post('/store/{pertemuan}', [\App\Http\Controllers\Absensi\AbsensiController::class, 'store'])->name('store');
        Route::get('/edit/{pertemuan}', [\App\Http\Controllers\Absensi\AbsensiController::class, 'edit'])->name('edit');
        Route::put('/update/{pertemuan}', [\App\Http\Controllers\Absensi\AbsensiController::class, 'update'])->name('update');
    });

    Route::get('/get-guru-mapel', [GuruKelasController::class, 'getGuruMapel'])->name('getGuruMapel');
    Route::get('/get-mapel', [GuruKelasController::class, 'getMapel'])->name('getMapel');
});

Route::get('/get-tahun-akademik', [\App\Http\Controllers\KegiatanKeagamaanController::class, 'getTahunAkademik'])->name('kegiatan-keagamaan.tahun-akademik');

require __DIR__ . '/auth.php';
