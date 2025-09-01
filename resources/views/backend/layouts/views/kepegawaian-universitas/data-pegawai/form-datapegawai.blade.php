@extends('backend.layouts.roles.kepegawaian-universitas.app')

@section('title', isset($pegawai) ? 'Edit Data Pegawai' : 'Tambah Data Pegawai')

@section('content')
{{-- Modern Background dengan Gradient Animasi --}}
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/50 relative overflow-hidden">
    {{-- Animated Background Elements --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-indigo-400/20 to-purple-400/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-tr from-blue-400/20 to-cyan-400/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-gradient-to-r from-purple-400/10 to-pink-400/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 4s;"></div>
    </div>

    <div class="w-full px-4 lg:px-6 py-6 lg:py-8 relative z-10">
        {{-- Header Section dengan Animasi Modern --}}
        <div class="bg-white/90 backdrop-blur-xl p-6 lg:p-8 rounded-3xl shadow-2xl border border-white/30 mb-6 lg:mb-8 transform transition-all duration-700 hover:scale-[1.02] hover:shadow-3xl animate-fade-in-down">
            <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 lg:gap-6">
                <div class="space-y-3 flex-1 min-w-0">
                    <div class="flex items-center gap-3">
                        <div class="p-2 lg:p-3 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl shadow-lg flex-shrink-0">
                            <svg class="w-6 h-6 lg:w-8 lg:h-8 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if(isset($pegawai))
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112-2h4a2 2 0 012 2v2a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                                @endif
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h1 class="text-2xl lg:text-4xl text-black font-bold break-words">
                                {{ isset($pegawai) ? 'Edit Data Pegawai' : 'Tambah Data Pegawai' }}
                            </h1>
                            <p class="text-slate-600 text-sm lg:text-lg mt-1 break-words">
                                {{ isset($pegawai) ? 'Perbarui informasi pegawai dengan data terbaru' : 'Tambahkan pegawai baru ke dalam sistem' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-xs lg:text-sm text-slate-500 bg-slate-100/50 px-3 lg:px-4 py-2 rounded-xl">
                        <svg class="w-3 h-3 lg:w-4 lg:h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="break-words">Kolom dengan <span class="text-red-500 font-semibold">*</span> wajib diisi</span>
                    </div>
                </div>

                {{-- Action Buttons dengan Animasi --}}
                <div class="flex flex-col sm:flex-row gap-2 lg:gap-3 flex-shrink-0">
                    <a href="{{ route('backend.kepegawaian-universitas.data-pegawai.index') }}"
                       class="group inline-flex items-center justify-center gap-2 lg:gap-3 px-4 lg:px-6 py-2 lg:py-3 text-slate-700 bg-white/80 backdrop-blur-sm border border-slate-200 rounded-xl shadow-lg hover:bg-slate-50 hover:shadow-xl hover:scale-105 transition-all duration-300 text-sm lg:text-base">
                        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform duration-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span class="whitespace-nowrap">Kembali</span>
                    </a>

                    @if(isset($pegawai))
                    <a href="{{ route('backend.kepegawaian-universitas.data-pegawai.show', $pegawai->id) }}"
                       class="group inline-flex items-center justify-center gap-2 lg:gap-3 px-4 lg:px-6 py-2 lg:py-3 text-blue-700 bg-blue-50/80 backdrop-blur-sm border border-blue-200 rounded-xl shadow-lg hover:bg-blue-100 hover:shadow-xl hover:scale-105 transition-all duration-300 text-sm lg:text-base">
                        <svg class="w-4 h-4 group-hover:scale-110 transition-transform duration-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span class="whitespace-nowrap">Lihat Detail</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Error Messages dengan Animasi Modern --}}
        @if ($errors->any())
            <div class="bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 text-red-700 p-4 lg:p-6 rounded-2xl mb-6 lg:mb-8 shadow-xl animate-shake" role="alert">
                <div class="flex items-start gap-3 lg:gap-4">
                    <div class="flex-shrink-0 p-2 bg-red-100 rounded-xl">
                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-red-800 mb-2 lg:mb-3 text-base lg:text-lg break-words">Terjadi kesalahan validasi</h3>
                        <ul class="space-y-2 text-xs lg:text-sm">
                            @foreach ($errors->all() as $error)
                                <li class="flex items-start gap-2 lg:gap-3 p-2 bg-red-100/50 rounded-lg">
                                    <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse flex-shrink-0 mt-1"></span>
                                    <span class="break-words">{{ $error }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Main Form dengan Layout Modern --}}
        <form action="{{ isset($pegawai) ? route('backend.kepegawaian-universitas.data-pegawai.update', $pegawai->id) : route('backend.kepegawaian-universitas.data-pegawai.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-6 lg:space-y-8"
              x-data="{
                  activeTab: 'personal',
                  isLoading: false,
                  showSuccess: {{ session('success') ? 'true' : 'false' }},
                  formProgress: 0,
                  jenisPegawai: '{{ old('jenis_pegawai', $pegawai->jenis_pegawai ?? '') }}',
                  jenisJabatan: '{{ old('jenis_jabatan', $pegawai->jabatan->jenis_jabatan ?? '') }}',
                  isPakIntegrasiEligible() {
                      return this.jenisJabatan === 'Dosen Fungsional' || this.jenisJabatan === 'Tenaga Kependidikan Fungsional Tertentu';
                  },
                  updateJenisJabatan(event) {
                      const selectedOption = event.target.options[event.target.selectedIndex];
                      this.jenisJabatan = selectedOption.getAttribute('data-jenis-jabatan') || '';
                  },
                  init() {
                      // Initialize jenisJabatan from selected option
                      const jabatanSelect = document.getElementById('jabatan_terakhir_id');
                      if (jabatanSelect && jabatanSelect.value) {
                          const selectedOption = jabatanSelect.options[jabatanSelect.selectedIndex];
                          this.jenisJabatan = selectedOption.getAttribute('data-jenis-jabatan') || '';
                      }
                  }
              }"
              x-init="init()">
            @csrf
            @if(isset($pegawai))
                @method('PUT')
            @endif

            {{-- ============================ SIDEBAR KIRI (FOTO & QUICK INFO) =============================== --}}
            <div class="xl:col-span-1">
                {{-- Photo Upload Card dengan Animasi Modern --}}
                <div class="bg-white/90 backdrop-blur-xl p-4 lg:p-6 rounded-3xl shadow-2xl border border-white/30 sticky top-8 animate-fade-in-left">
                    <div class="text-center space-y-4 lg:space-y-6">
                        {{-- Photo Preview dengan Efek Modern --}}
                        <div class="relative group">
                            <div class="w-32 h-32 lg:w-40 lg:h-40 mx-auto rounded-full overflow-hidden border-4 border-white shadow-2xl group-hover:shadow-3xl transition-all duration-500 relative">
                                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/20 to-purple-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <img id="foto-preview"
                                        src="{{ isset($pegawai) && $pegawai->foto ? route('backend.kepegawaian-universitas.data-pegawai.show-document', ['pegawai' => $pegawai->id, 'field' => 'foto']) : 'https://ui-avatars.com/api/?name=' . urlencode($pegawai->nama_lengkap ?? 'Pegawai') . '&background=6366f1&color=fff&size=160' }}"
                                        alt="Foto Pegawai"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                        onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($pegawai->nama_lengkap ?? 'Pegawai') }}&background=6366f1&color=fff&size=160'">
                            </div>

                            {{-- Upload Overlay dengan Animasi --}}
                            <label for="foto" class="absolute inset-0 flex items-center justify-center bg-black/60 opacity-0 group-hover:opacity-100 rounded-full cursor-pointer transition-all duration-500 backdrop-blur-sm">
                                <div class="text-white text-center transform translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                                    <svg class="w-6 h-6 lg:w-8 lg:h-8 mx-auto mb-1 lg:mb-2 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="text-xs lg:text-sm font-medium">Ganti Foto</span>
                                </div>
                            </label>

                            {{-- Upload Progress Indicator --}}
                            <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-24 lg:w-32 h-1 bg-slate-200 rounded-full overflow-hidden hidden z-10" id="upload-progress">
                                <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>

                            {{-- Upload Status Indicator --}}
                            <div class="absolute inset-0 flex items-center justify-center bg-black/80 opacity-0 rounded-full transition-all duration-300 hidden" id="upload-status">
                                <div class="text-white text-center">
                                    <div class="w-8 h-8 border-2 border-white border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
                                    <span class="text-xs font-medium">Loading...</span>
                                </div>
                            </div>
                        </div>

                        {{-- File Input --}}
                        <input type="file" name="foto" id="foto" class="hidden" accept="image/*" onchange="previewImage(event)">

                        {{-- Upload Info dengan Icon --}}
                        <div class="space-y-2 p-3 lg:p-4 bg-slate-50/50 rounded-xl">
                            <div class="flex items-center gap-2 text-xs lg:text-sm text-slate-600">
                                <svg class="w-3 h-3 lg:w-4 lg:h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="break-words">Format: JPG, PNG, JPEG</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                <svg class="w-3 h-3 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                                </svg>
                                <span>Maksimal 2MB</span>
                            </div>
                        </div>

                        {{-- Quick Stats dengan Animasi --}}
                        @if(isset($pegawai))
                        <div class="pt-4 lg:pt-6 border-t border-slate-200">
                            <div class="grid grid-cols-2 gap-3 lg:gap-4 text-center">
                                <div class="p-2 lg:p-3 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border border-blue-200/50 hover:scale-105 transition-transform duration-300">
                                    <div class="text-lg lg:text-2xl font-bold text-blue-600 animate-count-up">{{ $pegawai->usulans->count() }}</div>
                                    <div class="text-xs text-blue-500 font-medium">Usulan</div>
                                </div>
                                <div class="p-2 lg:p-3 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border border-green-200/50 hover:scale-105 transition-transform duration-300">
                                    <div class="text-lg lg:text-2xl font-bold text-green-600 animate-count-up">{{ number_format(\Carbon\Carbon::parse($pegawai->tmt_pns)->diffInYears(now()), 2) }}</div>
                                    <div class="text-xs text-green-500 font-medium">Tahun</div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ============================ KONTEN UTAMA (FORM FIELDS) =============================== --}}
            <div class="xl:col-span-5 space-y-6 lg:space-y-8">
                {{-- Progress Bar --}}
                <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/30 p-4 lg:p-6 animate-fade-in-up">
                    <div class="flex items-center justify-between mb-3 lg:mb-4">
                        <h3 class="text-base lg:text-lg font-semibold text-slate-800">Progress Pengisian</h3>
                        <span class="text-xs lg:text-sm text-slate-600" x-text="`${formProgress}%`"></span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-2 lg:h-3 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full transition-all duration-500 ease-out"
                                :style="`width: ${formProgress}%`"></div>
                    </div>
                </div>

                {{-- Tab Navigation dengan Desain Modern --}}
                <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/30 overflow-hidden animate-fade-in-up">
                    <div class="border-b border-slate-200 bg-gradient-to-r from-slate-50 to-blue-50/30">
                        <nav class="flex flex-wrap gap-1 p-2" aria-label="Tabs">
                            <button type="button"
                                    @click="activeTab = 'personal'; updateProgress()"
                                    :class="activeTab === 'personal' ? 'bg-white text-indigo-700 border-indigo-200 shadow-lg scale-105' : 'text-slate-600 hover:text-slate-800 hover:bg-white/50'"
                                    class="flex-1 min-w-0 px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm font-medium rounded-xl border transition-all duration-300 transform">
                                <div class="flex items-center justify-center gap-1 lg:gap-2">
                                    <svg class="w-3 h-3 lg:w-4 lg:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="truncate">Data Pribadi</span>
                                </div>
                            </button>
                            <button type="button"
                                    @click="activeTab = 'employment'; updateProgress()"
                                    :class="activeTab === 'employment' ? 'bg-white text-indigo-700 border-indigo-200 shadow-lg scale-105' : 'text-slate-600 hover:text-slate-800 hover:bg-white/50'"
                                    class="flex-1 px-4 py-3 text-sm font-medium rounded-xl border transition-all duration-300 transform">
                                <div class="flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                                    </svg>
                                    <span>Kepegawaian</span>
                                </div>
                            </button>
                            <button type="button"
                                    @click="activeTab = 'dosen'; updateProgress()"
                                    :class="activeTab === 'dosen' ? 'bg-white text-indigo-700 border-indigo-200 shadow-lg scale-105' : 'text-slate-600 hover:text-slate-800 hover:bg-white/50'"
                                    class="flex-1 px-4 py-3 text-sm font-medium rounded-xl border transition-all duration-300 transform"
                                    x-show="jenisPegawai === 'Dosen'"
                                    style="display: none;">
                                <div class="flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <span>Data Dosen</span>
                                </div>
                            </button>
                            <button type="button"
                                    @click="activeTab = 'documents'; updateProgress()"
                                    :class="activeTab === 'documents' ? 'bg-white text-indigo-700 border-indigo-200 shadow-lg scale-105' : 'text-slate-600 hover:text-slate-800 hover:bg-white/50'"
                                    class="flex-1 px-4 py-3 text-sm font-medium rounded-xl border transition-all duration-300 transform">
                                <div class="flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span>Dokumen</span>
                                </div>
                            </button>
                        </nav>
                    </div>

                    {{-- Tab Content dengan Animasi --}}
                    <div class="p-4 lg:p-6 xl:p-8">
                        {{-- Personal Data Tab --}}
                        <div x-show="activeTab === 'personal'"
                                x-transition:enter="transition ease-out duration-500"
                                x-transition:enter-start="opacity-0 transform translate-y-8 scale-95"
                                x-transition:enter-end="opacity-100 transform translate-y-0 scale-100">
                            @include('backend.layouts.views.kepegawaian-universitas.data-pegawai.partials.personal-data')
                        </div>

                        {{-- Employment Data Tab --}}
                        <div x-show="activeTab === 'employment'"
                                x-transition:enter="transition ease-out duration-500"
                                x-transition:enter-start="opacity-0 transform translate-y-8 scale-95"
                                x-transition:enter-end="opacity-100 transform translate-y-0 scale-100">
                            @include('backend.layouts.views.kepegawaian-universitas.data-pegawai.partials.employment-data')
                        </div>

                        {{-- Dosen Data Tab --}}
                        <div x-show="activeTab === 'dosen'"
                                x-transition:enter="transition ease-out duration-500"
                                x-transition:enter-start="opacity-0 transform translate-y-8 scale-95"
                                x-transition:enter-end="opacity-100 transform translate-y-0 scale-100">
                            @include('backend.layouts.views.kepegawaian-universitas.data-pegawai.partials.dosen-data')
                        </div>

                        {{-- Documents Tab --}}
                        <div x-show="activeTab === 'documents'"
                                x-transition:enter="transition ease-out duration-500"
                                x-transition:enter-start="opacity-0 transform translate-y-8 scale-95"
                                x-transition:enter-end="opacity-100 transform translate-y-0 scale-100">
                            @include('backend.layouts.views.kepegawaian-universitas.data-pegawai.partials.documents')
                        </div>
                    </div>
                </div>

                {{-- Submit Button dengan Desain Modern --}}
                <div class="bg-white/90 backdrop-blur-xl p-4 lg:p-6 rounded-3xl shadow-xl border border-white/30 animate-fade-in-up">
                    <div class="flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center">
                        <div class="flex items-start gap-2 text-xs lg:text-sm text-slate-600 bg-slate-100/50 px-3 lg:px-4 py-2 rounded-xl flex-1 min-w-0">
                            <svg class="w-3 h-3 lg:w-4 lg:h-4 text-indigo-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="break-words">Pastikan semua data telah diisi dengan benar sebelum menyimpan</span>
                        </div>
                        <div class="flex gap-3">
                            <button type="button"
                                    onclick="window.history.back()"
                                    class="group px-6 py-3 text-slate-700 bg-white/80 backdrop-blur-sm border border-slate-200 rounded-xl shadow-lg hover:bg-slate-50 hover:shadow-xl hover:scale-105 transition-all duration-300">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span>Batal</span>
                                </div>
                            </button>
                            <button type="submit"
                                    :disabled="isLoading"
                                    class="group px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl shadow-lg hover:shadow-xl hover:from-indigo-700 hover:to-purple-700 hover:scale-105 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:scale-100">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 group-hover:rotate-12 transition-transform duration-300" x-show="!isLoading" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l3 3m0 0l-3 3m3-3H4"></path>
                                    </svg>
                                    <svg class="w-4 h-4 animate-spin" x-show="isLoading" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    <span x-text="isLoading ? 'Menyimpan...' : 'Simpan Data'"></span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Success Toast dengan Animasi Modern --}}
