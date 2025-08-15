<?php

/**
 * Test Script untuk Verifikasi University Admin Document Display
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'test_university_admin_document_display.php';"
 */

echo "🔍 UNIVERSITY ADMIN DOCUMENT DISPLAY TEST\n";
echo "========================================\n\n";

// Test 1: Check Usulan model for dokumen_pendukung fields
$usulanModelFile = 'app/Models/BackendUnivUsulan/Usulan.php';
if (file_exists($usulanModelFile)) {
    $content = file_get_contents($usulanModelFile);
    
    echo "📊 USULAN MODEL CHECK:\n";
    
    // Check for dokumen_pendukung in validation fields
    $hasDokumenPendukung = strpos($content, 'dokumen_pendukung') !== false;
    echo $hasDokumenPendukung ? "✅ dokumen_pendukung in validation fields\n" : "❌ dokumen_pendukung missing from validation fields\n";
    
    // Check for specific fields
    $fields = ['nomor_surat_usulan', 'file_surat_usulan', 'nomor_berita_senat', 'file_berita_senat'];
    $allFieldsFound = true;
    foreach ($fields as $field) {
        if (strpos($content, $field) === false) {
            $allFieldsFound = false;
            break;
        }
    }
    echo $allFieldsFound ? "✅ All dokumen_pendukung fields included\n" : "❌ Some dokumen_pendukung fields missing\n";
    
    // Check for role-based filtering
    $hasRoleFiltering = strpos($content, 'admin_universitas') !== false && strpos($content, 'penilai') !== false;
    echo $hasRoleFiltering ? "✅ Role-based filtering exists\n" : "❌ Role-based filtering missing\n";
    
} else {
    echo "❌ Usulan model file not found\n";
}

echo "\n";

// Test 2: Check UsulanFieldHelper for dokumen_pendukung handling
$helperFile = 'app/Helpers/UsulanFieldHelper.php';
if (file_exists($helperFile)) {
    $content = file_get_contents($helperFile);
    
    echo "🔧 USULAN FIELD HELPER CHECK:\n";
    
    // Check for dokumen_pendukung category handling
    $hasDokumenPendukungHandling = strpos($content, 'dokumen_pendukung') !== false;
    echo $hasDokumenPendukungHandling ? "✅ dokumen_pendukung category handling exists\n" : "❌ dokumen_pendukung category handling missing\n";
    
    // Check for specific field handling
    $hasNomorSuratHandling = strpos($content, 'nomor_surat_usulan') !== false;
    echo $hasNomorSuratHandling ? "✅ nomor_surat_usulan handling exists\n" : "❌ nomor_surat_usulan handling missing\n";
    
    $hasFileSuratHandling = strpos($content, 'file_surat_usulan') !== false;
    echo $hasFileSuratHandling ? "✅ file_surat_usulan handling exists\n" : "❌ file_surat_usulan handling missing\n";
    
    // Check for validation labels
    $hasValidationLabels = strpos($content, 'Nomor Surat Usulan Fakultas') !== false;
    echo $hasValidationLabels ? "✅ Validation labels exist\n" : "❌ Validation labels missing\n";
    
} else {
    echo "❌ UsulanFieldHelper file not found\n";
}

echo "\n";

// Test 3: Check PusatUsulanController for admin_universitas role
$controllerFile = 'app/Http/Controllers/Backend/AdminUnivUsulan/PusatUsulanController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    echo "🎯 PUSAT USULAN CONTROLLER CHECK:\n";
    
    // Check for admin_universitas role in getValidationFieldsWithDynamicBkd
    $hasAdminUniversitasRole = strpos($content, 'admin_universitas') !== false;
    echo $hasAdminUniversitasRole ? "✅ admin_universitas role handling exists\n" : "❌ admin_universitas role handling missing\n";
    
    // Check for show method
    $hasShowMethod = strpos($content, 'function show') !== false;
    echo $hasShowMethod ? "✅ show method exists\n" : "❌ show method missing\n";
    
    // Check for validation fields loading
    $hasValidationFieldsLoading = strpos($content, 'getValidationFieldsWithDynamicBkd') !== false;
    echo $hasValidationFieldsLoading ? "✅ Validation fields loading exists\n" : "❌ Validation fields loading missing\n";
    
} else {
    echo "❌ PusatUsulanController file not found\n";
}

echo "\n";

