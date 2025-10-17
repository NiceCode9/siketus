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
    ]);

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



    Route::get('/get-guru-mapel', [GuruKelasController::class, 'getGuruMapel'])->name('getGuruMapel');
    Route::get('/get-mapel', [GuruKelasController::class, 'getMapel'])->name('getMapel');
});

require __DIR__ . '/auth.php';
