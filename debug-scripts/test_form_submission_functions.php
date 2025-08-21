<?php

/**
 * Test Script untuk Verifikasi Form Submission Functions
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'test_form_submission_functions.php';"
 */

echo "🔍 FORM SUBMISSION FUNCTIONS TEST\n";
echo "================================\n\n";

// Test 1: Check form IDs and action types
echo "📋 FORM IDS AND ACTION TYPES CHECK:\n";

$hiddenFormsFile = 'resources/views/backend/components/usulan/_hidden-forms.blade.php';
if (file_exists($hiddenFormsFile)) {
    $content = file_get_contents($hiddenFormsFile);
    
    // Check for correct form IDs
    $formIds = [
        'returnForm' => 'return_to_pegawai',
        'notRecommendedForm' => 'not_recommended',
        'sendToAssessorForm' => 'send_to_assessor_team',
        'sendToSenateForm' => 'send_to_senate_team'
    ];
    
    foreach ($formIds as $formId => $actionType) {
        $hasFormId = strpos($content, $formId) !== false;
        $hasActionType = strpos($content, $actionType) !== false;
        
        echo $hasFormId ? "✅ Form ID '{$formId}' exists\n" : "❌ Form ID '{$formId}' missing\n";
        echo $hasActionType ? "✅ Action type '{$actionType}' exists\n" : "❌ Action type '{$actionType}' missing\n";
        echo "\n";
    }
    
} else {
    echo "❌ Hidden forms file not found\n";
}

echo "\n";

// Test 2: Check JavaScript functions
echo "🔧 JAVASCRIPT FUNCTIONS CHECK:\n";

$validationScriptsFile = 'resources/views/backend/components/usulan/_validation-scripts.blade.php';
if (file_exists($validationScriptsFile)) {
    $content = file_get_contents($validationScriptsFile);
    
    // Check for form display/hide functions
    $displayFunctions = [
        'showReturnForm',
        'hideReturnForm',
        'showNotRecommendedForm',
        'hideNotRecommendedForm',
        'showSendToAssessorForm',
        'hideSendToAssessorForm',
        'showSendToSenateForm',
        'hideSendToSenateForm'
    ];
    
    foreach ($displayFunctions as $function) {
        $hasFunction = strpos($content, $function) !== false;
        echo $hasFunction ? "✅ Function '{$function}' exists\n" : "❌ Function '{$function}' missing\n";
    }
    
    // Check for form submission handlers
    $formHandlers = [
        'returnFormSubmit',
        'notRecommendedFormSubmit',
        'sendToAssessorFormSubmit',
        'sendToSenateFormSubmit'
    ];
    
    echo "\nForm submission handlers:\n";
    foreach ($formHandlers as $handler) {
        $hasHandler = strpos($content, $handler) !== false;
        echo $hasHandler ? "✅ Handler '{$handler}' exists\n" : "❌ Handler '{$handler}' missing\n";
    }
    
    // Check for validation functions
    $validationFunctions = [
        'validateAssessorSelection',
        'charCount_return',
        'charCount_not_recommended',
        'assessorCount'
    ];
    
    echo "\nValidation functions:\n";
    foreach ($validationFunctions as $function) {
        $hasFunction = strpos($content, $function) !== false;
        echo $hasFunction ? "✅ Validation '{$function}' exists\n" : "❌ Validation '{$function}' missing\n";
    }
    
} else {
    echo "❌ Validation scripts file not found\n";
}

echo "\n";

// Test 3: Check controller action handling
echo "🎯 CONTROLLER ACTION HANDLING CHECK:\n";

$controllerFile = 'app/Http/Controllers/Backend/AdminUnivUsulan/PusatUsulanController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    // Check for all required action types in validation
    $requiredActions = [
        'return_to_pegawai',
        'not_recommended',
        'send_to_assessor_team',
        'send_to_senate_team'
    ];
    
    foreach ($requiredActions as $action) {
        $hasAction = strpos($content, $action) !== false;
        echo $hasAction ? "✅ Action '{$action}' in validation exists\n" : "❌ Action '{$action}' in validation missing\n";
    }
    
    // Check for switch cases
    $switchCases = [
        'case \'return_to_pegawai\':',
        'case \'not_recommended\':',
        'case \'send_to_assessor_team\':',
        'case \'send_to_senate_team\':'
    ];
    
    echo "\nSwitch cases:\n";
    foreach ($switchCases as $case) {
        $hasCase = strpos($content, $case) !== false;
        echo $hasCase ? "✅ Case '{$case}' exists\n" : "❌ Case '{$case}' missing\n";
    }
    
} else {
    echo "❌ Controller file not found\n";
}

