<?php
namespace App\Policies;

// PERBAIKAN: Ubah use statement ini untuk menunjuk ke model Pegawai yang benar
use App\Models\KepegawaianUniversitas\Pegawai;

class PegawaiPolicy
{
    /**
     * Tentukan apakah pegawai bisa melihat daftar resource.
     */
    public function viewAny(Pegawai $pegawai): bool
    {
        // Kode ini sekarang akan berfungsi karena tipe $pegawai sudah benar
        return $pegawai->roles->contains('name', 'Kepegawaian Universitas');
    }

    /**
     * Tentukan apakah pegawai bisa memperbarui resource.
     */
    public function update(Pegawai $pegawai, Pegawai $model): bool
    {
        return $pegawai->roles->contains('name', 'Kepegawaian Universitas');
    }
}
