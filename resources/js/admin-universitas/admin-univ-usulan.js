// ========================================
// ADMIN UNIVERSITAS USULAN JAVASCRIPT
// ========================================

// Global JavaScript Functions
class AdminUnivUsulan {
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
        if (typeof window.jQuery !== 'undefined' && typeof window.jQuery.fn.DataTable !== 'undefined') {
            window.jQuery('.datatable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
                }
            });
        } else {
            console.log('DataTables or jQuery not available');
        }
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

    // Validation scripts functionality (moved from _validation-scripts.blade.php)
    initializeValidationScripts() {
        // Return for Revision Form Functions
        window.showReturnForRevisionForm = function() {
            document.getElementById('returnForRevisionForm').classList.remove('hidden');
            document.getElementById('catatan_umum_return_revision').focus();
        };

        window.hideReturnForRevisionForm = function() {
            document.getElementById('returnForRevisionForm').classList.add('hidden');
            document.getElementById('catatan_umum_return_revision').value = '';
            document.getElementById('charCount_return_revision').textContent = '0';
        };

        // Not Recommended Form Functions
        window.showNotRecommendedForm = function() {
            document.getElementById('notRecommendedForm').classList.remove('hidden');
            document.getElementById('catatan_umum_not_recommended').focus();
        };

        window.hideNotRecommendedForm = function() {
            document.getElementById('notRecommendedForm').classList.add('hidden');
            document.getElementById('catatan_umum_not_recommended').value = '';
            document.getElementById('charCount_not_recommended').textContent = '0';
        };

        // Send to Assessor Team Form Functions
        window.showSendToAssessorForm = function() {
            document.getElementById('sendToAssessorForm').classList.remove('hidden');
        };

        window.hideSendToAssessorForm = function() {
            document.getElementById('sendToAssessorForm').classList.add('hidden');
            // Reset checkboxes
            const checkboxes = document.querySelectorAll('input[name="assessor_ids[]"]');
            checkboxes.forEach(checkbox => checkbox.checked = false);
            this.validateAssessorSelection();
        };

        // Send to Senate Team Form Functions
        window.showSendToSenateForm = function() {
            document.getElementById('sendToSenateForm').classList.remove('hidden');
        };

        window.hideSendToSenateForm = function() {
            document.getElementById('sendToSenateForm').classList.add('hidden');
        };

        // Return Form Functions
        window.showReturnForm = function() {
            const form = document.getElementById('returnForm');
            const textarea = document.getElementById('catatan_umum_return');

            if (!form) {
                console.error('Return form not found');
                return;
            }

            form.classList.remove('hidden');
            if (textarea) {
                textarea.focus();
            }
        };

        window.hideReturnForm = function() {
            const form = document.getElementById('returnForm');
            const textarea = document.getElementById('catatan_umum_return');
            const charCount = document.getElementById('charCount_return');

            if (!form) {
                console.error('Return form not found');
                return;
            }

            form.classList.add('hidden');
            if (textarea) {
                textarea.value = '';
            }
            if (charCount) {
                charCount.textContent = '0';
            }
        };

        // Validation Functions
        window.validateAssessorSelection = function() {
            const checkboxes = document.querySelectorAll('input[name="assessor_ids[]"]:checked');
            const count = checkboxes.length;
            const submitBtn = document.getElementById('submitAssessorBtn');
            const countDisplay = document.getElementById('assessorCount');

            if (countDisplay) {
                countDisplay.textContent = count;
            }

            if (submitBtn) {
                if (count >= 1 && count <= 3) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('bg-gray-300', 'cursor-not-allowed');
                    submitBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                } else {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('bg-gray-300', 'cursor-not-allowed');
                    submitBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                }
            }
        };

        // Initialize form handlers
        document.addEventListener('DOMContentLoaded', () => {
            console.log('DOM loaded, initializing form handlers...');

            // Return Form Submission
            const returnForm = document.getElementById('returnFormSubmit');
            if (returnForm) {
                returnForm.addEventListener('submit', function(e) {
                    const textarea = document.getElementById('catatan_umum_return');
                    if (!textarea) {
                        console.error('Return form textarea not found');
                        return;
                    }

                    const value = textarea.value.trim();

                    if (value.length < 10) {
                        e.preventDefault();
                        alert('Catatan perbaikan harus minimal 10 karakter.');
                        textarea.focus();
                        return false;
                    }

                    if (value.length > 2000) {
                        e.preventDefault();
                        alert('Catatan perbaikan maksimal 2000 karakter.');
                        textarea.focus();
                        return false;
                    }

                    return confirm('Apakah Anda yakin ingin mengembalikan usulan ini ke pegawai untuk perbaikan?');
                });
            } else {
                console.warn('Return form submit handler not found');
            }

            // Not Recommended Form Submission
            const notRecommendedForm = document.getElementById('notRecommendedFormSubmit');
            if (notRecommendedForm) {
                notRecommendedForm.addEventListener('submit', function(e) {
                    const textarea = document.getElementById('catatan_umum_not_recommended');
                    if (!textarea) {
                        console.error('Not recommended form textarea not found');
                        return;
                    }

                    const value = textarea.value.trim();

                    if (value.length < 10) {
                        e.preventDefault();
                        alert('Alasan tidak direkomendasikan harus minimal 10 karakter.');
                        textarea.focus();
                        return false;
                    }

                    if (value.length > 2000) {
                        e.preventDefault();
                        alert('Alasan tidak direkomendasikan maksimal 2000 karakter.');
                        textarea.focus();
                        return false;
                    }

                    return confirm('Apakah Anda yakin ingin menandai usulan ini sebagai tidak direkomendasikan? Pegawai tidak dapat submit lagi di periode ini.');
                });
            } else {
                console.warn('Not recommended form submit handler not found');
            }

            // Send to Assessor Team Form Submission
            const sendToAssessorForm = document.getElementById('sendToAssessorFormSubmit');
            if (sendToAssessorForm) {
                sendToAssessorForm.addEventListener('submit', function(e) {
                    const checkboxes = document.querySelectorAll('input[name="assessor_ids[]"]:checked');

                    if (checkboxes.length < 1) {
                        e.preventDefault();
                        alert('Pilih minimal 1 penilai.');
                        return false;
                    }

                    if (checkboxes.length > 3) {
                        e.preventDefault();
                        alert('Pilih maksimal 3 penilai.');
                        return false;
                    }

                    const assessorNames = Array.from(checkboxes).map(cb => {
                        return cb.nextElementSibling ? cb.nextElementSibling.textContent : 'Unknown';
                    }).join(', ');

                    return confirm(`Apakah Anda yakin ingin mengirim usulan ini ke Tim Penilai?\n\nPenilai yang dipilih:\n${assessorNames}`);
                });
            } else {
                console.warn('Send to assessor form submit handler not found');
            }

            // Send to Senate Team Form Submission
            const sendToSenateForm = document.getElementById('sendToSenateFormSubmit');
            if (sendToSenateForm) {
                sendToSenateForm.addEventListener('submit', function(e) {
                    return confirm('Apakah Anda yakin ingin mengirim usulan ini ke Tim Senat untuk review final?');
                });
            } else {
                console.warn('Send to senate form submit handler not found');
            }

            // Character count handlers
            const returnTextarea = document.getElementById('catatan_umum_return');
            if (returnTextarea) {
                returnTextarea.addEventListener('input', function() {
                    const count = this.value.length;
                    const charCount = document.getElementById('charCount_return');
                    if (charCount) {
                        charCount.textContent = count;
                    }
                });
            } else {
                console.warn('Return form textarea not found for character count');
            }

            const notRecommendedTextarea = document.getElementById('catatan_umum_not_recommended');
            if (notRecommendedTextarea) {
                notRecommendedTextarea.addEventListener('input', function() {
                    const count = this.value.length;
                    const charCount = document.getElementById('charCount_not_recommended');
                    if (charCount) {
                        charCount.textContent = count;
                    }
                });
            } else {
                console.warn('Not recommended form textarea not found for character count');
            }

            // Initialize assessor selection validation
            this.validateAssessorSelection();

            console.log('Form handlers initialized successfully');
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new AdminUnivUsulan();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AdminUnivUsulan;
}
