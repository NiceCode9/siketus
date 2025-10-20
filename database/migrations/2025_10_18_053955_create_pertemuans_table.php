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
        Schema::create('pertemuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_pelajaran_id')->constrained('jadwal_pelajaran')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam_mulai_aktual')->nullable();
            $table->time('jam_selesai_aktual')->nullable();
            $table->text('materi')->nullable();
            $table->integer('pertemuan_ke')->default(1);
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->boolean('generated_auto')->default(false);
            $table->timestamps();

            // Index untuk performa query
            $table->index(['jadwal_pelajaran_id', 'tanggal']);
            $table->index(['tanggal', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pertemuan');
    }
};
