@extends('backend.layouts.roles.pegawai-unmul.app')

@section('title', 'Detail Usulan Tugas Belajar')

@section('content')
        @php
            // Check if usulan is in view-only status
            $viewOnlyStatuses = [
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_SUDAH_DIKIRIM_KE_TIM_SISTER,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_SISTER,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_TIM_SISTER,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS
            ];

            // Status yang dapat diedit (tidak view-only) - hanya status draft dan permintaan perbaikan
            $editableStatuses = [
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_DRAFT_USULAN,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_TIM_SISTER
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
                        Detail Usulan {{ $usulan->jenis_tubel }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Informasi lengkap usulan tugas belajar
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.index') }}"
                       class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
                        Kembali ke Daftar Usulan
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
                    \App\Models\KepegawaianUniversitas\Usulan::STATUS_DRAFT_USULAN => 'bg-gray-100 text-gray-800 border-gray-300',
                    \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS => 'bg-blue-100 text-blue-800 border-blue-300',
                    \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS => 'bg-blue-100 text-blue-800 border-blue-300',
                    \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS => 'bg-green-100 text-green-800 border-green-300',
                    \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_SUDAH_DIKIRIM_KE_TIM_SISTER => 'bg-purple-100 text-purple-800 border-purple-300',
                    \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_SISTER => 'bg-green-100 text-green-800 border-green-300',
                    \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS => 'bg-orange-100 text-orange-800 border-orange-300',
                    \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_TIM_SISTER => 'bg-orange-100 text-orange-800 border-orange-300',
                    \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS => 'bg-amber-100 text-amber-800 border-amber-300',
                    \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER => 'bg-amber-100 text-amber-800 border-amber-300',
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
            } elseif ($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) {
                $validationData = $usulan->getValidasiByRole('kepegawaian_universitas') ?? [];
                $validationSource = 'Kementerian';
            }

            $hasValidationIssues = !empty($validationData) && isset($validationData['validation']);

            // Field labels mapping untuk TUBEL
            $fieldLabels = [
                'data_pribadi' => [
                    'tempat_lahir' => 'Tempat Lahir',
                    'tanggal_lahir' => 'Tanggal Lahir',
                    'nomor_handphone' => 'Nomor Handphone',
                    'email' => 'Email',
                    'pendidikan_terakhir' => 'Pendidikan Terakhir',
                    'pangkat' => 'Pangkat Terakhir',
                    'tmt_pangkat' => 'TMT Pangkat',
                    'jabatan' => 'Jabatan Terakhir',
                    'tmt_jabatan' => 'TMT Jabatan'
                ],
                'data_usulan_tugas_belajar' => [
                    'tahun_studi' => 'Tahun Studi',
                    'alamat_lengkap' => 'Alamat Lengkap',
                    'pendidikan_ditempuh' => 'Pendidikan yang Ditempuh',
                    'nama_prodi_dituju' => 'Nama Prodi yang Dituju',
                    'nama_fakultas_dituju' => 'Nama Fakultas yang Dituju',
                    'nama_universitas_dituju' => 'Nama Universitas yang Dituju',
                    'negara_studi' => 'Negara Studi'
                ],
                'dokumen_tugas_belajar' => [
                    'kartu_pegawai' => 'Kartu Pegawai/Kartu Virtual ASN',
                    'dokumen_setneg' => 'Dokumen Setneg (Luar Negeri)'
                ],
                'dokumen_tubel' => [
                    'surat_tunjangan_keluarga' => 'Surat Keterangan Pembayaran Tunjangan Keluarga',
                    'akta_nikah' => 'Akta Nikah/Surat Keterangan Belum Menikah',
                    'surat_rekomendasi_atasan' => 'Surat Rekomendasi dari Atasan Langsung',
                    'surat_perjanjian_tubel' => 'Surat Perjanjian Tugas Belajar',
                    'surat_jaminan_pembiayaan' => 'Surat Jaminan Pembiayaan Tugas Belajar',
                    'surat_keterangan_pimpinan' => 'Surat Keterangan dari Pimpinan Unit Kerja',
                    'surat_hasil_kelulusan' => 'Surat Hasil Kelulusan dari Lembaga Pendidikan (LoA)',
                    'surat_pernyataan_pimpinan' => 'Surat Pernyataan dari Pimpinan Unit Kerja (10 Poin)',
                    'surat_pernyataan_bersangkutan' => 'Asli Surat Pernyataan yang Bersangkutan (3 Poin)',
                    'dokumen_akreditasi' => 'Dokumen Akreditasi Prodi dan PT/Tangkap Layar Daftar PTLN'
                ],
                'dokumen_perpanjangan_tubel' => [
                    'surat_perjanjian_perpanjangan' => 'Surat Perjanjian Perpanjangan Pemberian Tugas Belajar',
                    'surat_perpanjangan_jaminan_pembiayaan' => 'Surat Perpanjangan Jaminan Pembiayaan Tugas Belajar',
                    'surat_rekomendasi_lembaga_pendidikan' => 'Surat Rekomendasi Perpanjangan Pemberian Tugas Belajar dari Lembaga Pendidikan',
                    'surat_rekomendasi_pimpinan_unit' => 'Surat Rekomendasi Perpanjangan Tugas Belajar dari Pimpinan Unit Kerja',
                    'sk_tugas_belajar' => 'SK Tugas Belajar'
                ]
            ];
        @endphp

        @if($hasValidationIssues && ($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS || $usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN))
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="@if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) bg-gradient-to-r from-purple-600 to-indigo-600 @else bg-gradient-to-r from-red-600 to-pink-600 @endif px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN)
                        <i data-lucide="building" class="w-6 h-6 mr-3"></i>
                    @else
                        <i data-lucide="alert-triangle" class="w-6 h-6 mr-3"></i>
                    @endif
                    Field-Field Tidak Sesuai
                </h2>
            </div>
            <div class="p-6">
                <div class="mb-4 p-4 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) bg-purple-50 border-purple-200 @else bg-red-50 border-red-200 @endif rounded-lg border">
                    <div class="flex items-center">
                        <i data-lucide="info" class="w-5 h-5 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) text-purple-600 @else text-red-600 @endif mr-3"></i>
                        <div>
                            <h4 class="text-sm font-semibold @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) text-purple-800 @else text-red-800 @endif mb-1">Informasi Validasi</h4>
                            <p class="text-sm @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) text-purple-700 @else text-red-700 @endif">
                                Berikut adalah field-field yang tidak sesuai berdasarkan validasi dari {{ $validationSource }}.
                                Field-field ini harus diperbaiki sebelum usulan dapat diajukan kembali.
                                @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN)
                                    <br><br><strong>Note:</strong> Usulan ini dikembalikan dari Kementerian dan perlu diperbaiki sesuai dengan ketentuan yang berlaku.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    @foreach($validationData['validation'] ?? [] as $groupKey => $groupData)
                        @if(isset($fieldLabels[$groupKey]) && !in_array($groupKey, ['dokumen_tugas_belajar', 'dokumen_tubel', 'dokumen_perpanjangan_tubel']))
                            @foreach($groupData as $fieldKey => $fieldData)
                                @if(isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai')
                                    <div class="border-l-4 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) border-purple-500 @else border-red-500 @endif pl-4 py-3 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) bg-purple-50 @else bg-red-50 @endif rounded-r-lg">
                                        <div class="flex items-start">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-2 mb-2">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) bg-purple-100 text-purple-800 border-purple-300 @else bg-red-100 text-red-800 border-red-300 @endif border">
                                                        <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                                        Tidak Sesuai
                                                    </span>
                                                    <span class="text-sm font-medium @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) text-purple-800 @else text-red-800 @endif">{{ $fieldLabels[$groupKey][$fieldKey] ?? ucwords(str_replace('_', ' ', $fieldKey)) }}</span>
                                                </div>
                                                @if(!empty($fieldData['keterangan']))
                                                    <div class="bg-white border @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) border-purple-200 @else border-red-200 @endif rounded-lg p-3">
                                                        <div class="text-sm @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) text-purple-700 @else text-red-700 @endif">
                                                            <strong>Keterangan:</strong> {{ $fieldData['keterangan'] }}
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="bg-white border @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) border-purple-200 @else border-red-200 @endif rounded-lg p-3">
                                                        <div class="text-sm @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) text-purple-700 @else text-red-700 @endif">
                                                            <strong>Keterangan:</strong> Tidak ada keterangan spesifik
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Field Validation untuk Dokumen Usulan --}}
                    @if(!empty($validationData) && (isset($validationData['validation']['dokumen_tugas_belajar']) || isset($validationData['validation']['dokumen_tubel']) || isset($validationData['validation']['dokumen_perpanjangan_tubel'])))
                        @php
                            $dokumenGroups = ['dokumen_tugas_belajar', 'dokumen_tubel', 'dokumen_perpanjangan_tubel'];
                        @endphp
                        @foreach($dokumenGroups as $dokumenGroup)
                            @if(isset($validationData['validation'][$dokumenGroup]))
                                @foreach($validationData['validation'][$dokumenGroup] as $fieldKey => $fieldData)
                                    @if(isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai')
                                        <div class="border-l-4 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) border-purple-500 @else border-red-500 @endif pl-4 py-3 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) bg-purple-50 @else bg-red-50 @endif rounded-r-lg">
                                            <div class="flex items-start">
                                                <div class="flex-1">
                                                    <div class="flex items-center space-x-2 mb-2">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) bg-purple-100 text-purple-800 border-purple-300 @else bg-red-100 text-red-800 border-red-300 @endif border">
                                                            <i data-lucide="file-x" class="w-3 h-3 mr-1"></i>
                                                            Dokumen Tidak Sesuai
                                                        </span>
                                                        <span class="text-sm font-medium @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) text-purple-800 @else text-red-800 @endif">{{ $fieldLabels[$dokumenGroup][$fieldKey] ?? ucwords(str_replace('_', ' ', $fieldKey)) }}</span>
                                                    </div>
                                                    @if(!empty($fieldData['keterangan']))
                                                        <div class="bg-white border @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) border-purple-200 @else border-red-200 @endif rounded-lg p-3">
                                                            <div class="text-sm @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) text-purple-700 @else text-red-700 @endif">
                                                                <strong>Keterangan:</strong> {{ $fieldData['keterangan'] }}
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="bg-white border @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) border-purple-200 @else border-red-200 @endif rounded-lg p-3">
                                                            <div class="text-sm @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) text-purple-700 @else text-red-700 @endif">
                                                                <strong>Keterangan:</strong> Dokumen tidak sesuai dengan ketentuan
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    @endif

                    {{-- Keterangan Umum --}}
                    @if(!empty($validationData['keterangan_umum'] ?? ''))
                        <div class="border-l-4 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) border-purple-500 @else border-red-500 @endif pl-4 py-3 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) bg-purple-50 @else bg-red-50 @endif rounded-r-lg">
                            <div class="flex items-start">
                                <i data-lucide="sticky-note" class="w-5 h-5 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) text-purple-600 @else text-red-600 @endif mr-3 mt-0.5"></i>
                                <div class="flex-1">
                                    <div class="text-sm font-semibold @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) text-purple-800 @else text-red-800 @endif mb-2">
                                        Keterangan Umum dari {{ $validationSource }}:
                                    </div>
                                    <div class="bg-white border @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) border-purple-200 @else border-red-200 @endif rounded-lg p-3">
                                        <div class="text-sm @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN) text-purple-700 @else text-red-700 @endif">
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

        {{-- Informasi Usulan Tugas Belajar --}}
        <form action="{{ route('pegawai-unmul.usulan-tugas-belajar.update', $usulan) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="action" id="formAction" value="simpan">
            <input type="hidden" name="jenis_tubel" value="{{ $usulan->jenis_tubel }}">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
                <div class="bg-gray-400 px-6 py-5">
                    <h2 class="text-xl font-bold text-black flex items-center">
                        <i data-lucide="book-open" class="w-6 h-6 mr-3"></i>
                        Jenis Usulan Tugas Belajar
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <input type="text" value="{{ $usulan->jenis_tubel ?? '-' }}"
                                   class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
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
                            <label class="block text-sm font-semibold text-gray-800">Nomor Handphone</label>
                            <p class="text-xs text-gray-600 mb-2">Nomor handphone pegawai</p>
                            <input type="text" value="{{ $usulan->pegawai->nomor_handphone ?? '-' }}"
                                   class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800">Email</label>
                            <p class="text-xs text-gray-600 mb-2">Alamat email pegawai</p>
                            <input type="text" value="{{ $usulan->pegawai->email ?? '-' }}"
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
                            <label class="block text-sm font-semibold text-gray-800">Pendidikan Terakhir</label>
                            <p class="text-xs text-gray-600 mb-2">Tingkat pendidikan terakhir</p>
                            <input type="text" value="{{ $usulan->pegawai->pendidikan_terakhir ?? '-' }}"
                                   class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800">Pangkat Terakhir</label>
                            <p class="text-xs text-gray-600 mb-2">Pangkat terakhir pegawai</p>
                            @php
                                $pangkatValue = '-';
                                if ($usulan->pegawai && $usulan->pegawai->pangkat) {
                                    $pangkatValue = $usulan->pegawai->pangkat->pangkat;
                                }
                            @endphp
                            <input type="text" value="{{ $pangkatValue }}"
                                   class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800">TMT Pangkat</label>
                            <p class="text-xs text-gray-600 mb-2">Terhitung Mulai Tanggal pangkat</p>
                            <input type="text" value="{{ $usulan->pegawai->tmt_pangkat ? \Carbon\Carbon::parse($usulan->pegawai->tmt_pangkat)->isoFormat('D MMMM YYYY') : '-' }}"
                                   class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800">Jabatan Terakhir</label>
                            <p class="text-xs text-gray-600 mb-2">Jabatan terakhir pegawai</p>
                            @php
                                $jabatanValue = '-';
                                if ($usulan->pegawai && $usulan->pegawai->jabatan) {
                                    $jabatanValue = $usulan->pegawai->jabatan->jabatan;
                                }
                            @endphp
                            <input type="text" value="{{ $jabatanValue }}"
                                   class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800">TMT Jabatan</label>
                            <p class="text-xs text-gray-600 mb-2">Terhitung Mulai Tanggal jabatan</p>
                            <input type="text" value="{{ $usulan->pegawai->tmt_jabatan ? \Carbon\Carbon::parse($usulan->pegawai->tmt_jabatan)->isoFormat('D MMMM YYYY') : '-' }}"
                                   class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section Dokumen Pegawai --}}
            @include('backend.layouts.views.pegawai-unmul.usulan-tugas-belajar.components.dokumen-pegawai')

            {{-- Form Usulan Tugas Belajar --}}
            @include('backend.layouts.views.pegawai-unmul.usulan-tugas-belajar.components.tubel-general')
    </div>
    <div>
        {{-- Action Buttons --}}
        @include('backend.layouts.views.pegawai-unmul.usulan-tugas-belajar.components.pegawai-action-buttons')
    </div>
