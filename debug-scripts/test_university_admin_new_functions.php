<?php

/**
 * Test Script untuk Verifikasi Fungsi Baru University Admin
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'test_university_admin_new_functions.php';"
 */

echo "🔍 UNIVERSITY ADMIN NEW FUNCTIONS TEST\n";
echo "=====================================\n\n";

// Test 1: Check PusatUsulanController for new actions
echo "🎯 CONTROLLER NEW ACTIONS CHECK:\n";

$controllerFile = 'app/Http/Controllers/Backend/AdminUnivUsulan/PusatUsulanController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    // Check for new action types
    $newActions = ['return_for_revision', 'not_recommended', 'send_to_assessor_team'];
    $allActionsFound = true;
    foreach ($newActions as $action) {
        $hasAction = strpos($content, $action) !== false;
        echo $hasAction ? "✅ Action '{$action}' exists\n" : "❌ Action '{$action}' missing\n";
        if (!$hasAction) $allActionsFound = false;
    }
    
    // Check for validation rules
    $hasAssessorValidation = strpos($content, 'assessor_ids') !== false;
    echo $hasAssessorValidation ? "✅ Assessor validation exists\n" : "❌ Assessor validation missing\n";
    
    // Check for new status handling
    $newStatuses = ['Perlu Perbaikan', 'Tidak Direkomendasikan', 'Sedang Dinilai'];
    foreach ($newStatuses as $status) {
        $hasStatus = strpos($content, $status) !== false;
        echo $hasStatus ? "✅ Status '{$status}' handling exists\n" : "❌ Status '{$status}' handling missing\n";
    }
    
} else {
    echo "❌ Controller file not found\n";
}

echo "\n";

// Test 2: Check action buttons for new functions
echo "📋 ACTION BUTTONS CHECK:\n";

$actionButtonsFile = 'resources/views/backend/components/usulan/_action-buttons.blade.php';
if (file_exists($actionButtonsFile)) {
    $content = file_get_contents($actionButtonsFile);
    
    // Check for new button functions
    $newFunctions = ['showReturnForRevisionForm', 'showNotRecommendedForm', 'showSendToAssessorForm'];
    foreach ($newFunctions as $function) {
        $hasFunction = strpos($content, $function) !== false;
        echo $hasFunction ? "✅ Function '{$function}' exists\n" : "❌ Function '{$function}' missing\n";
    }
    
    // Check for new button text
    $newButtonTexts = ['Return for Revision', 'Not Recommended', 'Send Proposal to Assessor Team'];
    foreach ($newButtonTexts as $text) {
        $hasText = strpos($content, $text) !== false;
        echo $hasText ? "✅ Button text '{$text}' exists\n" : "❌ Button text '{$text}' missing\n";
    }
    
} else {
    echo "❌ Action buttons file not found\n";
}

echo "\n";

// Test 3: Check hidden forms for new actions
echo "📝 HIDDEN FORMS CHECK:\n";

$hiddenFormsFile = 'resources/views/backend/components/usulan/_hidden-forms.blade.php';
if (file_exists($hiddenFormsFile)) {
    $content = file_get_contents($hiddenFormsFile);
    
    // Check for new form IDs
    $newFormIds = ['returnForRevisionForm', 'notRecommendedForm', 'sendToAssessorForm'];
    foreach ($newFormIds as $formId) {
        $hasForm = strpos($content, $formId) !== false;
        echo $hasForm ? "✅ Form ID '{$formId}' exists\n" : "❌ Form ID '{$formId}' missing\n";
    }
    
    // Check for assessor selection
    $hasAssessorSelection = strpos($content, 'assessor_ids[]') !== false;
    echo $hasAssessorSelection ? "✅ Assessor selection exists\n" : "❌ Assessor selection missing\n";
    
    // Check for character count displays
    $charCounts = ['charCount_return_revision', 'charCount_not_recommended', 'assessorCount'];
    foreach ($charCounts as $count) {
        $hasCount = strpos($content, $count) !== false;
        echo $hasCount ? "✅ Character count '{$count}' exists\n" : "❌ Character count '{$count}' missing\n";
    }
    
} else {
    echo "❌ Hidden forms file not found\n";
}

echo "\n";

// Test 4: Check validation scripts for new functions
echo "🔧 VALIDATION SCRIPTS CHECK:\n";

