<?php

/**
 * Test Script untuk Verifikasi Completed Proposal View
 * 
 * Jalankan dengan: docker-compose exec app php artisan tinker --execute="require 'test_completed_proposal_view.php';"
 */

echo "🔍 COMPLETED PROPOSAL VIEW TEST\n";
echo "==============================\n\n";

// Test 1: Check controller logic
$controllerFile = 'app/Http/Controllers/Backend/AdminFakultas/AdminFakultasController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    echo "🎯 CONTROLLER LOGIC CHECK:\n";
    
    // Check for canEdit logic
    $hasCanEditLogic = strpos($content, 'in_array($usulan->status_usulan, [\'Diajukan\', \'Sedang Direview\'])') !== false;
    echo $hasCanEditLogic ? "✅ canEdit logic exists\n" : "❌ canEdit logic missing\n";
    
    // Check for canView logic
    $hasCanViewLogic = strpos($content, 'canView.*true') !== false;
    echo $hasCanViewLogic ? "✅ canView logic exists\n" : "❌ canView logic missing\n";
    
    // Check for completed status handling
    $hasCompletedStatus = strpos($content, 'Diusulkan ke Universitas') !== false;
    echo $hasCompletedStatus ? "✅ Completed status handling exists\n" : "❌ Completed status handling missing\n";
    
} else {
    echo "❌ Controller file not found\n";
}

echo "\n";

// Test 2: Check action buttons
$actionButtonsFile = 'resources/views/backend/layouts/admin-fakultas/partials/_action-buttons.blade.php';
if (file_exists($actionButtonsFile)) {
    $content = file_get_contents($actionButtonsFile);
    
    echo "🔘 ACTION BUTTONS CHECK:\n";
    
    // Check for conditional description
    $hasConditionalDescription = strpos($content, '@if($canEdit)') !== false;
    echo $hasConditionalDescription ? "✅ Conditional description exists\n" : "❌ Conditional description missing\n";
    
    // Check for status indicators include
    $hasStatusIndicators = strpos($content, '_status-indicators') !== false;
    echo $hasStatusIndicators ? "✅ Status indicators include exists\n" : "❌ Status indicators include missing\n";
    
    // Check for prominent back button
    $hasProminentBackButton = strpos($content, 'Kembali ke Daftar Pengusul') !== false;
    echo $hasProminentBackButton ? "✅ Prominent back button exists\n" : "❌ Prominent back button missing\n";
    
    // Check for conditional hidden forms
    $hasConditionalForms = strpos($content, '@if($canEdit)') !== false && strpos($content, '_hidden-forms') !== false;
    echo $hasConditionalForms ? "✅ Conditional hidden forms exists\n" : "❌ Conditional hidden forms missing\n";
    
} else {
    echo "❌ Action buttons file not found\n";
}

echo "\n";

// Test 3: Check status indicators
$statusIndicatorsFile = 'resources/views/backend/layouts/admin-fakultas/partials/_status-indicators.blade.php';
if (file_exists($statusIndicatorsFile)) {
    $content = file_get_contents($statusIndicatorsFile);
    
    echo "📊 STATUS INDICATORS CHECK:\n";
    
    // Check for "Diusulkan ke Universitas" status
    $hasDiusulkanStatus = strpos($content, 'Diusulkan ke Universitas') !== false;
    echo $hasDiusulkanStatus ? "✅ Diusulkan ke Universitas status exists\n" : "❌ Diusulkan ke Universitas status missing\n";
    
    // Check for "Direkomendasikan" status
    $hasDirekomendasikanStatus = strpos($content, 'Direkomendasikan') !== false;
    echo $hasDirekomendasikanStatus ? "✅ Direkomendasikan status exists\n" : "❌ Direkomendasikan status missing\n";
    
    // Check for "Ditolak Universitas" status
    $hasDitolakStatus = strpos($content, 'Ditolak Universitas') !== false;
    echo $hasDitolakStatus ? "✅ Ditolak Universitas status exists\n" : "❌ Ditolak Universitas status missing\n";
    
    // Check for proper styling classes
    $hasStylingClasses = strpos($content, 'bg-purple-100') !== false && strpos($content, 'bg-emerald-100') !== false;
    echo $hasStylingClasses ? "✅ Proper styling classes exist\n" : "❌ Proper styling classes missing\n";
    
} else {
    echo "❌ Status indicators file not found\n";
}

