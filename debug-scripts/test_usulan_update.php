<?php

/**
 * Test Script untuk Usulan Update
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'test_usulan_update.php';"
 */

use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\Pegawai;

class UsulanUpdateTest
{
    public function testUsulanUpdate()
    {
        echo "🔍 TESTING USULAN UPDATE FUNCTIONALITY\n";
        echo "=====================================\n\n";

        try {
            // Test 1: Check if user is authenticated
            if (!Auth::check()) {
                echo "❌ User tidak terautentikasi\n";
                return;
            }

            $user = Auth::user();
            echo "✅ User terautentikasi: {$user->nama_lengkap}\n";

            // Test 2: Check if user has usulans
            $usulans = $user->usulans()->whereIn('jenis_usulan', ['usulan-jabatan-dosen', 'usulan-jabatan-tendik'])->get();
            
            if ($usulans->isEmpty()) {
                echo "❌ User tidak memiliki usulan jabatan\n";
                return;
            }

            echo "✅ User memiliki " . $usulans->count() . " usulan jabatan\n";

            // Test 3: Check each usulan
            foreach ($usulans as $usulan) {
                echo "\n📋 USULAN ID: {$usulan->id}\n";
                echo "   - Status: {$usulan->status_usulan}\n";
                echo "   - Jenis: {$usulan->jenis_usulan}\n";
                echo "   - Can Edit: " . ($usulan->can_edit ? 'Yes' : 'No') . "\n";
                echo "   - Is Read Only: " . ($usulan->is_read_only ? 'Yes' : 'No') . "\n";
                echo "   - Data Usulan: " . (is_array($usulan->data_usulan) ? 'Valid' : 'Invalid') . "\n";
                
                if (is_array($usulan->data_usulan)) {
                    echo "   - Metadata: " . (isset($usulan->data_usulan['metadata']) ? 'Exists' : 'Missing') . "\n";
                    echo "   - Dokumen: " . (isset($usulan->data_usulan['dokumen_usulan']) ? 'Exists' : 'Missing') . "\n";
                }
            }

            // Test 4: Check editable usulans
            $editableUsulans = $usulans->filter(function($usulan) {
                return in_array($usulan->status_usulan, ['Draft', 'Perlu Perbaikan', 'Dikembalikan']);
            });

            echo "\n📝 EDITABLE USULANS: " . $editableUsulans->count() . "\n";
            
            if ($editableUsulans->isNotEmpty()) {
                foreach ($editableUsulans as $usulan) {
                    echo "   - ID: {$usulan->id}, Status: {$usulan->status_usulan}\n";
                }
            } else {
                echo "   ❌ Tidak ada usulan yang dapat diedit\n";
            }

            // Test 5: Check routes
            echo "\n🔗 ROUTE TESTING:\n";
            
            if ($editableUsulans->isNotEmpty()) {
                $testUsulan = $editableUsulans->first();
                $editRoute = route('pegawai-unmul.usulan-jabatan.edit', $testUsulan->id);
                $updateRoute = route('pegawai-unmul.usulan-jabatan.update', $testUsulan->id);
                
                echo "   - Edit Route: {$editRoute}\n";
                echo "   - Update Route: {$updateRoute}\n";
                echo "   ✅ Routes generated successfully\n";
            } else {
                echo "   ⚠️  Tidak dapat test routes karena tidak ada usulan yang dapat diedit\n";
            }

            // Test 6: Check form validation
            echo "\n✅ FORM VALIDATION:\n";
            echo "   - StoreJabatanUsulanRequest exists: " . (class_exists('App\Http\Requests\Backend\PegawaiUnmul\StoreJabatanUsulanRequest') ? 'Yes' : 'No') . "\n";
            
            if (class_exists('App\Http\Requests\Backend\PegawaiUnmul\StoreJabatanUsulanRequest')) {
                $request = new \App\Http\Requests\Backend\PegawaiUnmul\StoreJabatanUsulanRequest();
                echo "   - Request authorization: " . ($request->authorize() ? 'Yes' : 'No') . "\n";
                echo "   - Request rules count: " . count($request->rules()) . "\n";
            }

            echo "\n🎯 SUMMARY:\n";
            echo "   - Total Usulans: " . $usulans->count() . "\n";
            echo "   - Editable Usulans: " . $editableUsulans->count() . "\n";
            echo "   - Read Only Usulans: " . $usulans->filter(fn($u) => $u->is_read_only)->count() . "\n";
            
            if ($editableUsulans->isNotEmpty()) {
                echo "   ✅ Update functionality should work\n";
            } else {
                echo "   ⚠️  No editable usulans found\n";
            }

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        }
    }

    public function testControllerMethods()
    {
        echo "\n🔧 CONTROLLER METHOD TESTING:\n";
        echo "============================\n\n";

        try {
            $controller = new \App\Http\Controllers\Backend\PegawaiUnmul\UsulanJabatanController();
            
            // Test if methods exist
            $methods = ['create', 'store', 'edit', 'update'];
            foreach ($methods as $method) {
                echo "   - {$method} method: " . (method_exists($controller, $method) ? 'Exists' : 'Missing') . "\n";
            }

            // Test helper methods
            $helperMethods = ['getDocumentKeys', 'extractKaryaIlmiahData', 'extractSyaratKhususData'];
            foreach ($helperMethods as $method) {
                echo "   - {$method} method: " . (method_exists($controller, $method) ? 'Exists' : 'Missing') . "\n";
            }

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
        }
    }
}

// Run the test
$test = new UsulanUpdateTest();
$test->testUsulanUpdate();
$test->testControllerMethods();
