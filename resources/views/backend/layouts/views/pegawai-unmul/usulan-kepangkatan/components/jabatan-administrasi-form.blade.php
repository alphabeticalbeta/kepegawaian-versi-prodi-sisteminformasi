{{-- Dokumen Pendukung Jabatan Administrasi --}}
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5">
        <h2 class="text-xl font-bold text-white flex items-center">
            <i data-lucide="file-text" class="w-6 h-6 mr-3"></i>
            Dokumen Pendukung Jabatan Administrasi
        </h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">Dokumen Surat Pencantuman Gelar</label>
                <p class="text-xs text-gray-600 mb-3">Surat pencantuman gelar akademik (Opsional)</p>
                
                <div class="relative">
                    <div class="flex items-center gap-3">
                        <div class="flex-1">
                            <div class="relative">
                                <input type="file" name="surat_pencantuman_gelar" accept=".pdf" 
                                       class="block w-full text-sm text-gray-900 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 file:cursor-pointer">
                            </div>
                        </div>
                        
                        @if(isset($usulan->data_usulan['dokumen_usulan']['surat_pencantuman_gelar']['path']))
                            <a href="{{ route('pegawai-unmul.usulan-kepangkatan.show-document', ['usulanKepangkatan' => $usulan->id, 'field' => 'surat_pencantuman_gelar']) }}" 
                               target="_blank" 
                               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                Lihat
                            </a>
                        @else
                            <div class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium bg-gray-100 text-gray-500 rounded-lg">
                                <i data-lucide="file-x" class="w-4 h-4"></i>
                                Belum ada
                            </div>
                        @endif
                    </div>
                    
                    <div class="mt-2 flex items-center gap-2 text-xs text-gray-500">
                        <i data-lucide="info" class="w-3 h-3 text-green-500"></i>
                        <span>Format: PDF (Max: 1MB) - Opsional</span>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">Surat Tanda Lulus Ujian Dinas / Penyesuaian Ijazah</label>
                <p class="text-xs text-gray-600 mb-3">Surat tanda lulus ujian dinas atau penyesuaian ijazah (Opsional)</p>
                
                <div class="relative">
                    <div class="flex items-center gap-3">
                        <div class="flex-1">
                            <div class="relative">
                                <input type="file" name="surat_lulus_ujian_dinas" accept=".pdf" 
                                       class="block w-full text-sm text-gray-900 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 file:cursor-pointer">
                            </div>
                        </div>
                        
                        @if(isset($usulan->data_usulan['dokumen_usulan']['surat_lulus_ujian_dinas']['path']))
                            <a href="{{ route('pegawai-unmul.usulan-kepangkatan.show-document', ['usulanKepangkatan' => $usulan->id, 'field' => 'surat_lulus_ujian_dinas']) }}" 
                               target="_blank" 
                               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                Lihat
                            </a>
                        @else
                            <div class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium bg-gray-100 text-gray-500 rounded-lg">
                                <i data-lucide="file-x" class="w-4 h-4"></i>
                                Belum ada
                            </div>
                        @endif
                    </div>
                    
                    <div class="mt-2 flex items-center gap-2 text-xs text-gray-500">
                        <i data-lucide="info" class="w-3 h-3 text-green-500"></i>
                        <span>Format: PDF (Max: 1MB) - Opsional</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
