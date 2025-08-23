<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KepegawaianUniversitas\Penilai;

class PenilaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $penilais = [
            [
                'nama_lengkap' => 'Dr. Ahmad Hidayat, M.Si.',
                'nip' => '198501012010011001',
                'email' => 'ahmad.hidayat@unmul.ac.id',
                'bidang_keahlian' => 'Manajemen, Ekonomi, Administrasi Publik',
                'status' => 'aktif'
            ],
            [
                'nama_lengkap' => 'Prof. Dr. Siti Nurhaliza, M.Pd.',
                'nip' => '197503152005012001',
                'email' => 'siti.nurhaliza@unmul.ac.id',
                'bidang_keahlian' => 'Pendidikan, Psikologi, Pengembangan SDM',
                'status' => 'aktif'
            ],
            [
                'nama_lengkap' => 'Dr. Bambang Sutrisno, S.E., M.M.',
                'nip' => '198012102008011001',
                'email' => 'bambang.sutrisno@unmul.ac.id',
                'bidang_keahlian' => 'Manajemen, Bisnis, Keuangan',
                'status' => 'aktif'
            ],
            [
                'nama_lengkap' => 'Dr. Rina Marlina, S.Pd., M.Ed.',
                'nip' => '198604152010012001',
                'email' => 'rina.marlina@unmul.ac.id',
                'bidang_keahlian' => 'Pendidikan, Kurikulum, Evaluasi Pembelajaran',
                'status' => 'aktif'
            ],
            [
                'nama_lengkap' => 'Prof. Dr. Muhammad Rizki, S.T., M.T.',
                'nip' => '197208102003121001',
                'email' => 'muhammad.rizki@unmul.ac.id',
                'bidang_keahlian' => 'Teknik, Teknologi, Inovasi',
                'status' => 'aktif'
            ],
            [
                'nama_lengkap' => 'Dr. Fatimah Azzahra, S.Psi., M.Psi.',
                'nip' => '198709202012012001',
                'email' => 'fatimah.azzahra@unmul.ac.id',
                'bidang_keahlian' => 'Psikologi, Konseling, Pengembangan Karir',
                'status' => 'aktif'
            ],
            [
                'nama_lengkap' => 'Dr. Hendra Wijaya, S.H., M.H.',
                'nip' => '198303152009011001',
                'email' => 'hendra.wijaya@unmul.ac.id',
                'bidang_keahlian' => 'Hukum, Administrasi Negara, Kebijakan Publik',
                'status' => 'aktif'
            ],
            [
                'nama_lengkap' => 'Dr. Nurul Hidayah, S.Pd., M.Ed.',
                'nip' => '198511102011012001',
                'email' => 'nurul.hidayah@unmul.ac.id',
                'bidang_keahlian' => 'Pendidikan, Teknologi Pembelajaran, E-Learning',
                'status' => 'aktif'
            ]
        ];

        foreach ($penilais as $penilai) {
            Penilai::create($penilai);
        }
    }
}
