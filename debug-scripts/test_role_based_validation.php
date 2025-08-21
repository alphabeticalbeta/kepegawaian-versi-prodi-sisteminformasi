<?php

/**
 * Test Script untuk Verifikasi Role-Based Validation Fields
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'test_role_based_validation.php';"
 */

use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\Usulan;

class RoleBasedValidationTest
{
    public function testRoleBasedValidation()
    {
        echo "🔍 TESTING ROLE-BASED VALIDATION FIELDS\n";
        echo "=======================================\n\n";

        try {
            // Test 1: Check if user is authenticated
            if (!Auth::check()) {
                echo "❌ User tidak terautentikasi\n";
                return;
            }

            $user = Auth::user();
            echo "✅ User terautentikasi: {$user->nama_lengkap}\n";
            echo "   - Role: " . ($user->role ?? 'N/A') . "\n\n";

            // Test 2: Find a usulan to test
            $usulan = Usulan::with(['periodeUsulan'])->first();
            if (!$usulan) {
                echo "❌ Tidak ada usulan untuk ditest\n";
                return;
            }

            echo "✅ Menggunakan usulan ID: {$usulan->id}\n";
            echo "   - Periode: " . ($usulan->periodeUsulan->nama_periode ?? 'N/A') . "\n\n";

            // Test 3: Test validation fields for different roles
            echo "🎯 VALIDATION FIELDS BY ROLE:\n";
            $this->testValidationFieldsByRole($usulan);

            // Test 4: Test BKD fields consistency
            echo "\n📚 BKD FIELDS CONSISTENCY:\n";
            $this->testBkdFieldsConsistency($usulan);

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        }
    }

    private function testValidationFieldsByRole($usulan)
    {
        $roles = [
            'admin_fakultas' => 'Admin Fakultas',
            'admin_universitas' => 'Admin Universitas',
            'penilai' => 'Penilai',
            'senat' => 'Senat'
        ];

        foreach ($roles as $role => $roleName) {
            echo "   📋 Testing {$roleName} ({$role}):\n";
            
            $validationFields = \App\Models\BackendUnivUsulan\Usulan::getValidationFieldsWithDynamicBkd($usulan, $role);
            
            $hasDokumenPendukung = isset($validationFields['dokumen_pendukung']);
            $expectedHasDokumenPendukung = in_array($role, ['admin_universitas', 'penilai', 'senat']);
            
            $status = $hasDokumenPendukung ? '✅ SHOWS' : '❌ HIDDEN';
            $expected = $expectedHasDokumenPendukung ? 'SHOULD SHOW' : 'SHOULD HIDE';
            $result = ($hasDokumenPendukung === $expectedHasDokumenPendukung) ? '✅ CORRECT' : '❌ INCORRECT';
            
            echo "     {$status} Dokumen Pendukung ({$expected}) - {$result}\n";
            
            if ($hasDokumenPendukung) {
                echo "       - Fields: " . implode(', ', $validationFields['dokumen_pendukung']) . "\n";
            }
            
            echo "     - Total categories: " . count($validationFields) . "\n";
            echo "     - Categories: " . implode(', ', array_keys($validationFields)) . "\n\n";
        }
    }

    private function testBkdFieldsConsistency($usulan)
    {
        $roles = ['admin_fakultas', 'admin_universitas', 'penilai', 'senat'];
        
        echo "   🔄 Testing BKD fields consistency across roles:\n";
        
        $bkdFieldsByRole = [];
        foreach ($roles as $role) {
            $validationFields = \App\Models\BackendUnivUsulan\Usulan::getValidationFieldsWithDynamicBkd($usulan, $role);
            $bkdFieldsByRole[$role] = $validationFields['dokumen_bkd'] ?? [];
        }
        
        // Check if all roles have the same BKD fields
        $firstRole = array_key_first($bkdFieldsByRole);
        $firstBkdFields = $bkdFieldsByRole[$firstRole];
        
        $allConsistent = true;
        foreach ($bkdFieldsByRole as $role => $bkdFields) {
            $isConsistent = ($bkdFields === $firstBkdFields);
            $status = $isConsistent ? '✅ CONSISTENT' : '❌ INCONSISTENT';
            echo "     {$status} {$role}: " . implode(', ', $bkdFields) . "\n";
            
            if (!$isConsistent) {
                $allConsistent = false;
            }
        }
        
        if ($allConsistent) {
            echo "   ✅ All roles have consistent BKD fields\n";
        } else {
            echo "   ❌ BKD fields are inconsistent across roles\n";
        }
    }