</div>

@push('scripts')
<script>
    // Global functions for usulan Tugas Belajar (SweetAlert2 already loaded in app.blade.php)
    document.addEventListener('DOMContentLoaded', function() {

        // Check for flash success message and show SweetAlert instead
        @if(session('success'))
            // Hide the flash message
            const flashMessage = document.querySelector('.bg-green-50.border-green-200');
            if (flashMessage) {
                flashMessage.style.display = 'none';
            }

            // Show SweetAlert instead
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#10b981',
                    timer: 4000,
                    timerProgressBar: true
                });
            }
        @endif

        // Check for flash error message and show SweetAlert instead
        @if(session('error'))
            // Hide the flash message
            const errorFlashMessage = document.querySelector('.bg-red-50.border-red-200');
            if (errorFlashMessage) {
                errorFlashMessage.style.display = 'none';
            }

            // Show SweetAlert instead
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#ef4444',
                    timer: 5000,
                    timerProgressBar: true
                });
            }
        @endif

        // Global success handler
        window.showSuccess = function(message, title = 'Berhasil') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: title,
                    text: message,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#10b981',
                    timer: 3000,
                    timerProgressBar: true
                });
            } else {
                alert('Success: ' + message);
            }
        };

        // Global error handler
        window.showError = function(message, title = 'Terjadi Kesalahan') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: title,
                    text: message,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#ef4444'
                });
            } else {
                alert('Error: ' + message);
            }
        };

        // Global confirmation handler
        window.showConfirmation = function(message, title = 'Konfirmasi', callback) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: title,
                    text: message,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed && callback) {
                        callback();
                    }
                });
            } else {
                if (confirm(message)) {
                    callback();
                }
            }
        };

        // Global loading handler
        window.showLoading = function(message = 'Memproses...') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: message,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            } else {
                // Simple loading indicator
                const loadingDiv = document.createElement('div');
                loadingDiv.id = 'simpleLoading';
                loadingDiv.innerHTML = '<div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;"><div style="background: white; padding: 20px; border-radius: 8px;">' + message + '</div></div>';
                document.body.appendChild(loadingDiv);
            }
        };

        // Global close loading
        window.closeLoading = function() {
            if (typeof Swal !== 'undefined') {
                Swal.close();
            } else {
                const loadingDiv = document.getElementById('simpleLoading');
                if (loadingDiv) {
                    loadingDiv.remove();
                }
            }
        };
    });
</script>
@endpush

@endsection
