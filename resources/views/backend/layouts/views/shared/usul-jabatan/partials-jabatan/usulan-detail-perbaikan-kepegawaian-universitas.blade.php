{{-- Menampilkan Rangkuman Field Perbaikan untuk Admin Fakultas & Kepegawaian Universitas --}}
@if(
    ($currentRole === 'Admin Fakultas' && 
     in_array($usulan->status_usulan, [
        'Permintaan Perbaikan dari Admin Fakultas', 
        'Permintaan Perbaikan Ke Admin Fakultas Dari Kepegawaian Universitas', 
        'Usulan Perbaikan dari Kepegawaian Universitas'
     ])) ||
    ($currentRole === 'Kepegawaian Universitas' && 
     $usulan->status_usulan === 'Permintaan Perbaikan Ke Admin Fakultas Dari Kepegawaian Universitas')
    && isset($usulan->validasi_data['kepegawaian_universitas']['validation'])
)
    @php
        // Logika ini sekarang berjalan untuk kedua role, mengambil data dari Kepegawaian Universitas
        $kepegawaianUnivValidation = $usulan->validasi_data['kepegawaian_universitas']['validation'] ?? [];
        $invalidFields = [];

        foreach ($kepegawaianUnivValidation as $groupKey => $groupData) {
            if (is_array($groupData)) {
                foreach ($groupData as $fieldKey => $fieldData) {
                    if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
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
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-red-600 to-rose-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="alert-circle" class="w-6 h-6 mr-3"></i>
                    Informasi Perbaikan Usulan
                </h2>
            </div>
            <div class="p-6">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start">
                        <i data-lucide="info" class="w-5 h-5 text-red-600 mt-0.5 mr-3"></i>
                        <div>
                            <h4 class="text-sm font-medium text-red-800">
                                @if($currentRole === 'Admin Fakultas')
                                    Field yang Perlu Anda Perbaiki
                                @else
                                    Rangkuman Field yang Dikembalikan ke Admin Fakultas
                                @endif
                            </h4>
                            <p class="text-sm text-red-700 mt-1">
                                Berikut adalah daftar field yang ditandai "Tidak Sesuai" oleh Kepegawaian Universitas.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="space-y-3">
                            @foreach($invalidFields as $field)
                                <div class="flex items-start gap-3 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                                    <i data-lucide="alert-triangle" class="w-4 h-4 text-orange-600 mt-0.5 flex-shrink-0"></i>
                                    <div>
                                        <div class="text-sm font-medium text-gray-800">
                                            <span class="font-semibold">{{ $field['group'] }}</span> &raquo; {{ $field['field'] }}
                                        </div>
                                        <div class="text-sm text-orange-800 mt-1 pl-1 border-l-2 border-orange-200">
                                            <strong>Keterangan:</strong> {{ $field['keterangan'] }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if(!empty($usulan->catatan_verifikator))
                    <div class="mt-6 border-t pt-4">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">
                            <i data-lucide="message-square" class="w-4 h-4 inline-block mr-2"></i>
                            Catatan Tambahan dari Kepegawaian Universitas:
                        </h4>
                        <div class="prose prose-sm max-w-none text-gray-700 bg-gray-100 p-3 rounded-md">
                            {!! nl2br(e($usulan->catatan_verifikator)) !!}
                        </div>
                    </div>
                @endif

            </div>
        </div>
    @endif
@endif
