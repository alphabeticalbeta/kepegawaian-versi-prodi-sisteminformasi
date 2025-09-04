{{-- Form Dosen untuk jenis NUPTK Dosen --}}
<form action="{{ route('pegawai-unmul.usulan-nuptk.update', $usulan) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="action" id="formAction" value="simpan">
    
    <div class="space-y-6">
        {{-- Sub-section: Dokumen Pendukung --}}
        <div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                 {{-- Field Upload Surat Keterangan Sehat Rohani, Jasmani dan Bebas Narkotika --}}
                 <div>
                     <label for="surat_keterangan_sehat" class="block text-sm font-medium text-gray-700 mb-2">Surat Keterangan Sehat Rohani, Jasmani dan Bebas Narkotika</label>
                     <p class="text-xs text-gray-600 mb-3">Scan/foto Surat Keterangan Sehat (maksimal 1 MB, format: PDF)</p>
                     
                     @if(isset($usulan->data_usulan['surat_keterangan_sehat']) && $usulan->data_usulan['surat_keterangan_sehat'])
                         <div class="mb-3">
                             <div class="flex items-center space-x-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                 <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                     <i class="fas fa-check text-green-600"></i>
                                 </div>
                                 <div class="flex-1">
                                     <p class="text-sm font-medium text-green-800">Surat Keterangan Sehat Sudah Diupload</p>
                                     <p class="text-xs text-green-600">{{ basename($usulan->data_usulan['surat_keterangan_sehat']) }}</p>
                                 </div>
                                 @if(!$isViewOnly)
                                     <button type="button" 
                                             onclick="removeSuratKeteranganSehat()" 
                                             class="text-red-600 hover:text-red-800 text-sm">
                                         <i class="fas fa-trash"></i>
                                     </button>
                                 @endif
                             </div>
                         </div>
                     @endif
                     
                     @if(!$isViewOnly)
                         <div class="space-y-3">
                             <input type="file" 
                                    id="surat_keterangan_sehat" 
                                    name="surat_keterangan_sehat" 
                                    accept=".pdf"
                                    onchange="validateSuratKeteranganSehatFile(this)"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 file:cursor-pointer cursor-pointer border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('surat_keterangan_sehat') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">                                          
                             @error('surat_keterangan_sehat')
                                 <p class="text-sm text-red-600">{{ $message }}</p>
                             @enderror
                         </div>
                     @endif
                 </div>

                 {{-- Field Upload Surat Pernyataan dari Pimpinan PTN --}}
                 <div>
                     <label for="surat_pernyataan_pimpinan" class="block text-sm font-medium text-gray-700 mb-2">Surat Pernyataan dari Pimpinan PTN</label>
                     <p class="text-xs text-gray-600 mb-3">Scan/foto Surat Pernyataan (maksimal 1 MB, format: PDF)</p>
                     
                     @if(isset($usulan->data_usulan['surat_pernyataan_pimpinan']) && $usulan->data_usulan['surat_pernyataan_pimpinan'])
                         <div class="mb-3">
                             <div class="flex items-center space-x-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                 <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                     <i class="fas fa-check text-green-600"></i>
                                 </div>
                                 <div class="flex-1">
                                     <p class="text-sm font-medium text-green-800">Surat Pernyataan Sudah Diupload</p>
                                     <p class="text-xs text-green-600">{{ basename($usulan->data_usulan['surat_pernyataan_pimpinan']) }}</p>
                                 </div>
                                 @if(!$isViewOnly)
                                     <button type="button" 
                                             onclick="removeSuratPernyataanPimpinan()" 
                                             class="text-red-600 hover:text-red-800 text-sm">
                                         <i class="fas fa-trash"></i>
                                     </button>
                                 @endif
                             </div>
                         </div>
                     @endif
                     
                     @if(!$isViewOnly)
                         <div class="space-y-3">
                             <input type="file" 
                                    id="surat_pernyataan_pimpinan" 
                                    name="surat_pernyataan_pimpinan" 
                                    accept=".pdf"
                                    onchange="validateSuratPernyataanPimpinanFile(this)"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 file:cursor-pointer cursor-pointer border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('surat_pernyataan_pimpinan') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">                 
                             @error('surat_pernyataan_pimpinan')
                                 <p class="text-sm text-red-600">{{ $message }}</p>
                             @enderror
                         </div>
                     @endif
                 </div>

                 {{-- Field khusus untuk Dosen Tetap --}}
                 @if($usulan->jenis_nuptk === 'dosen_tetap')
                 {{-- Field Upload Surat Pernyataan Dosen Tetap --}}
                 <div>
                     <label for="surat_pernyataan_dosen_tetap" class="block text-sm font-medium text-gray-700 mb-2">Surat Pernyataan Dosen Tetap</label>
                     <p class="text-xs text-gray-600 mb-3">Scan/foto Surat Pernyataan Dosen Tetap (maksimal 1 MB, format: PDF)</p>
                     
                     @if(isset($usulan->data_usulan['surat_pernyataan_dosen_tetap']) && $usulan->data_usulan['surat_pernyataan_dosen_tetap'])
                         <div class="mb-3">
                             <div class="flex items-center space-x-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                 <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                     <i class="fas fa-check text-green-600"></i>
                                 </div>
                                 <div class="flex-1">
                                     <p class="text-sm font-medium text-green-800">Surat Pernyataan Dosen Tetap Sudah Diupload</p>
                                     <p class="text-xs text-green-600">{{ basename($usulan->data_usulan['surat_pernyataan_dosen_tetap']) }}</p>
                                 </div>
                                 @if(!$isViewOnly)
                                     <button type="button" 
                                             onclick="removeSuratPernyataanDosenTetap()" 
                                             class="text-red-600 hover:text-red-800 text-sm">
                                         <i class="fas fa-trash"></i>
                                     </button>
                                 @endif
                             </div>
                         </div>
                     @endif
                     
                     @if(!$isViewOnly)
                         <div class="space-y-3">
                             <input type="file" 
                                    id="surat_pernyataan_dosen_tetap" 
                                    name="surat_pernyataan_dosen_tetap" 
                                    accept=".pdf"
                                    onchange="validateSuratPernyataanDosenTetapFile(this)"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 file:cursor-pointer cursor-pointer border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('surat_pernyataan_dosen_tetap') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">                 
                             @error('surat_pernyataan_dosen_tetap')
                                 <p class="text-sm text-red-600">{{ $message }}</p>
                             @enderror
                         </div>
                     @endif
                 </div>

                 {{-- Field Upload Surat Keterangan Aktif Melaksanakan Tridharma --}}
                 <div>
                     <label for="surat_keterangan_aktif_tridharma" class="block text-sm font-medium text-gray-700 mb-2">Surat Keterangan Aktif Melaksanakan Tridharma</label>
                     <p class="text-xs text-gray-600 mb-3">Scan/foto Surat Keterangan Aktif Tridharma (maksimal 1 MB, format: PDF)</p>
                     
                     @if(isset($usulan->data_usulan['surat_keterangan_aktif_tridharma']) && $usulan->data_usulan['surat_keterangan_aktif_tridharma'])
                         <div class="mb-3">
                             <div class="flex items-center space-x-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                 <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                     <i class="fas fa-check text-green-600"></i>
                                 </div>
                                 <div class="flex-1">
                                     <p class="text-sm font-medium text-green-800">Surat Keterangan Aktif Tridharma Sudah Diupload</p>
                                     <p class="text-xs text-green-600">{{ basename($usulan->data_usulan['surat_keterangan_aktif_tridharma']) }}</p>
                                 </div>
                                 @if(!$isViewOnly)
                                     <button type="button" 
                                             onclick="removeSuratKeteranganAktifTridharma()" 
                                             class="text-red-600 hover:text-red-800 text-sm">
                                         <i class="fas fa-trash"></i>
                                     </button>
                                 @endif
                             </div>
                         </div>
                     @endif
                     
                     @if(!$isViewOnly)
                         <div class="space-y-3">
                             <input type="file" 
                                    id="surat_keterangan_aktif_tridharma" 
                                    name="surat_keterangan_aktif_tridharma" 
                                    accept=".pdf"
                                    onchange="validateSuratKeteranganAktifTridharmaFile(this)"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 file:cursor-pointer cursor-pointer border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('surat_keterangan_aktif_tridharma') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">                                        
                             @error('surat_keterangan_aktif_tridharma')
                                 <p class="text-sm text-red-600">{{ $message }}</p>
                             @enderror
                         </div>
                     @endif
                 </div>
                                   @endif

                  {{-- Field khusus untuk Dosen Tidak Tetap dan Pengajar Non Dosen --}}
                  @if(in_array($usulan->jenis_nuptk, ['dosen_tidak_tetap', 'pengajar_non_dosen']))
                  {{-- Field Upload Surat Izin Instansi Induk --}}
                  <div>
                      <label for="surat_izin_instansi_induk" class="block text-sm font-medium text-gray-700 mb-2">Surat Izin Instansi Induk</label>
                      <p class="text-xs text-gray-600 mb-3">Scan/foto Surat Izin Instansi Induk (maksimal 1 MB, format: PDF)</p>
                      
                      @if(isset($usulan->data_usulan['surat_izin_instansi_induk']) && $usulan->data_usulan['surat_izin_instansi_induk'])
                          <div class="mb-3">
                              <div class="flex items-center space-x-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                  <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                      <i class="fas fa-check text-green-600"></i>
                                  </div>
                                  <div class="flex-1">
                                      <p class="text-sm font-medium text-green-800">Surat Izin Instansi Induk Sudah Diupload</p>
                                      <p class="text-xs text-green-600">{{ basename($usulan->data_usulan['surat_izin_instansi_induk']) }}</p>
                                  </div>
                                  @if(!$isViewOnly)
                                      <button type="button" 
                                              onclick="removeSuratIzinInstansiInduk()" 
                                              class="text-red-600 hover:text-red-800 text-sm">
                                          <i class="fas fa-trash"></i>
                                      </button>
                                  @endif
                              </div>
                          </div>
                      @endif
                      
                      @if(!$isViewOnly)
                          <div class="space-y-3">
                              <input type="file" 
                                     id="surat_izin_instansi_induk" 
                                     name="surat_izin_instansi_induk" 
                                     accept=".pdf"
                                     onchange="validateSuratIzinInstansiIndukFile(this)"
                                     class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 file:cursor-pointer cursor-pointer border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('surat_izin_instansi_induk') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">                 
                                                            
                              @error('surat_izin_instansi_induk')
                                  <p class="text-sm text-red-600">{{ $message }}</p>
                              @enderror
                          </div>
                      @endif
                  </div>

                  {{-- Field Upload Surat Perjanjian Kerja --}}
                  <div>
                      <label for="surat_perjanjian_kerja" class="block text-sm font-medium text-gray-700 mb-2">Upload Surat Perjanjian Kerja</label>
                      <p class="text-xs text-gray-600 mb-3">Scan/foto Surat Perjanjian Kerja (maksimal 1 MB, format: PDF)</p>
                      
                      @if(isset($usulan->data_usulan['surat_perjanjian_kerja']) && $usulan->data_usulan['surat_perjanjian_kerja'])
                          <div class="mb-3">
                              <div class="flex items-center space-x-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                  <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                      <i class="fas fa-check text-green-600"></i>
                                  </div>
                                  <div class="flex-1">
                                      <p class="text-sm font-medium text-green-800">Surat Perjanjian Kerja Sudah Diupload</p>
                                      <p class="text-xs text-green-600">{{ basename($usulan->data_usulan['surat_perjanjian_kerja']) }}</p>
                                  </div>
                                  @if(!$isViewOnly)
                                      <button type="button" 
                                              onclick="removeSuratPerjanjianKerja()" 
                                              class="text-red-600 hover:text-red-800 text-sm">
                                          <i class="fas fa-trash"></i>
                                      </button>
                                  @endif
                              </div>
                          </div>
                      @endif
                      
                      @if(!$isViewOnly)
                          <div class="space-y-3">
                              <input type="file" 
                                     id="surat_perjanjian_kerja" 
                                     name="surat_perjanjian_kerja" 
                                     accept=".pdf"
                                     onchange="validateSuratPerjanjianKerjaFile(this)"
                                     class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 file:cursor-pointer cursor-pointer border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('surat_perjanjian_kerja') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">                 

                              @error('surat_perjanjian_kerja')
                                  <p class="text-sm text-red-600">{{ $message }}</p>
                              @enderror
                          </div>
                      @endif
                  </div>
                                     @endif

                   {{-- Field khusus untuk Pengajar Non Dosen --}}
                   @if($usulan->jenis_nuptk === 'pengajar_non_dosen')
                   {{-- Field Upload SK Tenaga Pengajar --}}
                   <div>
                       <label for="sk_tenaga_pengajar" class="block text-sm font-medium text-gray-700 mb-2">SK Tenaga Pengajar</label>
                       <p class="text-xs text-gray-600 mb-3">Scan/foto SK Tenaga Pengajar (maksimal 1 MB, format: PDF)</p>
                       
                       @if(isset($usulan->data_usulan['sk_tenaga_pengajar']) && $usulan->data_usulan['sk_tenaga_pengajar'])
                           <div class="mb-3">
                               <div class="flex items-center space-x-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                   <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                       <i class="fas fa-check text-green-600"></i>
                                   </div>
                                   <div class="flex-1">
                                       <p class="text-sm font-medium text-green-800">SK Tenaga Pengajar Sudah Diupload</p>
                                       <p class="text-xs text-green-600">{{ basename($usulan->data_usulan['sk_tenaga_pengajar']) }}</p>
                                   </div>
                                   @if(!$isViewOnly)
                                       <button type="button" 
                                               onclick="removeSkTenagaPengajar()" 
                                               class="text-red-600 hover:text-red-800 text-sm">
                                           <i class="fas fa-trash"></i>
                                       </button>
                                   @endif
                               </div>
                           </div>
                       @endif
                       
                       @if(!$isViewOnly)
                           <div class="space-y-3">
                               <input type="file" 
                                      id="sk_tenaga_pengajar" 
                                      name="sk_tenaga_pengajar" 
                                      accept=".pdf"
                                      onchange="validateSkTenagaPengajarFile(this)"
                                      class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 file:cursor-pointer cursor-pointer border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('sk_tenaga_pengajar') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">                                               
                               @error('sk_tenaga_pengajar')
                                   <p class="text-sm text-red-600">{{ $message }}</p>
                               @enderror
                           </div>
                       @endif
                   </div>
                   @endif
               </div>
              </div>
          </div>
      </div>
 </form>

