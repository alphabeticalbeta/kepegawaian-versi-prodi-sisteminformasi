{{-- Validation Scripts - Separated JavaScript for validation functionality --}}
@push('scripts')
<script>
(function() {
    'use strict';

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
        showLoadingState();
    };

    window.showReturnForm = function() {
        document.getElementById('returnForm').classList.remove('hidden');
        document.getElementById('forwardForm').classList.add('hidden');

        // Update validation issue summary
        updateValidationIssueSummary();
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

        showLoadingState();
        mainForm.submit();
    };

    // Updated submitForwardForm function di _validation-scripts.blade.php
    window.submitForwardForm = function() {
        // Validate form first
        if (!validateForwardForm()) {
            return false;
        }

        // Show confirmation
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
                processForwardSubmission();
            }
        });
    };

    function processForwardSubmission() {
        const mainForm = document.getElementById('validationForm');
        const forwardForm = document.getElementById('forwardUsulanForm');
        const progressDiv = document.getElementById('uploadProgress');
        const progressBar = document.getElementById('uploadProgressBar');

        // Show progress
        if (progressDiv) {
            progressDiv.classList.remove('hidden');
            animateProgress(progressBar, 0, 30, 500); // 0-30% in 500ms
        }

        // Validate all required fields
        const requiredFields = ['nomor_surat_usulan', 'file_surat_usulan', 'nomor_berita_senat', 'file_berita_senat'];
        let missingFields = [];

        requiredFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (!field) {
                missingFields.push(fieldName);
                return;
            }

            if (field.type === 'file') {
                if (field.files.length === 0) {
                    missingFields.push(fieldName);
                }
            } else {
                if (!field.value.trim()) {
                    missingFields.push(fieldName);
                }
            }
        });

        if (missingFields.length > 0) {
            hideProgress();
            Swal.fire({
                icon: 'error',
                title: 'Data Tidak Lengkap',
                text: 'Semua field dokumen fakultas wajib diisi.',
                confirmButtonText: 'OK'
            });
            return false;
        }

        // Update progress
        if (progressBar) {
            animateProgress(progressBar, 30, 60, 500); // 30-60% in 500ms
        }

        // Validate file sizes
        const fileSurat = document.getElementById('file_surat_usulan');
        const fileBerita = document.getElementById('file_berita_senat');
        const maxSizeBytes = 1 * 1024 * 1024; // 1MB

        if (fileSurat.files.length > 0 && fileSurat.files[0].size > maxSizeBytes) {
            hideProgress();
            Swal.fire({
                icon: 'error',
                title: 'File Terlalu Besar',
                text: 'File surat usulan terlalu besar. Maksimal 1MB.',
                confirmButtonText: 'OK'
            });
            return false;
        }

        if (fileBerita.files.length > 0 && fileBerita.files[0].size > maxSizeBytes) {
            hideProgress();
            Swal.fire({
                icon: 'error',
                title: 'File Terlalu Besar',
                text: 'File berita senat terlalu besar. Maksimal 1MB.',
                confirmButtonText: 'OK'
            });
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
            animateProgress(progressBar, 60, 85, 500); // 60-85% in 500ms
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
            animateProgress(progressBar, 85, 100, 300); // 85-100% in 300ms
            setTimeout(() => {
                showLoadingState();
                mainForm.submit();
            }, 300);
        } else {
            showLoadingState();
            mainForm.submit();
        }
    }

    function animateProgress(progressBar, startPercent, endPercent, duration) {
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
    }

    function hideProgress() {
        const progressDiv = document.getElementById('uploadProgress');
        if (progressDiv) {
            progressDiv.classList.add('hidden');
        }
        const progressBar = document.getElementById('uploadProgressBar');
        if (progressBar) {
            progressBar.style.width = '0%';
        }
    }

    // Helper function to update validation issue summary
    function updateValidationIssueSummary() {
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
                    const label = ucwords(category.replace(/_/g, ' ')) + ' - ' + ucwords(field.replace(/_/g, ' '));
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
    }

    // Helper function to show loading state
    function showLoadingState() {
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
    }

    // Helper function to convert string to title case
    function ucwords(str) {
        return str.replace(/\b\w/g, function(l) {
            return l.toUpperCase();
        });
    }

    // Setup CSRF token for AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        window.Laravel = { csrfToken: csrfToken.getAttribute('content') };
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
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
                validateFileInput(e.target, 2);
            });
        }

        if (fileBeritaInput) {
            fileBeritaInput.addEventListener('change', function(e) {
                validateFileInput(e.target, 5);
            });
        }
    });

    // Validate file input
    function validateFileInput(input, maxSizeMB) {
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
    }

})();
</script>

