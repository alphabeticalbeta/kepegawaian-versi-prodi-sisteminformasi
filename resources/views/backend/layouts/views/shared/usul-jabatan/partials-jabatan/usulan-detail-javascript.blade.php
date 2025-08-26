{{-- JavaScript Section --}}
<script>
// Auto-save functionality
let autoSaveTimeout;
const autoSaveDelay = 600; // 600ms delay

function debouncedAutoSave() {
    clearTimeout(autoSaveTimeout);
    autoSaveTimeout = setTimeout(() => {
        performAutoSave();
    }, autoSaveDelay);
}

async function performAutoSave() {
    const formData = new FormData();
    formData.append('_token', document.querySelector('input[name="_token"]').value);
    formData.append('action_type', 'autosave');

    // Collect all validation data
    const validationData = {};
    document.querySelectorAll('.validation-status').forEach(select => {
        const group = select.dataset.group;
        const field = select.dataset.field;
        const status = select.value;
        const keterangan = select.closest('tr').querySelector('.validation-keterangan').value;

        if (!validationData[group]) validationData[group] = {};
        validationData[group][field] = {
            status: status,
            keterangan: keterangan
        };
    });

    // Format data based on role
    if ('{{ $currentRole }}' === 'Admin Fakultas') {
        // For Admin Fakultas, send validation data directly
        Object.keys(validationData).forEach(groupKey => {
            Object.keys(validationData[groupKey]).forEach(fieldKey => {
                const fieldData = validationData[groupKey][fieldKey];

                // Add status
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = `validation[${groupKey}][${fieldKey}][status]`;
                statusInput.value = fieldData.status;
                formData.append(statusInput.name, statusInput.value);

                // Add keterangan
                const keteranganInput = document.createElement('input');
                keteranganInput.type = 'hidden';
                keteranganInput.name = `validation[${groupKey}][${fieldKey}][keterangan]`;
                keteranganInput.value = fieldData.keterangan || '';
                formData.append(keteranganInput.name, keteranganInput.value);
            });
        });
        formData.append('action_type', 'save_only');
    } else {
        // For other roles, send as JSON
        formData.append('validation_data', JSON.stringify(validationData));
    }

    try {
        // Use different endpoint based on role
        let endpoint;
        if ('{{ $currentRole }}' === 'Admin Fakultas') {
                    endpoint = `/{{ isset($config['routePrefix']) ? $config['routePrefix'] : 'admin-fakultas' }}/usulan/${@json($usulan->id)}/autosave`;
    } else {
        endpoint = `/{{ isset($config['routePrefix']) ? $config['routePrefix'] : 'admin-fakultas' }}/usulan/${@json($usulan->id)}/save-validation`;
        }

        const response = await fetch(endpoint, {
            method: 'POST',
            body: formData
        });

        if (response.ok) {
            const result = await response.json();
            console.log('✅ Auto-save successful', result);
            showAutoSaveStatus('success');
        } else {
            const errorResult = await response.json().catch(() => ({}));
            console.error('❌ Auto-save failed', errorResult);
            showAutoSaveStatus('error');
        }
    } catch (error) {
        console.error('❌ Auto-save error:', error);
        showAutoSaveStatus('error');
    }
}

function showAutoSaveStatus(status) {
    const feedback = document.getElementById('action-feedback');
    if (status === 'success') {
        feedback.innerHTML = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">Data tersimpan otomatis</div>';
    } else {
        feedback.innerHTML = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Gagal menyimpan data</div>';
    }
    feedback.classList.remove('hidden');
    setTimeout(() => {
        feedback.classList.add('hidden');
    }, 3000);
}

// Event listeners for auto-save
document.addEventListener('DOMContentLoaded', function() {
    // Prevent form auto-submission
    const actionForm = document.getElementById('action-form');
    if (actionForm) {
        actionForm.addEventListener('submit', function(e) {
            // Only allow submission if action_type is not 'save_only' (which is the default)
            const actionType = document.getElementById('action_type').value;
            if (actionType === 'save_only') {
                e.preventDefault();
                console.log('Form submission prevented - no action selected');
                return false;
            }
        });

        // Prevent form submission on Enter key
        actionForm.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                console.log('Form submission prevented - Enter key pressed');
                return false;
            }
        });

        // Clear form data on page load to prevent auto-submission
        actionForm.reset();
        document.getElementById('action_type').value = 'save_only';
        document.getElementById('catatan_umum').value = '';
    }

    // Auto-save on validation status change
    document.querySelectorAll('.validation-status').forEach(select => {
        select.addEventListener('change', debouncedAutoSave);
    });

    // Auto-save on keterangan change
    document.querySelectorAll('.validation-keterangan').forEach(textarea => {
        textarea.addEventListener('input', debouncedAutoSave);
    });

    // Enable/disable keterangan based on status
    document.querySelectorAll('.validation-status').forEach(select => {
        select.addEventListener('change', function() {
            const row = this.closest('tr');
            const keterangan = row.querySelector('.validation-keterangan');
            const isInvalid = this.value === 'tidak_sesuai';

            keterangan.disabled = !isInvalid;
            keterangan.classList.toggle('bg-gray-100', !isInvalid);
        });
    });
});

// Action button handlers
if (document.getElementById('btn-perbaikan')) {
    document.getElementById('btn-perbaikan').addEventListener('click', function() {
        showPerbaikanModal();
    });
}

// Handle btn-forward buttons (Admin Fakultas)
if (document.getElementById('btn-forward')) {
    document.getElementById('btn-forward').addEventListener('click', function() {
        console.log('Admin Fakultas btn-forward clicked');
        showForwardModal();
    });
}

