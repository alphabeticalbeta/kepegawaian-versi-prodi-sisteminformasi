<?php

/**
 * Simple Validation Test Script
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'test_simple_validation.php';"
 */

echo "🔍 SIMPLE VALIDATION TEST\n";
echo "========================\n\n";

// Test 1: Check if files exist
$files = [
    'resources/views/backend/layouts/admin-fakultas/partials/_hidden-forms.blade.php',
    'resources/views/backend/layouts/admin-fakultas/partials/_validation-scripts.blade.php',
    'app/Http/Controllers/Backend/AdminFakultas/AdminFakultasController.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ {$file}\n";
    } else {
        echo "❌ {$file}\n";
    }
}

echo "\n";

// Test 2: Check form structure
$hiddenFormsFile = 'resources/views/backend/layouts/admin-fakultas/partials/_hidden-forms.blade.php';
if (file_exists($hiddenFormsFile)) {
    $content = file_get_contents($hiddenFormsFile);
    
    echo "📋 FORM STRUCTURE CHECK:\n";
    
    // Check for returnUsulanForm
    $hasReturnForm = strpos($content, 'returnUsulanForm') !== false;
    echo $hasReturnForm ? "✅ returnUsulanForm exists\n" : "❌ returnUsulanForm missing\n";
    
    // Check for rejectUsulanForm
    $hasRejectForm = strpos($content, 'rejectUsulanForm') !== false;
    echo $hasRejectForm ? "✅ rejectUsulanForm exists\n" : "❌ rejectUsulanForm missing\n";
    
    // Check for catatan_umum field
    $hasCatatanUmum = strpos($content, 'name="catatan_umum"') !== false;
    echo $hasCatatanUmum ? "✅ catatan_umum field exists\n" : "❌ catatan_umum field missing\n";
    
    // Check for catatan_reject field
    $hasCatatanReject = strpos($content, 'name="catatan_reject"') !== false;
    echo $hasCatatanReject ? "✅ catatan_reject field exists\n" : "❌ catatan_reject field missing\n";
}

echo "\n";

// Test 3: Check JavaScript functions
$scriptFile = 'resources/views/backend/layouts/admin-fakultas/partials/_validation-scripts.blade.php';
if (file_exists($scriptFile)) {
    $content = file_get_contents($scriptFile);
    
    echo "🔧 JAVASCRIPT CHECK:\n";
    
    // Check for submitReturnForm function
    $hasSubmitReturn = strpos($content, 'function submitReturnForm()') !== false;
    echo $hasSubmitReturn ? "✅ submitReturnForm function exists\n" : "❌ submitReturnForm function missing\n";
    
    // Check for submitRejectForm function
    $hasSubmitReject = strpos($content, 'function submitRejectForm()') !== false;
    echo $hasSubmitReject ? "✅ submitRejectForm function exists\n" : "❌ submitRejectForm function missing\n";
    
    // Check for trim() usage
    $hasTrim = strpos($content, '.trim()') !== false;
    echo $hasTrim ? "✅ trim() usage exists\n" : "❌ trim() usage missing\n";
    
    // Check for length validation
    $hasLengthCheck = strpos($content, '.length < 10') !== false;
    echo $hasLengthCheck ? "✅ length < 10 validation exists\n" : "❌ length < 10 validation missing\n";
}

echo "\n";

// Test 4: Check controller validation
$controllerFile = 'app/Http/Controllers/Backend/AdminFakultas/AdminFakultasController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    echo "🎯 CONTROLLER CHECK:\n";
    
    // Check for reject_to_pegawai case
    $hasRejectCase = strpos($content, 'case \'reject_to_pegawai\':') !== false;
    echo $hasRejectCase ? "✅ reject_to_pegawai case exists\n" : "❌ reject_to_pegawai case missing\n";
    
    // Check for catatan_reject validation
    $hasCatatanRejectValidation = strpos($content, 'catatan_reject.*required.*string.*min:10') !== false;
    echo $hasCatatanRejectValidation ? "✅ catatan_reject validation exists\n" : "❌ catatan_reject validation missing\n";
    
    // Check for Belum Direkomendasikan status
    $hasStatus = strpos($content, 'Belum Direkomendasikan') !== false;
    echo $hasStatus ? "✅ Belum Direkomendasikan status exists\n" : "❌ Belum Direkomendasikan status missing\n";
}

echo "\n";

// Test 5: Sample validation test
echo "🧪 SAMPLE VALIDATION TEST:\n";

$testCases = [
    'Short' => false,
    'This is a valid note with more than 10 characters' => true,
    '   ' => false,
    'Valid' => false, // This should be false because it's only 5 chars
    'Valid text with more than 10 characters' => true,
    '123456789' => false,
    '1234567890' => true,
    '12345678901' => true
];

foreach ($testCases as $input => $expected) {
    $trimmed = trim($input);
    $length = strlen($trimmed);
    $isValid = $length >= 10;
    $status = ($isValid === $expected) ? '✅' : '❌';
    
    echo "   {$status} Input: '{$input}' → Trimmed: '{$trimmed}' → Length: {$length} → Expected: " . ($expected ? 'VALID' : 'INVALID') . " → Actual: " . ($isValid ? 'VALID' : 'INVALID') . "\n";
}

echo "\n✅ Test completed!\n";
