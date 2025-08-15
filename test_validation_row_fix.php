<?php

/**
 * Test Script untuk Verifikasi Validation Row Fix
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'test_validation_row_fix.php';"
 */

echo "🔍 VALIDATION ROW FIX TEST\n";
echo "=========================\n\n";

// Test 1: Check validation row structure
$validationRowFile = 'resources/views/backend/components/usulan/_validation-row.blade.php';
if (file_exists($validationRowFile)) {
    $content = file_get_contents($validationRowFile);
    
    echo "📋 VALIDATION ROW STRUCTURE CHECK:\n";
    
    // Check for textarea with proper ID
    $hasTextareaWithId = strpos($content, 'id="keterangan_') !== false;
    echo $hasTextareaWithId ? "✅ Textarea with proper ID exists\n" : "❌ Textarea with proper ID missing\n";
    
    // Check for disabled attribute
    $hasDisabledAttribute = strpos($content, 'disabled') !== false;
    echo $hasDisabledAttribute ? "✅ Disabled attribute exists\n" : "❌ Disabled attribute missing\n";
    
    // Check for placeholder
    $hasPlaceholder = strpos($content, 'placeholder=') !== false;
    echo $hasPlaceholder ? "✅ Placeholder attribute exists\n" : "❌ Placeholder attribute missing\n";
    
    // Check for select with proper name
    $hasSelectWithName = strpos($content, 'name="validation[') !== false;
    echo $hasSelectWithName ? "✅ Select with proper name exists\n" : "❌ Select with proper name missing\n";
    
} else {
    echo "❌ Validation row file not found\n";
}

echo "\n";

// Test 2: Check validation scripts
$scriptFile = 'resources/views/backend/layouts/admin-fakultas/partials/_validation-scripts.blade.php';
if (file_exists($scriptFile)) {
    $content = file_get_contents($scriptFile);
    
    echo "🔧 VALIDATION SCRIPTS CHECK:\n";
    
    // Check for toggleKeterangan function
    $hasToggleFunction = strpos($content, 'function toggleKeterangan') !== false;
    echo $hasToggleFunction ? "✅ toggleKeterangan function exists\n" : "❌ toggleKeterangan function missing\n";
    
    // Check for disabled property usage
    $hasDisabledProperty = strpos($content, '.disabled = ') !== false;
    echo $hasDisabledProperty ? "✅ Disabled property usage exists\n" : "❌ Disabled property usage missing\n";
    
    // Check for required property usage
    $hasRequiredProperty = strpos($content, '.required = ') !== false;
    echo $hasRequiredProperty ? "✅ Required property usage exists\n" : "❌ Required property usage missing\n";
    
    // Check for placeholder property usage
    $hasPlaceholderProperty = strpos($content, '.placeholder = ') !== false;
    echo $hasPlaceholderProperty ? "✅ Placeholder property usage exists\n" : "❌ Placeholder property usage missing\n";
    
    // Check for event listener
    $hasEventListener = strpos($content, 'addEventListener(\'change\'') !== false;
    echo $hasEventListener ? "✅ Change event listener exists\n" : "❌ Change event listener missing\n";
    
    // Check for regex pattern matching
    $hasRegexPattern = strpos($content, 'validation\\[\\w+\\]\\[\\w+\\]\\[status\\]') !== false;
    echo $hasRegexPattern ? "✅ Regex pattern matching exists\n" : "❌ Regex pattern matching missing\n";
    
} else {
    echo "❌ Validation scripts file not found\n";
}

echo "\n";

// Test 3: Check for debugging logs
if (file_exists($scriptFile)) {
    $content = file_get_contents($scriptFile);
    
    echo "🐛 DEBUGGING FEATURES CHECK:\n";
    
    // Check for console.log statements
    $hasConsoleLog = strpos($content, 'console.log') !== false;
    echo $hasConsoleLog ? "✅ Console.log debugging exists\n" : "❌ Console.log debugging missing\n";
    
    // Check for error logging
    $hasErrorLog = strpos($content, 'console.error') !== false;
    echo $hasErrorLog ? "✅ Error logging exists\n" : "❌ Error logging missing\n";
    
    // Check for fallback mechanism
    $hasFallback = strpos($content, 'querySelector') !== false;
    echo $hasFallback ? "✅ Fallback mechanism exists\n" : "❌ Fallback mechanism missing\n";
    
} else {
    echo "❌ Script file not found for debugging check\n";
}

echo "\n";

// Test 4: Sample validation scenarios
echo "🧪 SAMPLE VALIDATION SCENARIOS:\n";

$scenarios = [
    [
        'description' => 'Select "Sesuai"',
        'status' => 'sesuai',
        'expected_disabled' => true,
        'expected_required' => false,
        'expected_placeholder' => 'Pilih "Tidak Sesuai" untuk mengisi keterangan'
    ],
    [
        'description' => 'Select "Tidak Sesuai"',
        'status' => 'tidak_sesuai',
        'expected_disabled' => false,
        'expected_required' => true,
        'expected_placeholder' => 'Jelaskan mengapa item ini tidak sesuai...'
    ]
];

foreach ($scenarios as $scenario) {
    echo "   📋 {$scenario['description']}:\n";
    echo "     Status: {$scenario['status']}\n";
    echo "     Expected disabled: " . ($scenario['expected_disabled'] ? 'true' : 'false') . "\n";
    echo "     Expected required: " . ($scenario['expected_required'] ? 'true' : 'false') . "\n";
    echo "     Expected placeholder: {$scenario['expected_placeholder']}\n\n";
}

echo "✅ Test completed!\n";
echo "\n📝 MANUAL TESTING INSTRUCTIONS:\n";
echo "1. Login sebagai Admin Fakultas\n";
echo "2. Akses halaman detail usulan\n";
echo "3. Pilih dropdown status pada salah satu field\n";
echo "4. Pilih 'Tidak Sesuai' → textarea harus enabled dan required\n";
echo "5. Pilih 'Sesuai' → textarea harus disabled dan tidak required\n";
echo "6. Check browser console untuk debug logs\n";
