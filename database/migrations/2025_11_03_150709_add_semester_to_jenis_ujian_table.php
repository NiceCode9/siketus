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
        Schema::table('jenis_ujian', function (Blueprint $table) {
            $table->enum('semester', ['ganjil', 'genap'])->after('deskripsi')->default('ganjil');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jenis_ujian', function (Blueprint $table) {
            $table->dropColumn('semester');
        });
    }
};
