{{-- Section Dokumen Pegawai --}}
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-teal-600 to-cyan-600 px-6 py-5">
        <h2 class="text-xl font-bold text-white flex items-center">
            <i data-lucide="folder" class="w-6 h-6 mr-3"></i>
            Dokumen Pegawai
        </h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-800">SK CPNS</label>
                <p class="text-xs text-gray-600 mb-2">Surat Keputusan CPNS</p>
                @if($usulan->pegawai->sk_cpns)
                    <a href="{{ route('pegawai-unmul.profile.show-document', 'sk_cpns') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        Lihat Dokumen
                    </a>
                @else
                    <span class="inline-flex items-center gap-2 px-4 py-3 bg-gray-50 text-gray-500 rounded-lg">
                        <i data-lucide="file-x" class="w-4 h-4"></i>
                        Belum diupload
                    </span>
                @endif
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-800">SK PNS</label>
                <p class="text-xs text-gray-600 mb-2">Surat Keputusan PNS</p>
                @if($usulan->pegawai->sk_pns)
                    <a href="{{ route('pegawai-unmul.profile.show-document', 'sk_pns') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        Lihat Dokumen
                    </a>
                @else
                    <span class="inline-flex items-center gap-2 px-4 py-3 bg-gray-50 text-gray-500 rounded-lg">
                        <i data-lucide="file-x" class="w-4 h-4"></i>
                        Belum diupload
                    </span>
                @endif
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-800">SK Pangkat Terakhir</label>
                <p class="text-xs text-gray-600 mb-2">Surat Keputusan Pangkat Terakhir</p>
                @if($usulan->pegawai->sk_pangkat_terakhir)
                    <a href="{{ route('pegawai-unmul.profile.show-document', 'sk_pangkat_terakhir') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        Lihat Dokumen
                    </a>
                @else
                    <span class="inline-flex items-center gap-2 px-4 py-3 bg-gray-50 text-gray-500 rounded-lg">
                        <i data-lucide="file-x" class="w-4 h-4"></i>
                        Belum diupload
                    </span>
                @endif
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-800">SK Jabatan Terakhir</label>
                <p class="text-xs text-gray-600 mb-2">Surat Keputusan Jabatan Terakhir</p>
                @if($usulan->pegawai->sk_jabatan_terakhir)
                    <a href="{{ route('pegawai-unmul.profile.show-document', 'sk_jabatan_terakhir') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        Lihat Dokumen
                    </a>
                @else
                    <span class="inline-flex items-center gap-2 px-4 py-3 bg-gray-50 text-gray-500 rounded-lg">
                        <i data-lucide="file-x" class="w-4 h-4"></i>
                        Belum diupload
                    </span>
                @endif
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-800">Ijazah Terakhir</label>
                <p class="text-xs text-gray-600 mb-2">Ijazah pendidikan terakhir</p>
                @if($usulan->pegawai->ijazah_terakhir)
                    <a href="{{ route('pegawai-unmul.profile.show-document', 'ijazah_terakhir') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        Lihat Dokumen
                    </a>
                @else
                    <span class="inline-flex items-center gap-2 px-4 py-3 bg-gray-50 text-gray-500 rounded-lg">
                        <i data-lucide="file-x" class="w-4 h-4"></i>
                        Belum diupload
                    </span>
                @endif
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-800">Transkrip Nilai Terakhir</label>
                <p class="text-xs text-gray-600 mb-2">Transkrip nilai pendidikan terakhir</p>
                @if($usulan->pegawai->transkrip_nilai_terakhir)
                    <a href="{{ route('pegawai-unmul.profile.show-document', 'transkrip_nilai_terakhir') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        Lihat Dokumen
                    </a>
                @else
                    <span class="inline-flex items-center gap-2 px-4 py-3 bg-gray-50 text-gray-500 rounded-lg">
                        <i data-lucide="file-x" class="w-4 h-4"></i>
                        Belum diupload
                    </span>
                @endif
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-800">SKP Tahun {{ date('Y') - 1 }}</label>
                <p class="text-xs text-gray-600 mb-2">Sasaran Kinerja Pegawai tahun sebelumnya</p>
                @if($usulan->pegawai->skp_tahun_pertama)
                    <a href="{{ route('pegawai-unmul.profile.show-document', 'skp_tahun_pertama') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        Lihat Dokumen
                    </a>
                @else
                    <span class="inline-flex items-center gap-2 px-4 py-3 bg-gray-50 text-gray-500 rounded-lg">
                        <i data-lucide="file-x" class="w-4 h-4"></i>
                        Belum diupload
                    </span>
                @endif
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-800">SKP Tahun {{ date('Y') - 2 }}</label>
                <p class="text-xs text-gray-600 mb-2">Sasaran Kinerja Pegawai dua tahun sebelumnya</p>
                @if($usulan->pegawai->skp_tahun_kedua)
                    <a href="{{ route('pegawai-unmul.profile.show-document', 'skp_tahun_kedua') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        Lihat Dokumen
                    </a>
                @else
                    <span class="inline-flex items-center gap-2 px-4 py-3 bg-gray-50 text-gray-500 rounded-lg">
                        <i data-lucide="file-x" class="w-4 h-4"></i>
                        Belum diupload
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
