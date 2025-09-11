@extends('backend.layouts.roles.kepegawaian-universitas.app')

@section('title', isset($jabatan) ? 'Edit Jabatan' : 'Tambah Jabatan')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800">
                        {{ isset($jabatan) ? 'Edit Jabatan' : 'Tambah Jabatan' }}
                    </h1>
                    <p class="text-slate-600">Kelola data jabatan dengan hirarki yang tepat untuk sistem usulan</p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/30 overflow-hidden">
            <div class="p-6 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-blue-50/30">
                <h2 class="text-xl font-bold text-slate-800">Form Jabatan</h2>
                <p class="text-slate-600">Isi data jabatan dengan lengkap dan benar</p>
            </div>

            <div class="p-6">
                <form id="jabatanForm" action="{{ isset($jabatan) ? route('backend.kepegawaian-universitas.jabatan.update', $jabatan) : route('backend.kepegawaian-universitas.jabatan.store') }}"
                      method="POST"
                      class="space-y-6">
                    @csrf
                    @if(isset($jabatan))
                        @method('PUT')
                    @endif

                    <!-- Jenis Pegawai -->
                    <div>
                        <label for="jenis_pegawai" class="block text-sm font-medium text-slate-700 mb-2">
                            Jenis Pegawai <span class="text-red-500">*</span>
                        </label>
                        <select id="jenis_pegawai"
                                name="jenis_pegawai"
                                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                required>
                            <option value="">-- Pilih Jenis Pegawai --</option>
                            <option value="Dosen" {{ old('jenis_pegawai', $jabatan->jenis_pegawai ?? '') == 'Dosen' ? 'selected' : '' }}>
                                👨‍🎓 Dosen
                            </option>
                            <option value="Tenaga Kependidikan" {{ old('jenis_pegawai', $jabatan->jenis_pegawai ?? '') == 'Tenaga Kependidikan' ? 'selected' : '' }}>
                                👥 Tenaga Kependidikan
                            </option>
                        </select>
                        <div id="jenisPegawaiError" class="mt-1 text-sm text-red-600 hidden"></div>
                    </div>

                    <!-- Jenis Jabatan -->
                    <div>
                        <label for="jenis_jabatan" class="block text-sm font-medium text-slate-700 mb-2">
                            Jenis Jabatan <span class="text-red-500">*</span>
                        </label>
                        <select id="jenis_jabatan"
                                name="jenis_jabatan"
                                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                required>
                            <option value="">-- Pilih Jenis Jabatan --</option>
                        </select>
                        <div id="jenisJabatanError" class="mt-1 text-sm text-red-600 hidden"></div>
                    </div>

                    <!-- Nama Jabatan -->
                    <div>
                        <label for="jabatan" class="block text-sm font-medium text-slate-700 mb-2">
                            Nama Jabatan <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="jabatan"
                               name="jabatan"
                               value="{{ old('jabatan', $jabatan->jabatan ?? '') }}"
                               class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Contoh: Lektor, Kepala Bagian, Arsiparis Ahli Muda"
                               required>
                        <div id="jabatanError" class="mt-1 text-sm text-red-600 hidden"></div>
                    </div>

                    <!-- Hierarchy Level -->
                    <div>
                        <label for="hierarchy_level" class="block text-sm font-medium text-slate-700 mb-2">
                            Level Hirarki <span class="text-gray-500">(Opsional)</span>
                        </label>
                        <input type="number"
                               id="hierarchy_level"
                               name="hierarchy_level"
                               value="{{ old('hierarchy_level', $jabatan->hierarchy_level ?? '') }}"
                               class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Contoh: 1, 2, 3, 4, 5..."
                               min="1" max="100">
                        <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-blue-700 font-medium mb-1">💡 Panduan Level Hirarki:</p>
                            <ul class="text-xs text-blue-600 space-y-1">
                                <li><strong>Kosongkan</strong> jika jabatan tidak memiliki hirarki (flat/setara)</li>
                                <li><strong>1, 2, 3, 4, 5...</strong> untuk jabatan berurutan (level 1 = terendah)</li>
                                <li><strong>Contoh Dosen:</strong> Tenaga Pengajar (1) → Asisten Ahli (2) → Lektor (3) → dst</li>
                                <li><strong>Contoh TK:</strong> Arsiparis Pertama (1) → Arsiparis Muda (2) → dst</li>
                            </ul>
                        </div>
                        <div id="hierarchyLevelError" class="mt-1 text-sm text-red-600 hidden"></div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-200">
                        <a href="{{ route('backend.kepegawaian-universitas.jabatan.index') }}"
                           class="px-6 py-3 text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition-colors duration-200">
                            Batal
                        </a>
                        <button type="submit"
                                id="submitBtn"
                                class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <span id="submitText">{{ isset($jabatan) ? 'Update' : 'Simpan' }} Jabatan</span>
                            <span id="loadingText" class="hidden">Memproses...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Notification Container -->
