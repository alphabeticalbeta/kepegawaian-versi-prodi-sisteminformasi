// ========================================
// ADMIN FAKULTAS JAVASCRIPT
// ========================================

class AdminFakultas {
    constructor() {
        this.init();
    }

    init() {
        this.setupCsrfToken();
        this.initializeSidebar();
        this.initializeDropdowns();
        this.initializeDataTables();
        this.initializeFormValidation();
        this.initializeFileUploads();
        this.initializeAlerts();
        this.initializeUsulanManagement();
        this.initializeValidationScripts();
    }

    // CSRF Token Setup for AJAX requests
    setupCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            window.csrfToken = token.getAttribute('content');

            // Setup for fetch API
            window.fetchWithCsrf = function(url, options = {}) {
                options.headers = options.headers || {};
                options.headers['X-CSRF-TOKEN'] = window.csrfToken;
                options.headers['X-Requested-With'] = 'XMLHttpRequest';
                return fetch(url, options);
            };
        }
    }

    // Initialize Lucide icons
    initializeIcons() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    // Sidebar toggle functionality
    initializeSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');

        if (sidebar && mainContent) {
            window.toggleSidebar = () => {
                const isCollapsing = !sidebar.classList.contains('w-20');

                sidebar.classList.toggle('w-64', !isCollapsing);
                sidebar.classList.toggle('w-20', isCollapsing);
                mainContent.style.marginLeft = isCollapsing ? '5rem' : '16rem';

                // Toggle semua teks di dalam sidebar
                document.querySelectorAll('.sidebar-text').forEach(text => {
                    text.classList.toggle('hidden', isCollapsing);
                });

                // Jika sidebar diciutkan, pastikan semua submenu juga tertutup
                if (isCollapsing) {
                    document.querySelectorAll('.dropdown-menu').forEach(menu => {
                        menu.classList.add('hidden');
                        const button = document.querySelector(`[data-collapse-toggle="${menu.id}"]`);
                        if (button) {
                            button.setAttribute('aria-expanded', 'false');
                            const chevron = button.querySelector('[data-lucide="chevron-down"]');
                            if (chevron) {
                                chevron.classList.remove('rotate-180');
                            }
                        }
                    });
                }
            };
        }
    }

    // Dropdown sidebar functionality
    initializeDropdowns() {
        document.querySelectorAll('button[data-collapse-toggle]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const targetId = this.getAttribute('data-collapse-toggle');
                const dropdown = document.getElementById(targetId);

                if (!dropdown) return;

                const isOpening = dropdown.classList.contains('hidden');

                // Saat membuka dropdown, tutup dulu semua dropdown lain
                if (isOpening) {
                    document.querySelectorAll('.dropdown-menu').forEach(otherDropdown => {
                        if (otherDropdown.id !== targetId) {
                            otherDropdown.classList.add('hidden');
                            const otherButton = document.querySelector(`[data-collapse-toggle="${otherDropdown.id}"]`);
                            if (otherButton) {
                                otherButton.setAttribute('aria-expanded', 'false');
                                const otherChevron = otherButton.querySelector('[data-lucide="chevron-down"]');
                                if (otherChevron) {
                                    otherChevron.classList.remove('rotate-180');
                                }
                            }
                        }
                    });
                }

                // Toggle dropdown yang diklik
                dropdown.classList.toggle('hidden');
                this.setAttribute('aria-expanded', isOpening);

                const chevron = this.querySelector('[data-lucide="chevron-down"]');
                if (chevron) {
                    chevron.classList.toggle('rotate-180', isOpening);
                }
            });
        });
    }

    // DataTables initialization
    initializeDataTables() {
        // Initialize DataTables if available
        if (typeof $ !== 'undefined' && typeof $.fn !== 'undefined' && typeof $.fn.DataTable !== 'undefined') {
            $('.datatable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
                }
            });
        } else {
            console.log('jQuery or DataTables not available, skipping DataTables initialization');
        }
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

    // Usulan management functionality
    initializeUsulanManagement() {
        // Usulan approval/rejection functionality
        window.approveUsulan = function(usulanId) {
            if (confirm('Apakah Anda yakin ingin menyetujui usulan ini?')) {
                // Add approval logic here
                console.log('Approving usulan:', usulanId);
            }
        };

        window.rejectUsulan = function(usulanId) {
            const reason = prompt('Masukkan alasan penolakan:');
            if (reason) {
                // Add rejection logic here
                console.log('Rejecting usulan:', usulanId, 'Reason:', reason);
            }
        };

        window.viewUsulanDetail = function(usulanId) {
            // Add view detail logic here
            console.log('Viewing usulan detail:', usulanId);
        };
    }

    // Validation scripts functionality (moved from _validation-scripts.blade.php)
    initializeValidationScripts() {
        // Global functions for form interactions
        window.toggleKeterangan = function(fieldId, status) {
            const keteranganTextarea = document.getElementById(`keterangan_${fieldId}`);

            // Pengecekan keamanan: hanya jalankan jika elemen ditemukan
            if (keteranganTextarea) {
                if (status === 'tidak_sesuai') {
                    keteranganTextarea.disabled = false; // Buka kunci textarea
                    keteranganTextarea.placeholder = 'Jelaskan mengapa item ini tidak sesuai...';
                    keteranganTextarea.focus(); // Langsung fokuskan cursor ke textarea
                } else {
                    keteranganTextarea.disabled = true; // Kunci textarea
                    keteranganTextarea.placeholder = 'Pilih "Tidak Sesuai" untuk mengisi keterangan';
                    keteranganTextarea.value = ''; // Kosongkan nilainya untuk mencegah data tidak valid terkirim
                }
            }
        };

        window.resetForm = function() {
            if (confirm('Apakah Anda yakin ingin mereset semua validasi?')) {
                document.querySelectorAll('select[name*="[status]"]').forEach(select => {
                    select.value = 'sesuai';
                    const fieldParts = select.name.match(/validation\[(\w+)\]\[(\w+)\]/);
                    if (fieldParts) {
                        toggleKeterangan(fieldParts[1] + '_' + fieldParts[2], 'sesuai');
                    }
                });
            }
        };

        window.submitValidation = function(event) {
            const form = document.getElementById('validationForm');

            // Remove any existing action input
            const existingActionInput = form.querySelector('input[name="action_type"]');
            if (existingActionInput) {
                existingActionInput.remove();
            }

            // Add action type for save only
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action_type';
            actionInput.value = 'save_only';
            form.appendChild(actionInput);

            // Show loading state
            if (window.showLoadingState) {
                window.showLoadingState();
            }
        };

        window.showReturnForm = function() {
            document.getElementById('returnForm').classList.remove('hidden');
            document.getElementById('forwardForm').classList.add('hidden');

            // Update validation issue summary
            if (window.updateValidationIssueSummary) {
                window.updateValidationIssueSummary();
            }
        };

        window.hideReturnForm = function() {
            document.getElementById('returnForm').classList.add('hidden');
        };

        window.showForwardForm = function() {
            document.getElementById('forwardForm').classList.remove('hidden');
            document.getElementById('returnForm').classList.add('hidden');
        };

        window.hideForwardForm = function() {
            document.getElementById('forwardForm').classList.add('hidden');
        };

        window.submitReturnForm = function() {
            const mainForm = document.getElementById('validationForm');
            const catatanUmumTextarea = document.getElementById('catatan_umum_return');

            if (!catatanUmumTextarea) {
                console.error("ERROR: Textarea 'catatan_umum' not found!");
                alert("Terjadi error: komponen catatan tidak ditemukan.");
                return false;
            }

            const catatanUmum = catatanUmumTextarea.value;

            if (!catatanUmum || catatanUmum.trim().length < 10) {
                alert('Catatan untuk pegawai wajib diisi minimal 10 karakter.');
                catatanUmumTextarea.focus();
                return false;
            }

            if (!confirm('Apakah Anda yakin ingin mengembalikan usulan ini ke pegawai untuk perbaikan?')) {
                return false;
            }

            // Remove existing action input
            const existingActionInput = mainForm.querySelector('input[name="action_type"]');
            if (existingActionInput) {
                existingActionInput.remove();
            }

            // Add action type
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action_type';
            actionInput.value = 'return_to_pegawai';
            mainForm.appendChild(actionInput);

            // Add catatan
            const catatanInput = document.createElement('input');
            catatanInput.type = 'hidden';
            catatanInput.name = 'catatan_umum';
            catatanInput.value = catatanUmum;
            mainForm.appendChild(catatanInput);

            if (window.showLoadingState) {
                window.showLoadingState();
            }
            mainForm.submit();
        };

        // Helper functions - DEFINE FIRST
        window.validateForwardForm = function() {
            const requiredFields = ['nomor_surat_usulan', 'file_surat_usulan', 'nomor_berita_senat', 'file_berita_senat'];
            let missingFields = [];

            requiredFields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (!field || (field.type === 'file' && field.files.length === 0) || (field.type !== 'file' && !field.value.trim())) {
                    missingFields.push(fieldName);
                }
            });

            if (missingFields.length > 0) {
                alert('Mohon lengkapi semua field yang diperlukan: ' + missingFields.join(', '));
                return false;
            }

            return true;
        };

        // Updated submitForwardForm function - SAFE VERSION
        window.submitForwardForm = function() {
            // CRITICAL: Check if detail page override is active
            if (window.__DETAIL_PAGE_OVERRIDE_ACTIVE) {
                console.log('Detail page override active - skipping admin-fakultas.js submitForwardForm');
                return;
            }

            // Check if we're on the detail page (which has its own implementation)
            if (document.getElementById('action-form')) {
                console.log('Detail page detected - skipping admin-fakultas.js submitForwardForm');
                return;
            }

            // Check if we're on a page with forward form
            if (document.getElementById('forwardForm')) {
                console.log('Forward form detected - skipping admin-fakultas.js submitForwardForm');
                return;
            }

            // Only proceed if we're on a page that actually needs this functionality
            if (!document.getElementById('validationForm')) {
                console.log('No validation form found - skipping admin-fakultas.js submitForwardForm');
                return;
            }

            // Validate form first - SAFE VERSION
            if (typeof window.validateForwardForm !== 'function') {
                console.error('validateForwardForm function not found or not a function');
                alert('Sistem validasi tidak tersedia. Silakan refresh halaman.');
                return false;
            }

            try {
                if (!window.validateForwardForm()) {
                    return false;
                }
            } catch (error) {
                console.error('Error in validateForwardForm:', error);
                alert('Terjadi kesalahan dalam validasi form.');
                return false;
            }

            // Show confirmation
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Konfirmasi Pengiriman',
                    html: `
                        <div class="text-left">
                            <p class="mb-3">Apakah Anda yakin ingin <strong>mengirim usulan ini ke universitas</strong>?</p>
                            <div class="bg-yellow-50 border border-yellow-200 rounded p-3 text-sm">
                                <strong>⚠️ Perhatian:</strong>
                                <ul class="mt-2 space-y-1">
                                    <li>• Usulan akan diteruskan ke tingkat universitas</li>
                                    <li>• Dokumen yang sudah dikirim tidak dapat diubah</li>
                                    <li>• Proses ini tidak dapat dibatalkan</li>
                                </ul>
                            </div>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Kirim ke Universitas',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (window.processForwardSubmission) {
                            window.processForwardSubmission();
                        }
                    }
                });
            } else {
                if (confirm('Apakah Anda yakin ingin mengirim usulan ini ke universitas?')) {
                    if (window.processForwardSubmission) {
                        window.processForwardSubmission();
                    }
                }
            }
        };

        // Helper functions - validateForwardForm is now defined above

        window.processForwardSubmission = function() {
            const mainForm = document.getElementById('validationForm');
            const forwardForm = document.getElementById('forwardUsulanForm');
            const progressDiv = document.getElementById('uploadProgress');
            const progressBar = document.getElementById('uploadProgressBar');

            // Show progress
            if (progressDiv) {
                progressDiv.classList.remove('hidden');
                if (window.animateProgress) {
                    window.animateProgress(progressBar, 0, 30, 500); // 0-30% in 500ms
                }
            }

            // Validate file sizes
            const fileSurat = document.getElementById('file_surat_usulan');
            const fileBerita = document.getElementById('file_berita_senat');
            const maxSizeBytes = 1 * 1024 * 1024; // 1MB

            if (fileSurat.files.length > 0 && fileSurat.files[0].size > maxSizeBytes) {
                if (window.hideProgress) {
                    window.hideProgress();
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Terlalu Besar',
                        text: 'File surat usulan terlalu besar. Maksimal 1MB.',
                        confirmButtonText: 'OK'
                    });
                } else {
                    alert('File surat usulan terlalu besar. Maksimal 1MB.');
                }
                return false;
            }

            if (fileBerita.files.length > 0 && fileBerita.files[0].size > maxSizeBytes) {
                if (window.hideProgress) {
                    window.hideProgress();
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Terlalu Besar',
                        text: 'File berita senat terlalu besar. Maksimal 1MB.',
                        confirmButtonText: 'OK'
                    });
                } else {
                    alert('File berita senat terlalu besar. Maksimal 1MB.');
                }
                return false;
            }

            // Remove existing action input
            const existingActionInput = mainForm.querySelector('input[name="action_type"]');
            if (existingActionInput) {
                existingActionInput.remove();
            }

            // Add action type
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action_type';
            actionInput.value = 'forward_to_university';
            mainForm.appendChild(actionInput);

            // Update progress
            if (progressBar) {
                if (window.animateProgress) {
                    window.animateProgress(progressBar, 30, 85, 500); // 30-85% in 500ms
                }
            }

            // Clone forward form inputs to main form
            const forwardInputs = forwardForm.querySelectorAll('input, textarea, select');
            forwardInputs.forEach(input => {
                if (input.name) { // Only clone inputs with names
                    const clonedInput = input.cloneNode(true);
                    clonedInput.style.display = 'none';
                    mainForm.appendChild(clonedInput);
                }
            });

            // Complete progress and submit
            if (progressBar) {
                if (window.animateProgress) {
                    window.animateProgress(progressBar, 85, 100, 300); // 85-100% in 300ms
                }
                setTimeout(() => {
                    if (window.showLoadingState) {
                        window.showLoadingState();
                    }
                    mainForm.submit();
                }, 300);
            } else {
                if (window.showLoadingState) {
                    window.showLoadingState();
                }
                mainForm.submit();
            }
        };

        window.animateProgress = function(progressBar, startPercent, endPercent, duration) {
            if (!progressBar) return;

            const startTime = performance.now();
            const percentDiff = endPercent - startPercent;

            function updateProgress(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const currentPercent = startPercent + (percentDiff * progress);

                progressBar.style.width = currentPercent + '%';

                if (progress < 1) {
                    requestAnimationFrame(updateProgress);
                }
            }

            requestAnimationFrame(updateProgress);
        };

        window.hideProgress = function() {
            const progressDiv = document.getElementById('uploadProgress');
            if (progressDiv) {
                progressDiv.classList.add('hidden');
            }
            const progressBar = document.getElementById('uploadProgressBar');
            if (progressBar) {
                progressBar.style.width = '0%';
            }
        };

        window.updateValidationIssueSummary = function() {
            const issueList = document.getElementById('issueList');
            const summaryContainer = document.getElementById('validationIssueSummary');

            if (!issueList || !summaryContainer) return;

            // Clear existing list
            issueList.innerHTML = '';

            // Find all "tidak sesuai" items
            const invalidItems = [];
            document.querySelectorAll('select[name*="[status]"]').forEach(select => {
                if (select.value === 'tidak_sesuai') {
                    const fieldParts = select.name.match(/validation\[(\w+)\]\[(\w+)\]/);
                    if (fieldParts) {
                        const category = fieldParts[1];
                        const field = fieldParts[2];
                        const label = window.ucwords(category.replace(/_/g, ' ')) + ' - ' + window.ucwords(field.replace(/_/g, ' '));
                        invalidItems.push(label);
                    }
                }
            });

            // Populate list
            if (invalidItems.length > 0) {
                summaryContainer.classList.remove('hidden');
                invalidItems.forEach(item => {
                    const li = document.createElement('li');
                    li.textContent = item;
                    issueList.appendChild(li);
                });
            } else {
                summaryContainer.classList.add('hidden');
            }
        };

        window.showLoadingState = function() {
            // Create loading overlay
            const overlay = document.createElement('div');
            overlay.id = 'loadingOverlay';
            overlay.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
            overlay.innerHTML = `
                <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-5 w-96">
                    <div class="bg-white rounded-lg shadow-xl p-6">
                        <div class="flex justify-center items-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                        </div>
                        <p class="text-center mt-4 text-gray-600">Memproses usulan...</p>
                    </div>
                </div>
            `;
            document.body.appendChild(overlay);
        };

        window.ucwords = function(str) {
            return str.replace(/\b\w/g, function(l) {
                return l.toUpperCase();
            });
        };

        // Initialize validation scripts on page load
        document.addEventListener('DOMContentLoaded', () => {
            // Set initial state for all fields based on existing data
            document.querySelectorAll('select[name*="[status]"]').forEach(select => {
                const fieldParts = select.name.match(/validation\[(\w+)\]\[(\w+)\]/);
                if (fieldParts) {
                    toggleKeterangan(fieldParts[1] + '_' + fieldParts[2], select.value);
                }
            });

            document.querySelectorAll('select[data-field-id]').forEach(selectElement => {
                selectElement.addEventListener('change', function(event) {
                    const fieldId = event.target.getAttribute('data-field-id');
                    const status = event.target.value;
                    toggleKeterangan(fieldId, status);
                });
            });

            // Add event listeners for file inputs
            const fileSuratInput = document.getElementById('file_surat_usulan');
            const fileBeritaInput = document.getElementById('file_berita_senat');

            if (fileSuratInput) {
                fileSuratInput.addEventListener('change', function(e) {
                    if (window.validateFileInput) {
                        window.validateFileInput(e.target, 2);
                    }
                });
            }

            if (fileBeritaInput) {
                fileBeritaInput.addEventListener('change', function(e) {
                    if (window.validateFileInput) {
                        window.validateFileInput(e.target, 5);
                    }
                });
            }
        });

        // Validate file input
        window.validateFileInput = function(input, maxSizeMB) {
            if (input.files.length > 0) {
                const file = input.files[0];
                const maxSizeBytes = maxSizeMB * 1024 * 1024;

                if (file.size > maxSizeBytes) {
                    alert(`File terlalu besar. Maksimal ${maxSizeMB}MB.`);
                    input.value = '';
                    return false;
                }

                if (!file.type.includes('pdf')) {
                    alert('File harus berformat PDF.');
                    input.value = '';
                    return false;
                }
            }
            return true;
        };
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new AdminFakultas();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AdminFakultas;
}
