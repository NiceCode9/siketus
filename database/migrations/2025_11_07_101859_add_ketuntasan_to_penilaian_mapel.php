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
        Schema::table('penilaian_mapel', function (Blueprint $table) {
            $table->enum('status_ketuntasan', ['tuntas', 'remidi'])->nullable()->after('nilai_by_siswa');
            $table->decimal('kkm_saat_itu', 5, 2)->nullable()->after('status_ketuntasan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penilaian_mapel', function (Blueprint $table) {
            $table->dropColumn(['status_ketuntasan', 'kkm_saat_itu']);
        });
    }
};
