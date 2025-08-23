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
        Schema::create('pegawais', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // Pastikan engine ini ada
            $table->id();

            // Kolom Relasi
            $table->foreignId('pangkat_terakhir_id')->constrained('pangkats');
            $table->foreignId('jabatan_terakhir_id')->constrained('jabatans');
            $table->foreignId('unit_kerja_id')->constrained('sub_sub_unit_kerjas');

            // Data Utama & Autentikasi
            $table->string('jenis_pegawai'); // Dosen, Tenaga Kependidikan
            $table->string('status_kepegawaian')->nullable();
            $table->string('nip', 18)->unique();
            $table->string('nuptk', 16)->nullable();
            $table->string('gelar_depan')->nullable();
            $table->string('nama_lengkap'); // <-- Ditambahkan
            $table->string('gelar_belakang')->nullable();
            $table->string('email')->unique(); // <-- Ditambahkan
            $table->string('password'); // <-- Ditambahkan
            $table->string('nomor_kartu_pegawai')->nullable();
            $table->string('foto')->nullable(); // <-- Ditambahkan

            // Data Kelahiran & Pribadi
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['Laki-Laki', 'Perempuan']);
            $table->string('nomor_handphone');

            // Data Kepegawaian (TMT & SK) - Dibuat nullable agar tidak error saat data awal kosong
            $table->date('tmt_cpns')->nullable();
            $table->string('sk_cpns')->nullable(); // Path ke file
            $table->date('tmt_pns')->nullable();
            $table->string('sk_pns')->nullable(); // Path ke file
            $table->date('tmt_pangkat')->nullable();
            $table->string('sk_pangkat_terakhir')->nullable(); // Path ke file
            $table->date('tmt_jabatan')->nullable();
            $table->string('sk_jabatan_terakhir')->nullable(); // Path ke file

            // Data Pendidikan
            $table->string('pendidikan_terakhir')->nullable();
            $table->string('ijazah_terakhir')->nullable(); // Path ke file
            $table->string('transkrip_nilai_terakhir')->nullable(); // Path ke file
            $table->string('sk_penyetaraan_ijazah')->nullable(); // Path ke file
            $table->string('disertasi_thesis_terakhir')->nullable(); // Path ke file

            // Data Fungsional (jika dosen, dll)
            $table->text('mata_kuliah_diampu')->nullable();
            $table->text('ranting_ilmu_kepakaran')->nullable();
            $table->string('url_profil_sinta')->nullable();

            // Data Kinerja
            $table->enum('predikat_kinerja_tahun_pertama', ['Sangat Baik', 'Baik', 'Cukup', 'Kurang', 'Sangat Kurang', 'Perlu Perbaikan'])->nullable();
            $table->string('skp_tahun_pertama')->nullable(); // Path ke file
            $table->enum('predikat_kinerja_tahun_kedua', ['Sangat Baik', 'Baik', 'Cukup', 'Kurang', 'Sangat Kurang', 'Perlu Perbaikan'])->nullable();
            $table->string('skp_tahun_kedua')->nullable(); // Path ke file
            $table->float('nilai_konversi')->nullable();
            $table->string('pak_konversi')->nullable(); // Path ke file
            $table->timestamps();

            // $table->json('role'); // <-- Baris ini dihapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};
