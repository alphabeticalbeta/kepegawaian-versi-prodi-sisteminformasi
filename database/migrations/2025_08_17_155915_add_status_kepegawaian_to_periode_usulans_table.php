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
        Schema::table('periode_usulans', function (Blueprint $table) {
            $table->json('status_kepegawaian')->nullable()->after('jenis_usulan')->comment('Status kepegawaian yang diizinkan untuk mengakses periode ini');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('periode_usulans', function (Blueprint $table) {
            $table->dropColumn('status_kepegawaian');
        });
    }
};