    public function testSpecificRoleScenarios()
    {
        echo "\n🎯 SPECIFIC ROLE SCENARIOS:\n";
        echo "==========================\n\n";

        try {
            $usulan = Usulan::with(['periodeUsulan'])->first();
            if (!$usulan) {
                echo "❌ Tidak ada usulan untuk ditest\n";
                return;
            }

            // Test 1: Admin Fakultas should NOT have dokumen_pendukung
            echo "📋 Test 1: Admin Fakultas (should NOT have dokumen_pendukung):\n";
            $adminFakultasFields = \App\Models\BackendUnivUsulan\Usulan::getValidationFieldsWithDynamicBkd($usulan, 'admin_fakultas');
            $hasDokumenPendukung = isset($adminFakultasFields['dokumen_pendukung']);
            $status = !$hasDokumenPendukung ? '✅ CORRECT' : '❌ INCORRECT';
            echo "   {$status} Dokumen Pendukung is " . ($hasDokumenPendukung ? 'SHOWN' : 'HIDDEN') . "\n\n";

            // Test 2: Admin Universitas should HAVE dokumen_pendukung
            echo "📋 Test 2: Admin Universitas (should HAVE dokumen_pendukung):\n";
            $adminUnivFields = \App\Models\BackendUnivUsulan\Usulan::getValidationFieldsWithDynamicBkd($usulan, 'admin_universitas');
            $hasDokumenPendukung = isset($adminUnivFields['dokumen_pendukung']);
            $status = $hasDokumenPendukung ? '✅ CORRECT' : '❌ INCORRECT';
            echo "   {$status} Dokumen Pendukung is " . ($hasDokumenPendukung ? 'SHOWN' : 'HIDDEN') . "\n";
            if ($hasDokumenPendukung) {
                echo "   - Fields: " . implode(', ', $adminUnivFields['dokumen_pendukung']) . "\n";
            }
            echo "\n";

            // Test 3: Penilai should HAVE dokumen_pendukung
            echo "📋 Test 3: Penilai (should HAVE dokumen_pendukung):\n";
            $penilaiFields = \App\Models\BackendUnivUsulan\Usulan::getValidationFieldsWithDynamicBkd($usulan, 'penilai');
            $hasDokumenPendukung = isset($penilaiFields['dokumen_pendukung']);
            $status = $hasDokumenPendukung ? '✅ CORRECT' : '❌ INCORRECT';
            echo "   {$status} Dokumen Pendukung is " . ($hasDokumenPendukung ? 'SHOWN' : 'HIDDEN') . "\n";
            if ($hasDokumenPendukung) {
                echo "   - Fields: " . implode(', ', $penilaiFields['dokumen_pendukung']) . "\n";
            }
            echo "\n";

            // Test 4: Senat should HAVE dokumen_pendukung
            echo "📋 Test 4: Senat (should HAVE dokumen_pendukung):\n";
            $senatFields = \App\Models\BackendUnivUsulan\Usulan::getValidationFieldsWithDynamicBkd($usulan, 'senat');
            $hasDokumenPendukung = isset($senatFields['dokumen_pendukung']);
            $status = $hasDokumenPendukung ? '✅ CORRECT' : '❌ INCORRECT';
            echo "   {$status} Dokumen Pendukung is " . ($hasDokumenPendukung ? 'SHOWN' : 'HIDDEN') . "\n";
            if ($hasDokumenPendukung) {
                echo "   - Fields: " . implode(', ', $senatFields['dokumen_pendukung']) . "\n";
            }
            echo "\n";

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
        }
    }
}

// Run the test
$test = new RoleBasedValidationTest();
$test->testRoleBasedValidation();
$test->testSpecificRoleScenarios();
