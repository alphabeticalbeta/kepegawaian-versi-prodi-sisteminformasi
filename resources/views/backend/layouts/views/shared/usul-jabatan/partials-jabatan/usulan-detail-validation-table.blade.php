{{-- Validation Table --}}
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5">
        <h2 class="text-xl font-bold text-white flex items-center">
            <i data-lucide="check-square" class="w-6 h-6 mr-3"></i>
            Tabel Validasi
        </h2>
    </div>
    
    @if($currentRole === 'Kepegawaian Universitas' && !empty($penilaiValidation))
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
    
    {{-- TAHAP 2: Info filter field berdasarkan role dan status perbaikan --}}
                    @if($currentRole === 'Admin Fakultas' && ($usulan->status_usulan === 'Permintaan Perbaikan dari Admin Fakultas' || $usulan->status_usulan === 'Permintaan Perbaikan Ke Admin Fakultas Dari Kepegawaian Universitas' || $usulan->status_usulan === 'Usulan Perbaikan dari Kepegawaian Universitas'))
        <div class="bg-blue-50 border-b border-blue-200 px-6 py-3">
            <div class="flex items-center">
                <i data-lucide="shield" class="w-4 h-4 text-blue-600 mr-2"></i>
                <span class="text-sm text-blue-800">
                    <strong>Keamanan Data:</strong> Hanya menampilkan field yang menjadi tanggung jawab Admin Fakultas. Field data Pegawai disembunyikan untuk keamanan.
                </span>
            </div>
        </div>
    @endif
    
    @if($currentRole === 'Pegawai' && ($usulan->status_usulan === 'Permintaan Perbaikan dari Kepegawaian Universitas' || $usulan->status_usulan === 'Usulan Perbaikan dari Kepegawaian Universitas'))
        <div class="bg-green-50 border-b border-green-200 px-6 py-3">
            <div class="flex items-center">
                <i data-lucide="shield" class="w-4 h-4 text-green-600 mr-2"></i>
                <span class="text-sm text-green-800">
                    <strong>Keamanan Data:</strong> Hanya menampilkan field yang menjadi tanggung jawab Pegawai. Field data Admin Fakultas disembunyikan untuk keamanan.
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
                        @if($currentRole === 'Kepegawaian Universitas' && !empty($penilaiValidation))
                            <div class="text-xs text-orange-600 mt-1">
                                <i data-lucide="alert-triangle" class="w-3 h-3 inline mr-1"></i>
                                + Penilai
                            </div>
                        @endif
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Keterangan
                        @if($currentRole === 'Kepegawaian Universitas' && !empty($penilaiValidation))
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
                        @if($groupKey === 'dokumen_admin_fakultas' && $currentRole === 'Admin Fakultas' && !in_array($usulan->status_usulan, ['Usulan Dikirim ke Admin Fakultas', 'Usulan Disetujui Admin Fakultas', 'Usulan Perbaikan dari Admin Fakultas', 'Usulan Perbaikan dari Kepegawaian Universitas', 'Usulan Perbaikan dari Penilai Universitas', 'Permintaan Perbaikan dari Kepegawaian Universitas', 'Permintaan Perbaikan Ke Admin Fakultas Dari Kepegawaian Universitas', 'Usulan Perbaikan Dari Admin Fakultas Ke Kepegawaian Universitas']))
                            @continue
                        @endif
                        
                        {{-- Skip dokumen_admin_fakultas untuk Kepegawaian Universitas jika status tidak sesuai --}}
                        @if($groupKey === 'dokumen_admin_fakultas' && $currentRole === 'Kepegawaian Universitas' && !in_array($usulan->status_usulan, ['Usulan Disetujui Admin Fakultas', 'Usulan Disetujui Kepegawaian Universitas', 'Usulan Direkomendasi dari Penilai Universitas', 'Usulan Direkomendasi Penilai Universitas', 'Usulan Perbaikan Dari Admin Fakultas Ke Kepegawaian Universitas', 'Permintaan Perbaikan Ke Admin Fakultas Dari Kepegawaian Universitas']))
                            @continue
                        @endif
                        
                        {{-- TAHAP 2: Filter field berdasarkan role dan status perbaikan --}}
                        @php
                            // Field yang menjadi tanggung jawab Admin Fakultas
                            $adminFakultasFields = ['dokumen_admin_fakultas'];
                            
                            // Field yang menjadi tanggung jawab Pegawai
                            $pegawaiFields = ['data_pribadi', 'data_kepegawaian', 'data_pendidikan', 'data_kinerja', 'dokumen_profil', 'bkd', 'karya_ilmiah', 'dokumen_usulan', 'syarat_guru_besar'];
                            
                            // Logic untuk menyembunyikan field berdasarkan role dan status perbaikan
                            $shouldSkipField = false;
                            
                            // Admin Fakultas: Hanya tampilkan field dokumen_admin_fakultas saat perbaikan dari Kepegawaian Universitas
                            if ($currentRole === 'Admin Fakultas' && in_array($usulan->status_usulan, ['Permintaan Perbaikan dari Admin Fakultas', 'Usulan Perbaikan dari Kepegawaian Universitas', 'Permintaan Perbaikan Ke Admin Fakultas Dari Kepegawaian Universitas'])) {
                                if (!in_array($groupKey, $adminFakultasFields)) {
                                    $shouldSkipField = true;
                                }
                            }
                            
                            // Pegawai: Hanya tampilkan field Pegawai saat perbaikan dari Kepegawaian Universitas
                            if ($currentRole === 'Pegawai' && in_array($usulan->status_usulan, ['Permintaan Perbaikan dari Kepegawaian Universitas', 'Usulan Perbaikan dari Kepegawaian Universitas'])) {
                                if (!in_array($groupKey, $pegawaiFields)) {
                                    $shouldSkipField = true;
                                }
                            }
                            
                            // Kepegawaian Universitas: Tampilkan semua field untuk status Usulan Perbaikan Dari Admin Fakultas Ke Kepegawaian Universitas
                            if ($currentRole === 'Kepegawaian Universitas' && $usulan->status_usulan === 'Usulan Perbaikan Dari Admin Fakultas Ke Kepegawaian Universitas') {
                                $shouldSkipField = false;
                            }
                            
                            // Kepegawaian Universitas: Tampilkan semua field untuk status selain Usulan Perbaikan Dari Admin Fakultas Ke Kepegawaian Universitas
                            if ($currentRole === 'Kepegawaian Universitas' && $usulan->status_usulan !== 'Usulan Perbaikan Dari Admin Fakultas Ke Kepegawaian Universitas') {
                                $shouldSkipField = false;
                            }
                            
                            // Default: Tampilkan semua field untuk Kepegawaian Universitas
                            if ($currentRole === 'Kepegawaian Universitas') {
                                $shouldSkipField = false;
                            }
                        @endphp
                        
                        @if($shouldSkipField)
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

                        @if($groupKey === 'dokumen_admin_fakultas' && isset($group['isEditableForm']) && $group['isEditableForm'] && $currentRole === 'Admin Fakultas')
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
                                <td class="px-6 py-6" colspan="3">
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
                                                   class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-sm {{ $currentRole === 'Admin Fakultas' && in_array($usulan->status_usulan, ['Usulan Disetujui Admin Fakultas', 'Usulan Perbaikan Dari Admin Fakultas Ke Kepegawaian Universitas']) ? 'bg-gray-100' : '' }}"
                                                   placeholder="Contoh: 001/FK-UNMUL/2025 (Opsional)"
                                                   {{ $currentRole === 'Admin Fakultas' && in_array($usulan->status_usulan, ['Usulan Disetujui Admin Fakultas', 'Usulan Perbaikan Dari Admin Fakultas Ke Kepegawaian Universitas']) ? 'readonly' : '' }}>
                                            <small class="text-gray-500 mt-1">Format: Nomor/Fakultas/UNMUL/Tahun (Opsional)</small>
                                        </div>
                                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                                File Surat Usulan
                                            </label>
                                            @php
                                                $isViewOnlyStatusForFakultas = $currentRole === 'Admin Fakultas' && $usulan->status_usulan === 'Usulan Perbaikan Dari Admin Fakultas Ke Kepegawaian Universitas';
                                                $currentPath = $dokumenPendukung['file_surat_usulan_path']
                                                    ?? $dokumenPendukung['file_surat_usulan']
                                                    ?? $usulan->getDocumentPath('file_surat_usulan');
                                            @endphp

                                            @if($isViewOnlyStatusForFakultas)
                                                {{-- Mode View-Only untuk status spesifik --}}
                                                <div class="text-sm text-gray-800 bg-gray-100 p-3 rounded-md border border-gray-200">
                                                    @if($currentPath)
                                                        <i data-lucide="check-circle" class="w-4 h-4 inline mr-1 text-green-600"></i>
                                                        File telah diunggah: 
                                                        <a href="{{ asset('storage/' . $currentPath) }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline font-medium">Lihat File</a>
                                                    @else
                                                        <i data-lucide="x-circle" class="w-4 h-4 inline mr-1 text-red-600"></i>
                                                        <span>File tidak diunggah pada saat pengiriman.</span>
                                                    @endif
                                                </div>
                                            @else
                                                {{-- Mode Editable untuk status lainnya --}}
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
                                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-sm {{ $usulan->status_usulan === 'Usulan Disetujui Admin Fakultas' ? 'bg-gray-100' : '' }}"
                                                       {{ $usulan->status_usulan === 'Usulan Disetujui Admin Fakultas' ? 'disabled' : '' }}>
                                                    <small class="text-gray-500">Upload file baru untuk mengganti file yang ada (Opsional)</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            {{-- Baris kedua: Nomor Berita Senat dan File Berita Senat --}}
                            <tr class="hover:bg-blue-50 transition-colors">
                                <td class="px-6 py-6" colspan="3">
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
                                               class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-sm {{ $currentRole === 'Admin Fakultas' && in_array($usulan->status_usulan, ['Usulan Disetujui Admin Fakultas', 'Usulan Perbaikan Dari Admin Fakultas Ke Kepegawaian Universitas']) ? 'bg-gray-100' : '' }}"
                                               placeholder="Contoh: 001/Berita-Senat/2025 (Opsional)"
                                               {{ $currentRole === 'Admin Fakultas' && in_array($usulan->status_usulan, ['Usulan Disetujui Admin Fakultas', 'Usulan Perbaikan Dari Admin Fakultas Ke Kepegawaian Universitas']) ? 'readonly' : '' }}>
                                            <small class="text-gray-500 mt-1">Format: Nomor/Berita-Senat/Tahun (Opsional)</small>
                                        </div>
                                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                                File Berita Senat
                                            </label>
                                            @php
                                                $isViewOnlyStatusForFakultas = $currentRole === 'Admin Fakultas' && $usulan->status_usulan === 'Usulan Perbaikan Dari Admin Fakultas Ke Kepegawaian Universitas';
                                                $currentPath = $dokumenPendukung['file_berita_senat_path']
                                                    ?? $dokumenPendukung['file_berita_senat']
                                                    ?? $usulan->getDocumentPath('file_berita_senat');
                                            @endphp

                                            @if($isViewOnlyStatusForFakultas)
                                                {{-- Mode View-Only untuk status spesifik --}}
                                                <div class="text-sm text-gray-800 bg-gray-100 p-3 rounded-md border border-gray-200">
                                                    @if($currentPath)
                                                        <i data-lucide="check-circle" class="w-4 h-4 inline mr-1 text-green-600"></i>
                                                        File telah diunggah: 
                                                        <a href="{{ asset('storage/' . $currentPath) }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline font-medium">Lihat File</a>
                                                    @else
                                                        <i data-lucide="x-circle" class="w-4 h-4 inline mr-1 text-red-600"></i>
                                                        <span>File tidak diunggah pada saat pengiriman.</span>
                                                    @endif
                                                </div>
                                            @else
                                                {{-- Mode Editable untuk status lainnya --}}
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
                                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 text-sm {{ $usulan->status_usulan === 'Usulan Disetujui Admin Fakultas' ? 'bg-gray-100' : '' }}"
                                                       {{ $usulan->status_usulan === 'Usulan Disetujui Admin Fakultas' ? 'disabled' : '' }}>
                                                    <small class="text-gray-500">Upload file baru untuk mengganti file yang ada (Opsional)</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @elseif($groupKey === 'dokumen_admin_fakultas' && $currentRole === 'Kepegawaian Universitas' && in_array($usulan->status_usulan, ['Usulan Disetujui Admin Fakultas', 'Usulan Disetujui Kepegawaian Universitas', 'Usulan Direkomendasi dari Penilai Universitas', 'Usulan Direkomendasi Penilai Universitas', 'Usulan Perbaikan Dari Admin Fakultas Ke Kepegawaian Universitas', 'Permintaan Perbaikan Ke Admin Fakultas Dari Kepegawaian Universitas']))
                            {{-- Tampilan khusus untuk Kepegawaian Universitas melihat dokumen admin fakultas --}}
                            <tr class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500">
                                <td colspan="3" class="px-6 py-4">
                                    <div class="flex items-center">
                                        <i data-lucide="{{ $group['icon'] }}" class="w-5 h-5 mr-3 text-blue-600"></i>
                                        <span class="font-bold text-blue-800 text-lg">{{ $group['label'] }}</span>
                                    </div>
                                    <p class="text-sm text-blue-600 mt-1 ml-8">
                                        ⓘ Dokumen yang dikirim oleh Admin Fakultas ke Universitas
                                    </p>
                                </td>
                            </tr>
                            @foreach(is_callable($group['fields']) ? $group['fields']() : $group['fields'] as $fieldKey => $fieldLabel)
                            @php
                                $fieldValidation = $existingValidation['validation'][$groupKey][$fieldKey] ?? ['status' => 'sesuai', 'keterangan' => ''];
                                $isInvalid = $fieldValidation['status'] === 'tidak_sesuai';
                                
                                // Handle dokumen_admin_fakultas fields untuk Kepegawaian Universitas
                                $dokumenPendukung = $usulan->validasi_data['admin_fakultas']['dokumen_pendukung'] ?? [];
                                $value = '';
                                
                                if ($fieldKey === 'nomor_surat_usulan' || $fieldKey === 'nomor_berita_senat') {
                                    $value = $dokumenPendukung[$fieldKey] ?? '-';
                                } elseif ($fieldKey === 'file_surat_usulan' || $fieldKey === 'file_berita_senat') {
                                    $pathKey = $fieldKey . '_path';
                                    $path = $dokumenPendukung[$pathKey]
                                        ?? $dokumenPendukung[$fieldKey]
                                        ?? $usulan->getDocumentPath($fieldKey);

                                    if ($path) {
                                        $value = '<a href="' . asset('storage/' . $path) . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Lihat File</a>';
                                    } else {
                                        $value = 'File tidak tersedia';
                                    }
                                }
                            @endphp
                            <tr class="hover:bg-gray-50 {{ $isInvalid ? 'bg-red-50' : '' }}">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $fieldLabel }}</div>
                                    <div class="text-sm text-gray-500">
                                        {!! $value !!}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($canEdit && !($currentRole === 'Admin Fakultas' && in_array($usulan->status_usulan, ['Usulan Disetujui Admin Fakultas', 'Usulan Perbaikan Dari Admin Fakultas Ke Kepegawaian Universitas'])))
                                        <select name="validation[{{ $groupKey }}][{{ $fieldKey }}][status]" 
                                                class="validation-status block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                                data-group="{{ $groupKey }}" 
                                                data-field="{{ $fieldKey }}">
                                            <option value="sesuai" {{ $fieldValidation['status'] === 'sesuai' ? 'selected' : '' }}>Sesuai</option>
                                            <option value="tidak_sesuai" {{ $fieldValidation['status'] === 'tidak_sesuai' ? 'selected' : '' }}>Tidak Sesuai</option>
                                        </select>
                                    @else
                                        <div class="flex items-center">
                                            @if($fieldValidation['status'] === 'sesuai')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                                    Sesuai
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                                    Tidak Sesuai
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($canEdit && !($currentRole === 'Admin Fakultas' && in_array($usulan->status_usulan, ['Usulan Disetujui Admin Fakultas', 'Usulan Perbaikan Dari Admin Fakultas Ke Kepegawaian Universitas'])))
                                        <textarea name="validation[{{ $groupKey }}][{{ $fieldKey }}][keterangan]" 
                                                  class="validation-keterangan block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm {{ $fieldValidation['status'] !== 'tidak_sesuai' ? 'bg-gray-100' : '' }}"
                                                  rows="2"
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
                                if ($currentRole === 'Kepegawaian Universitas' && !empty($allPenilaiInvalidFields)) {
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
                                            } elseif ($groupKey === 'dokumen_admin_fakultas') {
                                                // Handle dokumen_admin_fakultas fields untuk Kepegawaian Universitas
                                                $dokumenPendukung = $usulan->validasi_data['admin_fakultas']['dokumen_pendukung'] ?? [];
                                                
                                                if ($fieldKey === 'nomor_surat_usulan' || $fieldKey === 'nomor_berita_senat') {
                                                    $value = $dokumenPendukung[$fieldKey] ?? '-';
                                                } elseif ($fieldKey === 'file_surat_usulan' || $fieldKey === 'file_berita_senat') {
                                                    $pathKey = $fieldKey . '_path';
                                                    $path = $dokumenPendukung[$pathKey]
                                                        ?? $dokumenPendukung[$fieldKey]
                                                        ?? $usulan->getDocumentPath($fieldKey);

                                                    if ($path) {
                                                        $value = '<a href="' . asset('storage/' . $path) . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Lihat File</a>';
                                                    } else {
                                                        $value = 'File tidak tersedia';
                                                    }
                                                }
                                            } elseif ($groupKey === 'dokumen_profil') {
                                                if ($usulan->pegawai->$fieldKey) {
                                                    // Gunakan route yang benar berdasarkan role
                                                    if ($currentRole === 'Kepegawaian Universitas') {
                                                        $route = route('backend.kepegawaian-universitas.data-pegawai.show-document', [$usulan->pegawai->id, $fieldKey]);
                                                    } elseif ($currentRole === 'Admin Fakultas') {
                                                        $route = route('admin-fakultas.usulan.show-pegawai-document', [$usulan->id, $fieldKey]);
                                                    } else {
                                                        $route = route('pegawai-unmul.profile.show-document', [$fieldKey]);
                                                    }
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
                                                    // Gunakan route yang benar berdasarkan role
                                                    if ($currentRole === 'Kepegawaian Universitas') {
                                                        $route = route('backend.kepegawaian-universitas.pusat-usulan.show-document', [$usulan->id, $fieldKey]);
                                                    } elseif ($currentRole === 'Admin Fakultas') {
                                                        $route = route('admin-fakultas.usulan.show-document', [$usulan->id, $fieldKey]);
                                                    } elseif ($currentRole === 'Penilai Universitas') {
                                                        $route = route('penilai-universitas.pusat-usulan.show-document', [$usulan->id, $fieldKey]);
                                                    } else {
                                                        $route = route('pegawai-unmul.usulan-jabatan.show-document', [$usulan->id, $fieldKey]);
                                                    }
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
                                                    // Gunakan route yang benar berdasarkan role
                                                    if ($currentRole === 'Kepegawaian Universitas') {
                                                        $route = route('backend.kepegawaian-universitas.pusat-usulan.show-document', [$usulan->id, $fieldKey]);
                                                    } elseif ($currentRole === 'Admin Fakultas') {
                                                        $route = route('admin-fakultas.usulan.show-document', [$usulan->id, $fieldKey]);
                                                    } elseif ($currentRole === 'Penilai Universitas') {
                                                        $route = route('penilai-universitas.pusat-usulan.show-document', [$usulan->id, $fieldKey]);
                                                    } else {
                                                        $route = route('pegawai-unmul.usulan-jabatan.show-document', [$usulan->id, $fieldKey]);
                                                    }
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
                                                        $docPath = $usulan->getDocumentPath('bukti_syarat_guru_besar');
                                                    }

                                                    if ($docPath) {
                                                        // Gunakan route yang benar berdasarkan role
                                                        if ($currentRole === 'Kepegawaian Universitas') {
                                                            $route = route('backend.kepegawaian-universitas.pusat-usulan.show-document', [$usulan->id, 'bukti_syarat_guru_besar']);
                                                        } elseif ($currentRole === 'Admin Fakultas') {
                                                            $route = route('admin-fakultas.usulan.show-document', [$usulan->id, 'bukti_syarat_guru_besar']);
                                                        } elseif ($currentRole === 'Penilai Universitas') {
                                                            $route = route('penilai-universitas.pusat-usulan.show-document', [$usulan->id, 'bukti_syarat_guru_besar']);
                                                        } else {
                                                            $route = route('pegawai-unmul.usulan-jabatan.show-document', [$usulan->id, 'bukti_syarat_guru_besar']);
                                                        }
                                                        $value = '<a href="' . e($route) . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Lihat</a>';
                                                    } else {
                                                        $value = 'Bukti tidak tersedia';
                                                    }
                                                }
                                            } elseif ($groupKey === 'dokumen_admin_fakultas') {
                                                // Handle dokumen_admin_fakultas fields
                                                $dokumenPendukung = $usulan->validasi_data['admin_fakultas']['dokumen_pendukung'] ?? [];
                                                
                                                if ($fieldKey === 'nomor_surat_usulan' || $fieldKey === 'nomor_berita_senat') {
                                                    $value = $dokumenPendukung[$fieldKey] ?? '-';
                                                } elseif ($fieldKey === 'file_surat_usulan' || $fieldKey === 'file_berita_senat') {
                                                    $pathKey = $fieldKey . '_path';
                                                    $path = $dokumenPendukung[$pathKey]
                                                        ?? $dokumenPendukung[$fieldKey]
                                                        ?? $usulan->getDocumentPath($fieldKey);

                                                    if ($path) {
                                                        $value = '<a href="' . asset('storage/' . $path) . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Lihat File</a>';
                                                    } else {
                                                        $value = 'File tidak tersedia';
                                                    }
                                                }
                                            }
                                        @endphp
                                        {!! $value !!}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($canEdit && !($currentRole === 'Admin Fakultas' && in_array($usulan->status_usulan, ['Usulan Disetujui Admin Fakultas', 'Usulan Perbaikan Dari Admin Fakultas Ke Kepegawaian Universitas'])))
                                        <select name="validation[{{ $groupKey }}][{{ $fieldKey }}][status]" 
                                                class="validation-status block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                                data-group="{{ $groupKey }}" 
                                                data-field="{{ $fieldKey }}">
                                            <option value="sesuai" {{ $fieldValidation['status'] === 'sesuai' ? 'selected' : '' }}>Sesuai</option>
                                            <option value="tidak_sesuai" {{ $fieldValidation['status'] === 'tidak_sesuai' ? 'selected' : '' }}>Tidak Sesuai</option>
                                        </select>
                                        
                                        @if($currentRole === 'Kepegawaian Universitas' && $penilaiInvalidStatus)
                                            <div class="mt-2">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                    <i data-lucide="alert-triangle" class="w-3 h-3 mr-1"></i>
                                                    Penilai: {{ ucwords(str_replace('_', ' ', $penilaiInvalidStatus)) }}
                                                </span>
                                            </div>
                                        @endif
                                    @else
                                        <div class="flex items-center">
                                            @if($fieldValidation['status'] === 'sesuai')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                                    Sesuai
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                                    Tidak Sesuai
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($canEdit && !($currentRole === 'Admin Fakultas' && in_array($usulan->status_usulan, ['Usulan Disetujui Admin Fakultas', 'Usulan Perbaikan Dari Admin Fakultas Ke Kepegawaian Universitas'])))
                                        <textarea name="validation[{{ $groupKey }}][{{ $fieldKey }}][keterangan]" 
                                                  class="validation-keterangan block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm {{ $fieldValidation['status'] !== 'tidak_sesuai' ? 'bg-gray-100' : '' }}"
                                                  rows="2"
                                                  placeholder="Keterangan (wajib jika tidak sesuai)"
                                                  {{ $fieldValidation['status'] !== 'tidak_sesuai' ? 'disabled' : '' }}>{{ $fieldValidation['keterangan'] ?? '' }}</textarea>
                                        
                                        @if($currentRole === 'Kepegawaian Universitas' && $penilaiInvalidKeterangan)
                                            <div class="mt-2">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                    <i data-lucide="alert-triangle" class="w-3 h-3 mr-1"></i>
                                                    Penilai: {{ $penilaiInvalidKeterangan }}
                                                </span>
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-sm text-gray-600">
                                            {{ $fieldValidation['keterangan'] ?? '-' }}
                                            
                                            @if($currentRole === 'Kepegawaian Universitas' && $penilaiInvalidKeterangan)
                                                <div class="mt-1">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                        <i data-lucide="alert-triangle" class="w-3 h-3 mr-1"></i>
                                                        Penilai: {{ $penilaiInvalidKeterangan }}
                                                    </span>
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
