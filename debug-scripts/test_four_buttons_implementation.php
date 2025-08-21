<?php

/**
 * Test Script untuk Verifikasi 4 Tombol University Admin
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'test_four_buttons_implementation.php';"
 */

echo "🔍 FOUR BUTTONS IMPLEMENTATION TEST\n";
echo "==================================\n\n";

// Test 1: Check action buttons for exactly 4 buttons
echo "📋 ACTION BUTTONS CHECK:\n";

$actionButtonsFile = 'resources/views/backend/components/usulan/_action-buttons.blade.php';
if (file_exists($actionButtonsFile)) {
    $content = file_get_contents($actionButtonsFile);
    
    // Check for the 4 required buttons
    $requiredButtons = [
        'Kembalikan untuk Revisi',
        'Belum Direkomendasikan', 
        'Kirim Usulan ke Tim Penilai',
        'Kirim Usulan ke Tim Senat'
    ];
    
    $foundButtons = [];
    foreach ($requiredButtons as $button) {
        $hasButton = strpos($content, $button) !== false;
        echo $hasButton ? "✅ Button '{$button}' exists\n" : "❌ Button '{$button}' missing\n";
        if ($hasButton) $foundButtons[] = $button;
    }
    
    // Check for removed buttons
    $removedButtons = [
        'Return for Revision',
        'Not Recommended',
        'Send Proposal to Assessor Team',
        'Direkomendasikan',
        'reject_proposal',
        'recommend_proposal'
    ];
    
    echo "\nRemoved buttons check:\n";
    foreach ($removedButtons as $button) {
        $hasButton = strpos($content, $button) !== false;
        echo $hasButton ? "❌ Button '{$button}' still exists (should be removed)\n" : "✅ Button '{$button}' correctly removed\n";
    }
    
    echo "\n📊 Summary: Found " . count($foundButtons) . " out of 4 required buttons\n";
    if (count($foundButtons) === 4) {
        echo "✅ All 4 required buttons are present\n";
    } else {
        echo "❌ Missing buttons: " . implode(', ', array_diff($requiredButtons, $foundButtons)) . "\n";
    }
    
} else {
    echo "❌ Action buttons file not found\n";
}

echo "\n";

// Test 2: Check controller for new action
echo "🎯 CONTROLLER ACTIONS CHECK:\n";

$controllerFile = 'app/Http/Controllers/Backend/AdminUnivUsulan/PusatUsulanController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    // Check for send_to_senate_team action
    $hasSenateAction = strpos($content, 'send_to_senate_team') !== false;
    echo $hasSenateAction ? "✅ send_to_senate_team action exists\n" : "❌ send_to_senate_team action missing\n";
    
    // Check for Senate status handling
    $hasSenateStatus = strpos($content, 'Sedang Direview Senat') !== false;
    echo $hasSenateStatus ? "✅ Sedang Direview Senat status handling exists\n" : "❌ Sedang Direview Senat status handling missing\n";
    
    // Check for Senate validation
    $hasSenateValidation = strpos($content, 'isRecommendedByReviewer') !== false;
    echo $hasSenateValidation ? "✅ Senate validation (isRecommendedByReviewer) exists\n" : "❌ Senate validation missing\n";
    
} else {
    echo "❌ Controller file not found\n";
}

echo "\n";

// Test 3: Check hidden forms for Senate form
echo "📝 HIDDEN FORMS CHECK:\n";

$hiddenFormsFile = 'resources/views/backend/components/usulan/_hidden-forms.blade.php';
if (file_exists($hiddenFormsFile)) {
    $content = file_get_contents($hiddenFormsFile);
    
    // Check for Senate form
    $hasSenateForm = strpos($content, 'sendToSenateForm') !== false;
    echo $hasSenateForm ? "✅ Senate form exists\n" : "❌ Senate form missing\n";
    
    // Check for Senate form action
    $hasSenateAction = strpos($content, 'send_to_senate_team') !== false;
    echo $hasSenateAction ? "✅ Senate form action exists\n" : "❌ Senate form action missing\n";
    
    // Check for Senate status display
    $hasSenateStatusDisplay = strpos($content, 'Status Tim Penilai') !== false;
    echo $hasSenateStatusDisplay ? "✅ Senate status display exists\n" : "❌ Senate status display missing\n";
    
} else {
    echo "❌ Hidden forms file not found\n";
}

