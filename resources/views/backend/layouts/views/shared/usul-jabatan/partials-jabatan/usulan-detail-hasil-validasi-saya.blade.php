{{-- Hasil Validasi Tim Penilai (Hanya untuk Penilai Universitas - Data Sendiri) --}}
@if($currentRole === 'Penilai Universitas')
    @php
        $invalidFields = [];
        $generalNotes = [];
        $currentPenilaiId = auth()->user()->id;

        // Ambil data validasi individual penilai menggunakan method baru
        $currentPenilaiData = $usulan->getValidasiIndividualPenilai($currentPenilaiId);

        if ($currentPenilaiData && is_array($currentPenilaiData)) {
            // Proses field yang tidak sesuai
            foreach ($currentPenilaiData as $groupKey => $groupData) {
                if (is_array($groupData)) {
                    foreach ($groupData as $fieldKey => $fieldData) {
                        if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                            $groupLabel = isset($fieldGroups[$groupKey]['label']) ? $fieldGroups[$groupKey]['label'] : ucwords(str_replace('_', ' ', $groupKey));
                            // Handle fields that might be closures
                            $groupFields = $fieldGroups[$groupKey]['fields'] ?? [];
                            if (is_callable($groupFields)) {
                                $groupFields = $groupFields();
                            }
                            $fieldLabel = $groupFields[$fieldKey] ?? ucwords(str_replace('_', ' ', $fieldKey));

                            $invalidFields[] = $fieldLabel . ' : ' . ($fieldData['keterangan'] ?? 'Tidak ada keterangan');
                        }
                    }
                }
            }

            // Collect keterangan umum
            if (isset($currentPenilaiData['keterangan_umum']) && !empty($currentPenilaiData['keterangan_umum'])) {
                $generalNotes[] = $currentPenilaiData['keterangan_umum'];
            }
        }

    @endphp

        @if($currentPenilaiData && is_array($currentPenilaiData))
        <div class="bg-white rounded-xl shadow-lg border border-red-200 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="alert-triangle" class="w-6 h-6 mr-3"></i>
                    Hasil Validasi Saya
                </h2>
            </div>
            <div class="p-6">
                @if(!empty($invalidFields))
                    <div class="mb-4">
                        <h4 class="font-medium text-red-800 mb-2 flex items-center">
                            <i data-lucide="alert-triangle" class="w-4 h-4 mr-2"></i>
                            Field yang Tidak Sesuai:
                        </h4>
                        <div class="space-y-2">
                        @foreach($invalidFields as $field)
                                <div class="text-sm text-red-800 bg-red-50 px-3 py-2 rounded border-l-4 border-red-400 flex items-start">
                                    <i data-lucide="x-circle" class="w-4 h-4 mr-2 mt-0.5 text-red-500 flex-shrink-0"></i>
                                    <span>{{ $field }}</span>
                                </div>
                        @endforeach
                        </div>
                    </div>
                @else
                    <div class="mb-4">
                        <div class="text-center py-4">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="check-circle" class="w-8 h-8 text-green-600"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-green-800 mb-2">Semua Field Sesuai</h4>
                            <p class="text-green-600">Tidak ada field yang memerlukan perbaikan.</p>
                        </div>
                    </div>
                @endif

                @if(!empty($generalNotes))
                    <div class="mt-4">
                        <h4 class="font-medium text-red-800 mb-2 flex items-center">
                            <i data-lucide="message-square" class="w-4 h-4 mr-2"></i>
                            Keterangan Umum:
                        </h4>
                        <div class="space-y-2">
                            @foreach($generalNotes as $note)
                                <div class="text-sm text-red-800 bg-red-50 px-3 py-2 rounded border-l-4 border-red-400 flex items-start">
                                    <i data-lucide="info" class="w-4 h-4 mr-2 mt-0.5 text-red-500 flex-shrink-0"></i>
                                    <span>{{ $note }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>
    @endif
@endif
