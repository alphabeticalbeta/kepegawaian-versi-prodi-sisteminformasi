
@php
    $fieldHelper = new \App\Helpers\UsulanFieldHelper($usulan);
    $fieldValue = $fieldHelper->getFieldValue($category, $field);
    $currentStatus = $existingValidation[$category][$field]['status'] ?? 'sesuai';
    $currentKeterangan = $existingValidation[$category][$field]['keterangan'] ?? '';
    
    // Determine if this is a link field
    $isLinkField = strpos($fieldValue, '<a href=') !== false;
    
    // Determine if this is an article field that needs smaller font
    $articleFields = ['nama_jurnal', 'judul_artikel', 'penerbit_artikel', 'volume_artikel', 'nomor_artikel', 'edisi_artikel', 'halaman_artikel'];
    $isArticleField = in_array($field, $articleFields);
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
                    <p class="text-lg font-semibold text-gray-900 uppercase">
                        {{ $fieldHelper->getValidationLabel($category, $field, $bkdLabels ?? []) }}
                    </p>
                    <div class="{{ $isLinkField ? '' : 'border border-gray-800 px-3 py-2 rounded-md bg-gray-50' }} {{ $isArticleField ? 'text-sm' : 'text-xl' }} font-bold text-gray-800 mt-2 break-words whitespace-normal" 
                         style="{{ $isLinkField ? '' : 'text-transform: uppercase; word-wrap: break-word; overflow-wrap: break-word;' }}">
                        {!! $isLinkField ? $fieldValue : strtoupper(strip_tags($fieldValue)) !!}
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
                <span class="inline-flex px-3 py-1 text-base font-semibold rounded-full" style="text-transform: lowercase; {{ $currentStatus === 'sesuai' ? 'background-color:#d1fae5;color:#065f46;' : 'background-color:#fee2e2;color:#991b1b;' }}">
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
                    class="w-full border border-gray-300 rounded-lg shadow-sm p-3 text-base font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 disabled:bg-gray-100 disabled:cursor-not-allowed resize-none text-center"
                    placeholder="{{ $currentStatus === 'sesuai' ? 'pilih \"tidak sesuai\" untuk mengisi keterangan' : 'jelaskan mengapa item ini tidak sesuai...' }}"
                    style="text-align: center; text-transform: lowercase;"
                    onfocus="this.style.textAlign='left';"
                    onblur="if(this.value === '') { this.style.textAlign='center'; }"
                    {{ $currentStatus === 'sesuai' ? 'disabled' : '' }}>{{ strtolower($currentKeterangan) }}
                </textarea>
            @else
                @if($currentStatus === 'tidak_sesuai' && $currentKeterangan)
                    <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-base text-red-800" style="text-transform: lowercase;">
                        {{ strtolower($currentKeterangan) }}
                    </div>
                @else
                    <div class="p-3 bg-gray-50 rounded-lg text-base text-gray-500 italic text-center" style="text-transform: lowercase;">
                        -
                    </div>
                @endif
            @endif
        </td>
    </tr>
</tbody>