echo "\n";

// Test 4: Check validation scripts for Senate functions
echo "🔧 VALIDATION SCRIPTS CHECK:\n";

$validationScriptsFile = 'resources/views/backend/components/usulan/_validation-scripts.blade.php';
if (file_exists($validationScriptsFile)) {
    $content = file_get_contents($validationScriptsFile);
    
    // Check for Senate JavaScript functions
    $senateFunctions = ['showSendToSenateForm', 'hideSendToSenateForm'];
    foreach ($senateFunctions as $function) {
        $hasFunction = strpos($content, $function) !== false;
        echo $hasFunction ? "✅ Function '{$function}' exists\n" : "❌ Function '{$function}' missing\n";
    }
    
    // Check for Senate form handler
    $hasSenateHandler = strpos($content, 'sendToSenateFormSubmit') !== false;
    echo $hasSenateHandler ? "✅ Senate form handler exists\n" : "❌ Senate form handler missing\n";
    
} else {
    echo "❌ Validation scripts file not found\n";
}

echo "\n";

// Test 5: Check Usulan model for new status
echo "📊 USULAN MODEL STATUS CHECK:\n";

$usulanModelFile = 'app/Models/BackendUnivUsulan/Usulan.php';
if (file_exists($usulanModelFile)) {
    $content = file_get_contents($usulanModelFile);
    
    // Check for new Senate status
    $hasSenateStatus = strpos($content, 'Sedang Direview Senat') !== false;
    echo $hasSenateStatus ? "✅ Sedang Direview Senat status in model exists\n" : "❌ Sedang Direview Senat status in model missing\n";
    
} else {
    echo "❌ Usulan model file not found\n";
}

echo "\n";

// Test 6: Button functionality summary
echo "🧪 BUTTON FUNCTIONALITY SUMMARY:\n";

$buttonFunctions = [
    [
        'button' => 'Kembalikan untuk Revisi',
        'action' => 'return_to_pegawai',
        'status' => 'Dikembalikan ke Pegawai',
        'description' => 'Mengembalikan usulan ke pegawai untuk perbaikan'
    ],
    [
        'button' => 'Belum Direkomendasikan',
        'action' => 'not_recommended',
        'status' => 'Tidak Direkomendasikan',
        'description' => 'Menandai usulan tidak direkomendasikan'
    ],
    [
        'button' => 'Kirim Usulan ke Tim Penilai',
        'action' => 'send_to_assessor_team',
        'status' => 'Sedang Dinilai',
        'description' => 'Mengirim usulan ke tim penilai (1-3 penilai)'
    ],
    [
        'button' => 'Kirim Usulan ke Tim Senat',
        'action' => 'send_to_senate_team',
        'status' => 'Sedang Direview Senat',
        'description' => 'Mengirim ke tim senat (setelah penilai merekomendasikan)'
    ]
];

foreach ($buttonFunctions as $func) {
    echo "   📋 {$func['button']}:\n";
    echo "     Action: {$func['action']}\n";
    echo "     Status: {$func['status']}\n";
    echo "     Description: {$func['description']}\n\n";
}

echo "✅ Test completed!\n";
echo "\n📝 MANUAL TESTING INSTRUCTIONS:\n";
echo "1. Login sebagai University Admin (Promotion Proposal)\n";
echo "2. Akses usulan dengan status 'Diusulkan ke Universitas' atau 'Sedang Direview Universitas'\n";
echo "3. Verify hanya ada 4 tombol:\n";
echo "   - Kembalikan untuk Revisi (yellow)\n";
echo "   - Belum Direkomendasikan (red)\n";
echo "   - Kirim Usulan ke Tim Penilai (green)\n";
echo "   - Kirim Usulan ke Tim Senat (purple, disabled jika penilai belum merekomendasikan)\n";
echo "4. Test 'Kirim Usulan ke Tim Senat':\n";
echo "   - Jika penilai belum merekomendasikan: tombol disabled\n";
echo "   - Jika penilai sudah merekomendasikan: tombol aktif\n";
echo "   - Klik tombol → status berubah ke 'Sedang Direview Senat'\n";
echo "5. Verify: Tombol-tombol lain yang tidak diperlukan sudah dihapus\n";
