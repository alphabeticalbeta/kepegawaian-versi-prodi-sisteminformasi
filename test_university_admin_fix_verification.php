<?php

/**
 * Test Script untuk Verifikasi University Admin Document Display Fix
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'test_university_admin_fix_verification.php';"
 */

echo "🔍 UNIVERSITY ADMIN DOCUMENT DISPLAY FIX VERIFICATION\n";
echo "==================================================\n\n";

// Test 1: Verify the fix in PusatUsulanController
echo "🎯 CONTROLLER FIX VERIFICATION:\n";

$controllerFile = 'app/Http/Controllers/Backend/AdminUnivUsulan/PusatUsulanController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    // Check if role is passed correctly
    $hasRolePassed = strpos($content, "getValidationFieldsWithDynamicBkd(\$usulan, 'admin_universitas')") !== false;
    echo $hasRolePassed ? "✅ Role 'admin_universitas' passed correctly\n" : "❌ Role not passed correctly\n";
    
    // Check if validationFields is passed to view
    $hasValidationFieldsPassed = strpos($content, "'validationFields' => \$validationFields") !== false;
    echo $hasValidationFieldsPassed ? "✅ validationFields passed to view\n" : "❌ validationFields not passed to view\n";
    
} else {
    echo "❌ Controller file not found\n";
}

echo "\n";

// Test 2: Verify the fix in view template
echo "📋 VIEW TEMPLATE FIX VERIFICATION:\n";

$viewFile = 'resources/views/backend/layouts/admin-univ-usulan/pusat-usulan/detail-usulan.blade.php';
if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    // Check if duplicate getValidationFieldsWithDynamicBkd call is removed
    $hasDuplicateCall = strpos($content, "getValidationFieldsWithDynamicBkd(\$usulan)") !== false;
    echo $hasDuplicateCall ? "❌ Duplicate call still exists\n" : "✅ Duplicate call removed\n";
    
    // Check if validationFields is used from controller
    $hasValidationFieldsUsage = strpos($content, "\$validationFields") !== false;
    echo $hasValidationFieldsUsage ? "✅ validationFields used from controller\n" : "❌ validationFields not used\n";
    
} else {
    echo "❌ View file not found\n";
}

echo "\n";

// Test 3: Verify data flow
echo "📊 DATA FLOW VERIFICATION:\n";

try {
    // Get a usulan with dokumen_pendukung
    $usulan = \App\Models\BackendUnivUsulan\Usulan::whereIn('status_usulan', ['Diusulkan ke Universitas', 'Sedang Direview Universitas'])
        ->first();
    
    if ($usulan) {
        echo "📋 Testing with Usulan ID: {$usulan->id}\n";
        
        // Test 1: Check if dokumen_pendukung exists in data
        $validasiData = $usulan->validasi_data ?? [];
        $hasDokumenPendukung = isset($validasiData['admin_fakultas']['dokumen_pendukung']);
        echo $hasDokumenPendukung ? "✅ dokumen_pendukung data exists\n" : "❌ dokumen_pendukung data missing\n";
        
        // Test 2: Check if getValidationFieldsWithDynamicBkd returns dokumen_pendukung for admin_universitas
        $fields = \App\Models\BackendUnivUsulan\Usulan::getValidationFieldsWithDynamicBkd($usulan, 'admin_universitas');
        $hasDokumenPendukungInFields = isset($fields['dokumen_pendukung']);
        echo $hasDokumenPendukungInFields ? "✅ dokumen_pendukung in validation fields for admin_universitas\n" : "❌ dokumen_pendukung missing from validation fields\n";
        
        // Test 3: Check if UsulanFieldHelper can read the data
        $helper = new \App\Helpers\UsulanFieldHelper($usulan);
        $nomorSurat = $helper->getFieldValue('dokumen_pendukung', 'nomor_surat_usulan');
        $hasNomorSurat = !empty($nomorSurat) && $nomorSurat !== '-';
        echo $hasNomorSurat ? "✅ UsulanFieldHelper can read nomor_surat_usulan\n" : "❌ UsulanFieldHelper cannot read nomor_surat_usulan\n";
        
        // Test 4: Check if validation labels are correct
        $label = $helper->getValidationLabel('dokumen_pendukung', 'nomor_surat_usulan');
        $hasCorrectLabel = $label === 'NOMOR SURAT USULAN FAKULTAS';
        echo $hasCorrectLabel ? "✅ Validation label is correct\n" : "❌ Validation label is incorrect: {$label}\n";
        
    } else {
        echo "❌ No usulan found for testing\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error in data flow verification: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Simulate controller behavior
echo "🎭 CONTROLLER BEHAVIOR SIMULATION:\n";

try {
    $usulan = \App\Models\BackendUnivUsulan\Usulan::whereIn('status_usulan', ['Diusulkan ke Universitas', 'Sedang Direview Universitas'])
        ->first();
    
    if ($usulan) {
        // Simulate what the controller does
        $validationFields = \App\Models\BackendUnivUsulan\Usulan::getValidationFieldsWithDynamicBkd($usulan, 'admin_universitas');
        $bkdLabels = $usulan->getBkdDisplayLabels();
        $existingValidation = $usulan->getValidasiByRole('admin_universitas');
        
        echo "📋 Controller simulation results:\n";
        echo "   Categories in validationFields: " . implode(', ', array_keys($validationFields)) . "\n";
        echo "   Has dokumen_pendukung: " . (isset($validationFields['dokumen_pendukung']) ? 'Yes' : 'No') . "\n";
        echo "   Has bkdLabels: " . (!empty($bkdLabels) ? 'Yes' : 'No') . "\n";
        echo "   Has existingValidation: " . (!empty($existingValidation) ? 'Yes' : 'No') . "\n";
        
        // Check if dokumen_pendukung fields are present
        if (isset($validationFields['dokumen_pendukung'])) {
            echo "   dokumen_pendukung fields: " . implode(', ', $validationFields['dokumen_pendukung']) . "\n";
            
            // Test each field
            foreach ($validationFields['dokumen_pendukung'] as $field) {
                $helper = new \App\Helpers\UsulanFieldHelper($usulan);
                $value = $helper->getFieldValue('dokumen_pendukung', $field);
                $label = $helper->getValidationLabel('dokumen_pendukung', $field);
                
                echo "     {$field}:\n";
                echo "       Label: {$label}\n";
                echo "       Value: " . (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) . "\n";
            }
        }
        
    } else {
        echo "❌ No usulan found for simulation\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error in controller simulation: " . $e->getMessage() . "\n";
}

echo "\n✅ Fix verification completed!\n";
echo "\n📝 EXPECTED BEHAVIOR AFTER FIX:\n";
echo "1. University Admin (Promotion Proposal) should see:\n";
echo "   - NOMOR SURAT USULAN FAKULTAS\n";
echo "   - DOKUMEN SURAT USULAN FAKULTAS (with 'Lihat Dokumen' button)\n";
echo "   - NOMOR SURAT SENAT\n";
echo "   - DOKUMEN SURAT SENAT (with 'Lihat Dokumen' button)\n";
echo "2. Admin Fakultas should NOT see these fields\n";
echo "3. Penilai and Senat should see these fields\n";
echo "4. All document links should work correctly\n";
