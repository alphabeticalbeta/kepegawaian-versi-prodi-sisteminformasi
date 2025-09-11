@extends('backend.layouts.roles.admin-universitas.app')

@section('title', 'Struktur Organisasi - Admin Universitas')

@push('styles')
<style>
/* Image Upload Styles */
.image-upload-container {
    border: 2px dashed #D1D5DB;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

.image-upload-container:hover {
    border-color: #3B82F6;
    background-color: #F8FAFC;
}

.image-upload-container.dragover {
    border-color: #3B82F6;
    background-color: #EBF8FF;
}

.image-preview {
    max-width: 100%;
    max-height: 400px;
    object-fit: contain;
    border-radius: 0.5rem;
}

.upload-icon {
    color: #9CA3AF;
    transition: color 0.3s ease;
}

.image-upload-container:hover .upload-icon {
    color: #3B82F6;
}

/* Loading overlay */
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.5rem;
}

/* Image actions */
.image-actions {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    display: flex;
    gap: 0.5rem;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.image-container:hover .image-actions {
    opacity: 1;
}
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <!-- Header Section -->
    <div class="relative overflow-hidden bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 shadow-2xl">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative px-6 py-12 sm:px-8 sm:py-16">
            <div class="mx-auto max-w-4xl text-center">
                <div class="mb-6">
                    <div class="mx-auto h-16 w-16 rounded-full bg-white bg-opacity-20 flex items-center justify-center backdrop-blur-sm">
                        <i data-lucide="sitemap" class="h-8 w-8 text-white"></i>
                    </div>
                </div>
                <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl mb-4">
                    Struktur Organisasi
                </h1>
                <p class="text-lg text-blue-100 sm:text-xl">
                    Kelola Struktur Organisasi Universitas Mulawarman
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="relative z-10 -mt-8 px-6 sm:px-8">
        <div class="mx-auto max-w-7xl">
            <!-- Header Section -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Data Struktur Organisasi</h2>
                <p class="text-gray-600 mt-1">Kelola gambar struktur organisasi universitas</p>
            </div>

            <!-- Image Upload Section -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                <i data-lucide="upload" class="h-6 w-6 text-indigo-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-xl font-bold text-gray-900">Upload Struktur Organisasi</h3>
                            <p class="text-gray-600 text-sm">Upload atau update gambar struktur organisasi</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Current Image Display -->
                    <div id="currentImageSection" class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Gambar Saat Ini</h4>
                        <div id="currentImageContainer" class="relative inline-block">
                            <!-- Current image will be loaded here -->
                        </div>
                    </div>

                    <!-- Image Upload Form -->
                    <form id="imageUploadForm" enctype="multipart/form-data">
                        <div class="space-y-6">
                            <!-- File Upload -->
                            <div>
                                <label for="strukturImage" class="block text-sm font-medium text-gray-700 mb-2">
                                    Pilih Gambar <span class="text-red-500">*</span>
                                </label>
                                <div class="image-upload-container p-8 text-center cursor-pointer" onclick="document.getElementById('strukturImage').click()">
                                    <input type="file" id="strukturImage" name="struktur_image" accept="image/*" class="hidden" required>
                                    <div class="space-y-4">
                                        <div class="mx-auto h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center">
                                            <i data-lucide="image" class="h-8 w-8 upload-icon"></i>
                                        </div>
                                        <div>
                                            <p class="text-lg font-medium text-gray-900">Klik untuk upload gambar</p>
                                            <p class="text-sm text-gray-500">atau drag & drop gambar di sini</p>
                                        </div>
                                        <p class="text-xs text-gray-400">
                                            Format yang didukung: JPG, PNG, GIF (Maksimal 5MB)
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Image Preview -->
                            <div id="imagePreviewSection" class="hidden">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">Preview Gambar</h4>
                                <div class="relative inline-block">
                                    <img id="imagePreview" class="image-preview" alt="Preview">
                                    <div class="image-actions">
                                        <button type="button" onclick="removePreview()" class="p-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors">
                                            <i data-lucide="x" class="h-4 w-4"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                    Deskripsi
                                </label>
                                <textarea id="description" name="description" rows="3"
                                          placeholder="Masukkan deskripsi struktur organisasi (opsional)..."
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"></textarea>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                            <button type="button" onclick="resetForm()"
                                    class="px-6 py-3 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-md hover:shadow-lg">
                                Reset
                            </button>
                            <button type="submit" id="submitBtn"
                                    class="inline-flex items-center px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl">
                                <span id="submitBtnText">Update Struktur Organisasi</span>
                                <i data-lucide="loader-2" class="h-4 w-4 animate-spin hidden ml-2" id="submitBtnLoader"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Global variables
let currentImageData = null;

// Escape HTML function for XSS protection
function escapeHtml(text) {
    if (text === null || text === undefined) {
        return '';
    }
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadCurrentImage();
    initializeForm();
    initializeDragAndDrop();
});

// Load current image
async function loadCurrentImage() {
    try {
        const response = await fetch('/struktur-organisasi/data', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            credentials: 'same-origin'
        });

        if (response.ok) {
            const result = await response.json();
            if (result.success && result.data) {
                displayCurrentImage(result.data);
            } else {
                showNoImageMessage();
            }
        } else {
            showNoImageMessage();
        }
    } catch (error) {
        showNoImageMessage();
    }
}

