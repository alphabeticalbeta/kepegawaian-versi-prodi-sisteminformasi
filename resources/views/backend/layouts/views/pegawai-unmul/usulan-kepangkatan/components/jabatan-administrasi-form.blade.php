@php
    // Check if usulan is in view-only status
    $viewOnlyStatuses = [
        \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS,
        \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS,
        \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_SUDAH_DIKIRIM_KE_BKN,
        \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_BKN,
        \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS,
        \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN
    ];
    
    // Status yang dapat diedit (tidak view-only) - hanya status draft dan permintaan perbaikan
    $editableStatuses = [
        \App\Models\KepegawaianUniversitas\Usulan::STATUS_DRAFT_USULAN,
        \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS,
        \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN
    ];
    
    if (in_array($usulan->status_usulan, $editableStatuses)) {
        $isViewOnly = false;  // Dapat diedit
    } else {
        $isViewOnly = true;  // View-only untuk semua status lainnya
    }


@endphp

{{-- Dokumen Pendukung Jabatan Administrasi --}}
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-emerald-600 to-green-600 px-6 py-5">
        <h2 class="text-xl font-bold text-white flex items-center">
            <i data-lucide="clipboard" class="w-6 h-6 mr-3"></i>
            Dokumen Pendukung Jabatan Administrasi
            @if($isViewOnly)
                <span class="ml-3 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                    <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                    View Only
                </span>
            @endif
        </h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-800">Dokumen Pendukung Jabatan Administrasi</label>
                <p class="text-xs text-gray-600 mb-2">Dokumen pendukung untuk jabatan administrasi (1 File)</p>
                @if($isViewOnly)
                    @if(isset($usulan->data_usulan['dokumen_usulan']['dokumen_pendukung_jabatan_administrasi']['path']))
                        <div class="space-y-3">
                            <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                <i data-lucide="file-text" class="w-5 h-5 text-green-600"></i>
                                <span class="text-sm text-green-800">Dokumen Pendukung sudah diupload</span>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                                <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-800">Dokumen Pendukung Jabatan Administrasi</div>
                                    <div class="text-xs text-gray-500">{{ basename($usulan->data_usulan['dokumen_usulan']['dokumen_pendukung_jabatan_administrasi']['path']) }}</div>
                                </div>
                                <a href="{{ asset('storage/' . $usulan->data_usulan['dokumen_usulan']['dokumen_pendukung_jabatan_administrasi']['path']) }}" 
                                   target="_blank" 
                                   class="inline-flex items-center gap-2 px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                    Lihat
                                </a>
                                <a href="{{ asset('storage/' . $usulan->data_usulan['dokumen_usulan']['dokumen_pendukung_jabatan_administrasi']['path']) }}" 
                                   download="{{ basename($usulan->data_usulan['dokumen_usulan']['dokumen_pendukung_jabatan_administrasi']['path']) }}"
                                   class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700 transition-colors">
                                    <i data-lucide="download" class="w-4 h-4"></i>
                                    Download
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                            <i data-lucide="file-x" class="w-5 h-5 text-red-600"></i>
                            <span class="text-sm text-red-800">Dokumen Pendukung belum diupload</span>
                        </div>
                    @endif
                @else
                    <input type="file" name="dokumen_pendukung_jabatan_administrasi" accept=".pdf" required
                           class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('dokumen_pendukung_jabatan_administrasi') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB) - Wajib</p>
                    @error('dokumen_pendukung_jabatan_administrasi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                @endif
            </div>
        </div>
    </div>
</div>
