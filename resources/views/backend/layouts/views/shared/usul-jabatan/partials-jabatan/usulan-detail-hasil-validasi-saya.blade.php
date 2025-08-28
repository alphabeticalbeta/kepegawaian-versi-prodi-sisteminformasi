{{-- Hasil Validasi Tim Penilai (Hanya untuk Penilai Universitas - Data Sendiri) --}}
@if($currentRole === 'Penilai Universitas')
    @php
        $invalidFields = [];
        $generalNotes = [];
        $currentPenilaiId = auth()->user()->id;
        $hasCurrentPenilaiData = false;
        
        // Ambil data validasi dari penilai universitas yang sedang login
        $penilaiValidation = $usulan->getValidasiByRole('tim_penilai') ?? [];
        
        if (!empty($penilaiValidation)) {
            // Cari data validasi dari penilai yang sedang login
            $currentPenilaiData = null;
            
            // Cek apakah ada data individual penilai
            if (isset($penilaiValidation['individual_penilai']) && is_array($penilaiValidation['individual_penilai'])) {
                foreach ($penilaiValidation['individual_penilai'] as $penilaiData) {
                    if (isset($penilaiData['penilai_id']) && $penilaiData['penilai_id'] == $currentPenilaiId) {
                        $currentPenilaiData = $penilaiData;
                        $hasCurrentPenilaiData = true;
                        break;
                    }
                }
            }
            
            // PERBAIKAN: Hanya proses data jika benar-benar ada data untuk penilai yang sedang login
            // Jangan fallback ke data umum atau data penilai lain
            if ($hasCurrentPenilaiData && $currentPenilaiData && is_array($currentPenilaiData)) {
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
                
                // Collect keterangan umum hanya dari data penilai yang sedang login
                if (isset($currentPenilaiData['keterangan_umum']) && !empty($currentPenilaiData['keterangan_umum'])) {
                    $generalNotes[] = $currentPenilaiData['keterangan_umum'];
                }
            }
        }
    @endphp

    @if(!empty($invalidFields))
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
                @endif
                

            </div>
        </div>
    @endif
@endif
