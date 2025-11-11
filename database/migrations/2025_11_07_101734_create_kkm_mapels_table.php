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
        Schema::create('kkm_mapel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_kelas_id')->constrained('guru_kelas')->onDelete('cascade');
            $table->foreignId('jenis_ujian_id')->constrained('jenis_ujian')->onDelete('cascade');
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->onDelete('cascade');
            $table->decimal('kkm', 5, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Unique constraint: satu guru_kelas, satu jenis_ujian, satu tahun akademik = satu KKM
            $table->unique(['guru_kelas_id', 'jenis_ujian_id', 'tahun_akademik_id'], 'unique_kkm_mapel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kkm_mapel');
    }
};
