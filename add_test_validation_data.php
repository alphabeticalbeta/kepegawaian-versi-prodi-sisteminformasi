<?php

echo "=== ADD TEST VALIDATION DATA ===\n";

// Connect to database directly
try {
    $pdo = new PDO('mysql:host=localhost;dbname=kepegawaian_unmul', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Database connected\n\n";
    
    // Get current validasi_data for usulan 16
    $stmt = $pdo->prepare("SELECT validasi_data FROM usulans WHERE id = 16");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result) {
        echo "❌ Usulan 16 tidak ditemukan\n";
        exit(1);
    }
    
    $currentValidasiData = json_decode($result['validasi_data'], true) ?: [];
    
    echo "Current validasi_data:\n";
    print_r($currentValidasiData);
    echo "\n";
    
    // Create test validation data structure
    $testValidationData = [
        'tim_penilai' => [
            'reviews' => [
                1 => [ // penilai ID 1
                    'type' => 'perbaikan_usulan',
                    'catatan' => 'Perbaiki data yang tidak sesuai',
                    'tanggal_return' => '2025-01-21 10:00:00',
                    'validation' => [
                        'data_pribadi' => [
                            'nama_lengkap' => [
                                'status' => 'tidak_sesuai',
                                'keterangan' => 'Nama tidak sesuai dengan dokumen'
                            ],
                            'tempat_lahir' => [
                                'status' => 'tidak_sesuai',
                                'keterangan' => 'Tempat lahir tidak sesuai'
                            ]
                        ],
                        'data_kepegawaian' => [
                            'nip' => [
                                'status' => 'tidak_sesuai',
                                'keterangan' => 'NIP tidak valid'
                            ]
                        ],
                        'data_pendidikan' => [
                            'gelar_akademik' => [
                                'status' => 'tidak_sesuai',
                                'keterangan' => 'Gelar tidak sesuai dengan ijazah'
                            ]
                        ]
                    ]
                ],
                2 => [ // penilai ID 2
                    'type' => 'perbaikan_usulan',
                    'catatan' => 'Perbaiki beberapa field yang bermasalah',
                    'tanggal_return' => '2025-01-21 11:00:00',
                    'validation' => [
                        'data_kinerja' => [
                            'skp_tahun_2023' => [
                                'status' => 'tidak_sesuai',
                                'keterangan' => 'SKP tidak sesuai format'
                            ]
                        ],
                        'dokumen_usulan' => [
                            'surat_pengantar' => [
                                'status' => 'tidak_sesuai',
                                'keterangan' => 'Surat pengantar tidak lengkap'
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];
    
    // Merge with existing data
    $mergedData = array_merge_recursive($currentValidasiData, $testValidationData);
    
    echo "Merged validation data:\n";
    print_r($mergedData);
    echo "\n";
    
    // Update the database
    $stmt = $pdo->prepare("UPDATE usulans SET validasi_data = ? WHERE id = 16");
    $jsonData = json_encode($mergedData, JSON_PRETTY_PRINT);
    $stmt->execute([$jsonData]);
    
    echo "✅ Test validation data added successfully!\n";
    echo "Total invalid fields that should appear:\n";
    echo "- Penilai 1: 3 fields (nama_lengkap, tempat_lahir, nip)\n";
    echo "- Penilai 2: 2 fields (skp_tahun_2023, surat_pengantar)\n\n";
    
    echo "Now you can check: http://localhost/admin-univ-usulan/usulan/16\n";
    echo "Field bermasalah should appear in single line format for each penilai!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
