// ========================================
// PEGAWAI UNMUL USULAN JAVASCRIPT
// ========================================

class PegawaiUsulan {
    constructor() {
        this.init();
    }

    init() {
        this.initializeForm();
        this.initializeSyaratGuruBesar();
        this.initializeFormValidation();
        this.initializeFileUploads();
    }

    // Initialize form functionality
    initializeForm() {
        console.log('Initializing form...');

        try {
            // FIXED: More specific form selection to avoid RadioNodeList conflict
            let form = null;

            // Try multiple approaches to find the actual form element
            const forms = document.forms;
            for (let i = 0; i < forms.length; i++) {
                if (forms[i].method.toLowerCase() === 'post') {
                    form = forms[i];
                    break;
                }
            }

            // Fallback: Try by action
            if (!form) {
                form = document.querySelector('form[action*="usulan-jabatan"]');
            }

            // Last resort: Get first form
            if (!form) {
                form = document.querySelector('form');
            }

            console.log('Form found:', form ? form.constructor.name : 'null');
            console.log('Form is HTMLFormElement:', form instanceof HTMLFormElement);

            if (form && form instanceof HTMLFormElement) {
                console.log('Valid form found, adding event listener');

                form.addEventListener('submit', function(e) {
                    console.log('Form submitting...');

                    // SIMPLIFIED: Remove client-side validation for now
                    // Just let the form submit normally
                    return true;
                });
            } else {
                console.warn('No valid HTMLFormElement found, skipping form validation');
            }
        } catch (error) {
            console.error('Error in initializeForm:', error);
        }
    }

    // Syarat Guru Besar handling
    initializeSyaratGuruBesar() {
        setTimeout(function() {
            try {
                const syaratSelect = document.getElementById('syarat_guru_besar');
                if (syaratSelect && syaratSelect.tagName === 'SELECT') {
                    console.log('Syarat guru besar select found');

                    const syaratDeskripsi = {
                        'hibah': 'Dokumen yang di-upload: MoU, SK Hibah, Laporan Hibah.',
                        'bimbingan': 'Dokumen yang di-upload: SK Pembimbing, Halaman Pengesahan, Cover Tesis yang dibimbing.',
                        'pengujian': 'Dokumen yang di-upload: SK Penguji, Berita Acara Hasil Ujian, Cover Tesis yang diuji.',
                        'reviewer': 'Dokumen yang di-upload: Surat Permohonan Reviewer, Dokumen yang di review.'
                    };

                    // Initialize display based on current value
                    updateSyaratDisplay(syaratSelect.value);

                    // CRITICAL FIX: Check if element is actually a form element
                    if (syaratSelect.addEventListener && typeof syaratSelect.addEventListener === 'function') {
                        syaratSelect.addEventListener('change', function() {
                            updateSyaratDisplay(this.value);
                        });
                    } else {
                        console.error('syaratSelect does not support addEventListener:', syaratSelect);
                    }

                    function updateSyaratDisplay(value) {
                        try {
                            console.log('updateSyaratDisplay called with value:', value);

                            const buktiContainer = document.getElementById('bukti_container');
                            const keteranganDiv = document.getElementById('keterangan_div');
                            const keteranganText = document.getElementById('keterangan_text');

                            console.log('Elements found:', {
                                buktiContainer: !!buktiContainer,
                                keteranganDiv: !!keteranganDiv,
                                keteranganText: !!keteranganText
                            });

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
                } else {
                    console.log('Syarat guru besar select not found or not a select element');
                }
            } catch (error) {
                console.error('Error in initializeSyaratGuruBesar:', error);
            }
        }, 100);
    }

    // Form validation
    initializeFormValidation() {
        // Add form validation logic here
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                // Add validation logic here
                console.log('Form submitted:', form.action);
            });
        });
    }

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
    new PegawaiUsulan();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PegawaiUsulan;
}
