{{-- Forward Form Component - Form untuk mengirim usulan ke universitas --}}
<div id="forwardForm" class="hidden mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
    <h4 class="font-medium text-green-800 mb-3 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
            </path>
        </svg>
        Lengkapi Dokumen untuk Mengirim ke Universitas
    </h4>

    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800">
        <p>
            <strong>Info:</strong>
            Pastikan validasi sudah tersimpan sebelum mengirim.
            Usulan akan diteruskan ke tingkat universitas untuk review lebih lanjut.
        </p>
    </div>

    <div id="forwardUsulanForm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            {{-- Nomor Surat Usulan --}}
            <div>
                <label for="nomor_surat_usulan" class="block text-sm font-medium text-gray-700 mb-2">
                    Nomor Surat Usulan Pimpinan Unit Kerja <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="nomor_surat_usulan"
                       name="nomor_surat_usulan"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500"
                       required
                       placeholder="Contoh: 001/FK-UNMUL/2025">
                <p class="text-xs text-gray-500 mt-1">
                    Format: Nomor/Unit/Tahun
                </p>
            </div>

            {{-- Upload Surat Usulan --}}
            <div>
                <label for="file_surat_usulan" class="block text-sm font-medium text-gray-700 mb-2">
                    Upload Dokumen Surat Usulan <span class="text-red-500">*</span>
                </label>
                <input type="file"
                       id="file_surat_usulan"
                       name="file_surat_usulan"
                       class="w-full border-gray-300 rounded-lg file:mr-4 file:py-2 file:px-4
                              file:rounded-lg file:border-0 file:text-sm file:font-semibold
                              file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
                       accept=".pdf"
                       required>
                <p class="text-xs text-gray-500 mt-1">
                    File PDF maksimal 2MB
                </p>
            </div>

            {{-- Nomor Berita Acara Senat --}}
            <div>
                <label for="nomor_berita_senat" class="block text-sm font-medium text-gray-700 mb-2">
                    Nomor Berita Acara Senat <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="nomor_berita_senat"
                       name="nomor_berita_senat"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500"
                       required
                       placeholder="Contoh: 002/SENAT-FK/2025">
                <p class="text-xs text-gray-500 mt-1">
                    Format: Nomor/SENAT-Unit/Tahun
                </p>
            </div>

            {{-- Upload Berita Acara Senat --}}
            <div>
                <label for="file_berita_senat" class="block text-sm font-medium text-gray-700 mb-2">
                    Upload Berita Acara & Daftar Hadir Senat <span class="text-red-500">*</span>
                </label>
                <input type="file"
                       id="file_berita_senat"
                       name="file_berita_senat"
                       class="w-full border-gray-300 rounded-lg file:mr-4 file:py-2 file:px-4
                              file:rounded-lg file:border-0 file:text-sm file:font-semibold
                              file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
                       accept=".pdf"
                       required>
                <p class="text-xs text-gray-500 mt-1">
                    Upload 1 file PDF yang berisi berita acara senat dan daftar hadir (maksimal 5MB)
                </p>
            </div>
        </div>

        {{-- Additional Notes (Optional) --}}
        <div class="mb-6">
            <label for="catatan_forward" class="block text-sm font-medium text-gray-700 mb-2">
                Catatan Tambahan (Opsional)
            </label>
            <textarea id="catatan_forward"
                      name="catatan_forward"
                      rows="3"
                      class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500"
                      placeholder="Tambahkan catatan jika diperlukan..."></textarea>
        </div>

        <div class="flex gap-3">
            <button type="button"
                    onclick="submitForwardForm()"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700
                           focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8">
                    </path>
                </svg>
                Konfirmasi Kirim ke Universitas
            </button>

            <button type="button"
                    onclick="hideForwardForm()"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                Batal
            </button>
        </div>
    </div>
</div>
