<?php

/**
 * Test Script untuk Verifikasi UI Fixes Admin Fakultas
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'test_admin_fakultas_ui_fixes.php';"
 */

use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\Usulan;

class AdminFakultasUIFixesTest
{
    public function testUIFixes()
    {
        echo "🔍 TESTING ADMIN FAKULTAS UI FIXES\n";
        echo "==================================\n\n";

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
            $usulan = Usulan::with(['periodeUsulan'])->first();
            if (!$usulan) {
                echo "❌ Tidak ada usulan untuk ditest\n";
                return;
            }

            echo "✅ Menggunakan usulan ID: {$usulan->id}\n";
            echo "   - Periode: " . ($usulan->periodeUsulan->nama_periode ?? 'N/A') . "\n\n";

            // Test 3: Test validation labels
            echo "🎯 VALIDATION LABELS TEST:\n";
            $this->testValidationLabels($usulan);

            // Test 4: Test field helper methods
            echo "\n🔧 FIELD HELPER METHODS TEST:\n";
            $this->testFieldHelperMethods($usulan);

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        }
    }

    private function testValidationLabels($usulan)
    {
        $fieldHelper = new \App\Helpers\UsulanFieldHelper($usulan);
        $bkdLabels = $usulan->getBkdDisplayLabels();

        // Test different categories
        $testCases = [
            // Regular fields - should be UPPERCASE
            ['data_pribadi', 'nama_lengkap', 'NAMA LENGKAP'],
            ['data_kepegawaian', 'nip', 'NIP'],
            ['data_pendidikan', 'pendidikan_terakhir', 'PENDIDIKAN TERAKHIR'],
            ['data_kinerja', 'predikat_kinerja_tahun_pertama', 'PREDIKAT KINERJA TAHUN PERTAMA'],
            
            // Article links - should be Title Case
            ['karya_ilmiah', 'link_artikel', 'Link Artikel'],
            ['karya_ilmiah', 'link_sinta', 'Link Sinta'],
            ['karya_ilmiah', 'link_scopus', 'Link Scopus'],
            
            // Document links - should be Title Case
            ['dokumen_profil', 'link_ijazah', 'Link Ijazah'],
            ['dokumen_usulan', 'link_pakta', 'Link Pakta'],
            
            // BKD documents - should be UPPERCASE
            ['dokumen_bkd', 'bkd_semester_1', 'BKD SEMESTER GANJIL 2024/2025'],
            
            // Dokumen pendukung - should be UPPERCASE
            ['dokumen_pendukung', 'nomor_surat_usulan', 'NOMOR SURAT USULAN FAKULTAS'],
            ['dokumen_pendukung', 'file_surat_usulan', 'DOKUMEN SURAT USULAN FAKULTAS'],
        ];

        foreach ($testCases as $testCase) {
            $category = $testCase[0];
            $field = $testCase[1];
            $expected = $testCase[2];
            
            $actual = $fieldHelper->getValidationLabel($category, $field, $bkdLabels);
            
            // For BKD fields, we need to check if it contains the expected pattern
            if ($category === 'dokumen_bkd') {
                $isCorrect = strpos($actual, 'BKD SEMESTER') !== false && strpos($actual, '2024/2025') !== false;
                $status = $isCorrect ? '✅ CORRECT' : '❌ INCORRECT';
                echo "   {$status} {$category}.{$field}: {$actual}\n";
            } else {
                $isCorrect = ($actual === $expected);
                $status = $isCorrect ? '✅ CORRECT' : '❌ INCORRECT';
                echo "   {$status} {$category}.{$field}: {$actual} (expected: {$expected})\n";
            }
        }
    }

    private function testFieldHelperMethods($usulan)
    {
        $fieldHelper = new \App\Helpers\UsulanFieldHelper($usulan);

        // Test getFieldValue method
        echo "   📋 Testing getFieldValue method:\n";
        
        $testFields = [
            ['data_pribadi', 'nama_lengkap'],
            ['karya_ilmiah', 'link_artikel'],
            ['dokumen_profil', 'ijazah_terakhir'],
            ['dokumen_bkd', 'bkd_semester_1'],
        ];

        foreach ($testFields as $testField) {
            $category = $testField[0];
            $field = $testField[1];
            
            $value = $fieldHelper->getFieldValue($category, $field);
            $hasLink = strpos($value, '<a href=') !== false;
            $status = $hasLink ? '🔗 LINK' : '📄 TEXT';
            
            echo "     {$status} {$category}.{$field}: " . (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) . "\n";
        }

        // Test getValidationLabel method
        echo "   🏷️ Testing getValidationLabel method:\n";
        
        $testLabels = [
            ['data_pribadi', 'nama_lengkap'],
            ['karya_ilmiah', 'link_artikel'],
            ['dokumen_pendukung', 'nomor_surat_usulan'],
        ];

        foreach ($testLabels as $testLabel) {
            $category = $testLabel[0];
            $field = $testLabel[1];
            
            $label = $fieldHelper->getValidationLabel($category, $field);
            $isUppercase = $label === strtoupper($label);
            $status = $isUppercase ? '🔤 UPPERCASE' : '📝 TITLE CASE';
            
            echo "     {$status} {$category}.{$field}: {$label}\n";
        }
    }

    public function testActionButtons()
    {
        echo "\n🔘 ACTION BUTTONS TEST:\n";
        echo "======================\n\n";

        try {
            // Test if action buttons file exists and contains expected content
            $actionButtonsFile = 'resources/views/backend/layouts/admin-fakultas/partials/_action-buttons.blade.php';
            
            if (file_exists($actionButtonsFile)) {
                $content = file_get_contents($actionButtonsFile);
                
                // Check if "Simpan Validasi" button is removed
                $hasSaveButton = strpos($content, 'Simpan Validasi') !== false;
                $status = !$hasSaveButton ? '✅ REMOVED' : '❌ STILL EXISTS';
                echo "   {$status} Save button (Simpan Validasi)\n";
                
                // Check if other buttons still exist
                $expectedButtons = [
                    'Perbaikan Usulan (Ke Pegawai)',
                    'Belum Direkomendasikan (Ke Pegawai)',
                    'Direkomendasikan (Ke Admin Universitas)'
                ];
                
                foreach ($expectedButtons as $button) {
                    $hasButton = strpos($content, $button) !== false;
                    $status = $hasButton ? '✅ EXISTS' : '❌ MISSING';
                    echo "   {$status} {$button}\n";
                }
                
            } else {
                echo "   ❌ Action buttons file not found\n";
            }

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
        }
    }

    public function testViewFiles()
    {
        echo "\n📁 VIEW FILES TEST:\n";
        echo "==================\n\n";

        try {
            $viewFiles = [
                'resources/views/backend/components/usulan/_validation-row.blade.php',
                'resources/views/backend/layouts/admin-fakultas/partials/_action-buttons.blade.php',
                'app/Helpers/UsulanFieldHelper.php'
            ];

            foreach ($viewFiles as $file) {
                $exists = file_exists($file);
                $status = $exists ? '✅ EXISTS' : '❌ MISSING';
                echo "   {$status} {$file}\n";
            }

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
        }
    }
}

// Run the test
$test = new AdminFakultasUIFixesTest();
$test->testUIFixes();
$test->testActionButtons();
$test->testViewFiles();
