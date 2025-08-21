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
        Schema::table('pegawais', function (Blueprint $table) {
            $table->string('nama_universitas_sekolah')->nullable()->after('pendidikan_terakhir');
            $table->string('nama_prodi_jurusan_s2')->nullable()->after('nama_universitas_sekolah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropColumn(['nama_universitas_sekolah', 'nama_prodi_jurusan_s2']);
        });
    }
};
