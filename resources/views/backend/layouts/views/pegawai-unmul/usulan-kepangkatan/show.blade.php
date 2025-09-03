@extends('backend.layouts.roles.pegawai-unmul.app')

@section('title', 'Detail Usulan Kepangkatan')

@section('content')
        @php
            // Check if usulan is in view-only status
            $viewOnlyStatuses = [
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_SUDAH_DIKIRIM_KE_BKN,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_BKN,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN
            ];
            
            // Status yang dapat diedit (tidak view-only) - hanya status draft dan permintaan perbaikan
            $editableStatuses = [
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_DRAFT_USULAN,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN
            ];
            
            if (in_array($usulan->status_usulan, $editableStatuses)) {
                $isViewOnly = false;  // Dapat diedit
            } else {
                $isViewOnly = in_array($usulan->status_usulan, $viewOnlyStatuses);  // View-only berdasarkan array
            }
        @endphp
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/30">
    {{-- Header Section --}}
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6 flex flex-wrap gap-4 justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Detail Usulan Kepangkatan
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Informasi lengkap usulan kepangkatan
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('pegawai-unmul.dashboard-pegawai-unmul') }}"
                       class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Flash Messages -->
        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-green-800 font-medium">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <span class="text-red-800 font-medium">{{ session('error') }}</span>
            </div>
        </div>
        @endif

        {{-- Status Badge --}}
        <div class="mb-6">
            @php
                $statusColors = [
                    'Draft' => 'bg-gray-100 text-gray-800 border-gray-300',
                    'Diajukan' => 'bg-blue-100 text-blue-800 border-blue-300',
                    'Sedang Direview' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                    'Disetujui' => 'bg-green-100 text-green-800 border-green-300',
                    'Direkomendasikan' => 'bg-purple-100 text-purple-800 border-purple-300',
                    'Ditolak' => 'bg-red-100 text-red-800 border-red-300',
                    'Dikembalikan ke Pegawai' => 'bg-orange-100 text-orange-800 border-orange-300',
                    'Perlu Perbaikan' => 'bg-amber-100 text-amber-800 border-amber-300',
                ];
                $statusColor = $statusColors[$usulan->status_usulan] ?? 'bg-gray-100 text-gray-800 border-gray-300';
            @endphp
            <div class="inline-flex items-center px-4 py-2 rounded-full border {{ $statusColor }}">
                <span class="text-sm font-medium">Status: {{ $usulan->status_usulan }}</span>
            </div>
        </div>



        {{-- Field Validation Display --}}
        @php
            // Get validation data based on status
            $validationData = [];
            $validationSource = '';
            $hasValidationIssues = false;
            
            if ($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS) {
                $validationData = $usulan->getValidasiByRole('kepegawaian_universitas') ?? [];
                $validationSource = 'Kepegawaian Universitas';
            } elseif ($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) {
                $validationData = $usulan->getValidasiByRole('kepegawaian_universitas') ?? [];
                $validationSource = 'Kepegawaian Universitas';
            } elseif ($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN) {
                $validationData = $usulan->getValidasiByRole('kepegawaian_universitas') ?? [];
                $validationSource = 'Kepegawaian Universitas';
            }
            
            $hasValidationIssues = !empty($validationData) && isset($validationData['validation']);

            // Field labels mapping
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
                    'nama_prodi_jurusan' => 'Nama Program Studi/Jurusan'
                ],
                'data_kinerja' => [
                    'predikat_kinerja_tahun_pertama' => 'Predikat SKP Tahun ' . (date('Y') - 1),
                    'predikat_kinerja_tahun_kedua' => 'Predikat SKP Tahun ' . (date('Y') - 2),
                    'nilai_konversi' => 'Nilai Konversi ' . (date('Y') - 1)
                ],
                'dokumen_profil' => [
                    'sk_pangkat_terakhir' => 'SK Pangkat Terakhir',
                    'sk_jabatan_terakhir' => 'SK Jabatan Terakhir',
                    'skp_tahun_pertama' => 'SKP Tahun ' . (date('Y') - 1),
                    'skp_tahun_kedua' => 'SKP Tahun ' . (date('Y') - 2),
                    'pak_konversi' => 'PAK Konversi ' . (date('Y') - 1),
                    'pak_integrasi' => 'PAK Integrasi',
                    'sk_cpns' => 'SK CPNS',
                    'sk_pns' => 'SK PNS',
                    'ijazah_terakhir' => 'Ijazah Terakhir',
                    'transkrip_nilai_terakhir' => 'Transkrip Nilai Terakhir'
                ]
            ];
        @endphp

        @if($hasValidationIssues && ($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS || $usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN || $usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN))
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="@if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) bg-gradient-to-r from-purple-600 to-indigo-600 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN) bg-gradient-to-r from-blue-600 to-indigo-600 @else bg-gradient-to-r from-red-600 to-pink-600 @endif px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN)
                        <i data-lucide="building" class="w-6 h-6 mr-3"></i>
                    @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN)
                        <i data-lucide="clipboard-check" class="w-6 h-6 mr-3"></i>
                    @else
                        <i data-lucide="alert-triangle" class="w-6 h-6 mr-3"></i>
                    @endif
                    Field-Field Tidak Sesuai
                </h2>
            </div>
            <div class="p-6">
                <div class="mb-4 p-4 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) bg-purple-50 border-purple-200 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN) bg-blue-50 border-blue-200 @else bg-red-50 border-red-200 @endif rounded-lg border">
                    <div class="flex items-center">
                        <i data-lucide="info" class="w-5 h-5 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) text-purple-600 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN) text-blue-600 @else text-red-600 @endif mr-3"></i>
                        <div>
                            <h4 class="text-sm font-semibold @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) text-purple-800 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN) text-blue-800 @else text-red-800 @endif mb-1">Informasi Validasi</h4>
                            <p class="text-sm @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) text-purple-700 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN) text-blue-700 @else text-red-700 @endif">
                                Berikut adalah field-field yang tidak sesuai berdasarkan validasi dari {{ $validationSource }}.
                                Field-field ini harus diperbaiki sebelum usulan dapat diajukan kembali.
                                @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN)
                                    <br><br><strong>Note:</strong> Usulan ini dikembalikan dari BKN dan perlu dikirim kembali ke Kepegawaian Universitas setelah perbaikan.
                                @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN)
                                    <br><br><strong>Note:</strong> Usulan ini akan dikirim ke BKN setelah perbaikan.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    @foreach($validationData['validation'] ?? [] as $groupKey => $groupData)
                        @if(isset($fieldLabels[$groupKey]))
                            @foreach($groupData as $fieldKey => $fieldData)
                                @if(isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai')
                                    <div class="border-l-4 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) border-purple-500 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN) border-blue-500 @else border-red-500 @endif pl-4 py-3 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) bg-purple-50 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN) bg-blue-50 @else bg-red-50 @endif rounded-r-lg">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-2 mb-2">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) bg-purple-100 text-purple-800 border-purple-300 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN) bg-blue-100 text-blue-800 border-blue-300 @else bg-red-100 text-red-800 border-red-300 @endif border">
                                                        <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                                        Tidak Sesuai
                                                    </span>
                                                    <span class="text-xs text-gray-500">{{ $fieldLabels[$groupKey][$fieldKey] ?? ucwords(str_replace('_', ' ', $fieldKey)) }}</span>
                                                </div>
                                                @if(!empty($fieldData['keterangan']))
                                                    <p class="text-sm @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) text-purple-700 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN) text-blue-700 @else text-red-700 @endif">{{ $fieldData['keterangan'] }}</p>
                                                @else
                                                    <p class="text-sm @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) text-purple-700 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN) text-blue-700 @else text-red-700 @endif">Tidak ada keterangan spesifik</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Field Validation untuk Dokumen Usulan --}}
                    @if(!empty($validationData) && isset($validationData['validation']['dokumen_usulan']))
                        @php
                            $dokumenUsulanFields = [
                                'dokumen_ukom_sk_jabatan',
                                'dokumen_pendukung_jabatan_administrasi',
                                'dokumen_pendukung_jabatan_fungsional_tertentu',
                                'surat_pelantikan_berita_acara',
                                'surat_pencantuman_gelar'
                            ];
                            $hasInvalidDokumenFields = false;
                            foreach ($dokumenUsulanFields as $fieldKey) {
                                if (isset($validationData['validation']['dokumen_usulan'][$fieldKey]['status']) && 
                                    $validationData['validation']['dokumen_usulan'][$fieldKey]['status'] === 'tidak_sesuai') {
                                    $hasInvalidDokumenFields = true;
                                    break;
                                }
                            }
                        @endphp
                        
                        @if($hasInvalidDokumenFields)
                            <div class="border-l-4 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) border-purple-500 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN) border-blue-500 @else border-red-500 @endif pl-4 py-3 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) bg-purple-50 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN) bg-blue-50 @else bg-red-50 @endif rounded-r-lg">
                                <div class="flex items-start">
                                    <i data-lucide="alert-triangle" class="w-5 h-5 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) text-purple-600 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN) text-blue-600 @else text-red-600 @endif mr-3 mt-0.5"></i>
                                    <div class="flex-1">
                                        <div class="text-sm font-semibold @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) text-purple-800 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN) text-blue-800 @else text-red-800 @endif mb-2">
                                            Field-Field Dokumen Tidak Sesuai dari {{ $validationSource }}:
                                        </div>
                                        <div class="space-y-2">
                                            @foreach($dokumenUsulanFields as $fieldKey)
                                                @if(isset($validationData['validation']['dokumen_usulan'][$fieldKey]['status']) && 
                                                    $validationData['validation']['dokumen_usulan'][$fieldKey]['status'] === 'tidak_sesuai')
                                                    <div class="bg-white border @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) border-purple-200 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN) border-blue-200 @else border-red-200 @endif rounded-lg p-3">
                                                        <div class="flex items-start justify-between">
                                                            <div class="flex-1">
                                                                <div class="text-sm font-medium @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) text-purple-800 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN) text-blue-800 @else text-red-800 @endif">
                                                                    @if($fieldKey === 'dokumen_ukom_sk_jabatan')
                                                                        Dokumen UKOM dan SK Jabatan
                                                                    @elseif($fieldKey === 'dokumen_pendukung_jabatan_administrasi')
                                                                        Dokumen Pendukung Jabatan Administrasi
                                                                    @elseif($fieldKey === 'dokumen_pendukung_jabatan_fungsional_tertentu')
                                                                        Dokumen Pendukung Jabatan Fungsional Tertentu
                                                                    @elseif($fieldKey === 'surat_pelantikan_berita_acara')
                                                                        Surat Pelantikan dan Berita Acara Jabatan Terakhir
                                                                    @elseif($fieldKey === 'surat_pencantuman_gelar')
                                                                        Surat Pencantuman Gelar
                                                                    @else
                                                                        {{ ucwords(str_replace('_', ' ', $fieldKey)) }}
                                                                    @endif
                                                                </div>
                                                                @if(!empty($validationData['validation']['dokumen_usulan'][$fieldKey]['keterangan']))
                                                                    <div class="text-sm @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) text-purple-700 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN) text-blue-700 @else text-red-700 @endif mt-1">
                                                                        <strong>Keterangan:</strong> {{ $validationData['validation']['dokumen_usulan'][$fieldKey]['keterangan'] }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) bg-purple-100 text-purple-800 border-purple-200 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN) bg-blue-100 text-blue-800 border-blue-200 @else bg-red-100 text-red-800 border-red-200 @endif border">
                                                                Tidak Sesuai
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- Keterangan Umum untuk Semua Status --}}
                    @if(!empty($validationData['keterangan_umum'] ?? ''))
                        <div class="border-l-4 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) border-purple-500 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN) border-blue-500 @else border-red-500 @endif pl-4 py-3 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) bg-purple-50 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN) bg-blue-50 @else bg-red-50 @endif rounded-r-lg">
                            <div class="flex items-start">
                                <i data-lucide="sticky-note" class="w-5 h-5 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) text-purple-600 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN) text-blue-600 @else text-red-600 @endif mr-3 mt-0.5"></i>
                                <div class="flex-1">
                                    <div class="text-sm font-semibold @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) text-purple-800 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN) text-blue-800 @else text-red-800 @endif mb-2">
                                        Keterangan Umum dari {{ $validationSource }}:
                                    </div>
                                    <div class="bg-white border @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) border-purple-200 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN) border-blue-200 @else border-red-200 @endif rounded-lg p-3">
                                        <div class="text-sm @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) text-purple-700 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN) text-blue-700 @else text-red-700 @endif">
                                            {{ $validationData['keterangan_umum'] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
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
                        <input type="text" value="{{ $usulan->periodeUsulan->nama_periode ?? 'Tidak ada periode aktif' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Masa Berlaku</label>
                        <p class="text-xs text-gray-600 mb-2">Rentang waktu periode usulan</p>
                        <input type="text" value="{{ $usulan->periodeUsulan ? \Carbon\Carbon::parse($usulan->periodeUsulan->tanggal_mulai)->isoFormat('D MMM YYYY') . ' - ' . \Carbon\Carbon::parse($usulan->periodeUsulan->tanggal_selesai)->isoFormat('D MMM YYYY') : '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                </div>
            </div>
        </div>

        {{-- Informasi Usulan Kepangkatan --}}
        <form action="{{ route('pegawai-unmul.usulan-kepangkatan.update', $usulan) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
                            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="award" class="w-6 h-6 mr-3"></i>
                    Informasi Usulan Kepangkatan
                </h2>
            </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-800">Jenis Usulan Pangkat</label>
                            <p class="text-xs text-gray-600 mb-2">Jenis usulan pangkat yang dipilih</p>
                            <input type="text" value="{{ $usulan->data_usulan['jenis_usulan_pangkat'] ?? '-' }}"
                                   class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800">Pangkat Tujuan</label>
                            <p class="text-xs text-gray-600 mb-2">Pangkat yang ingin diajukan</p>
                            @if($isViewOnly)
                                <input type="text" value="{{ $usulan->pangkatTujuan->pangkat ?? '-' }}"
                                       class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                            @else
                                <select name="pangkat_tujuan_id" class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('pangkat_tujuan_id') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                                    @php
                                        $currentPangkat = $usulan->pegawai->pangkat;
                                        $availablePangkats = \App\Models\KepegawaianUniversitas\Pangkat::where('hierarchy_level', '>', $currentPangkat->hierarchy_level ?? 0)
                                            ->where('status_pangkat', $currentPangkat->status_pangkat ?? 'PNS')
                                            ->orderBy('hierarchy_level', 'asc')
                                            ->get();
                                    @endphp
                                    
                                    @if($availablePangkats->count() > 0)
                                        @foreach($availablePangkats as $pangkat)
                                            <option value="{{ $pangkat->id }}" {{ $usulan->pangkat_tujuan_id == $pangkat->id ? 'selected' : '' }}>
                                                {{ $pangkat->pangkat }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="">Tidak ada pangkat tersedia</option>
                                    @endif
                                </select>
                                @error('pangkat_tujuan_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        {{-- Informasi Pegawai --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="user" class="w-6 h-6 mr-3"></i>
                    Informasi Pegawai
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Nama Lengkap</label>
                        <p class="text-xs text-gray-600 mb-2">Nama lengkap dengan gelar</p>
                        @php
                            $gelarDepan = $usulan->pegawai->gelar_depan ?? '';
                            $namaLengkap = $usulan->pegawai->nama_lengkap ?? '';
                            $gelarBelakang = $usulan->pegawai->gelar_belakang ?? '';
                            
                            $namaLengkapDisplay = '';
                            
                            // Tambahkan gelar depan jika ada dan bukan "-"
                            if (!empty($gelarDepan) && $gelarDepan !== '-') {
                                $namaLengkapDisplay .= $gelarDepan . ' ';
                            }
                            
                            // Tambahkan nama lengkap
                            $namaLengkapDisplay .= $namaLengkap;
                            
                            // Tambahkan gelar belakang jika ada dan bukan "-"
                            if (!empty($gelarBelakang) && $gelarBelakang !== '-') {
                                $namaLengkapDisplay .= ' ' . $gelarBelakang;
                            }
                        @endphp
                        <input type="text" value="{{ $namaLengkapDisplay ?: '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">NIP</label>
                        <p class="text-xs text-gray-600 mb-2">Nomor Induk Pegawai</p>
                        <input type="text" value="{{ $usulan->pegawai->nip ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Jenis Pegawai</label>
                        <p class="text-xs text-gray-600 mb-2">Kategori pegawai</p>
                        <input type="text" value="{{ $usulan->pegawai->jenis_pegawai ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Status Kepegawaian</label>
                        <p class="text-xs text-gray-600 mb-2">Status kepegawaian pegawai</p>
                        <input type="text" value="{{ $usulan->pegawai->status_kepegawaian ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Unit Kerja</label>
                        <p class="text-xs text-gray-600 mb-2">Unit kerja pegawai</p>
                        @php
                            $unitKerjaDisplay = '';
                            if ($usulan->pegawai->unitKerja) {
                                $unitKerjaDisplay = $usulan->pegawai->unitKerja->nama;
                                
                                // Tambahkan sub unit kerja jika ada
                                if ($usulan->pegawai->unitKerja->subUnitKerja) {
                                    $unitKerjaDisplay .= ' - ' . $usulan->pegawai->unitKerja->subUnitKerja->nama;
                                    
                                    // Tambahkan unit kerja utama jika ada
                                    if ($usulan->pegawai->unitKerja->subUnitKerja->unitKerja) {
                                        $unitKerjaDisplay = $usulan->pegawai->unitKerja->subUnitKerja->unitKerja->nama . ' - ' . $unitKerjaDisplay;
                                    }
                                }
                            }
                        @endphp
                        <input type="text" value="{{ $unitKerjaDisplay ?: '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Email</label>
                        <p class="text-xs text-gray-600 mb-2">Alamat email pegawai</p>
                        <input type="text" value="{{ $usulan->pegawai->email ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">No Kartu Pegawai</label>
                        <p class="text-xs text-gray-600 mb-2">Nomor kartu pegawai</p>
                        <input type="text" value="{{ $usulan->pegawai->nomor_kartu_pegawai ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Tempat Lahir</label>
                        <p class="text-xs text-gray-600 mb-2">Tempat lahir pegawai</p>
                        <input type="text" value="{{ $usulan->pegawai->tempat_lahir ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Tanggal Lahir</label>
                        <p class="text-xs text-gray-600 mb-2">Tanggal lahir pegawai</p>
                        <input type="text" value="{{ $usulan->pegawai->tanggal_lahir ? \Carbon\Carbon::parse($usulan->pegawai->tanggal_lahir)->isoFormat('D MMMM YYYY') : '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                                            <div>
                            <label class="block text-sm font-semibold text-gray-800">Jenis Kelamin</label>
                            <p class="text-xs text-gray-600 mb-2">Jenis kelamin pegawai</p>
                            <input type="text" value="{{ $usulan->pegawai->jenis_kelamin ?? '-' }}"
                                   class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800">Nomor Handphone</label>
                            <p class="text-xs text-gray-600 mb-2">Nomor telepon seluler pegawai</p>
                            <input type="text" value="{{ $usulan->pegawai->nomor_handphone ?? '-' }}"
                                   class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                        </div>
                </div>
            </div>
        </div>

        {{-- Data Kepegawaian --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="briefcase" class="w-6 h-6 mr-3"></i>
                    Data Kepegawaian
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Pangkat Saat Ini</label>
                        <p class="text-xs text-gray-600 mb-2">Pangkat terakhir pegawai</p>
                        <input type="text" value="{{ $usulan->pegawai->pangkat->pangkat ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">TMT Pangkat</label>
                        <p class="text-xs text-gray-600 mb-2">Terhitung Mulai Tanggal Pangkat</p>
                        <input type="text" value="{{ $usulan->pegawai->tmt_pangkat ? \Carbon\Carbon::parse($usulan->pegawai->tmt_pangkat)->isoFormat('D MMMM YYYY') : '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Jabatan Saat Ini</label>
                        <p class="text-xs text-gray-600 mb-2">Jabatan terakhir pegawai</p>
                        <input type="text" value="{{ $usulan->pegawai->jabatan->jabatan ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">TMT Jabatan</label>
                        <p class="text-xs text-gray-600 mb-2">Terhitung Mulai Tanggal Jabatan</p>
                        <input type="text" value="{{ $usulan->pegawai->tmt_jabatan ? \Carbon\Carbon::parse($usulan->pegawai->tmt_jabatan)->isoFormat('D MMMM YYYY') : '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">TMT CPNS</label>
                        <p class="text-xs text-gray-600 mb-2">Terhitung Mulai Tanggal CPNS</p>
                        <input type="text" value="{{ $usulan->pegawai->tmt_cpns ? \Carbon\Carbon::parse($usulan->pegawai->tmt_cpns)->isoFormat('D MMMM YYYY') : '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">TMT PNS</label>
                        <p class="text-xs text-gray-600 mb-2">Terhitung Mulai Tanggal PNS</p>
                        <input type="text" value="{{ $usulan->pegawai->tmt_pns ? \Carbon\Carbon::parse($usulan->pegawai->tmt_pns)->isoFormat('D MMMM YYYY') : '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                </div>
            </div>
        </div>

        {{-- Data Pendidikan --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="graduation-cap" class="w-6 h-6 mr-3"></i>
                    Data Pendidikan & Fungsional
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Pendidikan Terakhir</label>
                        <p class="text-xs text-gray-600 mb-2">Tingkat pendidikan terakhir</p>
                        <input type="text" value="{{ $usulan->pegawai->pendidikan_terakhir ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Nama Universitas/Sekolah</label>
                        <p class="text-xs text-gray-600 mb-2">Institusi pendidikan terakhir</p>
                        <input type="text" value="{{ $usulan->pegawai->nama_universitas_sekolah ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Program Studi/Jurusan</label>
                        <p class="text-xs text-gray-600 mb-2">Program studi terakhir</p>
                        <input type="text" value="{{ $usulan->pegawai->nama_prodi_jurusan ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                </div>
            </div>
        </div>

        {{-- Data Kinerja --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-orange-600 to-amber-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="trending-up" class="w-6 h-6 mr-3"></i>
                    Data Kinerja
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Predikat SKP Tahun {{ date('Y') - 1 }}</label>
                        <p class="text-xs text-gray-600 mb-2">Predikat SKP tahun sebelumnya</p>
                        <input type="text" value="{{ $usulan->pegawai->predikat_kinerja_tahun_pertama ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Predikat SKP Tahun {{ date('Y') - 2 }}</label>
                        <p class="text-xs text-gray-600 mb-2">Predikat SKP dua tahun sebelumnya</p>
                        <input type="text" value="{{ $usulan->pegawai->predikat_kinerja_tahun_kedua ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Nilai Konversi {{ date('Y') - 1 }}</label>
                        <p class="text-xs text-gray-600 mb-2">Nilai konversi tahun sebelumnya</p>
                        <input type="text" value="{{ $usulan->pegawai->nilai_konversi ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dokumen Profil --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-teal-600 to-cyan-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="folder" class="w-6 h-6 mr-3"></i>
                    Dokumen Profil
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">SK Pangkat Terakhir</label>
                        <p class="text-xs text-gray-600 mb-2">Surat Keputusan pangkat terakhir</p>
                        @if($usulan->pegawai->sk_pangkat_terakhir)
                            <a href="{{ route('pegawai-unmul.profile.show-document', 'sk_pangkat_terakhir') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                                Lihat Dokumen
                            </a>
                        @else
                            <span class="inline-flex items-center gap-2 px-4 py-3 bg-gray-50 text-gray-500 rounded-lg">
                                <i data-lucide="file-x" class="w-4 h-4"></i>
                                Belum diupload
                            </span>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">SK Jabatan Terakhir</label>
                        <p class="text-xs text-gray-600 mb-2">Surat Keputusan jabatan terakhir</p>
                        @if($usulan->pegawai->sk_jabatan_terakhir)
                            <a href="{{ route('pegawai-unmul.profile.show-document', 'sk_jabatan_terakhir') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                                Lihat Dokumen
                            </a>
                        @else
                            <span class="inline-flex items-center gap-2 px-4 py-3 bg-gray-50 text-gray-500 rounded-lg">
                                <i data-lucide="file-x" class="w-4 h-4"></i>
                                Belum diupload
                            </span>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">SKP Tahun {{ date('Y') - 1 }}</label>
                        <p class="text-xs text-gray-600 mb-2">Sasaran Kinerja Pegawai tahun sebelumnya</p>
                        @if($usulan->pegawai->skp_tahun_pertama)
                            <a href="{{ route('pegawai-unmul.profile.show-document', 'skp_tahun_pertama') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                                Lihat Dokumen
                            </a>
                        @else
                            <span class="inline-flex items-center gap-2 px-4 py-3 bg-gray-50 text-gray-500 rounded-lg">
                                <i data-lucide="file-x" class="w-4 h-4"></i>
                                Belum diupload
                            </span>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">SKP Tahun {{ date('Y') - 2 }}</label>
                        <p class="text-xs text-gray-600 mb-2">Sasaran Kinerja Pegawai dua tahun sebelumnya</p>
                        @if($usulan->pegawai->skp_tahun_kedua)
                            <a href="{{ route('pegawai-unmul.profile.show-document', 'skp_tahun_kedua') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                                Lihat Dokumen
                            </a>
                        @else
                            <span class="inline-flex items-center gap-2 px-4 py-3 bg-gray-50 text-gray-500 rounded-lg">
                                <i data-lucide="file-x" class="w-4 h-4"></i>
                                Belum diupload
                            </span>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">PAK Konversi {{ date('Y') - 1 }}</label>
                        <p class="text-xs text-gray-600 mb-2">Penilaian Angka Kredit konversi tahun sebelumnya</p>
                        @if($usulan->pegawai->pak_konversi)
                            <a href="{{ route('pegawai-unmul.profile.show-document', 'pak_konversi') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                                Lihat Dokumen
                            </a>
                        @else
                            <span class="inline-flex items-center gap-2 px-4 py-3 bg-gray-50 text-gray-500 rounded-lg">
                                <i data-lucide="file-x" class="w-4 h-4"></i>
                                Belum diupload
                            </span>
                        @endif
                    </div>
                    @if($usulan->pegawai->jabatan && in_array($usulan->pegawai->jabatan->jenis_jabatan, ['Dosen Fungsional', 'Tenaga Kependidikan Fungsional Tertentu']))
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">PAK Integrasi</label>
                        <p class="text-xs text-gray-600 mb-2">Penilaian Angka Kredit integrasi</p>
                        @if($usulan->pegawai->pak_integrasi)
                            <a href="{{ route('pegawai-unmul.profile.show-document', 'pak_integrasi') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                                Lihat Dokumen
                            </a>
                        @else
                            <span class="inline-flex items-center gap-2 px-4 py-3 bg-gray-50 text-gray-500 rounded-lg">
                                <i data-lucide="file-x" class="w-4 h-4"></i>
                                Belum diupload
                            </span>
                        @endif
                    </div>
                    @endif
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">SK CPNS</label>
                        <p class="text-xs text-gray-600 mb-2">Surat Keputusan CPNS</p>
                        @if($usulan->pegawai->sk_cpns)
                            <a href="{{ route('pegawai-unmul.profile.show-document', 'sk_cpns') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                                Lihat Dokumen
                            </a>
                        @else
                            <span class="inline-flex items-center gap-2 px-4 py-3 bg-gray-50 text-gray-500 rounded-lg">
                                <i data-lucide="file-x" class="w-4 h-4"></i>
                                Belum diupload
                            </span>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">SK PNS</label>
                        <p class="text-xs text-gray-600 mb-2">Surat Keputusan PNS</p>
                        @if($usulan->pegawai->sk_pns)
                            <a href="{{ route('pegawai-unmul.profile.show-document', 'sk_pns') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                                Lihat Dokumen
                            </a>
                        @else
                            <span class="inline-flex items-center gap-2 px-4 py-3 bg-gray-50 text-gray-500 rounded-lg">
                                <i data-lucide="file-x" class="w-4 h-4"></i>
                                Belum diupload
                            </span>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Ijazah Terakhir</label>
                        <p class="text-xs text-gray-600 mb-2">Ijazah pendidikan terakhir</p>
                        @if($usulan->pegawai->ijazah_terakhir)
                            <a href="{{ route('pegawai-unmul.profile.show-document', 'ijazah_terakhir') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                                Lihat Dokumen
                            </a>
                        @else
                            <span class="inline-flex items-center gap-2 px-4 py-3 bg-gray-50 text-gray-500 rounded-lg">
                                <i data-lucide="file-x" class="w-4 h-4"></i>
                                Belum diupload
                            </span>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Transkrip Nilai Terakhir</label>
                        <p class="text-xs text-gray-600 mb-2">Transkrip nilai pendidikan terakhir</p>
                        @if($usulan->pegawai->transkrip_nilai_terakhir)
                            <a href="{{ route('pegawai-unmul.profile.show-document', 'transkrip_nilai_terakhir') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                                Lihat Dokumen
                            </a>
                        @else
                            <span class="inline-flex items-center gap-2 px-4 py-3 bg-gray-50 text-gray-500 rounded-lg">
                                <i data-lucide="file-x" class="w-4 h-4"></i>
                                Belum diupload
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Dokumen Pendukung Dinamis --}}
        @php
            $jenisUsulanPangkat = $usulan->data_usulan['jenis_usulan_pangkat'] ?? '';
        @endphp
        
        @if($jenisUsulanPangkat === 'Dosen PNS')
            @include('backend.layouts.views.pegawai-unmul.usulan-kepangkatan.components.dosen-pns-form')
        @elseif($jenisUsulanPangkat === 'Jabatan Administrasi')
            @include('backend.layouts.views.pegawai-unmul.usulan-kepangkatan.components.jabatan-administrasi-form')
        @elseif($jenisUsulanPangkat === 'Jabatan Fungsional Tertentu')
            @include('backend.layouts.views.pegawai-unmul.usulan-kepangkatan.components.jabatan-fungsional-tertentu-form')
        @elseif($jenisUsulanPangkat === 'Jabatan Struktural')
            @include('backend.layouts.views.pegawai-unmul.usulan-kepangkatan.components.jabatan-struktural-form')
        @endif

        {{-- Field Validation Display --}}
        @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS)
        @php
            // Get validation data from Kepegawaian Universitas
            $validationData = $usulan->getValidasiByRole('kepegawaian_universitas') ?? [];
            $hasValidationIssues = !empty($validationData) && isset($validationData['validation']);
            
            // Field labels mapping
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
                    'nama_prodi_jurusan' => 'Nama Program Studi/Jurusan'
                ],
                'data_kinerja' => [
                    'predikat_kinerja_tahun_pertama' => 'Predikat SKP Tahun ' . (date('Y') - 1),
                    'predikat_kinerja_tahun_kedua' => 'Predikat SKP Tahun ' . (date('Y') - 2),
                    'nilai_konversi' => 'Nilai Konversi ' . (date('Y') - 1)
                ],
                'dokumen_profil' => [
                    'sk_pangkat_terakhir' => 'SK Pangkat Terakhir',
                    'sk_jabatan_terakhir' => 'SK Jabatan Terakhir',
                    'skp_tahun_pertama' => 'SKP Tahun ' . (date('Y') - 1),
                    'skp_tahun_kedua' => 'SKP Tahun ' . (date('Y') - 2),
                    'pak_konversi' => 'PAK Konversi ' . (date('Y') - 1),
                    'pak_integrasi' => 'PAK Integrasi',
                    'sk_cpns' => 'SK CPNS',
                    'sk_pns' => 'SK PNS',
                    'ijazah_terakhir' => 'Ijazah Terakhir',
                    'transkrip_nilai_terakhir' => 'Transkrip Nilai Terakhir'
                ]
            ];
        @endphp

        {{-- Catatan Pengusul --}}
        @if(isset($usulan->data_usulan['catatan_pengusul']) && $usulan->data_usulan['catatan_pengusul'])
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-emerald-600 to-green-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="message-square" class="w-6 h-6 mr-3"></i>
                    Catatan Pengusul
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Catatan</label>
                        <p class="text-xs text-gray-600 mb-2">Catatan yang diberikan oleh pengusul</p>
                        <textarea class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" rows="4" disabled>{{ $usulan->data_usulan['catatan_pengusul'] }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Catatan Verifikator --}}
        @if($usulan->catatan_verifikator || 
            ($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN && 
             !empty($validationData['keterangan_umum'] ?? '')) ||
            ($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS && 
             !empty($validationData['keterangan_umum'] ?? '')) ||
            ($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN && 
             !empty($validationData['keterangan_umum'] ?? '')))
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="@if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) bg-gradient-to-r from-purple-600 to-indigo-600 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN) bg-gradient-to-r from-blue-600 to-indigo-600 @else bg-gradient-to-r from-amber-600 to-orange-600 @endif px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS)
                        <i data-lucide="clipboard-check" class="w-6 h-6 mr-3"></i>
                        Catatan Kepegawaian Universitas
                    @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN)
                        <i data-lucide="building" class="w-6 h-6 mr-3"></i>
                        Catatan BKN
                    @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN)
                        <i data-lucide="clipboard-check" class="w-6 h-6 mr-3"></i>
                        Catatan Kepegawaian Universitas
                    @else
                        <i data-lucide="clipboard-check" class="w-6 h-6 mr-3"></i>
                        Catatan Verifikator
                    @endif
                    @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS || $usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN || $usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN)
                        <span class="ml-3 px-3 py-1 bg-red-100 text-red-800 text-sm font-medium rounded-full border border-red-200">
                            <i data-lucide="alert-triangle" class="w-4 h-4 inline mr-1"></i>
                            Perlu Perbaikan
                        </span>
                    @endif
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Catatan</label>
                        <p class="text-xs text-gray-600 mb-2">
                            @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS)
                                Catatan yang diberikan oleh Kepegawaian Universitas
                            @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN)
                                Catatan yang diberikan oleh BKN
                            @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN)
                                Catatan yang diberikan oleh Kepegawaian Universitas
                            @else
                                Catatan yang diberikan oleh verifikator
                            @endif
                        </p>
                        <textarea class="block w-full border-gray-200 rounded-lg shadow-sm @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) bg-purple-50 text-purple-800 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN) bg-blue-50 text-blue-800 @else bg-amber-50 text-amber-800 @endif font-medium cursor-not-allowed" rows="4" disabled>@if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN && !empty($validationData['keterangan_umum'] ?? '')){{ $validationData['keterangan_umum'] }}@elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS && !empty($validationData['keterangan_umum'] ?? '')){{ $validationData['keterangan_umum'] }}@elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN && !empty($validationData['keterangan_umum'] ?? '')){{ $validationData['keterangan_umum'] }}@else{{ $usulan->catatan_verifikator }}@endif</textarea>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endif

        {{-- Action Buttons untuk Pegawai --}}
        @include('backend.layouts.views.pegawai-unmul.usulan-kepangkatan.components.pegawai-action-buttons')
        </form>
    </div>
</div>
@endsection