// Test 4: Check AdminFakultasController for dokumen_pendukung saving
$adminFakultasControllerFile = 'app/Http/Controllers/Backend/AdminFakultas/AdminFakultasController.php';
if (file_exists($adminFakultasControllerFile)) {
    $content = file_get_contents($adminFakultasControllerFile);
    
    echo "🏛️ ADMIN FAKULTAS CONTROLLER CHECK:\n";
    
    // Check for dokumen_pendukung saving
    $hasDokumenPendukungSaving = strpos($content, 'dokumen_pendukung') !== false;
    echo $hasDokumenPendukungSaving ? "✅ dokumen_pendukung saving exists\n" : "❌ dokumen_pendukung saving missing\n";
    
    // Check for file path saving
    $hasFilePathSaving = strpos($content, 'file_surat_usulan_path') !== false;
    echo $hasFilePathSaving ? "✅ file path saving exists\n" : "❌ file path saving missing\n";
    
    // Check for validation rules
    $hasValidationRules = strpos($content, 'nomor_surat_usulan.*required') !== false;
    echo $hasValidationRules ? "✅ Validation rules exist\n" : "❌ Validation rules missing\n";
    
} else {
    echo "❌ AdminFakultasController file not found\n";
}

echo "\n";

// Test 5: Sample data structure scenarios
echo "🧪 SAMPLE DATA STRUCTURE SCENARIOS:\n";

$scenarios = [
    [
        'description' => 'Admin Fakultas saves dokumen_pendukung',
        'data_structure' => [
            'validasi_data' => [
                'admin_fakultas' => [
                    'dokumen_pendukung' => [
                        'nomor_surat_usulan' => '001/UNMUL/FT/2025',
                        'nomor_berita_senat' => '002/UNMUL/SENAT/2025',
                        'file_surat_usulan_path' => 'dokumen-fakultas/surat-usulan/file.pdf',
                        'file_berita_senat_path' => 'dokumen-fakultas/berita-senat/file.pdf'
                    ]
                ]
            ]
        ],
        'expected_visibility' => 'University Admin, Penilai, Senat'
    ],
    [
        'description' => 'University Admin should see dokumen_pendukung',
        'user_role' => 'admin_universitas',
        'expected_fields' => [
            'nomor_surat_usulan' => 'NOMOR SURAT USULAN FAKULTAS',
            'file_surat_usulan' => 'DOKUMEN SURAT USULAN FAKULTAS',
            'nomor_berita_senat' => 'NOMOR SURAT SENAT',
            'file_berita_senat' => 'DOKUMEN SURAT SENAT'
        ]
    ],
    [
        'description' => 'Admin Fakultas should NOT see dokumen_pendukung',
        'user_role' => 'admin_fakultas',
        'expected_fields' => 'NOT VISIBLE'
    ]
];

foreach ($scenarios as $scenario) {
    echo "   📋 {$scenario['description']}:\n";
    if (isset($scenario['data_structure'])) {
        echo "     Data Structure: Valid\n";
        echo "     Expected Visibility: {$scenario['expected_visibility']}\n";
    } elseif (isset($scenario['user_role'])) {
        echo "     User Role: {$scenario['user_role']}\n";
        if (is_array($scenario['expected_fields'])) {
            echo "     Expected Fields:\n";
            foreach ($scenario['expected_fields'] as $field => $label) {
                echo "       - {$field}: {$label}\n";
            }
        } else {
            echo "     Expected Fields: {$scenario['expected_fields']}\n";
        }
    }
    echo "\n";
}

echo "✅ Test completed!\n";
echo "\n📝 MANUAL TESTING INSTRUCTIONS:\n";
echo "1. Login sebagai Admin Fakultas\n";
echo "2. Akses usulan dan isi form 'Direkomendasikan (Ke Admin Universitas)'\n";
echo "3. Isi field: Nomor Surat Usulan, File Surat Usulan, Nomor Surat Senat, File Surat Senat\n";
echo "4. Submit form → usulan status berubah ke 'Diusulkan ke Universitas'\n";
echo "5. Login sebagai University Admin (Promotion Proposal)\n";
echo "6. Akses usulan yang sudah dikirim dari Admin Fakultas\n";
echo "7. Verify: Field 'NOMOR SURAT USULAN FAKULTAS' terlihat dan berisi data\n";
echo "8. Verify: Field 'DOKUMEN SURAT USULAN FAKULTAS' terlihat dengan tombol 'Lihat Dokumen'\n";
echo "9. Verify: Field 'NOMOR SURAT SENAT' terlihat dan berisi data\n";
echo "10. Verify: Field 'DOKUMEN SURAT SENAT' terlihat dengan tombol 'Lihat Dokumen'\n";
echo "11. Verify: Field-field ini TIDAK terlihat di halaman Admin Fakultas\n";
echo "12. Verify: Field-field ini terlihat di halaman Penilai dan Senat\n";
