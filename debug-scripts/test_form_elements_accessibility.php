<?php

/**
 * Test Script untuk Verifikasi Form Elements Accessibility
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'test_form_elements_accessibility.php';"
 */

echo "🔍 FORM ELEMENTS ACCESSIBILITY TEST\n";
echo "==================================\n\n";

// Test 1: Check if hidden forms file exists and has correct structure
echo "📋 HIDDEN FORMS STRUCTURE CHECK:\n";

$hiddenFormsFile = 'resources/views/backend/components/usulan/_hidden-forms.blade.php';
if (file_exists($hiddenFormsFile)) {
    $content = file_get_contents($hiddenFormsFile);
    
    // Check for required form IDs
    $requiredFormIds = [
        'returnForm',
        'notRecommendedForm', 
        'sendToAssessorForm',
        'sendToSenateForm'
    ];
    
    foreach ($requiredFormIds as $formId) {
        $hasFormId = strpos($content, "id=\"{$formId}\"") !== false;
        echo $hasFormId ? "✅ Form ID '{$formId}' exists\n" : "❌ Form ID '{$formId}' missing\n";
    }
    
    // Check for form submit IDs
    $formSubmitIds = [
        'returnFormSubmit',
        'notRecommendedFormSubmit',
        'sendToAssessorFormSubmit', 
        'sendToSenateFormSubmit'
    ];
    
    echo "\nForm submit IDs:\n";
    foreach ($formSubmitIds as $submitId) {
        $hasSubmitId = strpos($content, "id=\"{$submitId}\"") !== false;
        echo $hasSubmitId ? "✅ Submit ID '{$submitId}' exists\n" : "❌ Submit ID '{$submitId}' missing\n";
    }
    
    // Check for textarea IDs
    $textareaIds = [
        'catatan_umum_return',
        'catatan_umum_not_recommended'
    ];
    
    echo "\nTextarea IDs:\n";
    foreach ($textareaIds as $textareaId) {
        $hasTextareaId = strpos($content, "id=\"{$textareaId}\"") !== false;
        echo $hasTextareaId ? "✅ Textarea ID '{$textareaId}' exists\n" : "❌ Textarea ID '{$textareaId}' missing\n";
    }
    
    // Check for character count IDs
    $charCountIds = [
        'charCount_return',
        'charCount_not_recommended',
        'assessorCount'
    ];
    
    echo "\nCharacter count IDs:\n";
    foreach ($charCountIds as $charCountId) {
        $hasCharCountId = strpos($content, "id=\"{$charCountId}\"") !== false;
        echo $hasCharCountId ? "✅ Char count ID '{$charCountId}' exists\n" : "❌ Char count ID '{$charCountId}' missing\n";
    }
    
} else {
    echo "❌ Hidden forms file not found\n";
}

echo "\n";

// Test 2: Check if detail page includes hidden forms
echo "📄 DETAIL PAGE INCLUDE CHECK:\n";

$detailPageFile = 'resources/views/backend/layouts/admin-univ-usulan/pusat-usulan/detail-usulan.blade.php';
if (file_exists($detailPageFile)) {
    $content = file_get_contents($detailPageFile);
    
    // Check for hidden forms include
    $hasHiddenFormsInclude = strpos($content, '_hidden-forms') !== false;
    echo $hasHiddenFormsInclude ? "✅ Hidden forms include exists\n" : "❌ Hidden forms include missing\n";
    
    // Check for validation scripts include
    $hasValidationScriptsInclude = strpos($content, '_validation-scripts') !== false;
    echo $hasValidationScriptsInclude ? "✅ Validation scripts include exists\n" : "❌ Validation scripts include missing\n";
    
    // Check for correct include path
    $hasCorrectPath = strpos($content, 'backend.components.usulan._hidden-forms') !== false;
    echo $hasCorrectPath ? "✅ Correct include path exists\n" : "❌ Correct include path missing\n";
    
} else {
    echo "❌ Detail page file not found\n";
}