// Display current image
function displayCurrentImage(data) {
    const container = document.getElementById('currentImageContainer');
    container.innerHTML = `
        <div class="relative inline-block">
            <img src="${escapeHtml(data.image_url)}" alt="Struktur Organisasi" class="image-preview">
            <div class="image-actions">
                <button type="button" onclick="editImage()" class="p-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-full hover:from-blue-700 hover:to-cyan-700 transition-all duration-200 shadow-md hover:shadow-lg" title="Edit">
                    <i data-lucide="edit" class="h-4 w-4"></i>
                </button>
                <button type="button" onclick="deleteImage()" class="p-3 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-full hover:from-red-700 hover:to-pink-700 transition-all duration-200 shadow-md hover:shadow-lg" title="Hapus">
                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                </button>
            </div>
        </div>
        ${data.description ? `<p class="text-sm text-gray-600 mt-2">${escapeHtml(data.description)}</p>` : ''}
    `;

    currentImageData = data;

    // Reinitialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

// Show no image message
function showNoImageMessage() {
    const container = document.getElementById('currentImageContainer');
    container.innerHTML = `
        <div class="text-center py-8 text-gray-500">
            <i data-lucide="image-off" class="h-12 w-12 mx-auto mb-4 text-gray-300"></i>
            <p>Belum ada gambar struktur organisasi</p>
            <p class="text-sm">Upload gambar untuk memulai</p>
        </div>
    `;

    // Reinitialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

// Initialize form
function initializeForm() {
    const form = document.getElementById('imageUploadForm');
    const fileInput = document.getElementById('strukturImage');

    form.addEventListener('submit', handleFormSubmit);
    fileInput.addEventListener('change', handleFileSelect);
}

// Handle file selection
function handleFileSelect(event) {
    const file = event.target.files[0];
    if (file) {
        // Validate file type
        if (!file.type.startsWith('image/')) {
            showError('Harap pilih file gambar yang valid');
            return;
        }

        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            showError('Ukuran file maksimal 5MB');
            return;
        }

        // Show preview
        showImagePreview(file);
    }
}

// Show image preview
function showImagePreview(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        const previewSection = document.getElementById('imagePreviewSection');
        const previewImage = document.getElementById('imagePreview');

        previewImage.src = e.target.result;
        previewSection.classList.remove('hidden');
    };
    reader.readAsDataURL(file);
}

// Remove preview
function removePreview() {
    const previewSection = document.getElementById('imagePreviewSection');
    const fileInput = document.getElementById('strukturImage');

    previewSection.classList.add('hidden');
    fileInput.value = '';
}

// Initialize drag and drop
function initializeDragAndDrop() {
    const uploadContainer = document.querySelector('.image-upload-container');

    uploadContainer.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadContainer.classList.add('dragover');
    });

    uploadContainer.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadContainer.classList.remove('dragover');
    });

    uploadContainer.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadContainer.classList.remove('dragover');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const fileInput = document.getElementById('strukturImage');
            fileInput.files = files;
            handleFileSelect({ target: { files: files } });
        }
    });
}

// Handle form submit
function handleFormSubmit(e) {
    e.preventDefault();

    const formData = new FormData(e.target);
    const fileInput = document.getElementById('strukturImage');

    if (!fileInput.files[0]) {
        showError('Harap pilih gambar terlebih dahulu');
        return;
    }

    // Show loading
    showSubmitLoading(true);

    // Make API call
    fetch('/struktur-organisasi/store', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return response.json();
    })
    .then(result => {
        if (result.success) {
            showSuccess('Gambar berhasil diupload');
            loadCurrentImage();
            resetForm();
        } else {
            showError('Upload gagal');
        }
    })
    .catch(error => {
        showError('Terjadi kesalahan saat mengupload gambar');
    })
    .finally(() => {
        showSubmitLoading(false);
    });
}

// Edit image
function editImage() {
    document.getElementById('strukturImage').click();
}

// Delete image
function deleteImage() {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Apakah Anda yakin ingin menghapus gambar struktur organisasi?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'bg-gray-800 text-white',
            title: 'text-white',
            content: 'text-gray-300',
            confirmButton: 'bg-red-600 hover:bg-red-700 text-white',
            cancelButton: 'bg-gray-600 hover:bg-gray-700 text-white'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/struktur-organisasi/delete', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showSuccess('Gambar berhasil dihapus');
                    loadCurrentImage();
                } else {
                    showError('Gagal menghapus gambar');
                }
            })
            .catch(error => {
                showError('Terjadi kesalahan saat menghapus gambar');
            });
        }
    });
}

// Reset form
function resetForm() {
    document.getElementById('imageUploadForm').reset();
    document.getElementById('imagePreviewSection').classList.add('hidden');
}

// Show submit loading
function showSubmitLoading(show) {
    const btn = document.getElementById('submitBtn');
    const text = document.getElementById('submitBtnText');
    const loader = document.getElementById('submitBtnLoader');

    if (show) {
        btn.disabled = true;
        text.textContent = 'Mengupload...';
        loader.classList.remove('hidden');
    } else {
        btn.disabled = false;
        text.textContent = 'Update Struktur Organisasi';
        loader.classList.add('hidden');
    }
}

// Show success message
function showSuccess(message) {
    Swal.fire({
        title: 'Berhasil!',
        text: message,
        icon: 'success',
        confirmButtonText: 'OK',
        customClass: {
            popup: 'bg-gray-800 text-white',
            title: 'text-white',
            content: 'text-gray-300',
            confirmButton: 'bg-green-600 hover:bg-green-700 text-white'
        }
    });
}

// Show error message
function showError(message) {
    Swal.fire({
        title: 'Error!',
        text: message,
        icon: 'error',
        confirmButtonText: 'OK',
        customClass: {
            popup: 'bg-gray-800 text-white',
            title: 'text-white',
            content: 'text-gray-300',
            confirmButton: 'bg-red-600 hover:bg-red-700 text-white'
        }
    });
}
</script>
@endpush
@endsection
