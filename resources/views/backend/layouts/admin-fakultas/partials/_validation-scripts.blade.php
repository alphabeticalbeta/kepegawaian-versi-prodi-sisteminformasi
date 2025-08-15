{{-- resources/views/backend/layouts/admin-fakultas/partials/_validation-scripts.blade.php --}}
{{-- JavaScript untuk validasi Admin Fakultas --}}

<script>
// Toggle keterangan berdasarkan status validasi
function toggleKeterangan(fieldId, status) {
    console.log('Toggle called for:', fieldId, 'with status:', status); // Debug log
    
    const keteranganTextarea = document.getElementById(`keterangan_${fieldId}`);
    
    console.log('Textarea found:', keteranganTextarea); // Debug log

    if (keteranganTextarea) {
        if (status === 'tidak_sesuai') {
            // Enable textarea for tidak_sesuai
            keteranganTextarea.disabled = false;
            keteranganTextarea.required = true;
            keteranganTextarea.placeholder = 'Jelaskan mengapa item ini tidak sesuai...';
            keteranganTextarea.style.textAlign = 'center';
            console.log('Enabled textarea for:', fieldId);
        } else {
            // Disable textarea for sesuai
            keteranganTextarea.disabled = true;
            keteranganTextarea.required = false;
            keteranganTextarea.value = '';
            keteranganTextarea.placeholder = 'Pilih "Tidak Sesuai" untuk mengisi keterangan';
            keteranganTextarea.style.textAlign = 'center';
            console.log('Disabled textarea for:', fieldId);
        }
    } else {
        console.error('Textarea not found for field:', fieldId);
        console.error('Expected textarea ID:', `keterangan_${fieldId}`);
        
        // Fallback: try to find by name attribute
        const textareaByName = document.querySelector(`textarea[name*="${fieldId}"][name*="keterangan"]`);
        if (textareaByName) {
            console.log('Found textarea by name:', textareaByName.name);
            if (status === 'tidak_sesuai') {
                textareaByName.disabled = false;
                textareaByName.required = true;
                textareaByName.placeholder = 'Jelaskan mengapa item ini tidak sesuai...';
            } else {
                textareaByName.disabled = true;
                textareaByName.required = false;
                textareaByName.value = '';
                textareaByName.placeholder = 'Pilih "Tidak Sesuai" untuk mengisi keterangan';
            }
        }
    }
}

// Submit validasi saja
function submitValidation(event) {
    const form = document.getElementById('validationForm');
    
    // Hapus input action_type yang mungkin ada
    const existingActionInput = form.querySelector('input[name="action_type"]');
    if (existingActionInput) {
        existingActionInput.remove();
    }

    // Tambahkan input untuk tipe aksi "save_only"
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action_type';
    actionInput.value = 'save_only';
    form.appendChild(actionInput);
}

// Show/Hide forms
function showReturnForm() {
    document.getElementById('returnForm').classList.remove('hidden');
    document.getElementById('rejectForm').classList.add('hidden');
    document.getElementById('forwardForm').classList.add('hidden');
}

function hideReturnForm() {
    document.getElementById('returnForm').classList.add('hidden');
}

function showRejectForm() {
    document.getElementById('rejectForm').classList.remove('hidden');
    document.getElementById('returnForm').classList.add('hidden');
    document.getElementById('forwardForm').classList.add('hidden');
}

function hideRejectForm() {
    document.getElementById('rejectForm').classList.add('hidden');
}

function showForwardForm() {
    document.getElementById('forwardForm').classList.remove('hidden');
    document.getElementById('returnForm').classList.add('hidden');
    document.getElementById('rejectForm').classList.add('hidden');
}

function hideForwardForm() {
    document.getElementById('forwardForm').classList.add('hidden');
}

