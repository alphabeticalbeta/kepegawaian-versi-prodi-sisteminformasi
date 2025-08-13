@extends('backend.layouts.admin-fakultas.app')

@section('title', 'Validasi Detail Usulan Jabatan')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Header Halaman --}}
        <div class="mb-8">
            <a href="{{ route('admin-fakultas.periode.pendaftar', $usulan->periode_usulan_id) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 inline-flex items-center">
                <svg class="h-5 w-5 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Kembali ke Daftar Pengusul
            </a>
            <h1 class="text-2xl font-bold text-gray-800 mt-2">Validasi Usulan: {{ $usulan->pegawai->nama_lengkap ?? 'N/A' }}</h1>
            <p class="text-sm text-gray-500">Lakukan validasi terhadap setiap item data usulan pegawai.</p>
        </div>

        {{-- Alert Messages --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6" role="alert">
                <div class="flex">
                    <div class="py-1">
                        <svg class="fill-current h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg>
                    </div>
                    <div>
                        <p class="font-bold">Sukses!</p>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6" role="alert">
                <p class="font-medium">{{ session('error') }}</p>
            </div>
        @endif

        {{-- Form Validasi - PASTIKAN METHOD POST --}}
        <form action="{{ route('admin-fakultas.usulan.save-validation', $usulan->id) }}" method="POST" id="validationForm" enctype="multipart/form-data" novalidate>
            @csrf

            {{-- Card: Informasi Usulan --}}
            <div class="bg-white shadow-md rounded-lg mb-6">
                <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-800">Informasi Usulan</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Nama Lengkap</p>
                        <p class="text-gray-800">{{ $usulan->pegawai->nama_lengkap ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">NIP</p>
                        <p class="text-gray-800">{{ $usulan->pegawai->nip ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Jabatan Tujuan</p>
                        <p class="text-gray-800">{{ $usulan->jabatanTujuan->jabatan ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Status Saat Ini</p>
                        <span class="px-2 py-1 text-sm font-medium bg-blue-100 text-blue-800 rounded-full">{{ $usulan->status_usulan }}</span>
                    </div>
                </div>
            </div>

            {{-- Form Validasi Data --}}
            @foreach($validationFields as $category => $fields)
                <div class="bg-white shadow-md rounded-lg mb-6 overflow-x-auto">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white">
                            {{ ucwords(str_replace('_', ' ', $category)) }}
                        </h3>
                    </div>

                    {{-- Table with 3 rows horizontal --}}
                    <table class="w-full table-fixed">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="w-1/3 px-4 py-3 text-left text-sm font-medium text-gray-700">Data Usulan Pegawai</th>
                                <th class="w-1/3 px-4 py-3 text-center text-sm font-medium text-gray-700">Status Validasi</th>
                                <th class="w-1/3 px-4 py-3 text-center text-sm font-medium text-gray-700">Keterangan (Jika Tidak Sesuai)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($fields as $field)
                                @php
                                    $fieldValue = '-';

                                    // Handle data pribadi dan kepegawaian dari pegawai
                                    if (in_array($category, ['data_pribadi', 'data_kepegawaian'])) {
                                        if ($field === 'pangkat_saat_usul') {
                                            $fieldValue = $usulan->pegawai->pangkat->pangkat ?? '-';
                                        } elseif ($field === 'jabatan_saat_usul') {
                                            $fieldValue = $usulan->pegawai->jabatan->jabatan ?? '-';
                                        } elseif ($field === 'unit_kerja_saat_usul') {
                                            $fieldValue = $usulan->pegawai->unitKerja->nama ?? '-';
                                        } elseif (in_array($field, ['tanggal_lahir', 'tmt_pangkat', 'tmt_jabatan', 'tmt_cpns', 'tmt_pns'])) {
                                            $rawValue = $usulan->pegawai->{$field} ?? null;
                                            $fieldValue = $rawValue ? \Carbon\Carbon::parse($rawValue)->isoFormat('D MMMM YYYY') : '-';
                                        } else {
                                            $fieldValue = $usulan->pegawai->{$field} ?? '-';
                                        }
                                    }

                                    // Handle data pendidikan dan kinerja
                                    elseif (in_array($category, ['data_pendidikan', 'data_kinerja'])) {
                                        $fieldValue = $usulan->pegawai->{$field} ?? '-';
                                    }

                                    // Handle dokumen profil - LINK HANYA TAMPIL "Lihat Dokumen"
                                    elseif ($category === 'dokumen_profil') {
                                        $dokumenPath = $usulan->pegawai->{$field} ?? null;
                                        if (!empty($dokumenPath)) {
                                            $fieldValue = '<a href="' . asset('storage/' . $dokumenPath) . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                ✓ Lihat Dokumen
                                            </a>';
                                        } else {
                                            $fieldValue = '✗ Belum diunggah';
                                        }
                                    }

                                    // Handle karya ilmiah - PERBAIKAN LOGIC
                                    elseif ($category === 'karya_ilmiah') {
                                        if ($field === 'karya_ilmiah') {
                                            $fieldValue = $usulan->data_usulan['karya_ilmiah']['jenis_karya'] ??
                                                         $usulan->data_usulan['karya_ilmiah'] ?? '-';
                                        } elseif (in_array($field, ['link_artikel', 'link_sinta', 'link_scopus', 'link_scimago', 'link_wos'])) {
                                            // Cek di struktur links dulu
                                            $linkKey = str_replace('link_', '', $field);
                                            $linkValue = $usulan->data_usulan['karya_ilmiah']['links'][$linkKey] ??
                                                         $usulan->data_usulan['karya_ilmiah'][$field] ??
                                                         $usulan->data_usulan[$field] ?? '-';

                                            if ($linkValue !== '-' && filter_var($linkValue, FILTER_VALIDATE_URL)) {
                                                // Nama link yang user-friendly
                                                $linkNames = [
                                                    'link_artikel' => 'Lihat Artikel',
                                                    'link_sinta' => 'Lihat Profil SINTA',
                                                    'link_scopus' => 'Lihat Profil SCOPUS',
                                                    'link_scimago' => 'Lihat Profil SCIMAGO',
                                                    'link_wos' => 'Lihat Profil WoS'
                                                ];
                                                $linkText = $linkNames[$field] ?? 'Lihat Link';

                                                $fieldValue = '<a href="' . $linkValue . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                    </svg>
                                                    ' . $linkText . '
                                                </a>';
                                            } else {
                                                $fieldValue = $linkValue;
                                            }
                                        } else {
                                            // Field karya ilmiah lainnya
                                            $fieldValue = $usulan->data_usulan['karya_ilmiah'][$field] ??
                                                         $usulan->data_usulan[$field] ?? '-';
                                        }
                                    }

                                    // Handle dokumen usulan - LINK HANYA TAMPIL "Lihat Dokumen"
                                    elseif ($category === 'dokumen_usulan') {
                                        // Cek di struktur baru dulu
                                        if (!empty($usulan->data_usulan['dokumen_usulan'][$field]['path'])) {
                                            $fieldValue = '<a href="' . route('admin-fakultas.usulan.show-document', ['usulan' => $usulan->id, 'field' => $field]) . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                ✓ Lihat Dokumen
                                            </a>';
                                        }
                                        // Fallback ke struktur lama
                                        elseif (!empty($usulan->data_usulan[$field])) {
                                            $fieldValue = '<a href="' . route('admin-fakultas.usulan.show-document', ['usulan' => $usulan->id, 'field' => $field]) . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                ✓ Lihat Dokumen
                                            </a>';
                                        }
                                        else {
                                            $fieldValue = '✗ Belum diunggah';
                                        }
                                    }

                                    // Get existing validation
                                    $currentStatus = $existingValidation[$category][$field]['status'] ?? 'sesuai';
                                    $currentKeterangan = $existingValidation[$category][$field]['keterangan'] ?? '';
                                @endphp

                                <tr class="hover:bg-gray-50">
                                    {{-- Column 1: Data Usulan Pegawai --}}
                                    <td class="px-4 py-4">
                                        <div class="flex items-start gap-3">
                                            <div class="bg-indigo-100 p-2 rounded-lg flex-shrink-0">
                                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-base font-medium text-gray-900 truncate">
                                                    {{ ucwords(str_replace('_', ' ', $field)) }}
                                                </p>
                                                <p class="text-base text-gray-700 mt-1 break-words">
                                                    {!! $fieldValue !!}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Column 2: Status Dropdown --}}
                                    <td class="px-4 py-4 text-center">
                                        <select name="validation[{{ $category }}][{{ $field }}][status]"
                                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                onchange="toggleKeterangan('{{ $category }}_{{ $field }}', this.value)">
                                            <option value="sesuai" {{ $currentStatus === 'sesuai' ? 'selected' : '' }}>✓ Sesuai</option>
                                            <option value="tidak_sesuai" {{ $currentStatus === 'tidak_sesuai' ? 'selected' : '' }}>✗ Tidak Sesuai</option>
                                        </select>
                                    </td>

                                    {{-- Column 3: Keterangan --}}
                                    <td class="px-4 py-4">
                                        <textarea name="validation[{{ $category }}][{{ $field }}][keterangan]"
                                                  id="keterangan_{{ $category }}_{{ $field }}"
                                                  rows="3"
                                                  class="w-full border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 text-sm {{ $currentStatus === 'sesuai' ? 'hidden' : '' }}"
                                                  placeholder="Jelaskan mengapa item ini tidak sesuai..."
                                        >{{ $currentKeterangan }}</textarea>
                                        <div id="placeholder_{{ $category }}_{{ $field }}"
                                             class="text-sm text-gray-400 italic p-3 bg-gray-50 rounded-lg text-center {{ $currentStatus === 'tidak_sesuai' ? 'hidden' : '' }}">
                                            Keterangan akan muncul jika memilih "Tidak Sesuai"
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach

                {{-- Action Buttons --}}
                <div class="bg-white shadow-md rounded-lg p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Hasil Validasi</h3>
                            <p class="text-sm text-gray-600">Pilih aksi yang akan dilakukan setelah validasi selesai.</p>
                        </div>

                        @php
                            // Tentukan apakah usulan bisa diedit berdasarkan status
                            $canEdit = in_array($usulan->status_usulan, ['Diajukan', 'Sedang Direview']);
                            $isCompleted = in_array($usulan->status_usulan, [
                                'Perlu Perbaikan',
                                'Dikembalikan',
                                'Diteruskan Ke Universitas',
                                'Disetujui',
                                'Direkomendasikan',
                                'Ditolak'
                            ]);
                        @endphp

                        <div class="flex gap-4">
                            @if($canEdit)
                                {{-- Tombol Simpan Validasi - Hanya muncul jika bisa diedit --}}
                                <button type="submit"
                                        onclick="submitValidation(event)"
                                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                    </svg>
                                    Simpan Validasi
                                </button>

                                {{-- Tombol Kembalikan Usulan --}}
                                <button type="button" onclick="showReturnForm()"
                                        class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"></path>
                                    </svg>
                                    Kembalikan Usulan
                                </button>

                                {{-- Tombol Kirim Usulan --}}
                                <button type="button" onclick="showForwardForm()"
                                        class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Kirim Usulan
                                </button>

                            @else
                                {{-- Status indicator jika tidak bisa diedit --}}
                                <div class="flex items-center gap-4">
                                    @if($usulan->status_usulan === 'Perlu Perbaikan')
                                        <div class="flex items-center gap-2 px-4 py-2 bg-orange-100 text-orange-800 rounded-lg">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                            <span class="font-medium">Usulan sudah dikembalikan ke pegawai untuk perbaikan</span>
                                        </div>

                                    @elseif($usulan->status_usulan === 'Diteruskan Ke Universitas')
                                        <div class="flex items-center gap-2 px-4 py-2 bg-purple-100 text-purple-800 rounded-lg">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                            </svg>
                                            <span class="font-medium">Usulan sudah diteruskan ke tingkat universitas</span>
                                        </div>

                                    @elseif($usulan->status_usulan === 'Direkomendasikan')
                                        <div class="flex items-center gap-2 px-4 py-2 bg-emerald-100 text-emerald-800 rounded-lg">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="font-medium">Usulan telah selesai diproses - Direkomendasikan</span>
                                        </div>

                                    @elseif($usulan->status_usulan === 'Ditolak')
                                        <div class="flex items-center gap-2 px-4 py-2 bg-red-100 text-red-800 rounded-lg">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="font-medium">Usulan telah selesai diproses - Ditolak</span>
                                        </div>

                                    @else
                                        <div class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-800 rounded-lg">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="font-medium">Status: {{ $usulan->status_usulan }}</span>
                                        </div>
                                    @endif

                                    {{-- Tombol kembali ke daftar --}}
                                    <a href="{{ route('admin-fakultas.periode.pendaftar', $usulan->periode_usulan_id) }}"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"></path>
                                        </svg>
                                        Kembali ke Daftar
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

                {{-- Form Kembalikan (Hidden) --}}
                <div id="returnForm" class="hidden mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <h4 class="font-medium text-red-800 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        Kembalikan Usulan ke Pegawai
                    </h4>
                    <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-800">
                        <p><strong>Perhatian:</strong> Usulan akan dikembalikan dengan status "Perlu Perbaikan". Pegawai dapat memperbaiki dan mengirim ulang usulannya.</p>
                    </div>

                    {{-- Form untuk input catatan kembalikan (tidak akan di-submit langsung) --}}
                    <div id="returnUsulanForm">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan untuk Pegawai <span class="text-red-500">*</span>
                            </label>
                            <textarea name="catatan_umum"
                                    rows="4"
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500"
                                    placeholder="Berikan instruksi yang jelas kepada pegawai tentang apa yang perlu diperbaiki..."
                                    required></textarea>
                            <p class="text-xs text-gray-500 mt-1">
                                Catatan ini akan dikirim ke pegawai. Item detail yang tidak sesuai dari validasi akan otomatis disertakan.
                            </p>
                        </div>

                        <div class="flex gap-3">
                            <button type="button"
                                    onclick="submitReturnForm()"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"></path>
                                </svg>
                                Konfirmasi Kembalikan
                            </button>
                            <button type="button"
                                    onclick="hideReturnForm()"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Form Kirim (Hidden) --}}
                <div id="forwardForm" class="hidden mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <h4 class="font-medium text-green-800 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Lengkapi Dokumen untuk Mengirim ke Universitas
                    </h4>
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800">
                        <p><strong>Info:</strong> Pastikan validasi sudah tersimpan sebelum mengirim. Usulan akan diteruskan ke tingkat universitas.</p>
                    </div>

                    {{-- Form untuk input dokumen kirim (tidak akan di-submit langsung) --}}
                    <div id="forwardUsulanForm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Surat Usulan Pimpinan Unit Kerja *</label>
                                <input type="text" name="nomor_surat_usulan" class="w-full border-gray-300 rounded-lg shadow-sm" required
                                    placeholder="Contoh: 001/FK-UNMUL/2025">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Dokumen Surat Usulan *</label>
                                <input type="file" name="file_surat_usulan" class="w-full border-gray-300 rounded-lg" accept=".pdf" required>
                                <p class="text-xs text-gray-500 mt-1">File PDF maksimal 2MB</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Berita Acara Senat *</label>
                                <input type="text" name="nomor_berita_senat" class="w-full border-gray-300 rounded-lg shadow-sm" required
                                    placeholder="Contoh: 002/SENAT-FK/2025">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Berita Acara & Daftar Hadir Senat *</label>
                                <input type="file" name="file_berita_senat" class="w-full border-gray-300 rounded-lg" accept=".pdf" required>
                                <p class="text-xs text-gray-500 mt-1">Upload 1 file PDF yang berisi berita acara senat dan daftar hadir (maksimal 5MB)</p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button type="button"
                                    onclick="submitForwardForm()"
                                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                Konfirmasi Kirim ke Universitas
                            </button>
                            <button type="button"
                                    onclick="hideForwardForm()"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- Riwayat Usulan --}}
        <div class="bg-white shadow-md rounded-lg mt-6">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-800">Riwayat Usulan</h3>
            </div>
            <ul class="divide-y divide-gray-200">
                @forelse ($usulan->logs as $log)
                    <li class="p-6">
                        <p class="font-semibold text-gray-800">{{ $log->status_baru }}</p>
                        <p class="text-sm text-gray-500">
                            Oleh: <span class="font-medium">{{ $log->dilakukanOleh->nama_lengkap ?? 'Sistem' }}</span>
                        </p>
                        <p class="text-sm text-gray-400" title="{{ $log->created_at->format('d F Y, H:i:s') }}">
                            {{ $log->created_at->diffForHumans() }}
                        </p>
                        @if($log->catatan)
                            <p class="mt-2 text-sm text-gray-700 bg-yellow-50 border border-yellow-200 rounded-md p-3">
                                <span class="font-medium">Catatan:</span> "{!! nl2br(e($log->catatan)) !!}"
                            </p>
                        @endif
                    </li>
                @empty
                    <li class="p-6 text-center text-sm text-gray-500">
                        Belum ada riwayat untuk usulan ini.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- JavaScript untuk Toggle Keterangan --}}
    <script>
        // =============================================================
        // FUNGSI YANG DIPERBAIKI
        // =============================================================
        function toggleKeterangan(fieldId, status) {
            const keteranganTextarea = document.getElementById(`keterangan_${fieldId}`);
            const placeholder = document.getElementById(`placeholder_${fieldId}`);

            // Pengecekan keamanan: hanya jalankan jika elemen ditemukan
            if (keteranganTextarea && placeholder) {
                if (status === 'tidak_sesuai') {
                    keteranganTextarea.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                    keteranganTextarea.required = true;
                } else {
                    keteranganTextarea.classList.add('hidden');
                    placeholder.classList.remove('hidden');
                    keteranganTextarea.required = false;
                    keteranganTextarea.value = '';
                }
            }
        }
        // =============================================================

        function resetForm() {
            if (confirm('Apakah Anda yakin ingin mereset semua validasi?')) {
                document.querySelectorAll('select[name*="[status]"]').forEach(select => {
                    select.value = 'sesuai';
                    const fieldParts = select.name.match(/validation\[(\w+)\]\[(\w+)\]/);
                    if (fieldParts) {
                        toggleKeterangan(fieldParts[1] + '_' + fieldParts[2], 'sesuai');
                    }
                });
            }
        }

        function submitValidation(event) {
            // Simpan validasi saja tanpa aksi tambahan
            const form = document.getElementById('validationForm');

            // Hapus input action_type yang mungkin ada
            const existingActionInput = form.querySelector('input[name="action_type"]');
            if (existingActionInput) {
                existingActionInput.remove();
            }

            // Tambahkan input untuk tipe aksi "save_only"
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action_type';
            actionInput.value = 'save_only';
            form.appendChild(actionInput);

            form.method = 'POST';
            // Biarkan form submit secara alami karena button type="submit"
        }

        function showReturnForm() {
            document.getElementById('returnForm').classList.remove('hidden');
            document.getElementById('forwardForm').classList.add('hidden');
        }

        function hideReturnForm() {
            document.getElementById('returnForm').classList.add('hidden');
        }

        function showForwardForm() {
            document.getElementById('forwardForm').classList.remove('hidden');
            document.getElementById('returnForm').classList.add('hidden');
        }

        function hideForwardForm() {
            document.getElementById('forwardForm').classList.add('hidden');
        }

        function submitReturnForm() {
            const mainForm = document.getElementById('validationForm');
            const returnForm = document.getElementById('returnUsulanForm');

            const catatanUmumTextarea = returnForm.querySelector('textarea[name="catatan_umum"]');

            if (!catatanUmumTextarea) {
                console.error("CRITICAL ERROR: Textarea 'catatan_umum' tidak ditemukan!");
                alert("Terjadi error: komponen catatan tidak ditemukan.");
                return false;
            }

            const catatanUmum = catatanUmumTextarea.value;

            if (!catatanUmum || catatanUmum.trim().length < 10) {
                alert('Catatan untuk pegawai wajib diisi minimal 10 karakter.');
                return false;
            }

            if (!confirm('Apakah Anda yakin ingin mengembalikan usulan ini ke pegawai untuk perbaikan?')) {
                return false;
            }

            const existingActionInput = mainForm.querySelector('input[name="action_type"]');
            if (existingActionInput) {
                existingActionInput.remove();
            }

            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action_type';
            actionInput.value = 'return_to_pegawai';
            mainForm.appendChild(actionInput);

            const catatanInput = document.createElement('input');
            catatanInput.type = 'hidden';
            catatanInput.name = 'catatan_umum';
            catatanInput.value = catatanUmum;
            mainForm.appendChild(catatanInput);

            mainForm.submit();
        }

        function submitForwardForm() {
            const mainForm = document.getElementById('validationForm');
            const forwardForm = document.getElementById('forwardUsulanForm');

            // Validasi form forward (tetap sama)
            const nomorSurat = forwardForm.querySelector('input[name="nomor_surat_usulan"]').value;
            const fileSurat = forwardForm.querySelector('input[name="file_surat_usulan"]').files;
            const nomorBerita = forwardForm.querySelector('input[name="nomor_berita_senat"]').value;
            const fileBerita = forwardForm.querySelector('input[name="file_berita_senat"]').files;

            if (!nomorSurat || fileSurat.length === 0 || !nomorBerita || fileBerita.length === 0) {
                alert('Semua field dokumen fakultas wajib diisi.');
                return false;
            }

            if (!confirm('Apakah Anda yakin ingin mengirim usulan ini ke universitas?')) {
                return false;
            }

            // 1. Hapus input temporer dari pengiriman sebelumnya (jika ada) untuk mencegah duplikasi.
            mainForm.querySelectorAll('.temp-forward-input').forEach(el => el.remove());

            // 2. Tambahkan action_type sebagai input hidden.
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action_type';
            actionInput.value = 'forward_to_university';
            actionInput.classList.add('temp-forward-input');
            mainForm.appendChild(actionInput);

            // 3. Buat input hidden BARU untuk setiap field teks dari form forward.
            const textInputs = forwardForm.querySelectorAll('input[type="text"], textarea');
            textInputs.forEach(input => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = input.name;
                hiddenInput.value = input.value;
                hiddenInput.classList.add('temp-forward-input');
                mainForm.appendChild(hiddenInput);
            });

            // 4. Pindahkan elemen input file secara langsung (ini cara yang benar untuk file).
            const fileInputs = forwardForm.querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => {
                // Sembunyikan input file agar tidak mengganggu layout saat dipindahkan.
                input.style.display = 'none';
                input.classList.add('temp-forward-input');
                mainForm.appendChild(input);
            });

            // Submit form utama yang sekarang berisi SEMUA data.
            mainForm.submit();
        }

        // Initialize form on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial state for all fields based on existing data
            document.querySelectorAll('select[name*="[status]"]').forEach(select => {
                const fieldParts = select.name.match(/validation\[(\w+)\]\[(\w+)\]/);
                if (fieldParts) {
                    toggleKeterangan(fieldParts[1] + '_' + fieldParts[2], select.value);
                }
            });
        });
    </script>
@endsection
