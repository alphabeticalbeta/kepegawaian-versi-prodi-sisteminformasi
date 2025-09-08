{{-- Validation Table untuk Usulan Tugas Belajar --}}
<form action="{{ route('backend.kepegawaian-universitas.usulan.save-validasi-tubel', $usulan->id) }}" method="POST" id="validationForm">
    @csrf
    <input type="hidden" name="action_type" value="save_only">

    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i data-lucide="check-square" class="w-6 h-6 mr-3"></i>
                Tabel Validasi Usulan Tugas Belajar
                <span class="ml-3 px-3 py-1 bg-white/20 text-white text-sm font-medium rounded-full">
                    {{ $usulan->data_usulan['jenis_tubel'] ?? 'Tugas Belajar' }}
                </span>
                @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN)
                    <span class="ml-3 px-3 py-1 bg-orange-100 text-orange-800 text-sm font-medium rounded-full border border-orange-200">
                        <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>
                        View Only
                    </span>
                @endif
                @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEMENTERIAN)
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

        @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN)
            <div class="bg-red-50 border-b border-red-200 px-6 py-4">
                <div class="flex items-center">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600 mr-3"></i>
                    <div>
                        <div class="text-sm font-semibold text-red-800 mb-1">
                            Status: Permintaan Perbaikan ke Pegawai dari Kementerian
                        </div>
                        <div class="text-sm text-red-700">
                            <strong>Perhatian:</strong> Usulan telah dikembalikan ke pegawai untuk perbaikan.
                            Semua field validasi sekarang dalam mode <strong>View Only</strong> dan tidak dapat diedit.
                            Pegawai harus memperbaiki data yang tidak sesuai sebelum dapat diajukan kembali.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Field-Field Tidak Sesuai untuk Status Kementerian --}}
            @php
                $kementerianValidation = $usulan->getValidasiByRole('kepegawaian_universitas') ?? ['validation' => []];
                $kementerianInvalidFields = [];
                if (isset($kementerianValidation['validation'])) {
                    foreach ($kementerianValidation['validation'] as $groupKey => $groupData) {
                        if (is_array($groupData)) {
                            foreach ($groupData as $fieldKey => $fieldData) {
                                if (isset($fieldData['status']) && $fieldData['status'] === 'tidak_sesuai') {
                                    $kementerianInvalidFields[] = [
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

            @if(!empty($kementerianInvalidFields))
                <div class="bg-orange-50 border-b border-orange-200 px-6 py-4">
                    <div class="flex items-start">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-orange-600 mr-3 mt-0.5"></i>
                        <div class="flex-1">
                            <div class="text-sm font-semibold text-orange-800 mb-2">
                                Field-Field Tidak Sesuai dari Kementerian:
                            </div>
                            <div class="space-y-2">
                                @foreach($kementerianInvalidFields as $invalidField)
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

                {{-- Keterangan Umum untuk Status Kementerian --}}
                @if(!empty($kementerianValidation['keterangan_umum'] ?? ''))
                    <div class="bg-purple-50 border-b border-purple-200 px-6 py-4">
                        <div class="flex items-start">
                            <i data-lucide="sticky-note" class="w-5 h-5 text-purple-600 mr-3 mt-0.5"></i>
                            <div class="flex-1">
                                <div class="text-sm font-semibold text-purple-800 mb-2">
                                    Keterangan Umum dari Kementerian:
                                </div>
                                <div class="bg-white border border-purple-200 rounded-lg p-3">
                                    <div class="text-sm text-purple-700">
                                        {{ $kementerianValidation['keterangan_umum'] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        @endif

        @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEMENTERIAN)
            <div class="bg-red-50 border-b border-red-200 px-6 py-4">
                <div class="flex items-center">
                    <i data-lucide="x-circle" class="w-5 h-5 text-red-600 mr-3"></i>
                    <div>
                        <div class="text-sm font-semibold text-red-800 mb-1">
                            Status: Usulan Tidak Direkomendasikan Kementerian
                        </div>
                        <div class="text-sm text-red-700">
                            <strong>Perhatian:</strong> Usulan telah ditolak oleh Kementerian dan tidak direkomendasikan.
                            Semua field validasi sekarang dalam mode <strong>View Only</strong> dan tidak dapat diedit.
                            Pegawai harus memperbaiki data yang tidak sesuai sebelum dapat diajukan kembali.
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
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 35%;">
                            Data Usulan
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 25%;">
                            Validasi
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 40%;">
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
                                <td colspan="3" class="px-6 py-4 w-full">
                                    <div class="flex items-center">
                                        <i data-lucide="{{ $group['icon'] ?? 'folder' }}" class="w-5 h-5 mr-3 text-blue-600"></i>
                                        <span class="font-bold text-blue-800 text-lg">{{ $group['label'] ?? $group['title'] ?? ucfirst(str_replace('_', ' ', $groupKey)) }}</span>
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
                                    $canEdit = !$isViewOnly &&
                                               $currentRole === 'Kepegawaian Universitas' &&
                                               $usulan->status_usulan !== \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN &&
                                               $usulan->status_usulan !== \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEMENTERIAN &&
                                               $usulan->status_usulan !== \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS;
                                @endphp
                                <tr class="hover:bg-gray-50 {{ $isInvalid ? 'bg-red-50' : '' }}">
                                    <td class="px-6 py-4" style="width: 35%;">
                                        <div class="text-sm font-medium text-gray-600 mb-1">{{ $fieldLabel }}</div>
                                        <div class="text-lg font-semibold text-gray-900">
                                            @php
                                                $value = '';
                                                if ($groupKey === 'data_pribadi') {
                                                    if ($fieldKey === 'tanggal_lahir') {
                                                        $value = $usulan->pegawai->$fieldKey ? \Carbon\Carbon::parse($usulan->pegawai->$fieldKey)->isoFormat('D MMMM YYYY') : '-';
                                                    } elseif ($fieldKey === 'tmt_pangkat') {
                                                        $value = $usulan->pegawai->$fieldKey ? \Carbon\Carbon::parse($usulan->pegawai->$fieldKey)->isoFormat('D MMMM YYYY') : '-';
                                                    } elseif ($fieldKey === 'tmt_jabatan') {
                                                        $value = $usulan->pegawai->$fieldKey ? \Carbon\Carbon::parse($usulan->pegawai->$fieldKey)->isoFormat('D MMMM YYYY') : '-';
                                                    } elseif ($fieldKey === 'pangkat') {
                                                        $pangkatValue = '-';
                                                        if ($usulan->pegawai && $usulan->pegawai->pangkat) {
                                                            $pangkatValue = $usulan->pegawai->pangkat->pangkat;
                                                        }
                                                        $value = $pangkatValue;
                                                    } elseif ($fieldKey === 'jabatan') {
                                                        $jabatanValue = '-';
                                                        if ($usulan->pegawai && $usulan->pegawai->jabatan) {
                                                            $jabatanValue = $usulan->pegawai->jabatan->jabatan;
                                                        }
                                                        $value = $jabatanValue;
                                                    } else {
                                                        $value = $usulan->pegawai->$fieldKey ?? '-';
                                                    }
                                                } elseif ($groupKey === 'data_pendidikan') {
                                                    $value = $usulan->pegawai->$fieldKey ?? '-';
                                                } elseif ($groupKey === 'data_usulan_tugas_belajar') {
                                                    $value = $usulan->data_usulan[$fieldKey] ?? '-';
                                                } elseif ($groupKey === 'dokumen_tugas_belajar' || $groupKey === 'dokumen_perpanjangan_tubel' || $groupKey === 'dokumen_tubel') {
                                                    // Check document path
                                                    $docPath = null;
                                                    if (isset($usulan->data_usulan['dokumen_usulan'][$fieldKey]['path'])) {
                                                        $docPath = $usulan->data_usulan['dokumen_usulan'][$fieldKey]['path'];
                                                    } elseif (isset($usulan->data_usulan[$fieldKey])) {
                                                        $docPath = $usulan->data_usulan[$fieldKey];
                                                    }

                                                    if ($docPath) {
                                                        $route = route('backend.kepegawaian-universitas.usulan.show-document', [$usulan->id, $fieldKey]);
                                                        $value = '<a href="' . e($route) . '" target="_blank" class="inline-flex items-center px-4 py-2.5 text-base font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105"><i data-lucide="file-text" class="w-5 h-5 mr-2"></i>Lihat Dokumen</a>';
                                                    } else {
                                                        $value = '<span class="inline-flex items-center px-4 py-2.5 text-base font-medium text-gray-500 bg-gray-100 rounded-lg border border-gray-200"><i data-lucide="file-x" class="w-5 h-5 mr-2"></i>Dokumen tidak tersedia</span>';
                                                    }
                                                } else {
                                                    $value = $usulan->data_usulan[$fieldKey] ?? '-';
                                                }
                                            @endphp
                                            {!! $value !!}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4" style="width: 25%;">
                                        @if($canEdit)
                                            <select name="validation[{{ $groupKey }}][{{ $fieldKey }}][status]"
                                                    class="validation-status block w-full border-2 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg font-semibold px-4 py-3 bg-white"
                                                    data-group="{{ $groupKey }}"
                                                    data-field="{{ $fieldKey }}">
                                                <option value="sesuai" {{ $fieldValidation['status'] === 'sesuai' ? 'selected' : '' }}>Sesuai</option>
                                                <option value="tidak_sesuai" {{ $fieldValidation['status'] === 'tidak_sesuai' ? 'selected' : '' }}>Tidak Sesuai</option>
                                            </select>
                                        @else
                                            <div class="flex items-center">
                                                @if($fieldValidation['status'] === 'sesuai')
                                                    <span class="inline-flex items-center px-4 py-2.5 rounded-lg text-base font-bold bg-green-100 text-green-800 border-2 border-green-200 shadow-sm">
                                                        <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                                                        Sesuai
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-4 py-2.5 rounded-lg text-base font-bold bg-red-100 text-red-800 border-2 border-red-200 shadow-sm">
                                                        <i data-lucide="x-circle" class="w-5 h-5 mr-2"></i>
                                                        Tidak Sesuai
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4" style="width: 40%;">
                                        @if($canEdit)
                                            <textarea name="validation[{{ $groupKey }}][{{ $fieldKey }}][keterangan]"
                                                      class="validation-keterangan block w-full border-2 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base px-4 py-3 {{ $fieldValidation['status'] !== 'tidak_sesuai' ? 'bg-gray-100' : 'bg-white' }}"
                                                      rows="3"
                                                      placeholder="Keterangan (wajib jika tidak sesuai)"
                                                      {{ $fieldValidation['status'] !== 'tidak_sesuai' ? 'disabled' : '' }}>{{ $fieldValidation['keterangan'] ?? '' }}</textarea>
                                        @else
                                            <div class="text-base font-medium text-gray-800 leading-relaxed">
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
                            $canEditGeneralNote = !$isViewOnly &&
                                                  $currentRole === 'Kepegawaian Universitas' &&
                                                  $usulan->status_usulan !== \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN &&
                                                  $usulan->status_usulan !== \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEMENTERIAN &&
                                                  $usulan->status_usulan !== \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS;
                        @endphp
                        <tr class="bg-gray-50">
                            <td colspan="3" class="px-6 py-6 w-full">
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

    {{-- Action Buttons --}}
    @if($currentRole === 'Kepegawaian Universitas')
        <div class="flex flex-wrap gap-4 items-center justify-between mt-6">
            {{-- Status Change Buttons --}}
            @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS ||
                 $usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS)
            <div class="flex gap-3">
                {{-- Button Permintaan Perbaikan ke Pegawai --}}
                <button type="button"
                        onclick="saveAndChangeStatus('{{ \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS }}')"
                        class="flex items-center px-4 py-3 bg-orange-500 hover:bg-orange-600 text-black font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
                    <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                    Permintaan Perbaikan ke Pegawai Dari Kepegawaian Universitas
                </button>

                {{-- Button Tidak Direkomendasikan --}}
                <button type="button"
                        onclick="saveAndChangeStatus('{{ \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS }}')"
                        class="flex items-center px-4 py-3 bg-red-500 hover:bg-red-600 text-black font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
                    <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i>
                    Tidak Direkomendasikan Kepegawaian Universitas
                </button>

                {{-- Button Kirim ke Kementerian --}}
                <button type="button"
                        onclick="saveAndChangeStatus('{{ \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_SUDAH_DIKIRIM_KE_KEMENTERIAN }}')"
                        class="flex items-center px-4 py-3 bg-indigo-500 hover:bg-indigo-600 text-white font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
                    <i data-lucide="send" class="w-4 h-4 mr-2"></i>
                    Kirim Usulan ke Kementerian
                </button>
            </div>
            @endif

            {{-- Status Change Buttons for "Usulan Sudah Dikirim ke Kementerian" --}}
            @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_SUDAH_DIKIRIM_KE_KEMENTERIAN || $usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEMENTERIAN)
            <div class="flex gap-3">
                {{-- Button Permintaan Perbaikan Ke Pegawai Dari Kementerian --}}
                <button type="button"
                        onclick="saveAndChangeStatus('{{ \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN }}')"
                        class="flex items-center px-4 py-3 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
                    <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                    Permintaan Perbaikan Ke Pegawai Dari Kementerian
                </button>

                {{-- Button Belum Direkomendasikan Dari Kementerian --}}
                <button type="button"
                        onclick="saveAndChangeStatus('{{ \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEMENTERIAN }}')"
                        class="flex items-center px-4 py-3 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
                    <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i>
                    Belum Direkomendasikan Dari Kementerian
                </button>

                {{-- Button Usulan Direkomendasi Kementerian --}}
                <button type="button"
                        onclick="saveAndChangeStatus('{{ \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_KEMENTERIAN }}')"
                        class="flex items-center px-4 py-3 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
                    <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>
                    Usulan Direkomendasi Kementerian
                </button>
            </div>
        @endif

            {{-- Save Validation Button --}}
            @if($usulan->status_usulan !== \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN &&
                 $usulan->status_usulan !== \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEMENTERIAN &&
                 $usulan->status_usulan !== \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS &&
                 $usulan->status_usulan !== \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS &&
                 $usulan->status_usulan !== \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_KEMENTERIAN)
            <button type="submit" id="saveValidationBtn"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                Simpan Validasi
            </button>
            @endif
        </div>
    @endif
    </div>
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
            customClass: {
                popup: 'dark:bg-gray-800 dark:text-white',
                title: 'dark:text-white',
                content: 'dark:text-gray-200'
            },
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
                    icon: 'error',
                    customClass: {
                        popup: 'dark:bg-gray-800 dark:text-white',
                        title: 'dark:text-white',
                        content: 'dark:text-gray-200',
                        confirmButton: 'dark:bg-red-600 dark:hover:bg-red-700'
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Gagal Menyimpan',
                text: 'Terjadi kesalahan saat menyimpan validasi',
                icon: 'error',
                customClass: {
                    popup: 'dark:bg-gray-800 dark:text-white',
                    title: 'dark:text-white',
                    content: 'dark:text-gray-200',
                    confirmButton: 'dark:bg-red-600 dark:hover:bg-red-700'
                }
            });
        });
    };
});
</script>
