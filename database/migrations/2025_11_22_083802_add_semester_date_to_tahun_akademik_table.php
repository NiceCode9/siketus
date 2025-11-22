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
        Schema::table('tahun_akademik', function (Blueprint $table) {
            // Rename existing columns untuk semester ganjil
            $table->renameColumn('tanggal_mulai', 'tanggal_mulai_ganjil');
            $table->renameColumn('tanggal_selesai', 'tanggal_selesai_ganjil');
        });

        Schema::table('tahun_akademik', function (Blueprint $table) {
            // Tambah columns untuk semester genap
            $table->date('tanggal_mulai_genap')->nullable()->after('tanggal_selesai_ganjil');
            $table->date('tanggal_selesai_genap')->nullable()->after('tanggal_mulai_genap');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tahun_akademik', function (Blueprint $table) {
            $table->dropColumn(['tanggal_mulai_genap', 'tanggal_selesai_genap']);
        });

        Schema::table('tahun_akademik', function (Blueprint $table) {
            $table->renameColumn('tanggal_mulai_ganjil', 'tanggal_mulai');
            $table->renameColumn('tanggal_selesai_ganjil', 'tanggal_selesai');
        });
    }
};