<script>
// Function untuk menghapus Surat Keterangan Sehat
function removeSuratKeteranganSehat() {
    if (confirm('Apakah Anda yakin ingin menghapus file Surat Keterangan Sehat ini?')) {
        // Hapus file input
        document.getElementById('surat_keterangan_sehat').value = '';
        // Reload halaman untuk menghilangkan preview
        location.reload();
    }
}

// Function untuk menghapus Surat Pernyataan dari Pimpinan PTN
function removeSuratPernyataanPimpinan() {
    if (confirm('Apakah Anda yakin ingin menghapus file Surat Pernyataan dari Pimpinan PTN ini?')) {
        // Hapus file input
        document.getElementById('surat_pernyataan_pimpinan').value = '';
        // Reload halaman untuk menghilangkan preview
        location.reload();
    }
}

// Function untuk validasi file Surat Keterangan Sehat
function validateSuratKeteranganSehatFile(input) {
    const file = input.files[0];
    if (file) {
        // Validasi tipe file
        if (file.type !== 'application/pdf') {
            alert('File harus berformat PDF');
            input.value = '';
            return false;
        }
        
        // Validasi ukuran file (1 MB = 1024 * 1024 bytes)
        const maxSize = 1024 * 1024; // 1 MB
        if (file.size > maxSize) {
            alert('Ukuran file tidak boleh lebih dari 1 MB');
            input.value = '';
            return false;
        }
        
        // Jika validasi berhasil
        console.log('File Surat Keterangan Sehat valid:', file.name, 'Size:', (file.size / 1024).toFixed(2) + ' KB');
        return true;
    }
    return false;
}

