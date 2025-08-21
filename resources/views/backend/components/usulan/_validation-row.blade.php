@php
    $fieldHelper = new \App\Helpers\UsulanFieldHelper($usulan);
    $fieldValue = $fieldHelper->getFieldValue($category, $field);

    $currentStatus = $existingValidation[$category][$field]['status'] ?? 'sesuai';
    $currentKeterangan = $existingValidation[$category][$field]['keterangan'] ?? '';
@endphp

<tbody class="divide-y divide-gray-200">
    <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
        {{-- Column 1: Data Field --}}
        <td class="px-6 py-4">
            <div class="flex items-center gap-4">
                <div class="bg-indigo-100 p-2 rounded-lg flex-shrink-0">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-lg font-semibold text-gray-900 lowercase">
                        @if ($category === 'dokumen_bkd' && Str::startsWith($field, 'bkd_'))
                            {{-- Label BKD dinamis --}}
                            {{ strtolower($bkdLabels[$field] ?? ucwords(str_replace('_', ' ', $field))) }}
                        @elseif ($category === 'dokumen_pendukung')
                            @php
                                $labels = [
                                    'nomor_surat_usulan' => 'Nomor Surat Usulan Fakultas',
                                    'file_surat_usulan'  => 'Dokumen Surat Usulan Fakultas',
                                    'nomor_berita_senat' => 'Nomor Surat Senat',
                                    'file_berita_senat'  => 'Dokumen Surat Senat',
                                ];
                            @endphp
                            {{ strtolower($labels[$field] ?? ucwords(str_replace('_', ' ', $field))) }}
                        @else
                            {{ strtolower(ucwords(str_replace('_', ' ', $field))) }}
                        @endif
                    </p>
                    <div class="text-base text-gray-600 mt-1 break-words lowercase">
                        {!! strtolower($fieldValue) !!}
                    </div>
                </div>
            </div>
        </td>

        {{-- Column 2: Status Validation --}}
        <td class="px-6 py-4 text-center">
            @if($canEdit)
                <select name="validation[{{ $category }}][{{ $field }}][status]"
                        class="w-48 border border-gray-300 rounded-lg px-3 py-2 text-base font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 uppercase"
                        data-field-id="{{ $category }}_{{ $field }}">
                    <option value="sesuai" {{ $currentStatus === 'sesuai' ? 'selected' : '' }}>
                        ✓ SESUAI
                    </option>
                    <option value="tidak_sesuai" {{ $currentStatus === 'tidak_sesuai' ? 'selected' : '' }}>
                        ✗ TIDAK SESUAI
                    </option>
                </select>
            @else
                <span class="inline-flex px-3 py-1 text-base font-semibold rounded-full lowercase {{ $currentStatus === 'sesuai' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $currentStatus === 'sesuai' ? '✓ sesuai' : '✗ tidak sesuai' }}
                </span>
            @endif
        </td>

        {{-- Column 3: Keterangan --}}
        <td class="px-6 py-4">
            @if($canEdit)
                <textarea name="validation[{{ $category }}][{{ $field }}][keterangan]"
                    id="keterangan_{{ $category }}_{{ $field }}"
                    rows="3"
                    class="w-full border border-gray-300 rounded-lg shadow-sm p-3 text-base font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 disabled:bg-gray-100 disabled:cursor-not-allowed resize-none text-center lowercase"
                    placeholder="{{ $currentStatus === 'sesuai' ? 'pilih \"tidak sesuai\" untuk mengisi keterangan' : 'jelaskan mengapa item ini tidak sesuai...' }}"
                    style="text-align: center;"
                    onfocus="this.style.textAlign='left';"
                    onblur="if(this.value === '') { this.style.textAlign='center'; }"
                    {{ $currentStatus === 'sesuai' ? 'disabled' : '' }}>{{ strtolower($currentKeterangan) }}
                </textarea>
            @else
                @if($currentStatus === 'tidak_sesuai' && $currentKeterangan)
                    <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-base text-red-800 lowercase">
                        {{ strtolower($currentKeterangan) }}
                    </div>
                @else
                    <div class="p-3 bg-gray-50 rounded-lg text-base text-gray-500 italic text-center lowercase">
                        -
                    </div>
                @endif
            @endif
        </td>
    </tr>
</tbody>