echo "\n";

// Test 4: Check action buttons onclick functions
echo "📋 ACTION BUTTONS ONCLICK CHECK:\n";

$actionButtonsFile = 'resources/views/backend/components/usulan/_action-buttons.blade.php';
if (file_exists($actionButtonsFile)) {
    $content = file_get_contents($actionButtonsFile);
    
    // Check for onclick functions
    $onclickFunctions = [
        'showReturnForm()',
        'showNotRecommendedForm()',
        'showSendToAssessorForm()',
        'showSendToSenateForm()'
    ];
    
    foreach ($onclickFunctions as $function) {
        $hasFunction = strpos($content, $function) !== false;
        echo $hasFunction ? "✅ Onclick '{$function}' exists\n" : "❌ Onclick '{$function}' missing\n";
    }
    
} else {
    echo "❌ Action buttons file not found\n";
}

echo "\n";

// Test 5: Form submission flow summary
echo "🧪 FORM SUBMISSION FLOW SUMMARY:\n";

$formFlows = [
    [
        'button' => 'Kembalikan untuk Revisi',
        'onclick' => 'showReturnForm()',
        'form_id' => 'returnForm',
        'action_type' => 'return_to_pegawai',
        'status' => 'Dikembalikan ke Pegawai',
        'validation' => 'catatan_umum (min:10, max:2000)'
    ],
    [
        'button' => 'Belum Direkomendasikan',
        'onclick' => 'showNotRecommendedForm()',
        'form_id' => 'notRecommendedForm',
        'action_type' => 'not_recommended',
        'status' => 'Tidak Direkomendasikan',
        'validation' => 'catatan_umum (min:10, max:2000)'
    ],
    [
        'button' => 'Kirim Usulan ke Tim Penilai',
        'onclick' => 'showSendToAssessorForm()',
        'form_id' => 'sendToAssessorForm',
        'action_type' => 'send_to_assessor_team',
        'status' => 'Sedang Dinilai',
        'validation' => 'assessor_ids (array, min:1, max:3)'
    ],
    [
        'button' => 'Kirim Usulan ke Tim Senat',
        'onclick' => 'showSendToSenateForm()',
        'form_id' => 'sendToSenateForm',
        'action_type' => 'send_to_senate_team',
        'status' => 'Sedang Direview Senat',
        'validation' => 'requires assessor recommendation'
    ]
];

foreach ($formFlows as $flow) {
    echo "   📋 {$flow['button']}:\n";
    echo "     Onclick: {$flow['onclick']}\n";
    echo "     Form ID: {$flow['form_id']}\n";
    echo "     Action Type: {$flow['action_type']}\n";
    echo "     Status: {$flow['status']}\n";
    echo "     Validation: {$flow['validation']}\n\n";
}

echo "✅ Test completed!\n";
echo "\n📝 MANUAL TESTING INSTRUCTIONS:\n";
echo "1. Login sebagai University Admin (Promotion Proposal)\n";
echo "2. Akses usulan dengan status 'Diusulkan ke Universitas' atau 'Sedang Direview Universitas'\n";
echo "3. Test setiap tombol:\n";
echo "   a) Klik tombol → Modal form muncul\n";
echo "   b) Isi form sesuai validasi\n";
echo "   c) Klik Submit → Form terkirim ke controller\n";
echo "   d) Status usulan berubah sesuai action\n";
echo "4. Verify form validation:\n";
echo "   - Character count untuk textarea\n";
echo "   - Assessor selection (1-3) untuk Tim Penilai\n";
echo "   - Senate button disabled jika penilai belum merekomendasikan\n";
echo "5. Verify confirmation dialogs muncul sebelum submit\n";
echo "6. Verify success/error messages setelah submit\n";
