{{-- Section Dokumen Perpanjangan Tugas Belajar --}}
        {{-- Surat Perjanjian Perpanjangan Pemberian Tugas Belajar --}}
        <div>
            <label class="block text-sm font-semibold text-gray-800 mb-2">
                Surat Perjanjian Perpanjangan Pemberian Tugas Belajar <span class="text-red-500">*</span>
            </label>
            <p class="text-xs text-gray-600 mb-2">Surat perjanjian perpanjangan pemberian tugas belajar (PDF, maksimal 1MB)</p>

            @if(isset($usulan->data_usulan['dokumen_usulan']['surat_perjanjian_perpanjangan']['path']))
                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                        <span class="text-sm text-blue-800">Surat Perjanjian Perpanjangan sudah diupload</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                        <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-800">Surat Perjanjian Perpanjangan Pemberian Tugas Belajar</div>
                        </div>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'surat_perjanjian_perpanjangan']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            Lihat
                        </a>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'surat_perjanjian_perpanjangan']) }}?download=1"
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
                        @if(isset($usulan->data_usulan['dokumen_usulan']['surat_perjanjian_perpanjangan']['path']))
                            Ganti Dokumen
                        @else
                            Upload Dokumen
                        @endif
                    </label>
                    <input type="file" id="surat_perjanjian_perpanjangan" name="surat_perjanjian_perpanjangan" accept=".pdf"
                           class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('surat_perjanjian_perpanjangan') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                    @error('surat_perjanjian_perpanjangan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif
        </div>

        {{-- Surat Perpanjangan Jaminan Pembiayaan Tugas Belajar --}}
        <div>
            <label class="block text-sm font-semibold text-gray-800 mb-2">
                Surat Perpanjangan Jaminan Pembiayaan Tugas Belajar <span class="text-red-500">*</span>
            </label>
            <p class="text-xs text-gray-600 mb-2">
                Terperinci 6 komponen sesuai Persesjen Nomor 3 Tahun 2023 dan pembiayaan dari awal hingga selesai studi dan tidak pertahun (PDF, maksimal 1MB)
            </p>

            @if(isset($usulan->data_usulan['dokumen_usulan']['surat_perpanjangan_jaminan_pembiayaan']['path']))
                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                        <span class="text-sm text-blue-800">Surat Perpanjangan Jaminan Pembiayaan sudah diupload</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                        <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-800">Surat Perpanjangan Jaminan Pembiayaan Tugas Belajar</div>
                        </div>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'surat_perpanjangan_jaminan_pembiayaan']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            Lihat
                        </a>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'surat_perpanjangan_jaminan_pembiayaan']) }}?download=1"
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
                        @if(isset($usulan->data_usulan['dokumen_usulan']['surat_perpanjangan_jaminan_pembiayaan']['path']))
                            Ganti Dokumen
                        @else
                            Upload Dokumen
                        @endif
                    </label>
                    <input type="file" id="surat_perpanjangan_jaminan_pembiayaan" name="surat_perpanjangan_jaminan_pembiayaan" accept=".pdf"
                           class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('surat_perpanjangan_jaminan_pembiayaan') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                    @error('surat_perpanjangan_jaminan_pembiayaan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif
        </div>

        {{-- Surat Rekomendasi Perpanjangan Pemberian Tugas Belajar dari Lembaga Pendidikan --}}
        <div>
            <label class="block text-sm font-semibold text-gray-800 mb-2">
                Surat Rekomendasi Perpanjangan Pemberian Tugas Belajar dari Lembaga Pendidikan <span class="text-red-500">*</span>
            </label>
            <p class="text-xs text-gray-600 mb-2">
                Surat rekomendasi perpanjangan pemberian tugas belajar dari lembaga pendidikan tempat melaksanakan tugas belajar (PDF, maksimal 1MB)
            </p>

            @if(isset($usulan->data_usulan['dokumen_usulan']['surat_rekomendasi_lembaga_pendidikan']['path']))
                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                        <span class="text-sm text-blue-800">Surat Rekomendasi Lembaga Pendidikan sudah diupload</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                        <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-800">Surat Rekomendasi Perpanjangan dari Lembaga Pendidikan</div>
                        </div>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'surat_rekomendasi_lembaga_pendidikan']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            Lihat
                        </a>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'surat_rekomendasi_lembaga_pendidikan']) }}?download=1"
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
                        @if(isset($usulan->data_usulan['dokumen_usulan']['surat_rekomendasi_lembaga_pendidikan']['path']))
                            Ganti Dokumen
                        @else
                            Upload Dokumen
                        @endif
                    </label>
                    <input type="file" id="surat_rekomendasi_lembaga_pendidikan" name="surat_rekomendasi_lembaga_pendidikan" accept=".pdf"
                           class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('surat_rekomendasi_lembaga_pendidikan') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                    @error('surat_rekomendasi_lembaga_pendidikan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif
        </div>

        {{-- Surat Rekomendasi Perpanjangan Tugas Belajar dari Pimpinan Unit Kerja --}}
        <div>
            <label class="block text-sm font-semibold text-gray-800 mb-2">
                Surat Rekomendasi Perpanjangan Tugas Belajar dari Pimpinan Unit Kerja <span class="text-red-500">*</span>
            </label>
            <p class="text-xs text-gray-600 mb-2">
                Surat rekomendasi perpanjangan tugas belajar dari pimpinan unit kerja (PDF, maksimal 1MB)
            </p>

            @if(isset($usulan->data_usulan['dokumen_usulan']['surat_rekomendasi_pimpinan_unit']['path']))
                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                        <span class="text-sm text-blue-800">Surat Rekomendasi Pimpinan Unit sudah diupload</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                        <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-800">Surat Rekomendasi Perpanjangan dari Pimpinan Unit Kerja</div>
                        </div>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'surat_rekomendasi_pimpinan_unit']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            Lihat
                        </a>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'surat_rekomendasi_pimpinan_unit']) }}?download=1"
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
                        @if(isset($usulan->data_usulan['dokumen_usulan']['surat_rekomendasi_pimpinan_unit']['path']))
                            Ganti Dokumen
                        @else
                            Upload Dokumen
                        @endif
                    </label>
                    <input type="file" id="surat_rekomendasi_pimpinan_unit" name="surat_rekomendasi_pimpinan_unit" accept=".pdf"
                           class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('surat_rekomendasi_pimpinan_unit') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                    @error('surat_rekomendasi_pimpinan_unit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif
        </div>

        {{-- SK Tugas Belajar --}}
        <div>
            <label class="block text-sm font-semibold text-gray-800 mb-2">
                SK Tugas Belajar <span class="text-red-500">*</span>
            </label>
            <p class="text-xs text-gray-600 mb-2">Surat Keputusan Tugas Belajar (PDF, maksimal 1MB)</p>

            @if(isset($usulan->data_usulan['dokumen_usulan']['sk_tugas_belajar']['path']))
                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                        <span class="text-sm text-blue-800">SK Tugas Belajar sudah diupload</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                        <i data-lucide="file-pdf" class="w-5 h-5 text-red-600"></i>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-800">SK Tugas Belajar</div>
                        </div>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'sk_tugas_belajar']) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            Lihat
                        </a>
                        <a href="{{ route('pegawai-unmul.usulan-tugas-belajar.show-document', [$usulan, 'sk_tugas_belajar']) }}?download=1"
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
                        @if(isset($usulan->data_usulan['dokumen_usulan']['sk_tugas_belajar']['path']))
                            Ganti Dokumen
                        @else
                            Upload Dokumen
                        @endif
                    </label>
                    <input type="file" id="sk_tugas_belajar" name="sk_tugas_belajar" accept=".pdf"
                           class="block w-full border-gray-300 rounded-lg shadow-sm bg-white px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('sk_tugas_belajar') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Format: PDF (Max: 1MB)</p>
                    @error('sk_tugas_belajar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif
        </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // File size validation for surat perjanjian perpanjangan
    const suratPerjanjianPerpanjanganInput = document.getElementById('surat_perjanjian_perpanjangan');
    suratPerjanjianPerpanjanganInput.addEventListener('change', function(e) {
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

    // File size validation for surat perpanjangan jaminan pembiayaan
    const suratPerpanjanganJaminanInput = document.getElementById('surat_perpanjangan_jaminan_pembiayaan');
    suratPerpanjanganJaminanInput.addEventListener('change', function(e) {
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

    // File size validation for surat rekomendasi lembaga pendidikan
    const suratRekomendasiLembagaInput = document.getElementById('surat_rekomendasi_lembaga_pendidikan');
    suratRekomendasiLembagaInput.addEventListener('change', function(e) {
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

    // File size validation for surat rekomendasi pimpinan unit
    const suratRekomendasiPimpinanInput = document.getElementById('surat_rekomendasi_pimpinan_unit');
    suratRekomendasiPimpinanInput.addEventListener('change', function(e) {
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

    // File size validation for SK tugas belajar
    const skTugasBelajarInput = document.getElementById('sk_tugas_belajar');
    skTugasBelajarInput.addEventListener('change', function(e) {
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
