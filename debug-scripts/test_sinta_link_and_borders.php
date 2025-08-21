<?php

/**
 * Test Script untuk Verifikasi Sinta Link dan Border Styling
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'test_sinta_link_and_borders.php';"
 */

use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\Usulan;

class SintaLinkAndBordersTest
{
    public function testSintaLinkAndBorders()
    {
        echo "🔍 TESTING SINTA LINK AND BORDER STYLING\n";
        echo "========================================\n\n";

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

            // Test 3: Test Sinta link label
            echo "🔗 SINTA LINK LABEL TEST:\n";
            $this->testSintaLinkLabel($usulan);

            // Test 4: Test border styling
            echo "\n📦 BORDER STYLING TEST:\n";
            $this->testBorderStyling($usulan);

            // Test 5: Test view file modifications
            echo "\n📁 VIEW FILE TEST:\n";
            $this->testViewFile();

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        }
    }

    private function testSintaLinkLabel($usulan)
    {
        $fieldHelper = new \App\Helpers\UsulanFieldHelper($usulan);

        // Test Sinta link specifically
        $sintaValue = $fieldHelper->getFieldValue('karya_ilmiah', 'link_sinta');
        
        echo "   📋 Testing Sinta link label:\n";
        echo "     Field: karya_ilmiah.link_sinta\n";
        echo "     Value: " . (strlen($sintaValue) > 100 ? substr($sintaValue, 0, 100) . '...' : $sintaValue) . "\n";
        
        // Check if it contains "View Link"
        $hasViewLink = strpos($sintaValue, 'View Link') !== false;
        $status = $hasViewLink ? '✅ CORRECT' : '❌ INCORRECT';
        echo "     {$status} Contains 'View Link' label\n";
        
        // Check if it's a link
        $isLink = strpos($sintaValue, '<a href=') !== false;
        $linkStatus = $isLink ? '✅ IS LINK' : '❌ NOT A LINK';
        echo "     {$linkStatus} Link detection\n\n";

        // Test other link fields for comparison
        $otherLinks = [
            ['karya_ilmiah', 'link_artikel'],
            ['karya_ilmiah', 'link_scopus'],
            ['dokumen_profil', 'ijazah_terakhir'],
        ];

        echo "   📋 Testing other link fields:\n";
        foreach ($otherLinks as $linkField) {
            $category = $linkField[0];
            $field = $linkField[1];
            
            $value = $fieldHelper->getFieldValue($category, $field);
            $hasLihatLink = strpos($value, 'Lihat Link') !== false;
            $hasViewLink = strpos($value, 'View Link') !== false;
            $isLink = strpos($value, '<a href=') !== false;
            
            $expectedLabel = ($field === 'link_sinta') ? 'View Link' : 'Lihat Link';
            $hasCorrectLabel = ($field === 'link_sinta') ? $hasViewLink : $hasLihatLink;
            
            $status = $hasCorrectLabel ? '✅ CORRECT' : '❌ INCORRECT';
            echo "     {$status} {$category}.{$field}: {$expectedLabel}\n";
        }
    }

    private function testBorderStyling($usulan)
    {
        $fieldHelper = new \App\Helpers\UsulanFieldHelper($usulan);

        // Test different field types
        $testCases = [
            // Regular text fields - should have borders
            ['data_pribadi', 'nama_lengkap', true],
            ['data_kepegawaian', 'nip', true],
            ['data_pendidikan', 'pendidikan_terakhir', true],
            ['data_kinerja', 'predikat_kinerja_tahun_pertama', true],
            
            // Link fields - should NOT have borders
            ['karya_ilmiah', 'link_artikel', false],
            ['karya_ilmiah', 'link_sinta', false],
            ['dokumen_profil', 'ijazah_terakhir', false],
            ['dokumen_usulan', 'pakta_integritas', false],
        ];

        echo "   📦 Testing border styling logic:\n";
        foreach ($testCases as $testCase) {
            $category = $testCase[0];
            $field = $testCase[1];
            $shouldHaveBorder = $testCase[2];
            
            $value = $fieldHelper->getFieldValue($category, $field);
            $isLink = strpos($value, '<a href=') !== false;
            
            $status = ($isLink === !$shouldHaveBorder) ? '✅ CORRECT' : '❌ INCORRECT';
            $borderStatus = $shouldHaveBorder ? 'SHOULD HAVE BORDER' : 'NO BORDER';
            $linkStatus = $isLink ? '🔗 IS LINK' : '📄 IS TEXT';
            
            echo "     {$status} {$category}.{$field}: {$borderStatus} ({$linkStatus})\n";
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
                
                // Check for border styling
                $hasBorderClass = strpos($content, 'border border-gray-800') !== false;
                $status = $hasBorderClass ? '✅ EXISTS' : '❌ MISSING';
                echo "   {$status} Border styling (border border-gray-800)\n";
                
                // Check for padding
                $hasPadding = strpos($content, 'px-3 py-2') !== false;
                $status = $hasPadding ? '✅ EXISTS' : '❌ MISSING';
                echo "   {$status} Padding (px-3 py-2)\n";
                
                // Check for background
                $hasBackground = strpos($content, 'bg-gray-50') !== false;
                $status = $hasBackground ? '✅ EXISTS' : '❌ MISSING';
                echo "   {$status} Background (bg-gray-50)\n";
                
                // Check for rounded corners
                $hasRounded = strpos($content, 'rounded-md') !== false;
                $status = $hasRounded ? '✅ EXISTS' : '❌ MISSING';
                echo "   {$status} Rounded corners (rounded-md)\n";
                
                // Check for conditional border logic
                $hasConditionalBorder = strpos($content, '$isLinkField ? \'\' : \'border border-gray-800') !== false;
                $status = $hasConditionalBorder ? '✅ EXISTS' : '❌ MISSING';
                echo "   {$status} Conditional border logic\n";
                
            } else {
                echo "   ❌ View file not found\n";
            }

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
        }
    }

    public function testHelperFile()
    {
        echo "\n🔧 HELPER FILE TEST:\n";
        echo "==================\n\n";

        try {
            $helperFile = 'app/Helpers/UsulanFieldHelper.php';
            
            if (file_exists($helperFile)) {
                $content = file_get_contents($helperFile);
                
                // Check for Sinta link special handling
                $hasSintaSpecial = strpos($content, 'link_sinta') !== false;
                $status = $hasSintaSpecial ? '✅ EXISTS' : '❌ MISSING';
                echo "   {$status} Sinta link special handling\n";
                
                // Check for "View Link" text
                $hasViewLink = strpos($content, 'View Link') !== false;
                $status = $hasViewLink ? '✅ EXISTS' : '❌ MISSING';
                echo "   {$status} 'View Link' text for Sinta\n";
                
                // Check for conditional logic
                $hasConditional = strpos($content, '$field === \'link_sinta\'') !== false;
                $status = $hasConditional ? '✅ EXISTS' : '❌ MISSING';
                echo "   {$status} Conditional logic for Sinta link\n";
                
            } else {
                echo "   ❌ Helper file not found\n";
            }

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
        }
    }

    public function testSampleStyling()
    {
        echo "\n🎨 SAMPLE STYLING TEST:\n";
        echo "======================\n\n";

        try {
            $usulan = Usulan::with(['pegawai'])->first();
            if (!$usulan || !$usulan->pegawai) {
                echo "❌ Tidak ada data pegawai untuk ditest\n";
                return;
            }

            $pegawai = $usulan->pegawai;
            
            // Test sample field values with styling
            $sampleFields = [
                'nama_lengkap' => $pegawai->nama_lengkap ?? 'N/A',
                'nip' => $pegawai->nip ?? 'N/A',
                'email' => $pegawai->email ?? 'N/A',
            ];

            echo "   📄 Sample field styling preview:\n";
            foreach ($sampleFields as $field => $value) {
                $uppercaseValue = strtoupper($value);
                echo "     Field: {$field}\n";
                echo "     Original: {$value}\n";
                echo "     Styled: [BORDER] {$uppercaseValue} [BORDER]\n";
                echo "     Classes: text-xl font-bold text-gray-800 border border-gray-800 px-3 py-2 rounded-md bg-gray-50\n\n";
            }

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
        }
    }
}

// Run the test
$test = new SintaLinkAndBordersTest();
$test->testSintaLinkAndBorders();
$test->testHelperFile();
$test->testSampleStyling();

