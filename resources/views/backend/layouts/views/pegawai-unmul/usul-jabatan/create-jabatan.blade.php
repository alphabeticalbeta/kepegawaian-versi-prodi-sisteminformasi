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

            {{-- Hasil Penilaian Tim Penilai yang Diteruskan oleh Admin Universitas Section --}}
            @php
                $forwardedPenilaiResult = $usulan->validasi_data['admin_universitas']['forward_penilai_result'] ?? null;
                $isForwardedFromPenilai = $forwardedPenilaiResult && ($forwardedPenilaiResult['catatan_source'] ?? '') === 'tim_penilai';
                $directReview = $usulan->validasi_data['admin_universitas']['direct_review'] ?? null;
                $isDirectFromAdmin = $directReview && ($directReview['catatan_source'] ?? '') === 'admin_universitas';
            @endphp
            
            @if($isEditMode && $usulan && $usulan->status_usulan === 'Perbaikan Usulan' && $isForwardedFromPenilai)
                <div class="mb-6 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-5">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i data-lucide="users" class="w-6 h-6 mr-3"></i>
                            Hasil Penilaian dari Tim Penilai Universitas
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <div class="flex items-start">
                                <i data-lucide="info" class="w-5 h-5 text-blue-600 mt-0.5 mr-3"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-blue-800">Informasi Penyampaian</h4>
                                    <p class="text-sm text-blue-700 mt-1">
                                        Admin Universitas telah menyampaikan hasil penilaian dari Tim Penilai Universitas. Silakan periksa hasil penilaian dan lakukan perbaikan sesuai catatan di bawah ini.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Hasil Penilaian Asli dari Tim Penilai --}}
                        <div class="bg-white border border-blue-200 rounded-lg p-4 mb-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                                <i data-lucide="clipboard-list" class="w-4 h-4 mr-2 text-blue-600"></i>
                                Hasil Penilaian Tim Penilai:
                            </h4>
                            
                            @php
                                // Ambil semua data penilai dengan catatan dan field bermasalah
                                $allPenilaiReviews = [];
                                $penilaiReviewsData = $usulan->validasi_data['tim_penilai']['reviews'] ?? [];
                                $globalValidationData = $usulan->validasi_data['tim_penilai']['validation'] ?? [];
                                
                                foreach ($penilaiReviewsData as $reviewId => $review) {
                                    $catatan = null;
                                    
                                    // Check multiple possible catatan fields
                                    if (!empty($review['catatan'])) {
                                        $catatan = $review['catatan'];
                                    } elseif (!empty($review['perbaikan_usulan']['catatan'])) {
                                        $catatan = $review['perbaikan_usulan']['catatan'];
                                    } elseif (!empty($review['catatan_perbaikan'])) {
                                        $catatan = $review['catatan_perbaikan'];
                                    }
                                    
                                    if ($catatan) {
                                        // Ambil field bermasalah untuk penilai ini
                                        $invalidFieldsForPenilai = [];
                                        if (!empty($review['validation'])) {
                                            foreach ($review['validation'] as $category => $fields) {
                                                if (is_array($fields)) {
                                                    foreach ($fields as $field => $fieldData) {
                                                        if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                                                            $invalidFieldsForPenilai[] = [
                                                                'category' => $category,
                                                                'field' => $field,
                                                                'keterangan' => $fieldData['keterangan'] ?? 'Tidak ada keterangan'
                                                            ];
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        
                                        $allPenilaiReviews[] = [
                                            'penilai_id' => $review['penilai_id'] ?? $reviewId,
                                            'catatan' => $catatan,
                                            'tanggal' => $review['tanggal_return'] ?? null,
                                            'invalid_fields' => $invalidFieldsForPenilai
                                        ];
                                    }
                                }
                            @endphp
                            
                            @if(!empty($allPenilaiReviews))
                                <div class="space-y-6">
                                    @foreach($allPenilaiReviews as $index => $review)
                                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                            <div class="mb-4">
                                                <h5 class="text-lg font-semibold text-blue-800 mb-2 flex items-center">
                                                    <i data-lucide="user" class="w-5 h-5 mr-2"></i>
                                                    Hasil Review Penilai {{ $review['penilai_id'] }}
                                                    @if($review['tanggal'])
                                                        <span class="text-sm font-normal text-gray-500 ml-2">
                                                            ({{ \Carbon\Carbon::parse($review['tanggal'])->format('d F Y, H:i') }})
                                                        </span>
                                                    @endif
                                                </h5>
                                            </div>
                                            
                                            {{-- Catatan Umum --}}
                                            <div class="mb-4">
                                                <h6 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                                                    <i data-lucide="message-circle" class="w-4 h-4 mr-2 text-blue-600"></i>
                                                    Catatan Umum:
                                                </h6>
                                                <div class="bg-white border border-gray-200 rounded-lg p-3">
                                                    <div class="text-sm text-gray-700 whitespace-pre-wrap">
                                                        {{ $review['catatan'] }}
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            {{-- Field yang Tidak Sesuai --}}
                                            @if(!empty($review['invalid_fields']))
                                                <div>
                                                    <h6 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                                                        <i data-lucide="alert-triangle" class="w-4 h-4 mr-2 text-red-600"></i>
                                                        Catatan dari setiap field yang tidak sesuai:
                                                    </h6>
                                                    <div class="bg-white border border-red-200 rounded-lg p-3">
                                                        <div class="space-y-2">
                                                            @foreach($review['invalid_fields'] as $fieldIndex => $field)
                                                                <div class="flex items-start gap-3">
                                                                    <span class="flex-shrink-0 w-5 h-5 bg-red-100 border border-red-300 rounded-full flex items-center justify-center text-xs font-bold text-red-800">
                                                                        {{ $fieldIndex + 1 }}
                                                                    </span>
                                                                    <div class="flex-1">
                                                                        <div class="flex items-center gap-2 mb-1">
                                                                            <i data-lucide="x-circle" class="w-3 h-3 text-red-600"></i>
                                                                            <span class="text-sm font-semibold text-red-800">
                                                                                {{ ucwords(str_replace('_', ' ', $field['category'])) }} > 
                                                                                {{ ucwords(str_replace('_', ' ', $field['field'])) }}
                                                                            </span>
                                                                        </div>
                                                                        <p class="text-xs text-red-700 ml-5">
                                                                            {{ $field['keterangan'] }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div>
                                                    <h6 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                                                        <i data-lucide="check-circle" class="w-4 h-4 mr-2 text-green-600"></i>
                                                        Status Field:
                                                    </h6>
                                                    <div class="bg-white border border-green-200 rounded-lg p-3">
                                                        <div class="text-sm text-green-700">
                                                            Semua field sudah sesuai dengan ketentuan.
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                {{-- Fallback ke original catatan jika tidak ada reviews --}}
                                <div class="text-sm text-gray-700 whitespace-pre-wrap bg-gray-50 p-3 rounded">{{ $forwardedPenilaiResult['original_catatan'] }}</div>
                            @endif
                        </div>



                        {{-- Catatan Tambahan dari Admin Universitas (jika ada) --}}
                        @if(!empty($forwardedPenilaiResult['admin_catatan']))
                            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                                    <i data-lucide="message-circle" class="w-4 h-4 mr-2 text-orange-600"></i>
                                    Catatan Tambahan dari Admin Universitas:
                                </h4>
                                <div class="text-sm text-gray-700 whitespace-pre-wrap bg-orange-50 p-3 rounded">{{ $forwardedPenilaiResult['admin_catatan'] }}</div>
                            </div>
                        @endif

                        {{-- Informasi Waktu Penyampaian --}}
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                            <div class="flex items-center justify-between text-xs text-gray-600">
                                <span class="flex items-center">
                                    <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                    Diteruskan pada: {{ \Carbon\Carbon::parse($forwardedPenilaiResult['forwarded_at'])->format('d F Y, H:i') }}
                                </span>
                                <span class="flex items-center">
                                    <i data-lucide="user" class="w-3 h-3 mr-1"></i>
                                    oleh Admin Universitas
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Perbaikan dari Admin Universitas Section (Direct Review) --}}
            @if($isEditMode && $usulan && $usulan->status_usulan === 'Perbaikan Usulan' && $isDirectFromAdmin)
                <div class="mb-6 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-orange-600 to-red-600 px-6 py-5">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i data-lucide="alert-triangle" class="w-6 h-6 mr-3"></i>
                            Perbaikan dari Admin Universitas
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                            <div class="flex items-start">
                                <i data-lucide="info" class="w-5 h-5 text-orange-600 mt-0.5 mr-3"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-orange-800">Catatan Perbaikan</h4>
                                    <p class="text-sm text-orange-700 mt-1">
                                        Admin Universitas telah mengembalikan usulan ini untuk perbaikan. Silakan periksa dan perbaiki sesuai catatan di bawah ini.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-200 rounded-lg p-4 mb-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Detail Perbaikan:</h4>
                            <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $directReview['catatan'] }}</div>
                        </div>

                        {{-- Informasi Waktu Review --}}
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                            <div class="flex items-center justify-between text-xs text-gray-600">
                                <span class="flex items-center">
                                    <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                    Direview pada: {{ \Carbon\Carbon::parse($directReview['reviewed_at'])->format('d F Y, H:i') }}
                                </span>
                                <span class="flex items-center">
                                    <i data-lucide="user" class="w-3 h-3 mr-1"></i>
                                    oleh Admin Universitas
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Notification for Revision Status --}}
            @if($isEditMode && $usulan && $usulan->status_usulan === 'Perbaikan Usulan' && !$isForwardedFromPenilai && !$isDirectFromAdmin)
            @php
                // Determine which role sent the revision request
                $adminUnivValidation = $usulan->getValidasiByRole('admin_universitas');
                $adminFakultasValidation = $usulan->getValidasiByRole('admin_fakultas');

                $revisionFromRole = 'Admin Fakultas'; // Default
                $revisionFromRoleColor = 'amber';

                if (!empty($adminUnivValidation)) {
                    $revisionFromRole = 'Admin Universitas';
                    $revisionFromRoleColor = 'blue';
                } elseif (!empty($adminFakultasValidation)) {
                    $revisionFromRole = 'Admin Fakultas';
                    $revisionFromRoleColor = 'amber';
                }
            @endphp

            <div class="mb-6 bg-{{ $revisionFromRoleColor }}-50 border border-{{ $revisionFromRoleColor }}-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-{{ $revisionFromRoleColor }}-600"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-{{ $revisionFromRoleColor }}-800">
                            Usulan Dikembalikan untuk Perbaikan
                        </h3>
                        <div class="mt-2 text-sm text-{{ $revisionFromRoleColor }}-700">
                            <p class="mb-2"><strong>Catatan dari {{ $revisionFromRole }}:</strong></p>
                            <p class="bg-white p-3 rounded border border-{{ $revisionFromRoleColor }}-200">{{ $usulan->catatan_verifikator ?? 'Tidak ada catatan spesifik' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- All Validation Notes Summary --}}
            @if($isEditMode && !empty($validationData))
            @php
                // Collect all validation issues
                $allValidationIssues = [];
                $fieldGroups = ['data_pribadi', 'data_kepegawaian', 'data_pendidikan', 'data_kinerja', 'dokumen_profil', 'bkd', 'karya_ilmiah', 'dokumen_usulan', 'syarat_guru_besar'];
                
                foreach ($fieldGroups as $group) {
                    foreach ($validationData as $role => $data) {
                        if (isset($data[$group])) {
                            foreach ($data[$group] as $field => $validation) {
                                if (isset($validation['status']) && $validation['status'] === 'tidak_sesuai' && !empty($validation['keterangan'])) {
                                    $roleName = str_replace('_', ' ', ucfirst($role));
                                    $allValidationIssues[] = [
                                        'group' => $group,
                                        'field' => $field,
                                        'role' => $roleName,
                                        'note' => $validation['keterangan']
                                    ];
                                }
                            }
                        }
                    }
                }
            @endphp

            @if(!empty($allValidationIssues))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-medium text-red-800 mb-3">
                            Catatan Perbaikan dari Semua Admin
                        </h3>
                        <div class="space-y-3">
                            @foreach($allValidationIssues as $issue)
                            <div class="bg-white p-3 rounded border border-red-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                {{ $issue['role'] }}
                                            </span>
                                            <span class="text-xs text-gray-600">
                                                {{ ucfirst(str_replace('_', ' ', $issue['group'])) }} - {{ ucfirst(str_replace('_', ' ', $issue['field'])) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-red-700">{{ $issue['note'] }}</p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endif
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
                            {{-- Revision Mode: Submit back to university --}}
                            <button type="submit" name="action" value="submit_to_university"
                                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                                <i data-lucide="send" class="w-4 h-4"></i>
                                Kirim ke Universitas
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
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced field-level validation
    const form = document.getElementById('usulan-form');
    if (form) {
        console.log('Form found, enhanced validation active');

        // Field validation rules
        const validationRules = {
            'nama_lengkap': { required: true, minLength: 3 },
            'nip': { required: true, pattern: /^\d{18,20}$/ },
            'email': { required: true, pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/ },
            'tempat_lahir': { required: true },
            'tanggal_lahir': { required: true },
            'jenis_kelamin': { required: true },
            'nomor_handphone': { required: true, pattern: /^(\+62|62|0)8[1-9][0-9]{6,9}$/ },
            'pendidikan_terakhir': { required: true },
            'nama_universitas_sekolah': { required: true },
            'nama_prodi_jurusan': { required: true },
            'mata_kuliah_diampu': { required: true },
            'ranting_ilmu_kepakaran': { required: true },
            'url_profil_sinta': { required: false, pattern: /^https?:\/\/.+$/ }
        };

        // Real-time validation function
        function validateField(field) {
            const fieldName = field.name;
            const value = field.value.trim();
            const rules = validationRules[fieldName];
            
            if (!rules) return true;

            // Remove existing error styling
            field.classList.remove('border-red-500', 'bg-red-50');
            field.classList.add('border-gray-300', 'bg-white');
            
            // Remove existing error message
            const existingError = field.parentNode.querySelector('.field-error');
            if (existingError) {
                existingError.remove();
            }

            let isValid = true;
            let errorMessage = '';

            // Required validation
            if (rules.required && !value) {
                isValid = false;
                errorMessage = 'Field ini wajib diisi';
            }
            // Pattern validation
            else if (rules.pattern && value && !rules.pattern.test(value)) {
                isValid = false;
                if (fieldName === 'email') {
                    errorMessage = 'Format email tidak valid';
                } else if (fieldName === 'nip') {
                    errorMessage = 'NIP harus berupa angka 18-20 digit';
                } else if (fieldName === 'nomor_handphone') {
                    errorMessage = 'Format nomor handphone tidak valid';
                } else if (fieldName === 'url_profil_sinta') {
                    errorMessage = 'URL harus dimulai dengan http:// atau https://';
                }
            }
            // Min length validation
            else if (rules.minLength && value && value.length < rules.minLength) {
                isValid = false;
                errorMessage = `Minimal ${rules.minLength} karakter`;
            }

            // Apply styling and show error message
            if (!isValid) {
                field.classList.remove('border-gray-300', 'bg-white');
                field.classList.add('border-red-500', 'bg-red-50');
                
                const errorDiv = document.createElement('div');
                errorDiv.className = 'field-error text-sm text-red-600 mt-1 flex items-center gap-1';
                errorDiv.innerHTML = `
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    ${errorMessage}
                `;
                field.parentNode.appendChild(errorDiv);
            }

            return isValid;
        }

        // Add validation to all form fields
        const formFields = form.querySelectorAll('input, select, textarea');
        formFields.forEach(field => {
            // Real-time validation on input
            field.addEventListener('input', function() {
                validateField(this);
            });

            // Validation on blur
            field.addEventListener('blur', function() {
                validateField(this);
            });
        });

        // Form submission validation
        form.addEventListener('submit', function(e) {
            console.log('Form submission attempted');

            // Check if action is selected
            const actionField = form.querySelector('input[name="action"]:checked, button[name="action"][type="submit"]');
            if (!actionField) {
                e.preventDefault();
                console.log('No action selected - preventing submission');
                Swal.fire({
                    title: 'Aksi Belum Dipilih',
                    text: 'Mohon pilih aksi (Simpan Usulan, Kirim Usulan, atau Kirim ke Universitas/Fakultas).',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Validate all fields
            let allValid = true;
            formFields.forEach(field => {
                if (!validateField(field)) {
                    allValid = false;
                }
            });

            if (!allValid) {
                e.preventDefault();
                console.log('Validation failed - preventing submission');
                Swal.fire({
                    title: 'Validasi Gagal',
                    text: 'Mohon perbaiki field yang bermasalah sebelum melanjutkan.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            console.log('Action selected:', actionField.value);
            console.log('All validations passed - allowing submission');
        });

        // Auto-save functionality
        let autoSaveTimer;
        formFields.forEach(field => {
            field.addEventListener('input', function() {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(() => {
                    // Auto-save logic here if needed
                    console.log('Auto-save triggered');
                }, 2000);
            });
        });

    } else {
        console.log('Form not found');
    }

    // Enhanced error display for validation notes
    function showValidationError(fieldName, message) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field) {
            field.classList.add('border-red-500', 'bg-red-50');
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error text-sm text-red-600 mt-1 flex items-center gap-1';
            errorDiv.innerHTML = `
                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                ${message}
            `;
            field.parentNode.appendChild(errorDiv);
        }
    }

    // Initialize validation for fields with existing validation data
    @if($isEditMode && !empty($validationData))
        @foreach($validationData as $role => $data)
            @foreach($data as $group => $fields)
                @foreach($fields as $field => $validation)
                    @if(isset($validation['status']) && $validation['status'] === 'tidak_sesuai' && !empty($validation['keterangan']))
                        showValidationError('{{ $field }}', '{{ $validation['keterangan'] }}');
                    @endif
                @endforeach
            @endforeach
        @endforeach
    @endif
});
</script>
@endsection