echo "\n";

// Test 3: Check JavaScript functions for element access
echo "🔧 JAVASCRIPT ELEMENT ACCESS CHECK:\n";

$validationScriptsFile = 'resources/views/backend/components/usulan/_validation-scripts.blade.php';
if (file_exists($validationScriptsFile)) {
    $content = file_get_contents($validationScriptsFile);
    
    // Check for getElementById calls
    $elementAccessPatterns = [
        'getElementById(\'returnForm\')',
        'getElementById(\'notRecommendedForm\')',
        'getElementById(\'sendToAssessorForm\')',
        'getElementById(\'sendToSenateForm\')',
        'getElementById(\'catatan_umum_return\')',
        'getElementById(\'catatan_umum_not_recommended\')',
        'getElementById(\'charCount_return\')',
        'getElementById(\'charCount_not_recommended\')',
        'getElementById(\'assessorCount\')',
        'getElementById(\'submitAssessorBtn\')'
    ];
    
    foreach ($elementAccessPatterns as $pattern) {
        $hasPattern = strpos($content, $pattern) !== false;
        echo $hasPattern ? "✅ Element access '{$pattern}' exists\n" : "❌ Element access '{$pattern}' missing\n";
    }
    
} else {
    echo "❌ Validation scripts file not found\n";
}

echo "\n";

// Test 4: Check for null checks in JavaScript
echo "🛡️ JAVASCRIPT NULL SAFETY CHECK:\n";

if (file_exists($validationScriptsFile)) {
    $content = file_get_contents($validationScriptsFile);
    
    // Check for null safety patterns
    $nullSafetyPatterns = [
        'if (returnForm)',
        'if (notRecommendedForm)',
        'if (sendToAssessorForm)',
        'if (sendToSenateForm)',
        'if (returnTextarea)',
        'if (notRecommendedTextarea)'
    ];
    
    foreach ($nullSafetyPatterns as $pattern) {
        $hasPattern = strpos($content, $pattern) !== false;
        echo $hasPattern ? "✅ Null safety '{$pattern}' exists\n" : "❌ Null safety '{$pattern}' missing\n";
    }
    
} else {
    echo "❌ Validation scripts file not found\n";
}

echo "\n";

// Test 5: Check for DOMContentLoaded wrapper
echo "📦 DOM CONTENT LOADED CHECK:\n";

if (file_exists($validationScriptsFile)) {
    $content = file_get_contents($validationScriptsFile);
    
    $hasDOMContentLoaded = strpos($content, 'DOMContentLoaded') !== false;
    echo $hasDOMContentLoaded ? "✅ DOMContentLoaded wrapper exists\n" : "❌ DOMContentLoaded wrapper missing\n";
    
    $hasEventListener = strpos($content, 'addEventListener') !== false;
    echo $hasEventListener ? "✅ Event listener setup exists\n" : "❌ Event listener setup missing\n";
    
} else {
    echo "❌ Validation scripts file not found\n";
}

echo "\n";

// Test 6: Summary and recommendations
echo "🧪 SUMMARY AND RECOMMENDATIONS:\n";

echo "1. Ensure hidden forms are included in detail page\n";
echo "2. Verify all form IDs match between HTML and JavaScript\n";
echo "3. Check that JavaScript runs after DOM is loaded\n";
echo "4. Add null checks for all getElementById calls\n";
echo "5. Verify include paths are case-sensitive correct\n";
echo "6. Test form display/hide functions manually\n";

echo "\n✅ Test completed!\n";
echo "\n📝 DEBUGGING STEPS:\n";
echo "1. Open browser developer tools (F12)\n";
echo "2. Check Console tab for JavaScript errors\n";
echo "3. Check Elements tab to verify form IDs exist\n";
echo "4. Test each button click and verify modal appears\n";
echo "5. Check if formAction variable is passed correctly\n";
echo "6. Verify all required JavaScript functions are loaded\n";
