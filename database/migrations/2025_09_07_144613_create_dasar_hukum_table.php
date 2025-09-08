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
        Schema::create('dasar_hukum', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->longText('konten');
            $table->enum('jenis_dasar_hukum', ['keputusan', 'pedoman', 'peraturan', 'surat_edaran', 'surat_kementerian', 'surat_rektor', 'undang_undang']);
            $table->enum('sub_jenis', ['peraturan', 'surat_keputusan', 'sk_non_pns'])->nullable();
            $table->string('nomor_dokumen');
            $table->date('tanggal_dokumen');
            $table->string('nama_instansi');
            $table->date('masa_berlaku')->nullable();
            $table->string('penulis');
            $table->text('tags')->nullable();
            $table->string('thumbnail')->nullable();
            $table->text('lampiran')->nullable(); // JSON array of files
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->datetime('tanggal_publish')->nullable();
            $table->datetime('tanggal_berakhir')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dasar_hukum');
    }
};
