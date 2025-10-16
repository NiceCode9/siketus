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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('siswa_id')->after('password')->nullable()->constrained('siswa')->onDelete('cascade');
            $table->foreignId('guru_id')->after('siswa_id')->nullable()->constrained('guru')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['siswa_id', 'guru_id']);
            $table->dropColumn(['siswa_id', 'guru_id']);
        });
    }
};
