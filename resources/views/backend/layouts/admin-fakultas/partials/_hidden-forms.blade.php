{{-- resources/views/backend/layouts/admin-fakultas/partials/_hidden-forms.blade.php --}}
{{-- Hidden forms untuk berbagai aksi Admin Fakultas --}}

{{-- Form Perbaikan Usulan (Ke Pegawai) --}}
<div id="returnForm" class="hidden mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
    <h4 class="font-medium text-red-800 mb-3 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
        </svg>
        Perbaikan Usulan - Kembalikan ke Pegawai
    </h4>
    <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-800">
        <p><strong>Perhatian:</strong> Usulan akan dikembalikan dengan status "Perlu Perbaikan". Pegawai dapat memperbaiki dan mengirim ulang usulannya.</p>
    </div>

    <div id="returnUsulanForm">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Catatan untuk Pegawai <span class="text-red-500">*</span>
            </label>
            <textarea name="catatan_umum"
                      rows="4"
                      class="w-full border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500"
                      placeholder="Berikan instruksi yang jelas kepada pegawai tentang apa yang perlu diperbaiki..."
                      required></textarea>
            <p class="text-xs text-gray-500 mt-1">
                Catatan ini akan dikirim ke pegawai. Item detail yang tidak sesuai dari validasi akan otomatis disertakan.
            </p>
        </div>

        <div class="flex gap-3">
            <button type="button"
                    onclick="submitReturnForm()"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"></path>
                </svg>
                Konfirmasi Kembalikan
            </button>
            <button type="button"
                    onclick="hideReturnForm()"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                Batal
            </button>
        </div>
    </div>
</div>

{{-- Form Belum Direkomendasikan (Ke Pegawai) --}}
<div id="rejectForm" class="hidden mt-6 p-4 bg-orange-50 border border-orange-200 rounded-lg">
    <h4 class="font-medium text-orange-800 mb-3 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Belum Direkomendasikan - Kembalikan ke Pegawai
    </h4>
    <div class="mb-4 p-3 bg-orange-100 border border-orange-300 rounded-lg text-sm text-orange-800">
        <p><strong>Info:</strong> Usulan akan dikembalikan dengan status "Belum Direkomendasikan". Pegawai dapat memperbaiki dan mengirim ulang usulannya.</p>
    </div>

    <div id="rejectUsulanForm">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Alasan Belum Direkomendasikan <span class="text-red-500">*</span>
            </label>
            <textarea name="catatan_reject"
                      rows="4"
                      class="w-full border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500"
                      placeholder="Jelaskan alasan mengapa usulan belum dapat direkomendasikan..."
                      required></textarea>
        </div>

        <div class="flex gap-3">
            <button type="button"
                    onclick="submitRejectForm()"
                    class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                Konfirmasi Belum Direkomendasikan
            </button>
            <button type="button"
                    onclick="hideRejectForm()"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                Batal
            </button>
        </div>
    </div>
</div>

{{-- Form Direkomendasikan (Ke Admin Universitas) --}}
<div id="forwardForm" class="hidden mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
    <h4 class="font-medium text-green-800 mb-3 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        Lengkapi Dokumen untuk Mengirim ke Admin Universitas
    </h4>
    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800">
        <p><strong>Info:</strong> Pastikan validasi sudah tersimpan sebelum mengirim. Usulan akan diteruskan ke Admin Universitas.</p>
    </div>

    <div id="forwardUsulanForm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Surat Usulan Pimpinan Unit Kerja *</label>
                <input type="text" name="nomor_surat_usulan" class="w-full border-gray-300 rounded-lg shadow-sm" required
                       placeholder="Contoh: 001/FK-UNMUL/2025">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Dokumen Surat Usulan *</label>
                <input type="file" name="file_surat_usulan" class="w-full border-gray-300 rounded-lg" accept=".pdf" required>
                <p class="text-xs text-gray-500 mt-1">File PDF maksimal 2MB</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Berita Acara Senat *</label>
                <input type="text" name="nomor_berita_senat" class="w-full border-gray-300 rounded-lg shadow-sm" required
                       placeholder="Contoh: 002/SENAT-FK/2025">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Berita Acara & Daftar Hadir Senat *</label>
                <input type="file" name="file_berita_senat" class="w-full border-gray-300 rounded-lg" accept=".pdf" required>
                <p class="text-xs text-gray-500 mt-1">Upload 1 file PDF yang berisi berita acara senat dan daftar hadir (maksimal 5MB)</p>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="button"
                    onclick="submitForwardForm()"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                Konfirmasi Kirim ke Admin Universitas
            </button>
            <button type="button"
                    onclick="hideForwardForm()"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                Batal
            </button>
        </div>
    </div>
</div>