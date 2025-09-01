{{-- components/profile-display.blade.php --}}
{{-- Enhanced component dengan sistem validasi untuk semua field --}}
@php
    // Safety check untuk variabel yang mungkin tidak ada
    $catatanPerbaikan = $catatanPerbaikan ?? [];

    // Ensure all required array keys exist
    $catatanPerbaikan = array_merge([
        'data_pribadi' => [],
        'data_kepegawaian' => [],
        'data_pendidikan' => [],
        'data_kinerja' => [],
        'dokumen_profil' => [],
        'dokumen_usulan' => [],
        'dokumen_bkd' => [],
    ], $catatanPerbaikan);
@endphp
<div class="bg-white p-8 rounded-xl shadow-lg mb-6 border border-gray-100">
    <!-- Header Section -->
    <div class="border-b border-slate-200 pb-6 mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="bg-indigo-100 p-3 rounded-full">
                    <i data-lucide="user-circle" class="w-8 h-8 text-indigo-600"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Data Profil & Dokumen Pegawai</h1>
                    <p class="text-slate-600 mt-1">Informasi lengkap kepegawaian dan dokumen pendukung</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-1 gap-8">
        <!-- A. Data Pribadi -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-4 rounded-t-xl">
                <h3 class="text-lg font-semibold flex items-center">
                    <i data-lucide="user" class="w-5 h-5 mr-3"></i>
                    A. Data Pribadi
                </h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-5">
                @php
                    $dataPribadi = [
                        'jenis_pegawai'      => ['Jenis Pegawai', $pegawai->jenis_pegawai, 'briefcase'],
                        'status_kepegawaian' => ['Status Kepegawaian', $pegawai->status_kepegawaian, 'shield-check'],
                        'nip'               => ['NIP', $pegawai->nip, 'hash'],
                        'nuptk'             => ['NUPTK', $pegawai->nuptk, 'credit-card'],
                        'gelar_depan'       => ['Gelar Depan', $pegawai->gelar_depan, 'award'],
                        'nama_lengkap'      => ['Nama Lengkap', $pegawai->nama_lengkap, 'user'],
                        'gelar_belakang'    => ['Gelar Belakang', $pegawai->gelar_belakang, 'award'],
                        'email'             => ['Email', $pegawai->email, 'mail'],
                        'tempat_lahir'      => ['Tempat Lahir', $pegawai->tempat_lahir, 'map-pin'],
                        'tanggal_lahir'     => ['Tanggal Lahir', optional($pegawai->tanggal_lahir)->isoFormat('D MMMM YYYY'), 'calendar'],
                        'jenis_kelamin'     => ['Jenis Kelamin', $pegawai->jenis_kelamin, 'users'],
                        'nomor_handphone'   => ['Nomor HP', $pegawai->nomor_handphone, 'phone'],
                    ];
                @endphp

                @foreach ($dataPribadi as $key => [$label, $value, $icon])
                    @php
                        // Use hybrid approach for validation checking
                        $isFieldInvalid = isFieldInvalid('data_pribadi', $key, $validationData ?? [], $catatanPerbaikan ?? []);
                        $allValidationNotes = [];

                        if (isset($isEditMode) && $isEditMode) {
                            $allValidationNotes = getFieldValidationNotes('data_pribadi', $key, $validationData ?? [], $catatanPerbaikan ?? []);
                        }
                    @endphp

                    <div class="flex items-start gap-3 p-3 rounded-lg {{ $isFieldInvalid ? 'bg-red-50 border border-red-200' : 'bg-slate-50' }} hover:{{ $isFieldInvalid ? 'bg-red-100' : 'bg-slate-100' }} transition-colors">
                        <div class="bg-white p-2 rounded-lg shadow-sm">
                            <i data-lucide="{{ $icon }}" class="w-4 h-4 {{ $isFieldInvalid ? 'text-red-600' : 'text-slate-600' }}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <dt class="text-sm font-medium {{ $isFieldInvalid ? 'text-red-700' : 'text-slate-700' }}">
                                {{ $label }}
                                @if($isFieldInvalid)
                                    <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                                        Perlu Perbaikan
                                    </span>
                                @endif
                            </dt>
                            <dd class="text-sm {{ $isFieldInvalid ? 'text-red-900' : 'text-slate-900' }} mt-1 break-words">{{ $value ?? '-' }}</dd>

                            {{-- Tampilkan catatan dari semua role jika ada --}}
                            @if(!empty($allValidationNotes))
                                <div class="mt-2 space-y-2">
                                    @foreach($allValidationNotes as $note)
                                        <div class="text-xs bg-red-100 p-2 rounded border-l-2 border-red-400">
                                            <div class="flex items-start gap-1">
                                                <i data-lucide="message-square" class="w-3 h-3 mt-0.5 text-red-600"></i>
                                                <div>
                                                    <strong>{{ $note['role'] }}:</strong><br>
                                                    {{ $note['note'] }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($isFieldInvalid)
                                <div class="mt-2 text-xs text-red-700 bg-red-100 p-2 rounded border-l-2 border-red-400">
                                    <div class="flex items-start gap-1">
                                        <i data-lucide="message-square" class="w-3 h-3 mt-0.5 text-red-600"></i>
                                        <div>
                                            <strong>Catatan Perbaikan:</strong><br>
                                            {{ $fieldValidation['keterangan'] }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <input type="hidden" name="snapshot[{{ $key }}]" value="{{ $pegawai[$key] }}">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- B. Data Kepegawaian -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 text-white p-4 rounded-t-xl">
                <h3 class="text-lg font-semibold flex items-center">
                    <i data-lucide="briefcase" class="w-5 h-5 mr-3"></i>
                    B. Data Kepegawaian
                </h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-5">
                @php
                    $dataKepegawaian = [
                        'pangkat_saat_usul'    => ['Pangkat',         $pegawai->pangkat?->pangkat, 'star'],
                        'tmt_pangkat'          => ['TMT Pangkat',     optional($pegawai->tmt_pangkat)->isoFormat('D MMMM YYYY'), 'calendar-days'],
                        'jabatan_saat_usul'    => ['Jabatan',         $pegawai->jabatan?->jabatan, 'crown'],
                        'tmt_jabatan'          => ['TMT Jabatan',     optional($pegawai->tmt_jabatan)->isoFormat('D MMMM YYYY'), 'calendar-check'],
                        'tmt_cpns'             => ['TMT CPNS',        optional($pegawai->tmt_cpns)->isoFormat('D MMMM YYYY'), 'calendar'],
                        'tmt_pns'              => ['TMT PNS',         optional($pegawai->tmt_pns)->isoFormat('D MMMM YYYY'), 'calendar-plus'],
                        'unit_kerja_saat_usul' => ['Unit Kerja',     $pegawai->unitKerja?->nama, 'building'],
                    ];
                @endphp

                @foreach ($dataKepegawaian as $key => [$label, $value, $icon])
                    @php
                        // Use hybrid approach for validation checking
                        $isFieldInvalid = isFieldInvalid('data_kepegawaian', $key, $validationData ?? [], $catatanPerbaikan ?? []);
                        $allValidationNotes = [];

                        if (isset($isEditMode) && $isEditMode) {
                            $allValidationNotes = getFieldValidationNotes('data_kepegawaian', $key, $validationData ?? [], $catatanPerbaikan ?? []);
                        }
                    @endphp

                    <div class="flex items-start gap-3 p-3 rounded-lg {{ $isFieldInvalid ? 'bg-red-50 border border-red-200' : 'bg-slate-50' }} hover:{{ $isFieldInvalid ? 'bg-red-100' : 'bg-slate-100' }} transition-colors">
                        <div class="bg-white p-2 rounded-lg shadow-sm">
                            <i data-lucide="{{ $icon }}" class="w-4 h-4 {{ $isFieldInvalid ? 'text-red-600' : 'text-slate-600' }}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <dt class="text-sm font-medium {{ $isFieldInvalid ? 'text-red-700' : 'text-slate-700' }}">
                                {{ $label }}
                                @if($isFieldInvalid)
                                    <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                                        Perlu Perbaikan
                                    </span>
                                @endif
                            </dt>
                            <dd class="text-sm {{ $isFieldInvalid ? 'text-red-900' : 'text-slate-900' }} mt-1 break-words">{{ $value ?? '-' }}</dd>

                            {{-- Tampilkan catatan dari semua role jika ada --}}
                            @if(!empty($allValidationNotes))
                                <div class="mt-2 space-y-2">
                                    @foreach($allValidationNotes as $note)
                                        <div class="text-xs bg-red-100 p-2 rounded border-l-2 border-red-400">
                                            <div class="flex items-start gap-1">
                                                <i data-lucide="message-square" class="w-3 h-3 mt-0.5 text-red-600"></i>
                                                <div>
                                                    <strong>{{ $note['role'] }}:</strong><br>
                                                    {{ $note['note'] }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($isFieldInvalid)
                                <div class="mt-2 text-xs text-red-700 bg-red-100 p-2 rounded border-l-2 border-red-400">
                                    <div class="flex items-start gap-1">
                                        <i data-lucide="message-square" class="w-3 h-3 mt-0.5 text-red-600"></i>
                                        <div>
                                            <strong>Catatan Perbaikan:</strong><br>
                                            {{ $fieldValidation['keterangan'] }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <input type="hidden" name="snapshot[{{ $key }}]"
                                value="{{ is_string($value) ? $value : $pegawai->{$key} }}">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- C. Data Pendidikan & Fungsional -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-4 rounded-t-xl">
                <h3 class="text-lg font-semibold flex items-center">
                    <i data-lucide="graduation-cap" class="w-5 h-5 mr-3"></i>
                    C. Data Pendidikan & Fungsional
                </h3>
            </div>
            <div class="p-6 space-y-4">
                @php
                    $dataPendidikan = [
                        'pendidikan_terakhir'    => ['Pendidikan Terakhir',    $pegawai->pendidikan_terakhir, 'book-open'],
                        'nama_universitas_sekolah' => ['Nama Universitas/Sekolah', $pegawai->nama_universitas_sekolah, 'building'],
                        'nama_prodi_jurusan'     => ['Nama Program Studi/Jurusan', $pegawai->nama_prodi_jurusan, 'graduation-cap'],
                        'mata_kuliah_diampu'     => ['Mata Kuliah Diampu',     $pegawai->mata_kuliah_diampu, 'book'],
                        'ranting_ilmu_kepakaran' => ['Bidang Kepakaran',       $pegawai->ranting_ilmu_kepakaran, 'brain'],
                        'url_profil_sinta'       => ['Profil SINTA',           $pegawai->url_profil_sinta, 'external-link'],
                    ];
                @endphp

                @foreach ($dataPendidikan as $key => [$label, $value, $icon])
                    @php
                        // Use hybrid approach for validation checking
                        $isFieldInvalid = isFieldInvalid('data_pendidikan', $key, $validationData ?? [], $catatanPerbaikan ?? []);
                        $allValidationNotes = [];

                        if (isset($isEditMode) && $isEditMode) {
                            $allValidationNotes = getFieldValidationNotes('data_pendidikan', $key, $validationData ?? [], $catatanPerbaikan ?? []);
                        }
                    @endphp

                    <div class="flex items-start gap-3 p-3 rounded-lg {{ $isFieldInvalid ? 'bg-red-50 border border-red-200' : 'bg-slate-50' }} hover:{{ $isFieldInvalid ? 'bg-red-100' : 'bg-slate-100' }} transition-colors">
                        <div class="bg-white p-2 rounded-lg shadow-sm">
                            <i data-lucide="{{ $icon }}" class="w-4 h-4 {{ $isFieldInvalid ? 'text-red-600' : 'text-slate-600' }}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <dt class="text-sm font-medium {{ $isFieldInvalid ? 'text-red-700' : 'text-slate-700' }}">
                                {{ $label }}
                                @if($isFieldInvalid)
                                    <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                                        Perlu Perbaikan
                                    </span>
                                @endif
                            </dt>
                            <dd class="text-sm {{ $isFieldInvalid ? 'text-red-900' : 'text-slate-900' }} mt-1 break-words">
                                @if($key === 'url_profil_sinta' && $value)
                                    <a href="{{ $value }}" target="_blank" class="{{ $isFieldInvalid ? 'text-red-600 hover:text-red-800' : 'text-purple-600 hover:text-purple-800' }} underline">
                                        {{ $value }}
                                    </a>
                                @else
                                    {{ $value ?? '-' }}
                                @endif
                            </dd>

                            {{-- Tampilkan catatan dari semua role jika tidak valid --}}
                            @if($isFieldInvalid && !empty($allValidationNotes))
                                <div class="mt-2 space-y-2">
                                    @foreach($allValidationNotes as $note)
                                        <div class="text-xs text-red-700 bg-red-100 p-2 rounded border-l-2 border-red-400">
                                            <div class="flex items-start gap-1">
                                                <i data-lucide="message-square" class="w-3 h-3 mt-0.5 text-red-600"></i>
                                                <div>
                                                    <strong>{{ $note['role'] }}:</strong><br>
                                                    {{ $note['note'] }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <input type="hidden" name="snapshot[{{ $key }}]" value="{{ $value }}">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- D. Data Kinerja -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="bg-gradient-to-r from-amber-500 to-amber-600 text-white p-4 rounded-t-xl">
                <h3 class="text-lg font-semibold flex items-center">
                    <i data-lucide="trending-up" class="w-5 h-5 mr-3"></i>
                    D. Data Kinerja
                </h3>
            </div>
            <div class="p-6 space-y-4">
                @php
                    $dataKinerja = [
                        'predikat_kinerja_tahun_pertama' => ['Predikat SKP Tahun '. (date('Y') - 1), $pegawai->predikat_kinerja_tahun_pertama, 'target'],
                        'predikat_kinerja_tahun_kedua'   => ['Predikat SKP Tahun '. (date('Y') - 2), $pegawai->predikat_kinerja_tahun_kedua, 'target'],
                        'nilai_konversi'                 => ['Nilai Konversi '. (date('Y') - 1), $pegawai->nilai_konversi, 'calculator'],
                    ];
                @endphp

                @foreach ($dataKinerja as $key => [$label, $value, $icon])
                    @php
                        // Use hybrid approach for validation checking
                        $isFieldInvalid = isFieldInvalid('data_kinerja', $key, $validationData ?? [], $catatanPerbaikan ?? []);
                        $allValidationNotes = [];

                        if (isset($isEditMode) && $isEditMode) {
                            $allValidationNotes = getFieldValidationNotes('data_kinerja', $key, $validationData ?? [], $catatanPerbaikan ?? []);
                        }
                    @endphp

                    <div class="flex items-start gap-3 p-3 rounded-lg {{ $isFieldInvalid ? 'bg-red-50 border border-red-200' : 'bg-slate-50' }} hover:{{ $isFieldInvalid ? 'bg-red-100' : 'bg-slate-100' }} transition-colors">
                        <div class="bg-white p-2 rounded-lg shadow-sm">
                            <i data-lucide="{{ $icon }}" class="w-4 h-4 {{ $isFieldInvalid ? 'text-red-600' : 'text-slate-600' }}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <dt class="text-sm font-medium {{ $isFieldInvalid ? 'text-red-700' : 'text-slate-700' }}">
                                {{ $label }}
                                @if($isFieldInvalid)
                                    <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                                        Perlu Perbaikan
                                    </span>
                                @endif
                            </dt>
                            <dd class="text-sm {{ $isFieldInvalid ? 'text-red-900' : 'text-slate-900' }} mt-1">
                                @if($key === 'nilai_konversi' && $value)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $isFieldInvalid ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $value }}
                                    </span>
                                @else
                                    {{ $value ?? '-' }}
                                @endif
                            </dd>

                            {{-- Tampilkan catatan dari semua role jika ada --}}
                            @if(!empty($allValidationNotes))
                                <div class="mt-2 space-y-2">
                                    @foreach($allValidationNotes as $note)
                                        <div class="text-xs bg-red-100 p-2 rounded border-l-2 border-red-400">
                                            <div class="flex items-start gap-1">
                                                <i data-lucide="message-square" class="w-3 h-3 mt-0.5 text-red-600"></i>
                                                <div>
                                                    <strong>{{ $note['role'] }}:</strong><br>
                                                    {{ $note['note'] }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($isFieldInvalid)
                                <div class="mt-2 text-xs text-red-700 bg-red-100 p-2 rounded border-l-2 border-red-400">
                                    <div class="flex items-start gap-1">
                                        <i data-lucide="message-square" class="w-3 h-3 mt-0.5 text-red-600"></i>
                                        <div>
                                            <strong>Catatan Perbaikan:</strong><br>
                                            {{ $fieldValidation['keterangan'] }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <input type="hidden" name="snapshot[{{ $key }}]" value="{{ $value }}">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- E. Dokumen Profil -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="bg-gradient-to-r from-rose-500 to-rose-600 text-white p-4 rounded-t-xl">
                <h3 class="text-lg font-semibold flex items-center">
                    <i data-lucide="folder" class="w-5 h-5 mr-3"></i>
                    E. Dokumen Profil
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-5">
                    @php
                        $dokumenProfil = [
                            'ijazah_terakhir'           => ['Ijazah Terakhir', 'graduation-cap'],
                            'transkrip_nilai_terakhir'  => ['Transkrip Nilai Terakhir', 'file-text'],
                            'sk_pangkat_terakhir'       => ['SK Pangkat Terakhir', 'award'],
                            'sk_jabatan_terakhir'       => ['SK Jabatan Terakhir', 'briefcase'],
                            'skp_tahun_pertama'         => ['SKP Tahun ' . (date('Y') - 1), 'target'],
                            'skp_tahun_kedua'           => ['SKP Tahun ' . (date('Y') - 2), 'target'],
                            'pak_konversi'              => ['PAK Konversi ' . (date('Y') - 1), 'refresh-cw'],
                            'sk_cpns'                   => ['SK CPNS', 'user-plus'],
                            'sk_pns'                    => ['SK PNS', 'user-check'],
                            'sk_penyetaraan_ijazah'     => ['SK Penyetaraan Ijazah', 'scale'],
                            'disertasi_thesis_terakhir' => ['Disertasi/Thesis Terakhir', 'book-open'],
                        ];

                        // Tambahkan PAK Integrasi hanya untuk jabatan tertentu
                        if ($pegawai->jabatan && in_array($pegawai->jabatan->jenis_jabatan, ['Dosen Fungsional', 'Tenaga Kependidikan Fungsional Tertentu'])) {
                            $dokumenProfil['pak_integrasi'] = ['PAK Integrasi', 'calculator'];
                        }
                    @endphp

                    @foreach ($dokumenProfil as $key => [$label, $icon])
                        @php
                            // Use hybrid approach for validation checking
                            $isFieldInvalid = isFieldInvalid('dokumen_profil', $key, $validationData ?? [], $catatanPerbaikan ?? []);
                            $allValidationNotes = [];

                            if (isset($isEditMode) && $isEditMode) {
                                $allValidationNotes = getFieldValidationNotes('dokumen_profil', $key, $validationData ?? [], $catatanPerbaikan ?? []);
                            }
                        @endphp

                        <div class="group flex items-center justify-between p-4 {{ $isFieldInvalid ? 'bg-red-50 border border-red-200' : 'bg-slate-50' }} hover:{{ $isFieldInvalid ? 'bg-red-100' : 'bg-slate-100' }} rounded-lg transition-all duration-200">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="bg-white p-2 rounded-lg shadow-sm group-hover:shadow-md transition-shadow">
                                    <i data-lucide="{{ $icon }}" class="w-4 h-4 {{ $isFieldInvalid ? 'text-red-600' : 'text-slate-600' }}"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium {{ $isFieldInvalid ? 'text-red-700' : 'text-slate-700' }} truncate">
                                        {{ $label }}
                                        @if($isFieldInvalid)
                                            <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                                                Perlu Perbaikan
                                            </span>
                                        @endif
                                    </p>
                                    @if ($pegawai->{$key})
                                        <p class="text-xs {{ $isFieldInvalid ? 'text-red-500' : 'text-slate-500' }} mt-1">File tersedia</p>
                                    @else
                                        <p class="text-xs text-slate-400 italic mt-1">Belum diunggah</p>
                                    @endif

                                                                    {{-- Tampilkan catatan dari semua role jika tidak valid --}}
                                    @if($isFieldInvalid && !empty($allValidationNotes))
                                        <div class="mt-2 space-y-2">
                                            @foreach($allValidationNotes as $note)
                                                <div class="text-xs text-red-700 bg-red-100 p-2 rounded border-l-2 border-red-400">
                                                    <div class="flex items-start gap-1">
                                                        <i data-lucide="message-square" class="w-3 h-3 mt-0.5 text-red-600"></i>
                                                        <div>
                                                            <strong>{{ $note['role'] }}:</strong><br>
                                                            {{ $note['note'] }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if ($pegawai->{$key})
                                    <div class="flex items-center gap-1">
                                        <div class="w-2 h-2 {{ $isFieldInvalid ? 'bg-red-400' : 'bg-green-400' }} rounded-full"></div>
                                        <a href="{{ route('pegawai-unmul.profile.show-document', $key) }}" target="_blank"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white {{ $isFieldInvalid ? 'bg-red-600 hover:bg-red-700' : 'bg-indigo-600 hover:bg-indigo-700' }} rounded-lg transition-colors duration-200">
                                            <i data-lucide="eye" class="w-3 h-3"></i>
                                            Lihat
                                        </a>
                                    </div>
                                @else
                                    <div class="flex items-center gap-1">
                                        <div class="w-2 h-2 bg-slate-300 rounded-full"></div>
                                        <span class="px-3 py-1.5 text-xs font-medium text-slate-500 bg-slate-200 rounded-lg">
                                            Tidak ada
                                        </span>
                                    </div>
                                @endif
                                <input type="hidden" name="snapshot[{{ $key }}]" value="{{ $pegawai->{$key} }}">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
