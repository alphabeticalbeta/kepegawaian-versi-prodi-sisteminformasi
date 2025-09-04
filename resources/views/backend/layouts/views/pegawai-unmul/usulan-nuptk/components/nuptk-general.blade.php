{{-- Form General NUPTK untuk semua jenis NUPTK --}}
<form action="{{ route('pegawai-unmul.usulan-nuptk.update', $usulan) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="action" id="formAction" value="simpan">
    
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
                 <option value="belum_kawin" {{ old('status_kawin', $usulan->data_usulan['status_kawin'] ?? '') === 'belum_kawin' ? 'selected' : '' }}>
                     Belum Kawin
                 </option>
                 <option value="kawin" {{ old('status_kawin', $usulan->data_usulan['status_kawin'] ?? '') === 'kawin' ? 'selected' : '' }}>
                     Kawin
                 </option>
                 <option value="cerai" {{ old('status_kawin', $usulan->data_usulan['status_kawin'] ?? '') === 'cerai' ? 'selected' : '' }}>
                     Cerai
                 </option>
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
                 <option value="islam" {{ old('agama', $usulan->data_usulan['agama'] ?? '') === 'islam' ? 'selected' : '' }}>
                     Islam
                 </option>
                 <option value="kristen" {{ old('agama', $usulan->data_usulan['agama'] ?? '') === 'kristen' ? 'selected' : '' }}>
                     Kristen
                 </option>
                 <option value="katolik" {{ old('agama', $usulan->data_usulan['agama'] ?? '') === 'katolik' ? 'selected' : '' }}>
                     Katolik
                 </option>
                 <option value="hindu" {{ old('agama', $usulan->data_usulan['agama'] ?? '') === 'hindu' ? 'selected' : '' }}>
                     Hindu
                 </option>
                 <option value="budha" {{ old('agama', $usulan->data_usulan['agama'] ?? '') === 'budha' ? 'selected' : '' }}>
                     Budha
                 </option>
                 <option value="khonghucu" {{ old('agama', $usulan->data_usulan['agama'] ?? '') === 'khonghucu' ? 'selected' : '' }}>
                     Khonghucu
                 </option>
             </select>
             @error('agama')
                 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
             @enderror
         </div>


     </div>

     {{-- Field Alamat Detail --}}
     <div class="mt-6">
         <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 p-4 rounded-r-lg mb-6">
             <div class="flex items-center space-x-3">
                 <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                     <i class="fas fa-map-marker-alt text-blue-600"></i>
                 </div>
                 <div>
                     <h3 class="text-lg font-semibold text-blue-800">Alamat Lengkap</h3>
                     <p class="text-sm text-blue-600">Informasi detail alamat tempat tinggal</p>
                 </div>
             </div>
         </div>
         
         <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
             {{-- Nama Jalan --}}
             <div>
                 <label for="nama_jalan" class="block text-sm font-medium text-gray-700 mb-2">Nama Jalan</label>
                 <input type="text" 
                        id="nama_jalan" 
                        name="nama_jalan" 
                        value="{{ old('nama_jalan', $usulan->data_usulan['nama_jalan'] ?? '') }}"
                        class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('nama_jalan') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                        placeholder="Masukkan nama jalan lengkap"
                        {{ $isViewOnly ? 'disabled' : '' }}>
                 @error('nama_jalan')
                     <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                 @enderror
             </div>

             {{-- Nomor RT --}}
             <div>
                 <label for="nomor_rt" class="block text-sm font-medium text-gray-700 mb-2">Nomor RT</label>
                 <input type="text" 
                        id="nomor_rt" 
                        name="nomor_rt" 
                        value="{{ old('nomor_rt', $usulan->data_usulan['nomor_rt'] ?? '') }}"
                        class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('nomor_rt') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                        placeholder="Contoh: 001"
                        maxlength="3"
                        pattern="[0-9]{1,3}"
                        title="Nomor RT harus berupa angka 1-3 digit"
                        {{ $isViewOnly ? 'disabled' : '' }}>
                 @error('nomor_rt')
                     <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                 @enderror
             </div>

             {{-- Nomor RW --}}
             <div>
                 <label for="nomor_rw" class="block text-sm font-medium text-gray-700 mb-2">Nomor RW</label>
                 <input type="text" 
                        id="nomor_rw" 
                        name="nomor_rw" 
                        value="{{ old('nomor_rw', $usulan->data_usulan['nomor_rw'] ?? '') }}"
                        class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('nomor_rw') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                        placeholder="Contoh: 002"
                        maxlength="3"
                        pattern="[0-9]{1,3}"
                        title="Nomor RW harus berupa angka 1-3 digit"
                        {{ $isViewOnly ? 'disabled' : '' }}>
                 @error('nomor_rw')
                     <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                 @enderror
             </div>

             {{-- Nama Kelurahan --}}
             <div>
                 <label for="nama_kelurahan" class="block text-sm font-medium text-gray-700 mb-2">Nama Kelurahan</label>
                 <input type="text" 
                        id="nama_kelurahan" 
                        name="nama_kelurahan" 
                        value="{{ old('nama_kelurahan', $usulan->data_usulan['nama_kelurahan'] ?? '') }}"
                        class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('nama_kelurahan') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                        placeholder="Masukkan nama kelurahan"
                        {{ $isViewOnly ? 'disabled' : '' }}>
                 @error('nama_kelurahan')
                     <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                 @enderror
             </div>

             {{-- Nama Kecamatan --}}
             <div>
                 <label for="nama_kecamatan" class="block text-sm font-medium text-gray-700 mb-2">Nama Kecamatan</label>
                 <input type="text" 
                        id="nama_kecamatan" 
                        name="nama_kecamatan" 
                        value="{{ old('nama_kecamatan', $usulan->data_usulan['nama_kecamatan'] ?? '') }}"
                        class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('nama_kecamatan') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                        placeholder="Masukkan nama kecamatan"
                        {{ $isViewOnly ? 'disabled' : '' }}>
                 @error('nama_kecamatan')
                     <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                 @enderror
             </div>

             {{-- Nama Kota --}}
             <div>
                 <label for="nama_kota" class="block text-sm font-medium text-gray-700 mb-2">Nama Kota/Kabupaten</label>
                 <input type="text" 
                        id="nama_kota" 
                        name="nama_kota" 
                        value="{{ old('nama_kota', $usulan->data_usulan['nama_kota'] ?? '') }}"
                        class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('nama_kota') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                        placeholder="Masukkan nama kota atau kabupaten"
                        {{ $isViewOnly ? 'disabled' : '' }}>
                 @error('nama_kota')
                     <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                 @enderror
             </div>

             {{-- Nama Provinsi --}}
             <div>
                 <label for="nama_provinsi" class="block text-sm font-medium text-gray-700 mb-2">Nama Provinsi</label>
                 <input type="text" 
                        id="nama_provinsi" 
                        name="nama_provinsi" 
                        value="{{ old('nama_provinsi', $usulan->data_usulan['nama_provinsi'] ?? '') }}"
                        class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('nama_provinsi') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                        placeholder="Masukkan nama provinsi"
                        {{ $isViewOnly ? 'disabled' : '' }}>
                 @error('nama_provinsi')
                     <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                 @enderror
             </div>

             {{-- Field Kode Pos --}}
             <div>
                 <label for="kode_pos" class="block text-sm font-medium text-gray-700 mb-2">Kode Pos</label>
                 <input type="text" 
                        id="kode_pos" 
                        name="kode_pos" 
                        value="{{ old('kode_pos', $usulan->data_usulan['kode_pos'] ?? '') }}"
                        class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('kode_pos') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                        placeholder="Masukkan 5 digit kode pos"
                        minlength="5"
                        maxlength="5"
                        pattern="[0-9]{5}"
                        title="Kode pos harus berupa 5 digit angka"
                        {{ $isViewOnly ? 'disabled' : '' }}>
                 @error('kode_pos')
                     <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                 @enderror
             </div>
                  </div>
     </div>

     {{-- Section Dokumen Usulan NUPTK --}}
     <div class="mt-6">
         <div class="bg-gradient-to-r from-emerald-50 to-green-50 border-l-4 border-emerald-500 p-4 rounded-r-lg mb-6">
             <div class="flex items-center space-x-3">
                 <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center">
                     <i class="fas fa-file-alt text-emerald-600"></i>
                 </div>
                 <div>
                     <h3 class="text-lg font-semibold text-emerald-800">Dokumen Usulan NUPTK</h3>
                     <p class="text-sm text-emerald-600">Dokumen pendukung untuk usulan NUPTK</p>
                 </div>
             </div>
         </div>
         
                   <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
              <div class="space-y-6">
                  {{-- Sub-section: Dokumen Identitas --}}
                  <div>
                      <h4 class="text-lg font-semibold text-emerald-800 mb-4 border-b border-emerald-200 pb-2">Dokumen Identitas</h4>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                          {{-- Field Foto (dari my profile) --}}
                          <div>
                              <label class="block text-sm font-medium text-gray-700 mb-2">Foto</label>
                              <p class="text-xs text-gray-600 mb-3">Foto diambil dari profil pengguna</p>
                              
                              @php
                                  $currentUser = auth()->user();
                                  $hasFoto = $currentUser && $currentUser->foto;
                                  
                              @endphp
                              
                              @if($hasFoto)
                                  <div class="space-y-3">                         
                                      {{-- Action Buttons --}}
                                      <div class="flex flex-wrap gap-2">
                                          {{-- Lihat Foto --}}
                                          <a href="{{ asset('storage/' . $currentUser->foto) }}" 
                                             target="_blank"
                                             class="inline-flex items-center px-3 py-2 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 hover:border-emerald-300 transition-colors">
                                              <i class="fas fa-eye mr-2"></i>
                                              Lihat Foto
                                          </a>
                                         
                                      </div>
                                  </div>
                              @else
                                  <div class="flex items-center space-x-3 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                                      <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center">
                                          <i class="fas fa-exclamation-triangle text-amber-600"></i>
                                      </div>
                                      <div class="flex-1">
                                          <p class="text-sm font-medium text-amber-800">Foto Profil Belum Tersedia</p>
                                          <p class="text-xs text-amber-600">
                                              @if(!$currentUser)
                                                  User tidak terautentikasi
                                              @elseif(!$currentUser->foto)
                                                  Field foto kosong atau null
                                              @else
                                                  Silakan upload foto di halaman profil terlebih dahulu
                                              @endif
                                          </p>
                                      </div>
                                  </div>
                              @endif
                          </div>

                          {{-- Field Upload KTP --}}
                          <div>
                              <label for="ktp" class="block text-sm font-medium text-gray-700 mb-2">Upload KTP</label>
                              <p class="text-xs text-gray-600 mb-3">Scan/foto KTP (maksimal 1 MB, format: JPEG, JPG, PNG)</p>
                              
                              @if(isset($usulan->data_usulan['ktp']) && $usulan->data_usulan['ktp'])
                                  <div class="mb-3">
                                      <div class="flex items-center space-x-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                          <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                              <i class="fas fa-check text-green-600"></i>
                                          </div>
                                          <div class="flex-1">
                                              <p class="text-sm font-medium text-green-800">KTP Sudah Diupload</p>
                                              <p class="text-xs text-green-600">{{ basename($usulan->data_usulan['ktp']) }}</p>
                                          </div>
                                          @if(!$isViewOnly)
                                              <button type="button" 
                                                      onclick="removeKtp()" 
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
                                  id="ktp" 
                                  name="ktp" 
                                  accept=".jpeg,.jpg,.png"
                                  class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 file:cursor-pointer cursor-pointer border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('ktp') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">                 
                                      @error('ktp')
                                          <p class="text-sm text-red-600">{{ $message }}</p>
                                      @enderror
                                  </div>
                              @endif
                          </div>

                          {{-- Field Upload Kartu Keluarga (Hanya untuk Tenaga Kependidikan) --}}
                          @if($usulan->jenis_nuptk === 'tenaga_kependidikan')
                          <div>
                              <label for="kartu_keluarga" class="block text-sm font-medium text-gray-700 mb-2">Upload Kartu Keluarga</label>
                              <p class="text-xs text-gray-600 mb-3">Scan/foto Kartu Keluarga (maksimal 1 MB, format: JPEG, JPG, PNG)</p>
                              
                              @if(isset($usulan->data_usulan['kartu_keluarga']) && $usulan->data_usulan['kartu_keluarga'])
                                  <div class="mb-3">
                                      <div class="flex items-center space-x-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                          <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                              <i class="fas fa-check text-green-600"></i>
                                          </div>
                                          <div class="flex-1">
                                              <p class="text-sm font-medium text-green-800">Kartu Keluarga Sudah Diupload</p>
                                              <p class="text-xs text-green-600">{{ basename($usulan->data_usulan['kartu_keluarga']) }}</p>
                                          </div>
                                          @if(!$isViewOnly)
                                              <button type="button" 
                                                      onclick="removeKartuKeluarga()" 
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
                                             id="kartu_keluarga" 
                                             name="kartu_keluarga" 
                                             accept=".jpeg,.jpg,.png"
                                             class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 file:cursor-pointer cursor-pointer border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('kartu_keluarga') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">                 
                                      
                                      <div class="text-xs text-gray-500 space-y-1">
                                          <p>• Format yang didukung: JPEG, JPG, PNG</p>
                                          <p>• Ukuran maksimal: 1 MB</p>
                                          <p>• Pastikan Kartu Keluarga terlihat jelas dan lengkap</p>
                                      </div>
                                      
                                      @error('kartu_keluarga')
                                          <p class="text-sm text-red-600">{{ $message }}</p>
                                      @enderror
                                  </div>
                              @endif
                          </div>
                          @endif
                      </div>
                  </div>

                  {{-- Sub-section: Dokumen Pendidikan untuk Dosen --}}
                  @if(in_array($usulan->jenis_nuptk, ['dosen_tetap', 'dosen_tidak_tetap', 'pengajar_non_dosen']))
                  <div>
                      <h4 class="text-lg font-semibold text-purple-800 mb-4 border-b border-purple-200 pb-2">Dokumen Pendidikan</h4>
                      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                          {{-- Field S1 (Wajib) --}}
                          <div class="border-l-4 border-red-500 pl-4">
                              <h5 class="text-md font-semibold text-red-700 mb-3">S1 - Wajib</h5>
                              <label for="ijazah_transkrip_s1" class="block text-sm font-medium text-gray-700 mb-2">Ijazah & Transkrip S1</label>
                              <p class="text-xs text-gray-600 mb-3">PDF (max 1 MB)</p>
                              
                              @if(isset($usulan->data_usulan['ijazah_transkrip_s1']) && $usulan->data_usulan['ijazah_transkrip_s1'])
                                  <div class="mb-3">
                                      <div class="flex items-center space-x-2 p-2 bg-green-50 border border-green-200 rounded-lg">
                                          <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                              <i class="fas fa-check text-green-600 text-xs"></i>
                                          </div>
                                          <div class="flex-1">
                                              <p class="text-xs font-medium text-green-800">Sudah Upload</p>
                                              <p class="text-xs text-green-600">{{ basename($usulan->data_usulan['ijazah_transkrip_s1']) }}</p>
                                          </div>
                                          @if(!$isViewOnly)
                                              <button type="button" 
                                                      onclick="removeFile('ijazah_transkrip_s1')" 
                                                      class="text-red-600 hover:text-red-800 text-xs">
                                                  <i class="fas fa-trash"></i>
                                              </button>
                                          @endif
                                      </div>
                                  </div>
                              @endif
                              
                              @if(!$isViewOnly)
                                  <input type="file" 
                                         id="ijazah_transkrip_s1" 
                                         name="ijazah_transkrip_s1" 
                                         accept=".pdf"
                                         required
                                         class="block w-full text-sm text-gray-500 file:mr-2 file:py-3 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 file:cursor-pointer cursor-pointer border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('ijazah_transkrip_s1') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                                  
                                  @error('ijazah_transkrip_s1')
                                      <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                  @enderror
                              @endif
                          </div>

                          {{-- Field S2 (Wajib) --}}
                          <div class="border-l-4 border-red-500 pl-4">
                              <h5 class="text-md font-semibold text-red-700 mb-3">S2 - Wajib</h5>
                              <label for="ijazah_transkrip_s2" class="block text-sm font-medium text-gray-700 mb-2">Ijazah & Transkrip S2</label>
                              <p class="text-xs text-gray-600 mb-3">PDF (max 1 MB)</p>
                              
                              @if(isset($usulan->data_usulan['ijazah_transkrip_s2']) && $usulan->data_usulan['ijazah_transkrip_s2'])
                                  <div class="mb-3">
                                      <div class="flex items-center space-x-2 p-2 bg-green-50 border border-green-200 rounded-lg">
                                          <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                              <i class="fas fa-check text-green-600 text-xs"></i>
                                          </div>
                                          <div class="flex-1">
                                              <p class="text-xs font-medium text-green-800">Sudah Upload</p>
                                              <p class="text-xs text-green-600">{{ basename($usulan->data_usulan['ijazah_transkrip_s2']) }}</p>
                                          </div>
                                          @if(!$isViewOnly)
                                              <button type="button" 
                                                      onclick="removeFile('ijazah_transkrip_s2')" 
                                                      class="text-red-600 hover:text-red-800 text-xs">
                                                  <i class="fas fa-trash"></i>
                                              </button>
                                          @endif
                                      </div>
                                  </div>
                              @endif
                              
                              @if(!$isViewOnly)
                                  <input type="file" 
                                         id="ijazah_transkrip_s2" 
                                         name="ijazah_transkrip_s2" 
                                         accept=".pdf"
                                         required
                                         class="block w-full text-sm text-gray-500 file:mr-2 file:py-3 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 file:cursor-pointer cursor-pointer border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('ijazah_transkrip_s2') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                                  
                                  @error('ijazah_transkrip_s2')
                                      <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                  @enderror
                              @endif
                          </div>

                          {{-- Field S3 (Opsional) --}}
                          <div class="border-l-4 border-gray-400 pl-4">
                              <h5 class="text-md font-semibold text-gray-700 mb-3">S3 - Opsional</h5>
                              <label for="ijazah_transkrip_s3" class="block text-sm font-medium text-gray-700 mb-2">Ijazah & Transkrip S3</label>
                              <p class="text-xs text-gray-600 mb-3">PDF (max 1 MB)</p>
                              
                              @if(isset($usulan->data_usulan['ijazah_transkrip_s3']) && $usulan->data_usulan['ijazah_transkrip_s3'])
                                  <div class="mb-3">
                                      <div class="flex items-center space-x-2 p-2 bg-green-50 border border-green-200 rounded-lg">
                                          <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                              <i class="fas fa-check text-green-600 text-xs"></i>
                                          </div>
                                          <div class="flex-1">
                                              <p class="text-xs font-medium text-green-800">Sudah Upload</p>
                                              <p class="text-xs text-green-600">{{ basename($usulan->data_usulan['ijazah_transkrip_s3']) }}</p>
                                          </div>
                                          @if(!$isViewOnly)
                                              <button type="button" 
                                                      onclick="removeFile('ijazah_transkrip_s3')" 
                                                      class="text-red-600 hover:text-red-800 text-xs">
                                                  <i class="fas fa-trash"></i>
                                              </button>
                                          @endif
                                      </div>
                                  </div>
                              @endif
                              
                              @if(!$isViewOnly)
                                  <input type="file" 
                                         id="ijazah_transkrip_s3" 
                                         name="ijazah_transkrip_s3" 
                                         accept=".pdf"
                                         class="block w-full text-sm text-gray-500 file:mr-2 file:py-3 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100 file:cursor-pointer cursor-pointer border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('ijazah_transkrip_s3') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                                  
                                  @error('ijazah_transkrip_s3')
                                      <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                  @enderror
                              @endif
                          </div>
                      </div>
                  </div>
                  @endif

                  {{-- Sub-section: Dokumen Pendidikan untuk Tenaga Kependidikan --}}
                  @if($usulan->jenis_nuptk === 'tenaga_kependidikan')
                  <div>
                      <h4 class="text-lg font-semibold text-orange-800 mb-4 border-b border-orange-200 pb-2">Dokumen Pendidikan</h4>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                          {{-- Ijazah dari DataPegawaiController --}}
                          <div>
                              <label class="block text-sm font-medium text-gray-700 mb-2">Ijazah</label>
                              <p class="text-xs text-gray-600 mb-3">Dokumen ijazah dari data pegawai</p>
                              
                              @if(auth()->user() && auth()->user()->ijazah)
                                  <div class="flex items-center space-x-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                      <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                          <i class="fas fa-check text-green-600"></i>
                                      </div>
                                      <div class="flex-1">
                                          <p class="text-sm font-medium text-green-800">Ijazah Tersedia</p>
                                          <p class="text-xs text-green-600">{{ basename(auth()->user()->ijazah) }}</p>
                                      </div>
                                  </div>
                              @else
                                  <div class="flex items-center space-x-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                      <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center">
                                          <i class="fas fa-exclamation-triangle text-amber-600"></i>
                                      </div>
                                      <div class="flex-1">
                                          <p class="text-sm font-medium text-amber-800">Ijazah Belum Tersedia</p>
                                          <p class="text-xs text-amber-600">Silakan upload ijazah di halaman profil terlebih dahulu</p>
                                      </div>
                                  </div>
                              @endif
                          </div>

                          {{-- Transkrip dari DataPegawaiController --}}
                          <div>
                              <label class="block text-sm font-medium text-gray-700 mb-2">Transkrip Nilai</label>
                              <p class="text-xs text-gray-600 mb-3">Dokumen transkrip nilai dari data pegawai</p>
                              
                              @if(auth()->user() && auth()->user()->transkrip_nilai)
                                  <div class="flex items-center space-x-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                      <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                          <i class="fas fa-check text-green-600"></i>
                                      </div>
                                      <div class="flex-1">
                                          <p class="text-sm font-medium text-green-800">Transkrip Nilai Tersedia</p>
                                          <p class="text-xs text-green-600">{{ basename(auth()->user()->transkrip_nilai) }}</p>
                                      </div>
                                  </div>
                              @else
                                  <div class="flex items-center space-x-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                      <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center">
                                          <i class="fas fa-exclamation-triangle text-amber-600"></i>
                                      </div>
                                      <div class="flex-1">
                                          <p class="text-sm font-medium text-amber-800">Transkrip Nilai Belum Tersedia</p>
                                          <p class="text-xs text-amber-600">Silakan upload transkrip nilai di halaman profil terlebih dahulu</p>
                                      </div>
                                  </div>
                              @endif
                          </div>

                          {{-- Field Upload Nota Dinas (Hanya untuk Tenaga Kependidikan) --}}
                          <div>
                              <label for="nota_dinas" class="block text-sm font-medium text-gray-700 mb-2">Upload Nota Dinas</label>
                              <p class="text-xs text-gray-600 mb-3">Scan/foto Nota Dinas (maksimal 1 MB, format: PDF)</p>
                              
                              @if(isset($usulan->data_usulan['nota_dinas']) && $usulan->data_usulan['nota_dinas'])
                                  <div class="mb-3">
                                      <div class="flex items-center space-x-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                          <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                              <i class="fas fa-check text-green-600"></i>
                                          </div>
                                          <div class="flex-1">
                                              <p class="text-sm font-medium text-green-800">Nota Dinas Sudah Diupload</p>
                                              <p class="text-xs text-green-600">{{ basename($usulan->data_usulan['nota_dinas']) }}</p>
                                          </div>
                                          @if(!$isViewOnly)
                                              <button type="button" 
                                                      onclick="removeNotaDinas()" 
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
                                             id="nota_dinas" 
                                             name="nota_dinas" 
                                             accept=".pdf"
                                             onchange="validateNotaDinasFile(this)"
                                             class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 file:cursor-pointer cursor-pointer border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('nota_dinas') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">                                                      
                                      @error('nota_dinas')
                                          <p class="text-sm text-red-600">{{ $message }}</p>
                                      @enderror
                                  </div>
                              @endif
                          </div>
                      </div>
                  </div>
                  @endif
              </div>
          </div>

      
  
  </form>

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
 
 // Function untuk validasi file Nota Dinas
 function validateNotaDinasFile(input) {
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
         return true;
     }
     return false;
 }
 
 // Function untuk menghapus file pendidikan
 function removeFile(fieldName) {
     if (confirm(`Apakah Anda yakin ingin menghapus file ${fieldName} ini?`)) {
         // Hapus file input
         document.getElementById(fieldName).value = '';
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
        
        // Validasi saat form submit
        nikInput.closest('form').addEventListener('submit', function(e) {
            const nikValue = nikInput.value;
            if (nikValue.length > 0 && nikValue.length !== 16) {
                e.preventDefault();
                alert('NIK harus berupa 16 digit angka');
                nikInput.focus();
                return false;
            }
        });
    }
});
</script>
