<?php

/**
 * Script untuk menambahkan role baru: Admin Keuangan dan Tim Senat
 * Jalankan script ini jika php artisan db:seed tidak berfungsi
 */

// Bootstrap Laravel
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

try {
    echo "🚀 Memulai setup role baru...\n\n";

    // 1. Tambahkan role baru
    $newRoles = [
        'Admin Keuangan',
        'Tim Senat'
    ];

    echo "📝 Menambahkan role baru:\n";
    foreach ($newRoles as $roleName) {
        $role = Role::firstOrCreate(
            ['name' => $roleName, 'guard_name' => 'pegawai']
        );
        echo "   ✅ Role '{$roleName}' berhasil dibuat/ditemukan\n";
    }

    // 2. Tambahkan permission baru
    $newPermissions = [
        'view_financial_documents',  // Admin Keuangan - akses dokumen keuangan
        'view_senate_documents',     // Tim Senat - akses dokumen senat
    ];

    echo "\n🔐 Menambahkan permission baru:\n";
    foreach ($newPermissions as $permission) {
        $perm = Permission::firstOrCreate([
            'name' => $permission,
            'guard_name' => 'pegawai'
        ]);
        echo "   ✅ Permission '{$permission}' berhasil dibuat/ditemukan\n";
    }

    // 3. Assign permissions ke roles
    echo "\n🔗 Menghubungkan permission dengan role:\n";

    $adminKeuangan = Role::where('name', 'Admin Keuangan')->first();
    if ($adminKeuangan) {
        $adminKeuangan->givePermissionTo('view_financial_documents');
        echo "   ✅ Admin Keuangan → view_financial_documents\n";
    }

    $timSenat = Role::where('name', 'Tim Senat')->first();
    if ($timSenat) {
        $timSenat->givePermissionTo('view_senate_documents');
        echo "   ✅ Tim Senat → view_senate_documents\n";
    }

    // 4. Tampilkan semua role yang ada
    echo "\n📋 Daftar semua role yang tersedia:\n";
    $allRoles = Role::where('guard_name', 'pegawai')->get();
    foreach ($allRoles as $role) {
        $permissions = $role->permissions->pluck('name')->toArray();
        echo "   • {$role->name}\n";
        if (!empty($permissions)) {
            echo "     Permissions: " . implode(', ', $permissions) . "\n";
        }
    }

    echo "\n🎉 Setup role baru berhasil selesai!\n";
    echo "💡 Role 'Admin Keuangan' dan 'Tim Senat' sekarang tersedia di sistem.\n";
    echo "💡 Anda dapat mengassign role ini ke pegawai melalui halaman Role Pegawai.\n";

} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";

    // Tampilkan stack trace untuk debugging
    echo "\n🔍 Stack Trace:\n";
    echo $e->getTraceAsString() . "\n";
}
