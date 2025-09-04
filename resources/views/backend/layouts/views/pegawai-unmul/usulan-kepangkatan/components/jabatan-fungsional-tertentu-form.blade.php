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

{{-- Dokumen Pendukung Jabatan Fungsional Tertentu --}}
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-orange-600 to-red-600 px-6 py-5">
        <h2 class="text-xl font-bold text-white flex items-center">
            <i data-lucide="award" class="w-6 h-6 mr-3"></i>
            Dokumen Uji Kompetensi
            @if($isViewOnly)
                <span class="ml-3 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                    <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                    View Only
                </span>
            @endif
        </h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-800">Surat Uji Kompetensi</label>
                @if(isset($usulan->data_usulan['dokumen_usulan']['dokumen_uji_kompetensi']['path']))
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                            <i data-lucide="file-text" class="w-5 h-5 text-green-600"></i>
                            <span class="text-sm text-green-800">Surat Uji Kompetensi sudah diupload</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                            <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-800">Surat Uji Kompetensi</div>
                            </div>
                            <a href="{{ route('pegawai-unmul.usulan-kepangkatan.show-document', [$usulan, 'dokumen_uji_kompetensi']) }}" 
                               target="_blank" 
                               class="inline-flex items-center gap-2 px-3 py-2 bg-orange-600 text-white text-sm rounded-lg hover:bg-orange-700 transition-colors">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                Lihat
                            </a>
                            <a href="{{ route('pegawai-unmul.usulan-kepangkatan.show-document', [$usulan, 'dokumen_uji_kompetensi']) }}?download=1" 
                               class="inline-flex items-center gap-2 px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">
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
                        <span class="text-sm text-gray-600">Surat Uji Kompetensi belum diupload</span>
                    </div>
                @endif
                
                @if(!$isViewOnly)
                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-gray-800 mb-2">
                            @if(isset($usulan->data_usulan['dokumen_usulan']['dokumen_uji_kompetensi']['path']))
                                Ganti Dokumen
                            @else
                                Upload Dokumen
                            @endif
                        </label>
                        <input type="file" name="dokumen_uji_kompetensi" accept=".pdf" 
                               class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('dokumen_uji_kompetensi') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                        @error('dokumen_uji_kompetensi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
