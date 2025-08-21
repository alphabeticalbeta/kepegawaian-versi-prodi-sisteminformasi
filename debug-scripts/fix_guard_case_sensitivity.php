<?php

/**
 * Script untuk memperbaiki case sensitivity guard_name di database
 * Role baru menggunakan 'Pegawai' (huruf besar) sedangkan yang lama 'pegawai' (huruf kecil)
 */

// Bootstrap Laravel
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

try {
    echo "🔍 Mengecek guard_name di database...\n\n";

    // Cek semua role dan guard_name mereka
    $roles = DB::table('roles')->get();

    echo "📋 Role yang ada di database:\n";
    foreach ($roles as $role) {
        echo "   • ID: {$role->id}, Name: {$role->name}, Guard: {$role->guard_name}\n";
    }

    // Cari role dengan guard_name 'Pegawai' (huruf besar)
    $incorrectGuardRoles = DB::table('roles')
        ->where('guard_name', 'Pegawai')
        ->get();

    if ($incorrectGuardRoles->count() > 0) {
        echo "\n⚠️ Ditemukan role dengan guard_name 'Pegawai' (huruf besar):\n";
        foreach ($incorrectGuardRoles as $role) {
            echo "   • {$role->name} (ID: {$role->id})\n";
        }

        echo "\n🔧 Memperbaiki guard_name menjadi 'pegawai' (huruf kecil)...\n";

        // Update guard_name dari 'Pegawai' ke 'pegawai'
        $updated = DB::table('roles')
            ->where('guard_name', 'Pegawai')
            ->update(['guard_name' => 'pegawai']);

        echo "✅ Berhasil mengupdate {$updated} role\n";

        // Cek juga permissions
        $incorrectGuardPermissions = DB::table('permissions')
            ->where('guard_name', 'Pegawai')
            ->get();

        if ($incorrectGuardPermissions->count() > 0) {
            echo "\n⚠️ Ditemukan permissions dengan guard_name 'Pegawai' (huruf besar):\n";
            foreach ($incorrectGuardPermissions as $permission) {
                echo "   • {$permission->name} (ID: {$permission->id})\n";
            }

            echo "\n🔧 Memperbaiki guard_name permissions...\n";

            $updatedPermissions = DB::table('permissions')
                ->where('guard_name', 'Pegawai')
                ->update(['guard_name' => 'pegawai']);

            echo "✅ Berhasil mengupdate {$updatedPermissions} permissions\n";
        }

    } else {
        echo "\n✅ Semua role sudah menggunakan guard_name 'pegawai' (huruf kecil)\n";
    }

    // Tampilkan hasil akhir
    echo "\n📋 Status akhir role di database:\n";
    $finalRoles = DB::table('roles')->orderBy('name')->get();
    foreach ($finalRoles as $role) {
        echo "   • {$role->name} (Guard: {$role->guard_name})\n";
    }

    // Test query dengan guard yang benar
    echo "\n🧪 Testing query dengan guard 'pegawai':\n";
    $testRoles = Role::where('guard_name', 'pegawai')->get();
    echo "   Ditemukan {$testRoles->count()} role dengan guard 'pegawai':\n";
    foreach ($testRoles as $role) {
        echo "   • {$role->name}\n";
    }

    echo "\n🎉 Perbaikan guard case sensitivity selesai!\n";
    echo "💡 Role baru sekarang akan muncul di halaman Edit Role Pegawai\n";

} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";

    // Tampilkan stack trace untuk debugging
    echo "\n🔍 Stack Trace:\n";
    echo $e->getTraceAsString() . "\n";
}
