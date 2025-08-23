@extends('backend.layouts.roles.kepegawaian-universitas.app')

@section('title', isset($pangkat) ? 'Edit Pangkat' : 'Tambah Pangkat')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800">
                        {{ isset($pangkat) ? 'Edit Pangkat' : 'Tambah Pangkat' }}
                    </h1>
                    <p class="text-slate-600">Kelola data pangkat untuk sistem usulan kepegawaian</p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/30 overflow-hidden">
            <div class="p-6 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-blue-50/30">
                <h2 class="text-xl font-bold text-slate-800">Form Pangkat</h2>
                <p class="text-slate-600">Isi data pangkat dengan lengkap dan benar</p>
            </div>

            <div class="p-6">
                <form id="pangkatForm" action="{{ isset($pangkat) ? route('backend.kepegawaian-universitas.pangkat.update', $pangkat) : route('backend.kepegawaian-universitas.pangkat.store') }}"
                      method="POST"
                      class="space-y-6">
                    @csrf
                    @if(isset($pangkat))
                        @method('PUT')
                    @endif

                    <!-- Nama Pangkat -->
                    <div>
                        <label for="pangkat" class="block text-sm font-medium text-slate-700 mb-2">
                            Nama Pangkat <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="pangkat"
                               name="pangkat"
                               value="{{ old('pangkat', $pangkat->pangkat ?? '') }}"
                               class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Contoh: Penata Muda (III/a) / Pembina Tingkat I (IV/b)"
                               required>
                        <div id="pangkatError" class="mt-1 text-sm text-red-600 hidden"></div>
                    </div>

                    <!-- Status Pangkat -->
                    <div>
                        <label for="status_pangkat" class="block text-sm font-medium text-slate-700 mb-2">
                            Status Pangkat <span class="text-red-500">*</span>
                        </label>
                        <select id="status_pangkat"
                                name="status_pangkat"
                                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                required>
                            <option value="">-- Pilih Status Pangkat --</option>
                            <option value="PNS" {{ old('status_pangkat', $pangkat->status_pangkat ?? '') == 'PNS' ? 'selected' : '' }}>
                                PNS (Pegawai Negeri Sipil)
                            </option>
                            <option value="PPPK" {{ old('status_pangkat', $pangkat->status_pangkat ?? '') == 'PPPK' ? 'selected' : '' }}>
                                PPPK (Pegawai Pemerintah dengan Perjanjian Kerja)
                            </option>
                            <option value="Non-ASN" {{ old('status_pangkat', $pangkat->status_pangkat ?? '') == 'Non-ASN' ? 'selected' : '' }}>
                                Non-ASN (Kontrak, Honorer)
                            </option>
                        </select>
                        <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-blue-700 font-medium mb-1">💡 Panduan Status Pangkat:</p>
                            <ul class="text-xs text-blue-600 space-y-1">
                                <li><strong>PNS:</strong> Untuk pangkat PNS dengan hirarki promosi (Level 1-17)</li>
                                <li><strong>PPPK:</strong> Untuk pangkat PPPK dengan hirarki yang sama seperti PNS</li>
                                <li><strong>Non-ASN:</strong> Untuk pangkat kontrak/honorer tanpa hirarki</li>
                            </ul>
                        </div>
                        <div id="statusPangkatError" class="mt-1 text-sm text-red-600 hidden"></div>
                    </div>

                    <!-- Hierarchy Level -->
                    <div>
                        <label for="hierarchy_level" class="block text-sm font-medium text-slate-700 mb-2">
                            Level Hirarki <span class="text-gray-500">(Opsional untuk Non-ASN)</span>
                        </label>
                        <input type="number"
                               id="hierarchy_level"
                               name="hierarchy_level"
                               value="{{ old('hierarchy_level', $pangkat->hierarchy_level ?? '') }}"
                               class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Contoh: 1, 2, 3, 4, 5..."
                               min="1" max="20">
                        <div class="mt-2 p-3 bg-indigo-50 border border-indigo-200 rounded-lg">
                            <p class="text-sm text-indigo-700 font-medium mb-1">💡 Panduan Level Hirarki Pangkat:</p>
                            <ul class="text-xs text-indigo-600 space-y-1">
                                <li><strong>Kosongkan</strong> untuk pangkat Non-ASN (Kontrak, Honorer)</li>
                                <li><strong>1-4:</strong> Golongan I (Juru)</li>
                                <li><strong>5-8:</strong> Golongan II (Pengatur)</li>
                                <li><strong>9-12:</strong> Golongan III (Penata)</li>
                                <li><strong>13-17:</strong> Golongan IV (Pembina)</li>
                                <li><strong>Contoh:</strong> Penata Muda (III/a) = Level 9</li>
                            </ul>
                        </div>
                        <div id="hierarchyLevelError" class="mt-1 text-sm text-red-600 hidden"></div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-200">
                        <a href="{{ route('backend.kepegawaian-universitas.pangkat.index') }}"
                           class="px-6 py-3 text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition-colors duration-200">
                            Batal
                        </a>
                        <button type="submit"
                                id="submitBtn"
                                class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <span id="submitText">{{ isset($pangkat) ? 'Update' : 'Simpan' }} Pangkat</span>
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
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('pangkatForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const loadingText = document.getElementById('loadingText');
    const notificationContainer = document.getElementById('notificationContainer');

    // Form validation
    function validateForm() {
        let isValid = true;

        // Clear previous errors
        clearErrors();

        const pangkat = document.getElementById('pangkat').value.trim();
        const statusPangkat = document.getElementById('status_pangkat').value;
        const hierarchyLevel = document.getElementById('hierarchy_level').value;

        // Validate pangkat
        if (!pangkat) {
            showError('pangkat', 'Nama pangkat wajib diisi.');
            isValid = false;
        }

        // Validate status pangkat
        if (!statusPangkat) {
            showError('statusPangkat', 'Status pangkat wajib dipilih.');
            isValid = false;
        }

        // Validate hierarchy level for PNS and PPPK
        if ((statusPangkat === 'PNS' || statusPangkat === 'PPPK') && !hierarchyLevel) {
            showError('hierarchyLevel', 'Level hirarki wajib diisi untuk PNS dan PPPK.');
            isValid = false;
        }

        // Validate hierarchy level range
        if (hierarchyLevel && (hierarchyLevel < 1 || hierarchyLevel > 20)) {
            showError('hierarchyLevel', 'Level hirarki harus antara 1-20.');
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
                <span class="font-medium">${message}</span>
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
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Data pangkat berhasil disimpan!', 'success');

                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = '{{ route("backend.kepegawaian-universitas.pangkat.index") }}';
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
            console.error('Error:', error);
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
    const pangkatInput = document.getElementById('pangkat');
    const statusPangkatSelect = document.getElementById('status_pangkat');
    const hierarchyLevelInput = document.getElementById('hierarchy_level');

    pangkatInput.addEventListener('blur', function() {
        if (!this.value.trim()) {
            showError('pangkat', 'Nama pangkat wajib diisi.');
        } else {
            document.getElementById('pangkatError').classList.add('hidden');
        }
    });

    statusPangkatSelect.addEventListener('change', function() {
        if (!this.value) {
            showError('statusPangkat', 'Status pangkat wajib dipilih.');
        } else {
            document.getElementById('statusPangkatError').classList.add('hidden');

            // Clear hierarchy level for Non-ASN
            if (this.value === 'Non-ASN') {
                hierarchyLevelInput.value = '';
            }
        }
    });

    hierarchyLevelInput.addEventListener('blur', function() {
        const statusPangkat = statusPangkatSelect.value;
        const value = this.value;

        if ((statusPangkat === 'PNS' || statusPangkat === 'PPPK') && !value) {
            showError('hierarchyLevel', 'Level hirarki wajib diisi untuk PNS dan PPPK.');
        } else if (value && (value < 1 || value > 20)) {
            showError('hierarchyLevel', 'Level hirarki harus antara 1-20.');
        } else {
            document.getElementById('hierarchyLevelError').classList.add('hidden');
        }
    });

    // Auto-hide errors on input
    pangkatInput.addEventListener('input', function() {
        document.getElementById('pangkatError').classList.add('hidden');
    });

    statusPangkatSelect.addEventListener('change', function() {
        document.getElementById('statusPangkatError').classList.add('hidden');
    });

    hierarchyLevelInput.addEventListener('input', function() {
        document.getElementById('hierarchyLevelError').classList.add('hidden');
    });
});
</script>
@endsection
