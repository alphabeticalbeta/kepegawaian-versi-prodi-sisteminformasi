{{-- components/karya-ilmiah-section.blade.php --}}
{{-- Enhanced component dengan sistem validasi untuk karya ilmiah --}}

@if($formConfig['show_karya_ilmiah'])
<div class="bg-white p-8 rounded-xl shadow-lg mb-6 border border-gray-100">
    <!-- Header -->
    <div class="bg-gradient-to-r {{ $formConfig['gradient_colors'] }} -m-8 mb-8 p-6 rounded-t-xl ">
        <h2 class="text-2xl font-bold text-black flex items-center">
            <i data-lucide="book-open" class="w-6 h-6 mr-3"></i>
            Karya Ilmiah &amp; Artikel
        </h2>
        <p class="text-black/90 mt-2">Lengkapi informasi karya ilmiah dan artikel yang akan disubmit</p>
    </div>

    <!-- Jenis Karya Ilmiah -->
    <div class="mb-8">
        @php
            // Cek validasi untuk field jenis karya ilmiah dari semua role
            $karyaIlmiahValidation = null;
            $isKaryaIlmiahInvalid = false;
            $allValidationNotes = [];

            if (isset($validationData) && !empty($validationData)) {
                $allValidationNotes = getAllValidationNotes('karya_ilmiah', 'jenis_karya', $validationData);
                $isKaryaIlmiahInvalid = hasValidationIssue('karya_ilmiah', 'jenis_karya', $validationData);
            } else {
                // Fallback to old structure
                $karyaIlmiahValidation = $catatanPerbaikan['karya_ilmiah']['jenis_karya'] ?? null;
                $isKaryaIlmiahInvalid = $karyaIlmiahValidation && $karyaIlmiahValidation['status'] === 'tidak_sesuai';
            }
        @endphp

        <div class="bg-gray-50 rounded-lg p-6 border-l-4 {{ $isKaryaIlmiahInvalid ? 'border-red-500 bg-red-50' : 'border-indigo-500' }}">
            <label for="karya_ilmiah" class="block text-sm font-semibold {{ $isKaryaIlmiahInvalid ? 'text-red-800' : 'text-gray-800' }} mb-3">
                <i data-lucide="graduation-cap" class="w-4 h-4 inline mr-2"></i>
                Karya Ilmiah
                @if($formConfig['karya_ilmiah_required'] && !$isReadOnly)
                    <span class="text-red-500">*</span>
                @endif
                @if($isKaryaIlmiahInvalid)
                    <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                        Perlu Perbaikan
                    </span>
                @endif
            </label>
            <select id="karya_ilmiah" name="karya_ilmiah"
                class="block w-full border rounded-lg shadow-sm py-3 px-4
                    @if($isReadOnly) bg-gray-50 text-gray-600 @else bg-white @endif
                    @if($isKaryaIlmiahInvalid) border-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @endif"
                @if($isReadOnly) disabled @endif
                @if($formConfig['karya_ilmiah_required'] && !$isReadOnly) required @endif>
                <option value="">-- Pilih Jenis Karya Ilmiah --</option>
                @foreach($formConfig['karya_ilmiah_options'] as $option)
                    <option value="{{ $option }}"
                        {{ old('karya_ilmiah', $usulan->data_usulan['karya_ilmiah']['jenis_karya'] ?? '') == $option ? 'selected' : '' }}>
                        {{ $option }}
                    </option>
                @endforeach
            </select>
            @error('karya_ilmiah')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror

            {{-- Tampilkan catatan dari semua role jika tidak valid --}}
            @if($isKaryaIlmiahInvalid && !empty($allValidationNotes))
                <div class="mt-3 space-y-2">
                    @foreach($allValidationNotes as $note)
                        <div class="text-xs text-red-700 bg-red-100 p-3 rounded border-l-2 border-red-400">
                            <div class="flex items-start gap-2">
                                <i data-lucide="message-square" class="w-4 h-4 mt-0.5 text-red-600"></i>
                                <div>
                                    <strong>{{ $note['role'] }}:</strong><br>
                                    {{ $note['note'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @elseif($isKaryaIlmiahInvalid && $karyaIlmiahValidation && isset($karyaIlmiahValidation['keterangan']))
                {{-- Fallback untuk struktur data lama --}}
                <div class="mt-3 text-xs text-red-700 bg-red-100 p-3 rounded border-l-2 border-red-400">
                    <div class="flex items-start gap-2">
                        <i data-lucide="message-square" class="w-4 h-4 mt-0.5 text-red-600"></i>
                        <div>
                            <strong>Catatan Perbaikan:</strong><br>
                            {{ $karyaIlmiahValidation['keterangan'] }}
                        </div>
                    </div>
                </div>
            @endif

            @if(!$formConfig['karya_ilmiah_required'])
                <p class="text-xs {{ $isKaryaIlmiahInvalid ? 'text-red-600' : 'text-gray-500' }} mt-2">
                    <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                    Untuk jenjang ini, karya ilmiah bersifat opsional sebagai nilai tambah.
                </p>
            @endif
        </div>
    </div>

    <!-- Form Fields Grid -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
            <i data-lucide="file-text" class="w-5 h-5 mr-2 text-indigo-600"></i>
            Informasi Artikel
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @php
                // Definisikan fields berdasarkan jenjang
                $baseFields = [
                    'nama_jurnal'       => ['Nama Jurnal', 'Nama jurnal tempat terbit', 'bookmark', 'md:col-span-2'],
                    'judul_artikel'     => ['Judul Artikel', 'Judul artikel ilmiah', 'type', 'md:col-span-2'],
                ];

                $advancedFields = [
                    'penerbit_artikel'  => ['Penerbit Artikel', 'Nama penerbit', 'building', ''],
                    'volume_artikel'    => ['Volume Artikel', 'Volume (edisi)', 'layers', ''],
                    'nomor_artikel'     => ['Nomor Artikel', 'Nomor artikel / issue', 'hash', ''],
                    'edisi_artikel'     => ['Edisi Artikel (Tahun)', 'Edisi atau tahun artikel', 'calendar', ''],
                    'halaman_artikel'   => ['Halaman Artikel', 'Halaman artikel', 'file-minus', ''],
                ];

                // Untuk jenjang rendah, hanya tampilkan field dasar
                $textFields = $jenjangType === 'tenaga-pengajar-to-asisten-ahli'
                    ? $baseFields
                    : array_merge($baseFields, $advancedFields);

                // Link fields - semua jenjang sama
                $linkFields = [
                    'link_artikel' => ['Link Artikel', 'Tautan ke artikel online (wajib diisi)', 'link', 'md:col-span-2', true],
                    'link_sinta'   => ['Link SINTA', 'Tautan ke profil SINTA (opsional)', 'link-2', 'md:col-span-2', false],
                    'link_scopus'  => ['Link SCOPUS', 'Tautan ke profil SCOPUS (opsional)', 'link-2', 'md:col-span-2', false],
                    'link_scimago' => ['Link SCIMAGO', 'Tautan ke profil SCIMAGO (opsional)', 'link-2', 'md:col-span-2', false],
                    'link_wos'     => ['Link WoS', 'Tautan ke profil WoS (opsional)', 'link-2', 'md:col-span-2', false],
                ];

                // Untuk Guru Besar, link internasional lebih penting
                if ($jenjangType === 'lektor-kepala-to-guru-besar') {
                    $linkFields['link_scopus'][4] = true; // Make scopus required
                }
            @endphp

            {{-- PERULANGAN UNTUK INPUT TEKS --}}
            @foreach ($textFields as $name => [$label, $placeholder, $icon, $colSpan])
                                    @php
                        // Cek validasi untuk field ini dari semua role
                        $fieldValidation = null;
                        $isFieldInvalid = false;
                        $fieldValidationNotes = [];

                        if (isset($validationData) && !empty($validationData)) {
                            $fieldValidationNotes = getAllValidationNotes('karya_ilmiah', $name, $validationData);
                            $isFieldInvalid = hasValidationIssue('karya_ilmiah', $name, $validationData);
                        } else {
                            // Fallback to old structure
                            $fieldValidation = $catatanPerbaikan['karya_ilmiah'][$name] ?? null;
                            $isFieldInvalid = $fieldValidation && $fieldValidation['status'] === 'tidak_sesuai';
                        }
                    @endphp

                <div class="{{ $colSpan }}">
                    <label for="{{ $name }}" class="block text-sm font-medium {{ $isFieldInvalid ? 'text-red-700' : 'text-gray-700' }} mb-2">
                        <i data-lucide="{{ $icon }}" class="w-4 h-4 inline mr-1"></i>
                        {{ $label }}
                        @if($formConfig['karya_ilmiah_required'] && !$isReadOnly)
                            <span class="text-red-500">*</span>
                        @endif
                        @if($isFieldInvalid)
                            <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                                Perlu Perbaikan
                            </span>
                        @endif
                    </label>
                    <input id="{{ $name }}" name="{{ $name }}" type="text"
                        value="{{ old($name, $usulan->data_usulan['karya_ilmiah'][$name] ?? '') }}"
                        class="block w-full border rounded-lg shadow-sm py-3 px-4
                            @if($isReadOnly) bg-gray-50 text-gray-600 @else bg-white @endif
                            @if($isFieldInvalid) border-red-300 focus:border-red-500 focus:ring-red-500 bg-red-50 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @endif"
                        placeholder="{{ $placeholder }}"
                        @if($formConfig['karya_ilmiah_required'] && !$isReadOnly) required @endif
                        @if($isReadOnly) readonly @endif>
                    @error($name)<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror

                    {{-- Tampilkan catatan spesifik dari admin jika tidak valid --}}
                    @if($isFieldInvalid && isset($fieldValidation['keterangan']))
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
                </div>
            @endforeach

            {{-- PERULANGAN UNTUK INPUT LINK --}}
            @foreach ($linkFields as $name => [$label, $placeholder, $icon, $colSpan, $isRequired])
                @php
                    // Cek validasi untuk field link ini dari semua role
                    $fieldValidation = null;
                    $isFieldInvalid = false;
                    $fieldValidationNotes = [];

                    if (isset($validationData) && !empty($validationData)) {
                        $fieldValidationNotes = getAllValidationNotes('karya_ilmiah', $name, $validationData);
                        $isFieldInvalid = hasValidationIssue('karya_ilmiah', $name, $validationData);
                    } else {
                        // Fallback to old structure
                        $fieldValidation = $catatanPerbaikan['karya_ilmiah'][$name] ?? null;
                        $isFieldInvalid = $fieldValidation && $fieldValidation['status'] === 'tidak_sesuai';
                    }

                    $fieldMapping = [
                        'link_artikel' => 'artikel',
                        'link_sinta' => 'sinta',
                        'link_scopus' => 'scopus',
                        'link_scimago' => 'scimago',
                        'link_wos' => 'wos'
                    ];
                    $mappedField = $fieldMapping[$name] ?? $name;

                    // Akses data dari struktur baru dengan fallback ke struktur lama
                    $fieldValue = '';
                    if (isset($usulan->data_usulan['karya_ilmiah']['links'][$mappedField])) {
                        $fieldValue = $usulan->data_usulan['karya_ilmiah']['links'][$mappedField];
                    } elseif (isset($usulan->data_usulan[$name])) {
                        $fieldValue = $usulan->data_usulan[$name];
                    }
                @endphp

                <div class="{{ $colSpan }}">
                    <label for="{{ $name }}" class="block text-sm font-medium {{ $isFieldInvalid ? 'text-red-700' : 'text-gray-700' }} mb-2">
                        <i data-lucide="{{ $icon }}" class="w-4 h-4 inline mr-1"></i>
                        {{ $label }}
                        @if(($isRequired || $formConfig['karya_ilmiah_required']) && !$isReadOnly)
                            <span class="text-red-500">*</span>
                        @endif
                        @if($isFieldInvalid)
                            <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                                Perlu Perbaikan
                            </span>
                        @endif
                    </label>
                    <input id="{{ $name }}" name="{{ $name }}" type="url"
                        value="{{ old($name, $fieldValue) }}"
                        class="block w-full border rounded-lg shadow-sm py-3 px-4
                            @if($isReadOnly) bg-gray-50 text-gray-600 @else bg-white @endif
                            @if($isFieldInvalid) border-red-300 focus:border-red-500 focus:ring-red-500 bg-red-50 @else border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @endif"
                        placeholder="{{ $placeholder }}"
                        @if(($isRequired || $formConfig['karya_ilmiah_required']) && !$isReadOnly) required @endif
                        @if($isReadOnly) readonly @endif>
                    @error($name)<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror

                    {{-- Tampilkan catatan dari semua role jika tidak valid --}}
                    @if($isFieldInvalid && !empty($fieldValidationNotes))
                        <div class="mt-2 space-y-1">
                            @foreach($fieldValidationNotes as $note)
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
                    @elseif($isFieldInvalid && $fieldValidation && isset($fieldValidation['keterangan']))
                        {{-- Fallback untuk struktur data lama --}}
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
                </div>
            @endforeach
        </div>
    </div>

    <!-- Jenjang-specific info with validation awareness -->
    @php
        // Cek apakah ada error di section karya ilmiah dari semua role
        $hasKaryaIlmiahErrors = false;
        if (isset($validationData) && !empty($validationData)) {
            foreach ($validationData as $role => $data) {
                if (isset($data['karya_ilmiah'])) {
                    foreach ($data['karya_ilmiah'] as $field => $validation) {
                        if (isset($validation['status']) && $validation['status'] === 'tidak_sesuai') {
                            $hasKaryaIlmiahErrors = true;
                            break 2;
                        }
                    }
                }
            }
        } else {
            // Fallback to old structure
            $hasKaryaIlmiahErrors = collect($catatanPerbaikan['karya_ilmiah'] ?? [])
                ->contains(function ($validation) {
                    return $validation['status'] === 'tidak_sesuai';
                });
        }
    @endphp

    @if($jenjangType === 'tenaga-pengajar-to-asisten-ahli')
        <div class="mb-6 p-4 {{ $hasKaryaIlmiahErrors ? 'bg-red-50 border-l-4 border-red-400' : 'bg-green-50 border-l-4 border-green-400' }} rounded-r-lg">
            <div class="flex">
                @if($hasKaryaIlmiahErrors)
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600 mr-3 mt-0.5"></i>
                @else
                    <i data-lucide="info" class="w-5 h-5 text-green-600 mr-3 mt-0.5"></i>
                @endif
                <div class="ml-3">
                    @if($hasKaryaIlmiahErrors)
                        <p class="text-sm text-red-700">
                            <strong>Perhatian:</strong> Terdapat field karya ilmiah yang perlu diperbaiki. Meskipun opsional, kelengkapan dan keakuratan data akan memperkuat usulan Anda.
                        </p>
                    @else
                        <p class="text-sm text-green-700">
                            <strong>Catatan untuk Tenaga Pengajar:</strong> Karya ilmiah bersifat opsional namun sangat direkomendasikan untuk memperkuat usulan Anda.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    @elseif($jenjangType === 'lektor-kepala-to-guru-besar')
        <div class="mb-6 p-4 {{ $hasKaryaIlmiahErrors ? 'bg-red-50 border-l-4 border-red-400' : 'bg-purple-50 border-l-4 border-purple-400' }} rounded-r-lg">
            <div class="flex">
                @if($hasKaryaIlmiahErrors)
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600 mr-3 mt-0.5"></i>
                @else
                    <i data-lucide="info" class="w-5 h-5 text-purple-600 mr-3 mt-0.5"></i>
                @endif
                <div class="ml-3">
                    @if($hasKaryaIlmiahErrors)
                        <p class="text-sm text-red-700">
                            <strong>Perhatian Persyaratan Guru Besar:</strong> Terdapat field karya ilmiah yang perlu diperbaiki. Untuk usulan Guru Besar, karya ilmiah harus berupa jurnal internasional bereputasi dengan impact factor yang terukur.
                        </p>
                    @else
                        <p class="text-sm text-purple-700">
                            <strong>Persyaratan Guru Besar:</strong> Karya ilmiah wajib berupa jurnal internasional bereputasi dengan impact factor yang terukur.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    @else
        @if($hasKaryaIlmiahErrors)
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-r-lg">
                <div class="flex">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600 mr-3 mt-0.5"></i>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            <strong>Perhatian:</strong> Terdapat field karya ilmiah yang perlu diperbaiki. Silakan periksa dan perbaiki sesuai catatan yang diberikan.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- Summary Error Count untuk Section Karya Ilmiah --}}
    @if($hasKaryaIlmiahErrors)
        @php
            $errorCount = collect($catatanPerbaikan['karya_ilmiah'] ?? [])
                ->where('status', 'tidak_sesuai')
                ->count();
        @endphp
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center gap-3">
                <div class="bg-red-100 p-2 rounded-lg">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-red-800">
                        Ringkasan Perbaikan Karya Ilmiah
                    </h4>
                    <p class="text-xs text-red-700 mt-1">
                        {{ $errorCount }} field perlu diperbaiki dari total {{ count($textFields) + count($linkFields) + 1 }} field
                    </p>
                    <div class="mt-2">
                        <div class="w-full bg-red-200 rounded-full h-2">
                            <div class="bg-red-600 h-2 rounded-full"
                                 style="width: {{ ($errorCount / (count($textFields) + count($linkFields) + 1)) * 100 }}%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endif
