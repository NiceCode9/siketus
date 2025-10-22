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
            $table->decimal('nilai_by_siswa', 5, 2)->nullable()->after('nilai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penilaian_mapel', function (Blueprint $table) {
            $table->dropColumn('nilai_by_siswa');
        });
    }
};
