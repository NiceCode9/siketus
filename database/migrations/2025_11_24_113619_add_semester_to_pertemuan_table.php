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
        Schema::table('pertemuan', function (Blueprint $table) {
            $table->enum('semester', ['ganjil', 'genap'])->default('ganjil')->after('status');
            $table->index('semester');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pertemuan', function (Blueprint $table) {
            $table->dropIndex(['semester']);
            $table->dropColumn('semester');
        });
    }
};