// Function untuk validasi file Surat Pernyataan dari Pimpinan PTN
function validateSuratPernyataanPimpinanFile(input) {
    const file = input.files[0];
    if (file) {
        // Validasi tipe file
        if (file.type !== 'application/pdf') {
            alert('File harus berformat PDF');
            input.value = '';
            return false;
        }
        
        // Validasi ukuran file (1 MB = 1024 * 1024 bytes)
        const maxSize = 1024 * 1024; // 1 MB
        if (file.size > maxSize) {
            alert('Ukuran file tidak boleh lebih dari 1 MB');
            input.value = '';
            return false;
        }
        
        // Jika validasi berhasil
        console.log('File Surat Pernyataan dari Pimpinan PTN valid:', file.name, 'Size:', (file.size / 1024).toFixed(2) + ' KB');
        return true;
    }
    return false;
}

// Function untuk menghapus Surat Pernyataan Dosen Tetap
function removeSuratPernyataanDosenTetap() {
    if (confirm('Apakah Anda yakin ingin menghapus file Surat Pernyataan Dosen Tetap ini?')) {
        // Hapus file input
        document.getElementById('surat_pernyataan_dosen_tetap').value = '';
        // Reload halaman untuk menghilangkan preview
        location.reload();
    }
}

// Function untuk menghapus Surat Keterangan Aktif Melaksanakan Tridharma
function removeSuratKeteranganAktifTridharma() {
    if (confirm('Apakah Anda yakin ingin menghapus file Surat Keterangan Aktif Melaksanakan Tridharma ini?')) {
        // Hapus file input
        document.getElementById('surat_keterangan_aktif_tridharma').value = '';
        // Reload halaman untuk menghilangkan preview
        location.reload();
    }
}

