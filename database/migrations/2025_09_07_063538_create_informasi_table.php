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
        Schema::create('informasi', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->longText('konten');
            $table->enum('jenis', ['berita', 'pengumuman']);

            // Field khusus untuk Pengumuman
            $table->string('nomor_surat', 100)->nullable();
            $table->date('tanggal_surat')->nullable();

            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->enum('prioritas', ['normal', 'tinggi', 'urgent'])->default('normal');
            $table->datetime('tanggal_publish')->nullable();
            $table->datetime('tanggal_berakhir')->nullable();
            $table->string('penulis');
            $table->text('tags')->nullable(); // JSON array untuk kategori/tags
            $table->string('thumbnail', 500)->nullable(); // URL gambar thumbnail
            $table->text('lampiran')->nullable(); // JSON array untuk file attachments
            $table->integer('view_count')->default(0);
            $table->boolean('is_featured')->default(false); // Untuk berita/pengumuman utama
            $table->boolean('is_pinned')->default(false); // Untuk pengumuman yang di-pin
            $table->timestamps();

            // Index untuk performa
            $table->index(['jenis', 'status']);
            $table->index(['tanggal_publish']);
            $table->index(['is_featured', 'is_pinned']);
            $table->unique('nomor_surat'); // Nomor surat harus unik
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('informasi');
    }
};
