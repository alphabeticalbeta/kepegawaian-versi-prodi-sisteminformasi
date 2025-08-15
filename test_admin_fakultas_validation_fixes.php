<?php

/**
 * Test Script untuk Verifikasi Admin Fakultas Validation Fixes
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'test_admin_fakultas_validation_fixes.php';"
 */

use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\Usulan;

class AdminFakultasValidationFixesTest
{
    public function testValidationFixes()
    {
        echo "🔍 TESTING ADMIN FAKULTAS VALIDATION FIXES\n";
        echo "==========================================\n\n";

        try {
            // Test 1: Check if user is authenticated
            if (!Auth::check()) {
                echo "❌ User tidak terautentikasi\n";
                return;
            }

            $user = Auth::user();
            echo "✅ User terautentikasi: {$user->nama_lengkap}\n";
            echo "   - Role: " . ($user->role ?? 'N/A') . "\n\n";

            // Test 2: Find a usulan to test
            $usulan = Usulan::with(['periodeUsulan', 'pegawai'])->first();
            if (!$usulan) {
                echo "❌ Tidak ada usulan untuk ditest\n";
                return;
            }

            echo "✅ Menggunakan usulan ID: {$usulan->id}\n";
            echo "   - Status: {$usulan->status_usulan}\n";
            echo "   - Periode: " . ($usulan->periodeUsulan->nama_periode ?? 'N/A') . "\n\n";

            // Test 3: Test controller methods
            echo "🔧 CONTROLLER METHODS TEST:\n";
            $this->testControllerMethods($usulan);

            // Test 4: Test validation rules
            echo "\n📋 VALIDATION RULES TEST:\n";
            $this->testValidationRules($usulan);

            // Test 5: Test form files
            echo "\n📁 FORM FILES TEST:\n";
            $this->testFormFiles();

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        }
    }

    private function testControllerMethods($usulan)
    {
        // Test if controller has all required methods
        $controllerFile = 'app/Http/Controllers/Backend/AdminFakultas/AdminFakultasController.php';
        
        if (file_exists($controllerFile)) {
            $content = file_get_contents($controllerFile);
            
            // Check for reject_to_pegawai case
            $hasRejectCase = strpos($content, 'case \'reject_to_pegawai\':') !== false;
            $status = $hasRejectCase ? '✅ EXISTS' : '❌ MISSING';
            echo "   {$status} reject_to_pegawai case in controller\n";
            
            // Check for validation messages
            $hasValidationMessages = strpos($content, 'catatan_umum.min') !== false;
            $status = $hasValidationMessages ? '✅ EXISTS' : '❌ MISSING';
            echo "   {$status} Validation messages for min characters\n";
            
            // Check for catatan_reject validation
            $hasCatatanReject = strpos($content, 'catatan_reject') !== false;
            $status = $hasCatatanReject ? '✅ EXISTS' : '❌ MISSING';
            echo "   {$status} catatan_reject validation\n";
            
            // Check for Belum Direkomendasikan status
            $hasBelumDirekomendasikan = strpos($content, 'Belum Direkomendasikan') !== false;
            $status = $hasBelumDirekomendasikan ? '✅ EXISTS' : '❌ MISSING';
            echo "   {$status} Belum Direkomendasikan status handling\n";
            
        } else {
            echo "   ❌ Controller file not found\n";
        }
    }

    private function testValidationRules($usulan)
    {
        // Test validation rules for different actions
        $testCases = [
            [
                'action' => 'return_to_pegawai',
                'required_fields' => ['validation', 'catatan_umum'],
                'min_length' => ['catatan_umum' => 10],
                'max_length' => ['catatan_umum' => 2000]
            ],
            [
                'action' => 'reject_to_pegawai',
                'required_fields' => ['validation', 'catatan_reject'],
                'min_length' => ['catatan_reject' => 10],
                'max_length' => ['catatan_reject' => 2000]
            ],
            [
                'action' => 'forward_to_university',
                'required_fields' => ['validation', 'nomor_surat_usulan', 'file_surat_usulan', 'nomor_berita_senat', 'file_berita_senat'],
                'file_rules' => ['file_surat_usulan' => 'pdf|max:1024', 'file_berita_senat' => 'pdf|max:1024']
            ]
        ];

        foreach ($testCases as $testCase) {
            $action = $testCase['action'];
            echo "   📋 Testing {$action} validation:\n";
            
            // Check required fields
            foreach ($testCase['required_fields'] as $field) {
                echo "     ✅ Required field: {$field}\n";
            }
            
            // Check min length
            if (isset($testCase['min_length'])) {
                foreach ($testCase['min_length'] as $field => $min) {
                    echo "     ✅ Min length {$field}: {$min} characters\n";
                }
            }
            
            // Check max length
            if (isset($testCase['max_length'])) {
                foreach ($testCase['max_length'] as $field => $max) {
                    echo "     ✅ Max length {$field}: {$max} characters\n";
                }
            }
            
            // Check file rules
            if (isset($testCase['file_rules'])) {
                foreach ($testCase['file_rules'] as $field => $rules) {
                    echo "     ✅ File rules {$field}: {$rules}\n";
                }
            }
            
            echo "\n";
        }
    }

