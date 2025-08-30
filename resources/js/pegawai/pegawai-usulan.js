// ========================================
// PEGAWAI UNMUL USULAN JAVASCRIPT
// ========================================

class PegawaiUsulan {
    constructor() {
        this.formInitialized = false; // Add flag to prevent duplicate initialization
        this.init();
    }

    init() {
        this.initializeForm();
        this.initializeSyaratGuruBesar();
        // Remove initializeFormValidation() to avoid duplicate listeners
        // this.initializeFormValidation(); // COMMENTED OUT
        this.initializeFileUploads();
    }

    // Initialize form functionality
    initializeForm() {
        console.log('Initializing form...');

        // Prevent duplicate initialization
        if (this.formInitialized) {
            console.log('Form already initialized, skipping...');
            return;
        }

        try {
            // More specific: Look for usulan form specifically
            const form = document.getElementById('usulan-form');
            
            if (!form) {
                console.log('Usulan form not found, trying fallback...');
                // Fallback to forms with usulan-jabatan in action
                const fallbackForm = document.querySelector('form[action*="usulan-jabatan"]');
                if (fallbackForm) {
                    console.log('Found form via action attribute');
                } else {
                    console.warn('No usulan form found');
                    return;
                }
            }

            console.log('Form found:', form ? form.constructor.name : 'null');
            console.log('Form is HTMLFormElement:', form instanceof HTMLFormElement);

            if (form && form instanceof HTMLFormElement) {
                console.log('Valid form found');
                
                // Mark as initialized
                this.formInitialized = true;

                // Don't add submit listener here since we're using button onclick
                // Just log for debugging
                console.log('Form initialization complete - using button onclick handlers');
                
            } else {
                console.warn('No valid HTMLFormElement found');
            }
        } catch (error) {
            console.error('Error in initializeForm:', error);
        }
    }

    // Syarat Guru Besar handling
    initializeSyaratGuruBesar() {
        // Use MutationObserver instead of setTimeout for better performance
        const observer = new MutationObserver((mutations, obs) => {
            const syaratSelect = document.getElementById('syarat_guru_besar');
            if (syaratSelect) {
                this.setupSyaratGuruBesar(syaratSelect);
                obs.disconnect(); // Stop observing once found
            }
        });

        // Start observing
        observer.observe(document.body, { 
            childList: true, 
            subtree: true 
        });

        // Fallback with timeout
        setTimeout(() => {
            observer.disconnect();
            const syaratSelect = document.getElementById('syarat_guru_besar');
            if (syaratSelect) {
                this.setupSyaratGuruBesar(syaratSelect);
            }
        }, 500);
    }

    setupSyaratGuruBesar(syaratSelect) {
        try {
            if (syaratSelect && syaratSelect.tagName === 'SELECT') {
                console.log('Syarat guru besar select found');

                const syaratDeskripsi = {
                    'hibah': 'Dokumen yang di-upload: MoU, SK Hibah, Laporan Hibah.',
                    'bimbingan': 'Dokumen yang di-upload: SK Pembimbing, Halaman Pengesahan, Cover Tesis yang dibimbing.',
                    'pengujian': 'Dokumen yang di-upload: SK Penguji, Berita Acara Hasil Ujian, Cover Tesis yang diuji.',
                    'reviewer': 'Dokumen yang di-upload: Surat Permohonan Reviewer, Dokumen yang di review.'
                };

                function updateSyaratDisplay(value) {
                    try {
                        console.log('updateSyaratDisplay called with value:', value);

                        const buktiContainer = document.getElementById('bukti_container');
                        const keteranganDiv = document.getElementById('keterangan_div');
                        const keteranganText = document.getElementById('keterangan_text');

                        if (buktiContainer) {
                            buktiContainer.style.display = value ? 'block' : 'none';
                        }

                        if (keteranganDiv) {
                            keteranganDiv.style.display = value ? 'block' : 'none';
                            if (keteranganText && syaratDeskripsi[value]) {
                                keteranganText.textContent = syaratDeskripsi[value];
                            }
                        }
                    } catch (error) {
                        console.error('Error in updateSyaratDisplay:', error);
                    }
                }

                // Initialize display based on current value
                updateSyaratDisplay(syaratSelect.value);

                // Add change listener
                syaratSelect.addEventListener('change', function() {
                    updateSyaratDisplay(this.value);
                });
            }
        } catch (error) {
            console.error('Error in setupSyaratGuruBesar:', error);
        }
    }

    // REMOVED or COMMENTED OUT to avoid duplicate listeners
    /*
    initializeFormValidation() {
        // This was causing duplicate submit listeners
        // Validation is now handled by button onclick
    }
    */

    // File upload handling
    initializeFileUploads() {
        // File upload preview functionality
        window.previewUploadedFile = function(input, previewId) {
            const file = input.files[0];
            const previewElement = document.getElementById(previewId);

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
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Check if not already initialized
    if (!window.pegawaiUsulanInstance) {
        window.pegawaiUsulanInstance = new PegawaiUsulan();
    }
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PegawaiUsulan;
}