<script>
    // COMPREHENSIVE AUTOSAVE FIX - Prevent Data Loss
    (function () {
        'use strict';

        const form = document.getElementById('validationForm');
        if (!form) return;

        // Hindari inisialisasi ganda
        if (window.__autosaveInit) return;
        window.__autosaveInit = true;

        // UI indikator
        let saver = document.getElementById('autosaveIndicator');
        if (!saver) {
            saver = document.createElement('div');
            saver.id = 'autosaveIndicator';
            saver.className = 'fixed bottom-4 right-4 px-4 py-2 rounded-lg shadow bg-gray-800 text-white text-sm opacity-0 transition-opacity';
            saver.style.zIndex = 9999;
            saver.textContent = 'Menyimpan…';
            document.body.appendChild(saver);
        }

        let saveTimer = null;
        let busy = false;
        let retryCount = 0;
        const MAX_RETRIES = 2;
        const SAVE_TIMEOUT = 15000;

        // CRITICAL: Track all form data to prevent partial saves
        let lastFormSnapshot = {};

        function showSaving() {
            saver.textContent = 'Menyimpan…';
            saver.className = 'fixed bottom-4 right-4 px-4 py-2 rounded-lg shadow bg-blue-600 text-white text-sm opacity-100 transition-opacity';
        }
        
        function showSaved() {
            saver.textContent = 'Tersimpan ✓';
            saver.className = 'fixed bottom-4 right-4 px-4 py-2 rounded-lg shadow bg-green-600 text-white text-sm opacity-100 transition-opacity';
            setTimeout(() => {
                saver.classList.add('opacity-0');
            }, 2000);
        }
        
        function showError(message = 'Gagal menyimpan') {
            saver.textContent = message;
            saver.className = 'fixed bottom-4 right-4 px-4 py-2 rounded-lg shadow bg-red-600 text-white text-sm opacity-100 transition-opacity';
            setTimeout(() => {
                saver.classList.add('opacity-0');
            }, 4000);
        }

        function ensureActionType() {
            let input = form.querySelector('input[name="action_type"]');
            if (!input) {
                input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'action_type';
                form.appendChild(input);
            }
            input.value = 'save_only';
        }

        // CRITICAL: Comprehensive form data collection
        function getAllFormData() {
            const formData = {};
            
            // Get all validation selects
            const selects = form.querySelectorAll('select[name^="validation["]');
            selects.forEach(select => {
                if (select.name && select.value) {
                    formData[select.name] = select.value;
                }
            });

            // Get all validation textareas
            const textareas = form.querySelectorAll('textarea[name^="validation["]');
            textareas.forEach(textarea => {
                if (textarea.name) {
                    formData[textarea.name] = textarea.value || '';
                }
            });

            // Get all validation inputs
            const inputs = form.querySelectorAll('input[name^="validation["]');
            inputs.forEach(input => {
                if (input.name && input.type !== 'file') {
                    formData[input.name] = input.value || '';
                }
            });

            return formData;
        }

        // Check if form data has meaningful changes
        function hasSignificantChanges(newData, oldData) {
            const newKeys = Object.keys(newData);
            const oldKeys = Object.keys(oldData);
            
            // If different number of fields, definitely changed
            if (newKeys.length !== oldKeys.length) {
                return true;
            }

            // Check each field for changes
            for (const key of newKeys) {
                if (newData[key] !== oldData[key]) {
                    return true;
                }
            }

            return false;
        }

        async function autoSave() {
            if (busy) {
                console.log('Autosave: Already busy, skipping');
                return;
            }

            // Collect ALL current form data
            const currentFormData = getAllFormData();
            
            // Skip if no significant changes
            if (!hasSignificantChanges(currentFormData, lastFormSnapshot)) {
                console.log('Autosave: No changes detected, skipping');
                return;
            }
            
            busy = true;
            showSaving();
            ensureActionType();

            // Create abort controller for timeout
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), SAVE_TIMEOUT);

            try {
                // CRITICAL: Use comprehensive FormData, not just changed fields
                const fd = new FormData(form);
                
                // Ensure all validation data is included
                Object.keys(currentFormData).forEach(key => {
                    if (!fd.has(key)) {
                        fd.append(key, currentFormData[key]);
                    }
                });
                
                console.log('Autosave: Starting request with', Object.keys(currentFormData).length, 'validation fields');
                
                const resp = await fetch(form.action, {
                    method: 'POST',
                    body: fd,
                    signal: controller.signal,
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': window.Laravel ? window.Laravel.csrfToken : ''
                    },
                    credentials: 'same-origin'
                });

                clearTimeout(timeoutId);

                if (!resp.ok) {
                    if (resp.status === 504) {
                        throw new Error('Timeout - Server terlalu lama merespons');
                    } else if (resp.status === 500) {
                        throw new Error('Server Error - Silakan coba lagi');
                    } else {
                        throw new Error(`HTTP ${resp.status}`);
                    }
                }

                // Success: Update snapshot
                lastFormSnapshot = { ...currentFormData };
                console.log('Autosave: Success, saved', Object.keys(currentFormData).length, 'fields');
                showSaved();
                retryCount = 0;

            } catch (e) {
                clearTimeout(timeoutId);
                console.error('Autosave error', e);
                
                if (e.name === 'AbortError') {
                    showError('Timeout - Koneksi lambat');
                } else if (retryCount < MAX_RETRIES) {
                    retryCount++;
                    showError(`Gagal (${retryCount}/${MAX_RETRIES})`);
                    // Retry dengan delay
                    setTimeout(() => {
                        busy = false;
                        autoSave();
                    }, 2000 * retryCount);
                    return;
                } else {
                    showError('Gagal menyimpan - Refresh halaman');
                    retryCount = 0;
                }
            } finally {
                busy = false;
            }
        }

        function debounceSave() {
            clearTimeout(saveTimer);
            // Shorter debounce for better UX but prevent too frequent calls
            saveTimer = setTimeout(autoSave, 800);
        }

        // Initialize form snapshot
        function initializeSnapshot() {
            lastFormSnapshot = getAllFormData();
            console.log('Form snapshot initialized with', Object.keys(lastFormSnapshot).length, 'fields');
        }

        // Event binding with comprehensive coverage
        function bindEvents() {
            // Status selects - immediate save
            const statusSelects = form.querySelectorAll('select[name^="validation["][name$="[status]"]');
            statusSelects.forEach(sel => {
                sel.addEventListener('change', (e) => {
                    const fieldId = sel.getAttribute('data-field-id');
                    if (typeof window.toggleKeterangan === 'function') {
                        window.toggleKeterangan(fieldId, sel.value);
                    }
                    // Wait a bit for toggleKeterangan to complete
                    setTimeout(autoSave, 100);
                });
            });

            // Keterangan textareas - debounced save
            const ketTextareas = form.querySelectorAll('textarea[name^="validation["][name$="[keterangan]"]');
            ketTextareas.forEach(ta => {
                ta.addEventListener('input', debounceSave);
                ta.addEventListener('blur', () => {
                    // Immediate save on blur to ensure data isn't lost
                    clearTimeout(saveTimer);
                    setTimeout(autoSave, 100);
                });
            });

            // Any other validation inputs
            const otherInputs = form.querySelectorAll('input[name^="validation["], select[name^="validation["]');
            otherInputs.forEach(input => {
                if (!input.name.endsWith('[status]')) { // Skip status selects (already handled)
                    input.addEventListener('change', debounceSave);
                    if (input.type === 'text' || input.tagName === 'TEXTAREA') {
                        input.addEventListener('input', debounceSave);
                    }
                }
            });
        }

        // Manual trigger with force save
        window.submitValidation = function (e) {
            if (e && e.preventDefault) e.preventDefault();
            clearTimeout(saveTimer);
            autoSave();
        };

        // Force save when user is about to leave
        document.addEventListener('visibilitychange', function() {
            if (document.hidden && !busy) {
                clearTimeout(saveTimer);
                autoSave();
            }
        });

        // Before unload protection
        window.addEventListener('beforeunload', function(e) {
            const currentData = getAllFormData();
            if (hasSignificantChanges(currentData, lastFormSnapshot)) {
                e.preventDefault();
                e.returnValue = 'Ada perubahan yang belum tersimpan. Yakin ingin meninggalkan halaman?';
                return e.returnValue;
            }
        });

        // Initialize everything
        document.addEventListener('DOMContentLoaded', function() {
            initializeSnapshot();
            bindEvents();
            console.log('Enhanced autosave initialized');
        });

        // If DOM already loaded
        if (document.readyState === 'loading') {
            // Do nothing, DOMContentLoaded will fire
        } else {
            // DOM already loaded
            initializeSnapshot();
            bindEvents();
            console.log('Enhanced autosave initialized (DOM ready)');
        }

    })();
