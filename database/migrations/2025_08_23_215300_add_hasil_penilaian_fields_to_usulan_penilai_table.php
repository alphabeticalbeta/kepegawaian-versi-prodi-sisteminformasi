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
        Schema::table('usulan_penilai', function (Blueprint $table) {
            $table->enum('hasil_penilaian', ['rekomendasi', 'perbaikan', 'tidak_rekomendasi'])->nullable()->after('catatan_penilaian');
            $table->timestamp('tanggal_penilaian')->nullable()->after('hasil_penilaian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usulan_penilai', function (Blueprint $table) {
            $table->dropColumn(['hasil_penilaian', 'tanggal_penilaian']);
        });
    }
};
