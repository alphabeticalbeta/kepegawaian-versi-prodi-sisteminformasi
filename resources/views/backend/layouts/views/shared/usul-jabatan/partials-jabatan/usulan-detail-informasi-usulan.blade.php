{{-- Informasi Usulan --}}
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5">
        <h2 class="text-xl font-bold text-white flex items-center">
            <i data-lucide="info" class="w-6 h-6 mr-3"></i>
            Informasi Usulan
        </h2>
    </div>
    <div class="p-6">
        {{-- Baris Pertama: Pegawai, Periode, Jenis Usulan --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label class="block text-sm font-semibold text-gray-800">Pegawai</label>
                <p class="text-xs text-gray-600 mb-2">Nama pegawai pengusul</p>
                <input type="text" value="{{ $usulan->pegawai->nama_lengkap ?? '-' }}"
                       class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-800">Periode</label>
                <p class="text-xs text-gray-600 mb-2">Periode usulan</p>
                <input type="text" value="{{ $usulan->periodeUsulan->nama_periode ?? '-' }}"
                       class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-800">Jenis Usulan</label>
                <p class="text-xs text-gray-600 mb-2">Jenis usulan yang diajukan</p>
                <input type="text" value="{{ \App\Helpers\UsulanHelper::formatJenisUsulan($usulan->jenis_usulan) ?? '-' }}"
                       class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
            </div>
        </div>

        {{-- Baris Kedua: Jabatan Saat Ini, Jabatan Tujuan, Unit Kerja --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label class="block text-sm font-semibold text-gray-800">Jabatan Saat Ini</label>
                <p class="text-xs text-gray-600 mb-2">Jabatan yang sedang diemban</p>
                <input type="text" value="{{ $usulan->jabatanLama->jabatan ?? '-' }}"
                       class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-800">Jabatan Tujuan</label>
                <p class="text-xs text-gray-600 mb-2">Jabatan yang diusulkan</p>
                <input type="text" value="{{ $usulan->jabatanTujuan->jabatan ?? '-' }}"
                       class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-800">Unit Kerja</label>
                <p class="text-xs text-gray-600 mb-2">Jurusan / Prodi</p>
                <input type="text" value="{{ $usulan->pegawai->unitKerja->nama ?? '-' }}"
                       class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
            </div>
        </div>

        {{-- Baris Ketiga: Unit Kerja Induk --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-800">Unit Kerja Induk</label>
                <p class="text-xs text-gray-600 mb-2">Unit Kerja Induk (Fakultas)</p>
                <input type="text" value="{{ $usulan->pegawai->unitKerja->subUnitKerja->unitKerja->nama ?? '-' }}"
                       class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
            </div>
            <div class="md:col-span-2">
                {{-- Kolom kosong untuk menjaga alignment --}}
            </div>
        </div>
    </div>
</div>
