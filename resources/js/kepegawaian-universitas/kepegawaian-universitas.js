// ========================================
// KEPEGAWAIAN UNIVERSITAS JAVASCRIPT
// ========================================

// Global JavaScript Functions
class KepegawaianUniversitas {
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
        this.initializeKepegawaianUniversitasButtons();
        this.checkAndDisableButtons();
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
        console.log('Initializing dropdowns for kepegawaian universitas...');

        // Remove any existing event listeners first
        const existingButtons = document.querySelectorAll('button[data-collapse-toggle]');
        existingButtons.forEach(btn => {
            const newBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(newBtn, btn);
        });

        // Now add new event listeners
        document.querySelectorAll('button[data-collapse-toggle]').forEach(btn => {
            console.log('Setting up dropdown button:', btn.getAttribute('data-collapse-toggle'));

            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation(); // Stop all other handlers

                const targetId = this.getAttribute('data-collapse-toggle');
                const dropdown = document.getElementById(targetId);
                const isNested = this.hasAttribute('data-nested');

                console.log('Dropdown clicked:', {
                    targetId: targetId,
                    isNested: isNested,
                    dropdown: dropdown
                });

                if (!dropdown) return;

                const isOpening = dropdown.classList.contains('hidden');

                if (isNested) {
                    // For nested dropdowns, only toggle this specific dropdown
                    console.log('Handling nested dropdown - only toggle this one');

                    // Toggle only this nested dropdown
                    if (isOpening) {
                        dropdown.classList.remove('hidden');
                        dropdown.style.maxHeight = '1000px';
                        dropdown.style.opacity = '1';
                        console.log('Nested dropdown opened');
                    } else {
                        dropdown.classList.add('hidden');
                        dropdown.style.maxHeight = '0';
                        dropdown.style.opacity = '0';
                        console.log('Nested dropdown closed');
                    }
                } else {
                    // For parent dropdowns, close other parent dropdowns
                    console.log('Handling parent dropdown - close other parents');

                    document.querySelectorAll('.dropdown-menu').forEach(otherDropdown => {
                        if (otherDropdown.id !== targetId) {
                            // Don't close dropdowns that contain nested dropdowns
                            const containsNested = otherDropdown.querySelector('.nested-dropdown-container') !== null;

                            console.log('Checking dropdown:', {
                                id: otherDropdown.id,
                                containsNested: containsNested,
                                willClose: !containsNested
                            });

                            if (!containsNested) {
                                otherDropdown.classList.add('hidden');
                                otherDropdown.style.maxHeight = '0';
                                otherDropdown.style.opacity = '0';

                                const otherButton = document.querySelector(`[data-collapse-toggle="${otherDropdown.id}"]`);
                                if (otherButton) {
                                    otherButton.setAttribute('aria-expanded', 'false');
                                    const otherChevron = otherButton.querySelector('[data-lucide="chevron-down"]');
                                    if (otherChevron) {
                                        otherChevron.classList.remove('rotate-180');
                                    }
                                }
                                console.log('Closed dropdown:', otherDropdown.id);
                            } else {
                                console.log('Kept dropdown open (contains nested):', otherDropdown.id);
                            }
                        }
                    });

                    // Toggle the current dropdown
                    if (isOpening) {
                        dropdown.classList.remove('hidden');
                        dropdown.style.maxHeight = '2000px';
                        dropdown.style.opacity = '1';
                        console.log('Parent dropdown opened');
                    } else {
                        dropdown.classList.add('hidden');
                        dropdown.style.maxHeight = '0';
                        dropdown.style.opacity = '0';
                        console.log('Parent dropdown closed');
                    }
                }

                // Rotate the icon
                const chevron = this.querySelector('[data-lucide="chevron-down"]');
                if (chevron) {
                    chevron.classList.toggle('rotate-180', isOpening);
                }

                // Update aria-expanded
                this.setAttribute('aria-expanded', isOpening ? 'true' : 'false');

                console.log('Toggled dropdown:', {
                    id: targetId,
                    isNested: isNested,
                    isNowOpen: isOpening
                });
            });
        });

        console.log('Dropdown initialization complete for kepegawaian universitas');
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

            // Initialize button handlers for Kepegawaian Universitas actions
            this.initializeKepegawaianUniversitasButtons();

            console.log('Form handlers initialized successfully');
        });
    }

    // Validate assessor selection
    validateAssessorSelection() {
        const assessorCheckboxes = document.querySelectorAll('input[name="assessor_ids[]"]');
        const submitButton = document.querySelector('#sendToAssessorFormSubmit button[type="submit"]');

        if (assessorCheckboxes.length > 0 && submitButton) {
            const updateSubmitButton = () => {
                const checkedCount = document.querySelectorAll('input[name="assessor_ids[]"]:checked').length;
                submitButton.disabled = checkedCount < 1 || checkedCount > 3;
                submitButton.textContent = checkedCount < 1 ? 'Pilih minimal 1 penilai' :
                                          checkedCount > 3 ? 'Pilih maksimal 3 penilai' :
                                          'Kirim ke Tim Penilai';
            };

            assessorCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSubmitButton);
            });

            updateSubmitButton();
        }
    }

    // Initialize Kepegawaian Universitas button handlers
    initializeKepegawaianUniversitasButtons() {
        // Button: Teruskan Perbaikan ke Pegawai
        const btnPerbaikanKePegawai = document.getElementById('btn-perbaikan-ke-pegawai');
        if (btnPerbaikanKePegawai) {
            btnPerbaikanKePegawai.addEventListener('click', (e) => {
                e.preventDefault();
                this.showPerbaikanModal('pegawai');
            });
        }

        // Button: Teruskan Perbaikan ke Fakultas
        const btnPerbaikanKeFakultas = document.getElementById('btn-perbaikan-ke-fakultas');
        if (btnPerbaikanKeFakultas) {
            btnPerbaikanKeFakultas.addEventListener('click', (e) => {
                e.preventDefault();
                this.showPerbaikanModal('fakultas');
            });
        }

        // Button: Kirim Perbaikan ke Penilai Universitas
        const btnKirimPerbaikanKePenilai = document.getElementById('btn-kirim-perbaikan-ke-penilai');
        if (btnKirimPerbaikanKePenilai) {
            btnKirimPerbaikanKePenilai.addEventListener('click', (e) => {
                e.preventDefault();
                this.showPerbaikanModal('penilai');
            });
        }

        // Button: Tidak Direkomendasikan
        const btnTidakDirekomendasikan = document.getElementById('btn-tidak-direkomendasikan');
        if (btnTidakDirekomendasikan) {
            btnTidakDirekomendasikan.addEventListener('click', (e) => {
                e.preventDefault();
                this.showPerbaikanModal('tidak_direkomendasikan');
            });
        }

        // Button: Kirim Ke Senat
        const btnKirimKeSenat = document.getElementById('btn-kirim-ke-senat');
        if (btnKirimKeSenat) {
            btnKirimKeSenat.addEventListener('click', (e) => {
                e.preventDefault();
                this.showPerbaikanModal('senat');
            });
        }
    }

    // Show perbaikan modal and handle form submission
    showPerbaikanModal(actionType) {
        // Get usulan ID from current page
        const usulanId = this.getUsulanIdFromUrl();
        if (!usulanId) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Usulan ID tidak ditemukan!',
                confirmButtonColor: '#dc2626'
            });
            return;
        }

        // Create modal content based on action type
        let modalTitle, modalPlaceholder, actionUrl, modalIcon, confirmButtonColor;

        switch (actionType) {
            case 'pegawai':
                modalTitle = 'Teruskan Perbaikan ke Pegawai';
                modalPlaceholder = 'Masukkan catatan perbaikan untuk pegawai...';
                actionUrl = `/kepegawaian-universitas/usulan/${usulanId}/save-validation`;
                modalIcon = 'info';
                confirmButtonColor = '#3b82f6';
                break;

            case 'fakultas':
                modalTitle = 'Teruskan Perbaikan ke Fakultas';
                modalPlaceholder = 'Masukkan catatan perbaikan untuk admin fakultas...';
                actionUrl = `/kepegawaian-universitas/usulan/${usulanId}/save-validation`;
                modalIcon = 'info';
                confirmButtonColor = '#8b5cf6';
                break;

            case 'penilai':
                modalTitle = 'Kirim Perbaikan ke Tim Penilai';
                modalPlaceholder = 'Masukkan catatan perbaikan untuk tim penilai...';
                actionUrl = `/kepegawaian-universitas/usulan/${usulanId}/save-validation`;
                modalIcon = 'warning';
                confirmButtonColor = '#f59e0b';
                break;

            case 'tidak_direkomendasikan':
                modalTitle = 'Tidak Direkomendasikan';
                modalPlaceholder = 'Masukkan alasan mengapa usulan tidak direkomendasikan...';
                actionUrl = `/kepegawaian-universitas/usulan/${usulanId}/save-validation`;
                modalIcon = 'error';
                confirmButtonColor = '#dc2626';
                break;

            case 'senat':
                modalTitle = 'Kirim ke Tim Senat';
                modalPlaceholder = 'Masukkan catatan untuk tim senat...';
                actionUrl = `/kepegawaian-universitas/usulan/${usulanId}/save-validation`;
                modalIcon = 'success';
                confirmButtonColor = '#059669';
                break;
        }

        // Show SweetAlert2 modal with improved design
        Swal.fire({
            icon: modalIcon,
            title: modalTitle,
            html: `
                <div class="text-left">
                    <p class="text-gray-600 mb-4">Silakan masukkan catatan atau alasan untuk tindakan ini:</p>
                </div>
            `,
            input: 'textarea',
            inputPlaceholder: modalPlaceholder,
            inputAttributes: {
                'aria-label': 'Catatan perbaikan',
                'required': 'required',
                'rows': '4',
                'style': 'min-height: 100px; resize: vertical; border-radius: 8px; border: 2px solid #e5e7eb; padding: 12px; font-size: 14px;'
            },
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-paper-plane mr-2"></i>Kirim',
            cancelButtonText: '<i class="fas fa-times mr-2"></i>Batal',
            confirmButtonColor: confirmButtonColor,
            cancelButtonColor: '#6b7280',
            showLoaderOnConfirm: true,
            allowOutsideClick: () => !Swal.isLoading(),
            customClass: {
                popup: 'swal2-custom-popup',
                confirmButton: 'swal2-confirm-custom',
                cancelButton: 'swal2-cancel-custom',
                input: 'swal2-input-custom'
            },
            didOpen: () => {
                // Add custom styles
                const style = document.createElement('style');
                style.textContent = `
                    .swal2-custom-popup {
                        border-radius: 16px !important;
                        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
                        animation: fadeInDown 0.3s ease-out !important;
                    }
                    .swal2-confirm-custom {
                        border-radius: 8px !important;
                        font-weight: 600 !important;
                        padding: 12px 24px !important;
                        transition: all 0.2s ease !important;
                    }
                    .swal2-confirm-custom:hover {
                        transform: translateY(-1px) !important;
                        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
                    }
                    .swal2-cancel-custom {
                        border-radius: 8px !important;
                        font-weight: 600 !important;
                        padding: 12px 24px !important;
                        transition: all 0.2s ease !important;
                    }
                    .swal2-cancel-custom:hover {
                        transform: translateY(-1px) !important;
                        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
                    }
                    .swal2-input-custom {
                        border-radius: 8px !important;
                        border: 2px solid #e5e7eb !important;
                        padding: 12px !important;
                        font-size: 14px !important;
                        transition: border-color 0.2s ease !important;
                    }
                    .swal2-input-custom:focus {
                        border-color: ${confirmButtonColor} !important;
                        box-shadow: 0 0 0 3px ${confirmButtonColor}20 !important;
                        outline: none !important;
                    }
                    @keyframes fadeInDown {
                        from {
                            opacity: 0;
                            transform: translateY(-20px);
                        }
                        to {
                            opacity: 1;
                            transform: translateY(0);
                        }
                    }
                `;
                document.head.appendChild(style);
            },
            preConfirm: (catatan) => {
                if (!catatan || catatan.trim() === '') {
                    Swal.showValidationMessage('<i class="fas fa-exclamation-triangle text-red-500"></i> Catatan wajib diisi');
                    return false;
                }
                if (catatan.trim().length < 10) {
                    Swal.showValidationMessage('<i class="fas fa-exclamation-triangle text-yellow-500"></i> Catatan minimal 10 karakter');
                    return false;
                }
                return this.submitPerbaikanForm(actionUrl, catatan, actionType);
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Success handled in submitPerbaikanForm
            }
        });
    }



    // Submit perbaikan form
    async submitPerbaikanForm(actionUrl, catatan, actionType) {
        try {
            const formData = new FormData();
            formData.append('catatan_verifikator', catatan);
            formData.append('action_type', actionType);

            const response = await window.fetchWithCsrf(actionUrl, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: '<i class="fas fa-check-circle text-green-500"></i> Berhasil!',
                    html: `
                        <div class="text-center">
                            <p class="text-gray-700 mb-3">Aksi telah berhasil dilakukan!</p>
                            <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-left">
                                <p class="text-sm text-green-800"><strong>Catatan yang dikirim:</strong></p>
                                <p class="text-sm text-green-700 mt-1">"${catatan}"</p>
                            </div>
                        </div>
                    `,
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'animated fadeInUp'
                    }
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: '<i class="fas fa-exclamation-triangle text-red-500"></i> Gagal!',
                    html: `
                        <div class="text-center">
                            <p class="text-gray-700 mb-3">${result.message || 'Terjadi kesalahan saat memproses permintaan'}</p>
                            <button id="retry-btn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                <i class="fas fa-redo mr-2"></i>Coba Lagi
                            </button>
                        </div>
                    `,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'animated fadeInDown'
                    }
                });

                // Add retry functionality
                document.getElementById('retry-btn')?.addEventListener('click', () => {
                    Swal.close();
                    this.submitPerbaikanForm(actionUrl, catatan, actionType);
                });
            }
        } catch (error) {
            console.error('Error submitting form:', error);
            Swal.fire({
                icon: 'error',
                title: '<i class="fas fa-exclamation-triangle text-red-500"></i> Error Koneksi!',
                html: `
                    <div class="text-center">
                        <p class="text-gray-700 mb-3">Terjadi kesalahan koneksi saat mengirim data</p>
                        <p class="text-sm text-gray-500 mb-3">Error: ${error.message}</p>
                        <button id="retry-btn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                            <i class="fas fa-redo mr-2"></i>Coba Lagi
                        </button>
                    </div>
                `,
                showConfirmButton: false,
                customClass: {
                    popup: 'animated fadeInDown'
                }
            });

            // Add retry functionality
            document.getElementById('retry-btn')?.addEventListener('click', () => {
                Swal.close();
                this.submitPerbaikanForm(actionUrl, catatan, actionType);
            });
        }
    }

    // Check and disable buttons based on recent actions
    checkAndDisableButtons() {
        // Get usulan data from the page
        const usulanData = this.getUsulanDataFromPage();

        if (!usulanData) return;

        const validasiData = usulanData.validasi_data || {};
        const adminUniversitasData = validasiData.admin_universitas || {};

        // Check for recent actions that should disable buttons
        const recentActions = ['perbaikan_ke_pegawai', 'perbaikan_ke_fakultas'];
        let hasRecentAction = false;

        recentActions.forEach(action => {
            if (adminUniversitasData[action]) {
                hasRecentAction = true;
                console.log(`Found recent action: ${action}`);
            }
        });

        if (hasRecentAction) {
            // Disable the buttons
            const buttonsToDisable = [
                'btn-perbaikan-ke-pegawai',
                'btn-perbaikan-ke-fakultas'
            ];

            buttonsToDisable.forEach(buttonId => {
                const button = document.getElementById(buttonId);
                if (button) {
                    button.disabled = true;
                    button.classList.add('opacity-50', 'cursor-not-allowed');
                    button.classList.remove('hover:bg-red-700', 'hover:bg-amber-700');

                    // Add tooltip or visual indicator
                    button.title = 'Button dinonaktifkan karena sudah ada aksi perbaikan yang dikirim';

                    console.log(`Disabled button: ${buttonId}`);
                }
            });
        }
    }

    // Get usulan data from the page (from a data attribute or script tag)
    getUsulanDataFromPage() {
        // Try to get data from a script tag with usulan data
        const usulanDataScript = document.querySelector('script[data-usulan]');
        if (usulanDataScript) {
            try {
                return JSON.parse(usulanDataScript.textContent);
            } catch (e) {
                console.error('Error parsing usulan data:', e);
            }
        }

        // Try to get data from a data attribute
        const usulanDataElement = document.querySelector('[data-usulan]');
        if (usulanDataElement) {
            try {
                return JSON.parse(usulanDataElement.getAttribute('data-usulan'));
            } catch (e) {
                console.error('Error parsing usulan data from attribute:', e);
            }
        }

        // If no structured data found, try to extract from the page content
        // This is a fallback method
        return this.extractUsulanDataFromPageContent();
    }

    // Extract usulan data from page content as fallback
    extractUsulanDataFromPageContent() {
        // This is a simplified extraction - in a real implementation,
        // you might want to add a proper data attribute to the page
        const usulanId = this.getUsulanIdFromUrl();
        if (!usulanId) return null;

        // For now, we'll assume the page has the necessary data
        // In a real implementation, you might want to make an AJAX call
        // to get the current usulan data
        return {
            id: usulanId,
            // Other data would be populated from the server
        };
    }

    // Get usulan ID from URL
    getUsulanIdFromUrl() {
        const path = window.location.pathname;
        const matches = path.match(/\/usulan\/(\d+)/);
        return matches ? matches[1] : null;
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new KepegawaianUniversitas();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = KepegawaianUniversitas;
}
