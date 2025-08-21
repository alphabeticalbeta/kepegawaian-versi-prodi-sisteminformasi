<?php

namespace App\Helpers;

class ProfileDisplayHelper
{
    /**
     * Menampilkan nilai field dengan placeholder "-" jika kosong
     *
     * @param mixed $value
     * @param string $placeholder
     * @return string
     */
    public static function displayValue($value, $placeholder = '-')
    {
        if (empty($value) || $value === null || $value === '') {
            return $placeholder;
        }

        return $value;
    }

    /**
     * Menampilkan nilai field untuk halaman show (tidak menampilkan jika hanya "-")
     *
     * @param mixed $value
     * @param string $placeholder
     * @return string|null
     */
    public static function displayValueForShow($value, $placeholder = '-')
    {
        $displayValue = self::displayValue($value, $placeholder);

        if ($displayValue === $placeholder) {
            return null; // Tidak ditampilkan di halaman show
        }

        return $displayValue;
    }

    /**
     * Menampilkan nama lengkap dengan gelar
     *
     * @param object $pegawai
     * @return string
     */
    public static function displayNamaLengkap($pegawai)
    {
        $nama = [];

        if (!empty($pegawai->gelar_depan)) {
            $nama[] = $pegawai->gelar_depan;
        }

        $nama[] = $pegawai->nama_lengkap ?? '-';

        if (!empty($pegawai->gelar_belakang)) {
            $nama[] = $pegawai->gelar_belakang;
        }

        return implode(' ', $nama);
    }

    /**
     * Menampilkan nama lengkap untuk halaman show
     *
     * @param object $pegawai
     * @return string
     */
    public static function displayNamaLengkapForShow($pegawai)
    {
        $nama = [];

        if (!empty($pegawai->gelar_depan)) {
            $nama[] = $pegawai->gelar_depan;
        }

        if (!empty($pegawai->nama_lengkap)) {
            $nama[] = $pegawai->nama_lengkap;
        }

        if (!empty($pegawai->gelar_belakang)) {
            $nama[] = $pegawai->gelar_belakang;
        }

        return !empty($nama) ? implode(' ', $nama) : null;
    }

    /**
     * Menampilkan tempat dan tanggal lahir
     *
     * @param object $pegawai
     * @return string
     */
    public static function displayTempatTanggalLahir($pegawai)
    {
        $tempat = self::displayValue($pegawai->tempat_lahir);
        $tanggal = $pegawai->tanggal_lahir ? $pegawai->tanggal_lahir->format('d F Y') : '-';

        return "{$tempat}, {$tanggal}";
    }

    /**
     * Menampilkan tempat dan tanggal lahir untuk halaman show
     *
     * @param object $pegawai
     * @return string|null
     */
    public static function displayTempatTanggalLahirForShow($pegawai)
    {
        $tempat = self::displayValueForShow($pegawai->tempat_lahir);
        $tanggal = $pegawai->tanggal_lahir ? $pegawai->tanggal_lahir->format('d F Y') : null;

        if ($tempat && $tanggal) {
            return "{$tempat}, {$tanggal}";
        } elseif ($tempat) {
            return $tempat;
        } elseif ($tanggal) {
            return $tanggal;
        }

        return null;
    }

    /**
     * Menampilkan unit kerja lengkap
     *
     * @param object $pegawai
     * @return string
     */
    public static function displayUnitKerja($pegawai)
    {
        $unitKerja = [];

        if ($pegawai->unitKerja && $pegawai->unitKerja->subUnitKerja && $pegawai->unitKerja->subUnitKerja->unitKerja) {
            $unitKerja[] = self::displayValue($pegawai->unitKerja->subUnitKerja->unitKerja->nama);
            $unitKerja[] = self::displayValue($pegawai->unitKerja->subUnitKerja->nama);
            $unitKerja[] = self::displayValue($pegawai->unitKerja->nama);
        } else {
            $unitKerja[] = '-';
        }

        return implode(' > ', $unitKerja);
    }