// Submit return form (Perbaikan Usulan)
function submitReturnForm() {
    const mainForm = document.getElementById('validationForm');
    const returnForm = document.getElementById('returnUsulanForm');
    const catatanUmumTextarea = returnForm.querySelector('textarea[name="catatan_umum"]');

    if (!catatanUmumTextarea) {
        alert("Terjadi error: komponen catatan tidak ditemukan.");
        return false;
    }

    const catatanUmum = catatanUmumTextarea.value.trim();
    console.log('Catatan umum length:', catatanUmum.length); // Debug log
    
    if (!catatanUmum || catatanUmum.length < 10) {
        alert('Catatan untuk pegawai wajib diisi minimal 10 karakter. Saat ini: ' + catatanUmum.length + ' karakter.');
        catatanUmumTextarea.focus();
        return false;
    }

    if (!confirm('Apakah Anda yakin ingin mengembalikan usulan ini ke pegawai untuk perbaikan?')) {
        return false;
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

    mainForm.submit();
}

// Submit reject form (Belum Direkomendasikan)
function submitRejectForm() {
    const mainForm = document.getElementById('validationForm');
    const rejectForm = document.getElementById('rejectUsulanForm');
    const catatanRejectTextarea = rejectForm.querySelector('textarea[name="catatan_reject"]');

    if (!catatanRejectTextarea) {
        alert("Terjadi error: komponen catatan reject tidak ditemukan.");
        return false;
    }

    const catatanReject = catatanRejectTextarea.value.trim();
    console.log('Catatan reject length:', catatanReject.length); // Debug log
    
    if (!catatanReject || catatanReject.length < 10) {
        alert('Alasan belum direkomendasikan wajib diisi minimal 10 karakter. Saat ini: ' + catatanReject.length + ' karakter.');
        catatanRejectTextarea.focus();
        return false;
    }

    if (!confirm('Apakah Anda yakin usulan ini belum dapat direkomendasikan?')) {
        return false;
    }

    // Add action type
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action_type';
    actionInput.value = 'reject_to_pegawai';
    mainForm.appendChild(actionInput);

    // Add catatan
    const catatanInput = document.createElement('input');
    catatanInput.type = 'hidden';
    catatanInput.name = 'catatan_reject';
    catatanInput.value = catatanReject;
    mainForm.appendChild(catatanInput);

    mainForm.submit();
}

// Submit forward form (Direkomendasikan)
function submitForwardForm() {
    const mainForm = document.getElementById('validationForm');
    const forwardForm = document.getElementById('forwardUsulanForm');

    // Validasi form forward
    const nomorSurat = forwardForm.querySelector('input[name="nomor_surat_usulan"]').value;
    const fileSurat = forwardForm.querySelector('input[name="file_surat_usulan"]').files;
    const nomorBerita = forwardForm.querySelector('input[name="nomor_berita_senat"]').value;
    const fileBerita = forwardForm.querySelector('input[name="file_berita_senat"]').files;

    if (!nomorSurat || fileSurat.length === 0 || !nomorBerita || fileBerita.length === 0) {
        alert('Semua field dokumen fakultas wajib diisi.');
        return false;
    }

    if (!confirm('Apakah Anda yakin ingin mengirim usulan ini ke Admin Universitas?')) {
        return false;
    }

    // Hapus input temporer dari pengiriman sebelumnya
    mainForm.querySelectorAll('.temp-forward-input').forEach(el => el.remove());

    // Tambahkan action_type
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action_type';
    actionInput.value = 'forward_to_university';
    actionInput.classList.add('temp-forward-input');
    mainForm.appendChild(actionInput);

    // Pindahkan text inputs
    const textInputs = forwardForm.querySelectorAll('input[type="text"], textarea');
    textInputs.forEach(input => {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = input.name;
        hiddenInput.value = input.value;
        hiddenInput.classList.add('temp-forward-input');
        mainForm.appendChild(hiddenInput);
    });

    // Pindahkan file inputs
    const fileInputs = forwardForm.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.style.display = 'none';
        input.classList.add('temp-forward-input');
        mainForm.appendChild(input);
    });

    mainForm.submit();
}

// Initialize pada page load
document.addEventListener('change', function(e) {
    if (e.target.matches('select[name*="[status]"]')) {
        const selectName = e.target.name;
        const value = e.target.value;
        
        console.log('Select changed:', selectName, 'value:', value);
        
        // Extract field ID from name like: validation[category][field][status]
        const matches = selectName.match(/validation\[(\w+)\]\[(\w+)\]\[status\]/);
        if (matches) {
            const category = matches[1];
            const field = matches[2];
            const fieldId = `${category}_${field}`;
            
            console.log('Calling toggle for fieldId:', fieldId);
            toggleKeterangan(fieldId, value);
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DEBUGGING VALIDATION ELEMENTS ===');
    
    // Find all select elements
    const selects = document.querySelectorAll('select[name*="[status]"]');
    console.log('Found', selects.length, 'status selects');
    
    selects.forEach((select, index) => {
        console.log(`Select ${index}:`, select.name, 'value:', select.value);
    });
    
    // Find all textarea elements
    const textareas = document.querySelectorAll('textarea[name*="keterangan"]');
    console.log('Found', textareas.length, 'keterangan textareas');
    
    textareas.forEach((textarea, index) => {
        console.log(`Textarea ${index}:`, textarea.name, 'id:', textarea.id, 'disabled:', textarea.disabled);
    });
    
    console.log('=== END DEBUG INFO ===');
    
    // Set initial state untuk semua fields berdasarkan existing data
    selects.forEach(select => {
        const fieldParts = select.name.match(/validation\[(\w+)\]\[(\w+)\]/);
        if (fieldParts) {
            const fieldId = fieldParts[1] + '_' + fieldParts[2];
            console.log('Setting initial state for:', fieldId, 'value:', select.value);
            toggleKeterangan(fieldId, select.value);
        }
    });
});
</script>