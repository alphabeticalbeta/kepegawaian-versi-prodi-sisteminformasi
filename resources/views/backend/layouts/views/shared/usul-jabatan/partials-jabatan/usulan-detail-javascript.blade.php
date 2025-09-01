{{-- JavaScript Section --}}
<script>
// Standardized save functionality using submitAction

function showAutoSaveStatus(status) {
    const feedback = document.getElementById('action-feedback');
    let message = '';
    let className = '';

    switch (status) {
        case 'success':
            message = 'Data tersimpan otomatis';
            className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded';
            break;
        case 'auth_error':
            message = 'Sesi login telah berakhir. Silakan refresh halaman dan login kembali.';
            className = 'bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded';
            break;
        case 'unauthorized':
            message = 'Anda tidak memiliki akses untuk menyimpan data ini.';
            className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded';
            break;
        case 'no_action':
            message = 'Silakan pilih aksi yang akan dilakukan (Simpan, Kembalikan, atau Teruskan).';
            className = 'bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded';
            break;
        case 'error':
        default:
            message = 'Gagal menyimpan data. Silakan coba lagi.';
            className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded';
            break;
    }

    feedback.innerHTML = `<div class="${className}">${message}</div>`;
    feedback.classList.remove('hidden');

    // Auto-hide after 5 seconds for auth errors, 3 seconds for others
    const hideDelay = (status === 'auth_error') ? 5000 : 3000;
    setTimeout(() => {
        feedback.classList.add('hidden');
    }, hideDelay);
}

