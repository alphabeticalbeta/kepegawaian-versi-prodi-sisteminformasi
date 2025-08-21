<?php

/**
 * Script untuk mengecek dan menambahkan role baru
 */

// Bootstrap Laravel
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

try {
    echo "🔍 Mengecek role yang ada di database...\n\n";

    // Cek role yang ada
    $existingRoles = Role::where('guard_name', 'pegawai')->get();

    echo "📋 Role yang tersedia:\n";
    foreach ($existingRoles as $role) {
        echo "   • {$role->name}\n";
    }

    // Cek apakah role baru sudah ada
    $newRoles = ['Admin Keuangan', 'Tim Senat'];
    $missingRoles = [];

    foreach ($newRoles as $roleName) {
        $role = Role::where('name', $roleName)->where('guard_name', 'pegawai')->first();
        if (!$role) {
            $missingRoles[] = $roleName;
        }
    }

    if (empty($missingRoles)) {
        echo "\n✅ Semua role baru sudah ada di database!\n";
    } else {
        echo "\n⚠️ Role yang belum ada: " . implode(', ', $missingRoles) . "\n";

        echo "\n🔧 Menambahkan role yang hilang...\n";
        foreach ($missingRoles as $roleName) {
            $role = Role::create([
                'name' => $roleName,
                'guard_name' => 'pegawai'
            ]);
            echo "   ✅ Role '$roleName' berhasil ditambahkan\n";
        }

        // Tambahkan permissions
        $permissions = [
            'view_financial_documents' => 'Admin Keuangan',
            'view_senate_documents' => 'Tim Senat'
        ];

        foreach ($permissions as $permissionName => $roleName) {
            $permission = Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'pegawai'
            ]);

            $role = Role::where('name', $roleName)->where('guard_name', 'pegawai')->first();
            if ($role) {
                $role->givePermissionTo($permission);
                echo "   ✅ Permission '$permissionName' di-assign ke '$roleName'\n";
            }
        }
    }

    // Tampilkan semua role setelah update
    echo "\n📋 Daftar lengkap role setelah update:\n";
    $allRoles = Role::where('guard_name', 'pegawai')->orderBy('name')->get();
    foreach ($allRoles as $role) {
        $permissions = $role->permissions->pluck('name')->toArray();
        echo "   • {$role->name}\n";
        if (!empty($permissions)) {
            echo "     Permissions: " . implode(', ', $permissions) . "\n";
        }
    }

    echo "\n🎉 Pengecekan dan setup role selesai!\n";
    echo "💡 Role baru sekarang tersedia di halaman Edit Master Role Pegawai\n";

} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";

    // Tampilkan stack trace untuk debugging
    echo "\n🔍 Stack Trace:\n";
    echo $e->getTraceAsString() . "\n";
}
