@extends('backend.layouts.roles.pegawai-unmul.app')

@section('title', $isEditing ? 'Edit Profil Saya' : 'Profil Saya')

@php
    function formatDate($date) {
        return $date ? \Carbon\Carbon::parse($date)->translatedFormat('d F Y') : '-';
    }

    $documentFields = [
        'ijazah_terakhir' => ['label' => 'Ijazah Terakhir', 'icon' => 'graduation-cap'],
        'transkrip_nilai_terakhir' => ['label' => 'Transkrip Nilai', 'icon' => 'file-text'],
        'sk_pangkat_terakhir' => ['label' => 'SK Pangkat Terakhir', 'icon' => 'award'],
        'sk_jabatan_terakhir' => ['label' => 'SK Jabatan Terakhir', 'icon' => 'briefcase'],
        'skp_tahun_pertama' => ['label' => 'SKP Tahun Pertama', 'icon' => 'clipboard-check'],
        'skp_tahun_kedua' => ['label' => 'SKP Tahun Kedua', 'icon' => 'clipboard-list'],
        'sk_cpns' => ['label' => 'SK CPNS', 'icon' => 'user-check'],
        'sk_pns' => ['label' => 'SK PNS', 'icon' => 'user-plus'],
        'pak_konversi' => ['label' => 'PAK Konversi', 'icon' => 'file-digit'],
        'sk_penyetaraan_ijazah' => ['label' => 'SK Penyetaraan Ijazah', 'icon' => 'scale'],
        'disertasi_thesis_terakhir' => ['label' => 'Disertasi/Thesis', 'icon' => 'book-open'],
    ];

    // Tambahkan PAK Integrasi hanya untuk jabatan tertentu
    if ($pegawai->jabatan && in_array($pegawai->jabatan->jenis_jabatan, ['Dosen Fungsional', 'Tenaga Kependidikan Fungsional Tertentu'])) {
        $documentFields['pak_integrasi'] = ['label' => 'PAK Integrasi', 'icon' => 'calculator'];
    }
