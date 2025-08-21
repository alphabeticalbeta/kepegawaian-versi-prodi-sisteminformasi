{{-- Validation Row Component - Single row dalam tabel validasi --}}
@php
    $fieldValue = '-';
    $fieldHelper = new \App\Helpers\UsulanFieldHelper($usulan); // Assuming helper class exists

    // Get field value based on category
    $fieldValue = $fieldHelper->getFieldValue($category, $field) ?? '-';

    // For simpler implementation without helper:
    if (in_array($category, ['data_pribadi', 'data_kepegawaian', 'data_pendidikan', 'data_kinerja'])) {
        // Handle pegawai data fields
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

    // Handle document fields
    elseif ($category === 'dokumen_profil') {
        $dokumenPath = $usulan->pegawai->{$field} ?? null;
        if (!empty($dokumenPath)) {
            $fieldValue = '<a href="' . route('backend.admin-univ-usulan.data-pegawai.show-document', [$usulan->pegawai_id, $field]) . '"
                            target="_blank"
                            class="text-blue-600 hover:text-blue-800 underline inline-flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                ✓ Lihat Dokumen
            </a>';
        } else {
            $fieldValue = '<span class="text-red-500">✗ Belum diunggah</span>';
        }
    }

    // Handle karya ilmiah fields
    elseif ($category === 'karya_ilmiah') {
        if ($field === 'karya_ilmiah') {
            $fieldValue = $usulan->data_usulan['karya_ilmiah']['jenis_karya'] ??
                         $usulan->data_usulan['karya_ilmiah'] ?? '-';
        } elseif (str_starts_with($field, 'link_')) {
            $linkKey = str_replace('link_', '', $field);
            $linkValue = $usulan->data_usulan['karya_ilmiah']['links'][$linkKey] ??
                        $usulan->data_usulan['karya_ilmiah'][$field] ??
                        $usulan->data_usulan[$field] ?? null;

            if ($linkValue && filter_var($linkValue, FILTER_VALIDATE_URL)) {
                $linkNames = [
                    'link_artikel' => 'Lihat Artikel',
                    'link_sinta' => 'Lihat Profil SINTA',
                    'link_scopus' => 'Lihat Profil SCOPUS',
                    'link_scimago' => 'Lihat Profil SCIMAGO',
                    'link_wos' => 'Lihat Profil WoS'
                ];
                $linkText = $linkNames[$field] ?? 'Lihat Link';

                $fieldValue = '<a href="' . $linkValue . '"
                                target="_blank"
                                class="text-blue-600 hover:text-blue-800 underline inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    ' . $linkText . '
                </a>';
            } else {
                $fieldValue = $linkValue ?: '-';
            }
        } else {
            $fieldValue = $usulan->data_usulan['karya_ilmiah'][$field] ??
                         $usulan->data_usulan[$field] ?? '-';
        }
    }

    // Handle dokumen usulan fields
    elseif ($category === 'dokumen_usulan') {
        $docPath = $usulan->data_usulan['dokumen_usulan'][$field]['path'] ??
                   $usulan->data_usulan[$field] ?? null;

        if (!empty($docPath)) {
            $fieldValue = '<a href="' . route('backend.admin-univ-usulan.pusat-usulan.show-document', [$usulan->id, $field]) . '"
                            target="_blank"
                            class="text-blue-600 hover:text-blue-800 underline inline-flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                ✓ Lihat Dokumen
            </a>';
        } else {
            $fieldValue = '<span class="text-red-500">✗ Belum diunggah</span>';
        }
    }

    // Get existing validation status
    $currentStatus = $existingValidation[$category][$field]['status'] ?? 'sesuai';
    $currentKeterangan = $existingValidation[$category][$field]['keterangan'] ?? '';

    // Determine if this is view-only mode
    $isViewOnly = isset($viewOnly) && $viewOnly === true;
@endphp

<tr class="hover:bg-gray-50 transition-colors">
    {{-- Column 1: Data Field --}}
    <td class="px-4 py-4">
        <div class="flex items-start gap-3">
            <div class="bg-indigo-100 p-2 rounded-lg flex-shrink-0">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900">
                    {{ ucwords(str_replace('_', ' ', $field)) }}
                </p>
                <div class="text-sm text-gray-700 mt-1 break-words">
                    {!! $fieldValue !!}
                </div>
            </div>
        </div>
    </td>

    {{-- Column 2: Status Validation --}}
    <td class="px-4 py-4 text-center">
        @if(!$isViewOnly)
            <select name="validation[{{ $category }}][{{ $field }}][status]"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    onchange="toggleKeterangan('{{ $category }}_{{ $field }}', this.value)">
                <option value="sesuai" {{ $currentStatus === 'sesuai' ? 'selected' : '' }}>
                    ✓ Sesuai
                </option>
                <option value="tidak_sesuai" {{ $currentStatus === 'tidak_sesuai' ? 'selected' : '' }}>
                    ✗ Tidak Sesuai
                </option>
            </select>
        @else
            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                        {{ $currentStatus === 'sesuai' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $currentStatus === 'sesuai' ? '✓ Sesuai' : '✗ Tidak Sesuai' }}
            </span>
        @endif
    </td>

    {{-- Column 3: Keterangan --}}
    <td class="px-4 py-4">
        @if(!$isViewOnly)
            <textarea name="validation[{{ $category }}][{{ $field }}][keterangan]"
                      id="keterangan_{{ $category }}_{{ $field }}"
                      rows="3"
                      class="w-full border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 text-sm
                             {{ $currentStatus === 'sesuai' ? 'hidden' : '' }}"
                      placeholder="Jelaskan mengapa item ini tidak sesuai...">{{ $currentKeterangan }}</textarea>

            <div id="placeholder_{{ $category }}_{{ $field }}"
                 class="text-sm text-gray-400 italic p-3 bg-gray-50 rounded-lg text-center
                        {{ $currentStatus === 'tidak_sesuai' ? 'hidden' : '' }}">
                Keterangan akan muncul jika memilih "Tidak Sesuai"
            </div>
        @else
            @if($currentStatus === 'tidak_sesuai' && $currentKeterangan)
                <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-800">{{ $currentKeterangan }}</p>
                </div>
            @else
                <div class="text-sm text-gray-400 italic p-3 bg-gray-50 rounded-lg text-center">
                    -
                </div>
            @endif
        @endif
    </td>
</tr>
