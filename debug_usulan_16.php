<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BackendUnivUsulan\Usulan;

echo "=== DEBUG USULAN ID 16 ===\n\n";

// Get usulan with ID 16
$usulan = Usulan::find(16);

if ($usulan) {
    echo "✅ Usulan ditemukan:\n";
    echo "- ID: {$usulan->id}\n";
    echo "- Jenis: {$usulan->jenis_usulan}\n";
    echo "- Status: {$usulan->status_usulan}\n";
    echo "- Pegawai: " . ($usulan->pegawai->nama_lengkap ?? 'N/A') . "\n";
    echo "- NIP: " . ($usulan->pegawai->nip ?? 'N/A') . "\n";
    
    echo "\n📊 Validasi Data Structure:\n";
    $validasiData = $usulan->validasi_data ?? [];
    echo "- Total keys: " . count($validasiData) . "\n";
    
    foreach ($validasiData as $key => $value) {
        echo "- Key: {$key}\n";
        if (is_array($value)) {
            echo "  - Type: Array with " . count($value) . " items\n";
            
            if ($key === 'tim_penilai') {
                echo "  - Tim Penilai Data:\n";
                foreach ($value as $subKey => $subValue) {
                    echo "    - {$subKey}: " . (is_array($subValue) ? 'Array(' . count($subValue) . ')' : $subValue) . "\n";
                    
                    if ($subKey === 'validation' && is_array($subValue)) {
                        echo "    - Validation Fields:\n";
                        foreach ($subValue as $category => $fields) {
                            if (is_array($fields)) {
                                echo "      - {$category}: " . count($fields) . " fields\n";
                                foreach ($fields as $field => $fieldData) {
                                    if (is_array($fieldData)) {
                                        $status = $fieldData['status'] ?? 'unknown';
                                        $keterangan = $fieldData['keterangan'] ?? 'no keterangan';
                                        echo "        - {$field}: {$status} - {$keterangan}\n";
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            echo "  - Type: " . gettype($value) . " - Value: {$value}\n";
        }
    }
    
    echo "\n🔍 Penilai Validation Data:\n";
    $penilaiValidationData = $usulan->getValidasiByRole('tim_penilai');
    if ($penilaiValidationData) {
        echo "- Ada data validasi penilai\n";
        echo "- Total categories: " . count($penilaiValidationData) . "\n";
        
        foreach ($penilaiValidationData as $category => $fields) {
            if (is_array($fields)) {
                echo "- Category: {$category}\n";
                foreach ($fields as $field => $fieldData) {
                    if (is_array($fieldData)) {
                        $status = $fieldData['status'] ?? 'unknown';
                        $keterangan = $fieldData['keterangan'] ?? 'no keterangan';
                        echo "  - {$field}: {$status} - {$keterangan}\n";
                    }
                }
            }
        }
    } else {
        echo "- Tidak ada data validasi penilai\n";
    }
    
    echo "\n📋 Invalid Fields:\n";
    $invalidFields = $usulan->getInvalidFields('tim_penilai');
    if (!empty($invalidFields)) {
        echo "- Total invalid fields: " . count($invalidFields) . "\n";
        foreach ($invalidFields as $field) {
            echo "- {$field['category']} > {$field['field']}: {$field['keterangan']}\n";
        }
    } else {
        echo "- Tidak ada field yang invalid\n";
    }
    
} else {
    echo "❌ Usulan dengan ID 16 tidak ditemukan\n";
}

echo "\n=== SELESAI ===\n";
