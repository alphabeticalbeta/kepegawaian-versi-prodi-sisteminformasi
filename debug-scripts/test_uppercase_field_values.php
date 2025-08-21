<?php

/**
 * Test Script untuk Verifikasi Uppercase Field Values
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'test_uppercase_field_values.php';"
 */

use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\Usulan;

class UppercaseFieldValuesTest
{
    public function testUppercaseFieldValues()
    {
        echo "🔍 TESTING UPPERCASE FIELD VALUES\n";
        echo "=================================\n\n";

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
            echo "   - Periode: " . ($usulan->periodeUsulan->nama_periode ?? 'N/A') . "\n\n";

            // Test 3: Test field values formatting
            echo "🎯 FIELD VALUES FORMATTING TEST:\n";
            $this->testFieldValuesFormatting($usulan);

            // Test 4: Test link detection
            echo "\n🔗 LINK DETECTION TEST:\n";
            $this->testLinkDetection($usulan);

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        }
    }

    private function testFieldValuesFormatting($usulan)
    {
        $fieldHelper = new \App\Helpers\UsulanFieldHelper($usulan);

        // Test different field types
        $testCases = [
            // Regular text fields - should be UPPERCASE
            ['data_pribadi', 'nama_lengkap'],
            ['data_kepegawaian', 'nip'],
            ['data_pendidikan', 'pendidikan_terakhir'],
            ['data_kinerja', 'predikat_kinerja_tahun_pertama'],
            
            // Link fields - should remain as links
            ['karya_ilmiah', 'link_artikel'],
            ['dokumen_profil', 'ijazah_terakhir'],
            ['dokumen_usulan', 'pakta_integritas'],
        ];

        foreach ($testCases as $testCase) {
            $category = $testCase[0];
            $field = $testCase[1];
            
            $value = $fieldHelper->getFieldValue($category, $field);
            $isLink = strpos($value, '<a href=') !== false;
            
            if ($isLink) {
                $status = '🔗 LINK';
                $formattedValue = $value;
            } else {
                $status = '📄 TEXT';
                $formattedValue = strtoupper(strip_tags($value));
            }
            
            echo "   {$status} {$category}.{$field}:\n";
            echo "     Original: " . (strlen($value) > 100 ? substr($value, 0, 100) . '...' : $value) . "\n";
            echo "     Formatted: " . (strlen($formattedValue) > 100 ? substr($formattedValue, 0, 100) . '...' : $formattedValue) . "\n\n";
        }
    }

    private function testLinkDetection($usulan)
    {
        $fieldHelper = new \App\Helpers\UsulanFieldHelper($usulan);

        // Test link detection logic
        $testCases = [
            // Should be detected as links
            ['karya_ilmiah', 'link_artikel', true],
            ['dokumen_profil', 'ijazah_terakhir', true],
            ['dokumen_usulan', 'pakta_integritas', true],
            ['dokumen_bkd', 'bkd_semester_1', true],
            
            // Should NOT be detected as links
            ['data_pribadi', 'nama_lengkap', false],
            ['data_kepegawaian', 'nip', false],
            ['data_pendidikan', 'pendidikan_terakhir', false],
        ];

        foreach ($testCases as $testCase) {
            $category = $testCase[0];
            $field = $testCase[1];
            $expectedIsLink = $testCase[2];
            
            $value = $fieldHelper->getFieldValue($category, $field);
            $actualIsLink = strpos($value, '<a href=') !== false;
            
            $status = ($actualIsLink === $expectedIsLink) ? '✅ CORRECT' : '❌ INCORRECT';
            $linkStatus = $actualIsLink ? '🔗 IS LINK' : '📄 IS TEXT';
            
            echo "   {$status} {$category}.{$field}: {$linkStatus}\n";
        }
    }

    public function testViewFile()
    {
        echo "\n📁 VIEW FILE TEST:\n";
        echo "=================\n\n";

        try {
            $viewFile = 'resources/views/backend/components/usulan/_validation-row.blade.php';
            
            if (file_exists($viewFile)) {
                $content = file_get_contents($viewFile);
                
                // Check for uppercase styling
                $hasUppercaseStyle = strpos($content, 'text-transform: uppercase') !== false;
                $status = $hasUppercaseStyle ? '✅ EXISTS' : '❌ MISSING';
                echo "   {$status} Uppercase styling (text-transform: uppercase)\n";
                
                // Check for larger font size
                $hasLargerFont = strpos($content, 'text-xl') !== false;
                $status = $hasLargerFont ? '✅ EXISTS' : '❌ MISSING';
                echo "   {$status} Larger font size (text-xl)\n";
                
                // Check for bold font weight
                $hasBoldFont = strpos($content, 'font-bold') !== false;
                $status = $hasBoldFont ? '✅ EXISTS' : '❌ MISSING';
                echo "   {$status} Bold font weight (font-bold)\n";
                
                // Check for link detection
                $hasLinkDetection = strpos($content, '$isLinkField') !== false;
                $status = $hasLinkDetection ? '✅ EXISTS' : '❌ MISSING';
                echo "   {$status} Link detection logic (\$isLinkField)\n";
                
                // Check for conditional styling
                $hasConditionalStyle = strpos($content, '$isLinkField ?') !== false;
                $status = $hasConditionalStyle ? '✅ EXISTS' : '❌ MISSING';
                echo "   {$status} Conditional styling for links\n";
                
            } else {
                echo "   ❌ View file not found\n";
            }

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
        }
    }

    public function testSampleData()
    {
        echo "\n📊 SAMPLE DATA TEST:\n";
        echo "===================\n\n";

        try {
            $usulan = Usulan::with(['pegawai'])->first();
            if (!$usulan || !$usulan->pegawai) {
                echo "❌ Tidak ada data pegawai untuk ditest\n";
                return;
            }

            $pegawai = $usulan->pegawai;
            
            // Test sample field values
            $sampleFields = [
                'nama_lengkap' => $pegawai->nama_lengkap ?? 'N/A',
                'nip' => $pegawai->nip ?? 'N/A',
                'email' => $pegawai->email ?? 'N/A',
                'pendidikan_terakhir' => $pegawai->pendidikan_terakhir ?? 'N/A',
            ];

            foreach ($sampleFields as $field => $value) {
                $uppercaseValue = strtoupper($value);
                echo "   📄 {$field}:\n";
                echo "     Original: {$value}\n";
                echo "     Uppercase: {$uppercaseValue}\n\n";
            }

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
        }
    }
}

// Run the test
$test = new UppercaseFieldValuesTest();
$test->testUppercaseFieldValues();
$test->testViewFile();
$test->testSampleData();
