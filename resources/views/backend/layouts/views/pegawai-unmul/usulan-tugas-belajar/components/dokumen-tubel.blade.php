{{-- Section Dokumen Tugas Belajar --}}
        {{-- Surat Keterangan Pembayaran Tunjangan Keluarga --}}
        <div>
            <label class="block text-sm font-semibold text-gray-800 mb-2">
                Surat Keterangan Pembayaran Tunjangan Keluarga <span class="text-red-500">*</span>
            </label>
            <p class="text-xs text-gray-600 mb-2">Surat keterangan pembayaran tunjangan keluarga (PDF, maksimal 1MB)</p>

            @if(isset($usulan->data_usulan['dokumen_usulan']['surat_tunjangan_keluarga']['path']))
                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                        <span class="text-sm text-blue-800">Surat Keterangan Pembayaran Tunjangan Keluarga sudah diupload</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                        <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-800">Surat Keterangan Pembayaran Tunjangan Keluarga</div>
                        </div>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'surat_tunjangan_keluarga']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            Lihat
                        </a>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'surat_tunjangan_keluarga']) }}?download=1"
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
                        @if(isset($usulan->data_usulan['dokumen_usulan']['surat_tunjangan_keluarga']['path']))
                            Ganti Dokumen
                        @else
                            Upload Dokumen
                        @endif
                    </label>
                    <input type="file" id="surat_tunjangan_keluarga" name="surat_tunjangan_keluarga" accept=".pdf"
                           class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('surat_tunjangan_keluarga') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                    @error('surat_tunjangan_keluarga')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif
        </div>

        {{-- Akta Nikah atau Surat Keterangan Belum Menikah --}}
        <div>
            <label class="block text-sm font-semibold text-gray-800 mb-2">
                Akta Nikah / Surat Keterangan Belum Menikah <span class="text-red-500">*</span>
            </label>
            <p class="text-xs text-gray-600 mb-2">
                Akta nikah (jika sudah menikah) atau surat keterangan belum menikah (PDF, maksimal 1MB)
            </p>

            @if(isset($usulan->data_usulan['dokumen_usulan']['akta_nikah']['path']))
                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                        <span class="text-sm text-blue-800">Akta Nikah sudah diupload</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                        <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-800">Akta Nikah / Surat Keterangan Belum Menikah</div>
                        </div>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'akta_nikah']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            Lihat
                        </a>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'akta_nikah']) }}?download=1"
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
                        @if(isset($usulan->data_usulan['dokumen_usulan']['akta_nikah']['path']))
                            Ganti Dokumen
                        @else
                            Upload Dokumen
                        @endif
                    </label>
                    <input type="file" id="akta_nikah" name="akta_nikah" accept=".pdf"
                           class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('akta_nikah') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                    @error('akta_nikah')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif
        </div>

        {{-- Surat Rekomendasi dari Atasan Langsung --}}
        <div>
            <label class="block text-sm font-semibold text-gray-800 mb-2">
                Surat Rekomendasi dari Atasan Langsung <span class="text-red-500">*</span>
            </label>
            <p class="text-xs text-gray-600 mb-2">Surat rekomendasi dari atasan langsung (PDF, maksimal 1MB)</p>

            @if(isset($usulan->data_usulan['dokumen_usulan']['surat_rekomendasi_atasan']['path']))
                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                        <span class="text-sm text-blue-800">Surat Rekomendasi Atasan sudah diupload</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                        <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-800">Surat Rekomendasi dari Atasan Langsung</div>
                        </div>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'surat_rekomendasi_atasan']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            Lihat
                        </a>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'surat_rekomendasi_atasan']) }}?download=1"
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
                        @if(isset($usulan->data_usulan['dokumen_usulan']['surat_rekomendasi_atasan']['path']))
                            Ganti Dokumen
                        @else
                            Upload Dokumen
                        @endif
                    </label>
                    <input type="file" id="surat_rekomendasi_atasan" name="surat_rekomendasi_atasan" accept=".pdf"
                           class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('surat_rekomendasi_atasan') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                    @error('surat_rekomendasi_atasan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif
        </div>

        {{-- Surat Perjanjian Tugas Belajar --}}
        <div>
            <label class="block text-sm font-semibold text-gray-800 mb-2">
                Surat Perjanjian Tugas Belajar <span class="text-red-500">*</span>
            </label>
            <p class="text-xs text-gray-600 mb-2">Surat perjanjian tugas belajar (PDF, maksimal 1MB)</p>

            @if(isset($usulan->data_usulan['dokumen_usulan']['surat_perjanjian_tubel']['path']))
                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                        <span class="text-sm text-blue-800">Surat Perjanjian Tugas Belajar sudah diupload</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                        <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-800">Surat Perjanjian Tugas Belajar</div>
                        </div>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'surat_perjanjian_tubel']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            Lihat
                        </a>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'surat_perjanjian_tubel']) }}?download=1"
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
                        @if(isset($usulan->data_usulan['dokumen_usulan']['surat_perjanjian_tubel']['path']))
                            Ganti Dokumen
                        @else
                            Upload Dokumen
                        @endif
                    </label>
                    <input type="file" id="surat_perjanjian_tubel" name="surat_perjanjian_tubel" accept=".pdf"
                           class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('surat_perjanjian_tubel') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                    @error('surat_perjanjian_tubel')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif
        </div>

        {{-- Surat Jaminan Pembiayaan Tugas Belajar --}}
        <div>
            <label class="block text-sm font-semibold text-gray-800 mb-2">
                Surat Jaminan Pembiayaan Tugas Belajar <span class="text-red-500">*</span>
            </label>
            <p class="text-xs text-gray-600 mb-2">
                Terperinci 6 komponen sesuai Persesjen Nomor 3 Tahun 2023 dan pembiayaan dari awal hingga selesai studi (PDF, maksimal 1MB)
            </p>

            @if(isset($usulan->data_usulan['dokumen_usulan']['surat_jaminan_pembiayaan']['path']))
                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                        <span class="text-sm text-blue-800">Surat Jaminan Pembiayaan sudah diupload</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                        <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-800">Surat Jaminan Pembiayaan Tugas Belajar</div>
                        </div>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'surat_jaminan_pembiayaan']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            Lihat
                        </a>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'surat_jaminan_pembiayaan']) }}?download=1"
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
                        @if(isset($usulan->data_usulan['dokumen_usulan']['surat_jaminan_pembiayaan']['path']))
                            Ganti Dokumen
                        @else
                            Upload Dokumen
                        @endif
                    </label>
                    <input type="file" id="surat_jaminan_pembiayaan" name="surat_jaminan_pembiayaan" accept=".pdf"
                           class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('surat_jaminan_pembiayaan') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                    @error('surat_jaminan_pembiayaan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif
        </div>

        {{-- Surat Keterangan dari Pimpinan Unit Kerja --}}
        <div>
            <label class="block text-sm font-semibold text-gray-800 mb-2">
                Surat Keterangan dari Pimpinan Unit Kerja <span class="text-red-500">*</span>
            </label>
            <p class="text-xs text-gray-600 mb-2">
                Mengenai bidang studi yang akan ditempuh mempunyai hubungan atau sesuai dengan kebutuhan dan pengembangan organisasi (PDF, maksimal 1MB)
            </p>

            @if(isset($usulan->data_usulan['dokumen_usulan']['surat_keterangan_pimpinan']['path']))
                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                        <span class="text-sm text-blue-800">Surat Keterangan Pimpinan sudah diupload</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                        <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-800">Surat Keterangan dari Pimpinan Unit Kerja</div>
                        </div>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'surat_keterangan_pimpinan']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            Lihat
                        </a>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'surat_keterangan_pimpinan']) }}?download=1"
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
                        @if(isset($usulan->data_usulan['dokumen_usulan']['surat_keterangan_pimpinan']['path']))
                            Ganti Dokumen
                        @else
                            Upload Dokumen
                        @endif
                    </label>
                    <input type="file" id="surat_keterangan_pimpinan" name="surat_keterangan_pimpinan" accept=".pdf"
                           class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('surat_keterangan_pimpinan') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                    @error('surat_keterangan_pimpinan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif
        </div>

        {{-- Surat Hasil Kelulusan (LoA) --}}
        <div>
            <label class="block text-sm font-semibold text-gray-800 mb-2">
                Surat Hasil Kelulusan dari Lembaga Pendidikan (LoA) <span class="text-red-500">*</span>
            </label>
            <p class="text-xs text-gray-600 mb-2">Letter of Acceptance dari lembaga pendidikan yang dituju (PDF, maksimal 1MB)</p>

            @if(isset($usulan->data_usulan['dokumen_usulan']['surat_hasil_kelulusan']['path']))
                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                        <span class="text-sm text-blue-800">Surat Hasil Kelulusan (LoA) sudah diupload</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                        <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-800">Surat Hasil Kelulusan dari Lembaga Pendidikan (LoA)</div>
                        </div>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'surat_hasil_kelulusan']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            Lihat
                        </a>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'surat_hasil_kelulusan']) }}?download=1"
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
                        @if(isset($usulan->data_usulan['dokumen_usulan']['surat_hasil_kelulusan']['path']))
                            Ganti Dokumen
                        @else
                            Upload Dokumen
                        @endif
                    </label>
                    <input type="file" id="surat_hasil_kelulusan" name="surat_hasil_kelulusan" accept=".pdf"
                           class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('surat_hasil_kelulusan') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                    @error('surat_hasil_kelulusan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif
        </div>

        {{-- Surat Pernyataan dari Pimpinan Unit Kerja (10 Poin) --}}
        <div>
            <label class="block text-sm font-semibold text-gray-800 mb-2">
                Surat Pernyataan dari Pimpinan Unit Kerja (10 Poin) <span class="text-red-500">*</span>
            </label>
            <p class="text-xs text-gray-600 mb-2">Surat pernyataan dari pimpinan unit kerja (PDF, maksimal 1MB)</p>

            @if(isset($usulan->data_usulan['dokumen_usulan']['surat_pernyataan_pimpinan']['path']))
                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                        <span class="text-sm text-blue-800">Surat Pernyataan Pimpinan (10 Poin) sudah diupload</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                        <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-800">Surat Pernyataan dari Pimpinan Unit Kerja (10 Poin)</div>
                        </div>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'surat_pernyataan_pimpinan']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            Lihat
                        </a>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'surat_pernyataan_pimpinan']) }}?download=1"
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
                    <input type="file" id="surat_pernyataan_pimpinan" name="surat_pernyataan_pimpinan" accept=".pdf"
                           class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('surat_pernyataan_pimpinan') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                    @error('surat_pernyataan_pimpinan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif
        </div>

        {{-- Asli Surat Pernyataan yang Bersangkutan (3 Poin) --}}
        <div>
            <label class="block text-sm font-semibold text-gray-800 mb-2">
                Asli Surat Pernyataan yang Bersangkutan (3 Poin) <span class="text-red-500">*</span>
            </label>
            <p class="text-xs text-gray-600 mb-2">Asli surat pernyataan yang bersangkutan (PDF, maksimal 1MB)</p>

            @if(isset($usulan->data_usulan['dokumen_usulan']['surat_pernyataan_bersangkutan']['path']))
                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                        <span class="text-sm text-blue-800">Surat Pernyataan Bersangkutan (3 Poin) sudah diupload</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                        <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-800">Asli Surat Pernyataan yang Bersangkutan (3 Poin)</div>
                        </div>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'surat_pernyataan_bersangkutan']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            Lihat
                        </a>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'surat_pernyataan_bersangkutan']) }}?download=1"
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
                        @if(isset($usulan->data_usulan['dokumen_usulan']['surat_pernyataan_bersangkutan']['path']))
                            Ganti Dokumen
                        @else
                            Upload Dokumen
                        @endif
                    </label>
                    <input type="file" id="surat_pernyataan_bersangkutan" name="surat_pernyataan_bersangkutan" accept=".pdf"
                           class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('surat_pernyataan_bersangkutan') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                    @error('surat_pernyataan_bersangkutan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif
        </div>

        {{-- Dokumen Akreditasi Prodi dan PT --}}
        <div>
            <label class="block text-sm font-semibold text-gray-800 mb-2">
                Dokumen Akreditasi Prodi dan PT <span class="text-red-500">*</span>
            </label>
            <p class="text-xs text-gray-600 mb-2">
                Dokumen akreditasi prodi dan PT / Tangkap layar daftar PTLN pada laman Ditjen Diktiristek (PDF, maksimal 1MB)
            </p>

            @if(isset($usulan->data_usulan['dokumen_usulan']['dokumen_akreditasi']['path']))
                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                        <span class="text-sm text-blue-800">Dokumen Akreditasi Prodi dan PT sudah diupload</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                        <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-800">Dokumen Akreditasi Prodi dan PT</div>
                        </div>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'dokumen_akreditasi']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            Lihat
                        </a>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'dokumen_akreditasi']) }}?download=1"
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
                        @if(isset($usulan->data_usulan['dokumen_usulan']['dokumen_akreditasi']['path']))
                            Ganti Dokumen
                        @else
                            Upload Dokumen
                        @endif
                    </label>
                    <input type="file" id="dokumen_akreditasi" name="dokumen_akreditasi" accept=".pdf"
                           class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('dokumen_akreditasi') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                    @error('dokumen_akreditasi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // File size validation for surat tunjangan keluarga
    const suratTunjanganInput = document.getElementById('surat_tunjangan_keluarga');
    suratTunjanganInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Check file size (1MB = 1024 * 1024 bytes)
            if (file.size > 1024 * 1024) {
                alert('Ukuran file maksimal 1MB');
                this.value = '';
                return;
            }

            // Check file type
            if (file.type !== 'application/pdf') {
                alert('File harus berformat PDF');
                this.value = '';
                return;
            }
        }
    });

    // File size validation for akta nikah
    const aktaNikahInput = document.getElementById('akta_nikah');
    aktaNikahInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Check file size (1MB = 1024 * 1024 bytes)
            if (file.size > 1024 * 1024) {
                alert('Ukuran file maksimal 1MB');
                this.value = '';
                return;
            }

            // Check file type
            if (file.type !== 'application/pdf') {
                alert('File harus berformat PDF');
                this.value = '';
                return;
            }
        }
    });

    // File size validation for surat rekomendasi atasan
    const suratRekomendasiInput = document.getElementById('surat_rekomendasi_atasan');
    suratRekomendasiInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 1024 * 1024) {
                alert('Ukuran file maksimal 1MB');
                this.value = '';
                return;
            }
            if (file.type !== 'application/pdf') {
                alert('File harus berformat PDF');
                this.value = '';
                return;
            }
        }
    });

    // File size validation for surat perjanjian tubel
    const suratPerjanjianInput = document.getElementById('surat_perjanjian_tubel');
    suratPerjanjianInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 1024 * 1024) {
                alert('Ukuran file maksimal 1MB');
                this.value = '';
                return;
            }
            if (file.type !== 'application/pdf') {
                alert('File harus berformat PDF');
                this.value = '';
                return;
            }
        }
    });

    // File size validation for surat jaminan pembiayaan
    const suratJaminanInput = document.getElementById('surat_jaminan_pembiayaan');
    suratJaminanInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 1024 * 1024) {
                alert('Ukuran file maksimal 1MB');
                this.value = '';
                return;
            }
            if (file.type !== 'application/pdf') {
                alert('File harus berformat PDF');
                this.value = '';
                return;
            }
        }
    });

    // File size validation for surat keterangan pimpinan
    const suratKeteranganInput = document.getElementById('surat_keterangan_pimpinan');
    suratKeteranganInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 1024 * 1024) {
                alert('Ukuran file maksimal 1MB');
                this.value = '';
                return;
            }
            if (file.type !== 'application/pdf') {
                alert('File harus berformat PDF');
                this.value = '';
                return;
            }
        }
    });

    // File size validation for surat hasil kelulusan
    const suratKelulusanInput = document.getElementById('surat_hasil_kelulusan');
    suratKelulusanInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 1024 * 1024) {
                alert('Ukuran file maksimal 1MB');
                this.value = '';
                return;
            }
            if (file.type !== 'application/pdf') {
                alert('File harus berformat PDF');
                this.value = '';
                return;
            }
        }
    });

    // File size validation for surat pernyataan pimpinan
    const suratPernyataanPimpinanInput = document.getElementById('surat_pernyataan_pimpinan');
    suratPernyataanPimpinanInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 1024 * 1024) {
                alert('Ukuran file maksimal 1MB');
                this.value = '';
                return;
            }
            if (file.type !== 'application/pdf') {
                alert('File harus berformat PDF');
                this.value = '';
                return;
            }
        }
    });

    // File size validation for surat pernyataan bersangkutan
    const suratPernyataanBersangkutanInput = document.getElementById('surat_pernyataan_bersangkutan');
    suratPernyataanBersangkutanInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 1024 * 1024) {
                alert('Ukuran file maksimal 1MB');
                this.value = '';
                return;
            }
            if (file.type !== 'application/pdf') {
                alert('File harus berformat PDF');
                this.value = '';
                return;
            }
        }
    });

    // File size validation for dokumen akreditasi
    const dokumenAkreditasiInput = document.getElementById('dokumen_akreditasi');
    dokumenAkreditasiInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 1024 * 1024) {
                alert('Ukuran file maksimal 1MB');
                this.value = '';
                return;
            }
            if (file.type !== 'application/pdf') {
                alert('File harus berformat PDF');
                this.value = '';
                return;
            }
        }
    });
});
</script>
