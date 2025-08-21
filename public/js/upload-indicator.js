/**
 * Upload Indicator - Menambahkan tanda setelah upload dokumen
 * Digunakan untuk my profile role pegawai dan edit data pegawai role admin usulan
 * VERSION: 2.0 - Simplified and Safe
 */

// Flag untuk mencegah multiple event listeners
let eventListenersAttached = false;

// Fungsi untuk menampilkan tanda upload berhasil
function showUploadSuccessIndicator(inputElement, message = 'File berhasil diupload!') {
    // Hapus indikator sebelumnya jika ada
    const existingIndicator = document.querySelector('.upload-success-indicator');
    if (existingIndicator) {
        existingIndicator.remove();
    }

    // Buat indikator sukses
    const indicator = document.createElement('div');
    indicator.className = 'upload-success-indicator fixed top-4 right-4 z-50 bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg transform transition-all duration-300 opacity-0 translate-x-full';
    indicator.innerHTML = `
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <div>
                <p class="font-medium">${message}</p>
                <p class="text-sm opacity-90">File telah berhasil disimpan</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 ml-2 hover:opacity-75">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;

    // Tambahkan ke body
    document.body.appendChild(indicator);

    // Animasi masuk
    setTimeout(() => {
        indicator.classList.remove('opacity-0', 'translate-x-full');
    }, 100);

    // Auto remove setelah 5 detik
    setTimeout(() => {
        if (indicator.parentElement) {
            indicator.classList.add('opacity-0', 'translate-x-full');
            setTimeout(() => {
                if (indicator.parentElement) {
                    indicator.remove();
                }
            }, 300);
        }
    }, 5000);
}

// Fungsi untuk menampilkan tanda upload error
function showUploadErrorIndicator(inputElement, message = 'Gagal mengupload file!') {
    // Hapus indikator sebelumnya jika ada
    const existingIndicator = document.querySelector('.upload-error-indicator');
    if (existingIndicator) {
        existingIndicator.remove();
    }

    // Buat indikator error
    const indicator = document.createElement('div');
    indicator.className = 'upload-error-indicator fixed top-4 right-4 z-50 bg-red-500 text-white px-4 py-3 rounded-lg shadow-lg transform transition-all duration-300 opacity-0 translate-x-full';
    indicator.innerHTML = `
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <div>
                <p class="font-medium">${message}</p>
                <p class="text-sm opacity-90">Silakan coba lagi</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 ml-2 hover:opacity-75">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;

    // Tambahkan ke body
    document.body.appendChild(indicator);

    // Animasi masuk
    setTimeout(() => {
        indicator.classList.remove('opacity-0', 'translate-x-full');
    }, 100);

    // Auto remove setelah 5 detik
    setTimeout(() => {
        if (indicator.parentElement) {
            indicator.classList.add('opacity-0', 'translate-x-full');
            setTimeout(() => {
                if (indicator.parentElement) {
                    indicator.remove();
                }
            }, 300);
        }
    }, 5000);
}

// Fungsi untuk menampilkan preview nama file
function showFileNamePreview(inputElement) {
    const file = inputElement.files[0];
    if (!file) return;

    const container = inputElement.closest('.group') || inputElement.closest('div');
    if (!container) return;

    // Hapus preview sebelumnya jika ada
    const existingPreview = container.querySelector('.file-name-preview');
    if (existingPreview) {
        existingPreview.remove();
    }

    // Buat preview nama file
    const preview = document.createElement('div');
    preview.className = 'file-name-preview mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg';
    preview.innerHTML = `
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-blue-800 truncate" title="${file.name}">${file.name}</p>
                    <p class="text-xs text-blue-600">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                </div>
            </div>
            <button onclick="removeFileNamePreview(this)" class="flex-shrink-0 ml-2 text-red-500 hover:text-red-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="mt-2 text-xs text-blue-700">
            <span class="inline-flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                File siap untuk diupload saat form disimpan
            </span>
        </div>
    `;

    // Tambahkan preview setelah input file
    const inputContainer = inputElement.closest('div');
    if (inputContainer) {
        inputContainer.appendChild(preview);
    }

    // Animasi masuk
    preview.style.opacity = '0';
    preview.style.transform = 'translateY(-10px)';
    setTimeout(() => {
        preview.style.transition = 'all 0.3s ease';
        preview.style.opacity = '1';
        preview.style.transform = 'translateY(0)';
    }, 100);
}