$validationScriptsFile = 'resources/views/backend/components/usulan/_validation-scripts.blade.php';
if (file_exists($validationScriptsFile)) {
    $content = file_get_contents($validationScriptsFile);
    
    // Check for new JavaScript functions
    $newJsFunctions = [
        'showReturnForRevisionForm',
        'hideReturnForRevisionForm',
        'showNotRecommendedForm',
        'hideNotRecommendedForm',
        'showSendToAssessorForm',
        'hideSendToAssessorForm',
        'validateAssessorSelection'
    ];
    
    foreach ($newJsFunctions as $function) {
        $hasFunction = strpos($content, $function) !== false;
        echo $hasFunction ? "✅ JS Function '{$function}' exists\n" : "❌ JS Function '{$function}' missing\n";
    }
    
    // Check for form submission handlers
    $formHandlers = [
        'returnForRevisionFormSubmit',
        'notRecommendedFormSubmit',
        'sendToAssessorFormSubmit'
    ];
    
    foreach ($formHandlers as $handler) {
        $hasHandler = strpos($content, $handler) !== false;
        echo $hasHandler ? "✅ Form handler '{$handler}' exists\n" : "❌ Form handler '{$handler}' missing\n";
    }
    
} else {
    echo "❌ Validation scripts file not found\n";
}

echo "\n";

// Test 5: Check Usulan model for new statuses
echo "📊 USULAN MODEL STATUS CHECK:\n";

$usulanModelFile = 'app/Models/BackendUnivUsulan/Usulan.php';
if (file_exists($usulanModelFile)) {
    $content = file_get_contents($usulanModelFile);
    
    // Check for new statuses in badge method
    $newStatuses = [
        'Tidak Direkomendasikan',
        'Sedang Dinilai',
        'Sedang Direview Universitas',
        'Dikembalikan ke Pegawai',
        'Diusulkan ke Universitas',
        'Ditolak Universitas'
    ];
    
    foreach ($newStatuses as $status) {
        $hasStatus = strpos($content, $status) !== false;
        echo $hasStatus ? "✅ Status '{$status}' in model exists\n" : "❌ Status '{$status}' in model missing\n";
    }
    
} else {
    echo "❌ Usulan model file not found\n";
}

echo "\n";

// Test 6: Sample data scenarios
echo "🧪 SAMPLE DATA SCENARIOS:\n";

$scenarios = [
    [
        'action' => 'return_for_revision',
        'description' => 'Return for Revision - Langsung ke Employee',
        'expected_status' => 'Perlu Perbaikan',
        'required_fields' => ['catatan_umum'],
        'validation' => 'min:10, max:2000'
    ],
    [
        'action' => 'not_recommended',
        'description' => 'Not Recommended - Tidak bisa submit lagi',
        'expected_status' => 'Tidak Direkomendasikan',
        'required_fields' => ['catatan_umum'],
        'validation' => 'min:10, max:2000'
    ],
    [
        'action' => 'send_to_assessor_team',
        'description' => 'Send to Assessor Team - Kirim ke penilai',
        'expected_status' => 'Sedang Dinilai',
        'required_fields' => ['assessor_ids'],
        'validation' => 'array|min:1|max:3'
    ]
];

foreach ($scenarios as $scenario) {
    echo "   📋 {$scenario['description']}:\n";
    echo "     Action: {$scenario['action']}\n";
    echo "     Expected Status: {$scenario['expected_status']}\n";
    echo "     Required Fields: " . implode(', ', $scenario['required_fields']) . "\n";
    echo "     Validation: {$scenario['validation']}\n\n";
}

echo "✅ Test completed!\n";
echo "\n📝 MANUAL TESTING INSTRUCTIONS:\n";
echo "1. Login sebagai University Admin (Promotion Proposal)\n";
echo "2. Akses usulan dengan status 'Diusulkan ke Universitas' atau 'Sedang Direview Universitas'\n";
echo "3. Test 'Return for Revision':\n";
echo "   - Klik tombol 'Return for Revision'\n";
echo "   - Isi catatan minimal 10 karakter\n";
echo "   - Submit → status berubah ke 'Perlu Perbaikan'\n";
echo "4. Test 'Not Recommended':\n";
echo "   - Klik tombol 'Not Recommended'\n";
echo "   - Isi alasan minimal 10 karakter\n";
echo "   - Submit → status berubah ke 'Tidak Direkomendasikan'\n";
echo "5. Test 'Send to Assessor Team':\n";
echo "   - Klik tombol 'Send Proposal to Assessor Team'\n";
echo "   - Pilih 1-3 penilai dari daftar\n";
echo "   - Submit → status berubah ke 'Sedang Dinilai'\n";
echo "6. Verify: Penilai yang dipilih dapat melihat usulan di dashboard mereka\n";
echo "7. Verify: Employee dapat melihat status dan catatan di dashboard mereka\n";
