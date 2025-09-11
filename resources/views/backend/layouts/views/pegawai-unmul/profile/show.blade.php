{{-- resources/views/pegawai-unmul/profile/show.blade.php --}}
@php
    $layout = $isAdmin ? 'backend.layouts.roles.kepegawaian-universitas.app' : 'backend.layouts.roles.pegawai-unmul.app';
    $title = $isAdmin ?
        ($isCreating ? 'Tambah Data Pegawai' : ($isEditing ? 'Edit Data Pegawai' : 'Data Pegawai')) :
        ($isEditing ? 'Edit Profil Saya' : 'Profil Saya');
@endphp
@extends($layout)

@section('title', $title)

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

    // Tambahkan PAK Integrasi hanya untuk jabatan tertentu (hanya jika bukan create mode)
    if (!$isCreating && $pegawai->jabatan && in_array($pegawai->jabatan->jenis_jabatan, ['Dosen Fungsional', 'Tenaga Kependidikan Fungsional Tertentu'])) {
        $documentFields['pak_integrasi'] = ['label' => 'PAK Integrasi', 'icon' => 'calculator'];
    }
@endphp

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/30">
    <form action="{{ $isCreating ? route('backend.kepegawaian-universitas.data-pegawai.store') : ($isAdmin ? route('backend.kepegawaian-universitas.data-pegawai.update', $pegawai->id) : route('pegawai-unmul.profile.update')) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($isEditing && !$isCreating)
            @if(!$isCreating)
                @method('PUT')
            @endif
        @endif

        {{-- Header Section --}}
        <div class="bg-white border-b">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-6 flex flex-wrap gap-4 justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            @if($isAdmin)
                                {{ $isCreating ? 'Tambah Data Pegawai' : ($isEditing ? 'Edit Data Pegawai' : 'Data Pegawai') }}
                            @else
                                {{ $isEditing ? 'Edit Profil' : 'Profil Pegawai' }}
                            @endif
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            @if($isAdmin)
                                {{ $isCreating ? 'Tambahkan pegawai baru ke dalam sistem' : ($isEditing ? 'Perbarui informasi kepegawaian pegawai' : 'Informasi lengkap data kepegawaian pegawai') }}
                            @else
                                {{ $isEditing ? 'Perbarui informasi kepegawaian Anda' : 'Informasi lengkap data kepegawaian' }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 p-5"
             x-data="{
                 activeTab: 'personal',
                 jenisPegawai: '{{ old('jenis_pegawai', $pegawai->jenis_pegawai ?? 'Dosen') }}',
                 statusKepegawaian: '{{ old('status_kepegawaian', $pegawai->status_kepegawaian ?? '') }}',
                 statuses: {
                     'Dosen': ['Dosen PNS', 'Dosen PPPK', 'Dosen Non ASN'],
                     'Tenaga Kependidikan': ['Tenaga Kependidikan PNS', 'Tenaga Kependidikan PPPK', 'Tenaga Kependidikan Non ASN']
                 },
                 get availableStatuses() {
                     return this.statuses[this.jenisPegawai] || []
                 }
             }">

            {{-- Profile Header Component --}}
            @include('backend.layouts.views.pegawai-unmul.profile.profile-header', ['pegawai' => $pegawai, 'isEditing' => $isEditing, 'isAdmin' => $isAdmin, 'isCreating' => $isCreating])

            {{-- Tab Navigation & Content --}}
            <div class="bg-white rounded-xl shadow-sm border mb-6">
                <div class="border-b">
                    <nav class="flex space-x-8 px-6" aria-label="Tabs">
                        {{-- Personal Tab --}}
                        <button type="button"
                                @click="activeTab = 'personal'"
                                :class="activeTab === 'personal' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            <div class="flex items-center gap-2">
                                <i data-lucide="user" class="w-4 h-4"></i>
                                Data Pribadi
                            </div>
                        </button>

                        {{-- Kepegawaian Tab --}}
                        <button type="button"
                                @click="activeTab = 'kepegawaian'"
                                :class="activeTab === 'kepegawaian' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            <div class="flex items-center gap-2">
                                <i data-lucide="briefcase" class="w-4 h-4"></i>
                                Kepegawaian
                            </div>
                        </button>

                        {{-- PAK & SKP Tab --}}
                        <button type="button"
                                @click="activeTab = 'pak_skp'"
                                :class="activeTab === 'pak_skp' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            <div class="flex items-center gap-2">
                                <i data-lucide="clipboard-check" class="w-4 h-4"></i>
                                PAK & SKP
                            </div>
                        </button>

                        {{-- Informasi Dosen Tab - Conditional --}}
                        <button type="button"
                                x-show="jenisPegawai === 'Dosen'"
                                @click="activeTab = 'dosen'"
                                :class="activeTab === 'dosen' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            <div class="flex items-center gap-2">
                                <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                                Informasi Dosen
                            </div>
                        </button>

                        {{-- Security Tab --}}
                        <button type="button"
                                @click="activeTab = 'security'"
                                :class="activeTab === 'security' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            <div class="flex items-center gap-2">
                                <i data-lucide="shield" class="w-4 h-4"></i>
                                Keamanan
                            </div>
                        </button>

                        {{-- Dokumen Tab --}}
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
                    @include('backend.layouts.views.pegawai-unmul.profile.components.tabs.personal-tab', ['pegawai' => $pegawai, 'isEditing' => $isEditing, 'isAdmin' => $isAdmin, 'isCreating' => $isCreating])

                    {{-- Kepegawaian Tab --}}
                    @include('backend.layouts.views.pegawai-unmul.profile.components.tabs.kepegawaian-tab', [
                        'pegawai' => $pegawai,
                        'isEditing' => $isEditing,
                        'isAdmin' => $isAdmin,
                        'isCreating' => $isCreating,
                        'pangkats' => $pangkats ?? [],
                        'jabatans' => $jabatans ?? [],
                        'unitKerjas' => $unitKerjas ?? []
                    ])

                    {{-- PAK & SKP Tab --}}
                    @include('backend.layouts.views.pegawai-unmul.profile.components.tabs.pak-skp-tab', ['pegawai' => $pegawai, 'isEditing' => $isEditing, 'isAdmin' => $isAdmin, 'isCreating' => $isCreating])

                    {{-- Informasi Dosen Tab - Conditional --}}
                    @include('backend.layouts.views.pegawai-unmul.profile.components.tabs.dosen-tab', ['pegawai' => $pegawai, 'isEditing' => $isEditing, 'isAdmin' => $isAdmin, 'isCreating' => $isCreating])

                    {{-- Security Tab --}}
                    @include('backend.layouts.views.pegawai-unmul.profile.components.tabs.security-tab', ['pegawai' => $pegawai, 'isEditing' => $isEditing, 'isAdmin' => $isAdmin, 'isCreating' => $isCreating])

                    {{-- Dokumen Tab --}}
                    @include('backend.layouts.views.pegawai-unmul.profile.components.tabs.dokumen-tab', [
                        'pegawai' => $pegawai,
                        'isEditing' => $isEditing,
                        'isAdmin' => $isAdmin,
                        'isCreating' => $isCreating,
                        'documentFields' => $documentFields
                    ])
                </div>
            </div>

            {{-- Mobile Action Buttons --}}
            @if($isEditing)
                <div class="fixed bottom-0 left-0 right-0 bg-white border-t px-4 py-3 flex gap-3 md:hidden z-40">
                    <a href="{{ $isAdmin ? route('backend.kepegawaian-universitas.data-pegawai.index') : route('pegawai-unmul.profile.show') }}"
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

@push('scripts')
<script src="{{ asset('js/upload-indicator.js') }}"></script>
@endpush

@endsection
