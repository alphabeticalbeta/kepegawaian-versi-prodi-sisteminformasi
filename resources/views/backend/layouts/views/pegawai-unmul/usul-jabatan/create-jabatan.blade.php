<!-- create-jabatan.blade.php - FIXED VERSION -->
@extends('backend.layouts.roles.pegawai-unmul.app')

@php
    // Set default values for variables that might not be defined
    $isEditMode = $isEditMode ?? false;
    $isReadOnly = $isReadOnly ?? false;
    $isShowMode = $isShowMode ?? false;
    $existingUsulan = $existingUsulan ?? null;
    $daftarPeriode = $daftarPeriode ?? null;
    $pegawai = $pegawai ?? null;
    $usulan = $usulan ?? null;
    $jabatanTujuan = $jabatanTujuan ?? null;
    $jenjangType = $jenjangType ?? null;
    $formConfig = $formConfig ?? [];
    $jenisUsulanPeriode = $jenisUsulanPeriode ?? null;
    $bkdSemesters = $bkdSemesters ?? [];
    $documentFields = $documentFields ?? [];
    $catatanPerbaikan = $catatanPerbaikan ?? [];

    // Get validation data from all roles for edit mode
    $validationData = [];
    if ($isEditMode && $usulan) {
        $roles = ['admin_fakultas', 'admin_universitas', 'tim_penilai'];

        foreach ($roles as $role) {
            $roleData = $usulan->getValidasiByRole($role);
            if (!empty($roleData) && isset($roleData['validation']) && !empty($roleData['validation'])) {
                $validationData[$role] = $roleData['validation'];
            }
        }

        // Get Tim Penilai data for individual display
        $penilais = $usulan->penilais ?? collect();
        $timPenilaiIndividualData = [];
        
        // Define field labels for mapping
        $fieldLabels = [
            'data_pribadi' => [
                'jenis_pegawai' => 'Jenis Pegawai',
                'status_kepegawaian' => 'Status Kepegawaian',
                'nip' => 'NIP',
                'nuptk' => 'NUPTK',
                'gelar_depan' => 'Gelar Depan',
                'nama_lengkap' => 'Nama Lengkap',
                'gelar_belakang' => 'Gelar Belakang',
                'email' => 'Email',
                'tempat_lahir' => 'Tempat Lahir',
                'tanggal_lahir' => 'Tanggal Lahir',
                'jenis_kelamin' => 'Jenis Kelamin',
                'nomor_handphone' => 'Nomor Handphone'
            ],
            'data_kepegawaian' => [
                'pangkat_saat_usul' => 'Pangkat',
                'tmt_pangkat' => 'TMT Pangkat',
                'jabatan_saat_usul' => 'Jabatan',
                'tmt_jabatan' => 'TMT Jabatan',
                'tmt_cpns' => 'TMT CPNS',
                'tmt_pns' => 'TMT PNS',
                'unit_kerja_saat_usul' => 'Unit Kerja'
            ],
            'data_pendidikan' => [
                'pendidikan_terakhir' => 'Pendidikan Terakhir',
                'nama_universitas_sekolah' => 'Nama Universitas/Sekolah',
                'nama_prodi_jurusan' => 'Nama Program Studi/Jurusan',
                'mata_kuliah_diampu' => 'Mata Kuliah Diampu',
                'ranting_ilmu_kepakaran' => 'Bidang Kepakaran',
                'url_profil_sinta' => 'Profil SINTA'
            ],
            'data_kinerja' => [
                'predikat_kinerja_tahun_pertama' => 'Predikat SKP Tahun ' . (date('Y') - 1),
                'predikat_kinerja_tahun_kedua' => 'Predikat SKP Tahun ' . (date('Y') - 2),
                'nilai_konversi' => 'Nilai Konversi ' . (date('Y') - 1)
            ],
            'dokumen_profil' => [
                'ijazah_terakhir' => 'Ijazah Terakhir',
                'transkrip_nilai_terakhir' => 'Transkrip Nilai Terakhir',
                'sk_pangkat_terakhir' => 'SK Pangkat Terakhir',
                'sk_jabatan_terakhir' => 'SK Jabatan Terakhir',
                'skp_tahun_pertama' => 'SKP Tahun ' . (date('Y') - 1),
                'skp_tahun_kedua' => 'SKP Tahun ' . (date('Y') - 2),
                'pak_konversi' => 'PAK Konversi ' . (date('Y') - 1),
                'sk_cpns' => 'SK CPNS',
                'sk_pns' => 'SK PNS',
                'sk_penyetaraan_ijazah' => 'SK Penyetaraan Ijazah',
                'disertasi_thesis_terakhir' => 'Disertasi/Thesis Terakhir'
            ],
            'karya_ilmiah' => [
                'karya_ilmiah' => 'Karya Ilmiah',
                'nama_jurnal' => 'Nama Jurnal',
                'judul_artikel' => 'Judul Artikel',
                'penerbit_artikel' => 'Penerbit Artikel',
                'volume_artikel' => 'Volume Artikel',
                'nomor_artikel' => 'Nomor Artikel',
                'edisi_artikel' => 'Edisi Artikel',
                'halaman_artikel' => 'Halaman Artikel',
                'link_artikel' => 'Link Artikel',
                'link_sinta' => 'Link SINTA',
                'link_scopus' => 'Link Scopus',
                'link_scimago' => 'Link Scimago',
                'link_wos' => 'Link Web of Science'
            ],
            'dokumen_usulan' => [
                'pakta_integritas' => 'Pakta Integritas',
                'bukti_korespondensi' => 'Bukti Korespondensi',
                'turnitin' => 'Turnitin',
                'upload_artikel' => 'Upload Artikel',
                'bukti_syarat_guru_besar' => 'Bukti Syarat Guru Besar'
            ],
            'syarat_guru_besar' => [
                'bukti_syarat_guru_besar' => 'Bukti Syarat Guru Besar'
            ]
        ];
        
        if ($penilais->count() > 0) {
            foreach ($penilais as $index => $penilai) {
                $penilaiName = 'Penilai ' . ($index + 1);
                $penilaiInvalidFields = [];
                $penilaiGeneralNotes = [];
                
                // Check if penilai has completed assessment
                $hasAssessment = !empty($penilai->pivot->hasil_penilaian) || 
                                !empty($penilai->pivot->status_penilaian) || 
                                !empty($penilai->pivot->catatan_penilaian) ||
                                ($penilai->pivot->status_penilaian ?? '') !== 'Belum Dinilai';
                
                if ($hasAssessment) {
                    // Get individual penilai data from validasi_data
                    $validasiData = $usulan->validasi_data ?? [];
                    $individualPenilaiData = $validasiData['individual_penilai'] ?? [];
                    
                    // Find data for this specific penilai
                    $penilaiData = collect($individualPenilaiData)->firstWhere('penilai_id', $penilai->id);
                    
                    if ($penilaiData && is_array($penilaiData)) {
                        // Process invalid fields for this penilai
                        foreach ($penilaiData as $groupKey => $groupData) {
                            if (is_array($groupData)) {
                                foreach ($groupData as $fieldKey => $fieldData) {
                                    if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                                        // Enhanced field mapping
                                        $fieldLabelMap = [
                                            'file_berita_senat' => 'File Berita Senat',
                                            'file_surat_usulan' => 'File Surat Usulan',
                                            'nomor_berita_senat' => 'Nomor Berita Senat',
                                            'nomor_surat_usulan' => 'Nomor Surat Usulan',
                                            'turnitin' => 'Dokumen Turnitin',
                                            'upload_artikel' => 'Upload Artikel',
                                            'pakta_integritas' => 'Pakta Integritas',
                                            'bukti_korespondensi' => 'Bukti Korespondensi',
                                            'sk_pns' => 'SK PNS',
                                            'sk_cpns' => 'SK CPNS',
                                            'ijazah_terakhir' => 'Ijazah Terakhir',
                                            'skp_tahun_pertama' => 'SKP Tahun Pertama',
                                            'skp_tahun_kedua' => 'SKP Tahun Kedua',
                                            'sk_jabatan_terakhir' => 'SK Jabatan Terakhir',
                                            'sk_pangkat_terakhir' => 'SK Pangkat Terakhir',
                                            'transkrip_nilai_terakhir' => 'Transkrip Nilai Terakhir',
                                            'disertasi_thesis_terakhir' => 'Disertasi/Thesis Terakhir',
                                            'pak_konversi' => 'PAK Konversi',
                                            'sk_penyetaraan_ijazah' => 'SK Penyetaraan Ijazah',
                                            'syarat_guru_besar' => 'Syarat Guru Besar',
                                            'bukti_syarat_guru_besar' => 'Bukti Syarat Guru Besar'
                                        ];
                                        
                                        $fieldLabel = isset($fieldLabels[$groupKey][$fieldKey]) ? $fieldLabels[$groupKey][$fieldKey] : ($fieldLabelMap[$fieldKey] ?? ucwords(str_replace('_', ' ', $fieldKey)));
                                        
                                        $penilaiInvalidFields[] = $fieldLabel . ' : ' . ($fieldData['keterangan'] ?? 'Tidak ada keterangan');
                                    }
                                }
                            }
                        }
                        
                        // Collect general notes for this penilai
                        if (isset($penilaiData['keterangan_umum']) && !empty($penilaiData['keterangan_umum'])) {
                            $penilaiGeneralNotes[] = $penilaiData['keterangan_umum'];
                        }
                    }
                    
                    // Fallback: Try to get data from general tim_penilai validation if individual data is empty
                    if (empty($penilaiInvalidFields)) {
                        $timPenilaiValidation = $usulan->getValidasiByRole('tim_penilai');
                        if (!empty($timPenilaiValidation) && isset($timPenilaiValidation['validation'])) {
                            foreach ($timPenilaiValidation['validation'] as $groupKey => $groupData) {
                                if (is_array($groupData)) {
                                    foreach ($groupData as $fieldKey => $fieldData) {
                                        if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                                            $fieldLabel = isset($fieldLabels[$groupKey][$fieldKey]) ? $fieldLabels[$groupKey][$fieldKey] : ($fieldLabelMap[$fieldKey] ?? ucwords(str_replace('_', ' ', $fieldKey)));
                                            $penilaiInvalidFields[] = $fieldLabel . ' : ' . ($fieldData['keterangan'] ?? 'Tidak ada keterangan');
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    // Use pivot data if available
                    if (!empty($penilai->pivot->catatan_penilaian)) {
                        $penilaiGeneralNotes[] = $penilai->pivot->catatan_penilaian;
                    }
                } else {
                    $penilaiGeneralNotes[] = 'Belum memberikan penilaian';
                }
                
                $timPenilaiIndividualData[$penilaiName] = [
                    'invalid_fields' => $penilaiInvalidFields,
                    'general_notes' => $penilaiGeneralNotes,
                    'has_assessment' => $hasAssessment,
                    'assessment_date' => $penilai->pivot->tanggal_penilaian ?? $penilai->pivot->updated_at ?? null
                ];
            }
        }
    }

        // Function to check if field has validation issues - ENHANCED
    function hasValidationIssue($fieldGroup, $fieldName, $validationData) {
        if (empty($validationData)) {
            return false;
        }

        foreach ($validationData as $role => $data) {
            if (isset($data[$fieldGroup][$fieldName]['status']) &&
                $data[$fieldGroup][$fieldName]['status'] === 'tidak_sesuai') {
                return true;
            }
        }
        return false;
    }

    // Function to get validation notes for a field
    function getValidationNotes($fieldGroup, $fieldName, $validationData) {
        $notes = [];
        if (empty($validationData)) {
            return $notes;
        }

        foreach ($validationData as $role => $data) {
            if (isset($data[$fieldGroup][$fieldName]['keterangan']) &&
                !empty($data[$fieldGroup][$fieldName]['keterangan'])) {
                $roleName = str_replace('_', ' ', ucfirst($role));
                $notes[] = "<strong>{$roleName}:</strong> " . $data[$fieldGroup][$fieldName]['keterangan'];
            }
        }
        return $notes;
    }

    // Function to get all validation notes for a field (for display)
    function getAllValidationNotes($fieldGroup, $fieldName, $validationData) {
        $notes = [];
        if (empty($validationData)) {
            return $notes;
        }

        foreach ($validationData as $role => $data) {
            if (isset($data[$fieldGroup][$fieldName]['keterangan']) &&
                !empty($data[$fieldGroup][$fieldName]['keterangan'])) {
                $roleName = str_replace('_', ' ', ucfirst($role));
                $notes[] = [
                    'role' => $roleName,
                    'note' => $data[$fieldGroup][$fieldName]['keterangan'],
                    'status' => $data[$fieldGroup][$fieldName]['status'] ?? 'tidak_sesuai'
                ];
            }
        }
        return $notes;
    }

    // Function to get legacy validation for backwards compatibility
    function getLegacyValidation($fieldGroup, $fieldName, $catatanPerbaikan) {
        return $catatanPerbaikan[$fieldGroup][$fieldName] ?? null;
    }

    // Function to check if field is invalid (hybrid approach)
    function isFieldInvalid($fieldGroup, $fieldName, $validationData, $catatanPerbaikan) {
        // First, try new validation data structure
        if (!empty($validationData)) {
            return hasValidationIssue($fieldGroup, $fieldName, $validationData);
        }

        // Fallback to legacy structure
        $legacy = getLegacyValidation($fieldGroup, $fieldName, $catatanPerbaikan);
        return $legacy && isset($legacy['status']) && $legacy['status'] === 'tidak_sesuai';
    }

    // Function to get field validation notes (hybrid approach)
    function getFieldValidationNotes($fieldGroup, $fieldName, $validationData, $catatanPerbaikan) {
        // First, try new validation data structure
        if (!empty($validationData)) {
            return getAllValidationNotes($fieldGroup, $fieldName, $validationData);
        }

        // Fallback to legacy structure
        $legacy = getLegacyValidation($fieldGroup, $fieldName, $catatanPerbaikan);
        if ($legacy && isset($legacy['keterangan']) && !empty($legacy['keterangan'])) {
            return [[
                'role' => 'Admin Fakultas',
                'note' => $legacy['keterangan'],
                'status' => $legacy['status'] ?? 'tidak_sesuai'
            ]];
        }

        return [];
    }
@endphp

@section('title', $isShowMode ? 'Detail Usulan Jabatan' : ($isEditMode ? 'Edit Usulan Jabatan' : 'Buat Usulan Jabatan'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/30">
    {{-- Header Section --}}
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6 flex flex-wrap gap-4 justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ $isShowMode ? 'Detail Usulan Jabatan' : ($isEditMode ? 'Edit Usulan Jabatan' : 'Buat Usulan Jabatan') }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $isShowMode ? 'Detail lengkap usulan jabatan fungsional dosen' : ($isEditMode ? 'Perbarui usulan jabatan fungsional dosen' : 'Formulir pengajuan jabatan fungsional dosen') }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    @if($isShowMode)
                        <a href="{{ route('pegawai-unmul.usulan-pegawai.dashboard') }}"
                           class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
                            Kembali ke Usulan Saya
                        </a>
                    @else
                        <a href="{{ route('pegawai-unmul.usulan-jabatan.index') }}"
                           class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
                            Kembali
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @php
            // Cek kelengkapan data profil
            $requiredFields = [
                'nama_lengkap', 'nip', 'email', 'tempat_lahir', 'tanggal_lahir',
                'jenis_kelamin', 'nomor_handphone', 'gelar_depan', 'gelar_belakang',
                'ijazah_terakhir', 'transkrip_nilai_terakhir', 'sk_pangkat_terakhir',
                'sk_jabatan_terakhir', 'skp_tahun_pertama', 'skp_tahun_kedua'
            ];

            $missingFields = [];
            foreach ($requiredFields as $field) {
                if (empty($pegawai->$field)) {
                    $missingFields[] = $field;
                }
            }

            $isProfileComplete = empty($missingFields);
            $canProceed = $isProfileComplete;

            // Jika mode edit atau show, pastikan form tetap ditampilkan
            if ($isEditMode || $isShowMode) {
                $canProceed = true;
            }
        @endphp



        {{-- Form Content --}}
        @if($canProceed)
            @if($isEditMode)
                <form id="usulan-form" action="{{ route('pegawai-unmul.usulan-jabatan.update', $usulan->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
            @elseif(!$isShowMode)
                <form id="usulan-form" action="{{ route('pegawai-unmul.usulan-jabatan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
            @endif

            {{-- Detail Field Bermasalah untuk Pegawai --}}
            @if($isEditMode && $usulan && !empty($validationData))
                @php
                    // Define role labels and colors
                    $roleConfigs = [
                        'admin_fakultas' => [
                            'label' => 'Admin Fakultas',
                            'color' => 'amber',
                            'icon' => 'building-2'
                        ],
                        'admin_universitas' => [
                            'label' => 'Admin Universitas',
                            'color' => 'blue',
                            'icon' => 'university'
                        ],
                        'tim_penilai' => [
                            'label' => 'Tim Penilai',
                            'color' => 'purple',
                            'icon' => 'users'
                        ]
                    ];

                    // Define field group labels
                    $fieldGroupLabels = [
                        'data_pribadi' => 'Data Pribadi',
                        'data_kepegawaian' => 'Data Kepegawaian',
                        'data_pendidikan' => 'Data Pendidikan & Fungsional',
                        'data_kinerja' => 'Data Kinerja',
                        'dokumen_profil' => 'Dokumen Profil',
                        'bkd' => 'Beban Kinerja Dosen (BKD)',
                        'karya_ilmiah' => 'Karya Ilmiah',
                        'dokumen_usulan' => 'Dokumen Usulan',
                        'syarat_guru_besar' => 'Syarat Guru Besar'
                    ];

                    // Define field labels
                    $fieldLabels = [
                        'data_pribadi' => [
                            'jenis_pegawai' => 'Jenis Pegawai',
                            'status_kepegawaian' => 'Status Kepegawaian',
                            'nip' => 'NIP',
                            'nuptk' => 'NUPTK',
                            'gelar_depan' => 'Gelar Depan',
                            'nama_lengkap' => 'Nama Lengkap',
                            'gelar_belakang' => 'Gelar Belakang',
                            'email' => 'Email',
                            'tempat_lahir' => 'Tempat Lahir',
                            'tanggal_lahir' => 'Tanggal Lahir',
                            'jenis_kelamin' => 'Jenis Kelamin',
                            'nomor_handphone' => 'Nomor Handphone'
                        ],
                        'data_kepegawaian' => [
                            'pangkat_saat_usul' => 'Pangkat',
                            'tmt_pangkat' => 'TMT Pangkat',
                            'jabatan_saat_usul' => 'Jabatan',
                            'tmt_jabatan' => 'TMT Jabatan',
                            'tmt_cpns' => 'TMT CPNS',
                            'tmt_pns' => 'TMT PNS',
                            'unit_kerja_saat_usul' => 'Unit Kerja'
                        ],
                        'data_pendidikan' => [
                            'pendidikan_terakhir' => 'Pendidikan Terakhir',
                            'nama_universitas_sekolah' => 'Nama Universitas/Sekolah',
                            'nama_prodi_jurusan' => 'Nama Program Studi/Jurusan',
                            'mata_kuliah_diampu' => 'Mata Kuliah Diampu',
                            'ranting_ilmu_kepakaran' => 'Bidang Kepakaran',
                            'url_profil_sinta' => 'Profil SINTA'
                        ],
                        'data_kinerja' => [
                            'predikat_kinerja_tahun_pertama' => 'Predikat SKP Tahun ' . (date('Y') - 1),
                            'predikat_kinerja_tahun_kedua' => 'Predikat SKP Tahun ' . (date('Y') - 2),
                            'nilai_konversi' => 'Nilai Konversi ' . (date('Y') - 1)
                        ],
                        'dokumen_profil' => [
                            'ijazah_terakhir' => 'Ijazah Terakhir',
                            'transkrip_nilai_terakhir' => 'Transkrip Nilai Terakhir',
                            'sk_pangkat_terakhir' => 'SK Pangkat Terakhir',
                            'sk_jabatan_terakhir' => 'SK Jabatan Terakhir',
                            'skp_tahun_pertama' => 'SKP Tahun ' . (date('Y') - 1),
                            'skp_tahun_kedua' => 'SKP Tahun ' . (date('Y') - 2),
                            'pak_konversi' => 'PAK Konversi ' . (date('Y') - 1),
                            'sk_cpns' => 'SK CPNS',
                            'sk_pns' => 'SK PNS',
                            'sk_penyetaraan_ijazah' => 'SK Penyetaraan Ijazah',
                            'disertasi_thesis_terakhir' => 'Disertasi/Thesis Terakhir'
                        ],
                        'karya_ilmiah' => [
                            'karya_ilmiah' => 'Karya Ilmiah',
                            'nama_jurnal' => 'Nama Jurnal',
                            'judul_artikel' => 'Judul Artikel',
                            'penerbit_artikel' => 'Penerbit Artikel',
                            'volume_artikel' => 'Volume Artikel',
                            'nomor_artikel' => 'Nomor Artikel',
                            'edisi_artikel' => 'Edisi Artikel',
                            'halaman_artikel' => 'Halaman Artikel',
                            'link_artikel' => 'Link Artikel',
                            'link_sinta' => 'Link SINTA',
                            'link_scopus' => 'Link Scopus',
                            'link_scimago' => 'Link Scimago',
                            'link_wos' => 'Link Web of Science'
                        ],
                        'dokumen_usulan' => [
                            'pakta_integritas' => 'Pakta Integritas',
                            'bukti_korespondensi' => 'Bukti Korespondensi',
                            'turnitin' => 'Turnitin',
                            'upload_artikel' => 'Upload Artikel',
                            'bukti_syarat_guru_besar' => 'Bukti Syarat Guru Besar'
                        ],
                        'syarat_guru_besar' => [
                            'bukti_syarat_guru_besar' => 'Bukti Syarat Guru Besar'
                        ]
                    ];

                    // Define field categories relevant for Pegawai (role-based filtering)
                    $pegawaiFieldCategories = [
                        'data_pribadi',
                        'data_kepegawaian', 
                        'data_pendidikan',
                        'data_kinerja',
                        'dokumen_profil',
                        'karya_ilmiah',
                        'dokumen_usulan',
                        'syarat_guru_besar'
                    ];

                    // Collect filtered invalid fields from all roles (only relevant to Pegawai)
                    $allInvalidFields = [];
                    foreach ($validationData as $roleKey => $roleValidation) {
                        if (isset($roleConfigs[$roleKey])) {
                            $roleConfig = $roleConfigs[$roleKey];
                            $invalidFields = [];
                            
                            foreach ($roleValidation as $groupKey => $groupData) {
                                // Only process field groups that are relevant to Pegawai
                                if (in_array($groupKey, $pegawaiFieldCategories) && isset($fieldGroupLabels[$groupKey])) {
                                    $groupLabel = $fieldGroupLabels[$groupKey];
                                    
                                    foreach ($groupData as $fieldKey => $fieldData) {
                                        if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                                            $fieldLabel = $fieldLabels[$groupKey][$fieldKey] ?? ucwords(str_replace('_', ' ', $fieldKey));
                                            $invalidFields[] = [
                                                'group' => $groupLabel,
                                                'field' => $fieldLabel,
                                                'keterangan' => $fieldData['keterangan'] ?? 'Tidak ada keterangan spesifik'
                                            ];
                                        }
                                    }
                                }
                            }
                            
                            if (!empty($invalidFields)) {
                                $allInvalidFields[$roleKey] = [
                                    'config' => $roleConfig,
                                    'fields' => $invalidFields
                                ];
                            }
                        }
                    }
                @endphp

                @php
                    // Check if there are any invalid fields from any source
                    $hasAnyInvalidFields = !empty($allInvalidFields);
                    
                    // Also check if Tim Penilai has invalid fields
                    $hasTimPenilaiInvalidFields = false;
                    if (!empty($timPenilaiIndividualData)) {
                        foreach ($timPenilaiIndividualData as $penilaiData) {
                            if (!empty($penilaiData['invalid_fields'])) {
                                $hasTimPenilaiInvalidFields = true;
                                break;
                            }
                        }
                    }
                    
                    $shouldShowInvalidFields = $hasAnyInvalidFields || $hasTimPenilaiInvalidFields;
                @endphp

                @if($shouldShowInvalidFields)
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
                        <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-5">
                            <h2 class="text-xl font-bold text-white flex items-center">
                                <i data-lucide="alert-circle" class="w-6 h-6 mr-3"></i>
                                Detail Field yang Perlu Diperbaiki (Relevan untuk Pegawai)
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                                <div class="flex items-start">
                                    <i data-lucide="info" class="w-5 h-5 text-red-600 mr-2 mt-0.5"></i>
                                    <div>
                                        <h4 class="text-sm font-medium text-red-800">Informasi Perbaikan</h4>
                                        <p class="text-sm text-red-700 mt-1">
                                            Berikut adalah daftar field yang memerlukan perbaikan berdasarkan feedback dari tim verifikasi. 
                                            Hanya field yang relevan dengan tanggung jawab Pegawai yang ditampilkan di bawah ini.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @foreach($allInvalidFields as $roleKey => $roleData)
                                <div class="mb-6 last:mb-0">
                                    <div class="bg-gradient-to-r from-{{ $roleData['config']['color'] }}-100 to-{{ $roleData['config']['color'] }}-200 px-4 py-3 rounded-t-lg border-l-4 border-{{ $roleData['config']['color'] }}-500">
                                        <h5 class="text-sm font-bold text-{{ $roleData['config']['color'] }}-800 flex items-center">
                                            <i data-lucide="{{ $roleData['config']['icon'] }}" class="w-4 h-4 mr-2"></i>
                                            Feedback dari {{ $roleData['config']['label'] }}
                                        </h5>
                                    </div>
                                    <div class="bg-gray-50 border border-gray-200 rounded-b-lg p-4">
                                        <div class="space-y-3">
                                            @foreach($roleData['fields'] as $field)
                                                <div class="flex items-start gap-3 p-3 bg-white border border-red-200 rounded-lg">
                                                    <i data-lucide="x-circle" class="w-4 h-4 text-red-600 mt-0.5 flex-shrink-0"></i>
                                                    <div class="flex-1">
                                                        <div class="text-sm font-medium text-red-800">
                                                            {{ $field['group'] }} - {{ $field['field'] }}
                                                        </div>
                                                        <div class="text-sm text-red-700 mt-1">
                                                            {{ $field['keterangan'] }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-start">
                                    <i data-lucide="lightbulb" class="w-5 h-5 text-blue-600 mt-0.5 mr-3"></i>
                                    <div>
                                        <h4 class="text-sm font-medium text-blue-800">Tips Perbaikan</h4>
                                        <p class="text-sm text-blue-700 mt-1">
                                            Pastikan untuk memperbaiki semua field yang disebutkan di atas sebelum mengajukan kembali usulan Anda. 
                                            Hanya field yang relevan dengan tanggung jawab Pegawai yang ditampilkan. Field lain yang tidak ditampilkan mungkin sudah sesuai atau merupakan tanggung jawab role lain.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Show message when no relevant fields need fixing --}}
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
                        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5">
                            <h2 class="text-xl font-bold text-white flex items-center">
                                <i data-lucide="check-circle" class="w-6 h-6 mr-3"></i>
                                Tidak Ada Field yang Perlu Diperbaiki
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <i data-lucide="info" class="w-5 h-5 text-green-600 mr-2 mt-0.5"></i>
                                    <div>
                                        <h4 class="text-sm font-medium text-green-800">Status Perbaikan</h4>
                                        <p class="text-sm text-green-700 mt-1">
                                            Semua field yang relevan dengan tanggung jawab Pegawai sudah sesuai. 
                                            Perbaikan mungkin terkait dengan area lain yang bukan tanggung jawab Pegawai.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            {{-- Hasil Validasi Tim Penilai --}}
            @if($isEditMode && $usulan && !empty($timPenilaiIndividualData))
                <div class="bg-white rounded-xl shadow-lg border border-red-200 overflow-hidden mb-6">
                    <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-5">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i data-lucide="alert-triangle" class="w-6 h-6 mr-3"></i>
                            Hasil Validasi Tim Penilai
                        </h2>
                    </div>
                    <div class="p-6">
                        @foreach($timPenilaiIndividualData as $penilaiName => $penilaiData)
                            <div class="mb-6 last:mb-0">
                                <div class="flex items-center justify-between mb-3 border-b border-gray-200 pb-2">
                                    <h3 class="font-semibold text-lg text-gray-800">
                                        <i data-lucide="user-check" class="w-5 h-5 inline mr-2 text-purple-600"></i>
                                        {{ $penilaiName }}
                                    </h3>
                                    @if($penilaiData['assessment_date'])
                                        <span class="text-xs text-gray-500">
                                            <i data-lucide="clock" class="w-3 h-3 inline mr-1"></i>
                                            {{ \Carbon\Carbon::parse($penilaiData['assessment_date'])->format('d/m/Y H:i') }}
                                        </span>
                                    @endif
                                </div>
                                
                                @if(!empty($penilaiData['invalid_fields']))
                                    <div class="mb-4">
                                        <h4 class="font-medium text-red-800 mb-2 flex items-center">
                                            <i data-lucide="alert-triangle" class="w-4 h-4 mr-2"></i>
                                            Field yang Tidak Sesuai:
                                        </h4>
                                        <div class="space-y-2">
                                            @foreach($penilaiData['invalid_fields'] as $field)
                                                <div class="text-sm text-red-800 bg-red-50 px-3 py-2 rounded border-l-4 border-red-400 flex items-start">
                                                    <i data-lucide="x-circle" class="w-4 h-4 mr-2 mt-0.5 text-red-500 flex-shrink-0"></i>
                                                    <span>{{ $field }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                
                                @if(!empty($penilaiData['general_notes']))
                                    <div class="border-t border-red-200 pt-4">
                                        <h4 class="font-medium text-red-800 mb-2 flex items-center">
                                            <i data-lucide="message-square" class="w-4 h-4 mr-2"></i>
                                            Keterangan Umum:
                                        </h4>
                                        <div class="space-y-2">
                                            @foreach($penilaiData['general_notes'] as $note)
                                                <div class="text-sm text-red-700 bg-red-50 px-3 py-2 rounded border-l-4 border-red-400 flex items-start">
                                                    <i data-lucide="info" class="w-4 h-4 mr-2 mt-0.5 text-red-500 flex-shrink-0"></i>
                                                    <span>{{ $note }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Informasi Periode Usulan --}}
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i data-lucide="calendar-clock" class="w-6 h-6 mr-3"></i>
                        Informasi Periode Usulan
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-800">Periode</label>
                            <p class="text-xs text-gray-600 mb-2">Periode usulan yang sedang berlangsung</p>
                            <input type="text" value="{{ $daftarPeriode->nama_periode ?? 'Tidak ada periode aktif' }}"
                                   class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                            <input type="hidden" name="periode_usulan_id" value="{{ $daftarPeriode->id ?? '' }}">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800">Masa Berlaku</label>
                            <p class="text-xs text-gray-600 mb-2">Rentang waktu periode usulan</p>
                            <input type="text" value="{{ $daftarPeriode ? \Carbon\Carbon::parse($daftarPeriode->tanggal_mulai)->isoFormat('D MMM YYYY') . ' - ' . \Carbon\Carbon::parse($daftarPeriode->tanggal_selesai)->isoFormat('D MMM YYYY') : '-' }}"
                                   class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Informasi Pegawai --}}
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5">
                    <h2 class="text-xl font-bold text-black flex items-center">
                        <i data-lucide="user" class="w-6 h-6 mr-3"></i>
                        Informasi Usulan Pegawai
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-800">Nama Lengkap</label>
                            <p class="text-xs text-gray-600 mb-2">Nama lengkap pegawai</p>
                            <input type="text" value="{{ $pegawai->nama_lengkap ?? '-' }}"
                                   class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800">NIP</label>
                            <p class="text-xs text-gray-600 mb-2">Nomor Induk Pegawai</p>
                            <input type="text" value="{{ $pegawai->nip ?? '-' }}"
                                   class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800">Jabatan Sekarang</label>
                            <p class="text-xs text-gray-600 mb-2">Jabatan fungsional saat ini</p>
                            <input type="text" value="{{ $pegawai->jabatan->jabatan ?? '-' }}"
                                   class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800">Jabatan yang Dituju</label>
                            <p class="text-xs text-gray-600 mb-2">Jabatan fungsional yang diajukan</p>
                            <input type="text" value="{{ $pegawai->jabatan && $pegawai->jabatan->getNextLevel() ? $pegawai->jabatan->getNextLevel()->jabatan : '-' }}"
                                   class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Profile Display Component --}}
            @include('backend.layouts.views.pegawai-unmul.usul-jabatan.components.profile-display', [
                'validationData' => $validationData ?? []
            ])

            {{-- Karya Ilmiah Section Component --}}
            @include('backend.layouts.views.pegawai-unmul.usul-jabatan.components.karya-ilmiah-section', [
                'validationData' => $validationData ?? []
            ])

            {{-- Dokumen Upload Component --}}
            @include('backend.layouts.views.pegawai-unmul.usul-jabatan.components.dokumen-upload', [
                'validationData' => $validationData ?? []
            ])

            {{-- BKD Upload Component --}}
            @include('backend.layouts.views.pegawai-unmul.usul-jabatan.components.bkd-upload', [
                'validationData' => $validationData ?? []
            ])

            {{-- Form Actions --}}
            @if(!$isShowMode)
            @php
                // Determine who sent the revision request based on validation data
                $isRevisionFromUniversity = false;
                $isRevisionFromFakultas = false;

                if ($isEditMode && $usulan && $usulan->status_usulan === 'Perbaikan Usulan') {
                    // Check validation data to determine source of revision
                    $adminUnivValidation = $usulan->getValidasiByRole('admin_universitas');
                    $adminFakultasValidation = $usulan->getValidasiByRole('admin_fakultas');

                    // If Admin Universitas has validation data, revision is from university
                    if (!empty($adminUnivValidation)) {
                        $isRevisionFromUniversity = true;
                    }
                    // If only Admin Fakultas has validation data, revision is from fakultas
                    elseif (!empty($adminFakultasValidation)) {
                        $isRevisionFromFakultas = true;
                    }
                    // Default: if uncertain, assume from fakultas
                    else {
                        $isRevisionFromFakultas = true;
                    }
                }
            @endphp

            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
                        Pastikan semua data yang diperlukan telah diisi dengan benar
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="history.back()"
                                class="px-6 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Batal
                        </button>

                        {{-- Save Draft Button (always available) --}}
                        <button type="submit" name="action" value="save_draft"
                                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Simpan Usulan
                        </button>

                        {{-- Conditional Submit Buttons --}}
                        @if($isEditMode && $usulan && $usulan->status_usulan === 'Perbaikan Usulan')
                            {{-- Revision Mode: Show appropriate button based on who requested revision --}}
                            @if($isRevisionFromUniversity)
                                <button type="submit" name="action" value="submit_to_university"
                                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                                    <i data-lucide="send" class="w-4 h-4"></i>
                                    Kirim ke Universitas
                                </button>
                            @elseif($isRevisionFromFakultas)
                                <button type="submit" name="action" value="submit_to_fakultas"
                                        class="px-6 py-3 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors flex items-center gap-2">
                                    <i data-lucide="send" class="w-4 h-4"></i>
                                    Kirim ke Fakultas
                                </button>
                            @endif
                        @elseif($isEditMode && $usulan && $usulan->status_usulan === 'Perbaikan dari Tim Sister')
                            {{-- Perbaikan dari Tim Sister Mode --}}
                            <button type="submit" name="action" value="submit_perbaikan_sister"
                                    class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                                <i data-lucide="send" class="w-4 h-4"></i>
                                Kirim Perbaikan ke Universitas
                            </button>
                        @else
                            {{-- Normal Mode: Submit to fakultas --}}
                            <button type="submit" name="action" value="submit"
                                    class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                                <i data-lucide="send" class="w-4 h-4"></i>
                                Kirim Usulan
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            </form>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation - SIMPLIFIED
    const form = document.getElementById('usulan-form');
    if (form) {
        console.log('Form found, validation active');

        form.addEventListener('submit', function(e) {
            console.log('Form submission attempted');
            console.log('Form action:', form.action);
            console.log('Form method:', form.method);

            // Check if action is selected
            const actionField = form.querySelector('input[name="action"]:checked, button[name="action"][type="submit"]');
            if (!actionField) {
                e.preventDefault();
                console.log('No action selected - preventing submission');
                alert('Mohon pilih aksi (Simpan Usulan, Kirim Usulan, atau Kirim ke Universitas/Fakultas).');
                return;
            }

            console.log('Action selected:', actionField.value);
            console.log('Basic validation passed - allowing submission');
        });
    } else {
        console.log('Form not found');
    }
});
</script>
@endsection

