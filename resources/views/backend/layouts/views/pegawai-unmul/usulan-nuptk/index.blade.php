@extends('backend.layouts.roles.pegawai-unmul.app')

@section('title', 'Usulan NUPTK Saya')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    /* Custom CSS untuk animasi tombol */
    .btn-animate {
        transition: all 0.2s ease-in-out;
        cursor: pointer;
    }

    .btn-animate:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .btn-animate:active {
        transform: scale(0.98);
    }

    /* Memastikan hover berfungsi */
    .btn-animate:hover {
        opacity: 0.9;
    }
</style>

<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 rounded-2xl p-6 border border-blue-100 shadow-sm">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-400/5 to-purple-400/5"></div>
            <div class="relative flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="p-2 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl shadow-md flex-shrink-0">
                        <i data-lucide="id-card" class="w-6 h-6 text-white"></i>
                    </div>
                    <div class="flex flex-col">
                        <h1 class="text-2xl text-black font-bold leading-tight">
                    Usulan NUPTK Saya
                </h1>
                        <p class="text-black text-sm mt-1 leading-tight">
                    Pantau status dan riwayat usulan NUPTK yang telah Anda ajukan.
                </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="bg-white shadow-xl rounded-2xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">
                Daftar Periode Usulan NUPTK
            </h3>
            <p class="text-sm text-gray-500 mt-1">
                Berikut adalah periode usulan NUPTK yang tersedia untuk status kepegawaian Anda.
            </p>
    </div>

    <div class="overflow-x-auto">
        @if($periodeUsulans->count() > 0)
            <table class="w-full text-sm text-center text-gray-600">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 align-middle">No</th>
                        <th scope="col" class="px-6 py-4 align-middle">Nama Periode</th>
                        <th scope="col" class="px-6 py-4 align-middle">Periode Usulan</th>
                        <th scope="col" class="px-6 py-4 align-middle">Periode Perbaikan</th>
                        <th scope="col" class="px-6 py-4 align-middle">Status Usulan</th>
                        <th scope="col" class="px-6 py-4 align-middle">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($periodeUsulans as $index => $periode)
                        @php
                            $existingUsulan = $usulans->where('periode_usulan_id', $periode->id)->first();
                        @endphp
                        <tr class="bg-white border-b hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 font-medium text-gray-900 align-middle">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-900 align-middle">
                                {{ $periode->nama_periode }}
                            </td>
                            <td class="px-6 py-4 align-middle">
                                <div class="text-left">
                                    <div class="font-medium text-gray-900">
                                        <i data-lucide="calendar" class="w-3 h-3 inline mr-1"></i>
                                        {{ $periode->tanggal_mulai ? $periode->tanggal_mulai->isoFormat('D MMM YYYY') : '-' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <i data-lucide="calendar-x" class="w-3 h-3 inline mr-1"></i>
                                        {{ $periode->tanggal_selesai ? $periode->tanggal_selesai->isoFormat('D MMM YYYY') : '-' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 align-middle">
                                <div class="text-left">
                                    <div class="font-medium text-gray-900">
                                        <i data-lucide="edit" class="w-3 h-3 inline mr-1"></i>
                                        {{ $periode->tanggal_mulai_perbaikan ? $periode->tanggal_mulai_perbaikan->isoFormat('D MMM YYYY') : '-' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <i data-lucide="edit-3" class="w-3 h-3 inline mr-1"></i>
                                        {{ $periode->tanggal_selesai_perbaikan ? $periode->tanggal_selesai_perbaikan->isoFormat('D MMM YYYY') : '-' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center align-middle">
                                @if($existingUsulan)
                                    @php
                                        $statusClass = match($existingUsulan->status_usulan) {
                                            'Draft Usulan' => 'bg-yellow-100 text-yellow-800',
                                            'Usulan Dikirim ke Admin Fakultas' => 'bg-blue-100 text-blue-800',
                                            'Usulan Dikirim ke Kepegawaian Universitas' => 'bg-indigo-100 text-indigo-800',
                                            'Usulan Dikirim ke Penilai Universitas' => 'bg-purple-100 text-purple-800',
                                            'Usulan Dikirim ke Tim Sister' => 'bg-pink-100 text-pink-800',
                                            'Usulan Disetujui' => 'bg-green-100 text-green-800',
                                            'Usulan Ditolak' => 'bg-red-100 text-red-800',
                                            'Draft Perbaikan Admin Fakultas' => 'bg-orange-100 text-orange-800',
                                            'Draft Perbaikan Kepegawaian Universitas' => 'bg-orange-100 text-orange-800',
                                            'Draft Perbaikan Penilai Universitas' => 'bg-orange-100 text-orange-800',
                                            'Draft Perbaikan Tim Sister' => 'bg-orange-100 text-orange-800',
                                            'Permintaan Perbaikan dari Admin Fakultas' => 'bg-amber-100 text-amber-800',
                                            'Permintaan Perbaikan dari Penilai Universitas' => 'bg-amber-100 text-amber-800',
                                            'Permintaan Perbaikan Usulan dari Tim Sister' => 'bg-amber-100 text-amber-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };

                                        $statusIcon = match($existingUsulan->status_usulan) {
                                            'Draft Usulan' => 'file-text',
                                            'Usulan Dikirim ke Admin Fakultas' => 'send',
                                            'Usulan Dikirim ke Kepegawaian Universitas' => 'send',
                                            'Usulan Dikirim ke Penilai Universitas' => 'send',
                                            'Usulan Dikirim ke Tim Sister' => 'send',
                                            'Usulan Disetujui' => 'check-circle',
                                            'Usulan Ditolak' => 'x-circle',
                                            'Draft Perbaikan Admin Fakultas' => 'edit',
                                            'Draft Perbaikan Kepegawaian Universitas' => 'edit',
                                            'Draft Perbaikan Penilai Universitas' => 'edit',
                                            'Draft Perbaikan Tim Sister' => 'edit',
                                            'Permintaan Perbaikan dari Admin Fakultas' => 'alert-triangle',
                                            'Permintaan Perbaikan dari Penilai Universitas' => 'alert-triangle',
                                            'Permintaan Perbaikan Usulan dari Tim Sister' => 'alert-triangle',
                                            default => 'help-circle'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                        <i data-lucide="{{ $statusIcon }}" class="w-3 h-3 mr-1"></i>
                                        {{ $existingUsulan->status_usulan ?? 'Belum Ada Status' }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                        <i data-lucide="minus" class="w-3 h-3 mr-1"></i>
                                        Belum Ada Usulan
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center align-middle">
                                @if($existingUsulan)
                                    {{-- Jika sudah ada usulan, tampilkan tombol Detail, Log, dan Hapus --}}
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('pegawai-unmul.usulan-nuptk.show', $existingUsulan->id) }}"
                                                                                       class="btn-animate inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:text-blue-700">
                                            <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                            Lihat Detail
                                        </a>
                                        <button type="button"
                                                data-usulan-id="{{ $existingUsulan->id }}"
                                                onclick="showLogs('{{ $existingUsulan->id }}')"
                                                class="btn-animate inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-600 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 hover:text-green-700">
                                            <i data-lucide="activity" class="w-3 h-3 mr-1"></i>
                                            Log
                                        </button>
                                        <button type="button"
                                                data-usulan-id="{{ $existingUsulan->id }}"
                                                onclick="confirmDelete('{{ $existingUsulan->id }}')"
                                                class="btn-animate inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 hover:text-red-700">
                                            <i data-lucide="trash-2" class="w-3 h-3 mr-1"></i>
                                            Hapus
                                        </button>
                                    </div>
                                @else
                                    {{-- Jika belum ada usulan, tampilkan tombol Membuat Usulan --}}
                                    <button type="button"
                                            onclick="showCreateModal('{{ $periode->id }}')"
                                            class="btn-animate inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-blue-500 border border-blue-500 rounded-lg hover:bg-blue-600 hover:text-white">
                                        <i data-lucide="plus" class="w-3 h-3 mr-1"></i>
                                        Membuat Usulan
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada periode usulan</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Saat ini tidak ada periode usulan NUPTK yang aktif untuk status kepegawaian Anda.
                </p>
            </div>
        @endif
        </div>
    </div>
</div>

{{-- Log Modal --}}
<div id="logModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-4/5 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i data-lucide="activity" class="w-5 h-5 mr-2 text-green-600"></i>
                Log Aktivitas Usulan
            </h3>
            <button type="button" onclick="closeLogModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <div id="logModalContent" class="max-h-96 overflow-y-auto">
            <div class="text-center py-8">
                <i data-lucide="loader" class="w-8 h-8 text-gray-400 mx-auto animate-spin"></i>
                <p class="text-sm text-gray-500 mt-2">Memuat log aktivitas...</p>
            </div>
        </div>
    </div>
</div>

{{-- Create Usulan Modal --}}
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full hidden z-50" style="left: 280px; right: 0; padding: 0 20px;">
    <div class="relative top-10 mx-auto p-0 border w-11/12 max-w-2xl shadow-2xl rounded-lg bg-white" style="margin-left: auto; margin-right: auto; max-width: calc(100vw - 320px); min-width: 400px;">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-green-600 to-emerald-600 rounded-t-lg">
            <h3 class="text-xl font-bold text-white flex items-center">
                <i data-lucide="user-check" class="w-6 h-6 mr-3"></i>
                Pilih Jenis Usulan NUPTK
            </h3>
            <button type="button" onclick="closeCreateModal()" class="text-white hover:text-gray-200 transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        {{-- Modal Content --}}
        <div id="createModalContent" class="p-6">
            <form id="createUsulanForm" method="POST" action="{{ route('pegawai-unmul.usulan-nuptk.store') }}" onsubmit="return validateForm()">
                @csrf
                <input type="hidden" id="periode_id" name="periode_usulan_id">

                <div class="mb-6">
                    <label class="block text-lg font-semibold text-gray-800 mb-4">Pilih Jenis Usulan NUPTK:</label>
                    <div class="space-y-4">
                        @if(isset($availableNuptkTypes) && count($availableNuptkTypes) > 0)
                            @foreach($availableNuptkTypes as $key => $label)
                                <div class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
                                    <input type="radio" id="jenis_{{ $key }}" name="jenis_nuptk" value="{{ $key }}" class="h-5 w-5 text-green-600 focus:ring-green-500 border-gray-300" {{ $loop->first ? 'checked' : '' }}>
                                    <label for="jenis_{{ $key }}" class="ml-4 block text-base font-medium text-gray-700 cursor-pointer">
                                        <div class="flex items-center">
                                            @if(in_array($key, ['dosen_tetap', 'dosen_tidak_tetap', 'pengajar_non_dosen']))
                                                <i data-lucide="graduation-cap" class="w-5 h-5 text-blue-600 mr-3"></i>
                                            @else
                                                <i data-lucide="users" class="w-5 h-5 text-orange-600 mr-3"></i>
                                            @endif
                                            <div>
                                                <div class="font-semibold">{{ $label }}</div>
                                                <div class="text-sm text-gray-500">
                                                    @if(in_array($key, ['dosen_tetap', 'dosen_tidak_tetap', 'pengajar_non_dosen']))
                                                        Pengajuan NUPTK untuk Dosen
                                                    @else
                                                        Pengajuan NUPTK untuk Tenaga Kependidikan
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <i data-lucide="alert-triangle" class="w-12 h-12 text-yellow-400 mx-auto mb-4"></i>
                                <p class="text-sm text-gray-500">Tidak ada jenis NUPTK yang tersedia untuk status kepegawaian Anda.</p>
                            </div>
                        @endif
                    </div>
                </div>

                @if(isset($availableNuptkTypes) && count($availableNuptkTypes) > 0)
                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                        <button type="button" onclick="closeCreateModal()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors">
                            Batal
                        </button>
                        <button type="submit" id="submitBtn"
                                class="px-6 py-2 text-sm font-medium text-white bg-green-600 border border-green-600 rounded-lg hover:bg-green-700 transition-colors">
                            <span id="submitText">Buat Usulan</span>
                            <i data-lucide="arrow-right" class="w-4 h-4 ml-2 inline"></i>
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>

<script>
function showLogs(usulanId) {
    const modal = document.getElementById('logModal');
    const content = document.getElementById('logModalContent');

    // Show modal with loading state
    modal.classList.remove('hidden');
    content.innerHTML = `
        <div class="text-center py-8">
            <i data-lucide="loader" class="w-8 h-8 text-gray-400 mx-auto animate-spin"></i>
            <p class="text-sm text-gray-500 mt-2">Memuat log aktivitas...</p>
        </div>
    `;

    // Fetch logs
    fetch(`/pegawai-unmul/usulan/${usulanId}/logs`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.logs && data.logs.length > 0) {
                let html = '<div class="space-y-4">';
                data.logs.forEach(log => {
                    const statusClass = log.status_badge_class || 'bg-gray-100 text-gray-800 border-gray-300';
                    const statusIcon = log.status_icon || 'help-circle';

                    html += `
                        <div class="border-l-4 border-green-400 pl-4 py-3 bg-gray-50 rounded-r-lg">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                                            <i data-lucide="${statusIcon}" class="w-3 h-3 mr-1"></i>
                                            ${log.status_baru || log.status_sebelumnya || 'N/A'}
                                        </span>
                                        <span class="text-xs text-gray-500">${log.formatted_date || log.created_at}</span>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900 mb-1">${log.action || log.keterangan || 'Aktivitas usulan'}</p>
                                    ${log.catatan ? `<p class="text-xs text-gray-600">${log.catatan}</p>` : ''}
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-gray-400">${log.user_name || 'System'}</span>
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                content.innerHTML = html;
            } else {
                content.innerHTML = `
                    <div class="text-center py-8">
                        <i data-lucide="file-text" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                        <p class="text-sm text-gray-500">Belum ada log aktivitas untuk usulan ini</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            content.innerHTML = `
                <div class="text-center py-8">
                    <i data-lucide="alert-triangle" class="w-12 h-12 text-red-400 mx-auto mb-4"></i>
                    <p class="text-sm text-red-500">Gagal memuat log aktivitas</p>
                    <p class="text-xs text-gray-500 mt-1">Silakan coba lagi</p>
                </div>
            `;
        });
}

function closeLogModal() {
    const modal = document.getElementById('logModal');
    modal.classList.add('hidden');
}

function showCreateModal(periodeId) {
    const modal = document.getElementById('createModal');
    const periodeIdInput = document.getElementById('periode_id');

    periodeIdInput.value = periodeId;
    modal.classList.remove('hidden');
}

function closeCreateModal() {
    const modal = document.getElementById('createModal');

    // Hide modal
    modal.classList.add('hidden');

    // Reset form
    document.getElementById('createUsulanForm').reset();

    // Remove active classes
    const radioContainers = document.querySelectorAll('#createModal .flex.items-center.p-4');
    radioContainers.forEach(container => {
        container.classList.remove('bg-green-50', 'border-green-300');
    });
}

function validateForm() {
    const selectedOption = document.querySelector('input[name="jenis_nuptk"]:checked');

    if (!selectedOption) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Silakan pilih jenis usulan NUPTK terlebih dahulu.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#f59e0b'
        });
        return false;
    }

    // Show loading state
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitIcon = submitBtn.querySelector('i');

    submitBtn.disabled = true;
    submitText.textContent = 'Memproses...';
    submitIcon.className = 'w-4 h-4 mr-2 inline animate-spin';
    submitIcon.setAttribute('data-lucide', 'loader');

    // Reinitialize Lucide icons
    if (window.lucide) {
        window.lucide.createIcons();
    }

    return true;
}

// Add click handlers for radio button containers
document.addEventListener('DOMContentLoaded', function() {
    const radioContainers = document.querySelectorAll('#createModal .flex.items-center.p-4');
    radioContainers.forEach(container => {
        container.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;

                // Remove active class from all containers
                radioContainers.forEach(c => c.classList.remove('bg-green-50', 'border-green-300'));

                // Add active class to clicked container
                this.classList.add('bg-green-50', 'border-green-300');
            }
        });
    });
});

function confirmDelete(usulanId) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Apakah Anda yakin ingin menghapus usulan ini? Tindakan ini tidak dapat dibatalkan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Menghapus Usulan...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Create form untuk DELETE request
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/pegawai-unmul/usulan-nuptk/${usulanId}`;

            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfToken);

            // Add method override untuk DELETE
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);

            // Submit form
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const logModal = document.getElementById('logModal');
    if (logModal) {
        logModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeLogModal();
            }
        });
    }

    const createModal = document.getElementById('createModal');
    if (createModal) {
        createModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeCreateModal();
            }
        });
    }

    // Initialize SweetAlert2 functions for index page

    // Global success handler
    window.showSuccess = function(message, title = 'Berhasil') {
        Swal.fire({
            icon: 'success',
            title: title,
            text: message,
            confirmButtonText: 'OK',
            confirmButtonColor: '#10b981',
            timer: 3000,
            timerProgressBar: true
        });
    };

    // Global error handler
    window.showError = function(message, title = 'Terjadi Kesalahan') {
        Swal.fire({
            icon: 'error',
            title: title,
            text: message,
            confirmButtonText: 'OK',
            confirmButtonColor: '#ef4444'
        });
    };

    // Global confirmation handler
    window.showConfirmation = function(message, title = 'Konfirmasi', callback) {
        Swal.fire({
            title: title,
            text: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed && callback) {
                callback();
            }
        });
    };

    // Global loading handler
    window.showLoading = function(message = 'Memproses...') {
        Swal.fire({
            title: message,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    };

    // Global close loading
    window.closeLoading = function() {
        Swal.close();
    };
});
</script>
@endsection
