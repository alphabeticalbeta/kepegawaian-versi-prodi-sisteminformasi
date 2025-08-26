@extends('backend.layouts.roles.periode-usulan.app')

@push('styles')
<style>
    /* Flash message styling */
    .flash-message {
        animation: slideInRight 0.5s ease-out;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    /* Notification toast styling */
    .notification-toast {
        animation: slideInRight 0.3s ease-out;
        z-index: 9999;
    }
    
    /* Form feedback styling */
    .form-feedback {
        transition: all 0.3s ease;
    }
    
    .form-feedback.success {
        border-color: #10b981;
        background-color: #f0fdf4;
    }
    
    .form-feedback.error {
        border-color: #ef4444;
        background-color: #fef2f2;
    }
</style>
@endpush

@section('title', isset($periode) ? 'Edit Periode' : 'Tambah Periode')

@section('description', 'Pengelolaan Periode Usulan - Sistem Kepegawaian UNMUL')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Flash Messages untuk form --}}
    @if(session('success'))
        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm flash-message">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm flash-message">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if(session('warning'))
        <div class="mb-6 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg shadow-sm flash-message">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">{{ session('warning') }}</span>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="mb-6 bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded-lg shadow-sm flash-message">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">{{ session('info') }}</span>
            </div>
        </div>
    @endif
    <div class="bg-white shadow-xl rounded-2xl border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-6">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white">
                        {{ isset($periode) ? 'Edit Periode ' . ($nama_usulan ?? 'Usulan Jabatan') : 'Tambah Periode ' . ($nama_usulan ?? 'Usulan Jabatan') }}
                    </h3>
                    <p class="text-indigo-100 text-sm mt-1">
                        Kelola periode untuk {{ $nama_usulan ?? 'usulan jabatan dosen dan tenaga kependidikan' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="px-6 py-8">
            <form action="{{ isset($periode) ? route('backend.kepegawaian-universitas.periode-usulan.update', $periode->id) : route('backend.kepegawaian-universitas.periode-usulan.store') }}" method="POST" class="space-y-8" id="periodeForm">
                @csrf
                @if(isset($periode))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="nama_periode" class="block text-sm font-semibold text-gray-800">
                            Nama Periode <span class="text-red-500">*</span>
                        </label>
                            <input type="text" name="nama_periode" id="nama_periode"
                                class="block w-full px-4 py-3 text-gray-900 border-2 border-gray-200 rounded-xl shadow-sm placeholder-gray-400 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent hover:border-gray-300"
                                placeholder="Contoh: Periode Usulan Jabatan Semester Ganjil 2024"
                                value="{{ old('nama_periode', isset($periode) ? $periode->nama_periode : '') }}" required>
                        @error('nama_periode')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="jenis_usulan" class="block text-sm font-semibold text-gray-800">
                            Jenis Usulan <span class="text-red-500">*</span>
                        </label>
                                                <select name="jenis_usulan" id="jenis_usulan"
                                class="block w-full px-4 py-3 text-gray-900 border-2 border-gray-200 bg-white rounded-xl shadow-sm appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                required onchange="updateJenisUsulanInfo()">
                            <option value="">Pilih Jenis Usulan</option>
                            <optgroup label="Usulan Jabatan">
                                                            <option value="jabatan-dosen-regular" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : (isset($jenis_usulan_otomatis) ? $jenis_usulan_otomatis : '')) == 'jabatan-dosen-regular' ? 'selected' : '' }}>
                                Usulan Jabatan Dosen Reguler
                            </option>
                            <option value="jabatan-dosen-pengangkatan" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : (isset($jenis_usulan_otomatis) ? $jenis_usulan_otomatis : '')) == 'jabatan-dosen-pengangkatan' ? 'selected' : '' }}>
                                Usulan Jabatan Dosen Pengangkatan Pertama
                            </option>
                            </optgroup>
                            <option value="usulan-nuptk" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : (isset($jenis_usulan_otomatis) ? $jenis_usulan_otomatis : '')) == 'usulan-nuptk' ? 'selected' : '' }}>
                                Usulan NUPTK
                            </option>
                            <option value="usulan-laporan-lkd" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : (isset($jenis_usulan_otomatis) ? $jenis_usulan_otomatis : '')) == 'usulan-laporan-lkd' ? 'selected' : '' }}>
                                Usulan Laporan LKD
                            </option>
                            <option value="usulan-presensi" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : (isset($jenis_usulan_otomatis) ? $jenis_usulan_otomatis : '')) == 'usulan-presensi' ? 'selected' : '' }}>
                                Usulan Presensi
                            </option>
                            <option value="usulan-id-sinta-sister" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : (isset($jenis_usulan_otomatis) ? $jenis_usulan_otomatis : '')) == 'usulan-id-sinta-sister' ? 'selected' : '' }}>
                                Usulan ID SINTA ke SISTER
                            </option>
                            <option value="usulan-satyalancana" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : (isset($jenis_usulan_otomatis) ? $jenis_usulan_otomatis : '')) == 'usulan-satyalancana' ? 'selected' : '' }}>
                                Usulan Satyalancana
                            </option>
                            <option value="usulan-tugas-belajar" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : (isset($jenis_usulan_otomatis) ? $jenis_usulan_otomatis : '')) == 'usulan-tugas-belajar' ? 'selected' : '' }}>
                                Usulan Tugas Belajar
                            </option>
                            <option value="usulan-pengaktifan-kembali" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : (isset($jenis_usulan_otomatis) ? $jenis_usulan_otomatis : '')) == 'usulan-pengaktifan-kembali' ? 'selected' : '' }}>
                                Usulan Pengaktifan Kembali
                            </option>
                            <option value="usulan-penyesuaian-masa-kerja" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : (isset($jenis_usulan_otomatis) ? $jenis_usulan_otomatis : '')) == 'usulan-penyesuaian-masa-kerja' ? 'selected' : '' }}>
                                Usulan Penyesuaian Masa Kerja
                            </option>
                            <option value="usulan-ujian-dinas-ijazah" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : (isset($jenis_usulan_otomatis) ? $jenis_usulan_otomatis : '')) == 'usulan-ujian-dinas-ijazah' ? 'selected' : '' }}>
                                Usulan Ujian Dinas Ijazah
                            </option>
                            <option value="usulan-laporan-serdos" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : (isset($jenis_usulan_otomatis) ? $jenis_usulan_otomatis : '')) == 'usulan-laporan-serdos' ? 'selected' : '' }}>
                                Usulan Laporan SERDOS
                            </option>
                            <option value="usulan-pensiun" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : (isset($jenis_usulan_otomatis) ? $jenis_usulan_otomatis : '')) == 'usulan-pensiun' ? 'selected' : '' }}>
                                Usulan Pensiun
                            </option>
                            <option value="usulan-kepangkatan" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : (isset($jenis_usulan_otomatis) ? $jenis_usulan_otomatis : '')) == 'usulan-kepangkatan' ? 'selected' : '' }}>
                                Usulan Kepangkatan
                            </option>
                            <option value="usulan-pencantuman-gelar" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : (isset($jenis_usulan_otomatis) ? $jenis_usulan_otomatis : '')) == 'usulan-pencantuman-gelar' ? 'selected' : '' }}>
                                Usulan Pencantuman Gelar
                            </option>
                        </select>
                        @error('jenis_usulan')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                                <!-- Status Kepegawaian -->
                <div class="space-y-4">
                    <label class="block text-sm font-semibold text-gray-800">
                        Status Kepegawaian yang Diizinkan <span class="text-red-500">*</span>
                    </label>
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-700 mb-3">
                            <strong>💡 Panduan:</strong> Pilih status kepegawaian yang diizinkan untuk mengakses periode ini.
                            Minimal harus memilih satu status kepegawaian.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @php
                                $statusKepegawaian = [
                                    // Dosen
                                    'Dosen PNS' => 'Dosen PNS',
                                    'Dosen PPPK' => 'Dosen PPPK',
                                    'Dosen Non ASN' => 'Dosen Non ASN',

                                    // Tenaga Kependidikan
                                    'Tenaga Kependidikan PNS' => 'Tenaga Kependidikan PNS',
                                    'Tenaga Kependidikan PPPK' => 'Tenaga Kependidikan PPPK',
                                    'Tenaga Kependidikan Non ASN' => 'Tenaga Kependidikan Non ASN'
                                ];
                                $selectedStatus = old('status_kepegawaian', isset($periode) ? $periode->status_kepegawaian : []);
                            @endphp

                            @foreach($statusKepegawaian as $key => $label)
                                <label class="flex items-center space-x-2 p-3 bg-white rounded-lg border border-gray-200 hover:border-blue-300 cursor-pointer transition-colors">
                                    <input type="checkbox"
                                           name="status_kepegawaian[]"
                                           value="{{ $key }}"
                                           {{ in_array($key, $selectedStatus) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-gray-700">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-blue-600 mt-2">
                            <strong>Contoh:</strong> Jika hanya memilih "Dosen PNS" dan "Dosen PPPK", maka hanya dosen dengan status tersebut yang dapat mengajukan usulan pada periode ini.
                        </p>
                    </div>
                    @error('status_kepegawaian')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    @error('status_kepegawaian.*')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Box untuk Dosen -->
                <div id="info-dosen" class="info-box hidden p-4 bg-blue-50 border-l-4 border-blue-400 rounded-r-lg">
                    <div class="flex">
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Kriteria Eligibilitas:</strong> Hanya pegawai dengan status kepegawaian "Dosen PNS" yang dapat mengajukan usulan jabatan dosen.
                            </p>
                            <p class="text-sm text-blue-600 mt-1">
                                <strong>Usulan Jabatan Dosen Reguler:</strong> Untuk dosen yang sudah memiliki jabatan fungsional dan ingin naik jenjang
                            </p>
                            <p class="text-sm text-blue-600 mt-1">
                                <strong>Usulan Jabatan Dosen Pengangkatan Pertama:</strong> Untuk dosen yang baru pertama kali diangkat dalam jabatan fungsional
                            </p>
                            <p class="text-sm text-blue-600 mt-1">
                                Jenjang usulan: Tenaga Pengajar → Asisten Ahli → Lektor → Lektor Kepala → Guru Besar
                            </p>
                        </div>
                    </div>
                </div>



                <!-- Jenjang Jabatan Dosen -->
                <div id="jenjang-dosen" class="info-box hidden p-6 border-2 border-blue-200 rounded-xl bg-blue-50 space-y-4">
                    <h4 class="text-md font-semibold text-blue-800 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Jenjang Jabatan Dosen yang Tersedia
                    </h4>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="text-center p-3 bg-white rounded-lg border border-blue-200">
                            <div class="text-sm font-semibold text-blue-700">Dari</div>
                            <div class="text-xs text-blue-600 mt-1">Tenaga Pengajar</div>
                            <div class="text-blue-500 my-1">↓</div>
                            <div class="text-sm font-semibold text-green-700">Ke</div>
                            <div class="text-xs text-green-600 mt-1">Asisten Ahli</div>
                        </div>
                        <div class="text-center p-3 bg-white rounded-lg border border-blue-200">
                            <div class="text-sm font-semibold text-blue-700">Dari</div>
                            <div class="text-xs text-blue-600 mt-1">Asisten Ahli</div>
                            <div class="text-blue-500 my-1">↓</div>
                            <div class="text-sm font-semibold text-green-700">Ke</div>
                            <div class="text-xs text-green-600 mt-1">Lektor</div>
                        </div>
                        <div class="text-center p-3 bg-white rounded-lg border border-blue-200">
                            <div class="text-sm font-semibold text-blue-700">Dari</div>
                            <div class="text-xs text-blue-600 mt-1">Lektor</div>
                            <div class="text-blue-500 my-1">↓</div>
                            <div class="text-sm font-semibold text-green-700">Ke</div>
                            <div class="text-xs text-green-600 mt-1">Lektor Kepala</div>
                        </div>
                        <div class="text-center p-3 bg-white rounded-lg border border-blue-200">
                            <div class="text-sm font-semibold text-blue-700">Dari</div>
                            <div class="text-xs text-blue-600 mt-1">Lektor Kepala</div>
                            <div class="text-blue-500 my-1">↓</div>
                            <div class="text-sm font-semibold text-green-700">Ke</div>
                            <div class="text-xs text-green-600 mt-1">Guru Besar</div>
                        </div>
                    </div>
                </div>



                <div class="p-6 border-2 border-gray-200 rounded-xl space-y-4">
                    <h4 class="text-md font-semibold text-gray-800">Jadwal Pengajuan Usulan</h4>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="tanggal_mulai" class="block text-sm font-semibold text-gray-800">
                                Tanggal Mulai <span class="text-red-500">*</span>
                            </label>
                            {{-- PERBAIKAN: Tambahkan value --}}
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                                   class="block w-full px-4 py-3 text-gray-900 border-2 border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   value="{{ old('tanggal_mulai', isset($periode) && $periode->tanggal_mulai ? $periode->tanggal_mulai->format('Y-m-d') : '') }}" required>
                            @error('tanggal_mulai')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-2">
                            <label for="tanggal_selesai" class="block text-sm font-semibold text-gray-800">
                                Tanggal Selesai <span class="text-red-500">*</span>
                            </label>
                             {{-- PERBAIKAN: Tambahkan value --}}
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                                   class="block w-full px-4 py-3 text-gray-900 border-2 border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   value="{{ old('tanggal_selesai', isset($periode) && $periode->tanggal_selesai ? $periode->tanggal_selesai->format('Y-m-d') : '') }}" required>
                            @error('tanggal_selesai')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="p-6 border-2 border-gray-200 rounded-xl space-y-4">
                     <h4 class="text-md font-semibold text-gray-800">Jadwal Perbaikan (Opsional)</h4>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="tanggal_mulai_perbaikan" class="block text-sm font-semibold text-gray-800">
                                Tanggal Mulai Perbaikan
                            </label>
                                                            <input type="date" name="tanggal_mulai_perbaikan" id="tanggal_mulai_perbaikan"
                                class="block w-full px-4 py-3 text-gray-900 border-2 border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                value="{{ old('tanggal_mulai_perbaikan', isset($periode) && $periode->tanggal_mulai_perbaikan ? $periode->tanggal_mulai_perbaikan->format('Y-m-d') : '') }}"
                                onchange="handleTanggalPerbaikanChange()">
                            @error('tanggal_mulai_perbaikan')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-2">
                            <label for="tanggal_selesai_perbaikan" class="block text-sm font-semibold text-gray-800">
                                Tanggal Selesai Perbaikan
                            </label>
                            <input type="date" name="tanggal_selesai_perbaikan" id="tanggal_selesai_perbaikan"
                                class="block w-full px-4 py-3 text-gray-900 border-2 border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                value="{{ old('tanggal_selesai_perbaikan', isset($periode) && $periode->tanggal_selesai_perbaikan ? $periode->tanggal_selesai_perbaikan->format('Y-m-d') : '') }}"
                                onchange="handleTanggalPerbaikanChange()">
                            @error('tanggal_selesai_perbaikan')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Minimal Anggota Senat - Hanya muncul jika Dosen PNS dipilih -->
                <div id="senat-section" class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6 hidden">
                    <div class="space-y-2">
                        <label for="senat_min_setuju" class="block text-sm font-semibold text-gray-800">
                            Minimal Anggota Senat "Direkomendasikan" <span class="text-blue-500">(Direkomendasikan)</span>
                        </label>
                        <input
                            type="number"
                            min="1"
                            step="1"
                            id="senat_min_setuju"
                            name="senat_min_setuju"
                            value="{{ old('senat_min_setuju', isset($periode) ? $periode->senat_min_setuju : 1) }}"
                            class="block w-40 px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                                <p class="text-xs text-gray-500">
            Jumlah minimal anggota Senat yang harus memilih <b>Direkomendasikan</b>
            agar usulan bisa direkomendasikan oleh Admin Universitas.
            <br><span class="text-blue-600 font-medium">Hanya muncul untuk jenis usulan jabatan dengan status kepegawaian "Dosen PNS".</span>
        </p>
                        @error('senat_min_setuju')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>


                <div class="space-y-2">
                    <label for="status" class="block text-sm font-semibold text-gray-800">
                        Status Periode <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" class="block w-full px-4 py-3 text-gray-900 border-2 border-gray-200 bg-white rounded-xl shadow-sm appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="Tutup" {{ old('status', isset($periode) ? $periode->status : 'Tutup') == 'Tutup' ? 'selected' : '' }}>Tutup</option>
                        <option value="Buka" {{ old('status', isset($periode) ? $periode->status : 'Tutup') == 'Buka' ? 'selected' : '' }}>Buka</option>
                    </select>
                    @error('status')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('backend.kepegawaian-universitas.pusat-usulan.index') }}" class="px-6 py-3 border-2 border-gray-300 text-sm font-semibold text-gray-700 bg-white rounded-xl hover:bg-gray-50 transition-colors duration-200">
                        Batal
                    </a>
                    <button type="submit" class="px-8 py-3 border border-transparent text-sm font-semibold rounded-xl text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow-lg transition-all duration-200">
                        {{ isset($periode) ? 'Update Periode' : 'Simpan Periode' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fungsi untuk update info box berdasarkan jenis usulan
    function updateJenisUsulanInfo() {
        const jenisUsulan = document.getElementById('jenis_usulan').value;
        const infoDosen = document.getElementById('info-dosen');
        const jenjangDosen = document.getElementById('jenjang-dosen');

        // Sembunyikan semua info box terlebih dahulu
        [infoDosen, jenjangDosen].forEach(box => {
            if (box) box.classList.add('hidden');
        });

        // Senat section akan dikontrol oleh handleStatusKepegawaianChange
        const senatSection = document.getElementById('senat-section');
        if (senatSection) senatSection.classList.add('hidden');

        // Tampilkan info box yang sesuai
        if (jenisUsulan === 'jabatan-dosen-regular' || jenisUsulan === 'jabatan-dosen-pengangkatan') {
            if (infoDosen) infoDosen.classList.remove('hidden');
            if (jenjangDosen) jenjangDosen.classList.remove('hidden');
            // Senat section akan dikontrol oleh handleStatusKepegawaianChange
        } else if (jenisUsulan === 'usulan-nuptk' || jenisUsulan === 'usulan-laporan-lkd' || jenisUsulan === 'usulan-presensi' || jenisUsulan === 'usulan-id-sinta-sister' || jenisUsulan === 'usulan-satyalancana' || jenisUsulan === 'usulan-tugas-belajar' || jenisUsulan === 'usulan-pengaktifan-kembali' || jenisUsulan === 'usulan-penyesuaian-masa-kerja' || jenisUsulan === 'usulan-ujian-dinas-ijazah' || jenisUsulan === 'usulan-laporan-serdos' || jenisUsulan === 'usulan-pensiun' || jenisUsulan === 'usulan-kepangkatan' || jenisUsulan === 'usulan-pencantuman-gelar') {
            // No specific info box for these, just ensure they are hidden
            if (infoDosen) infoDosen.classList.add('hidden');
            if (jenjangDosen) jenjangDosen.classList.add('hidden');
            // Senat section akan dikontrol oleh handleStatusKepegawaianChange
        }
    }

    // Panggil fungsi saat halaman dimuat
    updateJenisUsulanInfo();

    // Event listener untuk perubahan jenis usulan
    document.getElementById('jenis_usulan').addEventListener('change', function() {
        updateJenisUsulanInfo();
        handleStatusKepegawaianChange();
    });

    // Fungsi untuk menangani checkbox status kepegawaian
    function handleStatusKepegawaianChange() {
        const checkboxes = document.querySelectorAll('input[name="status_kepegawaian[]"]');
        const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
        const senatSection = document.getElementById('senat-section');
        const jenisUsulan = document.getElementById('jenis_usulan').value;

        // Cek apakah "Dosen PNS" dipilih
        const dosenPNSChecked = Array.from(checkboxes).some(cb => cb.checked && cb.value === 'Dosen PNS');

            // Cek apakah jenis usulan adalah jabatan
    const isJabatanUsulan = jenisUsulan.startsWith('jabatan-dosen-');

        // Tampilkan/sembunyikan section senat berdasarkan pilihan
        if (senatSection) {
            if (dosenPNSChecked && isJabatanUsulan) {
                senatSection.classList.remove('hidden');
            } else {
                senatSection.classList.add('hidden');
            }
        }

        // Update info text berdasarkan pilihan
        const infoText = document.querySelector('.text-blue-600.mt-2');
        if (infoText) {
            if (checkedCount === 0) {
                infoText.innerHTML = '<strong>⚠️ Peringatan:</strong> Minimal harus memilih satu status kepegawaian.';
                infoText.className = 'text-xs text-red-600 mt-2';
            } else if (checkedCount === 1) {
                const checkedValue = Array.from(checkboxes).find(cb => cb.checked).value;
                infoText.innerHTML = `<strong>Info:</strong> Hanya pegawai dengan status <strong>${checkedValue}</strong> yang dapat mengakses periode ini.`;
                infoText.className = 'text-xs text-blue-600 mt-2';
            } else {
                const checkedValues = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
                const lastValue = checkedValues.pop();
                const valuesText = checkedValues.join(', ') + ' dan ' + lastValue;
                infoText.innerHTML = `<strong>Info:</strong> Hanya pegawai dengan status <strong>${valuesText}</strong> yang dapat mengakses periode ini.`;
                infoText.className = 'text-xs text-blue-600 mt-2';
            }
        }
    }

    // Event listener untuk checkbox status kepegawaian
    document.querySelectorAll('input[name="status_kepegawaian[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', handleStatusKepegawaianChange);
    });

    // Panggil fungsi saat halaman dimuat
    handleStatusKepegawaianChange();

        // Validasi tanggal
    const tanggalMulai = document.getElementById('tanggal_mulai');
    const tanggalSelesai = document.getElementById('tanggal_selesai');
    const tanggalMulaiPerbaikan = document.getElementById('tanggal_mulai_perbaikan');
    const tanggalSelesaiPerbaikan = document.getElementById('tanggal_selesai_perbaikan');

    if (tanggalMulai && tanggalSelesai) {
        tanggalMulai.addEventListener('change', function() {
            tanggalSelesai.min = this.value;
        });

        tanggalSelesai.addEventListener('change', function() {
            if (tanggalMulaiPerbaikan) {
                tanggalMulaiPerbaikan.min = this.value;
            }
        });
    }

    if (tanggalMulaiPerbaikan && tanggalSelesaiPerbaikan) {
        tanggalMulaiPerbaikan.addEventListener('change', function() {
            tanggalSelesaiPerbaikan.min = this.value;
        });
    }

        // Form validation dan submission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const statusCheckboxes = document.querySelectorAll('input[name="status_kepegawaian[]"]:checked');
            const submitButton = form.querySelector('button[type="submit"]');

            // Validasi status kepegawaian
            if (statusCheckboxes.length === 0) {
                e.preventDefault();
                showNotification('⚠️ Peringatan: Minimal harus memilih satu status kepegawaian!', 'error');
                return false;
            }

            // Validasi tanggal
            const tanggalMulai = document.getElementById('tanggal_mulai').value;
            const tanggalSelesai = document.getElementById('tanggal_selesai').value;
            
            if (new Date(tanggalSelesai) < new Date(tanggalMulai)) {
                e.preventDefault();
                showNotification('⚠️ Peringatan: Tanggal selesai tidak boleh lebih awal dari tanggal mulai!', 'error');
                return false;
            }

            // Validasi tanggal perbaikan
            const tanggalMulaiPerbaikan = document.getElementById('tanggal_mulai_perbaikan').value;
            const tanggalSelesaiPerbaikan = document.getElementById('tanggal_selesai_perbaikan').value;

            if (tanggalMulaiPerbaikan && tanggalSelesaiPerbaikan) {
                if (new Date(tanggalSelesaiPerbaikan) < new Date(tanggalMulaiPerbaikan)) {
                    e.preventDefault();
                    showNotification('⚠️ Peringatan: Tanggal selesai perbaikan tidak boleh lebih awal dari tanggal mulai perbaikan!', 'error');
                    return false;
                }
            }

            // Clean up empty date fields
            if (!tanggalMulaiPerbaikan) {
                document.getElementById('tanggal_mulai_perbaikan').removeAttribute('name');
            }
            if (!tanggalSelesaiPerbaikan) {
                document.getElementById('tanggal_selesai_perbaikan').removeAttribute('name');
            }

            // Show loading state
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<div class="flex items-center"><div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>Menyimpan...</div>';
            }

            showNotification('Sedang menyimpan periode usulan...', 'info');
        });
    }
    
    // Auto-hide flash messages after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const flashMessages = document.querySelectorAll('.bg-green-100, .bg-red-100, .bg-yellow-100, .bg-blue-100');
        flashMessages.forEach(function(message) {
            setTimeout(function() {
                message.style.transition = 'opacity 0.5s ease-out';
                message.style.opacity = '0';
                setTimeout(function() {
                    message.remove();
                }, 500);
            }, 5000);
        });
        
        // Handle form submission with better feedback
        const form = document.getElementById('periodeForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const submitButton = form.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<div class="flex items-center"><div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>Menyimpan...</div>';
                }
                
                // Show immediate feedback
                showNotification('Sedang menyimpan periode usulan...', 'info');
            });
        }
    });

    // Handle tanggal perbaikan change
    function handleTanggalPerbaikanChange() {
        const tanggalMulaiPerbaikan = document.getElementById('tanggal_mulai_perbaikan');
        const tanggalSelesaiPerbaikan = document.getElementById('tanggal_selesai_perbaikan');
        const tanggalSelesai = document.getElementById('tanggal_selesai');

        // Set min date for tanggal_mulai_perbaikan
        if (tanggalSelesai.value) {
            tanggalMulaiPerbaikan.min = tanggalSelesai.value;
        }

        // Set min date for tanggal_selesai_perbaikan
        if (tanggalMulaiPerbaikan.value) {
            tanggalSelesaiPerbaikan.min = tanggalMulaiPerbaikan.value;
        }
    }

    // Notification function
    function showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.notification-toast');
        existingNotifications.forEach(notification => notification.remove());

        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification-toast fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transform transition-all duration-300 translate-x-full`;

        // Set background color based on type
        switch(type) {
            case 'success':
                notification.className += ' bg-green-500 text-white';
                break;
            case 'error':
                notification.className += ' bg-red-500 text-white';
                break;
            case 'warning':
                notification.className += ' bg-yellow-500 text-white';
                break;
            default:
                notification.className += ' bg-blue-500 text-white';
        }

        notification.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="mr-2">${type === 'success' ? '✅' : type === 'error' ? '❌' : type === 'warning' ? '⚠️' : 'ℹ️'}</span>
                    <span>${message}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;

        // Add to page
        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        // Auto remove after 5 seconds (except for info type)
        if (type !== 'info') {
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }
    }


});
</script>

