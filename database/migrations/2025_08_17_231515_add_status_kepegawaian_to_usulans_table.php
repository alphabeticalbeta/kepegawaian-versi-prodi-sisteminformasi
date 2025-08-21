<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usulans', function (Blueprint $table) {
            // Tambah kolom baru
            $table->string('status_kepegawaian', 50)->nullable()->after('id');
            // sesuaikan posisi `after('id')` kalau mau taruh di kolom tertentu
        });
    }

    public function down(): void
    {
        Schema::table('usulans', function (Blueprint $table) {
            $table->dropColumn('status_kepegawaian');
        });
    }
};