// Fungsi untuk menghapus preview nama file
function removeFileNamePreview(button) {
    const preview = button.closest('.file-name-preview');
    if (preview) {
        const input = preview.closest('div').querySelector('input[type="file"]');
        if (input) {
            input.value = '';
        }

        preview.style.opacity = '0';
        preview.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            preview.remove();
        }, 300);
    }
}

// Fungsi untuk menangani file upload - SIMPLIFIED
function handleFileUpload(inputElement) {
    const file = inputElement.files[0];
    if (!file) return;

    // Validasi file
    const maxSize = parseInt(inputElement.getAttribute('data-max-size') || '2') * 1024 * 1024; // MB to bytes
    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];

    if (file.size > maxSize) {
        showUploadErrorIndicator(inputElement, `File terlalu besar! Maksimal ${inputElement.getAttribute('data-max-size') || '2'}MB`);
        inputElement.value = '';
        return;
    }

    if (!allowedTypes.includes(file.type)) {
        showUploadErrorIndicator(inputElement, 'Format file tidak didukung! Gunakan PDF, JPG, atau PNG');
        inputElement.value = '';
        return;
    }

    // Tampilkan preview nama file
    showFileNamePreview(inputElement);

    // Tampilkan indikator sukses
    showUploadSuccessIndicator(inputElement, `${file.name} siap diupload!`);
}

// Event listener untuk file input - SIMPLIFIED dan AMAN
function attachEventListeners() {
    if (eventListenersAttached) {
        return; // Mencegah multiple event listeners
    }

    // Hanya untuk input file yang belum memiliki event listener
    const fileInputs = document.querySelectorAll('input[type="file"]:not([data-upload-listener-attached])');

    fileInputs.forEach(input => {
        // Skip jika input sudah memiliki onchange handler
        if (input.hasAttribute('onchange')) {
            return;
        }

        // Tambahkan event listener
        input.addEventListener('change', function(e) {
            e.stopPropagation();
            handleFileUpload(this);
        });

        // Mark sebagai sudah memiliki listener
        input.setAttribute('data-upload-listener-attached', 'true');
    });

    eventListenersAttached = true;
}

// Event listener untuk DOM ready - SIMPLIFIED
document.addEventListener('DOMContentLoaded', function() {
    // Delay untuk memastikan semua element sudah ter-render
    setTimeout(() => {
        attachEventListeners();
    }, 200);
});

// Fungsi untuk preview file yang diupload (untuk kompatibilitas dengan existing code)
function previewUploadedFile(input, previewId) {
    const file = input.files[0];
    if (!file) return;

    const previewContainer = document.getElementById(previewId);
    if (!previewContainer) return;

    // Validasi file
    const maxSize = 2 * 1024 * 1024; // 2MB
    if (file.size > maxSize) {
        showUploadErrorIndicator(input, 'File terlalu besar! Maksimal 2MB');
        input.value = '';
        return;
    }

    if (file.type !== 'application/pdf') {
        showUploadErrorIndicator(input, 'Format file tidak didukung! Gunakan PDF');
        input.value = '';
        return;
    }

    // Tampilkan preview
    previewContainer.innerHTML = `
        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-green-800 truncate">${file.name}</p>
                    <p class="text-xs text-green-600">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                </div>
                <button onclick="removeFilePreview('${previewId}', '${input.id}')" class="flex-shrink-0 text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;
    previewContainer.classList.remove('hidden');

    // Tampilkan indikator sukses
    showUploadSuccessIndicator(input, `${file.name} siap diupload!`);
}

// Fungsi untuk menghapus preview file
function removeFilePreview(previewId, inputId) {
    const previewContainer = document.getElementById(previewId);
    const input = document.getElementById(inputId);

    if (previewContainer) {
        previewContainer.innerHTML = '';
        previewContainer.classList.add('hidden');
    }

    if (input) {
        input.value = '';
    }
}

// Export functions untuk digunakan di file lain
window.UploadIndicator = {
    showUploadSuccessIndicator,
    showUploadErrorIndicator,
    showFileNamePreview,
    removeFileNamePreview,
    handleFileUpload,
    previewUploadedFile,
    removeFilePreview,
    attachEventListeners
};
