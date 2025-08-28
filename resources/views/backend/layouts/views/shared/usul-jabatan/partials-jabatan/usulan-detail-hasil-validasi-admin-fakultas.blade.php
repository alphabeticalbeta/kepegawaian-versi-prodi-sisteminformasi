{{-- Hasil Validasi Admin Fakultas Section --}}
@if($currentRole === 'Admin Fakultas' && isset($usulan->validasi_data['admin_fakultas']['validation']))
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-5">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i data-lucide="check-circle" class="w-6 h-6 mr-3"></i>
                Hasil Validasi Admin Fakultas
            </h2>
        </div>
        <div class="p-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <div class="flex items-start">
                    <i data-lucide="info" class="w-5 h-5 text-blue-600 mt-0.5 mr-3"></i>
                    <div>
                        <h4 class="text-sm font-medium text-blue-800">Hasil Validasi</h4>
                        <p class="text-sm text-blue-700 mt-1">
                            Berikut adalah hasil validasi yang telah Anda lakukan untuk usulan ini.
                        </p>
                    </div>
                </div>
            </div>

            @php
                $adminFakultasValidation = $usulan->validasi_data['admin_fakultas']['validation'] ?? [];
                $invalidFields = [];
                
                foreach ($adminFakultasValidation as $groupKey => $groupData) {
                    if (is_array($groupData)) {
                        foreach ($groupData as $fieldKey => $fieldData) {
                            if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                                // Handle fields that might be closures
                                $groupFields = $fieldGroups[$groupKey]['fields'] ?? [];
                                if (is_callable($groupFields)) {
                                    $groupFields = $groupFields();
                                }
                                $fieldLabel = $groupFields[$fieldKey] ?? ucwords(str_replace('_', ' ', $fieldKey));
                                
                                $invalidFields[] = [
                                    'group' => $fieldGroups[$groupKey]['label'] ?? ucwords(str_replace('_', ' ', $groupKey)),
                                    'field' => $fieldLabel,
                                    'keterangan' => $fieldData['keterangan'] ?? 'Tidak ada keterangan'
                                ];
                            }
                        }
                    }
                }
            @endphp

            @if(!empty($invalidFields))
                <div class="mt-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Field yang Tidak Sesuai:</h4>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="space-y-2">
                            @foreach($invalidFields as $field)
                                <div class="flex items-start gap-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                                    <i data-lucide="x-circle" class="w-4 h-4 text-red-600 mt-0.5 flex-shrink-0"></i>
                                    <div>
                                        <div class="text-sm font-medium text-red-800">{{ $field['group'] }} - {{ $field['field'] }}</div>
                                        <div class="text-sm text-red-700 mt-1">{{ $field['keterangan'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if(!empty($usulan->catatan_verifikator))
                @php
                    $catatanLines = explode("\n", $usulan->catatan_verifikator);
                    $catatanTambahan = '';
                    
                    foreach ($catatanLines as $line) {
                        $trimmedLine = trim($line);
                        if ($trimmedLine === 'Catatan Tambahan:') {
                            // Get the next line as the actual catatan
                            $nextIndex = array_search($line, $catatanLines) + 1;
                            if (isset($catatanLines[$nextIndex])) {
                                $catatanTambahan = trim($catatanLines[$nextIndex]);
                            }
                            break;
                        }
                    }
                @endphp
                
                @if(!empty($catatanTambahan))
                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Catatan Tambahan:</h4>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start gap-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                                <i data-lucide="message-square" class="w-4 h-4 text-red-600 mt-0.5 flex-shrink-0"></i>
                                <div class="text-sm text-red-700">{{ $catatanTambahan }}</div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
@endif