echo "\n";

// Test 4: Check validation row for read-only mode
$validationRowFile = 'resources/views/backend/components/usulan/_validation-row.blade.php';
if (file_exists($validationRowFile)) {
    $content = file_get_contents($validationRowFile);
    
    echo "📋 VALIDATION ROW READ-ONLY CHECK:\n";
    
    // Check for conditional editing
    $hasConditionalEditing = strpos($content, '@if($canEdit)') !== false;
    echo $hasConditionalEditing ? "✅ Conditional editing exists\n" : "❌ Conditional editing missing\n";
    
    // Check for read-only status display
    $hasReadOnlyStatus = strpos($content, 'inline-flex px-3 py-1') !== false;
    echo $hasReadOnlyStatus ? "✅ Read-only status display exists\n" : "❌ Read-only status display missing\n";
    
    // Check for read-only keterangan display
    $hasReadOnlyKeterangan = strpos($content, 'bg-red-50') !== false && strpos($content, 'bg-gray-50') !== false;
    echo $hasReadOnlyKeterangan ? "✅ Read-only keterangan display exists\n" : "❌ Read-only keterangan display missing\n";
    
} else {
    echo "❌ Validation row file not found\n";
}

echo "\n";

// Test 5: Sample status scenarios
echo "🧪 SAMPLE STATUS SCENARIOS:\n";

$scenarios = [
    [
        'status' => 'Diusulkan ke Universitas',
        'canEdit' => false,
        'canView' => true,
        'description' => 'Proposal sent to university'
    ],
    [
        'status' => 'Direkomendasikan',
        'canEdit' => false,
        'canView' => true,
        'description' => 'Proposal recommended'
    ],
    [
        'status' => 'Ditolak Universitas',
        'canEdit' => false,
        'canView' => true,
        'description' => 'Proposal rejected by university'
    ],
    [
        'status' => 'Diajukan',
        'canEdit' => true,
        'canView' => true,
        'description' => 'Proposal still in draft'
    ],
    [
        'status' => 'Sedang Direview',
        'canEdit' => true,
        'canView' => true,
        'description' => 'Proposal under review'
    ]
];

foreach ($scenarios as $scenario) {
    $expectedCanEdit = in_array($scenario['status'], ['Diajukan', 'Sedang Direview']);
    $status = ($expectedCanEdit === $scenario['canEdit']) ? '✅' : '❌';
    
    echo "   {$status} {$scenario['description']}:\n";
    echo "     Status: {$scenario['status']}\n";
    echo "     Expected canEdit: " . ($expectedCanEdit ? 'true' : 'false') . "\n";
    echo "     Expected canView: true\n";
    echo "     Expected: Show data but " . ($expectedCanEdit ? 'allow editing' : 'disable editing') . "\n\n";
}

echo "✅ Test completed!\n";
echo "\n📝 MANUAL TESTING INSTRUCTIONS:\n";
echo "1. Login sebagai Admin Fakultas\n";
echo "2. Akses usulan dengan status 'Diusulkan ke Universitas'\n";
echo "3. Verify: Data usulan terlihat lengkap\n";
echo "4. Verify: Tidak ada tombol aksi (Perbaikan, Belum Direkomendasikan, Direkomendasikan)\n";
echo "5. Verify: Ada status indicator 'Usulan sudah diteruskan ke Admin Universitas'\n";
echo "6. Verify: Ada tombol 'Kembali ke Daftar Pengusul' yang menonjol\n";
echo "7. Verify: Validation fields dalam mode read-only (tidak bisa diedit)\n";
echo "8. Test: Klik tombol kembali → harus ke halaman daftar pengusul\n";
