{{-- Dokumen Fakultas Input Form Component --}}
{{-- File: resources/views/backend/layouts/admin-fakultas/partials/_dokumen-fakultas-form.blade.php --}}

<div id="forwardForm" class="hidden mt-6 bg-white shadow-md rounded-lg overflow-hidden">
    <div class="bg-gradient-to-r from-green-600 to-blue-600 px-6 py-4">
        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
            </svg>
            Input Dokumen Fakultas
        </h3>
        <p class="text-green-100 text-sm mt-1">
            Lengkapi dokumen fakultas sebelum mengirim usulan ke universitas
        </p>
    </div>

    <form id="forwardUsulanForm" class="p-6 space-y-6">
        
        {{-- Informasi Requirements --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                    </path>
                </svg>
                <div>
                    <h4 class="font-medium text-blue-900 mb-1">Persyaratan Dokumen:</h4>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• Format file harus PDF</li>
                        <li>• Ukuran maksimal 1 MB per file</li>
                        <li>• Semua field wajib diisi</li>
                        <li>• Pastikan dokumen sudah ditandatangani dan disetujui</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Nomor Surat Usulan Fakultas --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="nomor_surat_usulan" class="block text-sm font-medium text-gray-700 mb-2">
                    Nomor Surat Usulan Fakultas <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="nomor_surat_usulan" 
                       name="nomor_surat_usulan" 
                       placeholder="Contoh: 123/UN17.1/KP/2024"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                       required>
                <p class="text-xs text-gray-500 mt-1">Masukkan nomor surat usulan yang dikeluarkan fakultas</p>
            </div>

            {{-- File Surat Usulan Fakultas --}}
            <div>
                <label for="file_surat_usulan" class="block text-sm font-medium text-gray-700 mb-2">
                    Dokumen Surat Usulan Fakultas <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="file" 
                           id="file_surat_usulan" 
                           name="file_surat_usulan" 
                           accept=".pdf"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
                           required>
                </div>
                <p class="text-xs text-gray-500 mt-1">Upload dokumen surat usulan (PDF, max 1MB)</p>
                <div id="file_surat_usulan_info" class="hidden mt-2 text-sm text-green-600"></div>
            </div>
        </div>

        {{-- Nomor Surat Senat --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="nomor_berita_senat" class="block text-sm font-medium text-gray-700 mb-2">
                    Nomor Surat Senat <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="nomor_berita_senat" 
                       name="nomor_berita_senat" 
                       placeholder="Contoh: 456/UN17.1/SN/2024"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                       required>
                <p class="text-xs text-gray-500 mt-1">Masukkan nomor surat keputusan senat fakultas</p>
            </div>

            {{-- File Surat Senat --}}
            <div>
                <label for="file_berita_senat" class="block text-sm font-medium text-gray-700 mb-2">
                    Dokumen Surat Senat <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="file" 
                           id="file_berita_senat" 
                           name="file_berita_senat" 
                           accept=".pdf"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
                           required>
                </div>
                <p class="text-xs text-gray-500 mt-1">Upload dokumen surat senat (PDF, max 1MB)</p>
                <div id="file_berita_senat_info" class="hidden mt-2 text-sm text-green-600"></div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
            <button type="button" 
                    onclick="hideForwardForm()" 
                    class="px-4 py-2 text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Batal
            </button>

            <div class="flex gap-3">
                {{-- Preview/Check Button --}}
                <button type="button" 
                        onclick="validateForwardForm()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                    Validasi Dokumen
                </button>

                {{-- Submit Button --}}
                <button type="button" 
                        onclick="submitForwardForm()" 
                        id="submitForwardBtn"
                        class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8">
                        </path>
                    </svg>
                    Kirim ke Universitas
                </button>
            </div>
        </div>

        {{-- Progress/Status Info --}}
        <div id="uploadProgress" class="hidden">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600"></div>
                    <span class="text-blue-800 font-medium">Mengunggah dokumen dan mengirim usulan...</span>
                </div>
                <div class="mt-2 w-full bg-blue-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" id="uploadProgressBar" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- JavaScript untuk Handling Form --}}
<script>
// File validation functions
function validateFile(input, maxSizeMB = 1) {
    if (input.files.length > 0) {
        const file = input.files[0];
        const maxSizeBytes = maxSizeMB * 1024 * 1024;
        const infoDiv = document.getElementById(input.id + '_info');

        // Reset classes
        input.classList.remove('border-red-500', 'border-green-500');
        infoDiv.classList.add('hidden');

        // Check file type
        if (!file.type.includes('pdf')) {
            input.classList.add('border-red-500');
            infoDiv.className = 'mt-2 text-sm text-red-600';
            infoDiv.textContent = '❌ File harus berformat PDF';
            infoDiv.classList.remove('hidden');
            input.value = '';
            return false;
        }

        // Check file size
        if (file.size > maxSizeBytes) {
            input.classList.add('border-red-500');
            infoDiv.className = 'mt-2 text-sm text-red-600';
            infoDiv.textContent = `❌ File terlalu besar. Maksimal ${maxSizeMB}MB. Ukuran saat ini: ${(file.size / (1024 * 1024)).toFixed(2)}MB`;
            infoDiv.classList.remove('hidden');
            input.value = '';
            return false;
        }

        // Success
        input.classList.add('border-green-500');
        infoDiv.className = 'mt-2 text-sm text-green-600';
        infoDiv.textContent = `✅ File valid (${(file.size / (1024 * 1024)).toFixed(2)}MB)`;
        infoDiv.classList.remove('hidden');
        return true;
    }
    return false;
}

function validateForwardForm() {
    const requiredFields = [
        { id: 'nomor_surat_usulan', label: 'Nomor Surat Usulan' },
        { id: 'file_surat_usulan', label: 'File Surat Usulan' },
        { id: 'nomor_berita_senat', label: 'Nomor Surat Senat' },
        { id: 'file_berita_senat', label: 'File Surat Senat' }
    ];

    let allValid = true;
    let errors = [];

    requiredFields.forEach(field => {
        const input = document.getElementById(field.id);
        const value = input.type === 'file' ? input.files.length > 0 : input.value.trim();

        // Reset styling
        input.classList.remove('border-red-500', 'border-green-500');

        if (!value) {
            input.classList.add('border-red-500');
            errors.push(`${field.label} wajib diisi`);
            allValid = false;
        } else {
            input.classList.add('border-green-500');
            
            // Additional validation for files
            if (input.type === 'file') {
                const fileValid = validateFile(input, 1);
                if (!fileValid) {
                    allValid = false;
                }
            }
        }
    });

    if (allValid) {
        // Show success message
        Swal.fire({
            icon: 'success',
            title: 'Validasi Berhasil!',
            text: 'Semua dokumen sudah lengkap dan valid. Siap untuk dikirim ke universitas.',
            timer: 2000,
            showConfirmButton: false
        });
    } else {
        // Show errors
        Swal.fire({
            icon: 'error',
            title: 'Validasi Gagal',
            html: '<ul class="text-left">' + errors.map(error => `<li>• ${error}</li>`).join('') + '</ul>',
            confirmButtonText: 'OK'
        });
    }

    return allValid;
}

// Bind file validation events
document.addEventListener('DOMContentLoaded', function() {
    const fileSuratInput = document.getElementById('file_surat_usulan');
    const fileBeritaInput = document.getElementById('file_berita_senat');

    if (fileSuratInput) {
        fileSuratInput.addEventListener('change', function(e) {
            validateFile(e.target, 1);
        });
    }

    if (fileBeritaInput) {
        fileBeritaInput.addEventListener('change', function(e) {
            validateFile(e.target, 1);
        });
    }
});
</script>