{{-- Perbaikan dari Kepegawaian Universitas untuk Role Admin Fakultas Section --}}
@if($currentRole === 'Admin Fakultas' && $usulan->status_usulan === 'Usulan Perbaikan dari Kepegawaian Universitas' && !empty($usulan->catatan_verifikator) && isset($usulan->validasi_data['admin_universitas']['validation']))
    @php
        // Define field categories relevant for Admin Fakultas
        $adminFakultasFieldCategories = [
            'dokumen_admin_fakultas',
            'dokumen_pendukung_fakultas',
            'dokumen_pendukung' // Include general dokumen pendukung
        ];

        // Get validation data from Admin Universitas
        $adminUnivValidation = $usulan->validasi_data['admin_universitas']['validation'] ?? [];
        $filteredInvalidFields = [];

        // Filter only fields relevant to Admin Fakultas
        foreach ($adminUnivValidation as $groupKey => $groupData) {
            if (in_array($groupKey, $adminFakultasFieldCategories) && is_array($groupData)) {
                foreach ($groupData as $fieldKey => $fieldData) {
                    if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                                                    // Handle fields that might be closures
                            $groupFields = $fieldGroups[$groupKey]['fields'] ?? [];
                            if (is_callable($groupFields)) {
                                $groupFields = $groupFields();
                            }
                            $fieldLabel = $groupFields[$fieldKey] ?? ucwords(str_replace('_', ' ', $fieldKey));
                        $filteredInvalidFields[] = [
                            'group' => $fieldGroups[$groupKey]['label'] ?? ucwords(str_replace('_', ' ', $groupKey)),
                            'field' => $fieldLabel,
                            'keterangan' => $fieldData['keterangan'] ?? 'Tidak ada keterangan'
                        ];
                    }
                }
            }
        }
    @endphp

    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-5">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i data-lucide="alert-triangle" class="w-6 h-6 mr-3"></i>
                Perbaikan dari Kepegawaian Universitas
            </h2>
        </div>
        <div class="p-6">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                <div class="flex items-start">
                    <i data-lucide="info" class="w-5 h-5 text-red-600 mt-0.5 mr-3"></i>
                    <div>
                        <h4 class="text-sm font-medium text-red-800">Catatan Perbaikan</h4>
                        <p class="text-sm text-red-700 mt-1">
                            Kepegawaian Universitas telah mengembalikan usulan ini untuk perbaikan. Silakan periksa dan perbaiki field yang relevan dengan tanggung jawab Admin Fakultas di bawah ini.
                        </p>
                    </div>
                </div>
            </div>



            @if(!empty($filteredInvalidFields))
                <div class="mt-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Field yang Perlu Diperbaiki (Relevan untuk Admin Fakultas):</h4>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="space-y-2">
                            @foreach($filteredInvalidFields as $field)
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
            @else
                <div class="mt-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-blue-600 mt-0.5 mr-3"></i>
                            <div>
                                <h4 class="text-sm font-medium text-blue-800">Tidak Ada Field yang Perlu Diperbaiki</h4>
                                <p class="text-sm text-blue-700 mt-1">
                                    Semua field yang relevan dengan Admin Fakultas sudah sesuai. Perbaikan mungkin terkait dengan area lain yang bukan tanggung jawab Admin Fakultas.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                <div class="flex items-start">
                    <i data-lucide="lightbulb" class="w-5 h-5 text-amber-600 mt-0.5 mr-3"></i>
                    <div>
                        <h4 class="text-sm font-medium text-amber-800">Informasi Penting</h4>
                        <p class="text-sm text-amber-700 mt-1">
                            Hanya field yang relevan dengan tanggung jawab Admin Fakultas yang ditampilkan di atas. 
                            Field lain yang tidak ditampilkan mungkin sudah sesuai atau merupakan tanggung jawab role lain.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
