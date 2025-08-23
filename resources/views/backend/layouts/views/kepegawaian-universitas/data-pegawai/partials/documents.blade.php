@push('scripts')
    @vite(['resources/js/admin-universitas/documents.js'])
@endpush

{{-- Documents Tab --}}
<div class="space-y-6 lg:space-y-8">
    {{-- Education Documents Section --}}
    <div class="space-y-6 animate-fade-in-up">
        <div class="flex items-center gap-4 pb-6 border-b border-slate-200">
            <div class="p-3 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl shadow-lg">
                <i data-lucide="graduation-cap" class="w-6 h-6 text-white"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-800">Dokumen Pendidikan</h3>
                <p class="text-slate-600">Ijazah, transkrip, dan dokumen pendidikan terkait</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 lg:gap-6">
            {{-- Ijazah Terakhir --}}
            <div class="group">
                <label for="ijazah_terakhir" class="block text-sm lg:text-base font-semibold text-slate-700 mb-2 lg:mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <i data-lucide="file-text" class="w-3 h-3 lg:w-4 lg:h-4 text-indigo-600 flex-shrink-0"></i>
                        <span class="break-words">Ijazah Terakhir <span class="text-red-500">*</span></span>
                    </div>
                </label>
                <div class="relative">
                    <label class="flex flex-col items-center justify-center w-full h-32 lg:h-36 border-2 {{ isset($pegawai) && $pegawai->ijazah_terakhir ? 'border-green-400 bg-green-50' : 'border-slate-300 bg-slate-50' }} border-dashed rounded-2xl cursor-pointer hover:bg-slate-100 hover:border-indigo-400 transition-all duration-300 group/upload">
                        <div class="flex flex-col items-center justify-center pt-4 lg:pt-5 pb-4 lg:pb-6 text-center w-full px-2 overflow-hidden">
                            <i data-lucide="{{ isset($pegawai) && $pegawai->ijazah_terakhir ? 'file-check' : 'upload-cloud' }}" class="w-8 h-8 lg:w-10 lg:h-10 mb-2 lg:mb-3 {{ isset($pegawai) && $pegawai->ijazah_terakhir ? 'text-green-600' : 'text-slate-500' }} group-hover/upload:scale-110 transition-transform duration-300"></i>
                            <p class="mb-2 text-xs lg:text-sm {{ isset($pegawai) && $pegawai->ijazah_terakhir ? 'text-green-800' : 'text-slate-500' }} w-full">
                                <span class="font-semibold file-name-display block truncate">{{ isset($pegawai) && $pegawai->ijazah_terakhir ? 'File sudah ada' : 'Klik untuk unggah' }}</span>
                            </p>
                            <p class="text-xs text-slate-400">PDF, maksimal 2MB</p>
                        </div>
                        <input id="ijazah_terakhir" name="ijazah_terakhir" type="file" class="hidden" accept=".pdf" data-preview="ijazah_terakhir_preview" data-max-size="2" onchange="handleFileUpload(this)" />
                    </label>
                    @if(isset($pegawai) && $pegawai->ijazah_terakhir)
                    <div class="mt-3 text-center">
                        <a href="{{ route('backend.kepegawaian-universitas.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'ijazah_terakhir']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline transition-colors bg-indigo-50 px-3 py-2 rounded-lg hover:bg-indigo-100">
                            <i data-lucide="eye" class="w-3 h-3"></i>Lihat File Saat Ini
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Transkrip Nilai --}}
            <div class="group">
                <label for="transkrip_nilai_terakhir" class="block text-sm lg:text-base font-semibold text-slate-700 mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <i data-lucide="file-text" class="w-4 h-4 text-indigo-600"></i>
                        Transkrip Nilai <span class="text-red-500">*</span>
                    </div>
                </label>
                <div class="relative">
                    <label class="flex flex-col items-center justify-center w-full h-36 border-2 {{ isset($pegawai) && $pegawai->transkrip_nilai_terakhir ? 'border-green-400 bg-green-50' : 'border-slate-300 bg-slate-50' }} border-dashed rounded-2xl cursor-pointer hover:bg-slate-100 hover:border-indigo-400 transition-all duration-300 group/upload">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center w-full px-2 overflow-hidden">
                            <i data-lucide="{{ isset($pegawai) && $pegawai->transkrip_nilai_terakhir ? 'file-check' : 'upload-cloud' }}" class="w-10 h-10 mb-3 {{ isset($pegawai) && $pegawai->transkrip_nilai_terakhir ? 'text-green-600' : 'text-slate-500' }} group-hover/upload:scale-110 transition-transform duration-300"></i>
                            <p class="mb-2 text-sm {{ isset($pegawai) && $pegawai->transkrip_nilai_terakhir ? 'text-green-800' : 'text-slate-500' }} w-full">
                                <span class="font-semibold file-name-display block truncate">{{ isset($pegawai) && $pegawai->transkrip_nilai_terakhir ? 'File sudah ada' : 'Klik untuk unggah' }}</span>
                            </p>
                            <p class="text-xs text-slate-400">PDF, maksimal 2MB</p>
                        </div>
                        <input id="transkrip_nilai_terakhir" name="transkrip_nilai_terakhir" type="file" class="hidden" accept=".pdf" data-preview="transkrip_nilai_terakhir_preview" data-max-size="2" onchange="handleFileUpload(this)" />
                    </label>
                    @if(isset($pegawai) && $pegawai->transkrip_nilai_terakhir)
                    <div class="mt-3 text-center">
                        <a href="{{ route('backend.kepegawaian-universitas.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'transkrip_nilai_terakhir']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline transition-colors bg-indigo-50 px-3 py-2 rounded-lg hover:bg-indigo-100">
                            <i data-lucide="eye" class="w-3 h-3"></i>Lihat File Saat Ini
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- SK Penyetaraan Ijazah --}}
            <div class="group">
                <label for="sk_penyetaraan_ijazah" class="block text-sm lg:text-base font-semibold text-slate-700 mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <i data-lucide="file-text" class="w-4 h-4 text-indigo-600"></i>
                        SK Penyetaraan Ijazah
                    </div>
                </label>
                <div class="relative">
                    <label class="flex flex-col items-center justify-center w-full h-36 border-2 {{ isset($pegawai) && $pegawai->sk_penyetaraan_ijazah ? 'border-green-400 bg-green-50' : 'border-slate-300 bg-slate-50' }} border-dashed rounded-2xl cursor-pointer hover:bg-slate-100 hover:border-indigo-400 transition-all duration-300 group/upload">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center w-full px-2 overflow-hidden">
                            <i data-lucide="{{ isset($pegawai) && $pegawai->sk_penyetaraan_ijazah ? 'file-check' : 'upload-cloud' }}" class="w-10 h-10 mb-3 {{ isset($pegawai) && $pegawai->sk_penyetaraan_ijazah ? 'text-green-600' : 'text-slate-500' }} group-hover/upload:scale-110 transition-transform duration-300"></i>
                            <p class="mb-2 text-sm {{ isset($pegawai) && $pegawai->sk_penyetaraan_ijazah ? 'text-green-800' : 'text-slate-500' }} w-full">
                                <span class="font-semibold file-name-display block truncate">{{ isset($pegawai) && $pegawai->sk_penyetaraan_ijazah ? 'File sudah ada' : 'Klik untuk unggah' }}</span>
                            </p>
                            <p class="text-xs text-slate-400">PDF, maksimal 2MB</p>
                        </div>
                        <input id="sk_penyetaraan_ijazah" name="sk_penyetaraan_ijazah" type="file" class="hidden" accept=".pdf" data-preview="sk_penyetaraan_ijazah_preview" data-max-size="2" onchange="handleFileUpload(this)" />
                    </label>
                    @if(isset($pegawai) && $pegawai->sk_penyetaraan_ijazah)
                    <div class="mt-3 text-center">
                        <a href="{{ route('backend.kepegawaian-universitas.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'sk_penyetaraan_ijazah']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline transition-colors bg-indigo-50 px-3 py-2 rounded-lg hover:bg-indigo-100">
                            <i data-lucide="eye" class="w-3 h-3"></i>Lihat File Saat Ini
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Disertasi/Thesis --}}
            <div class="group">
                <label for="disertasi_thesis_terakhir" class="block text-sm lg:text-base font-semibold text-slate-700 mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <i data-lucide="file-text" class="w-4 h-4 text-indigo-600"></i>
                        Disertasi/Thesis
                    </div>
                </label>
                <div class="relative">
                    <label class="flex flex-col items-center justify-center w-full h-36 border-2 {{ isset($pegawai) && $pegawai->disertasi_thesis_terakhir ? 'border-green-400 bg-green-50' : 'border-slate-300 bg-slate-50' }} border-dashed rounded-2xl cursor-pointer hover:bg-slate-100 hover:border-indigo-400 transition-all duration-300 group/upload">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center w-full px-2 overflow-hidden">
                            <i data-lucide="{{ isset($pegawai) && $pegawai->disertasi_thesis_terakhir ? 'file-check' : 'upload-cloud' }}" class="w-10 h-10 mb-3 {{ isset($pegawai) && $pegawai->disertasi_thesis_terakhir ? 'text-green-600' : 'text-slate-500' }} group-hover/upload:scale-110 transition-transform duration-300"></i>
                            <p class="mb-2 text-sm {{ isset($pegawai) && $pegawai->disertasi_thesis_terakhir ? 'text-green-800' : 'text-slate-500' }} w-full">
                                <span class="font-semibold file-name-display block truncate">{{ isset($pegawai) && $pegawai->disertasi_thesis_terakhir ? 'File sudah ada' : 'Klik untuk unggah' }}</span>
                            </p>
                            <p class="text-xs text-slate-400">PDF, maksimal 10MB</p>
                        </div>
                        <input id="disertasi_thesis_terakhir" name="disertasi_thesis_terakhir" type="file" class="hidden" accept=".pdf" data-preview="disertasi_thesis_terakhir_preview" data-max-size="10" onchange="handleFileUpload(this)" />
                    </label>
                    @if(isset($pegawai) && $pegawai->disertasi_thesis_terakhir)
                    <div class="mt-3 text-center">
                        <a href="{{ route('backend.kepegawaian-universitas.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'disertasi_thesis_terakhir']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline transition-colors bg-indigo-50 px-3 py-2 rounded-lg hover:bg-indigo-100">
                            <i data-lucide="eye" class="w-3 h-3"></i>Lihat File Saat Ini
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Employment Documents Section --}}
    <div class="space-y-6 animate-fade-in-up">
        <div class="flex items-center gap-4 pb-6 border-b border-slate-200">
            <div class="p-3 bg-gradient-to-r from-blue-500 to-cyan-600 rounded-2xl shadow-lg">
                <i data-lucide="file-text" class="w-6 h-6 text-white"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-800">Dokumen Kepegawaian</h3>
                <p class="text-slate-600">SK CPNS, PNS, pangkat, dan jabatan</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- SK CPNS --}}
            <div class="group">
                <label for="sk_cpns" class="block text-sm lg:text-base font-semibold text-slate-700 mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <i data-lucide="file-text" class="w-4 h-4 text-indigo-600"></i>
                        SK CPNS <span class="text-red-500">*</span>
                    </div>
                </label>
                <div class="relative">
                    <label class="flex flex-col items-center justify-center w-full h-36 border-2 {{ isset($pegawai) && $pegawai->sk_cpns ? 'border-green-400 bg-green-50' : 'border-slate-300 bg-slate-50' }} border-dashed rounded-2xl cursor-pointer hover:bg-slate-100 hover:border-indigo-400 transition-all duration-300 group/upload">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center w-full px-2 overflow-hidden">
                            <i data-lucide="{{ isset($pegawai) && $pegawai->sk_cpns ? 'file-check' : 'upload-cloud' }}" class="w-10 h-10 mb-3 {{ isset($pegawai) && $pegawai->sk_cpns ? 'text-green-600' : 'text-slate-500' }} group-hover/upload:scale-110 transition-transform duration-300"></i>
                            <p class="mb-2 text-sm {{ isset($pegawai) && $pegawai->sk_cpns ? 'text-green-800' : 'text-slate-500' }} w-full">
                                <span class="font-semibold file-name-display block truncate">{{ isset($pegawai) && $pegawai->sk_cpns ? 'File sudah ada' : 'Klik untuk unggah' }}</span>
                            </p>
                            <p class="text-xs text-slate-400">PDF, maksimal 2MB</p>
                        </div>
                        <input id="sk_cpns" name="sk_cpns" type="file" class="hidden" accept=".pdf" data-preview="sk_cpns_preview" data-max-size="2" onchange="handleFileUpload(this)" />
                    </label>
                    @if(isset($pegawai) && $pegawai->sk_cpns)
                    <div class="mt-3 text-center">
                        <a href="{{ route('backend.kepegawaian-universitas.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'sk_cpns']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline transition-colors bg-indigo-50 px-3 py-2 rounded-lg hover:bg-indigo-100">
                            <i data-lucide="eye" class="w-3 h-3"></i>Lihat File Saat Ini
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- SK PNS --}}
            <div class="group">
                <label for="sk_pns" class="block text-sm lg:text-base font-semibold text-slate-700 mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <i data-lucide="file-text" class="w-4 h-4 text-indigo-600"></i>
                        SK PNS <span class="text-red-500">*</span>
                    </div>
                </label>
                <div class="relative">
                    <label class="flex flex-col items-center justify-center w-full h-36 border-2 {{ isset($pegawai) && $pegawai->sk_pns ? 'border-green-400 bg-green-50' : 'border-slate-300 bg-slate-50' }} border-dashed rounded-2xl cursor-pointer hover:bg-slate-100 hover:border-indigo-400 transition-all duration-300 group/upload">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center w-full px-2 overflow-hidden">
                            <i data-lucide="{{ isset($pegawai) && $pegawai->sk_pns ? 'file-check' : 'upload-cloud' }}" class="w-10 h-10 mb-3 {{ isset($pegawai) && $pegawai->sk_pns ? 'text-green-600' : 'text-slate-500' }} group-hover/upload:scale-110 transition-transform duration-300"></i>
                            <p class="mb-2 text-sm {{ isset($pegawai) && $pegawai->sk_pns ? 'text-green-800' : 'text-slate-500' }} w-full">
                                <span class="font-semibold file-name-display block truncate">{{ isset($pegawai) && $pegawai->sk_pns ? 'File sudah ada' : 'Klik untuk unggah' }}</span>
                            </p>
                            <p class="text-xs text-slate-400">PDF, maksimal 2MB</p>
                        </div>
                        <input id="sk_pns" name="sk_pns" type="file" class="hidden" accept=".pdf" data-preview="sk_pns_preview" data-max-size="2" onchange="handleFileUpload(this)" />
                    </label>
                    @if(isset($pegawai) && $pegawai->sk_pns)
                    <div class="mt-3 text-center">
                        <a href="{{ route('backend.kepegawaian-universitas.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'sk_pns']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline transition-colors bg-indigo-50 px-3 py-2 rounded-lg hover:bg-indigo-100">
                            <i data-lucide="eye" class="w-3 h-3"></i>Lihat File Saat Ini
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- SK Pangkat Terakhir --}}
            <div class="group">
                <label for="sk_pangkat_terakhir" class="block text-sm lg:text-base font-semibold text-slate-700 mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <i data-lucide="file-text" class="w-4 h-4 text-indigo-600"></i>
                        SK Pangkat Terakhir <span class="text-red-500">*</span>
                    </div>
                </label>
                <div class="relative">
                    <label class="flex flex-col items-center justify-center w-full h-36 border-2 {{ isset($pegawai) && $pegawai->sk_pangkat_terakhir ? 'border-green-400 bg-green-50' : 'border-slate-300 bg-slate-50' }} border-dashed rounded-2xl cursor-pointer hover:bg-slate-100 hover:border-indigo-400 transition-all duration-300 group/upload">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center w-full px-2 overflow-hidden">
                            <i data-lucide="{{ isset($pegawai) && $pegawai->sk_pangkat_terakhir ? 'file-check' : 'upload-cloud' }}" class="w-10 h-10 mb-3 {{ isset($pegawai) && $pegawai->sk_pangkat_terakhir ? 'text-green-600' : 'text-slate-500' }} group-hover/upload:scale-110 transition-transform duration-300"></i>
                            <p class="mb-2 text-sm {{ isset($pegawai) && $pegawai->sk_pangkat_terakhir ? 'text-green-800' : 'text-slate-500' }} w-full">
                                <span class="font-semibold file-name-display block truncate">{{ isset($pegawai) && $pegawai->sk_pangkat_terakhir ? 'File sudah ada' : 'Klik untuk unggah' }}</span>
                            </p>
                            <p class="text-xs text-slate-400">PDF, maksimal 2MB</p>
                        </div>
                        <input id="sk_pangkat_terakhir" name="sk_pangkat_terakhir" type="file" class="hidden" accept=".pdf" data-preview="sk_pangkat_terakhir_preview" data-max-size="2" onchange="handleFileUpload(this)" />
                    </label>
                    @if(isset($pegawai) && $pegawai->sk_pangkat_terakhir)
                    <div class="mt-3 text-center">
                        <a href="{{ route('backend.kepegawaian-universitas.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'sk_pangkat_terakhir']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline transition-colors bg-indigo-50 px-3 py-2 rounded-lg hover:bg-indigo-100">
                            <i data-lucide="eye" class="w-3 h-3"></i>Lihat File Saat Ini
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- SK Jabatan Terakhir --}}
            <div class="group">
                <label for="sk_jabatan_terakhir" class="block text-sm lg:text-base font-semibold text-slate-700 mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        SK Jabatan Terakhir <span class="text-red-500">*</span>
                    </div>
                </label>
                <div class="relative">
                    <label class="flex flex-col items-center justify-center w-full h-36 border-2 {{ isset($pegawai) && $pegawai->sk_jabatan_terakhir ? 'border-green-400 bg-green-50' : 'border-slate-300 bg-slate-50' }} border-dashed rounded-2xl cursor-pointer hover:bg-slate-100 hover:border-indigo-400 transition-all duration-300 group/upload">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center w-full px-2 overflow-hidden">
                            <i data-lucide="{{ isset($pegawai) && $pegawai->sk_jabatan_terakhir ? 'file-check' : 'upload-cloud' }}" class="w-10 h-10 mb-3 {{ isset($pegawai) && $pegawai->sk_jabatan_terakhir ? 'text-green-600' : 'text-slate-500' }} group-hover/upload:scale-110 transition-transform duration-300"></i>
                            <p class="mb-2 text-sm {{ isset($pegawai) && $pegawai->sk_jabatan_terakhir ? 'text-green-800' : 'text-slate-500' }} w-full">
                                <span class="font-semibold file-name-display block truncate">{{ isset($pegawai) && $pegawai->sk_jabatan_terakhir ? 'File sudah ada' : 'Klik untuk unggah' }}</span>
                            </p>
                            <p class="text-xs text-slate-400">PDF, maksimal 2MB</p>
                        </div>
                        <input id="sk_jabatan_terakhir" name="sk_jabatan_terakhir" type="file" class="hidden" accept=".pdf" data-preview="sk_jabatan_terakhir_preview" data-max-size="2" onchange="handleFileUpload(this)" />
                    </label>
                    @if(isset($pegawai) && $pegawai->sk_jabatan_terakhir)
                    <div class="mt-3 text-center">
                        <a href="{{ route('backend.kepegawaian-universitas.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'sk_jabatan_terakhir']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline transition-colors bg-indigo-50 px-3 py-2 rounded-lg hover:bg-indigo-100">
                            <i data-lucide="eye" class="w-3 h-3"></i>Lihat File Saat Ini
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Performance Documents Section --}}
    <div class="space-y-6 animate-fade-in-up">
        <div class="flex items-center gap-4 pb-6 border-b border-slate-200">
            <div class="p-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.7 8l-5.1 5.2-2.8-2.7L7 14.3"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-800">Dokumen Kinerja</h3>
                <p class="text-slate-600">SKP dan dokumen kinerja terkait</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- SKP Tahun Pertama --}}
            <div class="group">
                <label for="skp_tahun_pertama" class="block text-sm lg:text-base font-semibold text-slate-700 mb-3 group-hover:text-blue-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        SKP Tahun Pertama <span class="text-red-500">*</span>
                    </div>
                </label>
                <div class="relative">
                                            <label class="flex flex-col items-center justify-center w-full h-36 border-2 {{ isset($pegawai) && $pegawai->skp_tahun_pertama ? 'border-green-400 bg-green-50' : 'border-slate-300 bg-slate-50' }} border-dashed rounded-2xl cursor-pointer hover:bg-slate-100 hover:border-blue-400 transition-all duration-300 group/upload">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center w-full px-2 overflow-hidden">
                            <i data-lucide="{{ isset($pegawai) && $pegawai->skp_tahun_pertama ? 'file-check' : 'upload-cloud' }}" class="w-10 h-10 mb-3 {{ isset($pegawai) && $pegawai->skp_tahun_pertama ? 'text-green-600' : 'text-slate-500' }} group-hover/upload:scale-110 transition-transform duration-300"></i>
                            <p class="mb-2 text-sm {{ isset($pegawai) && $pegawai->skp_tahun_pertama ? 'text-green-800' : 'text-slate-500' }} w-full">
                                <span class="font-semibold file-name-display block truncate">{{ isset($pegawai) && $pegawai->skp_tahun_pertama ? 'File sudah ada' : 'Klik untuk unggah' }}</span>
                            </p>
                            <p class="text-xs text-slate-400">PDF, maksimal 2MB</p>
                        </div>
                        <input id="skp_tahun_pertama" name="skp_tahun_pertama" type="file" class="hidden" accept=".pdf" data-preview="skp_tahun_pertama_preview" data-max-size="2" onchange="handleFileUpload(this)" />
                    </label>
                    @if(isset($pegawai) && $pegawai->skp_tahun_pertama)
                    <div class="mt-3 text-center">
                        <a href="{{ route('backend.kepegawaian-universitas.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'skp_tahun_pertama']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline transition-colors bg-blue-50 px-3 py-2 rounded-lg hover:bg-blue-100">
                            <i data-lucide="eye" class="w-3 h-3"></i>Lihat File Saat Ini
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- SKP Tahun Kedua --}}
            <div class="group">
                <label for="skp_tahun_kedua" class="block text-sm lg:text-base font-semibold text-slate-700 mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        SKP Tahun Kedua <span class="text-red-500">*</span>
                    </div>
                </label>
                <div class="relative">
                    <label class="flex flex-col items-center justify-center w-full h-36 border-2 {{ isset($pegawai) && $pegawai->skp_tahun_kedua ? 'border-green-400 bg-green-50' : 'border-slate-300 bg-slate-50' }} border-dashed rounded-2xl cursor-pointer hover:bg-slate-100 hover:border-indigo-400 transition-all duration-300 group/upload">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center w-full px-2 overflow-hidden">
                            <i data-lucide="{{ isset($pegawai) && $pegawai->skp_tahun_kedua ? 'file-check' : 'upload-cloud' }}" class="w-10 h-10 mb-3 {{ isset($pegawai) && $pegawai->skp_tahun_kedua ? 'text-green-600' : 'text-slate-500' }} group-hover/upload:scale-110 transition-transform duration-300"></i>
                            <p class="mb-2 text-sm {{ isset($pegawai) && $pegawai->skp_tahun_kedua ? 'text-green-800' : 'text-slate-500' }} w-full">
                                <span class="font-semibold file-name-display block truncate">{{ isset($pegawai) && $pegawai->skp_tahun_kedua ? 'File sudah ada' : 'Klik untuk unggah' }}</span>
                            </p>
                            <p class="text-xs text-slate-400">PDF, maksimal 2MB</p>
                        </div>
                        <input id="skp_tahun_kedua" name="skp_tahun_kedua" type="file" class="hidden" accept=".pdf" data-preview="skp_tahun_kedua_preview" data-max-size="2" onchange="handleFileUpload(this)" />
                    </label>
                    @if(isset($pegawai) && $pegawai->skp_tahun_kedua)
                    <div class="mt-3 text-center">
                        <a href="{{ route('backend.kepegawaian-universitas.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'skp_tahun_kedua']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline transition-colors bg-indigo-50 px-3 py-2 rounded-lg hover:bg-indigo-100">
                            <i data-lucide="eye" class="w-3 h-3"></i>Lihat File Saat Ini
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- PAK Konversi (Conditional) --}}
            <div id="field_pak_konversi" class="hidden group">
                <label for="pak_konversi" class="block text-sm lg:text-base font-semibold text-slate-700 mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        PAK Konversi
                    </div>
                </label>
                <div class="relative">
                    <label class="flex flex-col items-center justify-center w-full h-36 border-2 {{ isset($pegawai) && $pegawai->pak_konversi ? 'border-green-400 bg-green-50' : 'border-slate-300 bg-slate-50' }} border-dashed rounded-2xl cursor-pointer hover:bg-slate-100 hover:border-indigo-400 transition-all duration-300 group/upload">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center w-full px-2 overflow-hidden">
                            <i data-lucide="{{ isset($pegawai) && $pegawai->pak_konversi ? 'file-check' : 'upload-cloud' }}" class="w-10 h-10 mb-3 {{ isset($pegawai) && $pegawai->pak_konversi ? 'text-green-600' : 'text-slate-500' }} group-hover/upload:scale-110 transition-transform duration-300"></i>
                            <p class="mb-2 text-sm {{ isset($pegawai) && $pegawai->pak_konversi ? 'text-green-800' : 'text-slate-500' }} w-full">
                                <span class="font-semibold file-name-display block truncate">{{ isset($pegawai) && $pegawai->pak_konversi ? 'File sudah ada' : 'Klik untuk unggah' }}</span>
                            </p>
                            <p class="text-xs text-slate-400">PDF, maksimal 2MB</p>
                        </div>
                        <input id="pak_konversi" name="pak_konversi" type="file" class="hidden" accept=".pdf" data-preview="pak_konversi_preview" data-max-size="2" onchange="handleFileUpload(this)" />
                    </label>
                    @if(isset($pegawai) && $pegawai->pak_konversi)
                    <div class="mt-3 text-center">
                        <a href="{{ route('backend.kepegawaian-universitas.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'pak_konversi']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline transition-colors bg-indigo-50 px-3 py-2 rounded-lg hover:bg-indigo-100">
                            <i data-lucide="eye" class="w-3 h-3"></i>Lihat File Saat Ini
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom animations for document upload areas */
    .group:hover label[for*="_terakhir"],
    .group:hover label[for*="_cpns"],
    .group:hover label[for*="_pns"],
    .group:hover label[for*="_pangkat"],
    .group:hover label[for*="_jabatan"],
    .group:hover label[for*="_tahun"],
    .group:hover label[for*="_konversi"] {
        transform: scale(1.02);
        box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.1);
    }

    /* Smooth transitions for all upload elements */
    label[for*="_terakhir"],
    label[for*="_cpns"],
    label[for*="_pns"],
    label[for*="_pangkat"],
    label[for*="_jabatan"],
    label[for*="_tahun"],
    label[for*="_konversi"] {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Hover effects for labels */
    .group:hover label {
        transform: translateX(5px);
    }

    /* Animation for file name display */
    .file-name-display {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Custom styling for upload areas */
    .group/upload:hover {
        transform: scale(1.02);
        box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.15);
    }

    /* Animation for conditional fields */
    #field_pak_konversi {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>


