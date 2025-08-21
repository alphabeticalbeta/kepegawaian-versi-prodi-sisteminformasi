<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BackendUnivUsulan\Usulan;

echo "=== TEST FIELD BERMASALAH DISPLAY ===\n\n";

// Get usulan with ID 16
$usulan = Usulan::find(16);

if ($usulan) {
    echo "✅ Usulan ditemukan:\n";
    echo "- ID: {$usulan->id}\n";
    echo "- Jenis: {$usulan->jenis_usulan}\n";
    echo "- Status: {$usulan->status_usulan}\n";
    echo "- Pegawai: " . ($usulan->pegawai->nama_lengkap ?? 'N/A') . "\n";
    
    echo "\n📊 Validasi Data Structure:\n";
    $validasiData = $usulan->validasi_data ?? [];
    echo "- Total keys: " . count($validasiData) . "\n";
    
    if (isset($validasiData['tim_penilai'])) {
        $timPenilaiData = $validasiData['tim_penilai'];
        echo "- Tim Penilai data found\n";
        
        // Check for reviews structure
        if (isset($timPenilaiData['reviews'])) {
            echo "- Reviews structure found\n";
            foreach ($timPenilaiData['reviews'] as $penilaiId => $reviewData) {
                echo "  - Penilai ID: {$penilaiId}\n";
                echo "    - Type: " . ($reviewData['type'] ?? 'N/A') . "\n";
                echo "    - Catatan: " . ($reviewData['catatan'] ?? 'N/A') . "\n";
                
                if (isset($reviewData['validation'])) {
                    echo "    - Validation data found\n";
                    $invalidCount = 0;
                    foreach ($reviewData['validation'] as $category => $fields) {
                        if (is_array($fields)) {
                            foreach ($fields as $field => $fieldData) {
                                if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                                    $invalidCount++;
                                    echo "      - Invalid: {$category} > {$field} - {$fieldData['keterangan']}\n";
                                }
                            }
                        }
                    }
                    echo "    - Total invalid fields: {$invalidCount}\n";
                }
            }
        } else {
            echo "- No reviews structure found\n";
            
            // Check old structure
            if (isset($timPenilaiData['perbaikan_usulan'])) {
                echo "- Old structure (perbaikan_usulan) found\n";
                echo "  - Catatan: " . ($timPenilaiData['perbaikan_usulan']['catatan'] ?? 'N/A') . "\n";
                echo "  - Penilai ID: " . ($timPenilaiData['perbaikan_usulan']['penilai_id'] ?? 'N/A') . "\n";
            }
            
            if (isset($timPenilaiData['validation'])) {
                echo "- Old validation structure found\n";
                $invalidCount = 0;
                foreach ($timPenilaiData['validation'] as $category => $fields) {
                    if (is_array($fields)) {
                        foreach ($fields as $field => $fieldData) {
                            if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                                $invalidCount++;
                                echo "  - Invalid: {$category} > {$field} - {$fieldData['keterangan']}\n";
                            }
                        }
                    }
                }
                echo "- Total invalid fields: {$invalidCount}\n";
            }
        }
    } else {
        echo "- No tim_penilai data found\n";
    }
    
    echo "\n🔍 Testing Display Logic:\n";
    
    // Test the display logic
    $assignedPenilais = $usulan->penilais ?? collect();
    echo "- Assigned penilais: " . $assignedPenilais->count() . "\n";
    
    foreach ($assignedPenilais as $penilai) {
        echo "- Penilai: {$penilai->nama_lengkap} (ID: {$penilai->id})\n";
    }
    
    // Test getValidasiByRole method
    $penilaiValidationData = $usulan->getValidasiByRole('tim_penilai');
    if ($penilaiValidationData) {
        echo "- getValidasiByRole returned data\n";
        $invalidFields = $usulan->getInvalidFields('tim_penilai');
        echo "- getInvalidFields returned " . count($invalidFields) . " fields\n";
        
        foreach ($invalidFields as $field) {
            echo "  - {$field['category']} > {$field['field']}: {$field['keterangan']}\n";
        }
    } else {
        echo "- getValidasiByRole returned empty/null\n";
    }
    
} else {
    echo "❌ Usulan dengan ID 16 tidak ditemukan\n";
}

echo "\n=== SELESAI ===\n";
