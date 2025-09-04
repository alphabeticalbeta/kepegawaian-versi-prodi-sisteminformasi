@extends('backend.layouts.roles.pegawai-unmul.app')

@section('title', 'Detail Usulan NUPTK')

@section('content')
        @php
            // Check if usulan is in view-only status
            $viewOnlyStatuses = [
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_SUDAH_DIKIRIM_KE_SISTER,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_SISTER,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS,
                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER
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
                        Detail Usulan NUPTK
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Informasi lengkap usulan NUPTK
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
                    \App\Models\KepegawaianUniversitas\Usulan::STATUS_DRAFT_USULAN => 'bg-gray-100 text-gray-800 border-gray-300',
                    \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS => 'bg-blue-100 text-blue-800 border-blue-300',
                    \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS => 'bg-blue-100 text-blue-800 border-blue-300',
                    \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS => 'bg-green-100 text-green-800 border-green-300',
                    \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_SUDAH_DIKIRIM_KE_SISTER => 'bg-purple-100 text-purple-800 border-purple-300',
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
            } elseif ($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_TIM_SISTER) {
                $validationData = $usulan->getValidasiByRole('kepegawaian_universitas') ?? [];
                $validationSource = 'Tim Sister';
            } elseif ($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) {
                $validationData = $usulan->getValidasiByRole('kepegawaian_universitas') ?? [];
                $validationSource = 'Kepegawaian Universitas';
            }
            
            $hasValidationIssues = !empty($validationData) && isset($validationData['validation']);

            // Field labels mapping untuk NUPTK
            $fieldLabels = [
                'data_pribadi' => [
                    'jenis_pegawai' => 'Jenis Pegawai',
                    'status_kepegawaian' => 'Status Kepegawaian',
                    'jenis_nuptk' => 'Jenis NUPTK',
                    'nik' => 'NIK',
                    'nama_ibu_kandung' => 'Nama Ibu Kandung',
                    'status_kawin' => 'Status Kawin',
                    'agama' => 'Agama',
                    'gelar_depan' => 'Gelar Depan',
                    'nama_lengkap' => 'Nama Lengkap',
                    'gelar_belakang' => 'Gelar Belakang',
                    'email' => 'Email',
                    'tempat_lahir' => 'Tempat Lahir',
                    'tanggal_lahir' => 'Tanggal Lahir',
                    'jenis_kelamin' => 'Jenis Kelamin',
                    'nomor_handphone' => 'Nomor Handphone'
                ],
                'data_alamat' => [
                    'nama_jalan' => 'Nama Jalan',
                    'nomor_rt' => 'RT',
                    'nomor_rw' => 'RW',
                    'nama_kelurahan' => 'Kelurahan',
                    'nama_kecamatan' => 'Kecamatan',
                    'nama_kota' => 'Kota',
                    'nama_provinsi' => 'Provinsi',
                    'kode_pos' => 'Kode Pos'
                ],
                'data_pendidikan' => [
                    'pendidikan_terakhir' => 'Pendidikan Terakhir',
                    'nama_universitas_sekolah' => 'Nama Universitas/Sekolah',
                    'nama_prodi_jurusan' => 'Nama Program Studi/Jurusan',
                    'tahun_lulus' => 'Tahun Lulus',
                    'nomor_ijazah' => 'Nomor Ijazah',
                    'ijazah_transkrip_s1' => 'Ijazah & Transkrip S1',
                    'ijazah_transkrip_s2' => 'Ijazah & Transkrip S2',
                    'ijazah_transkrip_s3' => 'Ijazah & Transkrip S3'
                ],
                'dokumen_usulan' => [
                    'ktp' => 'KTP',
                    'kartu_keluarga' => 'Kartu Keluarga',
                    'surat_keterangan_sehat' => 'Surat Keterangan Sehat',
                    'surat_pernyataan_pimpinan' => 'Surat Pernyataan Pimpinan PTN',
                    'surat_pernyataan_dosen_tetap' => 'Surat Pernyataan Dosen Tetap',
                    'surat_keterangan_aktif_tridharma' => 'Surat Keterangan Aktif Melaksanakan Tridharma',
                    'surat_izin_instansi_induk' => 'Surat Izin Instansi Induk',
                    'surat_perjanjian_kerja' => 'Surat Perjanjian Kerja',
                    'sk_tenaga_pengajar' => 'SK Tenaga Pengajar',
                    'nota_dinas' => 'Nota Dinas'
                ]
            ];
        @endphp

        @if($hasValidationIssues && ($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS || $usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_TIM_SISTER || $usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER))
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="@if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_TIM_SISTER) bg-gradient-to-r from-purple-600 to-indigo-600 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) bg-gradient-to-r from-blue-600 to-indigo-600 @else bg-gradient-to-r from-red-600 to-pink-600 @endif px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_TIM_SISTER)
                        <i data-lucide="building" class="w-6 h-6 mr-3"></i>
                    @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER)
                        <i data-lucide="clipboard-check" class="w-6 h-6 mr-3"></i>
                    @else
                        <i data-lucide="alert-triangle" class="w-6 h-6 mr-3"></i>
                    @endif
                    Field-Field Tidak Sesuai
                </h2>
            </div>
            <div class="p-6">
                <div class="mb-4 p-4 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER) bg-purple-50 border-purple-200 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) bg-blue-50 border-blue-200 @else bg-red-50 border-red-200 @endif rounded-lg border">
                    <div class="flex items-center">
                        <i data-lucide="info" class="w-5 h-5 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER) text-purple-600 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) text-blue-600 @else text-red-600 @endif mr-3"></i>
                        <div>
                            <h4 class="text-sm font-semibold @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER) text-purple-800 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) text-blue-800 @else text-red-800 @endif mb-1">Informasi Validasi</h4>
                            <p class="text-sm @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER) text-purple-700 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) text-blue-700 @else text-red-700 @endif">
                                Berikut adalah field-field yang tidak sesuai berdasarkan validasi dari {{ $validationSource }}.
                                Field-field ini harus diperbaiki sebelum usulan dapat diajukan kembali.
                                @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER)
                                    <br><br><strong>Note:</strong> Usulan ini dikembalikan dari SISTER dan perlu dikirim kembali ke Kepegawaian Universitas setelah perbaikan.
                                @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER)
                                    <br><br><strong>Note:</strong> Usulan ini akan dikirim ke SISTER setelah perbaikan.
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
                                    <div class="border-l-4 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER) border-purple-500 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) border-blue-500 @else border-red-500 @endif pl-4 py-3 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER) bg-purple-50 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) bg-blue-50 @else bg-red-50 @endif rounded-r-lg">
                                        <div class="flex items-start">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-2 mb-2">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER) bg-purple-100 text-purple-800 border-purple-300 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) bg-blue-100 text-blue-800 border-blue-300 @else bg-red-100 text-red-800 border-red-300 @endif border">
                                                        <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                                        Tidak Sesuai
                                                    </span>
                                                    <span class="text-sm font-medium @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER) text-purple-800 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) text-blue-800 @else text-red-800 @endif">{{ $fieldLabels[$groupKey][$fieldKey] ?? ucwords(str_replace('_', ' ', $fieldKey)) }}</span>
                                                </div>
                                                @if(!empty($fieldData['keterangan']))
                                                    <div class="bg-white border @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER) border-purple-200 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) border-blue-200 @else border-red-200 @endif rounded-lg p-3">
                                                        <div class="text-sm @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER) text-purple-700 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) text-blue-700 @else text-red-700 @endif">
                                                            <strong>Keterangan:</strong> {{ $fieldData['keterangan'] }}
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="bg-white border @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER) border-purple-200 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) border-blue-200 @else border-red-200 @endif rounded-lg p-3">
                                                        <div class="text-sm @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER) text-purple-700 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) text-blue-700 @else text-red-700 @endif">
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
                    @if(!empty($validationData) && isset($validationData['validation']['dokumen_usulan']))
                        @php
                            $dokumenUsulanFields = [
                                'ktp',
                                'kartu_keluarga',
                                'surat_keterangan_sehat',
                                'surat_pernyataan_pimpinan',
                                'surat_pernyataan_dosen_tetap',
                                'surat_keterangan_aktif_tridharma',
                                'surat_izin_instansi_induk',
                                'surat_perjanjian_kerja',
                                'sk_tenaga_pengajar',
                                'nota_dinas'
                            ];
                        @endphp
                        
                        @foreach($dokumenUsulanFields as $fieldKey)
                            @if(isset($validationData['validation']['dokumen_usulan'][$fieldKey]['status']) && 
                                $validationData['validation']['dokumen_usulan'][$fieldKey]['status'] === 'tidak_sesuai')
                                <div class="border-l-4 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER) border-purple-500 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) border-blue-500 @else border-red-500 @endif pl-4 py-3 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER) bg-purple-50 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) bg-blue-50 @else bg-red-50 @endif rounded-r-lg">
                                    <div class="flex items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER) bg-purple-100 text-purple-800 border-purple-300 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) bg-blue-100 text-blue-800 border-blue-300 @else bg-red-100 text-red-800 border-red-300 @endif border">
                                                    <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                                    Tidak Sesuai
                                                </span>
                                                <span class="text-sm font-medium @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER) text-purple-800 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) text-blue-800 @else text-red-800 @endif">
                                                    @if($fieldKey === 'ktp')
                                                        KTP
                                                    @elseif($fieldKey === 'kartu_keluarga')
                                                        Kartu Keluarga
                                                    @elseif($fieldKey === 'surat_keterangan_sehat')
                                                        Surat Keterangan Sehat
                                                    @elseif($fieldKey === 'surat_pernyataan_pimpinan')
                                                        Surat Pernyataan Pimpinan PTN
                                                    @elseif($fieldKey === 'surat_pernyataan_dosen_tetap')
                                                        Surat Pernyataan Dosen Tetap
                                                    @elseif($fieldKey === 'surat_keterangan_aktif_tridharma')
                                                        Surat Keterangan Aktif Melaksanakan Tridharma
                                                    @elseif($fieldKey === 'surat_izin_instansi_induk')
                                                        Surat Izin Instansi Induk
                                                    @elseif($fieldKey === 'surat_perjanjian_kerja')
                                                        Surat Perjanjian Kerja
                                                    @elseif($fieldKey === 'sk_tenaga_pengajar')
                                                        SK Tenaga Pengajar
                                                    @elseif($fieldKey === 'nota_dinas')
                                                        Nota Dinas
                                                    @else
                                                        {{ ucwords(str_replace('_', ' ', $fieldKey)) }}
                                                    @endif
                                                </span>
                                            </div>
                                            @if(!empty($validationData['validation']['dokumen_usulan'][$fieldKey]['keterangan']))
                                                <div class="bg-white border @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER) border-purple-200 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) border-blue-200 @else border-red-200 @endif rounded-lg p-3">
                                                    <div class="text-sm @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER) text-purple-700 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) text-blue-700 @else text-red-700 @endif">
                                                        <strong>Keterangan:</strong> {{ $validationData['validation']['dokumen_usulan'][$fieldKey]['keterangan'] }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif

                    {{-- Keterangan Umum untuk Semua Status --}}
                    @if(!empty($validationData['keterangan_umum'] ?? ''))
                        <div class="border-l-4 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER) border-purple-500 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) border-blue-500 @else border-red-500 @endif pl-4 py-3 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER) bg-purple-50 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) bg-blue-50 @else bg-red-50 @endif rounded-r-lg">
                            <div class="flex items-start">
                                                                  <i data-lucide="sticky-note" class="w-5 h-5 @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER) text-purple-600 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) text-blue-600 @else text-red-600 @endif mr-3 mt-0.5"></i>
                                <div class="flex-1">
                                    <div class="text-sm font-semibold @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER) text-purple-800 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) text-blue-800 @else text-red-800 @endif mb-2">
                                        Keterangan Umum dari {{ $validationSource }}:
                                    </div>
                                    <div class="bg-white border @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER) border-purple-200 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) border-blue-200 @else border-red-200 @endif rounded-lg p-3">
                                        <div class="text-sm @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER) text-purple-700 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) text-blue-700 @else text-red-700 @endif">
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

        {{-- Informasi Usulan NUPTK --}}
        <form action="{{ route('pegawai-unmul.usulan-nuptk.update', $usulan) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="action" id="formAction" value="simpan">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
                            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="award" class="w-6 h-6 mr-3"></i>
                    Informasi Usulan NUPTK
                </h2>
            </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            @php
                                $jenisNuptkLabels = [
                                    'dosen_tetap' => 'Dosen Tetap',
                                    'dosen_tidak_tetap' => 'Dosen Tidak Tetap',
                                    'pengajar_non_dosen' => 'Pengajar Non Dosen',
                                    'tenaga_kependidikan' => 'Tenaga Kependidikan'
                                ];
                                $jenisNuptkDisplay = $jenisNuptkLabels[$usulan->jenis_nuptk] ?? $usulan->jenis_nuptk ?? '-';
                            @endphp
                            <input type="text" value="{{ $jenisNuptkDisplay }}"
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
                        <label class="block text-sm font-semibold text-gray-800">SK Pangkat Terakhir</label>
                        <p class="text-xs text-gray-600 mb-2">Surat Keputusan Pangkat Terakhir</p>
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
                        <p class="text-xs text-gray-600 mb-2">Surat Keputusan Jabatan Terakhir</p>
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


                </div>
            </div>
        </div>

        {{-- Form Usulan NUPTK --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-emerald-600 to-green-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="file-text" class="w-6 h-6 mr-3"></i>
                    Form Usulan NUPTK
                </h2>
            </div>
            <div class="p-6">
                @include('backend.layouts.views.pegawai-unmul.usulan-nuptk.components.nuptk-general')
            </div>
        </div>

        {{-- Komponen Dosen (Hanya untuk jenis NUPTK Dosen) --}}
        @if(in_array($usulan->jenis_nuptk, ['dosen_tetap', 'dosen_tidak_tetap', 'pengajar_non_dosen']))
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6 pt-4">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="graduation-cap" class="w-6 h-6 mr-3"></i>
                    Dokumen Khusus Dosen
                </h2>
            </div>
            <div class="p-6">
                @include('backend.layouts.views.pegawai-unmul.usulan-nuptk.components.dosen')
            </div>
        </div>
        @endif

        {{-- Field Validation Display --}}
        @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS)
        @php
            // Get validation data from Kepegawaian Universitas
            $validationData = $usulan->getValidasiByRole('kepegawaian_universitas') ?? [];
            $hasValidationIssues = !empty($validationData) && isset($validationData['validation']);


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
            ($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_TIM_SISTER && 
             !empty($validationData['keterangan_umum'] ?? '')) ||
            ($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS && 
             !empty($validationData['keterangan_umum'] ?? '')) ||
            ($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER && 
             !empty($validationData['keterangan_umum'] ?? '')))
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="@if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER) bg-gradient-to-r from-purple-600 to-indigo-600 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) bg-gradient-to-r from-blue-600 to-indigo-600 @else bg-gradient-to-r from-amber-600 to-orange-600 @endif px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS)
                        <i data-lucide="clipboard-check" class="w-6 h-6 mr-3"></i>
                        Catatan Kepegawaian Universitas
                    @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER)
                        <i data-lucide="building" class="w-6 h-6 mr-3"></i>
                        Catatan SISTER
                    @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER)
                        <i data-lucide="clipboard-check" class="w-6 h-6 mr-3"></i>
                        Catatan Kepegawaian Universitas
                    @else
                        <i data-lucide="clipboard-check" class="w-6 h-6 mr-3"></i>
                        Catatan Verifikator
                    @endif
                    @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS || $usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER || $usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER)
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
                            @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER)
                                Catatan yang diberikan oleh SISTER
                            @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER)
                                Catatan yang diberikan oleh Kepegawaian Universitas
                            @else
                                Catatan yang diberikan oleh verifikator
                            @endif
                        </p>
                        <textarea class="block w-full border-gray-200 rounded-lg shadow-sm @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER) bg-purple-50 text-purple-800 @elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER) bg-blue-50 text-blue-800 @else bg-amber-50 text-amber-800 @endif font-medium cursor-not-allowed" rows="4" disabled>@if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_SISTER && !empty($validationData['keterangan_umum'] ?? '')){{ $validationData['keterangan_umum'] }}@elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS && !empty($validationData['keterangan_umum'] ?? '')){{ $validationData['keterangan_umum'] }}@elseif($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER && !empty($validationData['keterangan_umum'] ?? '')){{ $validationData['keterangan_umum'] }}@else{{ $usulan->catatan_verifikator }}@endif</textarea>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endif

        {{-- Action Buttons untuk Pegawai --}}
        @include('backend.layouts.views.pegawai-unmul.usulan-nuptk.components.pegawai-action-buttons')
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Global functions for usulan NUPTK (SweetAlert2 already loaded in app.blade.php)
    document.addEventListener('DOMContentLoaded', function() {
        
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
