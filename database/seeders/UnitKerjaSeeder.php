<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BackendUnivUsulan\UnitKerja;
use App\Models\BackendUnivUsulan\SubUnitKerja;
use App\Models\BackendUnivUsulan\SubSubUnitKerja;

class UnitKerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏢 Seeding data Unit Kerja...');

        // =========================================================
        //         FAKULTAS-FAKULTAS UNIVERSITAS MULAWARMAN
        // =========================================================

        // === FAKULTAS TEKNIK ===
        $fakultasTeknik = UnitKerja::firstOrCreate(['nama' => 'Fakultas Teknik']);

        // Jurusan di bawah Fakultas Teknik
        $jurusanInformatika = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasTeknik->id, 'nama' => 'S1 Informatika']
        );
        $jurusanSipil = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasTeknik->id, 'nama' => 'S1 Teknik Sipil']
        );
        $jurusanMesin = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasTeknik->id, 'nama' => 'S1 Teknik Mesin']
        );
        $jurusanElektro = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasTeknik->id, 'nama' => 'S1 Teknik Elektro']
        );

        // Prodi di bawah Jurusan Informatika
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanInformatika->id, 'nama' => 'Prodi Rekayasa Perangkat Lunak']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanInformatika->id, 'nama' => 'Prodi Jaringan Komputer']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanInformatika->id, 'nama' => 'Prodi Sistem Informasi']
        );

        // Prodi di bawah Jurusan Teknik Sipil
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanSipil->id, 'nama' => 'Prodi Struktur Bangunan']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanSipil->id, 'nama' => 'Prodi Manajemen Konstruksi']
        );

        // Prodi di bawah Jurusan Teknik Mesin
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanMesin->id, 'nama' => 'Prodi Konversi Energi']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanMesin->id, 'nama' => 'Prodi Manufaktur']
        );

        // Prodi di bawah Jurusan Teknik Elektro
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanElektro->id, 'nama' => 'Prodi Teknik Tenaga Listrik']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanElektro->id, 'nama' => 'Prodi Teknik Telekomunikasi']
        );

        // === FAKULTAS EKONOMI DAN BISNIS ===
        $fakultasEkonomi = UnitKerja::firstOrCreate(['nama' => 'Fakultas Ekonomi dan Bisnis']);

        // Jurusan di bawah Fakultas Ekonomi
        $jurusanManajemen = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasEkonomi->id, 'nama' => 'S1 Manajemen']
        );
        $jurusanAkuntansi = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasEkonomi->id, 'nama' => 'S1 Akuntansi']
        );
        $jurusanEkonomi = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasEkonomi->id, 'nama' => 'S1 Ekonomi Pembangunan']
        );

        // Prodi di bawah Jurusan Manajemen
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanManajemen->id, 'nama' => 'Prodi Manajemen Keuangan']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanManajemen->id, 'nama' => 'Prodi Manajemen Pemasaran']
        );

        // Prodi di bawah Jurusan Akuntansi
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanAkuntansi->id, 'nama' => 'Prodi Akuntansi Keuangan']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanAkuntansi->id, 'nama' => 'Prodi Akuntansi Manajemen']
        );

        // Prodi di bawah Jurusan Ekonomi
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanEkonomi->id, 'nama' => 'Prodi Ekonomi Regional']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanEkonomi->id, 'nama' => 'Prodi Ekonomi Moneter']
        );

        // === FAKULTAS KEDOKTERAN ===
        $fakultasKedokteran = UnitKerja::firstOrCreate(['nama' => 'Fakultas Kedokteran']);

        // Jurusan di bawah Fakultas Kedokteran
        $jurusanKedokteran = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasKedokteran->id, 'nama' => 'S1 Pendidikan Dokter']
        );
        $jurusanKeperawatan = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasKedokteran->id, 'nama' => 'S1 Keperawatan']
        );

        // Prodi di bawah Jurusan Kedokteran
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanKedokteran->id, 'nama' => 'Prodi Pendidikan Dokter Umum']
        );

        // Prodi di bawah Jurusan Keperawatan
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanKeperawatan->id, 'nama' => 'Prodi Keperawatan Medikal Bedah']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanKeperawatan->id, 'nama' => 'Prodi Keperawatan Maternitas']
        );

        // === FAKULTAS HUKUM ===
        $fakultasHukum = UnitKerja::firstOrCreate(['nama' => 'Fakultas Hukum']);

        // Jurusan di bawah Fakultas Hukum
        $jurusanHukum = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasHukum->id, 'nama' => 'S1 Ilmu Hukum']
        );

        // Prodi di bawah Jurusan Hukum
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanHukum->id, 'nama' => 'Prodi Hukum Pidana']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanHukum->id, 'nama' => 'Prodi Hukum Perdata']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanHukum->id, 'nama' => 'Prodi Hukum Tata Negara']
        );

        // === FAKULTAS PERTANIAN ===
        $fakultasPertanian = UnitKerja::firstOrCreate(['nama' => 'Fakultas Pertanian']);

        // Jurusan di bawah Fakultas Pertanian
        $jurusanAgroteknologi = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasPertanian->id, 'nama' => 'S1 Agroteknologi']
        );
        $jurusanAgribisnis = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasPertanian->id, 'nama' => 'S1 Agribisnis']
        );

        // Prodi di bawah Jurusan Agroteknologi
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanAgroteknologi->id, 'nama' => 'Prodi Budidaya Tanaman']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanAgroteknologi->id, 'nama' => 'Prodi Perlindungan Tanaman']
        );

        // Prodi di bawah Jurusan Agribisnis
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanAgribisnis->id, 'nama' => 'Prodi Agribisnis Hortikultura']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanAgribisnis->id, 'nama' => 'Prodi Agribisnis Perikanan']
        );

        // === FAKULTAS KEHUTANAN ===
        $fakultasKehutanan = UnitKerja::firstOrCreate(['nama' => 'Fakultas Kehutanan']);

        // Jurusan di bawah Fakultas Kehutanan
        $jurusanKehutanan = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasKehutanan->id, 'nama' => 'S1 Kehutanan']
        );

        // Prodi di bawah Jurusan Kehutanan
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanKehutanan->id, 'nama' => 'Prodi Manajemen Hutan']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanKehutanan->id, 'nama' => 'Prodi Teknologi Hasil Hutan']
        );

        // === FAKULTAS KELAUTAN DAN PERIKANAN ===
        $fakultasKelautan = UnitKerja::firstOrCreate(['nama' => 'Fakultas Kelautan dan Perikanan']);

        // Jurusan di bawah Fakultas Kelautan
        $jurusanPerikanan = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasKelautan->id, 'nama' => 'S1 Perikanan']
        );
        $jurusanKelautan = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasKelautan->id, 'nama' => 'S1 Ilmu Kelautan']
        );

        // Prodi di bawah Jurusan Perikanan
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanPerikanan->id, 'nama' => 'Prodi Budidaya Perairan']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanPerikanan->id, 'nama' => 'Prodi Teknologi Hasil Perikanan']
        );

        // Prodi di bawah Jurusan Kelautan
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanKelautan->id, 'nama' => 'Prodi Oseanografi']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanKelautan->id, 'nama' => 'Prodi Manajemen Sumberdaya Pesisir']
        );

        // === FAKULTAS MATEMATIKA DAN ILMU PENGETAHUAN ALAM ===
        $fakultasMIPA = UnitKerja::firstOrCreate(['nama' => 'Fakultas Matematika dan Ilmu Pengetahuan Alam']);

        // Jurusan di bawah Fakultas MIPA
        $jurusanMatematika = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasMIPA->id, 'nama' => 'S1 Matematika']
        );
        $jurusanFisika = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasMIPA->id, 'nama' => 'S1 Fisika']
        );
        $jurusanKimia = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasMIPA->id, 'nama' => 'S1 Kimia']
        );
        $jurusanBiologi = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasMIPA->id, 'nama' => 'S1 Biologi']
        );

        // Prodi di bawah Jurusan Matematika
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanMatematika->id, 'nama' => 'Prodi Matematika Murni']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanMatematika->id, 'nama' => 'Prodi Matematika Terapan']
        );

        // Prodi di bawah Jurusan Fisika
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanFisika->id, 'nama' => 'Prodi Fisika Material']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanFisika->id, 'nama' => 'Prodi Fisika Instrumentasi']
        );

        // Prodi di bawah Jurusan Kimia
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanKimia->id, 'nama' => 'Prodi Kimia Analitik']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanKimia->id, 'nama' => 'Prodi Kimia Organik']
        );

        // Prodi di bawah Jurusan Biologi
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanBiologi->id, 'nama' => 'Prodi Biologi Molekuler']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanBiologi->id, 'nama' => 'Prodi Biologi Lingkungan']
        );

        // === FAKULTAS ILMU SOSIAL DAN ILMU POLITIK ===
        $fakultasFISIP = UnitKerja::firstOrCreate(['nama' => 'Fakultas Ilmu Sosial dan Ilmu Politik']);

        // Jurusan di bawah Fakultas FISIP
        $jurusanSosiologi = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasFISIP->id, 'nama' => 'S1 Sosiologi']
        );
        $jurusanIlmuPolitik = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasFISIP->id, 'nama' => 'S1 Ilmu Politik']
        );
        $jurusanIlmuKomunikasi = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasFISIP->id, 'nama' => 'S1 Ilmu Komunikasi']
        );

        // Prodi di bawah Jurusan Sosiologi
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanSosiologi->id, 'nama' => 'Prodi Sosiologi Pembangunan']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanSosiologi->id, 'nama' => 'Prodi Sosiologi Lingkungan']
        );

        // Prodi di bawah Jurusan Ilmu Politik
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanIlmuPolitik->id, 'nama' => 'Prodi Politik Lokal']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanIlmuPolitik->id, 'nama' => 'Prodi Hubungan Internasional']
        );

        // Prodi di bawah Jurusan Ilmu Komunikasi
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanIlmuKomunikasi->id, 'nama' => 'Prodi Jurnalistik']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanIlmuKomunikasi->id, 'nama' => 'Prodi Public Relations']
        );

        // === FAKULTAS KEGURUAN DAN ILMU PENDIDIKAN ===
        $fakultasFKIP = UnitKerja::firstOrCreate(['nama' => 'Fakultas Keguruan dan Ilmu Pendidikan']);

        // Jurusan di bawah Fakultas FKIP
        $jurusanPendidikanMatematika = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasFKIP->id, 'nama' => 'S1 Pendidikan Matematika']
        );
        $jurusanPendidikanBiologi = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasFKIP->id, 'nama' => 'S1 Pendidikan Biologi']
        );
        $jurusanPendidikanBahasa = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasFKIP->id, 'nama' => 'S1 Pendidikan Bahasa Indonesia']
        );

        // Prodi di bawah Jurusan Pendidikan Matematika
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanPendidikanMatematika->id, 'nama' => 'Prodi Pendidikan Matematika SD']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanPendidikanMatematika->id, 'nama' => 'Prodi Pendidikan Matematika SMP/SMA']
        );

        // Prodi di bawah Jurusan Pendidikan Biologi
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanPendidikanBiologi->id, 'nama' => 'Prodi Pendidikan Biologi SMP/SMA']
        );

        // Prodi di bawah Jurusan Pendidikan Bahasa
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanPendidikanBahasa->id, 'nama' => 'Prodi Pendidikan Bahasa Indonesia SD']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanPendidikanBahasa->id, 'nama' => 'Prodi Pendidikan Bahasa Indonesia SMP/SMA']
        );

        // === UNIT KERJA NON-FAKULTAS ===
        $unitKerjaNonFakultas = UnitKerja::firstOrCreate(['nama' => 'Unit Kerja Non-Fakultas']);

        // Sub Unit Kerja di bawah Unit Kerja Non-Fakultas
        $subUnitLembaga = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $unitKerjaNonFakultas->id, 'nama' => 'Lembaga']
        );
        $subUnitBiro = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $unitKerjaNonFakultas->id, 'nama' => 'Biro']
        );
        $subUnitDirektorat = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $unitKerjaNonFakultas->id, 'nama' => 'Direktorat']
        );

        // Sub-sub Unit Kerja di bawah Lembaga
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $subUnitLembaga->id, 'nama' => 'Lembaga Penelitian dan Pengabdian Masyarakat']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $subUnitLembaga->id, 'nama' => 'Lembaga Pengembangan Pendidikan']
        );

        // Sub-sub Unit Kerja di bawah Biro
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $subUnitBiro->id, 'nama' => 'Biro Akademik dan Kemahasiswaan']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $subUnitBiro->id, 'nama' => 'Biro Umum dan Keuangan']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $subUnitBiro->id, 'nama' => 'Biro Perencanaan dan Kerjasama']
        );

        // Sub-sub Unit Kerja di bawah Direktorat
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $subUnitDirektorat->id, 'nama' => 'Direktorat Sistem Informasi']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $subUnitDirektorat->id, 'nama' => 'Direktorat Pengembangan Usaha']
        );

        // Log hasil seeding
        $totalUnitKerja = UnitKerja::count();
        $totalSubUnitKerja = SubUnitKerja::count();
        $totalSubSubUnitKerja = SubSubUnitKerja::count();

        $this->command->info("✅ UnitKerjaSeeder berhasil dijalankan!");
        $this->command->info("📊 Statistik Unit Kerja:");
        $this->command->info("   • Total Unit Kerja: {$totalUnitKerja}");
        $this->command->info("   • Total Sub Unit Kerja: {$totalSubUnitKerja}");
        $this->command->info("   • Total Sub-sub Unit Kerja: {$totalSubSubUnitKerja}");
        $this->command->info("   • Total Hierarki: " . ($totalUnitKerja + $totalSubUnitKerja + $totalSubSubUnitKerja));
    }
}
