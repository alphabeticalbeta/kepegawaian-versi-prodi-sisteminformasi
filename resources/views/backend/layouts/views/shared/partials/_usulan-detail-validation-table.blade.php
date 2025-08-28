{{-- USULAN DETAIL VALIDATION TABLE PARTIAL --}}
{{-- Usage: @include('backend.layouts.views.shared.partials._usulan-detail-validation-table', ['usulan' => $usulan, 'config' => $config, 'fieldGroups' => $fieldGroups, 'existingValidation' => $existingValidation, 'canEdit' => $canEdit, 'currentRole' => $currentRole]) --}}

{{-- Validation Table --}}
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5">
        <h2 class="text-xl font-bold text-white flex items-center">
            <i data-lucide="check-square" class="w-6 h-6 mr-3"></i>
            Tabel Validasi
        </h2>
    </div>
    
    @if($currentRole === 'Kepegawaian Universitas' && !empty($penilaiValidation ?? []))
        <div class="bg-orange-50 border-b border-orange-200 px-6 py-3">
            <div class="flex items-center">
                <i data-lucide="info" class="w-4 h-4 text-orange-600 mr-2"></i>
                <span class="text-sm text-orange-800">
                    <strong>Info:</strong> Tabel ini menampilkan validasi dari Kepegawaian Universitas dan data validasi dari Tim Penilai (jika ada).
                    Field yang tidak sesuai dari penilai akan ditandai dengan badge oranye.
                </span>
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
                        @if($currentRole === 'Kepegawaian Universitas' && !empty($penilaiValidation ?? []))
                            <div class="text-xs text-orange-600 mt-1">
                                <i data-lucide="alert-triangle" class="w-3 h-3 inline mr-1"></i>
                                + Penilai
                            </div>
                        @endif
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Keterangan
                        @if($currentRole === 'Kepegawaian Universitas' && !empty($penilaiValidation ?? []))
                            <div class="text-xs text-orange-600 mt-1">
                                <i data-lucide="alert-triangle" class="w-3 h-3 inline mr-1"></i>
                                + Penilai
                            </div>
                        @endif
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($config['validationFields'] as $groupKey)
                    @if(isset($fieldGroups[$groupKey]))
                        {{-- Skip dokumen_admin_fakultas untuk role yang tidak diizinkan --}}
                        @if($groupKey === 'dokumen_admin_fakultas' && $currentRole !== 'Admin Universitas' && $currentRole !== 'Admin Fakultas' && $currentRole !== 'Penilai Universitas' && $currentRole !== 'Kepegawaian Universitas')
                            @continue
                        @endif
                        
                        {{-- Skip dokumen_admin_fakultas untuk Admin Fakultas jika status tidak sesuai --}}
                        @if($groupKey === 'dokumen_admin_fakultas' && $currentRole === 'Admin Fakultas' && !in_array($usulan->status_usulan, ['Diusulkan ke Universitas', 'Sedang Direview', 'Direkomendasikan', 'Disetujui', 'Ditolak', 'Perbaikan Usulan', 'Diajukan']))
                            @continue
                        @endif
                        
                        {{-- Skip syarat_guru_besar jika jabatan tujuan bukan Guru Besar --}}
                        @if($groupKey === 'syarat_guru_besar')
                            @php
                                $jabatanTujuan = $usulan->jabatanTujuan->jabatan ?? '';
                                $isGuruBesar = stripos($jabatanTujuan, 'guru besar') !== false || stripos($jabatanTujuan, 'professor') !== false;
                            @endphp
                            @if(!$isGuruBesar)
                                @continue
                            @endif
                        @endif
                        @php $group = $fieldGroups[$groupKey]; @endphp

                        @if($groupKey === 'dokumen_admin_fakultas' && isset($group['isEditableForm']) && $group['isEditableForm'])
                            {{-- Tampilan khusus untuk form input dokumen admin fakultas --}}
                            <tr class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500">
                                <td colspan="3" class="px-6 py-4">
                                    <div class="flex items-center">
                                        <i data-lucide="{{ $group['icon'] }}" class="w-5 h-5 mr-3 text-blue-600"></i>
                                        <span class="font-bold text-blue-800 text-lg">{{ $group['label'] }}</span>
                                    </div>
                                    <p class="text-sm text-blue-600 mt-1 ml-8">
                                        ⓘ Isi semua field dokumen pendukung yang diperlukan sebelum mengirim ke universitas
                                    </p>
                                </td>
                            </tr>
                            {{-- Baris pertama: Nomor Surat Usulan dan File Surat Usulan --}}
                            <tr class="hover:bg-blue-50 transition-colors">
                                <td class="px-6 py-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                                Nomor Surat Usulan
                                            </label>
                                            @php
                                                $dokumenPendukung = $usulan->validasi_data['admin_fakultas']['dokumen_pendukung'] ?? [];
                                                $currentValue = $dokumenPendukung['nomor_surat_usulan'] ?? '';
                                            @endphp
                                            <input type="text"
                                                   name="dokumen_pendukung[nomor_surat_usulan]"
                                                   value="{{ e($currentValue) }}"
                                                   class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-sm"
                                                   placeholder="Contoh: 001/FK-UNMUL/2025 (Opsional)">
                                            <small class="text-gray-500 mt-1">Format: Nomor/Fakultas/UNMUL/Tahun (Opsional)</small>
                                        </div>
                                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                                File Surat Usulan
                                            </label>
                                            @php
                                                $currentPath = $dokumenPendukung['file_surat_usulan_path'] ?? null;
                                            @endphp
                                            <div class="space-y-3">
                                                @if($currentPath)
                                                    <div class="text-sm text-gray-600 bg-green-50 p-2 rounded border border-green-200">
                                                        <i data-lucide="check-circle" class="w-4 h-4 inline mr-1 text-green-600"></i>
                                                        File saat ini:
                                                        <a href="{{ asset('storage/' . $currentPath) }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline font-medium">Lihat File</a>
                                                    </div>
                                                @endif
                                                <input type="file"
                                                       name="dokumen_pendukung[file_surat_usulan]"
                                                       accept=".pdf"
                                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-sm">
                                                <small class="text-gray-500">Upload file baru untuk mengganti file yang ada (Opsional)</small>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-6"></td>
                                <td class="px-6 py-6"></td>
                            </tr>
                            {{-- Baris kedua: Nomor Berita Senat dan File Berita Senat --}}
                            <tr class="hover:bg-blue-50 transition-colors">
                                <td class="px-6 py-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                                Nomor Berita Senat
                                            </label>
                                            @php
                                                $currentValue = $dokumenPendukung['nomor_berita_senat'] ?? '';
                                            @endphp
                                            <input type="text"
                                                   name="dokumen_pendukung[nomor_berita_senat]"
                                                   value="{{ e($currentValue) }}"
                                                   class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-sm"
                                                   placeholder="Contoh: 001/Berita-Senat/2025 (Opsional)">
                                            <small class="text-gray-500 mt-1">Format: Nomor/Berita-Senat/Tahun (Opsional)</small>
                                        </div>
                                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                                File Berita Senat
                                            </label>
                                            @php
                                                $currentPath = $dokumenPendukung['file_berita_senat_path'] ?? null;
                                            @endphp
                                            <div class="space-y-3">
                                                @if($currentPath)
                                                    <div class="text-sm text-gray-600 bg-green-50 p-2 rounded border border-green-200">
                                                        <i data-lucide="check-circle" class="w-4 h-4 inline mr-1 text-green-600"></i>
                                                        File saat ini:
                                                        <a href="{{ asset('storage/' . $currentPath) }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline font-medium">Lihat File</a>
                                                    </div>
                                                @endif
                                                <input type="file"
                                                       name="dokumen_pendukung[file_berita_senat]"
                                                       accept=".pdf"
                                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-sm">
                                                <small class="text-gray-500">Upload file baru untuk mengganti file yang ada (Opsional)</small>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-6"></td>
                                <td class="px-6 py-6"></td>
                            </tr>
                        @else
                            {{-- Tampilan normal untuk role lain atau kondisi lain --}}
                            <tr class="bg-gray-50">
                                <td colspan="3" class="px-6 py-3">
                                    <div class="flex items-center">
                                        <i data-lucide="{{ $group['icon'] }}" class="w-4 h-4 mr-2 text-gray-600"></i>
                                        <span class="font-semibold text-gray-800">{{ $group['label'] }}</span>
                                    </div>
                                </td>
                            </tr>
                            @foreach(is_callable($group['fields']) ? $group['fields']() : $group['fields'] as $fieldKey => $fieldLabel)
                            @php
                                $fieldValidation = $existingValidation['validation'][$groupKey][$fieldKey] ?? ['status' => 'sesuai', 'keterangan' => ''];
                                $isInvalid = $fieldValidation['status'] === 'tidak_sesuai';
                                
                                // Cek apakah field ini ada dalam data validasi penilai yang tidak sesuai
                                $penilaiInvalidStatus = null;
                                $penilaiInvalidKeterangan = null;
                                
                                // PERBAIKAN: Hanya tampilkan data validasi penilai untuk Kepegawaian Universitas
                                // Role Penilai Universitas tidak boleh melihat data validasi penilai lain
                                if ($currentRole === 'Kepegawaian Universitas' && !empty($allPenilaiInvalidFields ?? [])) {
                                    // Cek apakah field ini ada dalam data field yang tidak sesuai dari Tim Penilai
                                    if (isset($allPenilaiInvalidFields[$groupKey][$fieldKey])) {
                                        $penilaiFieldData = $allPenilaiInvalidFields[$groupKey][$fieldKey];
                                        $penilaiInvalidStatus = 'tidak_sesuai';
                                        $penilaiInvalidKeterangan = $penilaiFieldData['keterangan'] ?? 'Tidak ada keterangan';
                                    }
                                }
                            @endphp
                            <tr class="hover:bg-gray-50 {{ $isInvalid ? 'bg-red-50' : '' }}">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $fieldLabel }}</div>
                                    <div class="text-sm text-gray-500">
                                        @php
                                            $value = '';
                                            if ($groupKey === 'data_pribadi') {
                                                if ($fieldKey === 'tanggal_lahir') {
                                                    $value = $usulan->pegawai->$fieldKey ? \Carbon\Carbon::parse($usulan->pegawai->$fieldKey)->isoFormat('D MMMM YYYY') : '-';
                                                } else {
                                                    $value = $usulan->pegawai->$fieldKey ?? '-';
                                                }
                                            } elseif ($groupKey === 'data_kepegawaian') {
                                                if ($fieldKey === 'pangkat_saat_usul') {
                                                    $value = $usulan->pegawai->pangkat?->pangkat ?? '-';
                                                } elseif ($fieldKey === 'jabatan_saat_usul') {
                                                    $value = $usulan->pegawai->jabatan?->jabatan ?? '-';
                                                } elseif ($fieldKey === 'unit_kerja_saat_usul') {
                                                    $value = $usulan->pegawai->unitKerja?->nama ?? '-';
                                                } elseif (str_starts_with($fieldKey, 'tmt_')) {
                                                    $value = $usulan->pegawai->$fieldKey ? \Carbon\Carbon::parse($usulan->pegawai->$fieldKey)->isoFormat('D MMMM YYYY') : '-';
                                                } else {
                                                    $value = $usulan->pegawai->$fieldKey ?? '-';
                                                }
                                            } elseif ($groupKey === 'data_pendidikan') {
                                                if ($fieldKey === 'url_profil_sinta' && $usulan->pegawai->$fieldKey) {
                                                    $value = '<a href="' . e($usulan->pegawai->$fieldKey) . '" target="_blank" class="text-indigo-600 hover:text-indigo-800 underline">Buka Profil SINTA</a>';
                                                } else {
                                                    $value = $usulan->pegawai->$fieldKey ?? '-';
                                                }
                                            } elseif ($groupKey === 'data_kinerja') {
                                                if ($fieldKey === 'nilai_konversi' && $usulan->pegawai->$fieldKey) {
                                                    $value = $usulan->pegawai->$fieldKey;
                                                } else {
                                                    $value = $usulan->pegawai->$fieldKey ?? '-';
                                                }
                                            } elseif ($groupKey === 'dokumen_profil') {
                                                if ($usulan->pegawai->$fieldKey) {
                                                    $route = route($config['routePrefix'] . '.show-pegawai-document', [$usulan->id, $fieldKey]);
                                                    $value = '<a href="' . e($route) . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Lihat</a>';
                                                } else {
                                                    $value = 'Dokumen tidak tersedia';
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
                                                    $route = route($config['routePrefix'] . '.show-document', [$usulan->id, $fieldKey]);
                                                    $value = '<a href="' . e($route) . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Lihat</a>';
                                                } else {
                                                    $value = 'BKD tidak tersedia';
                                                }
                                            } elseif ($groupKey === 'karya_ilmiah') {
                                                // Handle link fields with proper data structure
                                                if (str_contains($fieldKey, 'link_')) {
                                                    // Map field names for links
                                                    $fieldMapping = [
                                                        'link_artikel' => 'artikel',
                                                        'link_sinta' => 'sinta',
                                                        'link_scopus' => 'scopus',
                                                        'link_scimago' => 'scimago',
                                                        'link_wos' => 'wos'
                                                    ];
                                                    $mappedField = $fieldMapping[$fieldKey] ?? $fieldKey;

                                                    // Check new structure first (links object)
                                                    if (isset($usulan->data_usulan['karya_ilmiah']['links'][$mappedField])) {
                                                        $karyaValue = $usulan->data_usulan['karya_ilmiah']['links'][$mappedField];
                                                    }
                                                    // Check old structure
                                                    elseif (isset($usulan->data_usulan['karya_ilmiah'][$fieldKey])) {
                                                        $karyaValue = $usulan->data_usulan['karya_ilmiah'][$fieldKey];
                                                    }
                                                    // Check direct structure
                                                    elseif (isset($usulan->data_usulan[$fieldKey])) {
                                                        $karyaValue = $usulan->data_usulan[$fieldKey];
                                                    }
                                                    else {
                                                        $karyaValue = '-';
                                                    }

                                                    if ($karyaValue && $karyaValue !== '-') {
                                                        $value = '<a href="' . e($karyaValue) . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Buka Link</a>';
                                                    } else {
                                                        $value = 'Link tidak tersedia';
                                                    }
                                                } else {
                                                    // Handle non-link fields
                                                    $karyaValue = $usulan->data_usulan['karya_ilmiah'][$fieldKey] ?? '-';
                                                    $value = $karyaValue;
                                                }
                                            } elseif ($groupKey === 'bkd') {
                                                // Check multiple possible locations for BKD document path
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
                                                    $route = route(($config['documentRoutePrefix'] ?? $config['routePrefix']) . '.show-document', [$usulan->id, $fieldKey]);
                                                    $value = '<a href="' . e($route) . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Lihat</a>';
                                                } else {
                                                    $value = 'BKD tidak tersedia';
                                                }
                                            } elseif ($groupKey === 'syarat_guru_besar') {
                                                if ($fieldKey === 'syarat_guru_besar') {
                                                    // Map syarat guru besar values to readable labels
                                                    $syaratMapping = [
                                                        'hibah' => 'Pernah mendapatkan hibah penelitian',
                                                        'bimbingan' => 'Pernah membimbing mahasiswa S3',
                                                        'pengujian' => 'Pernah menjadi penguji disertasi',
                                                        'reviewer' => 'Pernah menjadi reviewer jurnal internasional'
                                                    ];
                                                    $syaratValue = $usulan->data_usulan['syarat_khusus']['syarat_guru_besar'] ?? '-';
                                                    $value = $syaratMapping[$syaratValue] ?? $syaratValue;
                                                } elseif ($fieldKey === 'bukti_syarat_guru_besar') {
                                                    // Check multiple possible locations for document path
                                                    $docPath = null;

                                                    // Check new structure first
                                                    if (isset($usulan->data_usulan['dokumen_usulan']['bukti_syarat_guru_besar']['path'])) {
                                                        $docPath = $usulan->data_usulan['dokumen_usulan']['bukti_syarat_guru_besar']['path'];
                                                    }
                                                    // Check old structure
                                                    elseif (isset($usulan->data_usulan['bukti_syarat_guru_besar'])) {
                                                        $docPath = $usulan->data_usulan['bukti_syarat_guru_besar'];
                                                    }
                                                    // Check using getDocumentPath method
                                                    else {
                                                        $docPath = $usulan->getDocumentPath($fieldKey);
                                                    }

                                                    if ($docPath) {
                                                        $route = route(($config['documentRoutePrefix'] ?? $config['routePrefix']) . '.show-document', [$usulan->id, $fieldKey]);
                                                        $value = '<a href="' . e($route) . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Lihat</a>';
                                                    } else {
                                                        $value = 'Dokumen tidak tersedia';
                                                    }
                                                }
                                            } elseif ($groupKey === 'dokumen_admin_fakultas') {
                                                // Handle dokumen admin fakultas fields untuk tampilan read-only
                                                // Data disimpan di validasi_data['admin_fakultas']['dokumen_pendukung']
                                                $dokumenPendukung = $usulan->validasi_data['admin_fakultas']['dokumen_pendukung'] ?? [];

                                                if ($fieldKey === 'nomor_surat_usulan') {
                                                    $value = $dokumenPendukung['nomor_surat_usulan'] ?? '-';
                                                } elseif ($fieldKey === 'file_surat_usulan') {
                                                    $docPath = $dokumenPendukung['file_surat_usulan_path'] ?? null;
                                                    if ($docPath) {
                                                        // Use proper route for Penilai Universitas
                                                        if ($currentRole === 'Penilai Universitas') {
                                                            $route = route('penilai-universitas.pusat-usulan.show-admin-fakultas-document', [$usulan->id, $fieldKey]);
                                                        } else {
                                                            $url = asset('storage/' . $docPath);
                                                        }
                                                        $value = '<a href="' . e($route ?? $url) . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Lihat</a>';
                                                    } else {
                                                        $value = 'Dokumen tidak tersedia';
                                                    }
                                                } elseif ($fieldKey === 'nomor_berita_senat') {
                                                    $value = $dokumenPendukung['nomor_berita_senat'] ?? '-';
                                                } elseif ($fieldKey === 'file_berita_senat') {
                                                    $docPath = $dokumenPendukung['file_berita_senat_path'] ?? null;
                                                    if ($docPath) {
                                                        // Use proper route for Penilai Universitas
                                                        if ($currentRole === 'Penilai Universitas') {
                                                            $route = route('penilai-universitas.pusat-usulan.show-admin-fakultas-document', [$usulan->id, $fieldKey]);
                                                        } else {
                                                            $url = asset('storage/' . $docPath);
                                                        }
                                                        $value = '<a href="' . e($route ?? $url) . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Lihat</a>';
                                                    } else {
                                                        $value = 'Dokumen tidak tersedia';
                                                    }
                                                }
                                            }
                                        @endphp
                                        {!! $value !!}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($canEdit)
                                        <select name="validation[{{ $groupKey }}][{{ $fieldKey }}][status]"
                                                class="validation-status block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 text-sm"
                                                data-group="{{ $groupKey }}" data-field="{{ $fieldKey }}">
                                            <option value="sesuai" {{ $fieldValidation['status'] === 'sesuai' ? 'selected' : '' }}>Sesuai</option>
                                            <option value="tidak_sesuai" {{ $fieldValidation['status'] === 'tidak_sesuai' ? 'selected' : '' }}>Tidak Sesuai</option>
                                        </select>
                                    @else
                                        <div class="space-y-1">
                                            {{-- Status validasi kepegawaian universitas --}}
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $fieldValidation['status'] === 'sesuai' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst(str_replace('_', ' ', $fieldValidation['status'])) }}
                                        </span>
                                            
                                            {{-- Status validasi penilai (jika ada) --}}
                                            @if($penilaiInvalidStatus)
                                                <div class="mt-1">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                        <i data-lucide="alert-triangle" class="w-3 h-3 mr-1"></i>
                                                        Penilai: Tidak Sesuai
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($canEdit)
                                        <textarea name="validation[{{ $groupKey }}][{{ $fieldKey }}][keterangan]"
                                                  class="validation-keterangan block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 text-sm {{ $fieldValidation['status'] === 'tidak_sesuai' ? '' : 'bg-gray-100' }}"
                                                  rows="2"
                                                  placeholder="Keterangan Wajib Diisi Jika Tidak Sesuai"
                                                  {{ $fieldValidation['status'] === 'tidak_sesuai' ? '' : 'disabled' }}>{{ $fieldValidation['keterangan'] ?? '' }}</textarea>
                                    @else
                                        <div class="space-y-2">
                                            {{-- Keterangan validasi kepegawaian universitas --}}
                                        <div class="text-sm text-gray-900">
                                            {{ $fieldValidation['keterangan'] ?? '-' }}
                                            </div>
                                            
                                            {{-- Keterangan validasi penilai (jika ada) --}}
                                            @if($penilaiInvalidKeterangan)
                                                <div class="bg-orange-50 border border-orange-200 rounded-lg p-2">
                                                    <div class="flex items-start">
                                                        <i data-lucide="alert-triangle" class="w-4 h-4 text-orange-600 mr-2 mt-0.5 flex-shrink-0"></i>
                                                        <div class="text-sm">
                                                            <div class="font-medium text-orange-800 mb-1">Keterangan Penilai:</div>
                                                            <div class="text-orange-700">{{ $penilaiInvalidKeterangan }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        @endif
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>