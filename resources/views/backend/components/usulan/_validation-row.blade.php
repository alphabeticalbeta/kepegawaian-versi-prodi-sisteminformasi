{{-- Validation Row Component - Single row dalam tabel validasi --}}
@php
    $fieldHelper = new \App\Helpers\UsulanFieldHelper($usulan);
    $fieldValue = $fieldHelper->getFieldValue($category, $field);

    $currentStatus = $existingValidation[$category][$field]['status'] ?? 'sesuai';
    $currentKeterangan = $existingValidation[$category][$field]['keterangan'] ?? '';
@endphp

<tbody class="divide-y divide-gray-200">
    <tr class="border border-gray-300">
        {{-- Column 1: Data Field --}}
        <td class="px-4 py-6">
            <div class="flex items-start gap-3">
                <div class="bg-indigo-100 p-2 flex-shrink-0">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xl font-bold text-gray-900">
                        @if ($category === 'dokumen_bkd' && Str::startsWith($field, 'bkd_'))
                            {{-- Label BKD dinamis --}}
                            {{ $bkdLabels[$field] ?? ucwords(str_replace('_', ' ', $field)) }}

                        @elseif ($category === 'dokumen_pendukung')
                            @php
                                $labels = [
                                    'nomor_surat_usulan' => 'Nomor Surat Usulan Fakultas',
                                    'file_surat_usulan'  => 'Dokumen Surat Usulan Fakultas',
                                    'nomor_berita_senat' => 'Nomor Surat Senat',
                                    'file_berita_senat'  => 'Dokumen Surat Senat',
                                ];
                            @endphp
                            {{ $labels[$field] ?? ucwords(str_replace('_', ' ', $field)) }}

                        @else
                            {{ ucwords(str_replace('_', ' ', $field)) }}
                        @endif
                    </p>
                    <div class="text-xl font-bold text-gray-700 mt-1 break-words">
                        {!! $fieldValue !!}
                    </div>
                </div>
            </div>
        </td>

        {{-- Column 2: Status Validation --}}
        <td class="px-4 py-4 text-xl text-center">
            @if($canEdit)
                <select name="validation[{{ $category }}][{{ $field }}][status]"
                        class="border border-gray-300 rounded px-2 py-1 text-center text-xl"
                        data-field-id="{{ $category }}_{{ $field }}">
                    <option value="sesuai" {{ $currentStatus === 'sesuai' ? 'selected' : '' }}>
                        ✓ Sesuai
                    </option>
                    <option value="tidak_sesuai" {{ $currentStatus === 'tidak_sesuai' ? 'selected' : '' }}>
                        ✗ Tidak Sesuai
                    </option>
                </select>
            @else
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $currentStatus === 'sesuai' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $currentStatus === 'sesuai' ? '✓ Sesuai' : '✗ Tidak Sesuai' }}
                </span>
            @endif
        </td>

        {{-- Column 3: Keterangan --}}
        <td class="px-4 py-4 text-lg">
            @if($canEdit)
                <textarea name="validation[{{ $category }}][{{ $field }}][keterangan]"
                    id="keterangan_{{ $category }}_{{ $field }}"
                    rows="3"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 text-lg p-3 disabled:bg-gray-100 disabled:cursor-not-allowed text-center focus:text-left resize-none flex items-center justify-center"
                    placeholder="{{ $currentStatus === 'sesuai' ? 'Pilih "Tidak Sesuai" untuk mengisi keterangan' : 'Jelaskan mengapa item ini tidak sesuai...' }}"
                    style="text-align: center; display: flex; align-items: center;"
                    onfocus="this.style.textAlign='left'; this.style.display='block';"
                    onblur="if(this.value === '') { this.style.textAlign='center'; this.style.display='flex'; }"
                    {{ $currentStatus === 'sesuai' ? 'disabled' : '' }}>{{ $currentKeterangan }}
                </textarea>
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
</tbody>
