{{-- Form General NUPTK untuk semua jenis NUPTK --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Field NIK --}}
        <div>
            <label for="nik" class="block text-sm font-semibold text-gray-800">NIK (Nomor Induk Kependudukan)</label>
            <p class="text-xs text-gray-600 mb-2">Nomor Induk Kependudukan sesuai KTP (16 digit angka)</p>
            <input type="text" 
                   id="nik" 
                   name="nik" 
                   value="{{ old('nik', $usulan->data_usulan['nik'] ?? '') }}"
                   class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('nik') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                   placeholder="Masukkan 16 digit NIK"
                   minlength="16"
                   maxlength="16"
                   pattern="[0-9]{16}"
                   title="NIK harus berupa 16 digit angka"
                   {{ $isViewOnly ? 'disabled' : '' }}>
            @error('nik')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

                 {{-- Field Nama Ibu Kandung --}}
         <div>
             <label for="nama_ibu_kandung" class="block text-sm font-semibold text-gray-800">Nama Ibu Kandung</label>
             <p class="text-xs text-gray-600 mb-2">Nama lengkap ibu kandung</p>
             <input type="text" 
                    id="nama_ibu_kandung" 
                    name="nama_ibu_kandung" 
                    value="{{ old('nama_ibu_kandung', $usulan->data_usulan['nama_ibu_kandung'] ?? '') }}"
                    class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('nama_ibu_kandung') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                    placeholder="Masukkan nama ibu kandung"
                    {{ $isViewOnly ? 'disabled' : '' }}>
             @error('nama_ibu_kandung')
                 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
             @enderror
         </div>

         {{-- Field Status Kawin --}}
         <div>
             <label for="status_kawin" class="block text-sm font-semibold text-gray-800">Status Kawin</label>
             <p class="text-xs text-gray-600 mb-2">Status perkawinan saat ini</p>
             <select id="status_kawin" 
                     name="status_kawin" 
                     class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('status_kawin') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                     {{ $isViewOnly ? 'disabled' : '' }}>
                 <option value="">Pilih Status Kawin</option>
                <option value="belum_kawin" {{ old('status_kawin', $usulan->data_usulan['status_kawin'] ?? '') === 'belum_kawin' ? 'selected' : '' }}>Belum Kawin</option>
                <option value="kawin" {{ old('status_kawin', $usulan->data_usulan['status_kawin'] ?? '') === 'kawin' ? 'selected' : '' }}>Kawin</option>
                <option value="cerai_hidup" {{ old('status_kawin', $usulan->data_usulan['status_kawin'] ?? '') === 'cerai_hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                <option value="cerai_mati" {{ old('status_kawin', $usulan->data_usulan['status_kawin'] ?? '') === 'cerai_mati' ? 'selected' : '' }}>Cerai Mati</option>
             </select>
             @error('status_kawin')
                 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
             @enderror
         </div>

         {{-- Field Agama --}}
         <div>
             <label for="agama" class="block text-sm font-semibold text-gray-800">Agama</label>
             <p class="text-xs text-gray-600 mb-2">Agama yang dianut</p>
             <select id="agama" 
                     name="agama" 
                     class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('agama') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                     {{ $isViewOnly ? 'disabled' : '' }}>
                 <option value="">Pilih Agama</option>
                <option value="islam" {{ old('agama', $usulan->data_usulan['agama'] ?? '') === 'islam' ? 'selected' : '' }}>Islam</option>
                <option value="kristen" {{ old('agama', $usulan->data_usulan['agama'] ?? '') === 'kristen' ? 'selected' : '' }}>Kristen</option>
                <option value="katolik" {{ old('agama', $usulan->data_usulan['agama'] ?? '') === 'katolik' ? 'selected' : '' }}>Katolik</option>
                <option value="hindu" {{ old('agama', $usulan->data_usulan['agama'] ?? '') === 'hindu' ? 'selected' : '' }}>Hindu</option>
                <option value="buddha" {{ old('agama', $usulan->data_usulan['agama'] ?? '') === 'buddha' ? 'selected' : '' }}>Buddha</option>
                <option value="khonghucu" {{ old('agama', $usulan->data_usulan['agama'] ?? '') === 'khonghucu' ? 'selected' : '' }}>Khonghucu</option>
             </select>
             @error('agama')
                 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
             @enderror
         </div>
     </div>

    {{-- Field Alamat Lengkap (Full Width) --}}
     <div class="mt-6">
        <label for="alamat_lengkap" class="block text-sm font-semibold text-gray-800">Alamat Lengkap</label>
        <p class="text-xs text-gray-600 mb-2">Masukkan alamat lengkap termasuk jalan, RT/RW, kelurahan, kecamatan, kota/kabupaten, provinsi, dan kode pos</p>
        <textarea id="alamat_lengkap" 
                  name="alamat_lengkap" 
                  rows="4" 
                  class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('alamat_lengkap') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                  placeholder="Masukkan alamat lengkap"
                  {{ $isViewOnly ? 'disabled' : '' }}>{{ old('alamat_lengkap', $usulan->data_usulan['alamat_lengkap'] ?? '') }}</textarea>
        @error('alamat_lengkap')
                     <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                 @enderror
     </div>

     {{-- Section Dokumen Usulan NUPTK --}}
    <div class="mt-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Dokumen Usulan NUPTK</h3>
        
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Field KTP --}}
                          <div>
                <label class="block text-sm font-semibold text-gray-800">KTP</label>
                <p class="text-xs text-gray-600 mb-2">Scan/foto KTP (maksimal 1 MB, format: PDF)</p>
                
                @if(isset($usulan->data_usulan['dokumen_usulan']['ktp']['path']))
                                  <div class="space-y-3">                         
                        <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                            <span class="text-sm text-blue-800">KTP sudah diupload</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                            <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-800">KTP</div>
                            </div>
                            <a href="{{ route('pegawai-unmul.usulan-nuptk.show-document', [$usulan, 'ktp']) }}" 
                                             target="_blank"
                               class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                Lihat
                            </a>
                            <a href="{{ route('pegawai-unmul.usulan-nuptk.show-document', [$usulan, 'ktp']) }}?download=1" 
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
                            @if(isset($usulan->data_usulan['dokumen_usulan']['ktp']['path']))
                                Ganti Dokumen
                            @else
                                Upload Dokumen
                            @endif
                        </label>
                        <input type="file" name="ktp" accept=".pdf" 
                               class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('ktp') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                                      @error('ktp')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                      @enderror
                                  </div>
                              @endif
                          </div>

            {{-- Field Kartu Keluarga --}}
                          <div>
                <label class="block text-sm font-semibold text-gray-800">Kartu Keluarga</label>
                <p class="text-xs text-gray-600 mb-2">Scan/foto Kartu Keluarga (maksimal 1 MB, format: PDF)</p>
                
                @if(isset($usulan->data_usulan['dokumen_usulan']['kartu_keluarga']['path']))
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                            <span class="text-sm text-blue-800">Kartu Keluarga sudah diupload</span>
                                          </div>
                        <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                            <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                                          <div class="flex-1">
                                <div class="text-sm font-medium text-gray-800">Kartu Keluarga</div>
                                          </div>
                            <a href="{{ route('pegawai-unmul.usulan-nuptk.show-document', [$usulan, 'kartu_keluarga']) }}" 
                               target="_blank" 
                               class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                Lihat
                            </a>
                            <a href="{{ route('pegawai-unmul.usulan-nuptk.show-document', [$usulan, 'kartu_keluarga']) }}?download=1" 
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
                            @if(isset($usulan->data_usulan['dokumen_usulan']['kartu_keluarga']['path']))
                                Ganti Dokumen
                            @else
                                Upload Dokumen
                            @endif
                        </label>
                        <input type="file" name="kartu_keluarga" accept=".pdf" 
                               class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kartu_keluarga') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                        @error('kartu_keluarga')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                  @enderror
                                          </div>
                                          @endif
                                      </div>
                                  </div>
                          </div>

            {{-- Field Nota Dinas - Hanya untuk Jabatan Fungsional Tertentu --}}
            @if($usulan->jenis_nuptk === 'jabatan_fungsional_tertentu')
                          <div>
                <label class="block text-sm font-semibold text-gray-800">Nota Dinas</label>
                <p class="text-xs text-gray-600 mb-2">Scan/foto Nota Dinas (maksimal 1 MB, format: PDF)</p>
                
                @if(isset($usulan->data_usulan['dokumen_usulan']['nota_dinas']['path']))
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                            <span class="text-sm text-blue-800">Nota Dinas sudah diupload</span>
                                          </div>
                        <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                            <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                                          <div class="flex-1">
                                <div class="text-sm font-medium text-gray-800">Nota Dinas</div>
                                          </div>
                            <a href="{{ route('pegawai-unmul.usulan-nuptk.show-document', [$usulan, 'nota_dinas']) }}" 
                               target="_blank" 
                               class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                Lihat
                            </a>
                            <a href="{{ route('pegawai-unmul.usulan-nuptk.show-document', [$usulan, 'nota_dinas']) }}?download=1" 
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
                            @if(isset($usulan->data_usulan['dokumen_usulan']['nota_dinas']['path']))
                                Ganti Dokumen
                            @else
                                Upload Dokumen
                            @endif
                        </label>
                        <input type="file" name="nota_dinas" id="nota_dinas" accept=".pdf" 
                               class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nota_dinas') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                                      @error('nota_dinas')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                      @enderror
                                  </div>
                              @endif
                          </div>
            @endif
        </div>
    </div>


 <script>
 // Function untuk menghapus KTP
 function removeKtp() {
     if (confirm('Apakah Anda yakin ingin menghapus file KTP ini?')) {
         // Hapus file input
         document.getElementById('ktp').value = '';
         // Reload halaman untuk menghilangkan preview
         location.reload();
     }
 }
 
 // Function untuk menghapus Kartu Keluarga
 function removeKartuKeluarga() {
     if (confirm('Apakah Anda yakin ingin menghapus file Kartu Keluarga ini?')) {
         // Hapus file input
         document.getElementById('kartu_keluarga').value = '';
         // Reload halaman untuk menghilangkan preview
         location.reload();
     }
 }
 
 // Function untuk menghapus Nota Dinas
 function removeNotaDinas() {
     if (confirm('Apakah Anda yakin ingin menghapus file Nota Dinas ini?')) {
         // Hapus file input
         document.getElementById('nota_dinas').value = '';
         // Reload halaman untuk menghilangkan preview
         location.reload();
     }
 }
 
 // NIK validation
 document.addEventListener('DOMContentLoaded', function() {
    const nikInput = document.getElementById('nik');
    if (nikInput) {
        // Hanya izinkan input angka
        nikInput.addEventListener('input', function(e) {
            // Hapus semua karakter non-angka
            let value = this.value.replace(/[^0-9]/g, '');
            
            // Batasi maksimal 16 digit
            if (value.length > 16) {
                value = value.substring(0, 16);
            }
            
            this.value = value;
            
            // Update styling berdasarkan validitas
            if (value.length === 16) {
                this.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
                this.classList.add('border-green-500', 'focus:ring-green-500', 'focus:border-green-500');
            } else if (value.length > 0) {
                this.classList.remove('border-green-500', 'focus:ring-green-500', 'focus:border-green-500');
                this.classList.add('border-amber-500', 'focus:ring-amber-500', 'focus:border-amber-500');
            } else {
                this.classList.remove('border-green-500', 'focus:ring-green-500', 'focus:border-green-500', 'border-amber-500', 'focus:ring-amber-500', 'focus:border-amber-500');
                this.classList.add('border-gray-300', 'focus:ring-emerald-500', 'focus:border-emerald-500');
            }
        });
        
        // Mencegah paste karakter non-angka
        nikInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const numericOnly = pastedText.replace(/[^0-9]/g, '');
            
            if (numericOnly.length > 0) {
                const currentValue = this.value;
                const newValue = currentValue + numericOnly;
                
                if (newValue.length <= 16) {
                    this.value = newValue;
                    this.dispatchEvent(new Event('input'));
                }
            }
        });
        
        // Mencegah drop karakter non-angka
        nikInput.addEventListener('drop', function(e) {
            e.preventDefault();
            const droppedText = e.dataTransfer.getData('text');
            const numericOnly = droppedText.replace(/[^0-9]/g, '');
            
            if (numericOnly.length > 0) {
                const currentValue = this.value;
                const newValue = currentValue + numericOnly;
                
                if (newValue.length <= 16) {
                    this.value = newValue;
                    this.dispatchEvent(new Event('input'));
                }
            }
        });
        
        // Mencegah keydown untuk karakter non-angka
        nikInput.addEventListener('keydown', function(e) {
            // Izinkan: backspace, delete, tab, escape, enter, arrow keys
            if ([8, 9, 27, 13, 37, 38, 39, 40, 46].indexOf(e.keyCode) !== -1) {
                return;
            }
            
            // Izinkan: angka 0-9
            if (e.keyCode >= 48 && e.keyCode <= 57) {
                return;
            }
            
            // Izinkan: angka di numpad
            if (e.keyCode >= 96 && e.keyCode <= 105) {
                return;
            }
            
            // Blokir semua karakter lainnya
            e.preventDefault();
        });
        
        // Validasi saat blur
        nikInput.addEventListener('blur', function() {
            const value = this.value;
            if (value.length > 0 && value.length < 16) {
                this.classList.add('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
            }
        });
        
        // Validasi saat form submit (hanya untuk action selain simpan)
        nikInput.closest('form').addEventListener('submit', function(e) {
            const actionInput = this.querySelector('input[name="action"]');
            const action = actionInput ? actionInput.value : 'simpan';
            
            // Hanya validasi NIK jika bukan action simpan
            if (action !== 'simpan') {
            const nikValue = nikInput.value;
            if (nikValue.length > 0 && nikValue.length !== 16) {
                e.preventDefault();
                alert('NIK harus berupa 16 digit angka');
                nikInput.focus();
                return false;
                }
            }
        });
    }
});
</script>
