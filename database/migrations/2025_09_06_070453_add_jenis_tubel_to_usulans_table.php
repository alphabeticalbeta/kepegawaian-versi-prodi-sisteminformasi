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
        Schema::table('usulans', function (Blueprint $table) {
            // Kolom untuk menyimpan jenis tugas belajar
            $table->string('jenis_tubel')->nullable()->comment('Jenis tugas belajar: Tugas Belajar atau Perpanjangan Tugas Belajar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usulans', function (Blueprint $table) {
            // Drop kolom jenis_tubel
            $table->dropColumn('jenis_tubel');
        });
    }
};
