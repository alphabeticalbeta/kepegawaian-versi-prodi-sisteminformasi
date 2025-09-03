@extends('backend.layouts.roles.pegawai-unmul.app')

@section('title', 'Usulan Pengaktifan Kembali Saya')

@section('content')
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
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    Usulan Pengaktifan Kembali Saya
                </h1>
                <p class="mt-2 text-gray-600">
                    Pantau status dan riwayat usulan Pengaktifan Kembali yang telah Anda ajukan.
                </p>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        @if($periodeUsulans->count() > 0)
            <table class="w-full text-sm text-center text-gray-600">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 align-middle">No</th>
                        <th scope="col" class="px-6 py-4 align-middle">Nama Periode</th>
                        <th scope="col" class="px-6 py-4 align-middle">Tanggal Pembukaan</th>
                        <th scope="col" class="px-6 py-4 align-middle">Tanggal Penutupan</th>
                        <th scope="col" class="px-6 py-4 align-middle">Tanggal Awal Perbaikan</th>
                        <th scope="col" class="px-6 py-4 align-middle">Tanggal Akhir Perbaikan</th>
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
                                {{ $periode->tanggal_mulai ? $periode->tanggal_mulai->isoFormat('D MMMM YYYY') : '-' }}
                            </td>
                            <td class="px-6 py-4 align-middle">
                                {{ $periode->tanggal_selesai ? $periode->tanggal_selesai->isoFormat('D MMMM YYYY') : '-' }}
                            </td>
                            <td class="px-6 py-4 align-middle">
                                {{ $periode->tanggal_mulai_perbaikan ? $periode->tanggal_mulai_perbaikan->isoFormat('D MMMM YYYY') : '-' }}
                            </td>
                            <td class="px-6 py-4 align-middle">
                                {{ $periode->tanggal_selesai_perbaikan ? $periode->tanggal_selesai_perbaikan->isoFormat('D MMMM YYYY') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-center align-middle">
                                @if($existingUsulan)
                                    {{-- Jika sudah ada usulan, tampilkan tombol Detail, Log, dan Hapus --}}
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('pegawai-unmul.usulan-pengaktifan-kembali.show', $existingUsulan->id) }}"
                                           class="btn-animate inline-flex items-center px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 hover:text-indigo-700">
                                            <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                            Lihat Detail
                                        </a>
                                        <button type="button"
                                                data-usulan-id="{{ $existingUsulan->id }}"
                                                onclick="showLogs({{ $existingUsulan->id }})"
                                                class="btn-animate inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-600 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 hover:text-green-700">
                                            <i data-lucide="activity" class="w-3 h-3 mr-1"></i>
                                            Log
                                        </button>
                                        <button type="button"
                                                data-usulan-id="{{ $existingUsulan->id }}"
                                                onclick="confirmDelete(this.dataset.usulanId)"
                                                class="btn-animate inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 hover:text-red-700">
                                            <i data-lucide="trash-2" class="w-3 h-3 mr-1"></i>
                                            Hapus
                                        </button>
                                    </div>
                                @else
                                    {{-- Jika belum ada usulan, tampilkan tombol Membuat Usulan --}}
                                    <a href="{{ route('pegawai-unmul.usulan-pengaktifan-kembali.create') }}?periode_id={{ $periode->id }}"
                                       class="btn-animate inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-blue-500 border border-blue-500 rounded-lg hover:bg-gray-500 hover:text-white">
                                        <i data-lucide="plus" class="w-3 h-3 mr-1"></i>
                                        Membuat Usulan
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada periode usulan yang tersedia</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Saat ini tidak ada periode usulan yang sesuai dengan status kepegawaian Anda.
                </p>
            </div>
        @endif
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
            console.error('Error loading logs:', error);
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
});
</script>
@endsection