</script>

// =====================================
// NEW FUNCTIONS FOR UNIVERSITY ADMIN
// =====================================

// Return for Revision Form Functions
function showReturnForRevisionForm() {
    document.getElementById('returnForRevisionForm').classList.remove('hidden');
    document.getElementById('catatan_umum_return_revision').focus();
}

function hideReturnForRevisionForm() {
    document.getElementById('returnForRevisionForm').classList.add('hidden');
    document.getElementById('catatan_umum_return_revision').value = '';
    document.getElementById('charCount_return_revision').textContent = '0';
}

// Not Recommended Form Functions
function showNotRecommendedForm() {
    document.getElementById('notRecommendedForm').classList.remove('hidden');
    document.getElementById('catatan_umum_not_recommended').focus();
}

function hideNotRecommendedForm() {
    document.getElementById('notRecommendedForm').classList.add('hidden');
    document.getElementById('catatan_umum_not_recommended').value = '';
    document.getElementById('charCount_not_recommended').textContent = '0';
}

// Send to Assessor Team Form Functions
function showSendToAssessorForm() {
    document.getElementById('sendToAssessorForm').classList.remove('hidden');
}

function hideSendToAssessorForm() {
    document.getElementById('sendToAssessorForm').classList.add('hidden');
    // Reset checkboxes
    const checkboxes = document.querySelectorAll('input[name="assessor_ids[]"]');
    checkboxes.forEach(checkbox => checkbox.checked = false);
    validateAssessorSelection();
}

// Send to Senate Team Form Functions
function showSendToSenateForm() {
    document.getElementById('sendToSenateForm').classList.remove('hidden');
}

function hideSendToSenateForm() {
    document.getElementById('sendToSenateForm').classList.add('hidden');
}

function validateAssessorSelection() {
    const checkboxes = document.querySelectorAll('input[name="assessor_ids[]"]:checked');
    const count = checkboxes.length;
    const submitBtn = document.getElementById('submitAssessorBtn');
    const countDisplay = document.getElementById('assessorCount');
    
    countDisplay.textContent = count;
    
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

// =====================================
// FORM DISPLAY/HIDE FUNCTIONS
// =====================================

// Return Form Functions
function showReturnForm() {
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
}

function hideReturnForm() {
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
}

// Not Recommended Form Functions
function showNotRecommendedForm() {
    const form = document.getElementById('notRecommendedForm');
    const textarea = document.getElementById('catatan_umum_not_recommended');
    
    if (!form) {
        console.error('Not recommended form not found');
        return;
    }
    
    form.classList.remove('hidden');
    if (textarea) {
        textarea.focus();
    }
}

function hideNotRecommendedForm() {
    const form = document.getElementById('notRecommendedForm');
    const textarea = document.getElementById('catatan_umum_not_recommended');
    const charCount = document.getElementById('charCount_not_recommended');
    
    if (!form) {
        console.error('Not recommended form not found');
        return;
    }
    
    form.classList.add('hidden');
    if (textarea) {
        textarea.value = '';
    }
    if (charCount) {
        charCount.textContent = '0';
    }
}

// Send to Assessor Team Form Functions
function showSendToAssessorForm() {
    const form = document.getElementById('sendToAssessorForm');
    
    if (!form) {
        console.error('Send to assessor form not found');
        return;
    }
    
    form.classList.remove('hidden');
}

function hideSendToAssessorForm() {
    const form = document.getElementById('sendToAssessorForm');
    
    if (!form) {
        console.error('Send to assessor form not found');
        return;
    }
    
    form.classList.add('hidden');
    
    // Reset checkboxes
    const checkboxes = document.querySelectorAll('input[name="assessor_ids[]"]');
    checkboxes.forEach(checkbox => checkbox.checked = false);
    validateAssessorSelection();
}

// Send to Senate Team Form Functions
function showSendToSenateForm() {
    const form = document.getElementById('sendToSenateForm');
    
    if (!form) {
        console.error('Send to senate form not found');
        return;
    }
    
    form.classList.remove('hidden');
}

function hideSendToSenateForm() {
    const form = document.getElementById('sendToSenateForm');
    
    if (!form) {
        console.error('Send to senate form not found');
        return;
    }
    
    form.classList.add('hidden');
}

// =====================================
// VALIDATION FUNCTIONS
// =====================================

function validateAssessorSelection() {
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
}

// =====================================
// FORM SUBMISSION HANDLERS
// =====================================

document.addEventListener('DOMContentLoaded', function() {
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
    
    // =====================================
    // CHARACTER COUNT HANDLERS
    // =====================================
    
    // Character count for Return Form
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
    
    // Character count for Not Recommended
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
    validateAssessorSelection();
    
    console.log('Form handlers initialized successfully');
});
@endpush

