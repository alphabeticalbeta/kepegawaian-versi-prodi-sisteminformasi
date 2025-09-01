{{-- Dokumen Pendukung Jabatan Struktural --}}
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-orange-600 to-red-600 px-6 py-5">
        <h2 class="text-xl font-bold text-white flex items-center">
            <i data-lucide="file-text" class="w-6 h-6 mr-3"></i>
            Dokumen Pendukung Jabatan Struktural
        </h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">Surat Pelantikan dan Berita Acara Jabatan Terakhir</label>
                <p class="text-xs text-gray-600 mb-3">Surat pelantikan dan berita acara jabatan terakhir (Wajib)</p>
                
                <div class="relative">
                    <div class="flex items-center gap-3">
                        <div class="flex-1">
                            <div class="relative">
                                <input type="file" name="surat_pelantikan_berita_acara" accept=".pdf" 
                                       class="block w-full text-sm text-gray-900 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 file:cursor-pointer"
                                       required>
                            </div>
                        </div>
                        
                        @if(isset($usulan->data_usulan['dokumen_usulan']['surat_pelantikan_berita_acara']['path']))
                            <a href="{{ route('pegawai-unmul.usulan-kepangkatan.show-document', ['usulanKepangkatan' => $usulan->id, 'field' => 'surat_pelantikan_berita_acara']) }}" 
                               target="_blank" 
                               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
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
                        <i data-lucide="info" class="w-3 h-3 text-orange-500"></i>
                        <span>Format: PDF (Max: 1MB) - Wajib</span>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">Surat Pencantuman Gelar</label>
                <p class="text-xs text-gray-600 mb-3">Surat pencantuman gelar akademik (Opsional)</p>
                
                <div class="relative">
                    <div class="flex items-center gap-3">
                        <div class="flex-1">
                            <div class="relative">
                                <input type="file" name="surat_pencantuman_gelar" accept=".pdf" 
                                       class="block w-full text-sm text-gray-900 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 file:cursor-pointer">
                            </div>
                        </div>
                        
                        @if(isset($usulan->data_usulan['dokumen_usulan']['surat_pencantuman_gelar']['path']))
                            <a href="{{ route('pegawai-unmul.usulan-kepangkatan.show-document', ['usulanKepangkatan' => $usulan->id, 'field' => 'surat_pencantuman_gelar']) }}" 
                               target="_blank" 
                               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
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
                        <i data-lucide="info" class="w-3 h-3 text-orange-500"></i>
                        <span>Format: PDF (Max: 1MB) - Opsional</span>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">Sertifikat Diklat / PIM / PKM</label>
                <p class="text-xs text-gray-600 mb-3">Sertifikat diklat kepemimpinan atau pengembangan kompetensi (Wajib)</p>
                
                <div class="relative">
                    <div class="flex items-center gap-3">
                        <div class="flex-1">
                            <div class="relative">
                                <input type="file" name="sertifikat_diklat_pim_pkm" accept=".pdf" 
                                       class="block w-full text-sm text-gray-900 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 file:cursor-pointer"
                                       required>
                            </div>
                        </div>
                        
                        @if(isset($usulan->data_usulan['dokumen_usulan']['sertifikat_diklat_pim_pkm']['path']))
                            <a href="{{ route('pegawai-unmul.usulan-kepangkatan.show-document', ['usulanKepangkatan' => $usulan->id, 'field' => 'sertifikat_diklat_pim_pkm']) }}" 
                               target="_blank" 
                               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
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
                        <i data-lucide="info" class="w-3 h-3 text-orange-500"></i>
                        <span>Format: PDF (Max: 1MB) - Wajib</span>
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
                        <li>• <strong>Surat Pelantikan dan Berita Acara Jabatan Terakhir:</strong> Dokumen resmi yang membuktikan pelantikan dan berita acara jabatan terakhir</li>
                        <li>• <strong>Surat Pencantuman Gelar:</strong> Dokumen resmi yang menyatakan gelar akademik yang dimiliki (Opsional)</li>
                        <li>• <strong>Sertifikat Diklat / PIM / PKM:</strong> Dokumen yang membuktikan kelulusan diklat kepemimpinan atau pengembangan kompetensi</li>
                        <li>• Dokumen wajib harus dilampirkan untuk kelengkapan pengajuan kenaikan pangkat</li>
                        <li>• Pastikan dokumen yang diupload jelas, lengkap, dan sesuai format yang diminta</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
