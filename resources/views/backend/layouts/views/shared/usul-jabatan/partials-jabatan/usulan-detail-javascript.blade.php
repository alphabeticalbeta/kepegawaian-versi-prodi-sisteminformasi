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
    
    // Use a more robust approach with event delegation and retry mechanism
    const validationStatusElements = document.querySelectorAll('.validation-status');
    console.log('📊 Found validation status elements:', validationStatusElements.length);
    
    if (validationStatusElements.length === 0) {
        console.log('⚠️ No validation status elements found, will retry in 500ms...');
        setTimeout(initializeValidationHandlers, 500);
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

// Action button handlers with robust initialization
function initializeButtonHandlers() {
    console.log('🔧 Setting up action button handlers...');
    
    // Initialize button handlers with retry mechanism
    const buttons = [
        { id: 'btn-perbaikan', handler: () => { console.log('✅ btn-perbaikan clicked'); showPerbaikanModal(); } },
        { id: 'btn-forward', handler: () => { console.log('✅ Admin Fakultas btn-forward clicked'); showForwardModal(); } },
        { id: 'btn-forward-other', handler: () => { console.log('✅ Other role btn-forward clicked'); showForwardModal(); } },
        { id: 'btn-autosave-admin-fakultas', handler: () => { console.log('✅ btn-autosave-admin-fakultas clicked'); submitAction('save_only', ''); } }
    ];
    
    let attachedCount = 0;
    buttons.forEach(button => {
        const element = document.getElementById(button.id);
        if (element) {
            // Remove existing listeners to prevent duplication
            element.removeEventListener('click', button.handler);
            // Add new listener
            element.addEventListener('click', button.handler);
            console.log(`✅ ${button.id} handler attached`);
            attachedCount++;
        } else {
            console.log(`⚠️ ${button.id} not found`);
        }
    });
    
    console.log(`📊 Button handlers attached: ${attachedCount}/${buttons.length}`);
    
    // If no buttons found, retry after a delay
    if (attachedCount === 0) {
        console.log('⚠️ No buttons found, will retry in 1000ms...');
        setTimeout(initializeButtonHandlers, 1000);
    }
}

// Initialize button handlers after DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize button handlers
    initializeButtonHandlers();
});

// Additional button handlers for other roles
const additionalButtons = [
    { id: 'btn-perbaikan-pegawai', handler: () => showPerbaikanKePegawaiModal() },
    { id: 'btn-perbaikan-fakultas', handler: () => showPerbaikanKeFakultasModal() },
    { id: 'btn-teruskan-penilai', handler: () => showTeruskanKePenilaiModal() },
    { id: 'btn-tambah-penilai', handler: () => showTambahPenilaiModal() },
    { id: 'btn-tugaskan-penilai', handler: () => showTambahPenilaiModal() },
    { id: 'btn-simpan-validasi-top', handler: () => submitAction('save_only', '') },
    { id: 'btn-simpan-validasi-bottom', handler: () => submitAction('save_only', '') }
];

// Initialize additional buttons
additionalButtons.forEach(button => {
    const element = document.getElementById(button.id);
    if (element) {
        element.addEventListener('click', button.handler);
        console.log(`✅ ${button.id} handler attached`);
    }
});

if (document.getElementById('btn-teruskan-senat')) {
    document.getElementById('btn-teruskan-senat').addEventListener('click', function() {
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
if (document.getElementById('btn-kirim-ke-universitas')) {
    document.getElementById('btn-kirim-ke-universitas').addEventListener('click', function() {
        showKirimKembaliKeUniversitasModal();
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
            title: 'Usulkan ke Universitas',
            text: 'Usulan akan dikirim ke universitas untuk diproses selanjutnya. Semua data akan diperiksa secara fleksibel.',
            input: 'textarea',
            inputPlaceholder: 'Catatan untuk universitas (opsional)...',
            inputAttributes: {
                'aria-label': 'Catatan untuk universitas'
            },
            showCancelButton: true,
            confirmButtonText: 'Usulkan ke Universitas',
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
                submitAction('forward_to_university', result.value);
            }
        });
    } else {
        console.log('Show forward modal for', currentRole);
    }
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
                } else if (actionType === 'forward_to_university' || actionType === 'resend_to_university') {
                    // Reload halaman setelah Admin Fakultas berhasil mengirim ke universitas
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

// Add usulan data for JavaScript access
</script>
<script type="application/json" data-usulan>
{
    "id": {{ $usulan->id }},
    "status_usulan": "{{ $usulan->status_usulan }}",
    "validasi_data": @json($usulan->validasi_data ?? [])
}
</script>
