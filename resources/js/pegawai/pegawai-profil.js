// ========================================
// PEGAWAI UNMUL PROFILE JAVASCRIPT
// ========================================

class PegawaiProfil {
    constructor() {
        this.init();
    }

    init() {
        this.initializeFilePreview();
        this.initializeImagePreview();
        this.initializeFormValidation();
        this.initializeTabs();
        this.initializeAlerts();
    }

    // Enhanced file preview with validation (Profile & Usulan)
    initializeFilePreview() {
        window.previewUploadedFile = function(input, previewId) {
            const file = input.files[0];
            const previewElement = document.getElementById(previewId);
            const progressElement = document.getElementById('progress-' + input.name);

            if (file) {
                // Validate file type
                if (file.type !== 'application/pdf') {
                    alert('Hanya file PDF yang diperbolehkan!');
                    input.value = '';
                    return;
                }

                // Validate file size (2MB = 2097152 bytes)
                if (file.size > 2097152) {
                    alert('Ukuran file maksimal 2MB!');
                    input.value = '';
                    return;
                }

                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                const fileName = file.name;

                // Show progress (for profile upload)
                if (progressElement) {
                    progressElement.classList.remove('hidden');
                    let progress = 0;
                    const progressBar = progressElement.querySelector('div div');
                    const interval = setInterval(() => {
                        progress += Math.random() * 30;
                        if (progress >= 100) {
                            progress = 100;
                            clearInterval(interval);
                            setTimeout(() => {
                                progressElement.classList.add('hidden');
                            }, 1000);
                        }
                        progressBar.style.width = progress + '%';
                    }, 200);
                }

                // Enhanced preview for profile pages
                if (previewElement) {
                    previewElement.innerHTML = `
                        <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 bg-green-100 rounded-full p-2">
                                    <i data-lucide="file-check" class="w-4 h-4 text-green-600"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-green-800 truncate">${fileName}</p>
                                    <div class="flex items-center gap-4 mt-1">
                                        <span class="text-xs text-green-600">Ukuran: ${fileSize} MB</span>
                                        <span class="text-xs text-green-600 flex items-center gap-1">
                                            <i data-lucide="check-circle" class="w-3 h-3"></i>
                                            Siap diupload
                                        </span>
                                    </div>
                                </div>
                                <button type="button" onclick="clearFilePreview('${input.id}', '${previewId}')"
                                        class="flex-shrink-0 text-green-600 hover:text-green-800">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    `;
                    previewElement.classList.remove('hidden');
                }

                // Re-initialize Lucide icons
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            } else if (previewElement) {
                previewElement.classList.add('hidden');
            }
        };

        // Clear file preview
        window.clearFilePreview = function(inputId, previewId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);

            if (input) input.value = '';
            if (preview) preview.classList.add('hidden');
        };
    }

    // Image preview for foto pegawai
    initializeImagePreview() {
        window.previewImage = function(input) {
            const file = input.files[0];
            if (file) {
                // Validasi file
                if (!file.type.startsWith('image/')) {
                    alert('Hanya file gambar yang diperbolehkan!');
                    input.value = '';
                    return;
                }

                // Validasi ukuran file (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file maksimal 2MB!');
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgElement = input.closest('.relative').querySelector('img');
                    if (imgElement) {
                        imgElement.src = e.target.result;
                    }

                    // Show preview message
                    const previewElement = document.getElementById('preview-foto');
                    if (previewElement) {
                        const fileSize = (file.size / 1024 / 1024).toFixed(2);
                        previewElement.innerHTML = `
                            <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 bg-green-100 rounded-full p-2">
                                        <i data-lucide="image" class="w-4 h-4 text-green-600"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-green-800 truncate">${file.name}</p>
                                        <div class="flex items-center gap-4 mt-1">
                                            <span class="text-xs text-green-600">Ukuran: ${fileSize} MB</span>
                                            <span class="text-xs text-green-600 flex items-center gap-1">
                                                <i data-lucide="check-circle" class="w-3 h-3"></i>
                                                Siap diupload
                                            </span>
                                        </div>
                                    </div>
                                    <button type="button" onclick="clearImagePreview('${input.id}', 'preview-foto')"
                                            class="flex-shrink-0 text-green-600 hover:text-green-800">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                        previewElement.classList.remove('hidden');
                    }

                    // Re-initialize Lucide icons
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }
                reader.readAsDataURL(file);
            }
        };

        // Clear image preview
        window.clearImagePreview = function(inputId, previewId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            const imgElement = input.closest('.relative').querySelector('img');

            if (input) input.value = '';
            if (preview) preview.classList.add('hidden');

            // Reset image to original
            if (imgElement) {
                const originalSrc = imgElement.getAttribute('data-original-src');
                if (originalSrc) {
                    imgElement.src = originalSrc;
                }
            }
        };
    }

    // Form validation
    initializeFormValidation() {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                // Add validation logic here
                console.log('Form submitted:', form.action);
            });
        });
    }

    // Tab functionality
    initializeTabs() {
        const tabButtons = document.querySelectorAll('[data-tab]');
        const tabContents = document.querySelectorAll('[data-tab-content]');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-tab');

                // Remove active class from all buttons and contents
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.add('hidden'));

                // Add active class to clicked button and show target content
                this.classList.add('active');
                const targetContent = document.querySelector(`[data-tab-content="${targetTab}"]`);
                if (targetContent) {
                    targetContent.classList.remove('hidden');
                }
            });
        });
    }

    // Alert handling
    initializeAlerts() {
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (alert.classList.contains('alert-success') || alert.classList.contains('alert-error')) {
                    alert.style.transition = 'opacity 0.5s ease-out';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }
            });
        }, 5000);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new PegawaiProfil();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PegawaiProfil;
}
