<?php

/**
 * Debug Script untuk Validasi Admin Fakultas
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'test_validation_debug.php';"
 */

use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\Usulan;

class ValidationDebugTest
{
    public function testValidationDebug()
    {
        echo "🔍 VALIDATION DEBUG TEST\n";
        echo "========================\n\n";

        try {
            // Test 1: Check authentication
            if (!Auth::check()) {
                echo "❌ User tidak terautentikasi\n";
                return;
            }

            $user = Auth::user();
            echo "✅ User: {$user->nama_lengkap}\n";
            echo "   Role: " . ($user->role ?? 'N/A') . "\n\n";

            // Test 2: Find usulan
            $usulan = Usulan::with(['periodeUsulan', 'pegawai'])->first();
            if (!$usulan) {
                echo "❌ Tidak ada usulan\n";
                return;
            }

            echo "✅ Usulan ID: {$usulan->id}\n";
            echo "   Status: {$usulan->status_usulan}\n\n";

            // Test 3: Test validation logic
            $this->testValidationLogic();

            // Test 4: Test form structure
            $this->testFormStructure();

            // Test 5: Test JavaScript functions
            $this->testJavaScriptFunctions();

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
        }
    }

    private function testValidationLogic()
    {
        echo "🧪 VALIDATION LOGIC TEST:\n";
        echo "=========================\n\n";

        // Test different scenarios
        $testCases = [
            [
                'input' => 'Short',
                'expected' => false,
                'description' => 'Short text (5 chars)'
            ],
            [
                'input' => 'This is a valid note with more than 10 characters',
                'expected' => true,
                'description' => 'Valid text (50 chars)'
            ],
            [
                'input' => '   ',
                'expected' => false,
                'description' => 'Whitespace only'
            ],
            [
                'input' => '   Valid   ',
                'expected' => true,
                'description' => 'Valid with whitespace'
            ],
            [
                'input' => '123456789',
                'expected' => false,
                'description' => 'Exactly 9 chars'
            ],
            [
                'input' => '1234567890',
                'expected' => true,
                'description' => 'Exactly 10 chars'
            ],
            [
                'input' => '12345678901',
                'expected' => true,
                'description' => 'Exactly 11 chars'
            ]
        ];

        foreach ($testCases as $testCase) {
            $input = $testCase['input'];
            $trimmed = trim($input);
            $length = strlen($trimmed);
            $isValid = $length >= 10;
            $status = ($isValid === $testCase['expected']) ? '✅ CORRECT' : '❌ INCORRECT';
            
            echo "   {$status} {$testCase['description']}\n";
            echo "     Input: '{$input}'\n";
            echo "     Trimmed: '{$trimmed}'\n";
            echo "     Length: {$length} chars\n";
            echo "     Expected: " . ($testCase['expected'] ? 'VALID' : 'INVALID') . "\n";
            echo "     Actual: " . ($isValid ? 'VALID' : 'INVALID') . "\n\n";
        }
    }

    private function testFormStructure()
    {
        echo "📁 FORM STRUCTURE TEST:\n";
        echo "=======================\n\n";

        $formFiles = [
            'resources/views/backend/layouts/admin-fakultas/partials/_hidden-forms.blade.php',
            'resources/views/backend/layouts/admin-fakultas/partials/_validation-scripts.blade.php',
            'resources/views/backend/layouts/admin-fakultas/usulan-detail-wrapper.blade.php'
        ];

        foreach ($formFiles as $file) {
            if (file_exists($file)) {
                $content = file_get_contents($file);
                
                echo "   📄 {$file}:\n";
                
                // Check for validationForm
                $hasValidationForm = strpos($content, 'validationForm') !== false;
                $status = $hasValidationForm ? '✅ EXISTS' : '❌ MISSING';
                echo "     {$status} validationForm\n";
                
                // Check for returnUsulanForm
                $hasReturnForm = strpos($content, 'returnUsulanForm') !== false;
                $status = $hasReturnForm ? '✅ EXISTS' : '❌ MISSING';
                echo "     {$status} returnUsulanForm\n";
                
                // Check for rejectUsulanForm
                $hasRejectForm = strpos($content, 'rejectUsulanForm') !== false;
                $status = $hasRejectForm ? '✅ EXISTS' : '❌ MISSING';
                echo "     {$status} rejectUsulanForm\n";
                
                // Check for catatan_umum field
                $hasCatatanUmum = strpos($content, 'catatan_umum') !== false;
                $status = $hasCatatanUmum ? '✅ EXISTS' : '❌ MISSING';
                echo "     {$status} catatan_umum field\n";
                
                // Check for catatan_reject field
                $hasCatatanReject = strpos($content, 'catatan_reject') !== false;
                $status = $hasCatatanReject ? '✅ EXISTS' : '❌ MISSING';
                echo "     {$status} catatan_reject field\n";
                
            } else {
                echo "   ❌ File not found: {$file}\n";
            }
            echo "\n";
        }
    }

