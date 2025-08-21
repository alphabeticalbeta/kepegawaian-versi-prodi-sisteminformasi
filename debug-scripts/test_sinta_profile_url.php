<?php

/**
 * Test Script untuk Verifikasi Sinta Profile URL Link
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'test_sinta_profile_url.php';"
 */

use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\Usulan;

class SintaProfileUrlTest
{
    public function testSintaProfileUrl()
    {
        echo "🔍 TESTING SINTA PROFILE URL LINK\n";
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

            // Test 3: Test Sinta Profile URL
            echo "🔗 SINTA PROFILE URL TEST:\n";
            $this->testSintaProfileUrl($usulan);

            // Test 4: Test different URL scenarios
            echo "\n📋 URL SCENARIOS TEST:\n";
            $this->testUrlScenarios($usulan);

            // Test 5: Test helper file modifications
            echo "\n🔧 HELPER FILE TEST:\n";
            $this->testHelperFile();

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        }
    }

    private function testSintaProfileUrl($usulan)
    {
        $fieldHelper = new \App\Helpers\UsulanFieldHelper($usulan);

        // Test Sinta Profile URL specifically
        $sintaProfileValue = $fieldHelper->getFieldValue('data_pribadi', 'url_profil_sinta');
        
        echo "   📋 Testing Sinta Profile URL:\n";
        echo "     Field: data_pribadi.url_profil_sinta\n";
        echo "     Value: " . (strlen($sintaProfileValue) > 100 ? substr($sintaProfileValue, 0, 100) . '...' : $sintaProfileValue) . "\n";
        
        // Check if it contains "View Link"
        $hasViewLink = strpos($sintaProfileValue, 'View Link') !== false;
        $status = $hasViewLink ? '✅ CORRECT' : '❌ INCORRECT';
        echo "     {$status} Contains 'View Link' label\n";
        
        // Check if it's a link
        $isLink = strpos($sintaProfileValue, '<a href=') !== false;
        $linkStatus = $isLink ? '✅ IS LINK' : '❌ NOT A LINK';
        echo "     {$linkStatus} Link detection\n";
        
        // Check if it has target="_blank"
        $hasTargetBlank = strpos($sintaProfileValue, 'target="_blank"') !== false;
        $targetStatus = $hasTargetBlank ? '✅ HAS TARGET BLANK' : '❌ NO TARGET BLANK';
        echo "     {$targetStatus} Target blank attribute\n\n";

        // Test pegawai data directly
        if ($usulan->pegawai) {
            $pegawai = $usulan->pegawai;
            echo "   📋 Direct Pegawai Data:\n";
            echo "     URL Profil Sinta: " . ($pegawai->url_profil_sinta ?? 'N/A') . "\n";
            echo "     Is Valid URL: " . (filter_var($pegawai->url_profil_sinta ?? '', FILTER_VALIDATE_URL) ? '✅ YES' : '❌ NO') . "\n\n";
        }
    }

    private function testUrlScenarios($usulan)
    {
        $fieldHelper = new \App\Helpers\UsulanFieldHelper($usulan);

        // Test different URL scenarios
        $testCases = [
            // Valid URL
            'https://sinta.kemdikbud.go.id/authors/profile/6788542',
            // Invalid URL
            'invalid-url',
            // Empty URL
            '',
            // Null URL
            null,
        ];

        echo "   📋 Testing different URL scenarios:\n";
        foreach ($testCases as $testUrl) {
            // Temporarily set the URL for testing
            if ($usulan->pegawai) {
                $originalUrl = $usulan->pegawai->url_profil_sinta;
                $usulan->pegawai->url_profil_sinta = $testUrl;
                
                $value = $fieldHelper->getFieldValue('data_pribadi', 'url_profil_sinta');
                $isLink = strpos($value, '<a href=') !== false;
                $hasViewLink = strpos($value, 'View Link') !== false;
                
                $status = $isLink ? '🔗 LINK' : '📄 TEXT';
                $urlStatus = $testUrl ? $testUrl : 'EMPTY/NULL';
                
                echo "     {$status} URL: {$urlStatus}\n";
                echo "       Result: " . (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) . "\n";
                
                // Restore original URL
                $usulan->pegawai->url_profil_sinta = $originalUrl;
            }
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
                
                // Check for url_profil_sinta handling
                $hasUrlProfilSinta = strpos($content, 'url_profil_sinta') !== false;
                $status = $hasUrlProfilSinta ? '✅ EXISTS' : '❌ MISSING';
                echo "   {$status} url_profil_sinta handling\n";
                
                // Check for "View Link" text
                $hasViewLink = strpos($content, 'View Link') !== false;
                $status = $hasViewLink ? '✅ EXISTS' : '❌ MISSING';
                echo "   {$status} 'View Link' text for Sinta Profile\n";
                
                // Check for URL validation
                $hasUrlValidation = strpos($content, 'filter_var($urlValue, FILTER_VALIDATE_URL)') !== false;
                $status = $hasUrlValidation ? '✅ EXISTS' : '❌ MISSING';
                echo "   {$status} URL validation logic\n";
                
                // Check for target="_blank"
                $hasTargetBlank = strpos($content, 'target="_blank"') !== false;
                $status = $hasTargetBlank ? '✅ EXISTS' : '❌ MISSING';
                echo "   {$status} Target blank attribute\n";
                
            } else {
                echo "   ❌ Helper file not found\n";
            }

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
        }
    }

    public function testComparisonWithOtherLinks()
    {
        echo "\n🔄 COMPARISON WITH OTHER LINKS:\n";
        echo "==============================\n\n";

        try {
            $usulan = Usulan::with(['pegawai'])->first();
            if (!$usulan) {
                echo "❌ Tidak ada usulan untuk ditest\n";
                return;
            }

            $fieldHelper = new \App\Helpers\UsulanFieldHelper($usulan);

            // Test different link types
            $linkTests = [
                ['data_pribadi', 'url_profil_sinta', 'Sinta Profile URL'],
                ['karya_ilmiah', 'link_sinta', 'Sinta Article Link'],
                ['karya_ilmiah', 'link_artikel', 'Article Link'],
                ['dokumen_profil', 'ijazah_terakhir', 'Document Link'],
            ];

            echo "   📋 Comparing different link types:\n";
            foreach ($linkTests as $test) {
                $category = $test[0];
                $field = $test[1];
                $description = $test[2];
                
                $value = $fieldHelper->getFieldValue($category, $field);
                $isLink = strpos($value, '<a href=') !== false;
                $hasViewLink = strpos($value, 'View Link') !== false;
                $hasLihatLink = strpos($value, 'Lihat Link') !== false;
                $hasLihatDokumen = strpos($value, 'Lihat Dokumen') !== false;
                
                $linkType = $isLink ? '🔗 LINK' : '📄 TEXT';
                $label = $hasViewLink ? 'View Link' : ($hasLihatLink ? 'Lihat Link' : ($hasLihatDokumen ? 'Lihat Dokumen' : 'N/A'));
                
                echo "     {$linkType} {$description} ({$category}.{$field}): {$label}\n";
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
                'url_profil_sinta' => $pegawai->url_profil_sinta ?? 'N/A',
                'email' => $pegawai->email ?? 'N/A',
            ];

            echo "   📄 Sample field values:\n";
            foreach ($sampleFields as $field => $value) {
                $isUrl = filter_var($value, FILTER_VALIDATE_URL);
                $status = $isUrl ? '🔗 URL' : '📄 TEXT';
                
                echo "     {$status} {$field}: {$value}\n";
            }

        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
        }
    }
}

// Run the test
$test = new SintaProfileUrlTest();
$test->testSintaProfileUrl();
$test->testComparisonWithOtherLinks();
$test->testSampleData();