// Function untuk validasi file Surat Pernyataan Dosen Tetap
function validateSuratPernyataanDosenTetapFile(input) {
    const file = input.files[0];
    if (file) {
        // Validasi tipe file
        if (file.type !== 'application/pdf') {
            alert('File harus berformat PDF');
            input.value = '';
            return false;
        }
        
        // Validasi ukuran file (1 MB = 1024 * 1024 bytes)
        const maxSize = 1024 * 1024; // 1 MB
        if (file.size > maxSize) {
            alert('Ukuran file tidak boleh lebih dari 1 MB');
            input.value = '';
            return false;
        }
        
        // Jika validasi berhasil
        console.log('File Surat Pernyataan Dosen Tetap valid:', file.name, 'Size:', (file.size / 1024).toFixed(2) + ' KB');
        return true;
    }
    return false;
}

// Function untuk validasi file Surat Keterangan Aktif Melaksanakan Tridharma
function validateSuratKeteranganAktifTridharmaFile(input) {
    const file = input.files[0];
    if (file) {
        // Validasi tipe file
        if (file.type !== 'application/pdf') {
            alert('File harus berformat PDF');
            input.value = '';
            return false;
        }
        
        // Validasi ukuran file (1 MB = 1024 * 1024 bytes)
        const maxSize = 1024 * 1024; // 1 MB
        if (file.size > maxSize) {
            alert('Ukuran file tidak boleh lebih dari 1 MB');
            input.value = '';
            return false;
        }
        
        // Jika validasi berhasil
        console.log('File Surat Keterangan Aktif Melaksanakan Tridharma valid:', file.name, 'Size:', (file.size / 1024).toFixed(2) + ' KB');
        return true;
    }
    return false;
}

