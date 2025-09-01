@php
    // Check if usulan is in view-only status
    $viewOnlyStatuses = [
        \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS,
        \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS,
        \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_SUDAH_DIKIRIM_KE_BKN,
        \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_BKN,
        \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN,
        \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN
    ];
    
    $isViewOnly = in_array($usulan->status_usulan, $viewOnlyStatuses);
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
        <div class="grid grid-cols-1 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-800">Surat Pelantikan dan Berita Acara Jabatan Terakhir</label>
                <p class="text-xs text-gray-600 mb-2">Surat pelantikan dan berita acara jabatan terakhir (Wajib)</p>
                @if($isViewOnly)
                    @if(isset($usulan->data_usulan['dokumen_usulan']['surat_pelantikan_berita_acara']['path']))
                        <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                            <i data-lucide="file-text" class="w-5 h-5 text-green-600"></i>
                            <span class="text-sm text-green-800">Surat Pelantikan sudah diupload</span>
                        </div>
                    @else
                        <div class="flex items-center gap-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                            <i data-lucide="file-x" class="w-5 h-5 text-red-600"></i>
                            <span class="text-sm text-red-800">Surat Pelantikan belum diupload</span>
                        </div>
                    @endif
                @else
                    <input type="file" name="surat_pelantikan_berita_acara" accept=".pdf" required
                           class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('surat_pelantikan_berita_acara') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB) - Wajib</p>
                    @error('surat_pelantikan_berita_acara')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                @endif
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-800">Surat Pencantuman Gelar</label>
                <p class="text-xs text-gray-600 mb-2">Surat pencantuman gelar (Opsional)</p>
                @if($isViewOnly)
                    @if(isset($usulan->data_usulan['dokumen_usulan']['surat_pencantuman_gelar']['path']))
                        <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                            <span class="text-sm text-blue-800">Surat Pencantuman Gelar sudah diupload</span>
                        </div>
                    @else
                        <div class="flex items-center gap-3 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                            <i data-lucide="file-x" class="w-5 h-5 text-gray-600"></i>
                            <span class="text-sm text-gray-600">Surat Pencantuman Gelar belum diupload</span>
                        </div>
                    @endif
                @else
                    <input type="file" name="surat_pencantuman_gelar" accept=".pdf"
                           class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('surat_pencantuman_gelar') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB) - Opsional</p>
                    @error('surat_pencantuman_gelar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                @endif
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-800">Sertifikat Diklat / PIM / PKM</label>
                <p class="text-xs text-gray-600 mb-2">Sertifikat diklat kepemimpinan atau pengembangan kompetensi (Wajib)</p>
                @if($isViewOnly)
                    @if(isset($usulan->data_usulan['dokumen_usulan']['sertifikat_diklat_pim_pkm']['path']))
                        <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                            <i data-lucide="file-text" class="w-5 h-5 text-green-600"></i>
                            <span class="text-sm text-green-800">Sertifikat Diklat sudah diupload</span>
                        </div>
                    @else
                        <div class="flex items-center gap-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                            <i data-lucide="file-x" class="w-5 h-5 text-red-600"></i>
                            <span class="text-sm text-red-800">Sertifikat Diklat belum diupload</span>
                        </div>
                    @endif
                @else
                    <input type="file" name="sertifikat_diklat_pim_pkm" accept=".pdf" required
                           class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('sertifikat_diklat_pim_pkm') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB) - Wajib</p>
                    @error('sertifikat_diklat_pim_pkm')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                @endif
            </div>
        </div>
    </div>
</div>
