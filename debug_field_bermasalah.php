<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BackendUnivUsulan\Usulan;

echo "=== DEBUG FIELD BERMASALAH ===\n";
echo "Usulan ID: 16\n\n";

try {
    $usulan = Usulan::find(16);
    
    if (!$usulan) {
        echo "❌ Usulan dengan ID 16 tidak ditemukan!\n";
        exit(1);
    }
    
    echo "✅ Usulan ditemukan\n";
    echo "Nama: " . ($usulan->pegawai->nama_lengkap ?? 'N/A') . "\n";
    echo "Status: " . ($usulan->status_usulan ?? 'N/A') . "\n\n";
    
    // Get validation data
    $validasiData = $usulan->validasi_data ?? [];
    echo "=== VALIDASI DATA ===\n";
    echo "Raw validasi_data:\n";
    print_r($validasiData);
    echo "\n";
    
    // Check tim_penilai data
    if (isset($validasiData['tim_penilai'])) {
        echo "=== TIM PENILAI DATA ===\n";
        $timPenilaiData = $validasiData['tim_penilai'];
        
        if (isset($timPenilaiData['reviews'])) {
            echo "Reviews found:\n";
            foreach ($timPenilaiData['reviews'] as $index => $review) {
                echo "Review #" . ($index + 1) . ":\n";
                echo "  Type: " . ($review['type'] ?? 'N/A') . "\n";
                echo "  Penilai: " . ($review['penilai']->nama_lengkap ?? 'N/A') . "\n";
                echo "  Catatan: " . ($review['catatan'] ?? 'N/A') . "\n";
                
                if (isset($review['validation'])) {
                    echo "  Validation data:\n";
                    foreach ($review['validation'] as $category => $fields) {
                        echo "    Category: $category\n";
                        if (is_array($fields)) {
                            foreach ($fields as $field => $fieldData) {
                                echo "      Field: $field\n";
                                echo "        Status: " . ($fieldData['status'] ?? 'N/A') . "\n";
                                echo "        Keterangan: " . ($fieldData['keterangan'] ?? 'N/A') . "\n";
                                
                                if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                                    echo "        ⚠️ FIELD INI BERMASALAH!\n";
                                }
                            }
                        }
                    }
                } else {
                    echo "  ❌ No validation data found\n";
                }
                echo "\n";
            }
        } else {
            echo "❌ No reviews found in tim_penilai data\n";
        }
        
        // Check old structure
        if (isset($timPenilaiData['perbaikan_usulan'])) {
            echo "=== OLD STRUCTURE (perbaikan_usulan) ===\n";
            print_r($timPenilaiData['perbaikan_usulan']);
        }
        
        if (isset($timPenilaiData['validation'])) {
            echo "=== OLD STRUCTURE (validation) ===\n";
            print_r($timPenilaiData['validation']);
        }
    } else {
        echo "❌ No tim_penilai data found\n";
    }
    
    // Test getInvalidFields method
    echo "\n=== TESTING getInvalidFields METHOD ===\n";
    $invalidFields = $usulan->getInvalidFields();
    echo "Invalid fields count: " . count($invalidFields) . "\n";
    if (!empty($invalidFields)) {
        echo "Invalid fields:\n";
        foreach ($invalidFields as $field) {
            echo "  - {$field['category']} > {$field['field']}: {$field['keterangan']}\n";
        }
    } else {
        echo "❌ No invalid fields found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
