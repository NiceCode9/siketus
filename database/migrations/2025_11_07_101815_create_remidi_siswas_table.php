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
        Schema::create('remidi_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('restrict');
            $table->foreignId('penilaian_mapel_id')->constrained('penilaian_mapel')->onDelete('restrict');
            $table->foreignId('guru_kelas_id')->constrained('guru_kelas')->onDelete('restrict');
            $table->foreignId('jenis_ujian_id')->constrained('jenis_ujian')->onDelete('restrict');
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->onDelete('restrict');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('restrict');
            $table->enum('semester', ['ganjil', 'genap']);
            $table->decimal('nilai_asli', 5, 2);
            $table->decimal('kkm', 5, 2);
            $table->decimal('nilai_remidi', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2);
            $table->enum('status_remidi', ['pending', 'selesai', 'batal'])->default('pending');
            $table->dateTime('tanggal_remidi')->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('is_notified')->default(true);
            $table->timestamps();

            // Index untuk query performa
            $table->index(['siswa_id', 'status_remidi']);
            $table->index(['guru_kelas_id', 'status_remidi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remidi_siswa');
    }
};
