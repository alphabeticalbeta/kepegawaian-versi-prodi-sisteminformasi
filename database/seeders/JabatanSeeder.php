<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BackendUnivUsulan\Jabatan;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jabatans = [
            // ===================================
            // DOSEN FUNGSIONAL (Hierarki Level 1-5)
            // ===================================
            [
                'jabatan' => 'Tenaga Pengajar',
                'jenis_pegawai' => 'Dosen',
                'jenis_jabatan' => 'Dosen Fungsional',
                'hierarchy_level' => 1
            ],
            [
                'jabatan' => 'Asisten Ahli',
                'jenis_pegawai' => 'Dosen',
                'jenis_jabatan' => 'Dosen Fungsional',
                'hierarchy_level' => 2
            ],
            [
                'jabatan' => 'Lektor',
                'jenis_pegawai' => 'Dosen',
                'jenis_jabatan' => 'Dosen Fungsional',
                'hierarchy_level' => 3
            ],
            [
                'jabatan' => 'Lektor Kepala',
                'jenis_pegawai' => 'Dosen',
                'jenis_jabatan' => 'Dosen Fungsional',
                'hierarchy_level' => 4
            ],
            [
                'jabatan' => 'Guru Besar',
                'jenis_pegawai' => 'Dosen',
                'jenis_jabatan' => 'Dosen Fungsional',
                'hierarchy_level' => 5
            ],

            // ===================================
            // DOSEN DENGAN TUGAS TAMBAHAN (Non-Hierarki)
            // ===================================
            [
                'jabatan' => 'Ketua Jurusan',
                'jenis_pegawai' => 'Dosen',
                'jenis_jabatan' => 'Dosen dengan Tugas Tambahan',
                'hierarchy_level' => null
            ],
            [
                'jabatan' => 'Wakil Dekan',
                'jenis_pegawai' => 'Dosen',
                'jenis_jabatan' => 'Dosen dengan Tugas Tambahan',
                'hierarchy_level' => null
            ],
            [
                'jabatan' => 'Dekan',
                'jenis_pegawai' => 'Dosen',
                'jenis_jabatan' => 'Dosen dengan Tugas Tambahan',
                'hierarchy_level' => null
            ],
            [
                'jabatan' => 'Wakil Rektor',
                'jenis_pegawai' => 'Dosen',
                'jenis_jabatan' => 'Dosen dengan Tugas Tambahan',
                'hierarchy_level' => null
            ],
            [
                'jabatan' => 'Rektor',
                'jenis_pegawai' => 'Dosen',
                'jenis_jabatan' => 'Dosen dengan Tugas Tambahan',
                'hierarchy_level' => null
            ],

            // ===================================
            // TENAGA KEPENDIDIKAN FUNGSIONAL TERTENTU (Hierarki Level 1-3)
            // ===================================
            [
                'jabatan' => 'Arsiparis Ahli Pertama',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Tertentu',
                'hierarchy_level' => 1
            ],
            [
                'jabatan' => 'Arsiparis Ahli Muda',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Tertentu',
                'hierarchy_level' => 2
            ],
            [
                'jabatan' => 'Arsiparis Ahli Madya',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Tertentu',
                'hierarchy_level' => 3
            ],
            [
                'jabatan' => 'Pustakawan Ahli Pertama',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Tertentu',
                'hierarchy_level' => 1
            ],
            [
                'jabatan' => 'Pustakawan Ahli Muda',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Tertentu',
                'hierarchy_level' => 2
            ],
            [
                'jabatan' => 'Pustakawan Ahli Madya',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Tertentu',
                'hierarchy_level' => 3
            ],
            [
                'jabatan' => 'Pranata Laboratorium Pendidikan Ahli Pertama',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Tertentu',
                'hierarchy_level' => 1
            ],
            [
                'jabatan' => 'Pranata Laboratorium Pendidikan Ahli Muda',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Tertentu',
                'hierarchy_level' => 2
            ],
            [
                'jabatan' => 'Pranata Laboratorium Pendidikan Ahli Madya',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Tertentu',
                'hierarchy_level' => 3
            ],

            // ===================================
            // TENAGA KEPENDIDIKAN FUNGSIONAL UMUM (Non-Hierarki)
            // ===================================
            [
                'jabatan' => 'Staf Administrasi',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Umum',
                'hierarchy_level' => null
            ],
            [
                'jabatan' => 'Koordinator Administrasi',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Umum',
                'hierarchy_level' => null
            ],
            [
                'jabatan' => 'Staf Keuangan',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Umum',
                'hierarchy_level' => null
            ],
            [
                'jabatan' => 'Staf Kepegawaian',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Umum',
                'hierarchy_level' => null
            ],
            [
                'jabatan' => 'Staf Akademik',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Umum',
                'hierarchy_level' => null
            ],
            [
                'jabatan' => 'Staf Kemahasiswaan',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Umum',
                'hierarchy_level' => null
            ],
            [
                'jabatan' => 'Staf Umum',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Umum',
                'hierarchy_level' => null
            ],

            // ===================================
            // TENAGA KEPENDIDIKAN STRUKTURAL (Non-Hierarki, TIDAK DAPAT USULAN)
            // ===================================
            [
                'jabatan' => 'Kepala Sub Bagian',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Struktural',
                'hierarchy_level' => null
            ],
            [
                'jabatan' => 'Kepala Bagian',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Struktural',
                'hierarchy_level' => null
            ],
            [
                'jabatan' => 'Kepala Biro',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Struktural',
                'hierarchy_level' => null
            ],
            [
                'jabatan' => 'Kepala Sub Direktorat',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Struktural',
                'hierarchy_level' => null
            ],
            [
                'jabatan' => 'Kepala Direktorat',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Struktural',
                'hierarchy_level' => null
            ],

            // ===================================
            // TENAGA KEPENDIDIKAN TUGAS TAMBAHAN (Non-Hierarki)
            // ===================================
            [
                'jabatan' => 'Koordinator Program',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Tugas Tambahan',
                'hierarchy_level' => null
            ],
            [
                'jabatan' => 'Sekretaris Fakultas',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Tugas Tambahan',
                'hierarchy_level' => null
            ],
            [
                'jabatan' => 'Wakil Sekretaris Fakultas',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Tugas Tambahan',
                'hierarchy_level' => null
            ],
            [
                'jabatan' => 'Koordinator Bidang',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Tugas Tambahan',
                'hierarchy_level' => null
            ],
            [
                'jabatan' => 'Koordinator Unit',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Tugas Tambahan',
                'hierarchy_level' => null
            ],
        ];

        // Insert data dengan updateOrCreate untuk mencegah duplicate
        foreach ($jabatans as $jabatan) {
            Jabatan::updateOrCreate(
                [
                    'jabatan' => $jabatan['jabatan'],
                    'jenis_pegawai' => $jabatan['jenis_pegawai'],
                    'jenis_jabatan' => $jabatan['jenis_jabatan']
                ],
                $jabatan
            );
        }

        // Log hasil seeding
        $totalJabatan = Jabatan::count();
        $denganHierarki = Jabatan::whereNotNull('hierarchy_level')->count();
        $tanpaHierarki = Jabatan::whereNull('hierarchy_level')->count();
        $dapatUsulan = Jabatan::where('jenis_jabatan', '!=', 'Tenaga Kependidikan Struktural')->count();
        $tidakDapatUsulan = Jabatan::where('jenis_jabatan', 'Tenaga Kependidikan Struktural')->count();

        $this->command->info("✅ JabatanSeeder berhasil dijalankan!");
        $this->command->info("📊 Statistik Jabatan:");
        $this->command->info("   • Total Jabatan: {$totalJabatan}");
        $this->command->info("   • Dengan Hierarki: {$denganHierarki}");
        $this->command->info("   • Tanpa Hierarki: {$tanpaHierarki}");
        $this->command->info("   • Dapat Usulan: {$dapatUsulan}");
        $this->command->info("   • Tidak Dapat Usulan: {$tidakDapatUsulan}");

        // Tampilkan breakdown berdasarkan jenis pegawai
        $dosenCount = Jabatan::where('jenis_pegawai', 'Dosen')->count();
        $tenagaKependidikanCount = Jabatan::where('jenis_pegawai', 'Tenaga Kependidikan')->count();

        $this->command->info("👥 Breakdown Jenis Pegawai:");
        $this->command->info("   • Dosen: {$dosenCount}");
        $this->command->info("   • Tenaga Kependidikan: {$tenagaKependidikanCount}");

        // Tampilkan breakdown berdasarkan jenis jabatan
        $jenisJabatanStats = Jabatan::selectRaw('jenis_jabatan, COUNT(*) as total')
                                   ->groupBy('jenis_jabatan')
                                   ->orderBy('jenis_jabatan')
                                   ->get();

        $this->command->info("🏢 Breakdown Jenis Jabatan:");
        foreach ($jenisJabatanStats as $stat) {
            $this->command->info("   • {$stat->jenis_jabatan}: {$stat->total}");
        }
    }
}