    /**
     * Menampilkan unit kerja lengkap untuk halaman show
     *
     * @param object $pegawai
     * @return string|null
     */
    public static function displayUnitKerjaForShow($pegawai)
    {
        $unitKerja = [];

        if ($pegawai->unitKerja && $pegawai->unitKerja->subUnitKerja && $pegawai->unitKerja->subUnitKerja->unitKerja) {
            $unitKerja[] = self::displayValueForShow($pegawai->unitKerja->subUnitKerja->unitKerja->nama);
            $unitKerja[] = self::displayValueForShow($pegawai->unitKerja->subUnitKerja->nama);
            $unitKerja[] = self::displayValueForShow($pegawai->unitKerja->nama);
        }

        $unitKerja = array_filter($unitKerja); // Hapus nilai null

        return !empty($unitKerja) ? implode(' > ', $unitKerja) : null;
    }

    /**
     * Menampilkan pangkat
     *
     * @param object $pegawai
     * @return string
     */
    public static function displayPangkat($pegawai)
    {
        return self::displayValue($pegawai->pangkat->pangkat ?? null);
    }

    /**
     * Menampilkan pangkat untuk halaman show
     *
     * @param object $pegawai
     * @return string|null
     */
    public static function displayPangkatForShow($pegawai)
    {
        return self::displayValueForShow($pegawai->pangkat->pangkat ?? null);
    }

    /**
     * Menampilkan jabatan
     *
     * @param object $pegawai
     * @return string
     */
    public static function displayJabatan($pegawai)
    {
        return self::displayValue($pegawai->jabatan->jabatan ?? null);
    }

    /**
     * Menampilkan jabatan untuk halaman show
     *
     * @param object $pegawai
     * @return string|null
     */
    public static function displayJabatanForShow($pegawai)
    {
        return self::displayValueForShow($pegawai->jabatan->jabatan ?? null);
    }

    /**
     * Menampilkan email dengan link
     *
     * @param object $pegawai
     * @return string
     */
    public static function displayEmail($pegawai)
    {
        return self::displayValue($pegawai->email);
    }

    /**
     * Menampilkan email untuk halaman show
     *
     * @param object $pegawai
     * @return string|null
     */
    public static function displayEmailForShow($pegawai)
    {
        return self::displayValueForShow($pegawai->email);
    }

    /**
     * Menampilkan nomor handphone
     *
     * @param object $pegawai
     * @return string
     */
    public static function displayNomorHandphone($pegawai)
    {
        return self::displayValue($pegawai->nomor_handphone);
    }

    /**
     * Menampilkan nomor handphone untuk halaman show
     *
     * @param object $pegawai
     * @return string|null
     */
    public static function displayNomorHandphoneForShow($pegawai)
    {
        return self::displayValueForShow($pegawai->nomor_handphone);
    }

    /**
     * Menampilkan NUPTK
     *
     * @param object $pegawai
     * @return string
     */
    public static function displayNuptk($pegawai)
    {
        return self::displayValue($pegawai->nuptk);
    }

    /**
     * Menampilkan NUPTK untuk halaman show
     *
     * @param object $pegawai
     * @return string|null
     */
    public static function displayNuptkForShow($pegawai)
    {
        return self::displayValueForShow($pegawai->nuptk);
    }

    /**
     * Menampilkan pendidikan terakhir
     *
     * @param object $pegawai
     * @return string
     */
    public static function displayPendidikanTerakhir($pegawai)
    {
        return self::displayValue($pegawai->pendidikan_terakhir);
    }

    /**
     * Menampilkan pendidikan terakhir untuk halaman show
     *
     * @param object $pegawai
     * @return string|null
     */
    public static function displayPendidikanTerakhirForShow($pegawai)
    {
        return self::displayValueForShow($pegawai->pendidikan_terakhir);
    }

    /**
     * Menampilkan nama universitas/sekolah
     *
     * @param object $pegawai
     * @return string
     */
    public static function displayNamaUniversitasSekolah($pegawai)
    {
        return self::displayValue($pegawai->nama_universitas_sekolah);
    }

