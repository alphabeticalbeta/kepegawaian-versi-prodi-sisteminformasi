<?php

echo "=== ADD NUMBERED TEST DATA ===\n";

// Connect to database directly
try {
    $pdo = new PDO('mysql:host=localhost;dbname=kepegawaian_unmul', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Database connected\n\n";
    
    // Create comprehensive test validation data with numbered fields
    $testValidationData = [
        'tim_penilai' => [
            'reviews' => [
                1 => [ // penilai ID 1
                    'type' => 'perbaikan_usulan',
                    'catatan' => 'Beberapa data perlu diperbaiki untuk memenuhi standar validasi',
                    'tanggal_return' => '2025-01-21 10:00:00',
                    'validation' => [
                        'data_pribadi' => [
                            'nama_lengkap' => [
                                'status' => 'tidak_sesuai',
                                'keterangan' => 'Nama tidak sesuai dengan dokumen identitas yang dilampirkan'
                            ],
                            'tempat_lahir' => [
                                'status' => 'tidak_sesuai',
                                'keterangan' => 'Tempat lahir tidak konsisten dengan akte kelahiran'
                            ],
                            'tanggal_lahir' => [
                                'status' => 'tidak_sesuai',
                                'keterangan' => 'Format tanggal lahir tidak sesuai standar'
                            ]
                        ],
                        'data_kepegawaian' => [
                            'nip' => [
                                'status' => 'tidak_sesuai',
                                'keterangan' => 'NIP tidak valid atau tidak terdaftar di sistem kepegawaian'
                            ],
                            'status_kepegawaian' => [
                                'status' => 'tidak_sesuai',
                                'keterangan' => 'Status kepegawaian tidak sesuai dengan persyaratan usulan'
                            ]
                        ]
                    ]
                ],
                2 => [ // penilai ID 2
                    'type' => 'perbaikan_usulan',
                    'catatan' => 'Dokumen dan data kinerja memerlukan perbaikan sesuai standar yang berlaku',
                    'tanggal_return' => '2025-01-21 11:30:00',
                    'validation' => [
                        'data_pendidikan' => [
                            'gelar_akademik' => [
                                'status' => 'tidak_sesuai',
                                'keterangan' => 'Gelar akademik tidak sesuai dengan ijazah yang dilampirkan'
                            ],
                            'institusi_pendidikan' => [
                                'status' => 'tidak_sesuai',
                                'keterangan' => 'Nama institusi pendidikan tidak sesuai dengan dokumen resmi'
                            ]
                        ],
                        'data_kinerja' => [
                            'skp_tahun_2023' => [
                                'status' => 'tidak_sesuai',
                                'keterangan' => 'SKP tahun 2023 tidak sesuai format atau belum divalidasi'
                            ]
                        ],
                        'dokumen_usulan' => [
                            'surat_pengantar' => [
                                'status' => 'tidak_sesuai',
                                'keterangan' => 'Surat pengantar tidak lengkap atau tidak ditandatangani'
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];
    
    // Update the database with new test data
    $stmt = $pdo->prepare("UPDATE usulans SET validasi_data = ? WHERE id = 16");
    $jsonData = json_encode($testValidationData, JSON_PRETTY_PRINT);
    $stmt->execute([$jsonData]);
    
    echo "✅ Numbered test validation data added successfully!\n\n";
    echo "=== EXPECTED NUMBERED LIST DISPLAY ===\n";
    echo "Penilai 1 - Field bermasalah:\n";
    echo "1. ❌ Data Pribadi > Nama Lengkap\n";
    echo "   Nama tidak sesuai dengan dokumen identitas yang dilampirkan\n";
    echo "2. ❌ Data Pribadi > Tempat Lahir\n";
    echo "   Tempat lahir tidak konsisten dengan akte kelahiran\n";
    echo "3. ❌ Data Pribadi > Tanggal Lahir\n";
    echo "   Format tanggal lahir tidak sesuai standar\n";
    echo "4. ❌ Data Kepegawaian > NIP\n";
    echo "   NIP tidak valid atau tidak terdaftar di sistem kepegawaian\n";
    echo "5. ❌ Data Kepegawaian > Status Kepegawaian\n";
    echo "   Status kepegawaian tidak sesuai dengan persyaratan usulan\n\n";
    
    echo "Penilai 2 - Field bermasalah:\n";
    echo "1. ❌ Data Pendidikan > Gelar Akademik\n";
    echo "   Gelar akademik tidak sesuai dengan ijazah yang dilampirkan\n";
    echo "2. ❌ Data Pendidikan > Institusi Pendidikan\n";
    echo "   Nama institusi pendidikan tidak sesuai dengan dokumen resmi\n";
    echo "3. ❌ Data Kinerja > SKP Tahun 2023\n";
    echo "   SKP tahun 2023 tidak sesuai format atau belum divalidasi\n";
    echo "4. ❌ Dokumen Usulan > Surat Pengantar\n";
    echo "   Surat pengantar tidak lengkap atau tidak ditandatangani\n\n";
    
    echo "Total field bermasalah: 9 fields\n";
    echo "Now check: http://localhost/admin-univ-usulan/usulan/16\n";
    echo "Field bermasalah should appear in numbered list format! 🎯\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
