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
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pertemuan_id')->constrained('pertemuan')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->enum('status_kehadiran', ['hadir', 'izin', 'sakit', 'alpha'])->default('alpha');
            $table->text('keterangan')->nullable();
            $table->timestamp('waktu_absen')->nullable();
            $table->timestamps();

            // Unique constraint: satu siswa hanya bisa diabsen sekali per pertemuan
            $table->unique(['pertemuan_id', 'siswa_id']);

            // Index untuk performa query
            $table->index(['pertemuan_id', 'status_kehadiran']);
            $table->index('siswa_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