@endphp

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/30">
    <form action="{{ route('pegawai-unmul.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($isEditing)
            @method('PUT')
        @endif

        {{-- Header Section --}}
        <div class="bg-white border-b">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-6 flex flex-wrap gap-4 justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            {{ $isEditing ? 'Edit Profil' : 'Profil Pegawai' }}
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ $isEditing ? 'Perbarui informasi kepegawaian Anda' : 'Informasi lengkap data kepegawaian' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        @if($isEditing)
                            <a href="{{ route('pegawai-unmul.profile.show') }}"
                               class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                Batal
                            </a>
                            <button type="submit"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                                <i data-lucide="save" class="w-4 h-4"></i>
                                Simpan Perubahan
                            </button>
                        @else
                            <a href="{{ route('pegawai-unmul.profile.edit') }}"
                               class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                                Edit Profil
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Alert Messages --}}
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-start gap-3">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="font-medium">Berhasil!</p>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-start gap-3 mt-4">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="font-medium">Terjadi Kesalahan</p>
                        <ul class="text-sm mt-1 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>

        {{-- Main Content --}}
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8"
             x-data="{
                 activeTab: 'personal',
                 jenisPegawai: '{{ old('jenis_pegawai', $pegawai->jenis_pegawai) ?? 'Dosen' }}',
                 statuses: {
                     'Dosen': ['Dosen PNS', 'Dosen PPPK', 'Dosen Non ASN'],
                     'Tenaga Kependidikan': ['Tenaga Kependidikan PNS', 'Tenaga Kependidikan PPPK', 'Tenaga Kependidikan Non ASN']
                 },
                 get availableStatuses() {
                     return this.statuses[this.jenisPegawai] || []
                 },
                 initJabatanFilter() {
                     // Karena jenis pegawai sudah difilter di controller, 
                     // kita hanya perlu memastikan jabatan yang ditampilkan sesuai
                     const jabatanSelect = document.querySelector('select[name=\"jabatan_terakhir_id\"]');
                     if (jabatanSelect) {
                         const currentJenisPegawai = '{{ $pegawai->jenis_pegawai }}';
                         const options = jabatanSelect.querySelectorAll('option');
                         
                         options.forEach(option => {
                             if (option.value === '') return; // Skip placeholder option
                             
                             const jabatanJenisPegawai = option.getAttribute('data-jenis-pegawai');
                             if (jabatanJenisPegawai !== currentJenisPegawai) {
                                 option.style.display = 'none';
                                 option.disabled = true;
                             }
                         });
                     }
                 }
             }"
             x-init="initJabatanFilter()">

            {{-- Profile Card --}}
            <div class="bg-white rounded-xl shadow-sm border mb-6 overflow-hidden">
                <div class="bg-gradient-to-r from-yellow-200 to-yellow-500 h-32"></div>
                <div class="px-6 pb-6">
                    <div class="flex flex-col sm:flex-row items-center sm:items-end gap-4 -mt-16">
                        {{-- Photo --}}
                        <div class="relative">
                            <div class="w-32 h-32 rounded-xl overflow-hidden border-4 border-white shadow-lg">
                                <img src="{{ $pegawai->foto ? asset('storage/' . $pegawai->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($pegawai->nama_lengkap) . '&size=128&background=6366f1&color=fff' }}"
                                     alt="Foto Profil"
                                     class="w-full h-full object-cover">
                            </div>
                            @if($isEditing)
                                <label for="foto" class="absolute bottom-0 right-0 bg-indigo-600 text-white p-2 rounded-lg cursor-pointer hover:bg-indigo-700 transition-colors shadow-lg">
                                    <i data-lucide="camera" class="w-4 h-4"></i>
                                </label>
                                <input type="file" id="foto" name="foto" class="hidden" accept="image/*"
                                    onchange="previewUploadedFile(this, 'preview-foto')">

                                {{-- Preview area untuk foto --}}
                                <div id="preview-foto" class="hidden mt-2"></div>
                            @endif
                        </div>

                        {{-- Basic Info --}}
                        <div class="flex-1 text-center sm:text-left">
                            <h2 class="text-2xl font-bold text-gray-900">
                                {{ $pegawai->gelar_depan ? $pegawai->gelar_depan . ' ' : '' }}
                                {{ $pegawai->nama_lengkap }}
                                {{ $pegawai->gelar_belakang ? ', ' . $pegawai->gelar_belakang : '' }}
                            </h2>
                            <p class="text-gray-600 mt-1">NIP: {{ $pegawai->nip }}</p>
                            <div class="flex flex-wrap gap-2 mt-3 justify-center sm:justify-start">
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-medium">
                                    {{ $pegawai->jabatan?->jabatan ?? 'Belum diset' }}
                                </span>
                                <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-medium">
                                    {{ $pegawai->pangkat?->pangkat ?? 'Belum diset' }}
                                </span>
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">
                                    {{ $pegawai->unitKerja?->nama ?? 'Belum diset' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab Navigation --}}
            <div class="bg-white rounded-xl shadow-sm border mb-6">
                <div class="border-b">
                    <nav class="flex space-x-8 px-6" aria-label="Tabs">
                        <button type="button"
                                @click="activeTab = 'personal'"
                                :class="activeTab === 'personal' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            <div class="flex items-center gap-2">
                                <i data-lucide="user" class="w-4 h-4"></i>
                                Data Pribadi
                            </div>
                        </button>
                        <button type="button"
                                @click="activeTab = 'kepegawaian'"
                                :class="activeTab === 'kepegawaian' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            <div class="flex items-center gap-2">
                                <i data-lucide="briefcase" class="w-4 h-4"></i>
                                Kepegawaian
                            </div>
                        </button>
                        <button type="button"
                                @click="activeTab = 'dokumen'"
                                :class="activeTab === 'dokumen' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            <div class="flex items-center gap-2">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                                Dokumen
                            </div>
                        </button>
                    </nav>
                </div>

                {{-- Tab Content --}}
                <div class="p-6">
                    {{-- Personal Tab --}}
                    <div x-show="activeTab === 'personal'" x-transition>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Nama Lengkap --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                @if($isEditing)
                                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $pegawai->nama_lengkap) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                @else
                                    <p class="text-gray-900">{{ $pegawai->nama_lengkap ?? '-' }}</p>
                                @endif
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                @if($isEditing)
                                    <input type="email" name="email" value="{{ old('email', $pegawai->email) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                @else
                                    <p class="text-gray-900">{{ $pegawai->email ?? '-' }}</p>
                                @endif
                            </div>

                            {{-- Gelar Depan --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Gelar Depan</label>
                                @if($isEditing)
                                    <input type="text" name="gelar_depan" value="{{ old('gelar_depan', $pegawai->gelar_depan) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                @else
                                    <p class="text-gray-900">{{ $pegawai->gelar_depan ?? '-' }}</p>
                                @endif
                            </div>

                            {{-- Gelar Belakang --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Gelar Belakang</label>
                                @if($isEditing)
                                    <input type="text" name="gelar_belakang" value="{{ old('gelar_belakang', $pegawai->gelar_belakang) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                @else
                                    <p class="text-gray-900">{{ $pegawai->gelar_belakang ?? '-' }}</p>
                                @endif
                            </div>

                            {{-- Tempat Lahir --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir</label>
                                @if($isEditing)
                                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $pegawai->tempat_lahir) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                @else
                                    <p class="text-gray-900">{{ $pegawai->tempat_lahir ?? '-' }}</p>
                                @endif
                            </div>

                            {{-- Tanggal Lahir --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                                @if($isEditing)
                                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $pegawai->tanggal_lahir ? $pegawai->tanggal_lahir->format('Y-m-d') : '') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                @else
                                    <p class="text-gray-900">{{ formatDate($pegawai->tanggal_lahir) }}</p>
                                @endif
                            </div>

                            {{-- Jenis Kelamin --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                                @if($isEditing)
                                    <select name="jenis_kelamin" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="Laki-Laki" {{ old('jenis_kelamin', $pegawai->jenis_kelamin) == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                        <option value="Perempuan" {{ old('jenis_kelamin', $pegawai->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                @else
                                    <p class="text-gray-900">{{ $pegawai->jenis_kelamin ?? '-' }}</p>
                                @endif
                            </div>

                            {{-- Nomor HP --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor HP</label>
                                @if($isEditing)
                                    <input type="text" name="nomor_handphone" value="{{ old('nomor_handphone', $pegawai->nomor_handphone) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                @else
                                    <p class="text-gray-900">{{ $pegawai->nomor_handphone ?? '-' }}</p>
                                @endif
                            </div>

                            {{-- Pendidikan Terakhir --}}
                             <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pendidikan Terakhir</label>
                                @if($isEditing)
                                    <select name="pendidikan_terakhir" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">-- Pilih Pendidikan Terakhir --</option>
                                        <option value="Sekolah Dasar (SD)" {{ old('pendidikan_terakhir', $pegawai->pendidikan_terakhir) == 'Sekolah Dasar (SD)' ? 'selected' : '' }}>Sekolah Dasar (SD)</option>
                                        <option value="Sekolah Lanjutan Tingkat Pertama (SLTP) / Sederajat" {{ old('pendidikan_terakhir', $pegawai->pendidikan_terakhir) == 'Sekolah Lanjutan Tingkat Pertama (SLTP) / Sederajat' ? 'selected' : '' }}>Sekolah Lanjutan Tingkat Pertama (SLTP) / Sederajat</option>
                                        <option value="Sekolah Lanjutan Tingkat Menengah (SLTA)" {{ old('pendidikan_terakhir', $pegawai->pendidikan_terakhir) == 'Sekolah Lanjutan Tingkat Menengah (SLTA)' ? 'selected' : '' }}>Sekolah Lanjutan Tingkat Menengah (SLTA)</option>
                                        <option value="Diploma I" {{ old('pendidikan_terakhir', $pegawai->pendidikan_terakhir) == 'Diploma I' ? 'selected' : '' }}>Diploma I</option>
                                        <option value="Diploma II" {{ old('pendidikan_terakhir', $pegawai->pendidikan_terakhir) == 'Diploma II' ? 'selected' : '' }}>Diploma II</option>
                                        <option value="Diploma III" {{ old('pendidikan_terakhir', $pegawai->pendidikan_terakhir) == 'Diploma III' ? 'selected' : '' }}>Diploma III</option>
                                        <option value="Sarjana (S1) / Diploma IV / Sederajat" {{ old('pendidikan_terakhir', $pegawai->pendidikan_terakhir) == 'Sarjana (S1) / Diploma IV / Sederajat' ? 'selected' : '' }}>Sarjana (S1) / Diploma IV / Sederajat</option>
                                        <option value="Magister (S2) / Sederajat" {{ old('pendidikan_terakhir', $pegawai->pendidikan_terakhir) == 'Magister (S2) / Sederajat' ? 'selected' : '' }}>Magister (S2) / Sederajat</option>
                                        <option value="Doktor (S3) / Sederajat" {{ old('pendidikan_terakhir', $pegawai->pendidikan_terakhir) == 'Doktor (S3) / Sederajat' ? 'selected' : '' }}>Doktor (S3) / Sederajat</option>
                                    </select>
                                @else
                                    <p class="text-gray-900">{{ $pegawai->pendidikan_terakhir ?? '-' }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Kepegawaian Tab --}}
                    <div x-show="activeTab === 'kepegawaian'" x-transition>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- NIP --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">NIP</label>
                                @if($isEditing)
                                    <input type="text" name="nip" value="{{ old('nip', $pegawai->nip) }}"
                                           class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-600"
                                           readonly>
                                @else
                                    <p class="text-gray-900">{{ $pegawai->nip }}</p>
                                @endif
                            </div>

                            {{-- No Kartu Pegawai --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">No. Kartu Pegawai</label>
                                @if($isEditing)
                                    <input type="text" name="nomor_kartu_pegawai" value="{{ old('nomor_kartu_pegawai', $pegawai->nomor_kartu_pegawai) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                @else
                                    <p class="text-gray-900">{{ $pegawai->nomor_kartu_pegawai ?? '-' }}</p>
                                @endif
                            </div>

                            {{-- Jenis Pegawai --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Pegawai</label>
                                @if($isEditing)
                                    <input type="hidden" name="jenis_pegawai" value="{{ $pegawai->jenis_pegawai }}">
                                    <input type="text" value="{{ $pegawai->jenis_pegawai }}"
                                           class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-600"
                                           readonly disabled>
                                @else
                                    <p class="text-gray-900">{{ $pegawai->jenis_pegawai ?? '-' }}</p>
                                @endif
                            </div>


                            {{-- Status Kepegawaian --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status Kepegawaian</label>
                                @if($isEditing)
                                    <input type="hidden" name="status_kepegawaian" value="{{ old('status_kepegawaian', $pegawai->status_kepegawaian) }}">
                                    <input type="text" value="{{ $pegawai->status_kepegawaian }}"
                                           class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-600"
                                           readonly>
                                @else
                                    <p class="text-gray-900">{{ $pegawai->status_kepegawaian ?? '-' }}</p>
                                @endif
                            </div>

                            {{-- Pangkat --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pangkat/Golongan</label>
                                @if($isEditing)
                                    <select name="pangkat_terakhir_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                        @foreach($pangkats as $pangkat)
                                            <option value="{{ $pangkat->id }}" {{ old('pangkat_terakhir_id', $pegawai->pangkat_terakhir_id) == $pangkat->id ? 'selected' : '' }}>
                                                {{ $pangkat->pangkat }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <p class="text-gray-900">{{ $pegawai->pangkat?->pangkat ?? '-' }}</p>
                                @endif
                            </div>

                            {{-- TMT Pangkat --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">TMT Pangkat</label>
                                @if($isEditing)
                                    <input type="date" name="tmt_pangkat" value="{{ old('tmt_pangkat', $pegawai->tmt_pangkat ? $pegawai->tmt_pangkat->format('Y-m-d') : '') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                @else
                                    <p class="text-gray-900">{{ formatDate($pegawai->tmt_pangkat) }}</p>
                                @endif
                            </div>

                            {{-- Jabatan --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                                @if($isEditing)
                                    <select name="jabatan_terakhir_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">-- Pilih Jabatan --</option>
                                        @foreach($jabatans as $jabatan)
                                            <option value="{{ $jabatan->id }}" 
                                                    data-jenis-pegawai="{{ $jabatan->jenis_pegawai }}"
                                                    data-jenis-jabatan="{{ $jabatan->jenis_jabatan }}"
                                                    {{ old('jabatan_terakhir_id', $pegawai->jabatan_terakhir_id) == $jabatan->id ? 'selected' : '' }}>
                                                {{ $jabatan->jabatan }}
                                                @if($jabatan->jenis_jabatan)
                                                    ({{ $jabatan->jenis_jabatan }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <p class="text-gray-900">{{ $pegawai->jabatan?->jabatan ?? '-' }}</p>
                                @endif
                            </div>

                            {{-- TMT Jabatan --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">TMT Jabatan</label>
                                @if($isEditing)
                                    <input type="date" name="tmt_jabatan" value="{{ old('tmt_jabatan', $pegawai->tmt_jabatan ? $pegawai->tmt_jabatan->format('Y-m-d') : '') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                @else
                                    <p class="text-gray-900">{{ formatDate($pegawai->tmt_jabatan) }}</p>
                                @endif
                            </div>

                            {{-- Unit Kerja --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Unit Kerja</label>
                                @if($isEditing)
                                    <select name="unit_kerja_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                        @foreach($unitKerjas as $unit)
                                            <option value="{{ $unit->id }}" {{ old('unit_kerja_id', $pegawai->unit_kerja_id) == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <p class="text-gray-900">{{ $pegawai->unitKerja?->nama ?? '-' }}</p>
                                @endif
                            </div>

                            {{-- TMT CPNS --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">TMT CPNS</label>
                                @if($isEditing)
                                    <input type="date" name="tmt_cpns" value="{{ old('tmt_cpns', $pegawai->tmt_cpns ? $pegawai->tmt_cpns->format('Y-m-d') : '') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                @else
                                    <p class="text-gray-900">{{ formatDate($pegawai->tmt_cpns) }}</p>
                                @endif
                            </div>

                            {{-- TMT PNS --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">TMT PNS</label>
                                @if($isEditing)
                                    <input type="date" name="tmt_pns" value="{{ old('tmt_pns', $pegawai->tmt_pns ? $pegawai->tmt_pns->format('Y-m-d') : '') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                @else
                                    <p class="text-gray-900">{{ formatDate($pegawai->tmt_pns) }}</p>
                                @endif
                            </div>

                            {{-- Predikat Kinerja --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Predikat Kinerja Tahun Pertama</label>
                                @if($isEditing)
                                    <select name="predikat_kinerja_tahun_pertama" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">-- Pilih Predikat --</option>
                                        <option value="Sangat Baik" {{ old('predikat_kinerja_tahun_pertama', $pegawai->predikat_kinerja_tahun_pertama) == 'Sangat Baik' ? 'selected' : '' }}>Sangat Baik</option>
                                        <option value="Baik" {{ old('predikat_kinerja_tahun_pertama', $pegawai->predikat_kinerja_tahun_pertama) == 'Baik' ? 'selected' : '' }}>Baik</option>
                                        <option value="Perlu Perbaikan" {{ old('predikat_kinerja_tahun_pertama', $pegawai->predikat_kinerja_tahun_pertama) == 'Perlu Perbaikan' ? 'selected' : '' }}>Perlu Perbaikan</option>
                                    </select>
                                @else
                                    <p class="text-gray-900">{{ $pegawai->predikat_kinerja_tahun_pertama ?? '-' }}</p>
                                @endif
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Predikat Kinerja Tahun Kedua</label>
                                @if($isEditing)
                                    <select name="predikat_kinerja_tahun_kedua" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">-- Pilih Predikat --</option>
                                        <option value="Sangat Baik" {{ old('predikat_kinerja_tahun_kedua', $pegawai->predikat_kinerja_tahun_kedua) == 'Sangat Baik' ? 'selected' : '' }}>Sangat Baik</option>
                                        <option value="Baik" {{ old('predikat_kinerja_tahun_kedua', $pegawai->predikat_kinerja_tahun_kedua) == 'Baik' ? 'selected' : '' }}>Baik</option>
                                        <option value="Perlu Perbaikan" {{ old('predikat_kinerja_tahun_kedua', $pegawai->predikat_kinerja_tahun_kedua) == 'Perlu Perbaikan' ? 'selected' : '' }}>Perlu Perbaikan</option>
                                    </select>
                                @else
                                    <p class="text-gray-900">{{ $pegawai->predikat_kinerja_tahun_kedua ?? '-' }}</p>
                                @endif
                            </div>

                            {{-- Khusus Dosen --}}
                            <div class="md:col-span-2" x-show="jenisPegawai === 'Dosen'">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Informasi Khusus Dosen</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {{-- NUPTK --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">NUPTK</label>
                                        @if($isEditing)
                                            <input type="text" name="nuptk" value="{{ old('nuptk', $pegawai->nuptk) }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                        @else
                                            <p class="text-gray-900">{{ $pegawai->nuptk ?? '-' }}</p>
                                        @endif
                                    </div>

                                    {{-- Nilai Konversi --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Nilai Konversi</label>
                                        @if($isEditing)
                                            <input type="text" name="nilai_konversi" value="{{ old('nilai_konversi', $pegawai->nilai_konversi) }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                        @else
                                            <p class="text-gray-900">{{ $pegawai->nilai_konversi ?? '-' }}</p>
                                        @endif
                                    </div>

                                    {{-- Ranting Ilmu --}}
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Ranting Ilmu/Kepakaran</label>
                                        @if($isEditing)
                                            <textarea name="ranting_ilmu_kepakaran" rows="2"
                                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('ranting_ilmu_kepakaran', $pegawai->ranting_ilmu_kepakaran) }}</textarea>
                                        @else
                                            <p class="text-gray-900">{{ $pegawai->ranting_ilmu_kepakaran ?? '-' }}</p>
                                        @endif
                                    </div>

                                    {{-- Mata Kuliah --}}
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Mata Kuliah Diampu</label>
                                        @if($isEditing)
                                            <textarea name="mata_kuliah_diampu" rows="2"
                                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('mata_kuliah_diampu', $pegawai->mata_kuliah_diampu) }}</textarea>
                                        @else
                                            <p class="text-gray-900">{{ $pegawai->mata_kuliah_diampu ?? '-' }}</p>
                                        @endif
                                    </div>

                                    {{-- URL Sinta --}}
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">URL Profil Sinta</label>
                                        @if($isEditing)
                                            <input type="url" name="url_profil_sinta" value="{{ old('url_profil_sinta', $pegawai->url_profil_sinta) }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                        @else
                                            @if($pegawai->url_profil_sinta)
                                                <a href="{{ $pegawai->url_profil_sinta }}" target="_blank"
                                                   class="text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                                                    {{ $pegawai->url_profil_sinta }}
                                                    <i data-lucide="external-link" class="w-3 h-3"></i>
                                                </a>
                                            @else
                                                <p class="text-gray-900">-</p>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Dokumen Tab --}}
                    <div x-show="activeTab === 'dokumen'" x-transition>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($documentFields as $field => $doc)
                                <div class="border rounded-lg p-4 hover:border-indigo-300 transition-colors {{ $pegawai->$field ? 'bg-green-50 border-green-200' : 'bg-gray-50' }}">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="p-2 rounded-lg {{ $pegawai->$field ? 'bg-green-100 text-green-600' : 'bg-gray-200 text-gray-400' }}">
                                                <i data-lucide="{{ $doc['icon'] }}" class="w-5 h-5"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-900">{{ $doc['label'] }}</h4>
                                                @if($pegawai->$field)
                                                    <p class="text-xs text-green-600 mt-1">
                                                        <i data-lucide="check-circle" class="w-3 h-3 inline"></i>
                                                        File tersedia
                                                    </p>
                                                @else
                                                    <p class="text-xs text-gray-500 mt-1">Belum diunggah</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @if($pegawai->$field)
                                        <a href="{{ route('backend.kepegawaian-universitas.data-pegawai.show-document', ['pegawai' => $pegawai->id, 'field' => $field]) }}"
                                           target="_blank"
                                           class="inline-flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                            Lihat Dokumen
                                        </a>
                                    @endif

                                    @if($isEditing)
                                        <div class="mt-3">
                                            <label for="{{ $field }}"
                                                class="block w-full text-center px-4 py-2 bg-white border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                                <i data-lucide="upload" class="w-4 h-4 inline mr-2"></i>
                                                {{ $pegawai->{$field} ? 'Ganti File' : 'Upload File' }}
                                            </label>
                                            <input type="file"
                                                name="{{ $field }}"
                                                id="{{ $field }}"
                                                class="hidden"
                                                accept=".pdf"
                                                onchange="previewUploadedFile(this, 'preview-{{ $field }}')">

                                            {{-- Preview area untuk file yang baru diupload --}}
                                            <div id="preview-{{ $field }}" class="hidden"></div>

                                            <p class="text-xs text-gray-500 mt-2 text-center">Format: PDF, Max: 2MB</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mobile Action Buttons --}}
            @if($isEditing)
                <div class="fixed bottom-0 left-0 right-0 bg-white border-t px-4 py-3 flex gap-3 md:hidden">
                    <a href="{{ route('pegawai-unmul.profile.show') }}"
                       class="flex-1 text-center px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg">
                        Batal
                    </a>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg">
                        Simpan
                    </button>
                </div>
            @endif
        </div>
    </form>
</div>
@endsection
