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
        Schema::table('kegiatan_keagamaan', function (Blueprint $table) {
            $table->unsignedBigInteger('tahun_akademik_id')->after('nama_kegiatan');
            $table->string('tingkat_kelas')->after('tahun_akademik_id');
            $table->string('semester')->after('tingkat_kelas');

            $table->foreign('tahun_akademik_id')->references('id')->on('tahun_akademik')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kegiatan_keagamaan', function (Blueprint $table) {
            $table->dropForeign(['tahun_akademik_id']);
            $table->dropColumn('tahun_akademik_id');
            $table->dropColumn('tingkat_kelas');
            $table->dropColumn('semester');
        });
    }
};
