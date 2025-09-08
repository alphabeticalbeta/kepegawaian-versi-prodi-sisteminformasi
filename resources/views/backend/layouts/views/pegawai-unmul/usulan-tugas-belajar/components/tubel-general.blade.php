{{-- Form Usulan Tugas Belajar --}}
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-emerald-600 to-green-600 px-6 py-5">
        <h2 class="text-xl font-bold text-white flex items-center">
            <i data-lucide="file-text" class="w-6 h-6 mr-3"></i>
            Form Usulan Tugas Belajar
        </h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Tahun Studi --}}
            <div>
                <label for="tahun_studi" class="block text-sm font-semibold text-gray-800 mb-2">
                    Tahun Studi <span class="text-red-500">*</span>
                </label>
                <p class="text-xs text-gray-600 mb-2">Tahun akademik yang akan diikuti</p>
                <select id="tahun_studi" name="tahun_studi"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent @if($isViewOnly) bg-gray-100 cursor-not-allowed @endif"
                        @if($isViewOnly) disabled @endif required>
                    <option value="">Pilih Tahun</option>
                    @php
                        $currentYear = date('Y');
                        $startYear = $currentYear - 10;
                        $endYear = $currentYear + 1;
                    @endphp
                    @for($year = $startYear; $year <= $endYear; $year++)
                        <option value="{{ $year }}"
                                {{ old('tahun_studi', $usulan->data_usulan['tahun_studi'] ?? '') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endfor
                </select>
                @error('tahun_studi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Alamat --}}
            <div>
                <label for="alamat" class="block text-sm font-semibold text-gray-800 mb-2">
                    Alamat Lengkap <span class="text-red-500">*</span>
                </label>
                <p class="text-xs text-gray-600 mb-2">Alamat tempat tinggal selama tugas belajar</p>
                <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent @if($isViewOnly) bg-gray-100 cursor-not-allowed @endif"
                          @if($isViewOnly) disabled @endif required>{{ old('alamat_lengkap', $usulan->data_usulan['alamat_lengkap'] ?? '') }}</textarea>
                @error('alamat_lengkap')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Jenjang Pendidikan --}}
            <div>
                <label for="jenjang_pendidikan" class="block text-sm font-semibold text-gray-800 mb-2">
                    Pendidikan yang Ditempuh <span class="text-red-500">*</span>
                </label>
                <p class="text-xs text-gray-600 mb-2">Jenjang pendidikan yang akan diambil</p>
                <select id="pendidikan_ditempuh" name="pendidikan_ditempuh"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent @if($isViewOnly) bg-gray-100 cursor-not-allowed @endif"
                        @if($isViewOnly) disabled @endif required>
                    <option value="">Pilih Jenjang</option>
                    @php
                        $pendidikanOptions = [
                            'Sekolah Dasar (SD)',
                            'Sekolah Lanjutan Tingkat Pertama (SLTP) / Sederajat',
                            'Sekolah Lanjutan Tingkat Menengah (SLTA)',
                            'Diploma I',
                            'Diploma II',
                            'Diploma III',
                            'Sarjana (S1) / Diploma IV / Sederajat',
                            'Magister (S2) / Sederajat',
                            'Doktor (S3) / Sederajat'
                        ];

                        // Ambil pendidikan terakhir pegawai
                        $pendidikanTerakhir = $usulan->pegawai->pendidikan_terakhir ?? '';

                        // Tentukan level pendidikan terakhir
                        $currentLevel = 0;
                        if (str_contains($pendidikanTerakhir, 'S3') || str_contains($pendidikanTerakhir, 'Doktor')) {
                            $currentLevel = 8;
                        } elseif (str_contains($pendidikanTerakhir, 'S2') || str_contains($pendidikanTerakhir, 'Magister')) {
                            $currentLevel = 7;
                        } elseif (str_contains($pendidikanTerakhir, 'S1') || str_contains($pendidikanTerakhir, 'Sarjana')) {
                            $currentLevel = 6;
                        } elseif (str_contains($pendidikanTerakhir, 'Diploma III')) {
                            $currentLevel = 5;
                        } elseif (str_contains($pendidikanTerakhir, 'Diploma II')) {
                            $currentLevel = 4;
                        } elseif (str_contains($pendidikanTerakhir, 'Diploma I')) {
                            $currentLevel = 3;
                        } elseif (str_contains($pendidikanTerakhir, 'SLTA')) {
                            $currentLevel = 2;
                        } elseif (str_contains($pendidikanTerakhir, 'SLTP')) {
                            $currentLevel = 1;
                        } elseif (str_contains($pendidikanTerakhir, 'SD')) {
                            $currentLevel = 0;
                        }

                        // Filter opsi yang lebih tinggi dari pendidikan terakhir
                        $filteredOptions = [];
                        foreach ($pendidikanOptions as $index => $option) {
                            if ($index > $currentLevel) {
                                $filteredOptions[] = $option;
                            }
                        }
                    @endphp
                    @foreach($filteredOptions as $option)
                        <option value="{{ $option }}"
                                {{ old('pendidikan_ditempuh', $usulan->data_usulan['pendidikan_ditempuh'] ?? '') == $option ? 'selected' : '' }}>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
                @error('pendidikan_ditempuh')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Program Studi --}}
            <div>
                <label for="program_studi" class="block text-sm font-semibold text-gray-800 mb-2">
                    Nama Prodi yang Dituju <span class="text-red-500">*</span>
                </label>
                <p class="text-xs text-gray-600 mb-2">Program studi yang akan diambil</p>
                <input type="text" id="nama_prodi_dituju" name="nama_prodi_dituju"
                       value="{{ old('nama_prodi_dituju', $usulan->data_usulan['nama_prodi_dituju'] ?? '') }}"
                       placeholder="Prodi Doktor Manajemen atau Prodi Magister Manajemen"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent @if($isViewOnly) bg-gray-100 cursor-not-allowed @endif"
                       @if($isViewOnly) disabled @endif required>
                @error('nama_prodi_dituju')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nama Fakultas yang Dituju --}}
            <div>
                <label for="fakultas_tujuan" class="block text-sm font-semibold text-gray-800 mb-2">
                    Nama Fakultas yang Dituju <span class="text-red-500">*</span>
                </label>
                <p class="text-xs text-gray-600 mb-2">Fakultas tempat program studi berada</p>
                <input type="text" id="nama_fakultas_dituju" name="nama_fakultas_dituju"
                       value="{{ old('nama_fakultas_dituju', $usulan->data_usulan['nama_fakultas_dituju'] ?? '') }}"
                       placeholder="Fakultas Ekonomi dan Bisnis"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent @if($isViewOnly) bg-gray-100 cursor-not-allowed @endif"
                       @if($isViewOnly) disabled @endif required>
                @error('nama_fakultas_dituju')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Universitas Tujuan --}}
            <div>
                <label for="universitas_tujuan" class="block text-sm font-semibold text-gray-800 mb-2">
                    Nama Universitas yang Dituju <span class="text-red-500">*</span>
                </label>
                <p class="text-xs text-gray-600 mb-2">Universitas tempat tugas belajar</p>
                <input type="text" id="nama_universitas_dituju" name="nama_universitas_dituju"
                       value="{{ old('nama_universitas_dituju', $usulan->data_usulan['nama_universitas_dituju'] ?? '') }}" placeholder="Universitas Mulawarman"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent @if($isViewOnly) bg-gray-100 cursor-not-allowed @endif"
                       @if($isViewOnly) disabled @endif required>
                @error('nama_universitas_dituju')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Negara Studi --}}
            <div>
                <label for="negara_studi" class="block text-sm font-semibold text-gray-800 mb-2">
                    Negara Studi <span class="text-red-500">*</span>
                </label>
                <p class="text-xs text-gray-600 mb-2">Lokasi tempat studi akan dilaksanakan</p>
                <select id="negara_studi" name="negara_studi"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent @if($isViewOnly) bg-gray-100 cursor-not-allowed @endif"
                        @if($isViewOnly) disabled @endif required>
                    <option value="">Pilih Negara Studi</option>
                    <option value="Dalam Negeri" {{ old('negara_studi', $usulan->data_usulan['negara_studi'] ?? '') == 'Dalam Negeri' ? 'selected' : '' }}>Dalam Negeri</option>
                    <option value="Luar Negeri" {{ old('negara_studi', $usulan->data_usulan['negara_studi'] ?? '') == 'Luar Negeri' ? 'selected' : '' }}>Luar Negeri</option>
                </select>
                @error('negara_studi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>
    {{-- Section Dokumen Pendukung --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-5">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i data-lucide="folder-open" class="w-6 h-6 mr-3"></i>
                Dokumen Pendukung
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Dokumen Setneg (Hanya untuk Luar Negeri) --}}
                <div id="dokumen-setneg-container" style="display: none;">
                    <label class="block text-sm font-semibold text-gray-800 mb-2">
                        Dokumen Setneg <span class="text-red-500">*</span>
                    </label>
                    <p class="text-xs text-gray-600 mb-2">Dokumen persetujuan dari Sekretariat Negara (PDF, maksimal 1MB)</p>

                    @if(isset($usulan->data_usulan['dokumen_usulan']['dokumen_setneg']['path']))
                        <div class="space-y-3">
                            <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                                <span class="text-sm text-blue-800">Dokumen Setneg sudah diupload</span>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                                <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-800">Dokumen Setneg</div>
                                </div>
                                <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'dokumen_setneg']) }}"
                                   target="_blank"
                                   class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                    Lihat
                                </a>
                                <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'dokumen_setneg']) }}?download=1"
                                   class="inline-flex items-center gap-2 px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                    <i data-lucide="download" class="w-4 h-4"></i>
                                    Download
                                </a>
                            </div>

                            @if(!$isViewOnly)
                            <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="info" class="w-4 h-4 text-yellow-600"></i>
                                    <span class="text-sm text-yellow-800">Anda dapat mengganti dokumen dengan mengupload file baru di bawah ini</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="flex items-center gap-3 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                            <i data-lucide="file-x" class="w-5 h-5 text-gray-600"></i>
                            <span class="text-sm text-gray-600">Dokumen belum diupload</span>
                        </div>
                    @endif

                    @if(!$isViewOnly)
                        <div class="mt-4">
                            <label class="block text-sm font-semibold text-gray-800 mb-2">
                                @if(isset($usulan->data_usulan['dokumen_usulan']['dokumen_setneg']['path']))
                                    Ganti Dokumen
                                @else
                                    Upload Dokumen
                                @endif
                            </label>
                            <input type="file" id="dokumen_setneg" name="dokumen_setneg" accept=".pdf"
                                   class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('dokumen_setneg') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                            @error('dokumen_setneg')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>

                {{-- Kartu Pegawai atau Kartu Virtual ASN --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2">
                        Kartu Pegawai atau Kartu Virtual ASN <span class="text-red-500">*</span>
                    </label>
                    <p class="text-xs text-gray-600 mb-2">Kartu identitas pegawai atau kartu virtual ASN (PDF, maksimal 1MB)</p>

                    @if(isset($usulan->data_usulan['dokumen_usulan']['kartu_pegawai']['path']))
                        <div class="space-y-3">
                            <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                                <span class="text-sm text-blue-800">Kartu Pegawai sudah diupload</span>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                                <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-800">Kartu Pegawai atau Kartu Virtual ASN</div>
                                </div>
                                <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'kartu_pegawai']) }}"
                                   target="_blank"
                                   class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                    Lihat
                                </a>
                                <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'kartu_pegawai']) }}?download=1"
                                   class="inline-flex items-center gap-2 px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                    <i data-lucide="download" class="w-4 h-4"></i>
                                    Download
                                </a>
                            </div>

                            @if(!$isViewOnly)
                            <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="info" class="w-4 h-4 text-yellow-600"></i>
                                    <span class="text-sm text-yellow-800">Anda dapat mengganti dokumen dengan mengupload file baru di bawah ini</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="flex items-center gap-3 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                            <i data-lucide="file-x" class="w-5 h-5 text-gray-600"></i>
                            <span class="text-sm text-gray-600">Dokumen belum diupload</span>
                        </div>
                    @endif

                    @if(!$isViewOnly)
                        <div class="mt-4">
                            <label class="block text-sm font-semibold text-gray-800 mb-2">
                                @if(isset($usulan->data_usulan['dokumen_usulan']['kartu_pegawai']['path']))
                                    Ganti Dokumen
                                @else
                                    Upload Dokumen
                                @endif
                            </label>
                            <input type="file" id="kartu_pegawai" name="kartu_pegawai" accept=".pdf"
                                   class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kartu_pegawai') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                            @error('kartu_pegawai')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>
                {{-- Section Dokumen Tugas Belajar - Hanya tampil jika jenis usulan adalah "Tugas Belajar" --}}
                @if($usulan->jenis_tubel === 'Tugas Belajar')
                    @include('backend.layouts.views.pegawai-unmul.usulan-tugas-belajar.components.dokumen-tubel')
                @endif

                {{-- Section Dokumen Perpanjangan Tugas Belajar - Hanya tampil jika jenis usulan adalah "Usul Perpanjangan Tugas Belajar" --}}
                @if($usulan->jenis_tubel === 'Perpanjangan Tugas Belajar')
                    @include('backend.layouts.views.pegawai-unmul.usulan-tugas-belajar.components.dokumen-perpanjangan-tuble')
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const negaraStudiSelect = document.getElementById('negara_studi');
    const dokumenSetnegContainer = document.getElementById('dokumen-setneg-container');
    const dokumenSetnegInput = document.getElementById('dokumen_setneg');

    // Function to toggle dokumen setneg field
    function toggleDokumenSetneg() {
        if (negaraStudiSelect.value === 'Luar Negeri') {
            dokumenSetnegContainer.style.display = 'block';
            dokumenSetnegInput.required = true;
        } else {
            dokumenSetnegContainer.style.display = 'none';
            dokumenSetnegInput.required = false;
            dokumenSetnegInput.value = ''; // Clear the file input
        }
    }

    // Initial check
    toggleDokumenSetneg();

    // Add event listener for change
    negaraStudiSelect.addEventListener('change', toggleDokumenSetneg);

    // File size validation for dokumen setneg
    dokumenSetnegInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Check file size (1MB = 1024 * 1024 bytes)
            if (file.size > 1024 * 1024) {
                alert('Ukuran file maksimal 1MB');
                this.value = '';
                return;
            }

            // Check file type
            if (file.type !== 'application/pdf') {
                alert('File harus berformat PDF');
                this.value = '';
                return;
            }
        }
    });

    // File size validation for kartu pegawai
    const kartuPegawaiInput = document.getElementById('kartu_pegawai');
    kartuPegawaiInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Check file size (1MB = 1024 * 1024 bytes)
            if (file.size > 1024 * 1024) {
                alert('Ukuran file maksimal 1MB');
                this.value = '';
                return;
            }

            // Check file type
            if (file.type !== 'application/pdf') {
                alert('File harus berformat PDF');
                this.value = '';
                return;
            }
        }
    });
});
</script>
