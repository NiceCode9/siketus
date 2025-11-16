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
        Schema::table('penilaian_kedisiplinan', function (Blueprint $table) {
            $table->boolean('validasi')->default(false)->after('nilai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penilaian_kedisiplinan', function (Blueprint $table) {
            $table->dropColumn('validasi');
        });
    }
};
