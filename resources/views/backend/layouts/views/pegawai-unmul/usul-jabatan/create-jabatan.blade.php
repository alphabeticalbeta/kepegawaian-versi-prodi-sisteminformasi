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

            {{-- Notification for Revision Status --}}
            @if($isEditMode && $usulan && $usulan->status_usulan === 'Perbaikan Usulan')
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

