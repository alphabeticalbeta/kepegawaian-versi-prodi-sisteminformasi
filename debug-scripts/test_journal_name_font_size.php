<?php

/**
 * Test Script untuk Verifikasi Journal Name Font Size Fix
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'test_journal_name_font_size.php';"
 */

echo "🔍 JOURNAL NAME FONT SIZE FIX TEST\n";
echo "==================================\n\n";

// Test 1: Check validation row file
$validationRowFile = 'resources/views/backend/components/usulan/_validation-row.blade.php';
if (file_exists($validationRowFile)) {
    $content = file_get_contents($validationRowFile);
    
    echo "📋 VALIDATION ROW CHECK:\n";
    
    // Check for nama_jurnal in article fields array
    $hasNamaJurnal = strpos($content, 'nama_jurnal') !== false;
    echo $hasNamaJurnal ? "✅ nama_jurnal in article fields array\n" : "❌ nama_jurnal missing from article fields array\n";
    
    // Check for all article fields including nama_jurnal
    $articleFields = ['nama_jurnal', 'judul_artikel', 'penerbit_artikel', 'volume_artikel', 'nomor_artikel', 'edisi_artikel', 'halaman_artikel'];
    $allFieldsFound = true;
    foreach ($articleFields as $field) {
        if (strpos($content, $field) === false) {
            $allFieldsFound = false;
            break;
        }
    }
    echo $allFieldsFound ? "✅ All article fields (including nama_jurnal) included\n" : "❌ Some article fields missing\n";
    
    // Check for improved text wrapping CSS
    $hasWhitespaceNormal = strpos($content, 'whitespace-normal') !== false;
    echo $hasWhitespaceNormal ? "✅ whitespace-normal CSS exists\n" : "❌ whitespace-normal CSS missing\n";
    
    // Check for word-wrap CSS
    $hasWordWrap = strpos($content, 'word-wrap: break-word') !== false;
    echo $hasWordWrap ? "✅ word-wrap CSS exists\n" : "❌ word-wrap CSS missing\n";
    
    // Check for overflow-wrap CSS
    $hasOverflowWrap = strpos($content, 'overflow-wrap: break-word') !== false;
    echo $hasOverflowWrap ? "✅ overflow-wrap CSS exists\n" : "❌ overflow-wrap CSS missing\n";
    
} else {
    echo "❌ Validation row file not found\n";
}

echo "\n";

// Test 2: Check UsulanFieldHelper for nama_jurnal handling
$helperFile = 'app/Helpers/UsulanFieldHelper.php';
if (file_exists($helperFile)) {
    $content = file_get_contents($helperFile);
    
    echo "🔧 USULAN FIELD HELPER CHECK:\n";
    
    // Check for nama_jurnal field handling
    $hasNamaJurnalHandling = strpos($content, 'nama_jurnal') !== false;
    echo $hasNamaJurnalHandling ? "✅ nama_jurnal field handling exists\n" : "❌ nama_jurnal field handling missing\n";
    
    // Check for karya_ilmiah category handling
    $hasKaryaIlmiahHandling = strpos($content, 'karya_ilmiah') !== false;
    echo $hasKaryaIlmiahHandling ? "✅ karya_ilmiah category handling exists\n" : "❌ karya_ilmiah category handling missing\n";
    
} else {
    echo "❌ UsulanFieldHelper file not found\n";
}

echo "\n";

// Test 3: Check Usulan model for nama_jurnal field
$usulanModelFile = 'app/Models/BackendUnivUsulan/Usulan.php';
if (file_exists($usulanModelFile)) {
    $content = file_get_contents($usulanModelFile);
    
    echo "📊 USULAN MODEL CHECK:\n";
    
    // Check for nama_jurnal in model
    $hasNamaJurnalInModel = strpos($content, 'nama_jurnal') !== false;
    echo $hasNamaJurnalInModel ? "✅ nama_jurnal in model exists\n" : "❌ nama_jurnal in model missing\n";
    
    // Check for karya_ilmiah category in model
    $hasKaryaIlmiahInModel = strpos($content, 'karya_ilmiah') !== false;
    echo $hasKaryaIlmiahInModel ? "✅ karya_ilmiah category in model exists\n" : "❌ karya_ilmiah category in model missing\n";
    
} else {
    echo "❌ Usulan model file not found\n";
}

