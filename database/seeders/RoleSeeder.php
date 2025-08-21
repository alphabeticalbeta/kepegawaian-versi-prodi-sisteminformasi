<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('👥 Seeding data Roles & Permissions...');

        // Daftar peran yang akan dibuat di dalam sistem
        $roles = [
            'Admin Universitas Usulan',
            'Admin Universitas',
            'Admin Fakultas',
            'Admin Keuangan',
            'Tim Senat',
            'Penilai Universitas',
            'Pegawai Unmul' // Menggunakan nama peran yang lebih spesifik
        ];

        // Lakukan perulangan untuk setiap nama peran
        foreach ($roles as $roleName) {
            // Buat peran HANYA JIKA belum ada.
            // [INI YANG DIPERBAIKI] guard_name harus 'pegawai' agar sesuai dengan config/auth.php
            Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'pegawai']
            );
        }

        // ========== TAMBAHAN: PERMISSIONS UNTUK DOKUMEN ==========

        // Daftar permissions untuk akses dokumen
        $permissions = [
            'view_all_pegawai_documents',        // Admin Univ Usulan - akses semua dokumen
            'view_fakultas_pegawai_documents',   // Admin Fakultas - akses dokumen pegawai di fakultasnya
            'view_own_documents',                // Pegawai - akses dokumen sendiri
            'view_assessment_documents',         // Penilai - akses dokumen yang sedang dinilai
            'view_financial_documents',          // Admin Keuangan - akses dokumen keuangan
            'view_senate_documents',             // Tim Senat - akses dokumen senat
            'manage_pegawai',                    // Admin - kelola data pegawai
            'manage_jabatan',                    // Admin - kelola data jabatan
            'manage_pangkat',                    // Admin - kelola data pangkat
            'manage_unit_kerja',                 // Admin - kelola data unit kerja
            'manage_roles',                      // Admin - kelola roles dan permissions
            'view_usulan',                       // View usulan
            'create_usulan',                     // Create usulan
            'edit_usulan',                       // Edit usulan
            'delete_usulan',                     // Delete usulan
            'approve_usulan',                    // Approve usulan
            'reject_usulan',                     // Reject usulan
        ];

        // Buat permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'pegawai'
            ]);
        }

        // Assign permissions ke roles
        $adminUnivUsulan = Role::where('name', 'Admin Universitas Usulan')->first();
        if ($adminUnivUsulan) {
            $adminUnivUsulan->givePermissionTo([
                'view_all_pegawai_documents',
                'manage_pegawai',
                'manage_jabatan',
                'manage_pangkat',
                'manage_unit_kerja',
                'manage_roles',
                'view_usulan',
                'create_usulan',
                'edit_usulan',
                'delete_usulan',
                'approve_usulan',
                'reject_usulan'
            ]);
        }

        $adminFakultas = Role::where('name', 'Admin Fakultas')->first();
        if ($adminFakultas) {
            $adminFakultas->givePermissionTo([
                'view_fakultas_pegawai_documents',
                'view_usulan',
                'create_usulan',
                'edit_usulan',
                'approve_usulan',
                'reject_usulan'
            ]);
        }

        $pegawaiUnmul = Role::where('name', 'Pegawai Unmul')->first();
        if ($pegawaiUnmul) {
            $pegawaiUnmul->givePermissionTo([
                'view_own_documents',
                'view_usulan',
                'create_usulan',
                'edit_usulan'
            ]);
        }

        $penilaiUniversitas = Role::where('name', 'Penilai Universitas')->first();
        if ($penilaiUniversitas) {
            $penilaiUniversitas->givePermissionTo([
                'view_assessment_documents',
                'view_usulan',
                'approve_usulan',
                'reject_usulan'
            ]);
        }

        $adminKeuangan = Role::where('name', 'Admin Keuangan')->first();
        if ($adminKeuangan) {
            $adminKeuangan->givePermissionTo([
                'view_financial_documents',
                'view_usulan'
            ]);
        }

        $timSenat = Role::where('name', 'Tim Senat')->first();
        if ($timSenat) {
            $timSenat->givePermissionTo([
                'view_senate_documents',
                'view_usulan',
                'approve_usulan',
                'reject_usulan'
            ]);
        }

        // Log hasil seeding
        $totalRoles = Role::where('guard_name', 'pegawai')->count();
        $totalPermissions = Permission::where('guard_name', 'pegawai')->count();

        $this->command->info("✅ RoleSeeder berhasil dijalankan!");
        $this->command->info("📊 Statistik Roles & Permissions:");
        $this->command->info("   • Total Roles: {$totalRoles}");
        $this->command->info("   • Total Permissions: {$totalPermissions}");

        // Tampilkan daftar roles yang dibuat
        $this->command->info("👥 Roles yang dibuat:");
        foreach ($roles as $role) {
            $this->command->info("   • {$role}");
        }
    }
}
