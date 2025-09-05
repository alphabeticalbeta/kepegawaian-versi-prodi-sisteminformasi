{{-- Validation Table untuk Usulan NUPTK --}}
<form action="{{ route('backend.kepegawaian-universitas.usulan.save-validasi-nuptk', $usulan->id) }}" method="POST" id="validationForm">
    @csrf
    <input type="hidden" name="action_type" value="save_only">
    
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i data-lucide="check-square" class="w-6 h-6 mr-3"></i>
                Tabel Validasi Usulan NUPTK
                @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_TIM_SISTER)
                    <span class="ml-3 px-3 py-1 bg-orange-100 text-orange-800 text-sm font-medium rounded-full border border-orange-200">
                        <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>
                        View Only
                    </span>
                @endif
                @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_SISTER)
                    <span class="ml-3 px-3 py-1 bg-orange-100 text-orange-800 text-sm font-medium rounded-full border border-orange-200">
                        <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>
                        View Only
                    </span>
                @endif
                @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_SISTER)
                    <span class="ml-3 px-3 py-1 bg-orange-100 text-orange-800 text-sm font-medium rounded-full border border-orange-200">
                        <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>
                        View Only
                    </span>
                @endif
                @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS)
                    <span class="ml-3 px-3 py-1 bg-orange-100 text-orange-800 text-sm font-medium rounded-full border border-orange-200">
                        <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>
                        View Only
                    </span>
                @endif
            </h2>
        </div>

        @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_TIM_SISTER)
            <div class="bg-red-50 border-b border-red-200 px-6 py-4">
                <div class="flex items-center">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600 mr-3"></i>
                    <div>
                        <div class="text-sm font-semibold text-red-800 mb-1">
                            Status: Permintaan Perbaikan ke Pegawai
                        </div>
                        <div class="text-sm text-red-700">
                            <strong>Perhatian:</strong> Usulan telah dikembalikan ke pegawai untuk perbaikan. 
                            Semua field validasi sekarang dalam mode <strong>View Only</strong> dan tidak dapat diedit. 
                            Pegawai harus memperbaiki data yang tidak sesuai sebelum dapat diajukan kembali.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Field-Field Tidak Sesuai untuk Status SISTER --}}
            @php
                $sisterValidation = $usulan->getValidasiByRole('kepegawaian_universitas') ?? ['validation' => []];
                $sisterInvalidFields = [];
                if (isset($sisterValidation['validation'])) {
                    foreach ($sisterValidation['validation'] as $groupKey => $groupData) {
                        if (is_array($groupData)) {
                            foreach ($groupData as $fieldKey => $fieldData) {
                                if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                                    $sisterInvalidFields[] = [
                                        'group' => $groupKey,
                                        'field' => $fieldKey,
                                        'keterangan' => $fieldData['keterangan'] ?? ''
                                    ];
                                }
                            }
                        }
                    }
                }
            @endphp

            @if(!empty($sisterInvalidFields))
                <div class="bg-orange-50 border-b border-orange-200 px-6 py-4">
                    <div class="flex items-start">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-orange-600 mr-3 mt-0.5"></i>
                        <div class="flex-1">
                            <div class="text-sm font-semibold text-orange-800 mb-2">
                                Field-Field Tidak Sesuai dari Tim SISTER:
                            </div>
                            <div class="space-y-2">
                                @foreach($sisterInvalidFields as $invalidField)
                                    @php
                                        $groupLabel = $config['validationFields'][$invalidField['group']] ?? ucfirst(str_replace('_', ' ', $invalidField['group']));
                                        $fieldLabel = $fieldGroups[$invalidField['group']]['fields'][$invalidField['field']] ?? ucfirst(str_replace('_', ' ', $invalidField['field']));
                                    @endphp
                                    <div class="bg-white border border-orange-200 rounded-lg p-3">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="text-sm font-medium text-orange-800">
                                                    {{ $groupLabel }} - {{ $fieldLabel }}
                                                </div>
                                                @if(!empty($invalidField['keterangan']))
                                                    <div class="text-sm text-orange-700 mt-1">
                                                        <strong>Keterangan:</strong> {{ $invalidField['keterangan'] }}
                                                    </div>
                                                @endif
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">
                                                Tidak Sesuai
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Keterangan Umum untuk Status SISTER --}}
                @if(!empty($sisterValidation['keterangan_umum'] ?? ''))
                    <div class="bg-purple-50 border-b border-purple-200 px-6 py-4">
                        <div class="flex items-start">
                            <i data-lucide="sticky-note" class="w-5 h-5 text-purple-600 mr-3 mt-0.5"></i>
                            <div class="flex-1">
                                <div class="text-sm font-semibold text-purple-800 mb-2">
                                    Keterangan Umum dari Tim SISTER:
                                </div>
                                <div class="bg-white border border-purple-200 rounded-lg p-3">
                                    <div class="text-sm text-purple-700">
                                        {{ $sisterValidation['keterangan_umum'] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        @endif

        @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_SISTER)
            <div class="bg-red-50 border-b border-red-200 px-6 py-4">
                <div class="flex items-center">
                    <i data-lucide="x-circle" class="w-5 h-5 text-red-600 mr-3"></i>
                    <div>
                        <div class="text-sm font-semibold text-red-800 mb-1">
                            Status: Usulan Tidak Direkomendasikan SISTER
                        </div>
                        <div class="text-sm text-red-700">
                            <strong>Perhatian:</strong> Usulan telah ditolak oleh Tim SISTER dan tidak direkomendasikan. 
                            Semua field validasi sekarang dalam mode <strong>View Only</strong> dan tidak dapat diedit. 
                            Pegawai harus memperbaiki data yang tidak sesuai sebelum dapat diajukan kembali.
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_SISTER)
            <div class="bg-green-50 border-b border-green-200 px-6 py-4">
                <div class="flex items-center">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600 mr-3"></i>
                    <div>
                        <div class="text-sm font-semibold text-green-800 mb-1">
                            Status: Usulan Direkomendasikan SISTER
                        </div>
                        <div class="text-sm text-green-700">
                            <strong>Selamat:</strong> Usulan telah direkomendasikan oleh Tim SISTER dan disetujui. 
                            Semua field validasi sekarang dalam mode <strong>View Only</strong> dan tidak dapat diedit. 
                            Usulan telah berhasil melewati tahap validasi SISTER.
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS)
            <div class="bg-red-50 border-b border-red-200 px-6 py-4">
                <div class="flex items-center">
                    <i data-lucide="x-circle" class="w-5 h-5 text-red-600 mr-3"></i>
                    <div>
                        <div class="text-sm font-semibold text-red-800 mb-1">
                            Status: Usulan Tidak Direkomendasikan Kepegawaian Universitas
                        </div>
                        <div class="text-sm text-red-700">
                            <strong>Perhatian:</strong> Usulan telah ditolak oleh Kepegawaian Universitas dan tidak direkomendasikan. 
                            Semua field validasi sekarang dalam mode <strong>View Only</strong> dan tidak dapat diedit. 
                            Pegawai harus memperbaiki data yang tidak sesuai sebelum dapat diajukan kembali.
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Data Usulan
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Validasi
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Keterangan
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        // Gunakan validasi dari Kepegawaian Universitas untuk tampilan Pegawai
                        $displayValidation = $existingValidation;
                        if ($currentRole === 'Pegawai') {
                            $displayValidation = $usulan->getValidasiByRole('Kepegawaian Universitas') ?? ['validation' => [], 'keterangan_umum' => ''];
                        }
                    @endphp
                    @foreach($config['validationFields'] as $groupKey)
                        @if(isset($fieldGroups[$groupKey]))
                            @php $group = $fieldGroups[$groupKey]; @endphp

                            {{-- Group Header --}}
                            <tr class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500">
                                <td colspan="3" class="px-6 py-4">
                                    <div class="flex items-center">
                                        <i data-lucide="{{ $group['icon'] }}" class="w-5 h-5 mr-3 text-blue-600"></i>
                                        <span class="font-bold text-blue-800 text-lg">{{ $group['label'] }}</span>
                                    </div>
                                </td>
                            </tr>

                            {{-- Fields dalam group --}}
                            @foreach($group['fields'] as $fieldKey => $fieldLabel)
                                @php
                                    // Get existing validation for this field
                                    $fieldValidation = $displayValidation['validation'][$groupKey][$fieldKey] ?? [
                                        'status' => 'sesuai',
                                        'keterangan' => ''
                                    ];

                                    // Check if field is invalid
                                    $isInvalid = $fieldValidation['status'] === 'tidak_sesuai';

                                    // Check if current role can edit
                                    $canEdit = $currentRole === 'Kepegawaian Universitas' && 
                                               $usulan->status_usulan !== \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_TIM_SISTER &&
                                               $usulan->status_usulan !== \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_SISTER &&
                                               $usulan->status_usulan !== \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_SISTER &&
                                               $usulan->status_usulan !== \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS;
                                @endphp
                                <tr class="hover:bg-gray-50 {{ $isInvalid ? 'bg-red-50' : '' }}">
                                    <td class="px-6 py-4">
                                        <div class="text-base font-semibold text-gray-900">{{ $fieldLabel }}</div>
                                        <div class="text-sm text-gray-500">
                                            @php
                                                $value = '';
                                                if ($groupKey === 'data_pribadi') {
                                                    if ($fieldKey === 'tanggal_lahir') {
                                                        $value = $usulan->pegawai->$fieldKey ? \Carbon\Carbon::parse($usulan->pegawai->$fieldKey)->isoFormat('D MMMM YYYY') : '-';
                                                    } elseif ($fieldKey === 'nik') {
                                                        $value = $usulan->data_usulan['nik'] ?? '-';
                                                    } elseif ($fieldKey === 'nama_ibu_kandung') {
                                                        $value = $usulan->data_usulan['nama_ibu_kandung'] ?? '-';
                                                    } elseif ($fieldKey === 'status_kawin') {
                                                        $value = $usulan->data_usulan['status_kawin'] ?? '-';
                                                    } elseif ($fieldKey === 'agama') {
                                                        $value = $usulan->data_usulan['agama'] ?? '-';
                                                    } elseif ($fieldKey === 'alamat_lengkap') {
                                                        $value = $usulan->data_usulan['alamat_lengkap'] ?? '-';
                                                    } else {
                                                        $value = $usulan->pegawai->$fieldKey ?? '-';
                                                    }
                                                } elseif ($groupKey === 'data_kepegawaian') {
                                                    if ($fieldKey === 'jabatan_saat_usul') {
                                                        $value = $usulan->pegawai->jabatan?->jabatan ?? '-';
                                                    } elseif ($fieldKey === 'unit_kerja_saat_usul') {
                                                        $value = $usulan->pegawai->unitKerja?->nama ?? '-';
                                                    } elseif (str_starts_with($fieldKey, 'tmt_')) {
                                                        $value = $usulan->pegawai->$fieldKey ? \Carbon\Carbon::parse($usulan->pegawai->$fieldKey)->isoFormat('D MMMM YYYY') : '-';
                                                    } else {
                                                        $value = $usulan->pegawai->$fieldKey ?? '-';
                                                    }
                                                } elseif ($groupKey === 'data_pendidikan') {
                                                    if ($fieldKey === 'jenis_nuptk') {
                                                        $value = ucwords(str_replace('_', ' ', $usulan->jenis_nuptk ?? '-'));
                                                    } else {
                                                        $value = $usulan->pegawai->$fieldKey ?? '-';
                                                    }
                                                } elseif ($groupKey === 'data_kinerja') {
                                                    $value = $usulan->pegawai->$fieldKey ?? '-';
                                                } elseif ($groupKey === 'dokumen_profil') {
                                                    if ($usulan->pegawai->$fieldKey) {
                                                        $route = route('backend.kepegawaian-universitas.usulan.show-pegawai-document', [$usulan->id, $fieldKey]);
                                                        $value = '<a href="' . e($route) . '" target="_blank" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors"><i data-lucide="eye" class="w-4 h-4 mr-2"></i>Lihat Dokumen</a>';
                                                    } else {
                                                        $value = '<span class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-gray-100 rounded-lg"><i data-lucide="file-x" class="w-4 h-4 mr-2"></i>Dokumen tidak tersedia</span>';
                                                    }
                                                } elseif ($groupKey === 'dokumen_usulan') {
                                                    // Check multiple possible locations for document path
                                                    $docPath = null;

                                                    // Check new structure first
                                                    if (isset($usulan->data_usulan['dokumen_usulan'][$fieldKey]['path'])) {
                                                        $docPath = $usulan->data_usulan['dokumen_usulan'][$fieldKey]['path'];
                                                    }
                                                    // Check old structure
                                                    elseif (isset($usulan->data_usulan[$fieldKey])) {
                                                        $docPath = $usulan->data_usulan[$fieldKey];
                                                    }
                                                    // Check using getDocumentPath method
                                                    else {
                                                        $docPath = $usulan->getDocumentPath($fieldKey);
                                                    }

                                                    if ($docPath) {
                                                        $route = route('backend.kepegawaian-universitas.usulan.show-document', [$usulan->id, $fieldKey]);
                                                        $value = '<a href="' . e($route) . '" target="_blank" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors"><i data-lucide="file-text" class="w-4 h-4 mr-2"></i>Lihat Dokumen</a>';
                                                    } else {
                                                        $value = '<span class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-gray-100 rounded-lg"><i data-lucide="file-x" class="w-4 h-4 mr-2"></i>Dokumen tidak tersedia</span>';
                                                    }
                                                }
                                            @endphp
                                            {!! $value !!}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($canEdit)
                                            <select name="validation[{{ $groupKey }}][{{ $fieldKey }}][status]"
                                                    class="validation-status block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base px-4 py-3"
                                                    data-group="{{ $groupKey }}"
                                                    data-field="{{ $fieldKey }}">
                                                <option value="sesuai" {{ $fieldValidation['status'] === 'sesuai' ? 'selected' : '' }}>Sesuai</option>
                                                <option value="tidak_sesuai" {{ $fieldValidation['status'] === 'tidak_sesuai' ? 'selected' : '' }}>Tidak Sesuai</option>
                                            </select>
                                        @else
                                            <div class="flex items-center">
                                                @if($fieldValidation['status'] === 'sesuai')
                                                    <span class="inline-flex items-center px-3 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                                        <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>
                                                        Sesuai
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-3 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                                        <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i>
                                                        Tidak Sesuai
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($canEdit)
                                            <textarea name="validation[{{ $groupKey }}][{{ $fieldKey }}][keterangan]"
                                                      class="validation-keterangan block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base px-4 py-3 {{ $fieldValidation['status'] !== 'tidak_sesuai' ? 'bg-gray-100' : '' }}"
                                                      rows="3"
                                                      placeholder="Keterangan (wajib jika tidak sesuai)"
                                                      {{ $fieldValidation['status'] !== 'tidak_sesuai' ? 'disabled' : '' }}>{{ $fieldValidation['keterangan'] ?? '' }}</textarea>
                                        @else
                                            <div class="text-sm text-gray-600">
                                                {{ $fieldValidation['keterangan'] ?? '-' }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                    
                    @if($currentRole === 'Kepegawaian Universitas' || $currentRole === 'Pegawai')
                        @php
                            $generalNote = $displayValidation['keterangan_umum'] ?? '';
                            $canEditGeneralNote = $currentRole === 'Kepegawaian Universitas' && 
                                                  $usulan->status_usulan !== \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_TIM_SISTER &&
                                                  $usulan->status_usulan !== \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_SISTER &&
                                                  $usulan->status_usulan !== \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_SISTER &&
                                                  $usulan->status_usulan !== \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS;
                        @endphp
                        <tr class="bg-gray-50">
                            <td colspan="3" class="px-6 py-6">
                                <div class="text-center">
                                    <div class="flex items-center justify-start mb-4 w-11/12 mx-auto">
                                        <i data-lucide="sticky-note" class="w-5 h-5 mr-3 text-gray-600"></i>
                                        <span class="text-lg font-semibold text-gray-800">Keterangan Umum</span>
                                    </div>
                                    @if($canEditGeneralNote)
                                        <textarea name="keterangan_umum" 
                                                class="block w-11/12 mx-auto border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base px-6 py-4"
                                                rows="6"
                                                placeholder="Keterangan umum untuk usulan ini">{{ $generalNote }}</textarea>
                                    @else
                                        <div class="text-base text-gray-600 bg-white border border-gray-200 rounded-lg px-6 py-4 w-11/12 mx-auto">{{ $generalNote !== '' ? $generalNote : '-' }}</div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- Action Buttons --}}
    @if($currentRole === 'Kepegawaian Universitas')
        <div class="flex flex-wrap gap-4 items-center justify-between mt-6">
            {{-- Status Change Buttons --}}
            @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS || 
                 $usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS)
            <div class="flex gap-3">
                {{-- Button Permintaan Perbaikan ke Pegawai --}}
                <button type="button" 
                        onclick="saveAndChangeStatus('{{ \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_TIM_SISTER }}')"
                        class="flex items-center px-4 py-3 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
                    <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                    Permintaan Perbaikan ke Pegawai
                </button>

                {{-- Button Tidak Direkomendasikan --}}
                <button type="button" 
                        onclick="saveAndChangeStatus('{{ \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS }}')"
                        class="flex items-center px-4 py-3 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
                    <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i>
                    Tidak Direkomendasikan Kepegawaian Universitas
                </button>

                {{-- Button Kirim ke SISTER --}}
                <button type="button" 
                        onclick="saveAndChangeStatus('{{ \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_SUDAH_DIKIRIM_KE_SISTER }}')"
                        class="flex items-center px-4 py-3 bg-indigo-500 hover:bg-indigo-600 text-white font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
                    <i data-lucide="send" class="w-4 h-4 mr-2"></i>
                    Kirim Usulan ke SISTER
                </button>
            </div>
            @endif

            {{-- Status Change Buttons for "Usulan Sudah Dikirim ke SISTER" --}}
            @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_SUDAH_DIKIRIM_KE_SISTER || $usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_SISTER)
            <div class="flex gap-3">
                {{-- Button Permintaan Perbaikan Ke Pegawai Dari SISTER --}}
                <button type="button" 
                        onclick="saveAndChangeStatus('{{ \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_TIM_SISTER }}')"
                        class="flex items-center px-4 py-3 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
                    <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                    Permintaan Perbaikan Ke Pegawai Dari SISTER
                </button>

                {{-- Button Belum Direkomendasikan Dari SISTER --}}
                <button type="button" 
                        onclick="saveAndChangeStatus('{{ \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_SISTER }}')"
                        class="flex items-center px-4 py-3 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
                    <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i>
                    Belum Direkomendasikan Dari SISTER
                </button>

                {{-- Button Usulan Direkomendasi SISTER --}}
                <button type="button" 
                        onclick="saveAndChangeStatus('{{ \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_SISTER }}')"
                        class="flex items-center px-4 py-3 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
                    <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>
                    Usulan Direkomendasi SISTER
                </button>
            </div>
        @endif

            {{-- Save Validation Button --}}
            @if($usulan->status_usulan !== \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_TIM_SISTER && 
                 $usulan->status_usulan !== \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_SISTER &&
                 $usulan->status_usulan !== \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_SISTER &&
                 $usulan->status_usulan !== \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS)
            <button type="submit" id="saveValidationBtn" 
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                Simpan Validasi
            </button>
            @endif
        </div>
    @endif
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle validation status change
    const validationStatusSelects = document.querySelectorAll('.validation-status');
    validationStatusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const group = this.dataset.group;
            const field = this.dataset.field;
            const status = this.value;
            
            // Find corresponding keterangan textarea
            const keteranganTextarea = document.querySelector(`textarea[name="validation[${group}][${field}][keterangan]"]`);
            if (keteranganTextarea) {
                if (status === 'tidak_sesuai') {
                    keteranganTextarea.disabled = false;
                    keteranganTextarea.classList.remove('bg-gray-100');
                } else {
                    keteranganTextarea.disabled = true;
                    keteranganTextarea.classList.add('bg-gray-100');
                    keteranganTextarea.value = '';
                }
            }
        });
    });

    // Handle save validation button click
    const saveValidationBtn = document.getElementById('saveValidationBtn');
    if (saveValidationBtn) {
        saveValidationBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Use submitAction function like in jabatan module
            if (typeof submitAction === 'function') {
                submitAction('save_only', '');
            } else {
                // Fallback: submit form directly
                const form = document.getElementById('validationForm');
                if (form) {
                    form.submit();
                }
            }
        });
    }

    // Function to save validation and then change status
    window.saveAndChangeStatus = function(newStatus) {
        // Show loading state
        const loadingText = 'Memproses permintaan perbaikan...';
        Swal.fire({
            title: 'Memproses Permintaan Perbaikan',
            text: loadingText,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Get form data
        const form = document.getElementById('validationForm');
        const formData = new FormData(form);
        formData.append('action_type', 'save_only');

        // Save validation first
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // If save successful, change status directly without showing intermediate modal
                changeStatus(newStatus);
            } else {
                // Show error if save failed
                Swal.fire({
                    title: 'Gagal Menyimpan',
                    text: data.message || 'Terjadi kesalahan saat menyimpan validasi',
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Gagal Menyimpan',
                text: 'Terjadi kesalahan saat menyimpan validasi',
                icon: 'error'
            });
        });
    };
});
</script>
