<?php

// Test script untuk memverifikasi file upload
echo "=== TEST FILE UPLOAD ===\n\n";

// Simulate form data
$formData = [
    '_token' => 'test_token',
    'action_type' => 'resend_to_university',
    'validation' => [
        'data_pribadi' => [
            'nama_lengkap' => ['status' => 'sesuai', 'keterangan' => '']
        ]
    ],
    'dokumen_pendukung' => [
        'nomor_surat_usulan' => 'TEST/001/2024',
        'nomor_berita_senat' => 'BERITA/001/2024'
    ]
];

echo "1. Form Data Structure:\n";
print_r($formData);

echo "\n2. File Detection Test:\n";

// Test different file detection methods
$testCases = [
    'dokumen_pendukung.file_surat_usulan',
    'dokumen_pendukung[file_surat_usulan]',
    'dokumen_pendukung.file_berita_senat',
    'dokumen_pendukung[file_berita_senat]'
];

foreach ($testCases as $testCase) {
    echo "   - Testing: {$testCase}\n";
    echo "     Has file: " . (isset($_FILES[$testCase]) ? 'YES' : 'NO') . "\n";
    if (isset($_FILES[$testCase])) {
        echo "     File info: " . json_encode($_FILES[$testCase]) . "\n";
    }
}

echo "\n3. All FILES array:\n";
if (empty($_FILES)) {
    echo "   No files detected in \$_FILES\n";
} else {
    print_r($_FILES);
}

echo "\n4. All POST data:\n";
if (empty($_POST)) {
    echo "   No POST data detected\n";
} else {
    print_r($_POST);
}

echo "\n5. Content Type:\n";
echo "   Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'Not set') . "\n";

echo "\n6. Request Method:\n";
echo "   Method: " . ($_SERVER['REQUEST_METHOD'] ?? 'Not set') . "\n";

echo "\n=== TEST COMPLETED ===\n";