    private function testJavaScriptFunctions()
    {
        echo "🔧 JAVASCRIPT FUNCTIONS TEST:\n";
        echo "=============================\n\n";

        $scriptFile = 'resources/views/backend/layouts/admin-fakultas/partials/_validation-scripts.blade.php';
        
        if (file_exists($scriptFile)) {
            $content = file_get_contents($scriptFile);
            
            // Check for submitReturnForm function
            $hasSubmitReturn = strpos($content, 'function submitReturnForm()') !== false;
            $status = $hasSubmitReturn ? '✅ EXISTS' : '❌ MISSING';
            echo "   {$status} submitReturnForm function\n";
            
            // Check for submitRejectForm function
            $hasSubmitReject = strpos($content, 'function submitRejectForm()') !== false;
            $status = $hasSubmitReject ? '✅ EXISTS' : '❌ MISSING';
            echo "   {$status} submitRejectForm function\n";
            
            // Check for trim() usage
            $hasTrim = strpos($content, '.trim()') !== false;
            $status = $hasTrim ? '✅ EXISTS' : '❌ MISSING';
            echo "   {$status} trim() usage\n";
            
            // Check for length validation
            $hasLengthCheck = strpos($content, '.length < 10') !== false;
            $status = $hasLengthCheck ? '✅ EXISTS' : '❌ MISSING';
            echo "   {$status} length < 10 validation\n";
            
            // Check for console.log debugging
            $hasConsoleLog = strpos($content, 'console.log') !== false;
            $status = $hasConsoleLog ? '✅ EXISTS' : '❌ MISSING';
            echo "   {$status} console.log debugging\n";
            
            // Check for focus() on error
            $hasFocus = strpos($content, '.focus()') !== false;
            $status = $hasFocus ? '✅ EXISTS' : '❌ MISSING';
            echo "   {$status} focus() on error\n";
            
            // Check for action_type input creation
            $hasActionType = strpos($content, 'action_type') !== false;
            $status = $hasActionType ? '✅ EXISTS' : '❌ MISSING';
            echo "   {$status} action_type input creation\n";
            
        } else {
            echo "   ❌ Script file not found\n";
        }
    }

    public function testControllerValidation()
    {
        echo "\n🎯 CONTROLLER VALIDATION TEST:\n";
        echo "=============================\n\n";

        $controllerFile = 'app/Http/Controllers/Backend/AdminFakultas/AdminFakultasController.php';
        
        if (file_exists($controllerFile)) {
            $content = file_get_contents($controllerFile);
            
            // Check for reject_to_pegawai case
            $hasRejectCase = strpos($content, 'case \'reject_to_pegawai\':') !== false;
            $status = $hasRejectCase ? '✅ EXISTS' : '❌ MISSING';
            echo "   {$status} reject_to_pegawai case\n";
            
            // Check for validation rules
            $hasValidationRules = strpos($content, 'catatan_reject.*required.*string.*min:10') !== false || 
                                 strpos($content, 'catatan_reject.*required') !== false;
            $status = $hasValidationRules ? '✅ EXISTS' : '❌ MISSING';
            echo "   {$status} catatan_reject validation rules\n";
            
            // Check for validation messages
            $hasValidationMessages = strpos($content, 'catatan_reject.min') !== false;
            $status = $hasValidationMessages ? '✅ EXISTS' : '❌ MISSING';
            echo "   {$status} catatan_reject validation messages\n";
            
            // Check for Belum Direkomendasikan status
            $hasStatus = strpos($content, 'Belum Direkomendasikan') !== false;
            $status = $hasStatus ? '✅ EXISTS' : '❌ MISSING';
            echo "   {$status} Belum Direkomendasikan status\n";
            
        } else {
            echo "   ❌ Controller file not found\n";
        }
    }

    public function testSampleFormSubmission()
    {
        echo "\n📝 SAMPLE FORM SUBMISSION TEST:\n";
        echo "===============================\n\n";

        // Simulate form data
        $sampleData = [
            'action_type' => 'reject_to_pegawai',
            'validation' => [
                'data_pribadi' => [
                    'nama_lengkap' => ['status' => 'sesuai', 'keterangan' => '']
                ]
            ],
            'catatan_reject' => 'This is a sample rejection note with more than 10 characters to test the validation'
        ];

        echo "   📋 Sample form data:\n";
        echo "     Action Type: {$sampleData['action_type']}\n";
        echo "     Catatan Reject: {$sampleData['catatan_reject']}\n";
        echo "     Catatan Length: " . strlen($sampleData['catatan_reject']) . " chars\n";
        
        // Test validation logic
        $isValid = strlen(trim($sampleData['catatan_reject'])) >= 10;
        $status = $isValid ? '✅ VALID' : '❌ INVALID';
        echo "     Validation Result: {$status}\n\n";

        // Test what would be sent to controller
        echo "   🔄 What would be sent to controller:\n";
        echo "     - action_type: {$sampleData['action_type']}\n";
        echo "     - validation: " . json_encode($sampleData['validation']) . "\n";
        echo "     - catatan_reject: {$sampleData['catatan_reject']}\n\n";
    }
}

// Run the debug test
$test = new ValidationDebugTest();
$test->testValidationDebug();
$test->testControllerValidation();
$test->testSampleFormSubmission();
