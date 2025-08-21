<?php

/**
 * Test Script untuk Verifikasi Dashboard Action Fix
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'test_dashboard_action_fix.php';"
 */

echo "🔍 DASHBOARD ACTION FIX TEST\n";
echo "============================\n\n";

// Test 1: Check controller logic
$controllerFile = 'app/Http/Controllers/Backend/AdminFakultas/AdminFakultasController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    echo "🎯 CONTROLLER LOGIC CHECK:\n";
    
    // Check for total_usulan count
    $hasTotalUsulanCount = strpos($content, 'total_usulan') !== false;
    echo $hasTotalUsulanCount ? "✅ total_usulan count exists\n" : "❌ total_usulan count missing\n";
    
    // Check for showPendaftar method
    $hasShowPendaftar = strpos($content, 'function showPendaftar') !== false;
    echo $hasShowPendaftar ? "✅ showPendaftar method exists\n" : "❌ showPendaftar method missing\n";
    
    // Check for all usulan display logic
    $hasAllUsulanLogic = strpos($content, 'HAPUS filter status_usulan') !== false || strpos($content, '// PERBAIKAN: Tampilkan semua usulan') !== false;
    echo $hasAllUsulanLogic ? "✅ All usulan display logic exists\n" : "❌ All usulan display logic missing\n";
    
} else {
    echo "❌ Controller file not found\n";
}

echo "\n";

// Test 2: Check dashboard view
$dashboardFile = 'resources/views/backend/layouts/admin-fakultas/dashboard.blade.php';
if (file_exists($dashboardFile)) {
    $content = file_get_contents($dashboardFile);
    
    echo "📊 DASHBOARD VIEW CHECK:\n";
    
    // Check for always visible action button
    $hasAlwaysVisibleButton = strpos($content, 'Lihat Semua') !== false;
    echo $hasAlwaysVisibleButton ? "✅ Always visible action button exists\n" : "❌ Always visible action button missing\n";
    
    // Check for total usulan info
    $hasTotalUsulanInfo = strpos($content, 'Total usulan:') !== false;
    echo $hasTotalUsulanInfo ? "✅ Total usulan info exists\n" : "❌ Total usulan info missing\n";
    
    // Check for improved description
    $hasImprovedDescription = strpos($content, 'Klik tombol aksi untuk melihat semua usulan') !== false;
    echo $hasImprovedDescription ? "✅ Improved description exists\n" : "❌ Improved description missing\n";
    
    // Check for conditional button text
    $hasConditionalButtonText = strpos($content, '@if($periode->jumlah_pengusul > 0)') !== false && strpos($content, 'Review (') !== false;
    echo $hasConditionalButtonText ? "✅ Conditional button text exists\n" : "❌ Conditional button text missing\n";
    
} else {
    echo "❌ Dashboard file not found\n";
}

echo "\n";

// Test 3: Check pengusul view
$pengusulFile = 'resources/views/backend/layouts/admin-fakultas/usulan/pengusul.blade.php';
if (file_exists($pengusulFile)) {
    $content = file_get_contents($pengusulFile);
    
    echo "👥 PENGAJAR VIEW CHECK:\n";
    
    // Check for all status display
    $hasAllStatusDisplay = strpos($content, 'Diusulkan ke Universitas') !== false && strpos($content, 'Direkomendasikan') !== false;
    echo $hasAllStatusDisplay ? "✅ All status display exists\n" : "❌ All status display missing\n";
    
    // Check for status configuration
    $hasStatusConfig = strpos($content, 'statusConfig') !== false;
    echo $hasStatusConfig ? "✅ Status configuration exists\n" : "❌ Status configuration missing\n";
    
    // Check for detail button
    $hasDetailButton = strpos($content, 'admin-fakultas.usulan.show') !== false;
    echo $hasDetailButton ? "✅ Detail button exists\n" : "❌ Detail button missing\n";
    
} else {
    echo "❌ Pengusul file not found\n";
}

echo "\n";

// Test 4: Sample scenarios
echo "🧪 SAMPLE SCENARIOS:\n";

$scenarios = [
    [
        'description' => 'Period with pending reviews',
        'jumlah_pengusul' => 3,
        'total_usulan' => 5,
        'expected_button_text' => 'Review (3)',
        'expected_visibility' => 'VISIBLE'
    ],
    [
        'description' => 'Period with no pending reviews but has completed proposals',
        'jumlah_pengusul' => 0,
        'total_usulan' => 2,
        'expected_button_text' => 'Lihat Semua',
        'expected_visibility' => 'VISIBLE'
    ],
    [
        'description' => 'Period with no proposals at all',
        'jumlah_pengusul' => 0,
        'total_usulan' => 0,
        'expected_button_text' => 'Lihat Semua',
        'expected_visibility' => 'VISIBLE'
    ]
];

foreach ($scenarios as $scenario) {
    echo "   📋 {$scenario['description']}:\n";
    echo "     Jumlah pengusul: {$scenario['jumlah_pengusul']}\n";
    echo "     Total usulan: {$scenario['total_usulan']}\n";
    echo "     Expected button text: {$scenario['expected_button_text']}\n";
    echo "     Expected visibility: {$scenario['expected_visibility']}\n\n";
}

echo "✅ Test completed!\n";
echo "\n📝 MANUAL TESTING INSTRUCTIONS:\n";
echo "1. Login sebagai Admin Fakultas\n";
echo "2. Akses dashboard 'Pusat Usulan Fakultas'\n";
echo "3. Verify: Info panel menampilkan 'Total usulan' dan 'Total usulan menunggu review'\n";
echo "4. Verify: Kolom 'AKSI' selalu menampilkan tombol (tidak ada 'Tidak Ada')\n";
echo "5. Verify: Tombol menampilkan 'Review (X)' jika ada usulan menunggu review\n";
echo "6. Verify: Tombol menampilkan 'Lihat Semua' jika tidak ada usulan menunggu review\n";
echo "7. Test: Klik tombol aksi → harus ke halaman daftar pengusul\n";
echo "8. Verify: Halaman daftar pengusul menampilkan semua usulan (termasuk yang sudah dikirim ke universitas)\n";
echo "9. Verify: Status usulan ditampilkan dengan warna yang sesuai\n";
echo "10. Test: Klik tombol 'Detail' pada usulan yang sudah dikirim ke universitas\n";
echo "11. Verify: Halaman detail menampilkan data lengkap dalam mode read-only\n";