// Function untuk menghapus Surat Izin Instansi Induk
function removeSuratIzinInstansiInduk() {
    if (confirm('Apakah Anda yakin ingin menghapus file Surat Izin Instansi Induk ini?')) {
        // Hapus file input
        document.getElementById('surat_izin_instansi_induk').value = '';
        // Reload halaman untuk menghilangkan preview
        location.reload();
    }
}

// Function untuk menghapus Surat Perjanjian Kerja
function removeSuratPerjanjianKerja() {
    if (confirm('Apakah Anda yakin ingin menghapus file Surat Perjanjian Kerja ini?')) {
        // Hapus file input
        document.getElementById('surat_perjanjian_kerja').value = '';
        // Reload halaman untuk menghilangkan preview
        location.reload();
    }
}

// Function untuk validasi file Surat Izin Instansi Induk
function validateSuratIzinInstansiIndukFile(input) {
    const file = input.files[0];
    if (file) {
        // Validasi tipe file
        if (file.type !== 'application/pdf') {
            alert('File harus berformat PDF');
            input.value = '';
            return false;
        }
        
        // Validasi ukuran file (1 MB = 1024 * 1024 bytes)
        const maxSize = 1024 * 1024; // 1 MB
        if (file.size > maxSize) {
            alert('Ukuran file tidak boleh lebih dari 1 MB');
            input.value = '';
            return false;
        }
        
        // Jika validasi berhasil
        console.log('File Surat Izin Instansi Induk valid:', file.name, 'Size:', (file.size / 1024).toFixed(2) + ' KB');
        return true;
    }
    return false;
}