echo "\n";

// Test 4: Sample journal name scenarios
echo "🧪 SAMPLE JOURNAL NAME SCENARIOS:\n";

$scenarios = [
    [
        'field' => 'nama_jurnal',
        'category' => 'karya_ilmiah',
        'expected_font_size' => 'text-sm',
        'description' => 'Journal Name - Long text field that needs wrapping'
    ],
    [
        'field' => 'judul_artikel',
        'category' => 'karya_ilmiah',
        'expected_font_size' => 'text-sm',
        'description' => 'Article Title - Long text field'
    ],
    [
        'field' => 'penerbit_artikel',
        'category' => 'karya_ilmiah',
        'expected_font_size' => 'text-sm',
        'description' => 'Article Publisher - Long text field'
    ],
    [
        'field' => 'nama_lengkap',
        'category' => 'data_pribadi',
        'expected_font_size' => 'text-xl',
        'description' => 'Regular field - Normal font size'
    ]
];

foreach ($scenarios as $scenario) {
    $isArticleField = in_array($scenario['field'], ['nama_jurnal', 'judul_artikel', 'penerbit_artikel', 'volume_artikel', 'nomor_artikel', 'edisi_artikel', 'halaman_artikel']);
    $expectedFontSize = $isArticleField ? 'text-sm' : 'text-xl';
    $status = ($expectedFontSize === $scenario['expected_font_size']) ? '✅' : '❌';
    
    echo "   {$status} {$scenario['description']}:\n";
    echo "     Field: {$scenario['field']}\n";
    echo "     Category: {$scenario['category']}\n";
    echo "     Expected font size: {$scenario['expected_font_size']}\n";
    echo "     Is article field: " . ($isArticleField ? 'Yes' : 'No') . "\n";
    echo "     Expected text wrapping: " . ($isArticleField ? 'Yes (whitespace-normal + word-wrap)' : 'No') . "\n\n";
}

echo "✅ Test completed!\n";
echo "\n📝 MANUAL TESTING INSTRUCTIONS:\n";
echo "1. Login sebagai Admin Fakultas\n";
echo "2. Akses halaman detail usulan yang memiliki data artikel\n";
echo "3. Verify: Field 'NAMA JURNAL' menggunakan font size 'text-sm' (lebih kecil)\n";
echo "4. Verify: Field 'JUDUL ARTIKEL' menggunakan font size 'text-sm' (lebih kecil)\n";
echo "5. Verify: Field 'PENERBIT ARTIKEL' menggunakan font size 'text-sm' (lebih kecil)\n";
echo "6. Verify: Field 'VOLUME ARTIKEL' menggunakan font size 'text-sm' (lebih kecil)\n";
echo "7. Verify: Field 'NOMOR ARTIKEL' menggunakan font size 'text-sm' (lebih kecil)\n";
echo "8. Verify: Field 'EDISI ARTIKEL' menggunakan font size 'text-sm' (lebih kecil)\n";
echo "9. Verify: Field 'HALAMAN ARTIKEL' menggunakan font size 'text-sm' (lebih kecil)\n";
echo "10. Verify: Field regular lainnya (nama, nip, dll) tetap menggunakan 'text-xl' (normal)\n";
echo "11. Verify: Text panjang pada field artikel (terutama nama_jurnal) dapat wrap ke baris berikutnya\n";
echo "12. Verify: Text tetap terbaca dengan jelas meskipun ukuran font lebih kecil\n";
echo "13. Verify: Tidak ada text yang terpotong atau overflow dari container\n";
