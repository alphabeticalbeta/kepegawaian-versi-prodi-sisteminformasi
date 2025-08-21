<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🚀 Memulai proses seeding database...');
        $this->command->info('');

        // Seeding dalam urutan yang benar untuk menghindari foreign key constraint errors
        $this->call([
            RoleSeeder::class,           // 1. Roles & Permissions (harus pertama)
            UnitKerjaSeeder::class,      // 2. Unit Kerja (master data)
            PangkatSeeder::class,        // 3. Pangkat (master data)
            JabatanSeeder::class,        // 4. Jabatan (master data)
            PegawaiSeeder::class,        // 5. Pegawai (tergantung pada master data)
        ]);

        $this->command->info('');
        $this->command->info('✅ Semua seeder berhasil dijalankan!');
        $this->command->info('');
        $this->command->info('🔑 Login Credentials:');
        $this->command->info('   • Admin: NIP 199405242024061001, Password: 199405242024061001');
        $this->command->info('   • Pegawai: NIP sesuai data, Password: NIP');
        $this->command->info('   • Semua user menggunakan NIP sebagai username dan password');
        $this->command->info('');
        $this->command->info('📊 Database siap digunakan!');
    }
}
