<?php

// Debug script untuk memverifikasi file upload
echo "=== DEBUG FILE UPLOAD ===\n\n";

// Simulate request data
$requestData = [
    '_token' => 'test_token',
    'action_type' => 'resend_to_university',
    'catatan_umum' => 'Test catatan',
    'validation' => [
        'dokumen_admin_fakultas' => [
            'nomor_surat_usulan' => ['status' => 'sesuai', 'keterangan' => ''],
            'file_surat_usulan' => ['status' => 'sesuai', 'keterangan' => ''],
            'nomor_berita_senat' => ['status' => 'sesuai', 'keterangan' => ''],
            'file_berita_senat' => ['status' => 'sesuai', 'keterangan' => '']
        ]
    ],
    'dokumen_pendukung' => [
        'nomor_surat_usulan' => 'TEST/001/2024',
        'nomor_berita_senat' => 'BERITA/001/2024'
    ]
];

echo "1. Request Data Structure:\n";
print_r($requestData);

echo "\n2. Testing file detection methods:\n";

// Test different file detection approaches
$testCases = [
    'dokumen_pendukung.file_surat_usulan',
    'dokumen_pendukung[file_surat_usulan]',
    'dokumen_pendukung.file_berita_senat',
    'dokumen_pendukung[file_berita_senat]'
];

foreach ($testCases as $testCase) {
    echo "   - Testing: {$testCase}\n";
    echo "     Has file: " . (isset($_FILES[$testCase]) ? 'YES' : 'NO') . "\n";
}

echo "\n3. All FILES array:\n";
print_r($_FILES);

echo "\n4. All POST data:\n";
print_r($_POST);

echo "\n5. All REQUEST data:\n";
print_r($_REQUEST);

echo "\n=== DEBUG COMPLETED ===\n";
