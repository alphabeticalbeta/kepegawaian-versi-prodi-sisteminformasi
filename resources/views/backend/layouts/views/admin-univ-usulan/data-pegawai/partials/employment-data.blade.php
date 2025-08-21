{{-- Employment Data Tab --}}
<div class="space-y-6 lg:space-y-8">
        {{-- Jenis Pegawai & Jabatan Section --}}
    <div class="space-y-6 animate-fade-in-up">
        <div class="flex items-center gap-4 pb-6 border-b border-slate-200">
            <div class="p-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-800">Jenis Pegawai & Jabatan</h3>
                <p class="text-slate-600">Kategori pegawai dan jenis jabatan</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 lg:gap-6">
            {{-- Jenis Pegawai --}}
            <div class="group">
                <label for="jenis_pegawai" class="block text-sm lg:text-base font-semibold text-slate-700 mb-2 lg:mb-3 group-hover:text-blue-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-3 h-3 lg:w-4 lg:h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="break-words">Jenis Pegawai <span class="text-red-500">*</span></span>
                    </div>
                </label>
                <div class="relative">
                    <select name="jenis_pegawai"
                            id="jenis_pegawai"
                            class="w-full px-3 lg:px-4 py-3 lg:py-4 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-200/50 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md appearance-none text-sm lg:text-base">
                        <option value="">Pilih Jenis Pegawai</option>
                        <option value="Dosen" {{ old('jenis_pegawai', $pegawai->jenis_pegawai ?? '') == 'Dosen' ? 'selected' : '' }}>Dosen</option>
                        <option value="Tenaga Kependidikan" {{ old('jenis_pegawai', $pegawai->jenis_pegawai ?? '') == 'Tenaga Kependidikan' ? 'selected' : '' }}>Tenaga Kependidikan</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 lg:pr-4 pointer-events-none">
                        <svg class="w-4 h-4 lg:w-5 lg:h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Jenis Jabatan --}}
            <div class="group">
                <label for="jenis_jabatan" class="block text-sm lg:text-base font-semibold text-slate-700 mb-2 lg:mb-3 group-hover:text-blue-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-3 h-3 lg:w-4 lg:h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                        </svg>
                        <span class="break-words">Jenis Jabatan <span class="text-red-500">*</span></span>
                    </div>
                </label>
                <div class="relative">
                    <div class="filter-status" id="jenis-jabatan-filter-status">Filter Active</div>
                    <select name="jenis_jabatan"
                            id="jenis_jabatan"
                            class="w-full px-3 lg:px-4 py-3 lg:py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md appearance-none text-sm lg:text-base">
                        <option value="">Pilih Jenis Jabatan</option>
                        {{-- Opsi untuk Dosen --}}
                        <option value="Dosen Fungsional" data-jenis-pegawai="Dosen" {{ old('jenis_jabatan', $pegawai->jenis_jabatan ?? '') == 'Dosen Fungsional' ? 'selected' : '' }}>Dosen Fungsional</option>
                        <option value="Dosen dengan Tugas Tambahan" data-jenis-pegawai="Dosen" {{ old('jenis_jabatan', $pegawai->jenis_jabatan ?? '') == 'Dosen dengan Tugas Tambahan' ? 'selected' : '' }}>Dosen dengan Tugas Tambahan</option>
                        {{-- Opsi untuk Tenaga Kependidikan --}}
                        <option value="Tenaga Kependidikan Fungsional Umum" data-jenis-pegawai="Tenaga Kependidikan" {{ old('jenis_jabatan', $pegawai->jenis_jabatan ?? '') == 'Tenaga Kependidikan Fungsional Umum' ? 'selected' : '' }}>Tenaga Kependidikan Fungsional Umum</option>
                        <option value="Tenaga Kependidikan Fungsional Tertentu" data-jenis-pegawai="Tenaga Kependidikan" {{ old('jenis_jabatan', $pegawai->jenis_jabatan ?? '') == 'Tenaga Kependidikan Fungsional Tertentu' ? 'selected' : '' }}>Tenaga Kependidikan Fungsional Tertentu</option>
                        <option value="Tenaga Kependidikan Struktural" data-jenis-pegawai="Tenaga Kependidikan" {{ old('jenis_jabatan', $pegawai->jenis_jabatan ?? '') == 'Tenaga Kependidikan Struktural' ? 'selected' : '' }}>Tenaga Kependidikan Struktural</option>
                        <option value="Tenaga Kependidikan Tugas Tambahan" data-jenis-pegawai="Tenaga Kependidikan" {{ old('jenis_jabatan', $pegawai->jenis_jabatan ?? '') == 'Tenaga Kependidikan Tugas Tambahan' ? 'selected' : '' }}>Tenaga Kependidikan Tugas Tambahan</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 lg:pr-4 pointer-events-none">
                        <svg class="w-4 h-4 lg:w-5 lg:h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Status Kepegawaian --}}
            <div class="group">
                <label for="status_kepegawaian" class="block text-sm lg:text-base font-semibold text-slate-700 mb-2 lg:mb-3 group-hover:text-blue-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-3 h-3 lg:w-4 lg:h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.5-1a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span class="break-words">Status Kepegawaian <span class="text-red-500">*</span></span>
                    </div>
                </label>
                <div class="relative">
                    <div class="filter-status" id="status-kepegawaian-filter-status">Filter Active</div>
                    <select name="status_kepegawaian"
                            id="status_kepegawaian"
                            class="w-full px-3 lg:px-4 py-3 lg:py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md appearance-none text-sm lg:text-base">
                        <option value="">Pilih Status Kepegawaian</option>
                        <option value="Dosen PNS" data-jenis-pegawai="Dosen" {{ old('status_kepegawaian', $pegawai->status_kepegawaian ?? '') == 'Dosen PNS' ? 'selected' : '' }}>Dosen PNS</option>
                        <option value="Dosen PPPK" data-jenis-pegawai="Dosen" {{ old('status_kepegawaian', $pegawai->status_kepegawaian ?? '') == 'Dosen PPPK' ? 'selected' : '' }}>Dosen PPPK</option>
                        <option value="Dosen Non ASN" data-jenis-pegawai="Dosen" {{ old('status_kepegawaian', $pegawai->status_kepegawaian ?? '') == 'Dosen Non ASN' ? 'selected' : '' }}>Dosen Non ASN</option>
                        <option value="Tenaga Kependidikan PNS" data-jenis-pegawai="Tenaga Kependidikan" {{ old('status_kepegawaian', $pegawai->status_kepegawaian ?? '') == 'Tenaga Kependidikan PNS' ? 'selected' : '' }}>Tenaga Kependidikan PNS</option>
                        <option value="Tenaga Kependidikan PPPK" data-jenis-pegawai="Tenaga Kependidikan" {{ old('status_kepegawaian', $pegawai->status_kepegawaian ?? '') == 'Tenaga Kependidikan PPPK' ? 'selected' : '' }}>Tenaga Kependidikan PPPK</option>
                        <option value="Tenaga Kependidikan Non ASN" data-jenis-pegawai="Tenaga Kependidikan" {{ old('status_kepegawaian', $pegawai->status_kepegawaian ?? '') == 'Tenaga Kependidikan Non ASN' ? 'selected' : '' }}>Tenaga Kependidikan Non ASN</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 lg:pr-4 pointer-events-none">
                        <svg class="w-4 h-4 lg:w-5 lg:h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Jabatan Terakhir --}}
            <div class="group">
                <label for="jabatan_terakhir_id" class="block text-sm lg:text-base font-semibold text-slate-700 mb-2 lg:mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-3 h-3 lg:w-4 lg:h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                        </svg>
                        <span class="break-words">Jabatan Terakhir <span id="jenis_jabatan_display" class="text-indigo-600 font-normal"></span>
                        <span class="text-red-500">*</span></span>
                    </div>
                </label>
                <div class="relative">
                    <div class="filter-status" id="jabatan-terakhir-filter-status">Filter Active</div>
                    <select name="jabatan_terakhir_id"
                            id="jabatan_terakhir_id"
                            class="w-full px-3 lg:px-4 py-3 lg:py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md appearance-none text-sm lg:text-base">
                        <option value="">Pilih Jabatan</option>
                        @foreach($jabatans as $jabatan)
                            <option value="{{ $jabatan->id }}"
                                    data-jenis-pegawai="{{ $jabatan->jenis_pegawai }}"
                                    data-jenis-jabatan="{{ $jabatan->jenis_jabatan }}"
                                    {{ old('jabatan_terakhir_id', $pegawai->jabatan_terakhir_id ?? '') == $jabatan->id ? 'selected' : '' }}>
                                {{ $jabatan->jabatan }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 lg:pr-4 pointer-events-none">
                        <svg class="w-4 h-4 lg:w-5 lg:h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- TMT Jabatan --}}
            <div class="group">
                <label for="tmt_jabatan" class="block text-sm lg:text-base font-semibold text-slate-700 mb-2 lg:mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-3 h-3 lg:w-4 lg:h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="break-words">TMT Jabatan <span class="text-red-500">*</span></span>
                    </div>
                </label>
                <div class="relative">
                    <input type="date"
                           name="tmt_jabatan"
                           id="tmt_jabatan"
                           value="{{ old('tmt_jabatan', isset($pegawai) ? $pegawai->tmt_jabatan->format('Y-m-d') : '') }}"
                           class="w-full px-3 lg:px-4 py-3 lg:py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md text-sm lg:text-base">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 lg:pr-4 pointer-events-none">
                        <svg class="w-4 h-4 lg:w-5 lg:h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- NUPTK (Conditional) --}}
            <div id="field_nuptk" class="hidden group">
                <label for="nuptk" class="block text-sm lg:text-base font-semibold text-slate-700 mb-2 lg:mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-3 h-3 lg:w-4 lg:h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                        </svg>
                        <span class="break-words">NUPTK <span class="text-red-500">*</span></span>
                    </div>
                </label>
                <div class="relative">
                    <input type="text"
                           name="nuptk"
                           id="nuptk"
                           value="{{ old('nuptk', $pegawai->nuptk ?? '') }}"
                           class="w-full px-3 lg:px-4 py-3 lg:py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 placeholder-slate-400 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md text-sm lg:text-base"
                           placeholder="16 Karakter Numerik"
                           maxlength="16">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 lg:pr-4 pointer-events-none">
                        <svg class="w-4 h-4 lg:w-5 lg:h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pangkat Section --}}
    <div class="space-y-6 animate-fade-in-up">
        <div class="flex items-center gap-4 pb-6 border-b border-slate-200">
            <div class="p-3 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.5-1a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-800">Pangkat</h3>
                <p class="text-slate-600">Informasi pangkat terakhir</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 lg:gap-6">
            {{-- Pangkat Terakhir --}}
            <div class="group">
                <label for="pangkat_terakhir_id" class="block text-sm lg:text-base font-semibold text-slate-700 mb-2 lg:mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-3 h-3 lg:w-4 lg:h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.5-1a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span class="break-words">Pangkat Terakhir <span class="text-red-500">*</span></span>
                    </div>
                </label>
                <div class="relative">
                    <select name="pangkat_terakhir_id"
                            id="pangkat_terakhir_id"
                            class="w-full px-3 lg:px-4 py-3 lg:py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md appearance-none text-sm lg:text-base">
                        <option value="">Pilih Pangkat</option>
                        {{-- PNS Pangkat --}}
                        <optgroup label="PNS">
                            @foreach($pangkats->where('status_pangkat', 'PNS') as $pangkat)
                                <option value="{{ $pangkat->id }}"
                                        data-status="{{ $pangkat->status_pangkat }}"
                                        {{ old('pangkat_terakhir_id', $pegawai->pangkat_terakhir_id ?? '') == $pangkat->id ? 'selected' : '' }}>
                                    {{ $pangkat->pangkat }}
                                </option>
                            @endforeach
                        </optgroup>

                        {{-- PPPK Pangkat --}}
                        <optgroup label="PPPK">
                            @foreach($pangkats->where('status_pangkat', 'PPPK') as $pangkat)
                                <option value="{{ $pangkat->id }}"
                                        data-status="{{ $pangkat->status_pangkat }}"
                                        {{ old('pangkat_terakhir_id', $pegawai->pangkat_terakhir_id ?? '') == $pangkat->id ? 'selected' : '' }}>
                                    {{ $pangkat->pangkat }}
                                </option>
                            @endforeach
                        </optgroup>

                        {{-- Non ASN Pangkat --}}
                        <optgroup label="Non ASN">
                            @foreach($pangkats->where('status_pangkat', 'Non-ASN') as $pangkat)
                                <option value="{{ $pangkat->id }}"
                                        data-status="{{ $pangkat->status_pangkat }}"
                                        {{ old('pangkat_terakhir_id', $pegawai->pangkat_terakhir_id ?? '') == $pangkat->id ? 'selected' : '' }}>
                                    {{ $pangkat->pangkat }}
                                </option>
                            @endforeach
                        </optgroup>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 lg:pr-4 pointer-events-none">
                        <svg class="w-4 h-4 lg:w-5 lg:h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- TMT Pangkat --}}
            <div class="group">
                <label for="tmt_pangkat" class="block text-sm lg:text-base font-semibold text-slate-700 mb-2 lg:mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-3 h-3 lg:w-4 lg:h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="break-words">TMT Pangkat <span class="text-red-500">*</span></span>
                    </div>
                </label>
                <div class="relative">
                    <input type="date"
                           name="tmt_pangkat"
                           id="tmt_pangkat"
                           value="{{ old('tmt_pangkat', isset($pegawai) ? $pegawai->tmt_pangkat->format('Y-m-d') : '') }}"
                           class="w-full px-3 lg:px-4 py-3 lg:py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md text-sm lg:text-base">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 lg:pr-4 pointer-events-none">
                        <svg class="w-4 h-4 lg:w-5 lg:h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CPNS & PNS Section --}}
    <div class="space-y-6 animate-fade-in-up">
        <div class="flex items-center gap-4 pb-6 border-b border-slate-200">
            <div class="p-3 bg-gradient-to-r from-blue-500 to-cyan-600 rounded-2xl shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-800">CPNS & PNS</h3>
                <p class="text-slate-600">Informasi pengangkatan CPNS dan PNS</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 lg:gap-6">
            {{-- TMT CPNS --}}
            <div class="group">
                <label for="tmt_cpns" class="block text-sm lg:text-base font-semibold text-slate-700 mb-2 lg:mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-3 h-3 lg:w-4 lg:h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="break-words">TMT CPNS <span class="text-red-500">*</span></span>
                    </div>
                </label>
                <div class="relative">
                    <input type="date"
                           name="tmt_cpns"
                           id="tmt_cpns"
                           value="{{ old('tmt_cpns', isset($pegawai) && $pegawai->tmt_cpns ? $pegawai->tmt_cpns->format('Y-m-d') : '') }}"
                           class="w-full px-3 lg:px-4 py-3 lg:py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md text-sm lg:text-base">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 lg:pr-4 pointer-events-none">
                        <svg class="w-4 h-4 lg:w-5 lg:h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- TMT PNS --}}
            <div class="group">
                <label for="tmt_pns" class="block text-sm lg:text-base font-semibold text-slate-700 mb-2 lg:mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-3 h-3 lg:w-4 lg:h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="break-words">TMT PNS <span class="text-red-500">*</span></span>
                    </div>
                </label>
                <div class="relative">
                    <input type="date"
                           name="tmt_pns"
                           id="tmt_pns"
                           value="{{ old('tmt_pns', isset($pegawai) && $pegawai->tmt_pns ? $pegawai->tmt_pns->format('Y-m-d') : '') }}"
                           class="w-full px-3 lg:px-4 py-3 lg:py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md text-sm lg:text-base">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 lg:pr-4 pointer-events-none">
                        <svg class="w-4 h-4 lg:w-5 lg:h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Performance & Conversion Section --}}
    <div class="space-y-6 animate-fade-in-up">
        <div class="flex items-center gap-4 pb-6 border-b border-slate-200">
            <div class="p-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.7 8l-5.1 5.2-2.8-2.7L7 14.3"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-800">Kinerja & Konversi</h3>
                <p class="text-slate-600">Predikat kinerja dan nilai konversi</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 lg:gap-6">
            @php $kinerjaOptions = ['Sangat Baik', 'Baik', 'Perbaiki']; @endphp

            {{-- Predikat Kinerja Tahun Pertama --}}
            <div class="group">
                <label for="predikat_kinerja_tahun_pertama" class="block text-sm lg:text-base font-semibold text-slate-700 mb-2 lg:mb-3 group-hover:text-blue-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-3 h-3 lg:w-4 lg:h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 6l-9.5 9.5-5-5L1 18"></path>
                        </svg>
                        <span class="break-words">Predikat Kinerja Thn. 1 <span class="text-red-500">*</span></span>
                    </div>
                </label>
                <div class="relative">
                    <select name="predikat_kinerja_tahun_pertama"
                            id="predikat_kinerja_tahun_pertama"
                            class="w-full px-3 lg:px-4 py-3 lg:py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md appearance-none text-sm lg:text-base">
                        <option value="">Pilih Predikat</option>
                        @foreach($kinerjaOptions as $option)
                            <option value="{{ $option }}" {{ old('predikat_kinerja_tahun_pertama', $pegawai->predikat_kinerja_tahun_pertama ?? '') == $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 lg:pr-4 pointer-events-none">
                        <svg class="w-4 h-4 lg:w-5 lg:h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Predikat Kinerja Tahun Kedua --}}
            <div class="group">
                <label for="predikat_kinerja_tahun_kedua" class="block text-sm lg:text-base font-semibold text-slate-700 mb-2 lg:mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-3 h-3 lg:w-4 lg:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 6l-9.5 9.5-5-5L1 18"></path>
                        </svg>
                        <span class="break-words">Predikat Kinerja Thn. 2 <span class="text-red-500">*</span></span>
                    </div>
                </label>
                <div class="relative">
                    <select name="predikat_kinerja_tahun_kedua"
                            id="predikat_kinerja_tahun_kedua"
                            class="w-full px-3 lg:px-4 py-3 lg:py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md appearance-none text-sm lg:text-base">
                        <option value="">Pilih Predikat</option>
                        @foreach($kinerjaOptions as $option)
                            <option value="{{ $option }}" {{ old('predikat_kinerja_tahun_kedua', $pegawai->predikat_kinerja_tahun_kedua ?? '') == $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 lg:pr-4 pointer-events-none">
                        <svg class="w-4 h-4 lg:w-5 lg:h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Nilai Konversi (Conditional) --}}
            <div id="field_nilai_konversi" class="hidden group">
                <label for="nilai_konversi" class="block text-sm lg:text-base font-semibold text-slate-700 mb-2 lg:mb-3 group-hover:text-indigo-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-3 h-3 lg:w-4 lg:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <span class="break-words">Nilai Konversi <span class="text-red-500">*</span></span>
                    </div>
                </label>
                <div class="relative">
                    <input type="number"
                           name="nilai_konversi"
                           id="nilai_konversi"
                           step="any"
                           value="{{ old('nilai_konversi', $pegawai->nilai_konversi ?? '') }}"
                           class="w-full px-3 lg:px-4 py-3 lg:py-4 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200/50 transition-all duration-300 placeholder-slate-400 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md text-sm lg:text-base"
                           placeholder="Contoh: 112.50">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 lg:pr-4 pointer-events-none">
                        <svg class="w-4 h-4 lg:w-5 lg:h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom animations for employment form elements */
    .group:hover input,
    .group:hover select,
    .group:hover textarea {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.1);
    }

    .group input:focus,
    .group select:focus,
    .group textarea:focus {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.2);
    }

    /* Smooth transitions for all form elements */
    input, select, textarea, label {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Custom select styling */
    select {
        background-image: none !important;
    }

    /* Hover effects for labels */
    .group:hover label {
        transform: translateX(5px);
    }

    /* Animation for conditional fields */
    #field_nuptk,
    #field_nilai_konversi {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Textarea specific styling */
    textarea {
        resize: none;
        min-height: 120px;
    }

    /* Custom scrollbar for textarea */
    textarea::-webkit-scrollbar {
        width: 6px;
    }

    textarea::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    textarea::-webkit-scrollbar-thumb {
        background: linear-gradient(to bottom, #6366f1, #8b5cf6);
        border-radius: 3px;
    }

    textarea::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(to bottom, #4f46e5, #7c3aed);
    }

    /* Styling for disabled options */
    select option:disabled {
        color: #9ca3af;
        background-color: #f3f4f6;
    }

    select option:not(:disabled) {
        color: #374151;
        background-color: white;
    }

    /* Ensure hidden options are properly hidden */
    #jenis_jabatan option[style*="display: none"] {
        display: none !important;
    }

    #status_kepegawaian option[style*="display: none"] {
        display: none !important;
    }

    #jabatan_terakhir_id option[style*="display: none"] {
        display: none !important;
    }

    /* Styling for optgroup in pangkat dropdown */
    #pangkat_terakhir_id optgroup {
        font-weight: bold;
        color: #374151;
        background-color: #f9fafb;
        padding: 8px 0;
    }

    #pangkat_terakhir_id optgroup[label="PNS"] {
        color: #059669;
        background-color: #ecfdf5;
    }

    #pangkat_terakhir_id optgroup[label="PPPK"] {
        color: #2563eb;
        background-color: #eff6ff;
    }

    #pangkat_terakhir_id optgroup[label="Non ASN"] {
        color: #ea580c;
        background-color: #fff7ed;
    }

    #pangkat_terakhir_id option {
        padding: 8px 12px;
        font-weight: normal;
    }

    /* Hide optgroups when filtered */
    #pangkat_terakhir_id optgroup[style*="display: none"] {
        display: none !important;
    }

    /* Visual indicator for filtering status */
    .filter-status {
        position: absolute;
        top: -8px;
        right: 10px;
        background: #10b981;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: bold;
        z-index: 10;
    }

    .filter-status.inactive {
        background: #ef4444;
    }
