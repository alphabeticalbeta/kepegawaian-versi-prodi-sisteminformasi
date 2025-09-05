{{-- Form Dosen untuk jenis NUPTK Dosen --}}
    <div class="space-y-6">
        {{-- Sub-section: Dokumen Pendukung --}}
        <div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                 {{-- Field Upload Surat Keterangan Sehat Rohani, Jasmani dan Bebas Narkotika --}}
                 @if($usulan->jenis_nuptk !== 'jabatan_fungsional_tertentu')
                 <div>
                     <label class="block text-sm font-semibold text-gray-800">Surat Keterangan Sehat Rohani, Jasmani dan Bebas Narkotika</label>
                     <p class="text-xs text-gray-600 mb-2">Scan/foto Surat Keterangan Sehat (maksimal 1 MB, format: PDF)</p>
                     
                     @if(isset($usulan->data_usulan['dokumen_usulan']['surat_keterangan_sehat']['path']))
                         <div class="space-y-3">
                             <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                 <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                                 <span class="text-sm text-blue-800">Surat Keterangan Sehat sudah diupload</span>
                                 </div>
                             <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                                 <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                                 <div class="flex-1">
                                     <div class="text-sm font-medium text-gray-800">Surat Keterangan Sehat</div>
                                 </div>
                                 <a href="{{ route('pegawai-unmul.usulan-nuptk.show-document', [$usulan, 'surat_keterangan_sehat']) }}" 
                                    target="_blank" 
                                    class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                     <i data-lucide="eye" class="w-4 h-4"></i>
                                     Lihat
                                 </a>
                                 <a href="{{ route('pegawai-unmul.usulan-nuptk.show-document', [$usulan, 'surat_keterangan_sehat']) }}?download=1" 
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
                                 @if(isset($usulan->data_usulan['dokumen_usulan']['surat_keterangan_sehat']['path']))
                                     Ganti Dokumen
                                 @else
                                     Upload Dokumen
                                 @endif
                             </label>
                             <input type="file" name="surat_keterangan_sehat" accept=".pdf" 
                                    class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('surat_keterangan_sehat') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                             <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                             @error('surat_keterangan_sehat')
                                 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                             @enderror
                         </div>
                     @endif
                 </div>
                 @endif

                 {{-- Field Upload Surat Pernyataan dari Pimpinan PTN --}}
                 @if($usulan->jenis_nuptk !== 'jabatan_fungsional_tertentu')
                 <div>
                     <label class="block text-sm font-semibold text-gray-800">Surat Pernyataan dari Pimpinan PTN</label>
                     <p class="text-xs text-gray-600 mb-2">Scan/foto Surat Pernyataan (maksimal 1 MB, format: PDF)</p>
                     
                     @if(isset($usulan->data_usulan['dokumen_usulan']['surat_pernyataan_pimpinan']['path']))
                         <div class="space-y-3">
                             <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                 <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                                 <span class="text-sm text-blue-800">Surat Pernyataan dari Pimpinan PTN sudah diupload</span>
                                 </div>
                             <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                                 <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                                 <div class="flex-1">
                                     <div class="text-sm font-medium text-gray-800">Surat Pernyataan dari Pimpinan PTN</div>
                                 </div>
                                 <a href="{{ route('pegawai-unmul.usulan-nuptk.show-document', [$usulan, 'surat_pernyataan_pimpinan']) }}" 
                                    target="_blank" 
                                    class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                     <i data-lucide="eye" class="w-4 h-4"></i>
                                     Lihat
                                 </a>
                                 <a href="{{ route('pegawai-unmul.usulan-nuptk.show-document', [$usulan, 'surat_pernyataan_pimpinan']) }}?download=1" 
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
                                 @if(isset($usulan->data_usulan['dokumen_usulan']['surat_pernyataan_pimpinan']['path']))
                                     Ganti Dokumen
                                 @else
                                     Upload Dokumen
                                 @endif
                             </label>
                             <input type="file" name="surat_pernyataan_pimpinan" accept=".pdf" 
                                    class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('surat_pernyataan_pimpinan') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                             <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                             @error('surat_pernyataan_pimpinan')
                                 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                             @enderror
                         </div>
                     @endif
                 </div>
                 @endif

                 {{-- Field khusus untuk Dosen Tetap --}}
                 @if($usulan->jenis_nuptk === 'dosen_tetap')
                 {{-- Field Upload Surat Pernyataan Dosen Tetap --}}
                 <div>
                     <label class="block text-sm font-semibold text-gray-800">Surat Pernyataan Dosen Tetap</label>
                     <p class="text-xs text-gray-600 mb-2">Scan/foto Surat Pernyataan Dosen Tetap (maksimal 1 MB, format: PDF)</p>
                     
                     @if(isset($usulan->data_usulan['dokumen_usulan']['surat_pernyataan_dosen_tetap']['path']))
                         <div class="space-y-3">
                             <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                 <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                                 <span class="text-sm text-blue-800">Surat Pernyataan Dosen Tetap sudah diupload</span>
                                 </div>
                             <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                                 <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                                 <div class="flex-1">
                                     <div class="text-sm font-medium text-gray-800">Surat Pernyataan Dosen Tetap</div>
                                 </div>
                                 <a href="{{ route('pegawai-unmul.usulan-nuptk.show-document', [$usulan, 'surat_pernyataan_dosen_tetap']) }}" 
                                    target="_blank" 
                                    class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                     <i data-lucide="eye" class="w-4 h-4"></i>
                                     Lihat
                                 </a>
                                 <a href="{{ route('pegawai-unmul.usulan-nuptk.show-document', [$usulan, 'surat_pernyataan_dosen_tetap']) }}?download=1" 
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
                                 @if(isset($usulan->data_usulan['dokumen_usulan']['surat_pernyataan_dosen_tetap']['path']))
                                     Ganti Dokumen
                                 @else
                                     Upload Dokumen
                                 @endif
                             </label>
                             <input type="file" name="surat_pernyataan_dosen_tetap" accept=".pdf" 
                                    class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('surat_pernyataan_dosen_tetap') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                             <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                             @error('surat_pernyataan_dosen_tetap')
                                 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                             @enderror
                         </div>
                     @endif
                 </div>

                 {{-- Field Upload Surat Keterangan Aktif Melaksanakan Tridharma --}}
                 <div>
                     <label class="block text-sm font-semibold text-gray-800">Surat Keterangan Aktif Melaksanakan Tridharma</label>
                     <p class="text-xs text-gray-600 mb-2">Scan/foto Surat Keterangan Aktif Tridharma (maksimal 1 MB, format: PDF)</p>
                     
                     @if(isset($usulan->data_usulan['dokumen_usulan']['surat_keterangan_aktif_tridharma']['path']))
                         <div class="space-y-3">
                             <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                 <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                                 <span class="text-sm text-blue-800">Surat Keterangan Aktif Tridharma sudah diupload</span>
                                 </div>
                             <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                                 <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                                 <div class="flex-1">
                                     <div class="text-sm font-medium text-gray-800">Surat Keterangan Aktif Tridharma</div>
                                 </div>
                                 <a href="{{ route('pegawai-unmul.usulan-nuptk.show-document', [$usulan, 'surat_keterangan_aktif_tridharma']) }}" 
                                    target="_blank" 
                                    class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                     <i data-lucide="eye" class="w-4 h-4"></i>
                                     Lihat
                                 </a>
                                 <a href="{{ route('pegawai-unmul.usulan-nuptk.show-document', [$usulan, 'surat_keterangan_aktif_tridharma']) }}?download=1" 
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
                                 @if(isset($usulan->data_usulan['dokumen_usulan']['surat_keterangan_aktif_tridharma']['path']))
                                     Ganti Dokumen
                                 @else
                                     Upload Dokumen
                                 @endif
                             </label>
                             <input type="file" name="surat_keterangan_aktif_tridharma" accept=".pdf" 
                                    class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('surat_keterangan_aktif_tridharma') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                             <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                             @error('surat_keterangan_aktif_tridharma')
                                 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                             @enderror
                         </div>
                     @endif
                 </div>
                                   @endif

                  {{-- Field khusus untuk Dosen Tidak Tetap dan Pengajar Non Dosen --}}
                  @if(in_array($usulan->jenis_nuptk, ['dosen_tidak_tetap', 'pengajar_non_dosen']))
                  {{-- Field Upload Surat Izin Instansi Induk --}}
                  <div>
                      <label class="block text-sm font-semibold text-gray-800">Surat Izin Instansi Induk</label>
                      <p class="text-xs text-gray-600 mb-2">Scan/foto Surat Izin Instansi Induk (maksimal 1 MB, format: PDF)</p>
                      
                      @if(isset($usulan->data_usulan['dokumen_usulan']['surat_izin_instansi_induk']['path']))
                          <div class="space-y-3">
                              <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                  <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                                  <span class="text-sm text-blue-800">Surat Izin Instansi Induk sudah diupload</span>
                                  </div>
                              <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                                  <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                                  <div class="flex-1">
                                      <div class="text-sm font-medium text-gray-800">Surat Izin Instansi Induk</div>
                                  </div>
                                  <a href="{{ route('pegawai-unmul.usulan-nuptk.show-document', [$usulan, 'surat_izin_instansi_induk']) }}" 
                                     target="_blank" 
                                     class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                      <i data-lucide="eye" class="w-4 h-4"></i>
                                      Lihat
                                  </a>
                                  <a href="{{ route('pegawai-unmul.usulan-nuptk.show-document', [$usulan, 'surat_izin_instansi_induk']) }}?download=1" 
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
                                  @if(isset($usulan->data_usulan['dokumen_usulan']['surat_izin_instansi_induk']['path']))
                                      Ganti Dokumen
                                  @else
                                      Upload Dokumen
                                  @endif
                              </label>
                              <input type="file" name="surat_izin_instansi_induk" accept=".pdf" 
                                     class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('surat_izin_instansi_induk') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                              <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                              @error('surat_izin_instansi_induk')
                                  <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                              @enderror
                          </div>
                      @endif
                  </div>

                  {{-- Field Upload Surat Perjanjian Kerja --}}
                  <div>
                      <label class="block text-sm font-semibold text-gray-800">Upload Surat Perjanjian Kerja</label>
                      <p class="text-xs text-gray-600 mb-2">Scan/foto Surat Perjanjian Kerja (maksimal 1 MB, format: PDF)</p>
                      
                      @if(isset($usulan->data_usulan['dokumen_usulan']['surat_perjanjian_kerja']['path']))
                          <div class="space-y-3">
                              <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                  <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                                  <span class="text-sm text-blue-800">Surat Perjanjian Kerja sudah diupload</span>
                                  </div>
                              <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                                  <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                                  <div class="flex-1">
                                      <div class="text-sm font-medium text-gray-800">Surat Perjanjian Kerja</div>
                                  </div>
                                  <a href="{{ route('pegawai-unmul.usulan-nuptk.show-document', [$usulan, 'surat_perjanjian_kerja']) }}" 
                                     target="_blank" 
                                     class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                      <i data-lucide="eye" class="w-4 h-4"></i>
                                      Lihat
                                  </a>
                                  <a href="{{ route('pegawai-unmul.usulan-nuptk.show-document', [$usulan, 'surat_perjanjian_kerja']) }}?download=1" 
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
                                  @if(isset($usulan->data_usulan['dokumen_usulan']['surat_perjanjian_kerja']['path']))
                                      Ganti Dokumen
                                  @else
                                      Upload Dokumen
                                  @endif
                              </label>
                              <input type="file" name="surat_perjanjian_kerja" accept=".pdf" 
                                     class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('surat_perjanjian_kerja') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                              <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                              @error('surat_perjanjian_kerja')
                                  <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                              @enderror
                          </div>
                      @endif
                  </div>
                                     @endif

                   {{-- Field khusus untuk Pengajar Non Dosen --}}
                   @if($usulan->jenis_nuptk === 'pengajar_non_dosen')
                   {{-- Field Upload SK Tenaga Pengajar --}}
                   <div>
                       <label class="block text-sm font-semibold text-gray-800">SK Tenaga Pengajar</label>
                       <p class="text-xs text-gray-600 mb-2">Scan/foto SK Tenaga Pengajar (maksimal 1 MB, format: PDF)</p>
                       
                       @if(isset($usulan->data_usulan['dokumen_usulan']['sk_tenaga_pengajar']['path']))
                           <div class="space-y-3">
                               <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                   <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                                   <span class="text-sm text-blue-800">SK Tenaga Pengajar sudah diupload</span>
                                   </div>
                               <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                                   <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                                   <div class="flex-1">
                                       <div class="text-sm font-medium text-gray-800">SK Tenaga Pengajar</div>
                                   </div>
                                   <a href="{{ route('pegawai-unmul.usulan-nuptk.show-document', [$usulan, 'sk_tenaga_pengajar']) }}" 
                                      target="_blank" 
                                      class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                       <i data-lucide="eye" class="w-4 h-4"></i>
                                       Lihat
                                   </a>
                                   <a href="{{ route('pegawai-unmul.usulan-nuptk.show-document', [$usulan, 'sk_tenaga_pengajar']) }}?download=1" 
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
                                   @if(isset($usulan->data_usulan['dokumen_usulan']['sk_tenaga_pengajar']['path']))
                                       Ganti Dokumen
                                   @else
                                       Upload Dokumen
                                   @endif
                               </label>
                               <input type="file" name="sk_tenaga_pengajar" accept=".pdf" 
                                      class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('sk_tenaga_pengajar') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                               <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                               @error('sk_tenaga_pengajar')
                                   <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                               @enderror
                           </div>
                       @endif
                   </div>
                   @endif
               </div>
              </div>
          </div>
      </div>
