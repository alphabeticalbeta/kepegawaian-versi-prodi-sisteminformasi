{{-- Dokumen Pendukung Dosen PNS --}}
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-5">
        <h2 class="text-xl font-bold text-white flex items-center">
            <i data-lucide="graduation-cap" class="w-6 h-6 mr-3"></i>
            Dokumen Pendukung Dosen PNS
        </h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">Dokumen Uji Kompetensi (UKOM) dan SK Jabatan</label>
                <p class="text-xs text-gray-600 mb-3">Dokumen Uji Kompetensi dan SK Jabatan (1 File)</p>
                
                <div class="relative">
                    <div class="flex items-center gap-3">
                        <div class="flex-1">
                            <div class="relative">
                                <input type="file" name="dokumen_ukom_sk_jabatan" accept=".pdf" 
                                       class="block w-full text-sm text-gray-900 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 file:cursor-pointer">
                            </div>
                        </div>
                        
                        @if(isset($usulan->data_usulan['dokumen_usulan']['dokumen_ukom_sk_jabatan']['path']))
                            <a href="{{ route('pegawai-unmul.usulan-kepangkatan.show-document', ['usulanKepangkatan' => $usulan->id, 'field' => 'dokumen_ukom_sk_jabatan']) }}" 
                               target="_blank" 
                               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
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
                        <i data-lucide="info" class="w-3 h-3 text-blue-500"></i>
                        <span>Format: PDF (Max: 1MB)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