<div x-data="{ showSuccess: {{ session('success') ? 'true' : 'false' }} }"
     x-show="showSuccess"
     x-transition:enter="transition ease-out duration-500"
     x-transition:enter-start="opacity-0 transform translate-y-4 scale-95"
     x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
     x-transition:leave-end="opacity-0 transform translate-y-4 scale-95"
     x-init="if(showSuccess) { setTimeout(() => showSuccess = false, 5000) }"
     class="fixed bottom-6 right-6 bg-gradient-to-r from-green-500 to-emerald-500 text-white px-6 py-4 rounded-2xl shadow-2xl z-50 backdrop-blur-sm border border-white/20">
    <div class="flex items-center gap-3">
        <div class="p-1 bg-white/20 rounded-full">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <span class="font-medium">Data berhasil disimpan!</span>
        <button @click="showSuccess = false" class="ml-2 hover:bg-white/20 rounded-full p-1 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>

@endsection

@push('scripts')
    @vite(['resources/js/admin-universitas/data-pegawai.js'])
    @vite(['resources/js/admin-universitas/documents.js'])
    @vite(['resources/js/admin-universitas/dosen-data.js'])
    <script>
        // Pass data to JavaScript
        window.successMessage = @json(session('success'));
        @if(isset($pegawai))
            window.pegawaiId = {{ $pegawai->id }};
        @endif

        // ========================================
        // FOTO PREVIEW FUNCTION
        // ========================================
        function previewImage(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('foto-preview');
            const progressIndicator = document.getElementById('upload-progress');
            const statusIndicator = document.getElementById('upload-status');
            const progressBar = progressIndicator.querySelector('div');

            if (file) {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    // Show modern alert
                    showAlert('error', 'Format file tidak didukung! Gunakan JPG, JPEG, atau PNG.');
                    event.target.value = '';
                    return;
                }

                // Validate file size (2MB = 2048KB)
                if (file.size > 2048 * 1024) {
                    showAlert('error', 'Ukuran file terlalu besar! Maksimal 2MB.');
                    event.target.value = '';
                    return;
                }

                // Show loading status
                statusIndicator.classList.remove('hidden');
                statusIndicator.style.opacity = '1';
                progressIndicator.classList.remove('hidden');
                progressBar.style.width = '0%';

                // Create FileReader
                const reader = new FileReader();

                reader.onloadstart = function() {
                    progressBar.style.width = '20%';
                };

                reader.onprogress = function(e) {
                    if (e.lengthComputable) {
                        const percentLoaded = Math.round((e.loaded / e.total) * 100);
                        progressBar.style.width = percentLoaded + '%';
                    }
                };

                reader.onload = function(e) {
                    // Update progress to 100%
                    progressBar.style.width = '100%';

                    // Update preview image with animation
                    preview.style.opacity = '0.5';
                    preview.style.transform = 'scale(0.95)';

                    setTimeout(() => {
                        preview.src = e.target.result;
                        preview.style.opacity = '1';
                        preview.style.transform = 'scale(1)';

                        // Hide indicators
                        statusIndicator.style.opacity = '0';
                        setTimeout(() => {
                            statusIndicator.classList.add('hidden');
                            progressIndicator.classList.add('hidden');
                        }, 300);

                        // Show success message
                        showAlert('success', 'Foto berhasil dipilih dan akan disimpan saat form disubmit');
                        console.log('✅ Foto berhasil di-preview');
                    }, 300);
                };

                reader.onerror = function() {
                    showAlert('error', 'Gagal membaca file. Silakan coba lagi.');
                    statusIndicator.classList.add('hidden');
                    progressIndicator.classList.add('hidden');
                    event.target.value = '';
                };

                // Read file as data URL
                reader.readAsDataURL(file);

            } else {
                // Reset to default if no file selected
                preview.src = 'https://ui-avatars.com/api/?name={{ urlencode($pegawai->nama_lengkap ?? 'Pegawai') }}&background=6366f1&color=fff&size=160';
                statusIndicator.classList.add('hidden');
                progressIndicator.classList.add('hidden');
            }
        }

        // ========================================
        // MODERN ALERT SYSTEM
        // ========================================
        function showAlert(type, message) {
            // Remove existing alerts
            const existingAlert = document.querySelector('.modern-alert');
            if (existingAlert) {
                existingAlert.remove();
            }

            const alertColors = {
                success: 'from-green-500 to-emerald-500',
                error: 'from-red-500 to-pink-500',
                warning: 'from-yellow-500 to-orange-500',
                info: 'from-blue-500 to-indigo-500'
            };

            const alertIcons = {
                success: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>`,
                error: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>`,
                warning: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>`,
                info: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>`
            };

            const alert = document.createElement('div');
            alert.className = `modern-alert fixed top-6 right-6 bg-gradient-to-r ${alertColors[type]} text-white px-6 py-4 rounded-2xl shadow-2xl z-50 backdrop-blur-sm border border-white/20 transform translate-x-full transition-all duration-500`;
            alert.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="p-1 bg-white/20 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            ${alertIcons[type]}
                        </svg>
                    </div>
                    <span class="font-medium">${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-2 hover:bg-white/20 rounded-full p-1 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;

            document.body.appendChild(alert);

            // Show animation
            setTimeout(() => {
                alert.style.transform = 'translateX(0)';
            }, 100);

            // Auto hide after 5 seconds
            setTimeout(() => {
                alert.style.transform = 'translateX(full)';
                setTimeout(() => {
                    if (alert.parentElement) {
                        alert.remove();
                    }
                }, 500);
            }, 5000);
        }

        // ========================================
        // DRAG & DROP FUNCTIONALITY
        // ========================================
        document.addEventListener('DOMContentLoaded', function() {
            const photoContainer = document.querySelector('.relative.group');
            const fileInput = document.getElementById('foto');

            if (photoContainer && fileInput) {
                // Prevent default drag behaviors
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    photoContainer.addEventListener(eventName, preventDefaults, false);
                    document.body.addEventListener(eventName, preventDefaults, false);
                });

                // Highlight drop area when item is dragged over it
                ['dragenter', 'dragover'].forEach(eventName => {
                    photoContainer.addEventListener(eventName, highlight, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    photoContainer.addEventListener(eventName, unhighlight, false);
                });

                // Handle dropped files
                photoContainer.addEventListener('drop', handleDrop, false);

                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                function highlight(e) {
                    photoContainer.classList.add('ring-2', 'ring-indigo-500', 'ring-opacity-50');
                }

                function unhighlight(e) {
                    photoContainer.classList.remove('ring-2', 'ring-indigo-500', 'ring-opacity-50');
                }

                function handleDrop(e) {
                    const dt = e.dataTransfer;
                    const files = dt.files;

                    if (files.length > 0) {
                        fileInput.files = files;
                        previewImage({ target: { files: files } });
                    }
                }
            }
        });

        // ========================================
        // FORM VALIDATION ENHANCED
        // ========================================
        function updateProgress() {
            // Update form progress based on filled fields
            // This can be enhanced based on your specific requirements
            console.log('Progress updated');
        }
    </script>

    {{-- Upload Indicator Script --}}
    <script src="{{ asset('js/upload-indicator.js') }}"></script>
@endpush
