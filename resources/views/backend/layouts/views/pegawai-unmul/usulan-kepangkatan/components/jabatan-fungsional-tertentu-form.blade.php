{{-- Dokumen Pendukung Jabatan Fungsional Tertentu --}}
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-purple-600 to-violet-600 px-6 py-5">
        <h2 class="text-xl font-bold text-white flex items-center">
            <i data-lucide="file-text" class="w-6 h-6 mr-3"></i>
            Dokumen Pendukung Jabatan Fungsional Tertentu
        </h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">Dokumen Uji Kompetensi</label>
                <p class="text-xs text-gray-600 mb-3">Dokumen uji kompetensi atau sertifikasi (Opsional)</p>
                
                <div class="relative">
                    <div class="flex items-center gap-3">
                        <div class="flex-1">
                            <div class="relative">
                                <input type="file" name="dokumen_uji_kompetensi" accept=".pdf" 
                                       class="block w-full text-sm text-gray-900 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 file:cursor-pointer">
                            </div>
                        </div>
                        
                        @if(isset($usulan->data_usulan['dokumen_usulan']['dokumen_uji_kompetensi']['path']))
                            <a href="{{ route('pegawai-unmul.usulan-kepangkatan.show-document', ['usulanKepangkatan' => $usulan->id, 'field' => 'dokumen_uji_kompetensi']) }}" 
                               target="_blank" 
                               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
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
                        <i data-lucide="info" class="w-3 h-3 text-purple-500"></i>
                        <span>Format: PDF (Max: 1MB) - Opsional</span>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">SK Penyetaraan Ijazah</label>
                <p class="text-xs text-gray-600 mb-3">Surat Keputusan Penyetaraan Ijazah (Opsional)</p>
                
                <div class="relative">
                    <div class="flex items-center gap-3">
                        <div class="flex-1">
                            <div class="relative">
                                <input type="file" name="sk_penyetaraan_ijazah" accept=".pdf" 
                                       class="block w-full text-sm text-gray-900 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 file:cursor-pointer">
                            </div>
                        </div>
                        
                        @if(isset($usulan->data_usulan['dokumen_usulan']['sk_penyetaraan_ijazah']['path']))
                            <a href="{{ route('pegawai-unmul.usulan-kepangkatan.show-document', ['usulanKepangkatan' => $usulan->id, 'field' => 'sk_penyetaraan_ijazah']) }}" 
                               target="_blank" 
                               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
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
                        <i data-lucide="info" class="w-3 h-3 text-purple-500"></i>
                        <span>Format: PDF (Max: 1MB) - Opsional</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Box --}}
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <i data-lucide="info" class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0"></i>
                <div class="text-sm text-blue-800">
                    <h4 class="font-semibold mb-1">Informasi Dokumen Pendukung</h4>
                    <ul class="space-y-1">
                        <li>• <strong>Dokumen Uji Kompetensi:</strong> Dokumen yang membuktikan kelulusan uji kompetensi atau sertifikasi</li>
                        <li>• <strong>SK Penyetaraan Ijazah:</strong> Dokumen resmi yang menyatakan penyetaraan ijazah</li>
                        <li>• Kedua dokumen bersifat opsional namun dapat memperkuat pengajuan kenaikan pangkat</li>
                        <li>• Pastikan dokumen yang diupload jelas, lengkap, dan sesuai format yang diminta</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
