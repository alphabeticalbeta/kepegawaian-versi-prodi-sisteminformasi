<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\BackendUnivUsulan\Pegawai;
use App\Models\BackendUnivUsulan\Pangkat;
use App\Models\BackendUnivUsulan\Jabatan;
use App\Models\BackendUnivUsulan\SubSubUnitKerja;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('👤 Seeding data Pegawai...');

        // 1. Ambil data master yang relevan terlebih dahulu
        $pangkatPNS = Pangkat::where('status_pangkat', 'PNS')->first();
        $pangkatPPPK = Pangkat::where('status_pangkat', 'PPPK')->first();
        $pangkatNonASN = Pangkat::where('status_pangkat', 'Non-ASN')->first();

        $jabatanDosen = Jabatan::where('jenis_pegawai', 'Dosen')->first();
        $jabatanTenagaKependidikan = Jabatan::where('jenis_pegawai', 'Tenaga Kependidikan')->first();

        $subSubUnitKerja = SubSubUnitKerja::first();

        // 2. Lakukan pengecekan untuk memastikan data master ada
        if (!$pangkatPNS || !$jabatanDosen || !$subSubUnitKerja) {
            $this->command->error('❌ Seeder Pangkat/Jabatan/SubSubUnitKerja belum dijalankan atau tabel kosong.');
            $this->command->error('❌ PegawaiSeeder dibatalkan.');
            return;
        }

        $pegawais = [
            // ===================================
            // ADMIN & SUPER ADMIN
            // ===================================
            [
                'nip' => '199405242024061001',
                'nama_lengkap' => 'Muhammad Rivani Ibrahim',
                'email' => 'admin.fakultas@kepegawaian.com',
                'jenis_pegawai' => 'Dosen',
                'status_kepegawaian' => 'Dosen PNS',
                'is_admin' => true,
                'pangkat' => $pangkatPNS,
                'jabatan' => $jabatanDosen,
                'roles' => ['Admin Fakultas', 'Admin Universitas Usulan']
            ],
            [
                'nip' => '199001012015011001',
                'nama_lengkap' => 'Budi Santoso',
                'email' => 'budi.santoso@unmul.ac.id',
                'jenis_pegawai' => 'Dosen',
                'status_kepegawaian' => 'Dosen PNS',
                'is_admin' => false,
                'pangkat' => $pangkatPNS,
                'jabatan' => $jabatanDosen,
                'roles' => ['Pegawai Unmul']
            ],
            [
                'nip' => '199202022016022002',
                'nama_lengkap' => 'Citra Lestari',
                'email' => 'citra.lestari@unmul.ac.id',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'status_kepegawaian' => 'Tenaga Kependidikan PNS',
                'is_admin' => false,
                'pangkat' => $pangkatPNS,
                'jabatan' => $jabatanTenagaKependidikan,
                'roles' => ['Pegawai Unmul']
            ],
            [
                'nip' => '199503032017033003',
                'nama_lengkap' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@unmul.ac.id',
                'jenis_pegawai' => 'Dosen',
                'status_kepegawaian' => 'Dosen PNS',
                'is_admin' => false,
                'pangkat' => $pangkatPNS,
                'jabatan' => $jabatanDosen,
                'roles' => ['Pegawai Unmul']
            ],
            [
                'nip' => '199604042018044004',
                'nama_lengkap' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@unmul.ac.id',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'status_kepegawaian' => 'Tenaga Kependidikan PNS',
                'is_admin' => false,
                'pangkat' => $pangkatPNS,
                'jabatan' => $jabatanTenagaKependidikan,
                'roles' => ['Pegawai Unmul']
            ],
            [
                'nip' => '199705052019055005',
                'nama_lengkap' => 'Rizki Pratama',
                'email' => 'rizki.pratama@unmul.ac.id',
                'jenis_pegawai' => 'Dosen',
                'status_kepegawaian' => 'Dosen PPPK',
                'is_admin' => false,
                'pangkat' => $pangkatPPPK,
                'jabatan' => $jabatanDosen,
                'roles' => ['Pegawai Unmul']
            ],
            [
                'nip' => '199806062020066006',
                'nama_lengkap' => 'Dewi Sartika',
                'email' => 'dewi.sartika@unmul.ac.id',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'status_kepegawaian' => 'Tenaga Kependidikan PPPK',
                'is_admin' => false,
                'pangkat' => $pangkatPPPK,
                'jabatan' => $jabatanTenagaKependidikan,
                'roles' => ['Pegawai Unmul']
            ],
            [
                'nip' => '199907072021077007',
                'nama_lengkap' => 'Hendra Wijaya',
                'email' => 'hendra.wijaya@unmul.ac.id',
                'jenis_pegawai' => 'Dosen',
                'status_kepegawaian' => 'Dosen Non ASN',
                'is_admin' => false,
                'pangkat' => $pangkatNonASN,
                'jabatan' => $jabatanDosen,
                'roles' => ['Pegawai Unmul']
            ],
            [
                'nip' => '200008082022088008',
                'nama_lengkap' => 'Maya Indah',
                'email' => 'maya.indah@unmul.ac.id',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'status_kepegawaian' => 'Tenaga Kependidikan Non ASN',
                'is_admin' => false,
                'pangkat' => $pangkatNonASN,
                'jabatan' => $jabatanTenagaKependidikan,
                'roles' => ['Pegawai Unmul']
            ],
            [
                'nip' => '200109092023099009',
                'nama_lengkap' => 'Doni Kusuma',
                'email' => 'doni.kusuma@unmul.ac.id',
                'jenis_pegawai' => 'Dosen',
                'status_kepegawaian' => 'Dosen PNS',
                'is_admin' => false,
                'pangkat' => $pangkatPNS,
                'jabatan' => $jabatanDosen,
                'roles' => ['Pegawai Unmul']
            ],
            // ===================================
            // PENILAI UNIVERSITAS
            // ===================================
            [
                'nip' => '198505152010011001',
                'nama_lengkap' => 'Prof. Dr. Bambang Setiawan',
                'email' => 'bambang.setiawan@unmul.ac.id',
                'jenis_pegawai' => 'Dosen',
                'status_kepegawaian' => 'Dosen PNS',
                'is_admin' => false,
                'pangkat' => $pangkatPNS,
                'jabatan' => $jabatanDosen,
                'roles' => ['Penilai Universitas']
            ],
            [
                'nip' => '198606162011022002',
                'nama_lengkap' => 'Prof. Dr. Sri Wahyuni',
                'email' => 'sri.wahyuni@unmul.ac.id',
                'jenis_pegawai' => 'Dosen',
                'status_kepegawaian' => 'Dosen PNS',
                'is_admin' => false,
                'pangkat' => $pangkatPNS,
                'jabatan' => $jabatanDosen,
                'roles' => ['Penilai Universitas']
            ],
            // ===================================
            // TIM SENAT
            // ===================================
            [
                'nip' => '197707172008011003',
                'nama_lengkap' => 'Prof. Dr. Agus Setiawan',
                'email' => 'agus.setiawan@unmul.ac.id',
                'jenis_pegawai' => 'Dosen',
                'status_kepegawaian' => 'Dosen PNS',
                'is_admin' => false,
                'pangkat' => $pangkatPNS,
                'jabatan' => $jabatanDosen,
                'roles' => ['Tim Senat']
            ],
            [
                'nip' => '197808182009022004',
                'nama_lengkap' => 'Prof. Dr. Endang Sulistyowati',
                'email' => 'endang.sulistyowati@unmul.ac.id',
                'jenis_pegawai' => 'Dosen',
                'status_kepegawaian' => 'Dosen PNS',
                'is_admin' => false,
                'pangkat' => $pangkatPNS,
                'jabatan' => $jabatanDosen,
                'roles' => ['Tim Senat']
            ],
            // ===================================
            // ADMIN KEUANGAN
            // ===================================
            [
                'nip' => '198909192012033005',
                'nama_lengkap' => 'Sri Mulyani',
                'email' => 'sri.mulyani@unmul.ac.id',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'status_kepegawaian' => 'Tenaga Kependidikan PNS',
                'is_admin' => false,
                'pangkat' => $pangkatPNS,
                'jabatan' => $jabatanTenagaKependidikan,
                'roles' => ['Admin Keuangan']
            ],
        ];

        foreach ($pegawais as $pegawaiData) {
            // Data dasar yang sama untuk semua pegawai
            $baseData = [
                'pangkat_terakhir_id' => $pegawaiData['pangkat']->id,
                'jabatan_terakhir_id' => $pegawaiData['jabatan']->id,
                'unit_kerja_terakhir_id' => $subSubUnitKerja->id,
                'gelar_depan' => null,
                'gelar_belakang' => 'S.Kom., M.Kom.',
                'nomor_kartu_pegawai' => '1234567890123456',
                'tempat_lahir' => 'Samarinda',
                'tanggal_lahir' => '1990-01-01',
                'jenis_kelamin' => 'Laki-Laki',
                'nomor_handphone' => '081234567890',
                'tmt_cpns' => '2020-01-01',
                'tmt_pns' => '2021-01-01',
                'tmt_pangkat' => '2022-01-01',
                'tmt_jabatan' => '2023-01-01',
                'pendidikan_terakhir' => 'Magister (S2)',
                'predikat_kinerja_tahun_pertama' => 'Baik',
                'predikat_kinerja_tahun_kedua' => 'Sangat Baik',
                'nuptk' => '1234567890123456',
                'nilai_konversi' => 85.5,
                'password' => Hash::make($pegawaiData['nip']), // Password = NIP
                'username' => $pegawaiData['nip'], // Username = NIP
            ];

            // Data khusus untuk Dosen
            if ($pegawaiData['jenis_pegawai'] === 'Dosen') {
                $baseData = array_merge($baseData, [
                    'mata_kuliah_diampu' => 'Pemrograman Web, Basis Data, Algoritma',
                    'ranting_ilmu_kepakaran' => 'Teknologi Informasi',
                    'url_profil_sinta' => 'https://sinta.kemdikbud.go.id/authors/profile/123456',
                ]);
            }

            // Path dummy untuk dokumen (sesuai dengan controller)
            $dummyPaths = [
                'foto' => 'pegawai-files/foto/dummy-avatar.svg',
                'sk_cpns' => 'pegawai-files/sk_cpns/dummy-sk-cpns.pdf',
                'sk_pns' => 'pegawai-files/sk_pns/dummy-sk-pns.pdf',
                'sk_pangkat_terakhir' => 'pegawai-files/sk_pangkat_terakhir/dummy-sk-pangkat.pdf',
                'sk_jabatan_terakhir' => 'pegawai-files/sk_jabatan_terakhir/dummy-sk-jabatan.pdf',
                'ijazah_terakhir' => 'pegawai-files/ijazah_terakhir/dummy-ijazah.pdf',
                'transkrip_nilai_terakhir' => 'pegawai-files/transkrip_nilai_terakhir/dummy-transkrip.pdf',
                'skp_tahun_pertama' => 'pegawai-files/skp_tahun_pertama/dummy-skp-2023.pdf',
                'skp_tahun_kedua' => 'pegawai-files/skp_tahun_kedua/dummy-skp-2024.pdf',
                'pak_konversi' => 'pegawai-files/pak_konversi/dummy-pak.pdf',
                'sk_penyetaraan_ijazah' => null,
                'disertasi_thesis_terakhir' => null,
            ];

            $baseData = array_merge($baseData, $dummyPaths);

            // Simpan roles sebelum array_merge
            $roles = $pegawaiData['roles'];

            // Gabungkan data dasar dengan data spesifik pegawai
            $pegawaiData = array_merge($baseData, [
                'nip' => $pegawaiData['nip'],
                'nama_lengkap' => $pegawaiData['nama_lengkap'],
                'email' => $pegawaiData['email'],
                'jenis_pegawai' => $pegawaiData['jenis_pegawai'],
                'status_kepegawaian' => $pegawaiData['status_kepegawaian'],
            ]);

            // Simpan pegawai
            $pegawai = Pegawai::updateOrCreate(
                ['nip' => $pegawaiData['nip']],
                $pegawaiData
            );

            // Assign roles
            foreach ($roles as $role) {
                $pegawai->assignRole($role);
            }

            // Set unit_kerja_id untuk admin fakultas
            if (in_array('Admin Fakultas', $roles)) {
                $pegawai->update(['unit_kerja_id' => $subSubUnitKerja->subUnitKerja->unit_kerja_id]);
            }
        }

        // Log hasil seeding
        $totalPegawai = Pegawai::count();
        $dosenCount = Pegawai::where('jenis_pegawai', 'Dosen')->count();
        $tenagaKependidikanCount = Pegawai::where('jenis_pegawai', 'Tenaga Kependidikan')->count();
        $adminCount = Pegawai::whereHas('roles', function($q) {
            $q->whereIn('name', ['Admin Fakultas', 'Admin Universitas Usulan']);
        })->count();

        $this->command->info("✅ PegawaiSeeder berhasil dijalankan!");
        $this->command->info("📊 Statistik Pegawai:");
        $this->command->info("   • Total Pegawai: {$totalPegawai}");
        $this->command->info("   • Dosen: {$dosenCount}");
        $this->command->info("   • Tenaga Kependidikan: {$tenagaKependidikanCount}");
        $this->command->info("   • Admin: {$adminCount}");

        // Tampilkan breakdown berdasarkan status kepegawaian
        $statusStats = Pegawai::selectRaw('status_kepegawaian, COUNT(*) as total')
                             ->groupBy('status_kepegawaian')
                             ->orderBy('status_kepegawaian')
                             ->get();

        $this->command->info("👥 Breakdown Status Kepegawaian:");
        foreach ($statusStats as $stat) {
            $this->command->info("   • {$stat->status_kepegawaian}: {$stat->total}");
        }

        // Tampilkan breakdown berdasarkan roles
        $roleStats = \Spatie\Permission\Models\Role::where('guard_name', 'pegawai')
                                                   ->withCount('users')
                                                   ->get();

        $this->command->info("🎭 Breakdown Roles:");
        foreach ($roleStats as $role) {
            $this->command->info("   • {$role->name}: {$role->users_count}");
        }

        $this->command->info("🔑 Login Credentials:");
        $this->command->info("   • Admin: NIP 199405242024061001, Password: 199405242024061001");
        $this->command->info("   • Pegawai: NIP sesuai data, Password: NIP");
        $this->command->info("   • Semua user menggunakan NIP sebagai username dan password");
    }
}