    /**
     * Menampilkan nama universitas/sekolah untuk halaman show
     *
     * @param object $pegawai
     * @return string|null
     */
    public static function displayNamaUniversitasSekolahForShow($pegawai)
    {
        return self::displayValueForShow($pegawai->nama_universitas_sekolah);
    }

    /**
     * Menampilkan nama prodi/jurusan
     *
     * @param object $pegawai
     * @return string
     */
    public static function displayNamaProdiJurusan($pegawai)
    {
        return self::displayValue($pegawai->nama_prodi_jurusan);
    }

    /**
     * Menampilkan nama prodi/jurusan untuk halaman show
     *
     * @param object $pegawai
     * @return string|null
     */
    public static function displayNamaProdiJurusanForShow($pegawai)
    {
        return self::displayValueForShow($pegawai->nama_prodi_jurusan);
    }

    /**
     * Menampilkan mata kuliah diampu
     *
     * @param object $pegawai
     * @return string
     */
    public static function displayMataKuliahDiampu($pegawai)
    {
        return self::displayValue($pegawai->mata_kuliah_diampu);
    }

    /**
     * Menampilkan mata kuliah diampu untuk halaman show
     *
     * @param object $pegawai
     * @return string|null
     */
    public static function displayMataKuliahDiampuForShow($pegawai)
    {
        return self::displayValueForShow($pegawai->mata_kuliah_diampu);
    }

    /**
     * Menampilkan ranting ilmu kepakaran
     *
     * @param object $pegawai
     * @return string
     */
    public static function displayRantingIlmuKepakaran($pegawai)
    {
        return self::displayValue($pegawai->ranting_ilmu_kepakaran);
    }

    /**
     * Menampilkan ranting ilmu kepakaran untuk halaman show
     *
     * @param object $pegawai
     * @return string|null
     */
    public static function displayRantingIlmuKepakaranForShow($pegawai)
    {
        return self::displayValueForShow($pegawai->ranting_ilmu_kepakaran);
    }

    /**
     * Menampilkan URL profil SINTA
     *
     * @param object $pegawai
     * @return string
     */
    public static function displayUrlProfilSinta($pegawai)
    {
        return self::displayValue($pegawai->url_profil_sinta);
    }

    /**
     * Menampilkan URL profil SINTA untuk halaman show
     *
     * @param object $pegawai
     * @return string|null
     */
    public static function displayUrlProfilSintaForShow($pegawai)
    {
        return self::displayValueForShow($pegawai->url_profil_sinta);
    }

    /**
     * Menampilkan predikat kinerja tahun pertama
     *
     * @param object $pegawai
     * @return string
     */
    public static function displayPredikatKinerjaTahunPertama($pegawai)
    {
        return self::displayValue($pegawai->predikat_kinerja_tahun_pertama);
    }

    /**
     * Menampilkan predikat kinerja tahun pertama untuk halaman show
     *
     * @param object $pegawai
     * @return string|null
     */
    public static function displayPredikatKinerjaTahunPertamaForShow($pegawai)
    {
        return self::displayValueForShow($pegawai->predikat_kinerja_tahun_pertama);
    }

    /**
     * Menampilkan predikat kinerja tahun kedua
     *
     * @param object $pegawai
     * @return string
     */
    public static function displayPredikatKinerjaTahunKedua($pegawai)
    {
        return self::displayValue($pegawai->predikat_kinerja_tahun_kedua);
    }

    /**
     * Menampilkan predikat kinerja tahun kedua untuk halaman show
     *
     * @param object $pegawai
     * @return string|null
     */
    public static function displayPredikatKinerjaTahunKeduaForShow($pegawai)
    {
        return self::displayValueForShow($pegawai->predikat_kinerja_tahun_kedua);
    }

    /**
     * Menampilkan nilai konversi
     *
     * @param object $pegawai
     * @return string
     */
    public static function displayNilaiKonversi($pegawai)
    {
        return self::displayValue($pegawai->nilai_konversi);
    }

    /**
     * Menampilkan nilai konversi untuk halaman show
     *
     * @param object $pegawai
     * @return string|null
     */
    public static function displayNilaiKonversiForShow($pegawai)
    {
        return self::displayValueForShow($pegawai->nilai_konversi);
    }
}