</style>

{{-- Include Employment Data JavaScript --}}
<script>
// Employment Data Filtering JavaScript
document.addEventListener('DOMContentLoaded', function() {

    function updateFilterStatus(elementId, message, isActive = true) {
        const statusEl = document.getElementById(elementId);
        if (statusEl) {
            statusEl.textContent = message;
            statusEl.className = isActive ? 'filter-status' : 'filter-status inactive';
        }
    }

    function filterJenisJabatan() {
        const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
        const jenisJabatanSelect = document.getElementById('jenis_jabatan');

        if (!jenisPegawaiSelect || !jenisJabatanSelect) {
            updateFilterStatus('jenis-jabatan-filter-status', 'Elements Not Found', false);
            return;
        }

        const selectedJenisPegawai = jenisPegawaiSelect.value;

        if (!selectedJenisPegawai) {
            updateFilterStatus('jenis-jabatan-filter-status', 'No Selection');
            return;
        }

        const options = jenisJabatanSelect.querySelectorAll('option');
        let visibleCount = 0;
        let hiddenCount = 0;

        options.forEach((option, index) => {
            if (option.value === '') {
                // Keep placeholder visible
                option.style.display = '';
                option.disabled = false;
                return;
            }

            const dataJenisPegawai = option.getAttribute('data-jenis-pegawai');

            if (dataJenisPegawai === selectedJenisPegawai) {
                option.style.display = '';
                option.disabled = false;
                visibleCount++;
            } else {
                option.style.display = 'none';
                option.disabled = true;
                hiddenCount++;
            }
        });

        updateFilterStatus('jenis-jabatan-filter-status', `${visibleCount} Options Visible`);

        // Force browser to re-render the select
        jenisJabatanSelect.style.display = 'none';
        setTimeout(() => {
            jenisJabatanSelect.style.display = '';
        }, 10);
    }

    function filterStatusKepegawaian() {
        const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
        const statusKepegawaianSelect = document.getElementById('status_kepegawaian');

        if (!jenisPegawaiSelect || !statusKepegawaianSelect) {
            updateFilterStatus('status-kepegawaian-filter-status', 'Elements Not Found', false);
            return;
        }

        const selectedJenisPegawai = jenisPegawaiSelect.value;

        if (!selectedJenisPegawai) {
            updateFilterStatus('status-kepegawaian-filter-status', 'No Selection');
            return;
        }

        const options = statusKepegawaianSelect.querySelectorAll('option');
        let visibleCount = 0;
        let hiddenCount = 0;

        options.forEach((option, index) => {
            if (option.value === '') {
                // Keep placeholder visible
                option.style.display = '';
                option.disabled = false;
                return;
            }

            const dataJenisPegawai = option.getAttribute('data-jenis-pegawai');

            if (dataJenisPegawai === selectedJenisPegawai) {
                option.style.display = '';
                option.disabled = false;
                visibleCount++;
            } else {
                option.style.display = 'none';
                option.disabled = true;
                hiddenCount++;
            }
        });

        updateFilterStatus('status-kepegawaian-filter-status', `${visibleCount} Options Visible`);
    }

    function filterJabatanTerakhir() {
        const jenisJabatanSelect = document.getElementById('jenis_jabatan');
        const jabatanTerakhirSelect = document.getElementById('jabatan_terakhir_id');

        if (!jenisJabatanSelect || !jabatanTerakhirSelect) {
            return;
        }

        const selectedJenisJabatan = jenisJabatanSelect.value;

        if (!selectedJenisJabatan) {
            // Show all options if no jenis jabatan selected
            const options = jabatanTerakhirSelect.querySelectorAll('option');
            options.forEach(option => {
                option.style.display = '';
                option.disabled = false;
            });
            return;
        }

        const options = jabatanTerakhirSelect.querySelectorAll('option');
        let visibleCount = 0;
        let hiddenCount = 0;

        options.forEach((option, index) => {
            if (option.value === '') {
                // Keep placeholder visible
                option.style.display = '';
                option.disabled = false;
                return;
            }

            const dataJenisJabatan = option.getAttribute('data-jenis-jabatan');

            if (dataJenisJabatan === selectedJenisJabatan) {
                option.style.display = '';
                option.disabled = false;
                visibleCount++;
            } else {
                option.style.display = 'none';
                option.disabled = true;
                hiddenCount++;
            }
        });
    }

    function filterPangkat() {
        const statusKepegawaianSelect = document.getElementById('status_kepegawaian');
        const pangkatSelect = document.getElementById('pangkat_terakhir_id');

        if (!statusKepegawaianSelect || !pangkatSelect) {
            return;
        }

        const selectedStatusKepegawaian = statusKepegawaianSelect.value;

        if (!selectedStatusKepegawaian) {
            // Show all options if no status kepegawaian selected
            const options = pangkatSelect.querySelectorAll('option');
            options.forEach(option => {
                option.style.display = '';
                option.disabled = false;
            });
            return;
        }

        // Map status kepegawaian to pangkat status
        let targetPangkatStatus;
        switch (selectedStatusKepegawaian) {
            case 'Dosen PNS':
            case 'Tenaga Kependidikan PNS':
                targetPangkatStatus = 'PNS';
                break;
            case 'Dosen PPPK':
            case 'Tenaga Kependidikan PPPK':
                targetPangkatStatus = 'PPPK';
                break;
            case 'Dosen Non ASN':
            case 'Tenaga Kependidikan Non ASN':
                targetPangkatStatus = 'Non ASN';
                break;
            default:
                targetPangkatStatus = null;
        }

        const options = pangkatSelect.querySelectorAll('option');
        let visibleCount = 0;
        let hiddenCount = 0;

        options.forEach((option, index) => {
            if (option.value === '') {
                // Keep placeholder visible
                option.style.display = '';
                option.disabled = false;
                return;
            }

            const optgroup = option.parentElement;
            const optgroupLabel = optgroup.label || '';

            if (targetPangkatStatus && optgroupLabel.includes(targetPangkatStatus)) {
                option.style.display = '';
                option.disabled = false;
                visibleCount++;
            } else {
                option.style.display = 'none';
                option.disabled = true;
                hiddenCount++;
            }
        });
    }

    function filterAllEmploymentData() {
        filterJenisJabatan();
        filterStatusKepegawaian();
        filterJabatanTerakhir();
        filterPangkat();
    }

    // Apply filters immediately
    filterAllEmploymentData();

    // Add event listeners
    const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
    if (jenisPegawaiSelect) {
        jenisPegawaiSelect.addEventListener('change', function() {
            filterJenisJabatan();
            filterStatusKepegawaian();
            filterJabatanTerakhir();
            filterPangkat();
        });
    }

    const jenisJabatanSelect = document.getElementById('jenis_jabatan');
    if (jenisJabatanSelect) {
        jenisJabatanSelect.addEventListener('change', function() {
            filterJabatanTerakhir();
        });
    }

    const statusKepegawaianSelect = document.getElementById('status_kepegawaian');
    if (statusKepegawaianSelect) {
        statusKepegawaianSelect.addEventListener('change', function() {
            filterPangkat();
        });
    }

    // Apply filters again after delays
    setTimeout(() => {
        filterAllEmploymentData();
    }, 100);
    setTimeout(() => {
        filterAllEmploymentData();
    }, 500);
    setTimeout(() => {
        filterAllEmploymentData();
    }, 1000);

    // Make functions globally available
    window.directFilterJenisJabatan = filterJenisJabatan;
    window.directFilterStatusKepegawaian = filterStatusKepegawaian;
    window.directFilterJabatanTerakhir = filterJabatanTerakhir;
    window.directFilterPangkat = filterPangkat;
    window.directFilterAllEmploymentData = filterAllEmploymentData;

});

</script>
