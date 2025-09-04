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

{{-- Dokumen Pendukung Jabatan Struktural --}}
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-5">
        <h2 class="text-xl font-bold text-white flex items-center">
            <i data-lucide="briefcase" class="w-6 h-6 mr-3"></i>
            Dokumen Pendukung Jabatan Struktural
            @if($isViewOnly)
                <span class="ml-3 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                    <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                    View Only
                </span>
            @endif
        </h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-800">Surat Pelantikan dan Berita Acara Jabatan Terakhir</label>
                <p class="text-xs text-gray-600 mb-2">Surat pelantikan dan berita acara jabatan terakhir (Wajib)</p>
                @if(isset($usulan->data_usulan['dokumen_usulan']['surat_pelantikan_berita_acara']['path']))
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                            <i data-lucide="file-text" class="w-5 h-5 text-green-600"></i>
                            <span class="text-sm text-green-800">Surat Pelantikan sudah diupload</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                            <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-800">Surat Pelantikan dan Berita Acara Jabatan Terakhir</div>
                                <div class="text-xs text-gray-500">{{ basename($usulan->data_usulan['dokumen_usulan']['surat_pelantikan_berita_acara']['path']) }}</div>
                            </div>
                            <a href="{{ route('pegawai-unmul.usulan-kepangkatan.show-document', [$usulan, 'surat_pelantikan_berita_acara']) }}" 
                               target="_blank" 
                               class="inline-flex items-center gap-2 px-3 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition-colors">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                Lihat
                            </a>
                            <a href="{{ route('pegawai-unmul.usulan-kepangkatan.show-document', [$usulan, 'surat_pelantikan_berita_acara']) }}?download=1" 
                               class="inline-flex items-center gap-2 px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors">
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
                        <span class="text-sm text-gray-600">Surat Pelantikan belum diupload</span>
                    </div>
                @endif
                
                @if(!$isViewOnly)
                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-gray-800 mb-2">
                            @if(isset($usulan->data_usulan['dokumen_usulan']['surat_pelantikan_berita_acara']['path']))
                                Ganti Dokumen
                            @else
                                Upload Dokumen
                            @endif
                        </label>
                        <input type="file" name="surat_pelantikan_berita_acara" accept=".pdf" 
                               class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('surat_pelantikan_berita_acara') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                        @error('surat_pelantikan_berita_acara')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-800">Surat Pencantuman Gelar</label>
                <p class="text-xs text-gray-600 mb-2">Surat pencantuman gelar (Opsional)</p>
                @if(isset($usulan->data_usulan['dokumen_usulan']['surat_pencantuman_gelar']['path']))
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                            <span class="text-sm text-blue-800">Surat Pencantuman Gelar sudah diupload</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                            <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-800">Surat Pencantuman Gelar</div>
                                <div class="text-xs text-gray-500">{{ basename($usulan->data_usulan['dokumen_usulan']['surat_pencantuman_gelar']['path']) }}</div>
                            </div>
                            <a href="{{ route('pegawai-unmul.usulan-kepangkatan.show-document', [$usulan, 'surat_pencantuman_gelar']) }}" 
                               target="_blank" 
                               class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                Lihat
                            </a>
                            <a href="{{ route('pegawai-unmul.usulan-kepangkatan.show-document', [$usulan, 'surat_pencantuman_gelar']) }}?download=1" 
                               class="inline-flex items-center gap-2 px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors">
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
                        <span class="text-sm text-gray-600">Surat Pencantuman Gelar belum diupload</span>
                    </div>
                @endif
                
                @if(!$isViewOnly)
                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-gray-800 mb-2">
                            @if(isset($usulan->data_usulan['dokumen_usulan']['surat_pencantuman_gelar']['path']))
                                Ganti Dokumen
                            @else
                                Upload Dokumen
                            @endif
                        </label>
                        <input type="file" name="surat_pencantuman_gelar" accept=".pdf"
                               class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('surat_pencantuman_gelar') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB) - Opsional</p>
                        @error('surat_pencantuman_gelar')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-800">Sertifikat Diklat / PIM / PKM</label>
                <p class="text-xs text-gray-600 mb-2">Sertifikat diklat kepemimpinan atau pengembangan kompetensi (Wajib)</p>
                @if(isset($usulan->data_usulan['dokumen_usulan']['sertifikat_diklat_pim_pkm']['path']))
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                            <i data-lucide="file-text" class="w-5 h-5 text-green-600"></i>
                            <span class="text-sm text-green-800">Sertifikat Diklat sudah diupload</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                            <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-800">Sertifikat Diklat / PIM / PKM</div>
                                <div class="text-xs text-gray-500">{{ basename($usulan->data_usulan['dokumen_usulan']['sertifikat_diklat_pim_pkm']['path']) }}</div>
                            </div>
                            <a href="{{ route('pegawai-unmul.usulan-kepangkatan.show-document', [$usulan, 'sertifikat_diklat_pim_pkm']) }}" 
                               target="_blank" 
                               class="inline-flex items-center gap-2 px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                Lihat
                            </a>
                            <a href="{{ route('pegawai-unmul.usulan-kepangkatan.show-document', [$usulan, 'sertifikat_diklat_pim_pkm']) }}?download=1" 
                               class="inline-flex items-center gap-2 px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors">
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
                        <span class="text-sm text-gray-600">Sertifikat Diklat belum diupload</span>
                    </div>
                @endif
                
                @if(!$isViewOnly)
                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-gray-800 mb-2">
                            @if(isset($usulan->data_usulan['dokumen_usulan']['sertifikat_diklat_pim_pkm']['path']))
                                Ganti Dokumen
                            @else
                                Upload Dokumen
                            @endif
                        </label>
                        <input type="file" name="sertifikat_diklat_pim_pkm" accept=".pdf" 
                               class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('sertifikat_diklat_pim_pkm') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                        @error('sertifikat_diklat_pim_pkm')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