<div id="notificationContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <script>
        // XSS Protection Function
        function escapeHtml(text) {
            if (text === null || text === undefined) {
                return '';
            }
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
        }

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('jabatanForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const loadingText = document.getElementById('loadingText');
    const notificationContainer = document.getElementById('notificationContainer');
    const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
    const jenisJabatanSelect = document.getElementById('jenis_jabatan');

    // Jabatan options berdasarkan seeder yang benar
    const jabatanOptions = {
        'Dosen': [
            'Dosen Fungsional',
            'Dosen dengan Tugas Tambahan'
        ],
        'Tenaga Kependidikan': [
            'Tenaga Kependidikan Fungsional Tertentu',
            'Tenaga Kependidikan Fungsional Umum',
            'Tenaga Kependidikan Struktural',
            'Tenaga Kependidikan Tugas Tambahan'
        ]
    };

    // Update jenis jabatan options berdasarkan jenis pegawai
    function updateJenisJabatanOptions() {
        const selectedJenisPegawai = jenisPegawaiSelect.value;
        jenisJabatanSelect.innerHTML = '<option value="">-- Pilih Jenis Jabatan --</option>';

        if (selectedJenisPegawai && jabatanOptions[selectedJenisPegawai]) {
            jabatanOptions[selectedJenisPegawai].forEach(option => {
                const optionElement = document.createElement('option');
                optionElement.value = option;
                optionElement.textContent = option;
                jenisJabatanSelect.appendChild(optionElement);
            });
        }
    }

    // Initialize jenis jabatan options
    updateJenisJabatanOptions();

    // Update options when jenis pegawai changes
    jenisPegawaiSelect.addEventListener('change', function() {
        updateJenisJabatanOptions();
        document.getElementById('jenisJabatanError').classList.add('hidden');
    });

    // Form validation
    function validateForm() {
        let isValid = true;

        // Clear previous errors
        clearErrors();

        const jenisPegawai = jenisPegawaiSelect.value;
        const jenisJabatan = jenisJabatanSelect.value;
        const jabatan = document.getElementById('jabatan').value.trim();
        const hierarchyLevel = document.getElementById('hierarchy_level').value;

        // Validate jenis pegawai
        if (!jenisPegawai) {
            showError('jenisPegawai', 'Jenis pegawai wajib dipilih.');
            isValid = false;
        }

        // Validate jenis jabatan
        if (!jenisJabatan) {
            showError('jenisJabatan', 'Jenis jabatan wajib dipilih.');
            isValid = false;
        }

        // Validate jabatan
        if (!jabatan) {
            showError('jabatan', 'Nama jabatan wajib diisi.');
            isValid = false;
        }

        // Validate hierarchy level range
        if (hierarchyLevel && (hierarchyLevel < 1 || hierarchyLevel > 100)) {
            showError('hierarchyLevel', 'Level hirarki harus antara 1-100.');
            isValid = false;
        }

        return isValid;
    }

    function showError(field, message) {
        const errorElement = document.getElementById(field + 'Error');
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.remove('hidden');
        }
    }

    function clearErrors() {
        const errorElements = document.querySelectorAll('[id$="Error"]');
        errorElements.forEach(element => {
            element.classList.add('hidden');
            element.textContent = '';
        });
    }

    // Show notification
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `p-4 rounded-lg shadow-lg border-l-4 transform transition-all duration-300 translate-x-full ${
            type === 'success'
                ? 'bg-green-50 border-green-400 text-green-800'
                : 'bg-red-50 border-red-400 text-red-800'
        }`;

        notification.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 ${type === 'success' ? 'text-green-600' : 'text-red-600'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${
                        type === 'success'
                            ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
                            : 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z'
                    }"></path>
                </svg>
                <span class="font-medium">${escapeHtml(message)}</span>
            </div>
        `;

        notificationContainer.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!validateForm()) {
            showNotification('Mohon perbaiki kesalahan pada form.', 'error');
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        loadingText.classList.remove('hidden');

        // Submit form using fetch
        fetch(form.action, {
            method: form.method,
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Data jabatan berhasil disimpan!', 'success');

                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = '{{ route("backend.kepegawaian-universitas.jabatan.index") }}';
                }, 2000);
            } else {
                showNotification(data.message || 'Terjadi kesalahan saat menyimpan data.', 'error');

                // Show validation errors if any
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        showError(field, data.errors[field][0]);
                    });
                }
            }
        })
        .catch(error => {
            showNotification('Terjadi kesalahan pada server. Silakan coba lagi.', 'error');
        })
        .finally(() => {
            // Reset loading state
            submitBtn.disabled = false;
            submitText.classList.remove('hidden');
            loadingText.classList.add('hidden');
        });
    });

    // Real-time validation
    const jabatanInput = document.getElementById('jabatan');
    const hierarchyLevelInput = document.getElementById('hierarchy_level');

    jenisPegawaiSelect.addEventListener('blur', function() {
        if (!this.value) {
            showError('jenisPegawai', 'Jenis pegawai wajib dipilih.');
        } else {
            document.getElementById('jenisPegawaiError').classList.add('hidden');
        }
    });

    jenisJabatanSelect.addEventListener('blur', function() {
        if (!this.value) {
            showError('jenisJabatan', 'Jenis jabatan wajib dipilih.');
        } else {
            document.getElementById('jenisJabatanError').classList.add('hidden');
        }
    });

    jabatanInput.addEventListener('blur', function() {
        if (!this.value.trim()) {
            showError('jabatan', 'Nama jabatan wajib diisi.');
        } else {
            document.getElementById('jabatanError').classList.add('hidden');
        }
    });

    hierarchyLevelInput.addEventListener('blur', function() {
        const value = this.value;

        if (value && (value < 1 || value > 100)) {
            showError('hierarchyLevel', 'Level hirarki harus antara 1-100.');
        } else {
            document.getElementById('hierarchyLevelError').classList.add('hidden');
        }
    });

    // Auto-hide errors on input
    jenisPegawaiSelect.addEventListener('change', function() {
        document.getElementById('jenisPegawaiError').classList.add('hidden');
    });

    jenisJabatanSelect.addEventListener('change', function() {
        document.getElementById('jenisJabatanError').classList.add('hidden');
    });

    jabatanInput.addEventListener('input', function() {
        document.getElementById('jabatanError').classList.add('hidden');
    });

    hierarchyLevelInput.addEventListener('input', function() {
        document.getElementById('hierarchyLevelError').classList.add('hidden');
    });
});
</script>
@endsection
