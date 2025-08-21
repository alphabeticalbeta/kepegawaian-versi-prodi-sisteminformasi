<?php

/**
 * Script untuk debug dan perbaiki masalah role assignment
 */

// Bootstrap Laravel
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\BackendUnivUsulan\Pegawai;

try {
    echo "🔍 Debug Role Assignment Issue...\n\n";

    // 1. Cek semua role di database
    echo "📋 Role yang ada di database:\n";
    $roles = DB::table('roles')->get();
    foreach ($roles as $role) {
        echo "   • ID: {$role->id}, Name: '{$role->name}', Guard: '{$role->guard_name}'\n";
    }

    // 2. Cek role dengan guard 'pegawai'
    echo "\n🔍 Role dengan guard 'pegawai':\n";
    $pegawaiRoles = Role::where('guard_name', 'pegawai')->get();
    foreach ($pegawaiRoles as $role) {
        echo "   • {$role->name}\n";
    }

    // 3. Cek permissions
    echo "\n🔐 Permissions dengan guard 'pegawai':\n";
    $pegawaiPermissions = Permission::where('guard_name', 'pegawai')->get();
    foreach ($pegawaiPermissions as $permission) {
        echo "   • {$permission->name}\n";
    }

    // 4. Test role assignment
    echo "\n🧪 Testing role operations...\n";

    // Ambil pegawai pertama untuk test
    $testPegawai = Pegawai::first();
    if ($testPegawai) {
        echo "   Testing dengan pegawai: {$testPegawai->nama_lengkap}\n";

        // Cek role yang dimiliki
        $currentRoles = $testPegawai->roles;
        echo "   Role saat ini:\n";
        foreach ($currentRoles as $role) {
            echo "     • {$role->name} (Guard: {$role->guard_name})\n";
        }

        // Test hasRole untuk setiap role
        echo "\n   Test hasRole() untuk setiap role:\n";
        foreach ($pegawaiRoles as $role) {
            $hasRole = $testPegawai->hasRole($role->name, 'pegawai');
            $status = $hasRole ? '✅' : '❌';
            echo "     {$status} hasRole('{$role->name}', 'pegawai'): " . ($hasRole ? 'true' : 'false') . "\n";
        }

    } else {
        echo "   ⚠️ Tidak ada pegawai untuk testing\n";
    }

    // 5. Cek role-permission assignments
    echo "\n🔗 Role-Permission Assignments:\n";
    $rolePermissions = DB::table('role_has_permissions')
        ->join('roles', 'role_has_permissions.role_id', '=', 'roles.id')
        ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
        ->select('roles.name as role_name', 'permissions.name as permission_name')
        ->get();

    foreach ($rolePermissions as $rp) {
        echo "   • {$rp->role_name} → {$rp->permission_name}\n";
    }

    // 6. Verifikasi guard consistency
    echo "\n🔍 Guard Consistency Check:\n";

    $inconsistentRoles = DB::table('roles')
        ->where('guard_name', '!=', 'pegawai')
        ->get();

    if ($inconsistentRoles->count() > 0) {
        echo "   ⚠️ Role dengan guard inconsistent:\n";
        foreach ($inconsistentRoles as $role) {
            echo "     • {$role->name} (Guard: {$role->guard_name})\n";
        }
    } else {
        echo "   ✅ Semua role menggunakan guard 'pegawai'\n";
    }

    $inconsistentPermissions = DB::table('permissions')
        ->where('guard_name', '!=', 'pegawai')
        ->get();

    if ($inconsistentPermissions->count() > 0) {
        echo "   ⚠️ Permission dengan guard inconsistent:\n";
        foreach ($inconsistentPermissions as $permission) {
            echo "     • {$permission->name} (Guard: {$permission->guard_name})\n";
        }
    } else {
        echo "   ✅ Semua permission menggunakan guard 'pegawai'\n";
    }

    echo "\n🎉 Debug selesai!\n";
    echo "💡 Periksa hasil di atas untuk mengidentifikasi masalah role assignment.\n";

} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";

    // Tampilkan stack trace untuk debugging
    echo "\n🔍 Stack Trace:\n";
    echo $e->getTraceAsString() . "\n";
}