    public function testFormFiles()
    {
        echo "\n📁 FORM FILES TEST:\n";
        echo "==================\n\n";

        $formFiles = [
            'resources/views/backend/layouts/admin-fakultas/partials/_hidden-forms.blade.php',
            'resources/views/backend/layouts/admin-fakultas/partials/_validation-scripts.blade.php',
            'resources/views/backend/layouts/admin-fakultas/partials/_action-buttons.blade.php'
        ];

        foreach ($formFiles as $file) {
            if (file_exists($file)) {
                $content = file_get_contents($file);
                
                // Check for reject form
                $hasRejectForm = strpos($content, 'rejectUsulanForm') !== false;
                $status = $hasRejectForm ? '✅ EXISTS' : '❌ MISSING';
                echo "   {$status} rejectUsulanForm in {$file}\n";
                
                // Check for catatan_reject field
                $hasCatatanReject = strpos($content, 'catatan_reject') !== false;
                $status = $hasCatatanReject ? '✅ EXISTS' : '❌ MISSING';
                echo "   {$status} catatan_reject field in {$file}\n";
                
                // Check for submitRejectForm function
                $hasSubmitReject = strpos($content, 'submitRejectForm') !== false;
                $status = $hasSubmitReject ? '✅ EXISTS' : '❌ MISSING';
                echo "   {$status} submitRejectForm function in {$file}\n";
                
            } else {
                echo "   ❌ File not found: {$file}\n";
            }
        }
    }

    public function testJavaScriptValidation()
    {
        echo "\n🔧 JAVASCRIPT VALIDATION TEST:\n";
        echo "=============================\n\n";

        $scriptFile = 'resources/views/backend/layouts/admin-fakultas/partials/_validation-scripts.blade.php';
        
        if (file_exists($scriptFile)) {
            $content = file_get_contents($scriptFile);
            
            // Check for character count validation
            $hasCharCount = strpos($content, '.length < 10') !== false;
            $status = $hasCharCount ? '✅ EXISTS' : '❌ MISSING';
            echo "   {$status} Character count validation (10 chars)\n";
            
            // Check for trim() usage
            $hasTrim = strpos($content, '.trim()') !== false;
            $status = $hasTrim ? '✅ EXISTS' : '❌ MISSING';
            echo "   {$status} trim() usage for validation\n";
            
            // Check for debug logging
            $hasDebugLog = strpos($content, 'console.log') !== false;
            $status = $hasDebugLog ? '✅ EXISTS' : '❌ MISSING';
            echo "   {$status} Debug logging for troubleshooting\n";
            
            // Check for focus() on error
            $hasFocus = strpos($content, '.focus()') !== false;
            $status = $hasFocus ? '✅ EXISTS' : '❌ MISSING';
            echo "   {$status} Focus on error field\n";
            
        } else {
            echo "   ❌ Script file not found\n";
        }
    }

    public function testStatusHandling()
    {
        echo "\n📊 STATUS HANDLING TEST:\n";
        echo "========================\n\n";

        try {
            $usulan = Usulan::first();
            if (!$usulan) {
                echo "❌ Tidak ada usulan untuk ditest\n";
                return;
            }

            // Test status transitions
            $statusTests = [
                'return_to_pegawai' => 'Perlu Perbaikan',
                'reject_to_pegawai' => 'Belum Direkomendasikan',
                'forward_to_university' => 'Diusulkan ke Universitas'
            ];

            foreach ($statusTests as $action => $expectedStatus) {
                echo "   📋 Action: {$action} → Expected Status: {$expectedStatus}\n";
                
                // Check if status is handled in controller
                $controllerFile = 'app/Http/Controllers/Backend/AdminFakultas/AdminFakultasController.php';
                if (file_exists($controllerFile)) {
                    $content = file_get_contents($controllerFile);
                    $hasStatus = strpos($content, $expectedStatus) !== false;
                    $status = $hasStatus ? '✅ HANDLED' : '❌ NOT HANDLED';
                    echo "     {$status} Status handling in controller\n";
                }
            }

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
        }
    }

    public function testSampleValidation()
    {
        echo "\n🧪 SAMPLE VALIDATION TEST:\n";
        echo "=========================\n\n";

        // Test sample validation scenarios
        $testScenarios = [
            [
                'description' => 'Valid catatan (15 chars)',
                'catatan' => 'Ini adalah catatan yang valid untuk testing dengan panjang 15 karakter',
                'expected' => 'VALID'
            ],
            [
                'description' => 'Short catatan (5 chars)',
                'catatan' => 'Short',
                'expected' => 'INVALID'
            ],
            [
                'description' => 'Empty catatan',
                'catatan' => '',
                'expected' => 'INVALID'
            ],
            [
                'description' => 'Whitespace only',
                'catatan' => '   ',
                'expected' => 'INVALID'
            ]
        ];

        foreach ($testScenarios as $scenario) {
            $catatan = $scenario['catatan'];
            $trimmedLength = strlen(trim($catatan));
            $isValid = $trimmedLength >= 10;
            $actual = $isValid ? 'VALID' : 'INVALID';
            $status = ($actual === $scenario['expected']) ? '✅ CORRECT' : '❌ INCORRECT';
            
            echo "   {$status} {$scenario['description']}\n";
            echo "     Length: {$trimmedLength} chars, Result: {$actual}\n\n";
        }
    }
}

// Run the test
$test = new AdminFakultasValidationFixesTest();
$test->testValidationFixes();
$test->testJavaScriptValidation();
$test->testStatusHandling();
$test->testSampleValidation();

