{{-- Return Form Component - Form untuk mengembalikan usulan ke pegawai --}}
<div id="returnForm" class="hidden mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
    <h4 class="font-medium text-red-800 mb-3 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
            </path>
        </svg>
        Kembalikan Usulan ke Pegawai
    </h4>

    <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-800">
        <p>
            <strong>Perhatian:</strong>
            Usulan akan dikembalikan dengan status "Perlu Perbaikan".
            Pegawai dapat memperbaiki dan mengirim ulang usulannya.
        </p>
    </div>

    <div id="returnUsulanForm">
        <div class="mb-4">
            <label for="catatan_umum_return" class="block text-sm font-medium text-gray-700 mb-2">
                Catatan untuk Pegawai <span class="text-red-500">*</span>
            </label>
            <textarea id="catatan_umum_return"
                      name="catatan_umum"
                      rows="4"
                      class="w-full border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500"
                      placeholder="Berikan instruksi yang jelas kepada pegawai tentang apa yang perlu diperbaiki..."
                      required></textarea>
            <p class="text-xs text-gray-500 mt-1">
                Catatan ini akan dikirim ke pegawai. Item detail yang tidak sesuai dari validasi akan otomatis disertakan.
            </p>
        </div>

        {{-- Summary of validation issues --}}
        <div id="validationIssueSummary" class="mb-4 p-3 bg-white border border-gray-200 rounded-lg hidden">
            <h5 class="text-sm font-semibold text-gray-700 mb-2">Item yang Tidak Sesuai:</h5>
            <ul id="issueList" class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                {{-- Will be populated by JavaScript --}}
            </ul>
        </div>

        <div class="flex gap-3">
            <button type="button"
                    onclick="submitReturnForm()"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700
                           focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01">
                    </path>
                </svg>
                Konfirmasi Kembalikan
            </button>

            <button type="button"
                    onclick="hideReturnForm()"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                Batal
            </button>
        </div>
    </div>
</div>
