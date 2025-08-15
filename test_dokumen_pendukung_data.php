<?php

/**
 * Test Script untuk Memeriksa Data Dokumen Pendukung
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'test_dokumen_pendukung_data.php';"
 */

echo "🔍 DOKUMEN PENDUKUNG DATA TEST\n";
echo "==============================\n\n";

// Test 1: Check if there are any usulans with dokumen_pendukung data
echo "📊 CHECKING USULAN DATA:\n";

try {
    // Get usulans that have been submitted to university
    $usulans = \App\Models\BackendUnivUsulan\Usulan::whereIn('status_usulan', ['Diusulkan ke Universitas', 'Sedang Direview Universitas'])
        ->with(['pegawai:id,nama_lengkap', 'periodeUsulan:id,nama_periode'])
        ->limit(5)
        ->get();
    
    echo "Found " . $usulans->count() . " usulans with status 'Diusulkan ke Universitas' or 'Sedang Direview Universitas'\n\n";
    
    foreach ($usulans as $usulan) {
        echo "📋 Usulan ID: {$usulan->id}\n";
        echo "   Pegawai: {$usulan->pegawai->nama_lengkap}\n";
        echo "   Periode: {$usulan->periodeUsulan->nama_periode}\n";
        echo "   Status: {$usulan->status_usulan}\n";
        
        // Check validasi_data structure
        $validasiData = $usulan->validasi_data ?? [];
        echo "   Validasi Data Keys: " . implode(', ', array_keys($validasiData)) . "\n";
        
        // Check admin_fakultas data
        if (isset($validasiData['admin_fakultas'])) {
            $adminFakultasData = $validasiData['admin_fakultas'];
            echo "   Admin Fakultas Keys: " . implode(', ', array_keys($adminFakultasData)) . "\n";
            
            // Check dokumen_pendukung
            if (isset($adminFakultasData['dokumen_pendukung'])) {
                $dokumenPendukung = $adminFakultasData['dokumen_pendukung'];
                echo "   Dokumen Pendukung Keys: " . implode(', ', array_keys($dokumenPendukung)) . "\n";
                
                // Show actual values
                foreach ($dokumenPendukung as $key => $value) {
                    if (is_string($value)) {
                        echo "     {$key}: " . (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) . "\n";
                    } else {
                        echo "     {$key}: " . gettype($value) . "\n";
                    }
                }
            } else {
                echo "   ❌ Dokumen Pendukung: NOT FOUND\n";
            }
        } else {
            echo "   ❌ Admin Fakultas Data: NOT FOUND\n";
        }
        
        echo "\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error checking usulan data: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Test UsulanFieldHelper with sample data
echo "🔧 TESTING USULAN FIELD HELPER:\n";

try {
    // Get first usulan with dokumen_pendukung
    $usulan = \App\Models\BackendUnivUsulan\Usulan::whereIn('status_usulan', ['Diusulkan ke Universitas', 'Sedang Direview Universitas'])
        ->first();
    
    if ($usulan) {
        $helper = new \App\Helpers\UsulanFieldHelper($usulan);
        
        // Test dokumen_pendukung fields
        $fields = ['nomor_surat_usulan', 'file_surat_usulan', 'nomor_berita_senat', 'file_berita_senat'];
        
        foreach ($fields as $field) {
            try {
                $value = $helper->getFieldValue('dokumen_pendukung', $field);
                echo "   {$field}: " . (strlen($value) > 100 ? substr($value, 0, 100) . '...' : $value) . "\n";
            } catch (\Exception $e) {
                echo "   {$field}: ERROR - " . $e->getMessage() . "\n";
            }
        }
        
        // Test validation labels
        echo "\n   Validation Labels:\n";
        foreach ($fields as $field) {
            $label = $helper->getValidationLabel('dokumen_pendukung', $field);
            echo "     {$field}: {$label}\n";
        }
        
    } else {
        echo "❌ No usulan found with dokumen_pendukung data\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error testing helper: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Test getValidationFieldsWithDynamicBkd
echo "🎯 TESTING getValidationFieldsWithDynamicBkd:\n";

try {
    $usulan = \App\Models\BackendUnivUsulan\Usulan::whereIn('status_usulan', ['Diusulkan ke Universitas', 'Sedang Direview Universitas'])
        ->first();
    
    if ($usulan) {
        // Test with admin_universitas role
        $fields = \App\Models\BackendUnivUsulan\Usulan::getValidationFieldsWithDynamicBkd($usulan, 'admin_universitas');
        
        echo "   Categories: " . implode(', ', array_keys($fields)) . "\n";
        
        if (isset($fields['dokumen_pendukung'])) {
            echo "   ✅ dokumen_pendukung category found\n";
            echo "   dokumen_pendukung fields: " . implode(', ', $fields['dokumen_pendukung']) . "\n";
        } else {
            echo "   ❌ dokumen_pendukung category NOT found\n";
        }
        
        // Test with admin_fakultas role (should NOT have dokumen_pendukung)
        $fieldsFakultas = \App\Models\BackendUnivUsulan\Usulan::getValidationFieldsWithDynamicBkd($usulan, 'admin_fakultas');
        
        if (isset($fieldsFakultas['dokumen_pendukung'])) {
            echo "   ❌ admin_fakultas should NOT have dokumen_pendukung\n";
        } else {
            echo "   ✅ admin_fakultas correctly does NOT have dokumen_pendukung\n";
        }
        
    } else {
        echo "❌ No usulan found for testing\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error testing getValidationFieldsWithDynamicBkd: " . $e->getMessage() . "\n";
}

echo "\n✅ Test completed!\n";
