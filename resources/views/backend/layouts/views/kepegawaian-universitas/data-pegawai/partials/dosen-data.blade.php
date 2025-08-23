@push('scripts')
    @vite(['resources/js/admin-universitas/dosen-data.js'])
@endpush

{{-- Dosen Data Tab --}}
<div class="space-y-6 lg:space-y-8">
    {{-- Auto-save Indicator --}}
    <div id="auto-save-indicator" class="hidden text-xs text-slate-500 text-center py-2 bg-slate-50 rounded-lg border border-slate-200">
        <div class="flex items-center justify-center gap-2">
            <svg class="w-3 h-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Tersimpan otomatis</span>
        </div>
    </div>

    {{-- Dosen Specific Section --}}
    <div class="space-y-6 animate-fade-in-up">
        <div class="flex items-center gap-4 pb-6 border-b border-slate-200">
            <div class="p-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-800">Data Dosen</h3>
                <p class="text-slate-600">Informasi spesifik untuk dosen</p>
            </div>
        </div>

        {{-- Conditional Display for Dosen Only --}}
        <div x-show="jenisPegawai === 'Dosen'" x-transition>
            <div class="space-y-6">
                {{-- Mata Kuliah yang Diampu --}}
                <div class="group">
                    <label for="mata_kuliah_diampu" class="block text-sm lg:text-base font-semibold text-slate-700 mb-2 lg:mb-3 group-hover:text-blue-600 transition-colors">
                        <div class="flex items-center gap-2">
                            <svg class="w-3 h-3 lg:w-4 lg:h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <span class="break-words">Mata Kuliah yang Diampu <span class="text-red-500">*</span></span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 font-normal">Daftar mata kuliah yang diampu oleh dosen</p>
                    </label>
                    <div class="relative">
                        <textarea name="mata_kuliah_diampu"
                                  id="mata_kuliah_diampu"
                                  rows="4"
                                  class="w-full px-3 lg:px-4 py-3 lg:py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 placeholder-slate-400 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md resize-none text-sm lg:text-base"
                                  placeholder="Contoh: Pemrograman Web, Basis Data, Algoritma dan Struktur Data, Sistem Informasi, Pengembangan Aplikasi Web">{{ old('mata_kuliah_diampu', $pegawai->mata_kuliah_diampu ?? '') }}</textarea>
                        <div class="absolute top-3 lg:top-4 right-3 lg:right-4 pointer-events-none">
                            <svg class="w-4 h-4 lg:w-5 lg:h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                    </div>
                    {{-- Character counter and progress bar will be added by JavaScript --}}
                </div>

                {{-- Ranting Ilmu / Kepakaran --}}
                <div class="group">
                    <label for="ranting_ilmu_kepakaran" class="block text-sm lg:text-base font-semibold text-slate-700 mb-2 lg:mb-3 group-hover:text-indigo-600 transition-colors">
                        <div class="flex items-center gap-2">
                            <svg class="w-3 h-3 lg:w-4 lg:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                            <span class="break-words">Ranting Ilmu / Kepakaran <span class="text-red-500">*</span></span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 font-normal">Bidang keahlian dan kepakaran dosen</p>
                    </label>
                    <div class="relative">
                        <textarea name="ranting_ilmu_kepakaran"
                                  id="ranting_ilmu_kepakaran"
                                  rows="3"
                                  class="w-full px-3 lg:px-4 py-3 lg:py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 placeholder-slate-400 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md resize-none text-sm lg:text-base"
                                  placeholder="Contoh: Teknologi Informasi, Sistem Informasi, Pengembangan Perangkat Lunak, Database Management">{{ old('ranting_ilmu_kepakaran', $pegawai->ranting_ilmu_kepakaran ?? '') }}</textarea>
                        <div class="absolute top-3 lg:top-4 right-3 lg:right-4 pointer-events-none">
                            <svg class="w-4 h-4 lg:w-5 lg:h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                    </div>
                    {{-- Character counter and progress bar will be added by JavaScript --}}
                </div>

                {{-- URL Profil Sinta --}}
                <div class="xl:col-span-2 group">
                    <label for="url_profil_sinta" class="block text-sm lg:text-base font-semibold text-slate-700 mb-2 lg:mb-3 group-hover:text-indigo-600 transition-colors">
                        <div class="flex items-center gap-2">
                            <svg class="w-3 h-3 lg:w-4 lg:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                            <span class="break-words">URL Profil Akun Sinta <span class="text-red-500">*</span></span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 font-normal">Link ke profil Sinta Kemdikbud untuk verifikasi publikasi</p>
                    </label>
                    <div class="relative">
                        <input type="url"
                               name="url_profil_sinta"
                               id="url_profil_sinta"
                               value="{{ old('url_profil_sinta', $pegawai->url_profil_sinta ?? '') }}"
                               class="w-full px-3 lg:px-4 py-3 lg:py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 placeholder-slate-400 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md text-sm lg:text-base"
                               placeholder="https://sinta.kemdikbud.go.id/authors/profile/...">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 lg:pr-4 pointer-events-none">
                            <svg class="w-4 h-4 lg:w-5 lg:h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </div>
                    </div>
                    {{-- URL feedback will be added by JavaScript --}}
                </div>

                {{-- Additional Dosen Information Section --}}
                <div class="mt-8 p-4 lg:p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border border-blue-200">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-blue-800">Informasi Tambahan</h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div class="flex items-center gap-2 text-blue-700">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Data akan tersimpan otomatis saat Anda mengetik</span>
                        </div>
                        <div class="flex items-center gap-2 text-blue-700">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>URL Sinta akan divalidasi secara otomatis</span>
                        </div>
                        <div class="flex items-center gap-2 text-blue-700">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Progress pengisian ditampilkan secara real-time</span>
                        </div>
                        <div class="flex items-center gap-2 text-blue-700">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Semua data akan divalidasi sebelum disimpan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Message for Non-Dosen --}}
        <div x-show="jenisPegawai !== 'Dosen'" x-transition class="text-center py-12">
            <div class="max-w-md mx-auto">
                <div class="p-4 bg-slate-100 rounded-2xl mb-4">
                    <svg class="w-12 h-12 text-slate-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-700 mb-2">Data Dosen Tidak Diperlukan</h3>
                <p class="text-slate-600 text-sm">Tab ini hanya untuk pegawai dengan jenis "Dosen". Silakan pilih "Dosen" pada jenis pegawai untuk mengisi data ini.</p>
            </div>
        </div>
    </div>
</div>