// Handle btn-forward-other buttons (Other roles)
if (document.getElementById('btn-forward-other')) {
    document.getElementById('btn-forward-other').addEventListener('click', function() {
        console.log('Other role btn-forward clicked');
        showForwardModal();
    });
}

// Admin Universitas specific buttons
if (document.getElementById('btn-perbaikan-pegawai')) {
    document.getElementById('btn-perbaikan-pegawai').addEventListener('click', function() {
        showPerbaikanKePegawaiModal();
    });
}

if (document.getElementById('btn-perbaikan-fakultas')) {
    document.getElementById('btn-perbaikan-fakultas').addEventListener('click', function() {
        showPerbaikanKeFakultasModal();
    });
}

if (document.getElementById('btn-teruskan-penilai')) {
    document.getElementById('btn-teruskan-penilai').addEventListener('click', function() {
        showTeruskanKePenilaiModal();
    });
}

// Handler untuk button Tambah Penilai Universitas
if (document.getElementById('btn-tambah-penilai')) {
    document.getElementById('btn-tambah-penilai').addEventListener('click', function() {
        showTambahPenilaiModal();
    });
}

// Handler untuk button Tugaskan Penilai Universitas
if (document.getElementById('btn-tugaskan-penilai')) {
    document.getElementById('btn-tugaskan-penilai').addEventListener('click', function() {
        showTambahPenilaiModal();
    });
}

// Handler untuk button Simpan Validasi (top)
if (document.getElementById('btn-simpan-validasi-top')) {
    document.getElementById('btn-simpan-validasi-top').addEventListener('click', function() {
        submitAction('autosave', '');
    });
}

// Handler untuk button Simpan Validasi (bottom)
if (document.getElementById('btn-simpan-validasi-bottom')) {
    document.getElementById('btn-simpan-validasi-bottom').addEventListener('click', function() {
        submitAction('autosave', '');
    });
}

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

// Modal functions
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
                submitAction('perbaikan_usulan', result.value);
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
                submitAction('rekomendasikan', result.value || '');
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

    // Collect validation data before submit
    let validationData = {};
    document.querySelectorAll('.validation-status').forEach(select => {
        const group = select.dataset.group;
        const field = select.dataset.field;
        const status = select.value;
        const keterangan = select.closest('tr').querySelector('.validation-keterangan').value;

        if (!validationData[group]) validationData[group] = {};
        validationData[group][field] = {
            status: status,
            keterangan: keterangan
        };
    });

    // Jika tidak ada validation data (karena form input dokumen), buat data kosong
    if (Object.keys(validationData).length === 0) {
        validationData['dokumen_admin_fakultas'] = {
            'nomor_surat_usulan': { 'status': 'sesuai', 'keterangan': '' },
            'file_surat_usulan': { 'status': 'sesuai', 'keterangan': '' },
            'nomor_berita_senat': { 'status': 'sesuai', 'keterangan': '' },
            'file_berita_senat': { 'status': 'sesuai', 'keterangan': '' }
        };
    }

    // Collect dokumen pendukung data if exists
    const dokumenPendukungData = {};
    document.querySelectorAll('input[name^="dokumen_pendukung["], textarea[name^="dokumen_pendukung["]').forEach(input => {
        const name = input.name;
        const value = input.value;

        // Extract field name from name attribute
        const match = name.match(/dokumen_pendukung\[([^\]]+)\]/);
        if (match) {
            const fieldName = match[1];
            dokumenPendukungData[fieldName] = value;
        }
    });

    // Add validation data to form
    Object.keys(validationData).forEach(groupKey => {
        Object.keys(validationData[groupKey]).forEach(fieldKey => {
            const fieldData = validationData[groupKey][fieldKey];

            // Add status
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = `validation[${groupKey}][${fieldKey}][status]`;
            statusInput.value = fieldData.status;
            form.appendChild(statusInput);

            // Add keterangan
            const keteranganInput = document.createElement('input');
            keteranganInput.type = 'hidden';
            keteranganInput.name = `validation[${groupKey}][${fieldKey}][keterangan]`;
            keteranganInput.value = fieldData.keterangan || '';
            form.appendChild(keteranganInput);
        });
    });

    // Add dokumen pendukung data to form if exists
    if (Object.keys(dokumenPendukungData).length > 0) {
        Object.keys(dokumenPendukungData).forEach(fieldName => {
            // Skip file inputs as they need to be handled differently
            if (fieldName !== 'file_surat_usulan' && fieldName !== 'file_berita_senat') {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `dokumen_pendukung[${fieldName}]`;
                input.value = dokumenPendukungData[fieldName];
                form.appendChild(input);
            }
        });
    }

    // Handle file inputs separately
    document.querySelectorAll('input[type="file"][name^="dokumen_pendukung["]').forEach(fileInput => {
        if (fileInput.files.length > 0) {
            // File inputs are already in the form, no need to add them
            console.log('File input found:', fileInput.name, fileInput.files[0].name);
        }
    });

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

    // Submit form with enhanced notification handling
    const formData = new FormData(form);
    
    console.log('Submitting form to:', form.action);

    // Manually collect file inputs that are outside the form
    document.querySelectorAll('input[type="file"][name^="dokumen_pendukung["]').forEach(fileInput => {
        if (fileInput.files.length > 0) {
            formData.append(fileInput.name, fileInput.files[0]);
            console.log('Added file to FormData:', fileInput.name, fileInput.files[0].name);
        }
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
