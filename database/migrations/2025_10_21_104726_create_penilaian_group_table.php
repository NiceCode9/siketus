<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabel untuk Penilaian Kedisiplinan
        Schema::create('penilaian_kedisiplinan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('guru_id')->constrained('guru')->onDelete('cascade');
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->enum('semester', ['ganjil', 'genap']);
            $table->foreignId('kedisiplinan_id')->constrained('kedisiplinan')->onDelete('cascade');
            $table->decimal('nilai', 5, 2);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['siswa_id', 'tahun_akademik_id', 'semester', 'kedisiplinan_id'], 'unique_penilaian_kedisiplinan');
        });

        // Tabel untuk Penilaian Kegiatan Keagamaan
        Schema::create('penilaian_keagamaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('guru_id')->constrained('guru')->onDelete('cascade');
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('kegiatan_keagamaan_id')->constrained('kegiatan_keagamaan')->onDelete('cascade');
            $table->enum('semester', ['ganjil', 'genap']);
            $table->decimal('nilai', 5, 2);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['siswa_id', 'kegiatan_keagamaan_id', 'tahun_akademik_id', 'semester'], 'unique_penilaian_keagamaan');
        });

        // Tabel untuk Penilaian Mata Pelajaran
        Schema::create('penilaian_mapel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('guru_kelas_id')->constrained('guru_kelas')->onDelete('cascade');
            $table->foreignId('jenis_ujian_id')->constrained('jenis_ujian')->onDelete('cascade');
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->enum('semester', ['ganjil', 'genap']);
            $table->decimal('nilai', 5, 2);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['siswa_id', 'guru_kelas_id', 'jenis_ujian_id', 'semester'], 'unique_penilaian_mapel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_mapel');
        Schema::dropIfExists('penilaian_keagamaan');
        Schema::dropIfExists('penilaian_kedisiplinan');
    }
};
