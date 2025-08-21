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
        // Only create penilais table if it doesn't exist
        if (!Schema::hasTable('penilais')) {
            Schema::create('penilais', function (Blueprint $table) {
                $table->id();
                $table->string('nama_lengkap');
                $table->string('nip')->unique();
                $table->string('email')->unique();
                $table->text('bidang_keahlian')->nullable();
                $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
                $table->timestamps();
            });
        }

        // Only create usulan_penilai table if it doesn't exist
        if (!Schema::hasTable('usulan_penilai')) {
            Schema::create('usulan_penilai', function (Blueprint $table) {
                $table->id();
                $table->foreignId('usulan_id')->constrained('usulans')->onDelete('cascade');
                $table->foreignId('penilai_id')->constrained('penilais')->onDelete('cascade');
                $table->enum('status_penilaian', ['pending', 'sedang_dinilai', 'selesai'])->default('pending');
                $table->text('catatan_penilaian')->nullable();
                $table->timestamps();

                $table->unique(['usulan_id', 'penilai_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usulan_penilai');
        Schema::dropIfExists('penilais');
    }
};
