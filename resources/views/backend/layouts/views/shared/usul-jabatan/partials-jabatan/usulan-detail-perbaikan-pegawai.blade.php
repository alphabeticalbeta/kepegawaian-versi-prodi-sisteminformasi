{{-- Perbaikan untuk Role Pegawai Section --}}
@if($currentRole === 'Pegawai' && in_array($usulan->status_usulan, ['Usulan Perbaikan dari Admin Fakultas', 'Usulan Perbaikan dari Kepegawaian Universitas', 'Usulan Perbaikan dari Penilai Universitas']) && !empty($usulan->catatan_verifikator))
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-orange-600 to-red-600 px-6 py-5">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i data-lucide="alert-triangle" class="w-6 h-6 mr-3"></i>
                Perbaikan dari Admin Fakultas
            </h2>
        </div>
        <div class="p-6">
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                <div class="flex items-start">
                    <i data-lucide="info" class="w-5 h-5 text-orange-600 mt-0.5 mr-3"></i>
                    <div>
                        <h4 class="text-sm font-medium text-orange-800">Catatan Perbaikan</h4>
                        <p class="text-sm text-orange-700 mt-1">
                            Admin Fakultas telah mengembalikan usulan ini untuk perbaikan. Silakan periksa dan perbaiki sesuai catatan di bawah ini.
                        </p>
                    </div>
                </div>
            </div>



            @if(isset($usulan->validasi_data['admin_fakultas']['validation']))
                <div class="mt-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Field dan Keterangan yang Tidak Sesuai:</h4>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
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
                        @else
                            <div class="text-sm text-gray-600">Tidak ada field spesifik yang perlu diperbaiki.</div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif
