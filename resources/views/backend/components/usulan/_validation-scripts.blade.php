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

    window.submitForwardForm = function() {
        const mainForm = document.getElementById('validationForm');

        // Validate forward form fields
        const nomorSurat = document.getElementById('nomor_surat_usulan').value;
        const fileSurat = document.getElementById('file_surat_usulan').files;
        const nomorBerita = document.getElementById('nomor_berita_senat').value;
        const fileBerita = document.getElementById('file_berita_senat').files;

        if (!nomorSurat || fileSurat.length === 0 || !nomorBerita || fileBerita.length === 0) {
            alert('Semua field dokumen fakultas wajib diisi.');
            return false;
        }

        // Validate file sizes
        const maxSizeInBytes = {
            surat: 2 * 1024 * 1024, // 2MB
            berita: 5 * 1024 * 1024  // 5MB
        };

        if (fileSurat[0].size > maxSizeInBytes.surat) {
            alert('File surat usulan terlalu besar. Maksimal 2MB.');
            return false;
        }

        if (fileBerita[0].size > maxSizeInBytes.berita) {
            alert('File berita acara terlalu besar. Maksimal 5MB.');
            return false;
        }

        if (!confirm('Apakah Anda yakin ingin mengirim usulan ini ke universitas?')) {
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

        // Clone forward form inputs to main form
        const forwardInputs = document.getElementById('forwardUsulanForm').querySelectorAll('input, textarea, select');
        forwardInputs.forEach(input => {
            const clonedInput = input.cloneNode(true);
            clonedInput.style.display = 'none';
            mainForm.appendChild(clonedInput);
        });

        showLoadingState();
        mainForm.submit();
    };

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

        function showSaving() {
            saver.textContent = 'Menyimpan…';
            saver.classList.remove('opacity-0');
        }
        function showSaved() {
            saver.textContent = 'Tersimpan';
            setTimeout(() => saver.classList.add('opacity-0'), 800);
        }
        function showError() {
            saver.textContent = 'Gagal menyimpan';
            saver.classList.remove('opacity-0');
            setTimeout(() => saver.classList.add('opacity-0'), 1200);
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

        async function autoSave() {
            if (busy) return;
            busy = true;
            showSaving();
            ensureActionType();

            try {
            const fd = new FormData(form);
            const resp = await fetch(form.action, {
                method: 'POST',
                body: fd,
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': window.Laravel ? window.Laravel.csrfToken : ''
                },
                credentials: 'same-origin'
            });
            if (!resp.ok) throw new Error('HTTP ' + resp.status);
            showSaved();
            } catch (e) {
            console.error('Autosave error', e);
            showError();
            } finally {
            busy = false;
            }
        }

        function debounceSave() {
            clearTimeout(saveTimer);
            saveTimer = setTimeout(autoSave, 600);
        }

        // Bind: select status & textarea keterangan
        const statusSelects = form.querySelectorAll('select[name^="validation["][name$="[status]"]');
        const ketTextareas = form.querySelectorAll('textarea[name^="validation["][name$="[keterangan]"]');

        statusSelects.forEach(sel => {
            sel.addEventListener('change', () => {
            const fieldId = sel.getAttribute('data-field-id');
            if (typeof window.toggleKeterangan === 'function') {
                window.toggleKeterangan(fieldId, sel.value);
            }
            autoSave();
            });
        });

        ketTextareas.forEach(ta => {
            ta.addEventListener('input', debounceSave);
            ta.addEventListener('change', autoSave);
        });

        // Tetap sediakan trigger manual jika dibutuhkan
        window.submitValidation = function (e) {
            if (e && e.preventDefault) e.preventDefault();
            autoSave();
        };
    })();
</script>
@endpush

