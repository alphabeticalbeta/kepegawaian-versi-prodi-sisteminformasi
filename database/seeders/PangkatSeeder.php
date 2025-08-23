<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KepegawaianUniversitas\Pangkat;

class PangkatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📊 Seeding data Pangkat...');

        $pangkats = [
            // ===================================
            // PANGKAT PNS GOLONGAN I (Level 1-4)
            // ===================================
            [
                'pangkat' => 'Juru Muda (I/a)',
                'status_pangkat' => 'PNS',
                'hierarchy_level' => 1
            ],
            [
                'pangkat' => 'Juru Muda Tingkat I (I/b)',
                'status_pangkat' => 'PNS',
                'hierarchy_level' => 2
            ],
            [
                'pangkat' => 'Juru (I/c)',
                'status_pangkat' => 'PNS',
                'hierarchy_level' => 3
            ],
            [
                'pangkat' => 'Juru Tingkat I (I/d)',
                'status_pangkat' => 'PNS',
                'hierarchy_level' => 4
            ],

            // ===================================
            // PANGKAT PNS GOLONGAN II (Level 5-8)
            // ===================================
            [
                'pangkat' => 'Pengatur Muda (II/a)',
                'status_pangkat' => 'PNS',
                'hierarchy_level' => 5
            ],
            [
                'pangkat' => 'Pengatur Muda Tingkat I (II/b)',
                'status_pangkat' => 'PNS',
                'hierarchy_level' => 6
            ],
            [
                'pangkat' => 'Pengatur (II/c)',
                'status_pangkat' => 'PNS',
                'hierarchy_level' => 7
            ],
            [
                'pangkat' => 'Pengatur Tingkat I (II/d)',
                'status_pangkat' => 'PNS',
                'hierarchy_level' => 8
            ],

            // ===================================
            // PANGKAT PNS GOLONGAN III (Level 9-12)
            // ===================================
            [
                'pangkat' => 'Penata Muda (III/a)',
                'status_pangkat' => 'PNS',
                'hierarchy_level' => 9
            ],
            [
                'pangkat' => 'Penata Muda Tingkat I (III/b)',
                'status_pangkat' => 'PNS',
                'hierarchy_level' => 10
            ],
            [
                'pangkat' => 'Penata (III/c)',
                'status_pangkat' => 'PNS',
                'hierarchy_level' => 11
            ],
            [
                'pangkat' => 'Penata Tingkat I (III/d)',
                'status_pangkat' => 'PNS',
                'hierarchy_level' => 12
            ],

            // ===================================
            // PANGKAT PNS GOLONGAN IV (Level 13-17)
            // ===================================
            [
                'pangkat' => 'Pembina (IV/a)',
                'status_pangkat' => 'PNS',
                'hierarchy_level' => 13
            ],
            [
                'pangkat' => 'Pembina Tingkat I (IV/b)',
                'status_pangkat' => 'PNS',
                'hierarchy_level' => 14
            ],
            [
                'pangkat' => 'Pembina Utama Muda (IV/c)',
                'status_pangkat' => 'PNS',
                'hierarchy_level' => 15
            ],
            [
                'pangkat' => 'Pembina Utama Madya (IV/d)',
                'status_pangkat' => 'PNS',
                'hierarchy_level' => 16
            ],
            [
                'pangkat' => 'Pembina Utama (IV/e)',
                'status_pangkat' => 'PNS',
                'hierarchy_level' => 17
            ],

            // ===================================
            // PANGKAT PPPK (Level 18-21)
            // ===================================
            [
                'pangkat' => 'PPPK Ahli Pertama (III/a)',
                'status_pangkat' => 'PPPK',
                'hierarchy_level' => 18
            ],
            [
                'pangkat' => 'PPPK Ahli Muda (III/b)',
                'status_pangkat' => 'PPPK',
                'hierarchy_level' => 19
            ],
            [
                'pangkat' => 'PPPK Ahli Madya (III/c)',
                'status_pangkat' => 'PPPK',
                'hierarchy_level' => 20
            ],
            [
                'pangkat' => 'PPPK Ahli Utama (III/d)',
                'status_pangkat' => 'PPPK',
                'hierarchy_level' => 21
            ],

            // ===================================
            // PANGKAT NON-PNS (Tanpa Hierarchy)
            // ===================================
            [
                'pangkat' => 'Non-PNS',
                'status_pangkat' => 'Non-ASN',
                'hierarchy_level' => null
            ],
            [
                'pangkat' => 'Kontrak',
                'status_pangkat' => 'Non-ASN',
                'hierarchy_level' => null
            ],
            [
                'pangkat' => 'Honorer',
                'status_pangkat' => 'Non-ASN',
                'hierarchy_level' => null
            ],
        ];

        foreach ($pangkats as $pangkat) {
            Pangkat::updateOrCreate(
                ['pangkat' => $pangkat['pangkat']],
                $pangkat
            );
        }

        // Log hasil seeding
        $totalPangkat = Pangkat::count();
        $pnsCount = Pangkat::where('status_pangkat', 'PNS')->count();
        $pppkCount = Pangkat::where('status_pangkat', 'PPPK')->count();
        $nonAsnCount = Pangkat::where('status_pangkat', 'Non-ASN')->count();
        $denganHierarki = Pangkat::whereNotNull('hierarchy_level')->count();
        $tanpaHierarki = Pangkat::whereNull('hierarchy_level')->count();

        $this->command->info("✅ PangkatSeeder berhasil dijalankan!");
        $this->command->info("📊 Statistik Pangkat:");
        $this->command->info("   • Total Pangkat: {$totalPangkat}");
        $this->command->info("   • PNS: {$pnsCount}");
        $this->command->info("   • PPPK: {$pppkCount}");
        $this->command->info("   • Non-ASN: {$nonAsnCount}");
        $this->command->info("   • Dengan Hierarki: {$denganHierarki}");
        $this->command->info("   • Tanpa Hierarki: {$tanpaHierarki}");
    }
}
