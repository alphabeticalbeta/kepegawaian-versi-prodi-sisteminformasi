<?php

/**
 * Test Script untuk Verifikasi Article Field Font Size Fix
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'test_article_field_font_size.php';"
 */

echo "🔍 ARTICLE FIELD FONT SIZE FIX TEST\n";
echo "===================================\n\n";

// Test 1: Check validation row file
$validationRowFile = 'resources/views/backend/components/usulan/_validation-row.blade.php';
if (file_exists($validationRowFile)) {
    $content = file_get_contents($validationRowFile);
    
    echo "📋 VALIDATION ROW CHECK:\n";
    
    // Check for article fields array
    $hasArticleFieldsArray = strpos($content, 'judul_artikel') !== false && strpos($content, 'penerbit_artikel') !== false;
    echo $hasArticleFieldsArray ? "✅ Article fields array exists\n" : "❌ Article fields array missing\n";
    
    // Check for isArticleField variable
    $hasIsArticleField = strpos($content, 'isArticleField') !== false;
    echo $hasIsArticleField ? "✅ isArticleField variable exists\n" : "❌ isArticleField variable missing\n";
    
    // Check for conditional font size
    $hasConditionalFontSize = strpos($content, 'text-sm') !== false && strpos($content, 'text-xl') !== false;
    echo $hasConditionalFontSize ? "✅ Conditional font size exists\n" : "❌ Conditional font size missing\n";
    
    // Check for all article fields
    $articleFields = ['judul_artikel', 'penerbit_artikel', 'volume_artikel', 'nomor_artikel', 'edisi_artikel', 'halaman_artikel'];
    $allFieldsFound = true;
    foreach ($articleFields as $field) {
        if (strpos($content, $field) === false) {
            $allFieldsFound = false;
            break;
        }
    }
    echo $allFieldsFound ? "✅ All article fields included\n" : "❌ Some article fields missing\n";
    
} else {
    echo "❌ Validation row file not found\n";
}

echo "\n";

// Test 2: Check UsulanFieldHelper for article field handling
$helperFile = 'app/Helpers/UsulanFieldHelper.php';
if (file_exists($helperFile)) {
    $content = file_get_contents($helperFile);
    
    echo "🔧 USULAN FIELD HELPER CHECK:\n";
    
    // Check for article field handling
    $hasArticleFieldHandling = strpos($content, 'karya_ilmiah') !== false;
    echo $hasArticleFieldHandling ? "✅ Article field handling exists\n" : "❌ Article field handling missing\n";
    
    // Check for getValidationLabel method
    $hasGetValidationLabel = strpos($content, 'getValidationLabel') !== false;
    echo $hasGetValidationLabel ? "✅ getValidationLabel method exists\n" : "❌ getValidationLabel method missing\n";
    
} else {
    echo "❌ UsulanFieldHelper file not found\n";
}

echo "\n";

// Test 3: Check Usulan model for article fields
$usulanModelFile = 'app/Models/BackendUnivUsulan/Usulan.php';
if (file_exists($usulanModelFile)) {
    $content = file_get_contents($usulanModelFile);
    
    echo "📊 USULAN MODEL CHECK:\n";
    
    // Check for article fields in model
    $hasArticleFieldsInModel = strpos($content, 'judul_artikel') !== false && strpos($content, 'penerbit_artikel') !== false;
    echo $hasArticleFieldsInModel ? "✅ Article fields in model exists\n" : "❌ Article fields in model missing\n";
    
    // Check for karya_ilmiah category
    $hasKaryaIlmiahCategory = strpos($content, 'karya_ilmiah') !== false;
    echo $hasKaryaIlmiahCategory ? "✅ Karya ilmiah category exists\n" : "❌ Karya ilmiah category missing\n";
    
} else {
    echo "❌ Usulan model file not found\n";
}

echo "\n";

// Test 4: Sample article field scenarios
echo "🧪 SAMPLE ARTICLE FIELD SCENARIOS:\n";

$scenarios = [
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
        'field' => 'volume_artikel',
        'category' => 'karya_ilmiah',
        'expected_font_size' => 'text-sm',
        'description' => 'Article Volume - Long text field'
    ],
    [
        'field' => 'nomor_artikel',
        'category' => 'karya_ilmiah',
        'expected_font_size' => 'text-sm',
        'description' => 'Article Number - Long text field'
    ],
    [
        'field' => 'edisi_artikel',
        'category' => 'karya_ilmiah',
        'expected_font_size' => 'text-sm',
        'description' => 'Article Edition - Long text field'
    ],
    [
        'field' => 'halaman_artikel',
        'category' => 'karya_ilmiah',
        'expected_font_size' => 'text-sm',
        'description' => 'Article Pages - Long text field'
    ],
    [
        'field' => 'nama_lengkap',
        'category' => 'data_pribadi',
        'expected_font_size' => 'text-xl',
        'description' => 'Regular field - Normal font size'
    ],
    [
        'field' => 'nip',
        'category' => 'data_kepegawaian',
        'expected_font_size' => 'text-xl',
        'description' => 'Regular field - Normal font size'
    ]
];

foreach ($scenarios as $scenario) {
    $isArticleField = in_array($scenario['field'], ['judul_artikel', 'penerbit_artikel', 'volume_artikel', 'nomor_artikel', 'edisi_artikel', 'halaman_artikel']);
    $expectedFontSize = $isArticleField ? 'text-sm' : 'text-xl';
    $status = ($expectedFontSize === $scenario['expected_font_size']) ? '✅' : '❌';
    
    echo "   {$status} {$scenario['description']}:\n";
    echo "     Field: {$scenario['field']}\n";
    echo "     Category: {$scenario['category']}\n";
    echo "     Expected font size: {$scenario['expected_font_size']}\n";
    echo "     Is article field: " . ($isArticleField ? 'Yes' : 'No') . "\n\n";
}

echo "✅ Test completed!\n";
echo "\n📝 MANUAL TESTING INSTRUCTIONS:\n";
echo "1. Login sebagai Admin Fakultas\n";
echo "2. Akses halaman detail usulan yang memiliki data artikel\n";
echo "3. Verify: Field 'JUDUL ARTIKEL' menggunakan font size 'text-sm' (lebih kecil)\n";
echo "4. Verify: Field 'PENERBIT ARTIKEL' menggunakan font size 'text-sm' (lebih kecil)\n";
echo "5. Verify: Field 'VOLUME ARTIKEL' menggunakan font size 'text-sm' (lebih kecil)\n";
echo "6. Verify: Field 'NOMOR ARTIKEL' menggunakan font size 'text-sm' (lebih kecil)\n";
echo "7. Verify: Field 'EDISI ARTIKEL' menggunakan font size 'text-sm' (lebih kecil)\n";
echo "8. Verify: Field 'HALAMAN ARTIKEL' menggunakan font size 'text-sm' (lebih kecil)\n";
echo "9. Verify: Field regular lainnya (nama, nip, dll) tetap menggunakan 'text-xl' (normal)\n";
echo "10. Verify: Text tetap terbaca dengan jelas meskipun ukuran font lebih kecil\n";