// Function untuk validasi file Surat Perjanjian Kerja
function validateSuratPerjanjianKerjaFile(input) {
    const file = input.files[0];
    if (file) {
        // Validasi tipe file
        if (file.type !== 'application/pdf') {
            alert('File harus berformat PDF');
            input.value = '';
            return false;
        }
        
        // Validasi ukuran file (1 MB = 1024 * 1024 bytes)
        const maxSize = 1024 * 1024; // 1 MB
        if (file.size > maxSize) {
            alert('Ukuran file tidak boleh lebih dari 1 MB');
            input.value = '';
            return false;
        }
        
        // Jika validasi berhasil
        console.log('File Surat Perjanjian Kerja valid:', file.name, 'Size:', (file.size / 1024).toFixed(2) + ' KB');
        return true;
    }
    return false;
}

// Function untuk menghapus SK Tenaga Pengajar
function removeSkTenagaPengajar() {
    if (confirm('Apakah Anda yakin ingin menghapus file SK Tenaga Pengajar ini?')) {
        // Hapus file input
        document.getElementById('sk_tenaga_pengajar').value = '';
        // Reload halaman untuk menghilangkan preview
        location.reload();
    }
}

// Function untuk validasi file SK Tenaga Pengajar
function validateSkTenagaPengajarFile(input) {
    const file = input.files[0];
    if (file) {
        // Validasi tipe file
        if (file.type !== 'application/pdf') {
            alert('File harus berformat PDF');
            input.value = '';
            return false;
        }
        
        // Validasi ukuran file (1 MB = 1024 * 1024 bytes)
        const maxSize = 1024 * 1024; // 1 MB
        if (file.size > maxSize) {
            alert('Ukuran file tidak boleh lebih dari 1 MB');
            input.value = '';
            return false;
        }
        
        // Jika validasi berhasil
        console.log('File SK Tenaga Pengajar valid:', file.name, 'Size:', (file.size / 1024).toFixed(2) + ' KB');
        return true;
    }
    return false;
}
</script>
