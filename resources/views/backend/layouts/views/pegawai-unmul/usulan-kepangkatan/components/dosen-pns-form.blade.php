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

{{-- Dokumen Pendukung Dosen PNS --}}
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-5">
        <h2 class="text-xl font-bold text-white flex items-center">
            <i data-lucide="graduation-cap" class="w-6 h-6 mr-3"></i>
            Dokumen Pendukung Dosen PNS
            @if($isViewOnly)
                <span class="ml-3 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                    View Only
                </span>
            @endif
        </h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-800">Dokumen Uji Kompetensi (UKOM) dan SK Jabatan</label>
                <p class="text-xs text-gray-600 mb-2">Dokumen Uji Kompetensi dan SK Jabatan (1 File)</p>
                @if(isset($usulan->data_usulan['dokumen_usulan']['dokumen_ukom_sk_jabatan']['path']))
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                            <span class="text-sm text-blue-800">Dokumen UKOM dan SK Jabatan sudah diupload</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                            <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-800">Dokumen UKOM dan SK Jabatan</div>
                            </div>
                            <a href="{{ route('pegawai-unmul.usulan-kepangkatan.show-document', [$usulan, 'dokumen_ukom_sk_jabatan']) }}" 
                               target="_blank" 
                               class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                Lihat
                            </a>
                            <a href="{{ route('pegawai-unmul.usulan-kepangkatan.show-document', [$usulan, 'dokumen_ukom_sk_jabatan']) }}?download=1" 
                               class="inline-flex items-center gap-2 px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                <i data-lucide="download" class="w-4 h-4"></i>
                                Download
                            </a>
                        </div>
                        
                        @if(!$isViewOnly)
                            <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="info" class="w-4 h-4 text-yellow-600"></i>
                                    <span class="text-sm text-yellow-800">Anda dapat mengganti dokumen dengan mengupload file baru di bawah ini</span>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="flex items-center gap-3 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                        <i data-lucide="file-x" class="w-5 h-5 text-gray-600"></i>
                        <span class="text-sm text-gray-600">Dokumen belum diupload</span>
                    </div>
                @endif
                
                @if(!$isViewOnly)
                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-gray-800 mb-2">
                            @if(isset($usulan->data_usulan['dokumen_usulan']['dokumen_ukom_sk_jabatan']['path']))
                                Ganti Dokumen
                            @else
                                Upload Dokumen
                            @endif
                        </label>
                        <input type="file" name="dokumen_ukom_sk_jabatan" accept=".pdf" 
                               class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('dokumen_ukom_sk_jabatan') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                        @error('dokumen_ukom_sk_jabatan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
