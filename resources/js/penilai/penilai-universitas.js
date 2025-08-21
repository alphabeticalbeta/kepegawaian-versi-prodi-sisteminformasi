// ========================================
// PENILAI UNIVERSITAS JAVASCRIPT
// ========================================

class PenilaiUniversitas {
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
        this.initializePenilaian();
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
        if (typeof $.fn.DataTable !== 'undefined') {
            $('.datatable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
                }
            });
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

    // Penilaian functionality
    initializePenilaian() {
        // Penilaian usulan functionality
        window.nilaiUsulan = function(usulanId) {
            // Add penilaian logic here
            console.log('Nilai usulan:', usulanId);
        };

        window.viewUsulanDetail = function(usulanId) {
            // Add view detail logic here
            console.log('Viewing usulan detail:', usulanId);
        };

        window.downloadDokumen = function(dokumenId) {
            // Add download logic here
            console.log('Downloading dokumen:', dokumenId);
        };

        // Score calculation
        window.calculateScore = function() {
            const scores = document.querySelectorAll('.score-input');
            let totalScore = 0;
            let totalWeight = 0;

            scores.forEach(score => {
                const value = parseFloat(score.value) || 0;
                const weight = parseFloat(score.dataset.weight) || 1;
                totalScore += value * weight;
                totalWeight += weight;
            });

            const finalScore = totalWeight > 0 ? totalScore / totalWeight : 0;
            const finalScoreElement = document.getElementById('final-score');
            if (finalScoreElement) {
                finalScoreElement.textContent = finalScore.toFixed(2);
            }
        };

        // Initialize score calculation on input change
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('score-input')) {
                this.calculateScore();
            }
        }.bind(this));
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
            this.showLoadingState();
        };

        // Helper function to show loading state
        this.showLoadingState = function() {
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
                        <p class="text-center mt-4 text-gray-600">Memproses penilaian...</p>
                    </div>
                </div>
            `;
            document.body.appendChild(overlay);
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
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new PenilaiUniversitas();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PenilaiUniversitas;
}