// Event listeners for manual save only
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM Content Loaded - Initializing JavaScript...');

    // Smart form submission handling
    const actionForm = document.getElementById('action-form');
    if (actionForm) {
        console.log('✅ Action form found, setting up smart submission handling');

        actionForm.addEventListener('submit', function(e) {
            const actionType = document.getElementById('action_type')?.value;
            console.log('🔄 Form submission triggered with action_type:', actionType);

            // Only prevent submission if action_type is 'save_only' (no action selected)
            if (actionType === 'save_only') {
                e.preventDefault();
                console.log('❌ Form submission prevented - no action selected');
                showAutoSaveStatus('no_action');
                return false;
            }

            // Show loading state for valid actions
            const submitBtn = actionForm.querySelector('button[type="submit"]');
            if (submitBtn && actionType !== 'save_only') {
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <span class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Memproses...
                    </span>
                `;

                // Set flag for success handling
                sessionStorage.setItem('form_processing', 'true');
            }

            // Allow submission for valid actions
            console.log('✅ Form submission allowed for action:', actionType);
        });

        // Prevent accidental form submission on Enter key only in specific fields
        actionForm.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                const target = e.target;
                // Only prevent Enter submission in validation fields, not in action buttons
                if (target.classList.contains('validation-status') ||
                    target.classList.contains('validation-keterangan') ||
                    target.classList.contains('form-control')) {
                e.preventDefault();
                    console.log('❌ Enter key submission prevented in validation field');
                return false;
                }
            }
        });

        // Initialize form with safe defaults
        const actionTypeField = document.getElementById('action_type');
        const catatanUmumField = document.getElementById('catatan_umum');

        if (actionTypeField) {
            actionTypeField.value = 'save_only';
            console.log('✅ Action type initialized to save_only');
        }

        if (catatanUmumField) {
            catatanUmumField.value = '';
            console.log('✅ Catatan umum initialized to empty');
        }

        console.log('✅ Form initialization completed');
    } else {
        console.log('⚠️ Action form not found');
    }

    // Manual save only - no auto-save
    console.log('🚫 Auto-save disabled - manual save only');

    // Initialize validation status handlers with robust approach
    initializeValidationHandlers();
});

// Robust validation handler initialization
function initializeValidationHandlers() {
    console.log('🔧 Initializing validation handlers...');

    // Use a more robust approach with event delegation
    const validationStatusElements = document.querySelectorAll('.validation-status');
    console.log('📊 Found validation status elements:', validationStatusElements.length);

    if (validationStatusElements.length === 0) {
        console.log('ℹ️ No validation status elements found - this is normal for document-only forms');
        return;
    }

    validationStatusElements.forEach((select, index) => {
        console.log(`📝 Setting up handler for validation status ${index + 1}:`, select.name || select.id || 'unnamed');

        // Remove any existing listeners to prevent duplication
        select.removeEventListener('change', handleStatusChange);

        // Add new listener
        select.addEventListener('change', handleStatusChange);

        // Initialize current state
        const row = select.closest('tr');
        const keterangan = row?.querySelector('.validation-keterangan');
        if (keterangan) {
            const isInvalid = select.value === 'tidak_sesuai';
            keterangan.disabled = !isInvalid;
            keterangan.classList.toggle('bg-gray-100', !isInvalid);

            if (isInvalid) {
                keterangan.placeholder = 'Jelaskan mengapa item ini tidak sesuai...';
            } else {
                keterangan.placeholder = 'Keterangan (wajib jika tidak sesuai)';
            }
        }
    });

    console.log('✅ Validation handlers initialized successfully');
}

// Centralized status change handler
function handleStatusChange() {
    const row = this.closest('tr');
    const keterangan = row?.querySelector('.validation-keterangan');
    const isInvalid = this.value === 'tidak_sesuai';

    console.log('🔄 Status changed for field:', this.name, 'to:', this.value);
    console.log('  - Is invalid:', isInvalid);
    console.log('  - Keterangan element found:', !!keterangan);

    if (keterangan) {
        keterangan.disabled = !isInvalid;
        keterangan.classList.toggle('bg-gray-100', !isInvalid);

        if (isInvalid) {
            keterangan.placeholder = 'Jelaskan mengapa item ini tidak sesuai...';
            keterangan.focus();
        } else {
            keterangan.placeholder = 'Keterangan (wajib jika tidak sesuai)';
            keterangan.value = ''; // Clear keterangan when status is 'sesuai'
        }

        console.log('  - Keterangan disabled:', keterangan.disabled);
        console.log('  - Keterangan has bg-gray-100:', keterangan.classList.contains('bg-gray-100'));
    } else {
        console.log('❌ Keterangan element not found for field:', this.name);
    }
}

// DISABLED: Old action button handlers that cause infinite loop
// function initializeButtonHandlers() {
//     // This is disabled - new button handlers are at the end of script
// }

// DISABLED: Initialize button handlers after DOM is loaded
// document.addEventListener('DOMContentLoaded', function() {
//     // Initialize button handlers - DISABLED to prevent infinite loop
//     initializeButtonHandlers();
// });

// Additional button handlers for other roles
const additionalButtons = [
    { id: 'btn-perbaikan-universitas-pegawai', handler: () => showPerbaikanKePegawaiModal() },
    { id: 'btn-perbaikan-universitas-fakultas', handler: () => showPerbaikanKeFakultasModal() },
    { id: 'btn-perbaikan-penilai-universitas-pegawai', handler: () => showPerbaikanKePegawaiModal() },
    { id: 'btn-perbaikan-penilai-universitas-fakultas', handler: () => showPerbaikanKeFakultasModal() },
    { id: 'btn-kirim-perbaikan-ke-penilai', handler: () => showKirimPerbaikanKePenilaiModal() },
    // Penilai Universitas buttons
    { id: 'btn-autosave-penilai', handler: () => submitAction('autosave', '') },
    { id: 'btn-rekomendasikan-penilai', handler: () => showRekomendasiPenilaiModal() },
    { id: 'btn-perbaikan-penilai', handler: () => showPerbaikanPenilaiModal() },
    { id: 'btn-tidak-rekomendasikan-penilai', handler: () => showTidakRekomendasiPenilaiModal() },
    // Forward to assessor opens the hidden form modal if available on the page
    { id: 'btn-teruskan-penilai', handler: () => {
        if (typeof showSendToAssessorForm === 'function') {
            showSendToAssessorForm();
        } else {
            // Fallback: try to toggle the hidden form directly
            const form = document.getElementById('sendToAssessorForm');
            if (form) {
                form.classList.remove('hidden');
            } else {
                console.warn('sendToAssessorForm not found and showSendToAssessorForm() is undefined');
            }
        }
    } },
    { id: 'btn-tambah-penilai', handler: () => showTambahPenilaiModal() },
    { id: 'btn-tugaskan-penilai', handler: () => showTambahPenilaiModal() },
    { id: 'btn-simpan-validasi-top', handler: () => submitAction('save_only', '') },
    { id: 'btn-simpan-validasi-bottom', handler: () => submitAction('save_only', '') },
    { id: 'btn-simpan-validasi-kepegawaian', handler: () => submitAction('save_only', '') }
];

// Note: Additional buttons will be initialized after all functions are defined

// Utilities for assessor modal (available when hidden forms are included)
if (typeof window.filterAssessorList !== 'function') {
    window.filterAssessorList = function() {
        const input = document.getElementById('assessor-filter');
        const term = (input?.value || '').toLowerCase();
        const items = document.querySelectorAll('#assessor-list .assessor-item');
        let visible = 0;
        items.forEach(el => {
            const name = (el.getAttribute('data-name') || '').toLowerCase();
            const match = name.includes(term);
            el.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        const empty = document.getElementById('assessor-empty');
        if (empty) empty.classList.toggle('hidden', visible !== 0);
    }
}

if (typeof window.validateAssessorSelection !== 'function') {
    window.validateAssessorSelection = function() {
        const checkboxes = document.querySelectorAll('input[name="assessor_ids[]"]:checked');
        const count = checkboxes.length;
        const submitBtn = document.getElementById('submitAssessorBtn');
        const countDisplay = document.getElementById('assessorCount');
        if (countDisplay) countDisplay.textContent = count;
        if (submitBtn) {
            if (count >= 1 && count <= 3) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('bg-gray-300', 'cursor-not-allowed');
                submitBtn.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.add('bg-gray-300', 'cursor-not-allowed');
                submitBtn.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
            }
        }
    }
}

if (document.getElementById('btn-kirim-sister')) {
    document.getElementById('btn-kirim-sister').addEventListener('click', function() {
        if (!this.disabled) {
            showTeruskanKeSenaModal();
        }
    });
}

if (document.getElementById('btn-kembalikan-dari-penilai')) {
    document.getElementById('btn-kembalikan-dari-penilai').addEventListener('click', function() {
        showKembalikanDariPenilaiModal();
    });
}

// Admin Fakultas specific button for resending to university
if (document.getElementById('btn-kirim-perbaikan-penilai-ke-universitas')) {
    document.getElementById('btn-kirim-perbaikan-penilai-ke-universitas').addEventListener('click', function() {
        showResubmitUniversityModal();
    });
}

// Admin Fakultas specific button for submitting to university
if (document.getElementById('btn-submit-university')) {
    document.getElementById('btn-submit-university').addEventListener('click', function() {
        showSubmitUniversityModal();
    });
}

// Admin Fakultas specific button for resubmitting to university
if (document.getElementById('btn-resubmit-university')) {
    document.getElementById('btn-resubmit-university').addEventListener('click', function() {
        showResubmitUniversityModal();
    });
}

// Pegawai specific buttons with unique IDs
if (document.getElementById('btn-kirim-perbaikan-admin-fakultas')) {
    document.getElementById('btn-kirim-perbaikan-admin-fakultas').addEventListener('click', function() {
        showKirimPerbaikanModal();
    });
}

if (document.getElementById('btn-kirim-perbaikan-kepegawaian')) {
    document.getElementById('btn-kirim-perbaikan-kepegawaian').addEventListener('click', function() {
        showKirimPerbaikanModal();
    });
}

if (document.getElementById('btn-kirim-perbaikan-penilai')) {
    document.getElementById('btn-kirim-perbaikan-penilai').addEventListener('click', function() {
        showKirimPerbaikanModal();
    });
}

if (document.getElementById('btn-kirim-perbaikan-sister')) {
    document.getElementById('btn-kirim-perbaikan-sister').addEventListener('click', function() {
        showKirimPerbaikanModal();
    });
}

// Penilai Universitas specific buttons
if (document.getElementById('btn-kembali-penilai-readonly')) {
    document.getElementById('btn-kembali-penilai-readonly').addEventListener('click', function() {
        window.history.back();
    });
}

// Modal functions
function showKirimPerbaikanModal() {
    Swal.fire({
        title: 'Kirim Perbaikan',
        text: 'Usulan akan dikirim kembali untuk diproses selanjutnya. Silakan berikan catatan perbaikan (opsional).',
        input: 'textarea',
        inputPlaceholder: 'Catatan perbaikan (opsional)...',
        inputAttributes: {
            'aria-label': 'Catatan perbaikan'
        },
        showCancelButton: true,
        confirmButtonText: 'Kirim Perbaikan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#2563eb'
            }).then((result) => {
            if (result.isConfirmed) {
                submitAction('return_to_pegawai', result.value || '');
            }
        });
}

function showPerbaikanModal() {
    // Implementation for perbaikan modal based on role
    const currentRole = '{{ $currentRole ?? "" }}';

    if (currentRole === 'Penilai Universitas') {
        Swal.fire({
            title: 'Perbaikan ke Admin Universitas',
            text: 'Usulan akan dikirim ke Admin Universitas untuk review. Silakan berikan catatan perbaikan yang detail.',
            input: 'textarea',
            inputPlaceholder: 'Masukkan catatan perbaikan untuk Admin Universitas...',
            inputAttributes: {
                'aria-label': 'Catatan perbaikan'
            },
            showCancelButton: true,
            confirmButtonText: 'Kirim ke Admin Univ',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d97706',
            preConfirm: (catatan) => {
                if (!catatan || catatan.trim() === '') {
                    Swal.showValidationMessage('Catatan perbaikan wajib diisi');
                    return false;
                }
                return catatan;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                submitAction('return_to_pegawai', result.value);
            }
        });
    } else if (currentRole === 'Admin Fakultas') {
        Swal.fire({
            title: 'Perbaikan ke Pegawai',
            text: 'Usulan akan dikembalikan ke pegawai untuk perbaikan. Silakan berikan catatan perbaikan yang detail.',
            input: 'textarea',
            inputPlaceholder: 'Masukkan catatan perbaikan untuk pegawai...',
            inputAttributes: {
                'aria-label': 'Catatan perbaikan'
            },
            showCancelButton: true,
            confirmButtonText: 'Kembalikan ke Pegawai',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d97706',
            preConfirm: (catatan) => {
                if (!catatan || catatan.trim() === '') {
                    Swal.showValidationMessage('Catatan perbaikan wajib diisi');
                    return false;
                }
                return catatan;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                submitAction('return_to_pegawai', result.value);
            }
        });
    } else {
        console.log('Show perbaikan modal for', currentRole);
    }
}

function showForwardModal() {
    // Implementation for forward modal based on role
    const currentRole = '{{ $currentRole ?? "" }}';

    if (currentRole === 'Penilai Universitas') {
        Swal.fire({
            title: 'Rekomendasikan Usulan',
            text: 'Usulan akan direkomendasikan ke Tim Senat. Silakan berikan catatan rekomendasi (opsional).',
            input: 'textarea',
            inputPlaceholder: 'Masukkan catatan rekomendasi (opsional)...',
            inputAttributes: {
                'aria-label': 'Catatan rekomendasi'
            },
            showCancelButton: true,
            confirmButtonText: 'Rekomendasikan ke Senat',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#7c3aed'
        }).then((result) => {
            if (result.isConfirmed) {
                submitAction('forward_to_university', result.value || '');
            }
        });
    } else if (currentRole === 'Admin Fakultas') {
        console.log('Admin Fakultas showForwardModal called');

        Swal.fire({
            title: 'Kirim Usulan Ke Kepegawaian Universitas',
            text: 'Apakah Anda yakin ingin mengirim usulan ke Kepegawaian Universitas?',
            input: 'textarea',
            inputPlaceholder: 'Catatan untuk Kepegawaian Universitas (opsional)...',
            inputAttributes: {
                'aria-label': 'Catatan untuk Kepegawaian Universitas'
            },
            showCancelButton: true,
            confirmButtonText: 'Kirim Ke Kepegawaian Universitas',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#2563eb',
            preConfirm: (catatan) => {
                // Completely flexible validation - no blocking at all
                const nomorSurat = document.querySelector('input[name="dokumen_pendukung[nomor_surat_usulan]"]')?.value;
                const fileSurat = document.querySelector('input[name="dokumen_pendukung[file_surat_usulan]"]')?.files[0];
                const nomorBerita = document.querySelector('input[name="dokumen_pendukung[nomor_berita_senat]"]')?.value;
                const fileBerita = document.querySelector('input[name="dokumen_pendukung[file_berita_senat]"]')?.files[0];

                // Optional info display (not warnings)
                let info = [];

                if (document.querySelector('input[name="dokumen_pendukung[nomor_surat_usulan]"]')) {
                    if (!nomorSurat) {
                        info.push('• Nomor Surat Usulan belum diisi');
                    }
                    if (!fileSurat) {
                        info.push('• File Surat Usulan belum diunggah');
                    }
                    if (!nomorBerita) {
                        info.push('• Nomor Berita Senat belum diisi');
                    }
                    if (!fileBerita) {
                        info.push('• File Berita Senat belum diunggah');
                    }
                }

                // If there are missing items, show info but always allow continuation
                if (info.length > 0) {
                    Swal.showValidationMessage(
                        'Informasi:\n' + info.join('\n') + '\n\nAnda dapat melanjutkan pengiriman usulan.'
                    );
                }

                return catatan || '';
            }
        }).then((result) => {
            if (result.isConfirmed) {
                submitAction('submit_university', result.value);
            }
        });
    } else {
        console.log('Show forward modal for', currentRole);
    }
}

function showSubmitUniversityModal() {
    Swal.fire({
        title: 'Kirim Usulan Ke Kepegawaian Universitas',
        text: 'Apakah Anda yakin ingin mengirim usulan ke Kepegawaian Universitas?',
        input: 'textarea',
        inputPlaceholder: 'Catatan untuk Kepegawaian Universitas (opsional)...',
        inputAttributes: {
            'aria-label': 'Catatan untuk Kepegawaian Universitas'
        },
        showCancelButton: true,
        confirmButtonText: 'Kirim Ke Kepegawaian Universitas',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#2563eb'
    }).then((result) => {
        if (result.isConfirmed) {
            submitAction('submit_university', result.value || '');
        }
    });
}

function showResubmitUniversityModal() {
    Swal.fire({
        title: 'Kirim Usulan Perbaikan Ke Kepegawaian Universitas',
        text: 'Apakah Anda yakin ingin mengirim usulan perbaikan ke Kepegawaian Universitas?',
        input: 'textarea',
        inputPlaceholder: 'Catatan untuk Kepegawaian Universitas (opsional)...',
        inputAttributes: {
            'aria-label': 'Catatan untuk Kepegawaian Universitas'
        },
        showCancelButton: true,
        confirmButtonText: 'Kirim Perbaikan Ke Kepegawaian Universitas',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#2563eb'
    }).then((result) => {
        if (result.isConfirmed) {
            submitAction('resend_to_university', result.value || '');
        }
    });
}

function showPerbaikanKeFakultasModal() {
    Swal.fire({
        title: 'Permintaan Perbaikan Penilai Ke Admin Fakultas',
        html: `
            <div class="text-left">
                <div class="mb-4">
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 mr-2"></i>
                            <span class="text-sm text-amber-800">
                                <strong>Peringatan:</strong> Permintaan perbaikan dari Penilai akan diteruskan ke Admin Fakultas.
                            </span>
                        </div>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i data-lucide="info" class="w-5 h-5 text-blue-600 mr-2"></i>
                            <div class="text-sm text-blue-800">
                                <strong>Informasi:</strong>
                                <ul class="mt-2 list-disc list-inside space-y-1">
                                    <li>Admin Fakultas akan menerima notifikasi perbaikan</li>
                                    <li>Usulan akan kembali ke status "Permintaan Perbaikan dari Admin Fakultas"</li>
                                    <li>Admin Fakultas dapat memperbaiki dan mengirim kembali</li>
                                    <li>Proses dapat berulang hingga usulan sesuai standar</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i data-lucide="message-square" class="w-4 h-4 inline mr-1"></i>
                        Catatan Perbaikan untuk Admin Fakultas:
                    </label>
                    <textarea id="catatan-perbaikan-fakultas"
                              placeholder="Jelaskan detail perbaikan yang diperlukan oleh Admin Fakultas..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 resize-none"
                              rows="4"></textarea>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Kirim Permintaan Perbaikan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d97706',
        width: '600px',
        preConfirm: () => {
            const catatan = document.getElementById('catatan-perbaikan-fakultas').value;
            if (!catatan || catatan.trim() === '') {
                Swal.showValidationMessage('Catatan perbaikan wajib diisi untuk menjelaskan perbaikan yang diperlukan');
                return false;
            }
            if (catatan.trim().length < 10) {
                Swal.showValidationMessage('Catatan perbaikan minimal 10 karakter untuk memberikan penjelasan yang jelas');
                return false;
            }
            return catatan.trim();
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show confirmation dialog
            Swal.fire({
                title: 'Konfirmasi Permintaan Perbaikan',
                html: `
                    <div class="text-center">
                        <div class="mb-4">
                            <i data-lucide="alert-circle" class="w-16 h-16 text-amber-500 mx-auto mb-4"></i>
                            <p class="text-lg font-semibold text-gray-800 mb-2">Apakah Anda yakin?</p>
                            <p class="text-sm text-gray-600 mb-4">
                                Usulan akan dikirim kembali ke Admin Fakultas dengan status "Permintaan Perbaikan dari Admin Fakultas"
                            </p>
                        </div>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-left">
                            <p class="text-sm text-gray-700"><strong>Catatan yang akan dikirim:</strong></p>
                            <p class="text-sm text-gray-600 mt-1">"${result.value}"</p>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Ya, Kirim Permintaan Perbaikan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d97706',
                width: '500px'
            }).then((finalResult) => {
                if (finalResult.isConfirmed) {
                    // AUTO-SAVE + SEND: Simpan validasi dan kirim perbaikan
                    const form = document.getElementById('action-form');

                    // Set catatan_verifikator
                    const catatanVerifikatorInput = document.createElement('input');
                    catatanVerifikatorInput.type = 'hidden';
                    catatanVerifikatorInput.name = 'catatan_verifikator';
                    catatanVerifikatorInput.value = result.value;
                    form.appendChild(catatanVerifikatorInput);

                    // Auto-save validasi + send perbaikan
                    submitAction('perbaikan_penilai_ke_fakultas', '');
                }
            });
        }
    });
}

function showPerbaikanKePegawaiModal() {
    Swal.fire({
        title: 'Permintaan Perbaikan Penilai Ke Pegawai',
        html: `
            <div class="text-left">
                <div class="mb-4">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600 mr-2"></i>
                            <span class="text-sm text-red-800">
                                <strong>Peringatan:</strong> Permintaan perbaikan dari Penilai akan diteruskan ke Pegawai.
                            </span>
                        </div>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i data-lucide="info" class="w-5 h-5 text-blue-600 mr-2"></i>
                            <div class="text-sm text-blue-800">
                                <strong>Informasi:</strong>
                                <ul class="mt-2 list-disc list-inside space-y-1">
                                    <li>Pegawai akan menerima notifikasi perbaikan</li>
                                    <li>Usulan akan kembali ke status "Permintaan Perbaikan Ke Pegawai Dari Penilai"</li>
                                    <li>Pegawai dapat memperbaiki dan mengirim kembali</li>
                                    <li>Proses dapat berulang hingga usulan sesuai standar</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i data-lucide="message-square" class="w-4 h-4 inline mr-1"></i>
                        Catatan Perbaikan untuk Pegawai:
                    </label>
                    <textarea id="catatan-perbaikan-pegawai"
                              placeholder="Jelaskan detail perbaikan yang diperlukan oleh pegawai..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none"
                              rows="4"></textarea>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Kirim Permintaan Perbaikan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc2626',
        width: '600px',
        preConfirm: () => {
            const catatan = document.getElementById('catatan-perbaikan-pegawai').value;
            if (!catatan || catatan.trim() === '') {
                Swal.showValidationMessage('Catatan perbaikan wajib diisi untuk menjelaskan perbaikan yang diperlukan');
                return false;
            }
            if (catatan.trim().length < 10) {
                Swal.showValidationMessage('Catatan perbaikan minimal 10 karakter untuk memberikan penjelasan yang jelas');
                return false;
            }
            return catatan.trim();
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show confirmation dialog
            Swal.fire({
                title: 'Konfirmasi Permintaan Perbaikan',
                html: `
                    <div class="text-center">
                        <div class="mb-4">
                            <i data-lucide="alert-circle" class="w-16 h-16 text-red-500 mx-auto mb-4"></i>
                            <p class="text-lg font-semibold text-gray-800 mb-2">Apakah Anda yakin?</p>
                            <p class="text-sm text-gray-600 mb-4">
                                Usulan akan dikirim kembali ke Pegawai dengan status "Permintaan Perbaikan Ke Pegawai Dari Penilai"
                            </p>
                        </div>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-left">
                            <p class="text-sm text-gray-700"><strong>Catatan yang akan dikirim:</strong></p>
                            <p class="text-sm text-gray-600 mt-1">"${result.value}"</p>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Ya, Kirim Permintaan Perbaikan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626',
                width: '500px'
            }).then((finalResult) => {
                if (finalResult.isConfirmed) {
                    // AUTO-SAVE + SEND: Simpan validasi dan kirim perbaikan
                    const form = document.getElementById('action-form');

                    // Set catatan_verifikator
                    const catatanVerifikatorInput = document.createElement('input');
                    catatanVerifikatorInput.type = 'hidden';
                    catatanVerifikatorInput.name = 'catatan_verifikator';
                    catatanVerifikatorInput.value = result.value;
                    form.appendChild(catatanVerifikatorInput);

                    // Auto-save validasi + send perbaikan
                    submitAction('perbaikan_penilai_ke_pegawai', '');
                }
            });
        }
    });
}



function submitAction(actionType, catatan) {
    console.log('submitAction called with:', { actionType, catatan });

    // Additional validation for forward_to_penilai action
    if (actionType === 'forward_to_penilai') {
        const selectedPenilais = document.querySelectorAll('input[name="selected_penilais[]"]:checked');
        if (selectedPenilais.length === 0) {
            Swal.fire({
                title: '❌ Penilai Belum Dipilih',
                text: 'Silakan pilih minimal 1 penilai terlebih dahulu.',
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#2563eb'
            });
            return;
        }
    }

    const form = document.getElementById('action-form');
    const actionInput = document.getElementById('action_type');
    const catatanInput = document.getElementById('catatan_umum');

    actionInput.value = actionType;
    catatanInput.value = catatan;

    // Show loading
    Swal.fire({
        title: 'Memproses...',
        text: 'Sedang memproses aksi Anda',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    // Submit form - now all input fields are already in the form
    const formData = new FormData(form);

    console.log('Submitting form to:', form.action);
    console.log('Action type:', actionType);
    console.log('Form action URL:', form.action);
    console.log('CSRF token:', document.querySelector('input[name="_token"]')?.value);

    // Debug form data
    console.log('FormData entries:');
    for (let [key, value] of formData.entries()) {
        console.log(`  ${key}: ${value}`);
    }

    // Additional debugging for validation fields
    console.log('Validation fields found in form:');
    document.querySelectorAll('select[name^="validation["], textarea[name^="validation["]').forEach(field => {
        console.log(`  ${field.name}: ${field.value}`);
    });

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success notification with enhanced styling
            Swal.fire({
                title: '🎉 Berhasil!',
                html: `
                    <div class="text-center">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-6xl text-green-500"></i>
                        </div>
                        <p class="text-lg font-semibold text-gray-800 mb-2">${data.message}</p>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-4">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                Usulan telah berhasil diproses dan status telah diperbarui.
                            </p>
                        </div>
                    </div>
                `,
                icon: 'success',
                confirmButtonText: 'Lanjutkan',
                confirmButtonColor: '#10b981',
                allowOutsideClick: false
            }).then((result) => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else if (actionType === 'save_only') {
                    // Reload halaman setelah save berhasil untuk memastikan data ter-update
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000); // Delay 1 detik untuk user melihat pesan sukses
                } else if (actionType === 'forward_to_university' || actionType === 'resend_to_university' || actionType === 'submit_university') {
                    // Reload halaman setelah Admin Fakultas berhasil mengirim ke universitas
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500); // Delay 1.5 detik untuk user melihat pesan sukses
                } else if (actionType === 'return_to_pegawai') {
                    // Reload halaman setelah Admin Fakultas berhasil mengirim Permintaan Perbaikan
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500); // Delay 1.5 detik untuk user melihat pesan sukses
                } else if (actionType === 'rekomendasikan_usulan') {
                    // Reload halaman setelah Penilai berhasil merekomendasikan usulan
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500); // Delay 1.5 detik untuk user melihat pesan sukses
                } else if (actionType === 'tidak_rekomendasikan') {
                    // Reload halaman setelah Penilai tidak merekomendasikan usulan
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500); // Delay 1.5 detik untuk user melihat pesan sukses
                } else if (actionType === 'perbaikan_usulan') {
                    // Reload halaman setelah Penilai mengirim permintaan perbaikan
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500); // Delay 1.5 detik untuk user melihat pesan sukses
                } else if (actionType === 'perbaikan_ke_fakultas') {
                    // Reload halaman setelah Admin Universitas mengirim perbaikan ke fakultas
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500); // Delay 1.5 detik untuk user melihat pesan sukses
                } else if (actionType === 'perbaikan_ke_pegawai') {
                    // Reload halaman setelah Admin Universitas mengirim perbaikan ke pegawai
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500); // Delay 1.5 detik untuk user melihat pesan sukses
                } else if (actionType === 'rekomendasikan') {
                    // Reload halaman setelah Penilai merekomendasikan (action berbeda dari rekomendasikan_usulan)
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500); // Delay 1.5 detik untuk user melihat pesan sukses
                } else if (actionType === 'autosave') {
                    // Reload halaman setelah autosave berhasil
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000); // Delay 1 detik untuk autosave
                } else if (actionType === 'forward_to_penilai') {
                    // Reload halaman setelah berhasil mengirim ke penilai
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500); // Delay 1.5 detik untuk user melihat pesan sukses
                }
            });
        } else {
            // Show error notification
            Swal.fire({
                title: '❌ Gagal!',
                text: data.message || 'Terjadi kesalahan saat memproses aksi.',
                icon: 'error',
                confirmButtonText: 'Coba Lagi',
                confirmButtonColor: '#ef4444'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: '❌ Error!',
            text: 'Terjadi kesalahan jaringan. Silakan coba lagi.',
            icon: 'error',
            confirmButtonText: 'Coba Lagi',
            confirmButtonColor: '#ef4444'
        });
    });
}

    // Check for success messages on page load and handle auto-reload
    window.addEventListener('load', function() {
        // Check if form was processing
        const wasProcessing = sessionStorage.getItem('form_processing');

        if (wasProcessing) {
            sessionStorage.removeItem('form_processing');

            // Check for success messages
            const successAlert = document.querySelector('.alert-success, .bg-green-100, .border-green-400');
            const errorAlert = document.querySelector('.alert-danger, .alert-error, .bg-red-100, .border-red-400');

            if (successAlert && !errorAlert) {
                const message = successAlert.textContent.trim();

                // Show success SweetAlert with auto-reload
                Swal.fire({
                    title: 'Berhasil!',
                    html: `
                        <div class="text-center">
                            <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <p class="text-gray-700">${message}</p>
                            <div class="mt-4 text-sm text-gray-600">
                                <p>Halaman akan dimuat ulang untuk menampilkan perubahan terbaru.</p>
                            </div>
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonColor: '#10B981',
                    confirmButtonText: 'Tutup',
                    timer: 3000,
                    timerProgressBar: true,
                    allowOutsideClick: false
                }).then(() => {
                    // Reload page after user closes dialog or timer expires
                    window.location.reload();
                });
            }
        }

        // Also check for direct success messages (for non-AJAX forms)
        const urlParams = new URLSearchParams(window.location.search);
        const successParam = urlParams.get('success');

        if (successParam) {
            // Remove success parameter from URL
            urlParams.delete('success');
            const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
            window.history.replaceState({}, document.title, newUrl);

            // Show success message
            Swal.fire({
                title: 'Berhasil!',
                text: 'Data berhasil disimpan.',
                icon: 'success',
                confirmButtonColor: '#10B981',
                confirmButtonText: 'Tutup',
                timer: 2000,
                timerProgressBar: true
            });
        }
    });

// Add usulan data for JavaScript access
</script>
<script type="application/json" data-usulan>
{
    "id": {{ $usulan->id }},
    "status_usulan": "{{ $usulan->status_usulan }}",
    "validasi_data": @json($usulan->validasi_data ?? [])
}
</script>
<script>

// PENILAI UNIVERSITAS MODAL FUNCTIONS
function showRekomendasiPenilaiModal() {
    Swal.fire({
        title: 'Rekomendasi Usulan',
        html: `
            <div class="text-left">
                <div class="mb-4">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-600 mr-2"></i>
                            <span class="text-sm text-green-800">
                                <strong>Aksi:</strong> Usulan akan direkomendasikan dan dikirim ke Admin Universitas untuk review.
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i data-lucide="message-square" class="w-4 h-4 inline mr-1"></i>
                        Catatan Rekomendasi (Opsional):
                    </label>
                    <textarea id="catatan-rekomendasi-penilai"
                              placeholder="Berikan catatan atau komentar tambahan untuk rekomendasi ini..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none"
                              rows="3"></textarea>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Ya, Rekomendasikan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#16a34a',
        width: '600px',
        preConfirm: () => {
            const catatan = document.getElementById('catatan-rekomendasi-penilai').value;
            return catatan.trim();
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Set catatan
            const form = document.getElementById('action-form');
            const catatanInput = document.createElement('input');
            catatanInput.type = 'hidden';
            catatanInput.name = 'catatan_umum';
            catatanInput.value = result.value;
            form.appendChild(catatanInput);

            submitAction('rekomendasikan', result.value);
        }
    });
}

function showPerbaikanPenilaiModal() {
    console.log('🔧 showPerbaikanPenilaiModal called');
    Swal.fire({
        title: 'Permintaan Perbaikan Usulan',
        html: `
            <div class="text-left">
                <div class="mb-4">
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 mr-2"></i>
                            <span class="text-sm text-amber-800">
                                <strong>Aksi:</strong> Usulan memerlukan perbaikan dan akan dikirim ke Admin Universitas untuk review.
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i data-lucide="message-square" class="w-4 h-4 inline mr-1"></i>
                        Catatan Perbaikan <span class="text-red-500">*</span>:
                    </label>
                    <textarea id="catatan-perbaikan-penilai"
                              placeholder="Jelaskan detail perbaikan yang diperlukan..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 resize-none"
                              rows="4"></textarea>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Kirim Perbaikan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d97706',
        width: '600px',
        preConfirm: () => {
            const catatan = document.getElementById('catatan-perbaikan-penilai').value;
            if (!catatan || catatan.trim() === '') {
                Swal.showValidationMessage('Catatan perbaikan wajib diisi');
                return false;
            }
            if (catatan.trim().length < 10) {
                Swal.showValidationMessage('Catatan perbaikan minimal 10 karakter');
                return false;
            }
            return catatan.trim();
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Set catatan
            const form = document.getElementById('action-form');
            const catatanInput = document.createElement('input');
            catatanInput.type = 'hidden';
            catatanInput.name = 'catatan_umum';
            catatanInput.value = result.value;
            form.appendChild(catatanInput);

            submitAction('perbaikan_usulan', result.value);
        }
    });
}

function showTidakRekomendasiPenilaiModal() {
    Swal.fire({
        title: 'Tidak Merekomendasikan Usulan',
        html: `
            <div class="text-left">
                <div class="mb-4">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <i data-lucide="x-circle" class="w-5 h-5 text-red-600 mr-2"></i>
                            <span class="text-sm text-red-800">
                                <strong>Peringatan:</strong> Usulan tidak akan direkomendasikan dan akan dikirim ke Admin Universitas untuk review.
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i data-lucide="message-square" class="w-4 h-4 inline mr-1"></i>
                        Alasan Tidak Direkomendasikan <span class="text-red-500">*</span>:
                    </label>
                    <textarea id="catatan-tidak-rekomendasi-penilai"
                              placeholder="Jelaskan alasan mengapa usulan tidak direkomendasikan..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none"
                              rows="4"></textarea>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Tidak Direkomendasikan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc2626',
        width: '600px',
        preConfirm: () => {
            const catatan = document.getElementById('catatan-tidak-rekomendasi-penilai').value;
            if (!catatan || catatan.trim() === '') {
                Swal.showValidationMessage('Alasan tidak direkomendasikan wajib diisi');
                return false;
            }
            if (catatan.trim().length < 10) {
                Swal.showValidationMessage('Alasan minimal 10 karakter');
                return false;
            }
            return catatan.trim();
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin tidak merekomendasikan usulan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tidak Direkomendasikan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626'
            }).then((confirmation) => {
                if (confirmation.isConfirmed) {
                    // Set catatan
                    const form = document.getElementById('action-form');
                    const catatanInput = document.createElement('input');
                    catatanInput.type = 'hidden';
                    catatanInput.name = 'catatan_umum';
                    catatanInput.value = result.value;
                    form.appendChild(catatanInput);

                    submitAction('tidak_rekomendasikan', result.value);
                }
            });
        }
    });
}

// Ensure modal functions are globally accessible for button handlers
if (typeof window !== 'undefined') {
    window.showRekomendasiPenilaiModal = window.showRekomendasiPenilaiModal || showRekomendasiPenilaiModal;
    window.showPerbaikanPenilaiModal = window.showPerbaikanPenilaiModal || showPerbaikanPenilaiModal;
    window.showTidakRekomendasiPenilaiModal = window.showTidakRekomendasiPenilaiModal || showTidakRekomendasiPenilaiModal;
}

// Initialize additional buttons AFTER all functions are defined
additionalButtons.forEach(button => {
    const element = document.getElementById(button.id);
    if (element) {
        element.addEventListener('click', function(e) {
            console.log(`🔄 Button ${button.id} clicked`);
            button.handler();
        });
        console.log(`✅ ${button.id} handler attached`);
    } else {
        console.log(`❌ ${button.id} element not found`);
    }
});

function showKirimPerbaikanKePenilaiModal() {
    Swal.fire({
        title: 'Kirim Kembali ke Penilai Universitas',
        html: `
            <div class="text-left">
                <div class="mb-4">
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-orange-600 mr-2"></i>
                            <span class="text-sm text-orange-800">
                                <strong>Peringatan:</strong> Usulan akan dikirim kembali ke Tim Penilai untuk review ulang.
                            </span>
                        </div>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i data-lucide="info" class="w-5 h-5 text-blue-600 mr-2"></i>
                            <div class="text-sm text-blue-800">
                                <strong>Informasi:</strong>
                                <ul class="mt-2 list-disc list-inside space-y-1">
                                    <li>Tim Penilai akan melakukan review ulang</li>
                                    <li>Usulan akan kembali ke status "Usulan Perbaikan Ke Penilai Universitas"</li>
                                    <li>Penilaian sebelumnya mungkin direset</li>
                                    <li>Proses penilaian akan dimulai dari awal</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i data-lucide="message-square" class="w-4 h-4 inline mr-1"></i>
                        Catatan untuk Tim Penilai:
                    </label>
                    <textarea id="catatan-kirim-ke-penilai"
                              placeholder="Jelaskan alasan mengirim kembali ke Tim Penilai..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 resize-none"
                              rows="4"></textarea>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Kirim ke Penilai',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ea580c',
        width: '600px',
        preConfirm: () => {
            const catatan = document.getElementById('catatan-kirim-ke-penilai').value;
            if (!catatan || catatan.trim() === '') {
                Swal.showValidationMessage('Catatan wajib diisi untuk menjelaskan alasan pengiriman ulang ke Tim Penilai');
                return false;
            }
            if (catatan.trim().length < 10) {
                Swal.showValidationMessage('Catatan minimal 10 karakter untuk memberikan penjelasan yang jelas');
                return false;
            }
            return catatan.trim();
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show confirmation dialog
            Swal.fire({
                title: 'Konfirmasi Kirim ke Penilai',
                html: `
                    <div class="text-center">
                        <div class="mb-4">
                            <i data-lucide="alert-circle" class="w-16 h-16 text-orange-500 mx-auto mb-4"></i>
                            <p class="text-lg font-semibold text-gray-800 mb-2">Apakah Anda yakin?</p>
                            <p class="text-sm text-gray-600 mb-4">
                                Usulan akan dikirim kembali ke Tim Penilai dengan status "Usulan Perbaikan Ke Penilai Universitas"
                            </p>
                        </div>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-left">
                            <p class="text-sm text-gray-700"><strong>Catatan yang akan dikirim:</strong></p>
                            <p class="text-sm text-gray-600 mt-1">"${result.value}"</p>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Ya, Kirim ke Penilai',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ea580c',
                width: '500px'
            }).then((finalResult) => {
                if (finalResult.isConfirmed) {
                    // AUTO-SAVE + SEND: Simpan validasi dan kirim ke penilai
                    const form = document.getElementById('action-form');

                    // Set catatan_verifikator
                    const catatanVerifikatorInput = document.createElement('input');
                    catatanVerifikatorInput.type = 'hidden';
                    catatanVerifikatorInput.name = 'catatan_verifikator';
                    catatanVerifikatorInput.value = result.value;
                    form.appendChild(catatanVerifikatorInput);

                    // Auto-save validasi + send to penilai
                    submitAction('kirim_perbaikan_ke_penilai', '');
                }
            });
        }
    });
}

</script>
