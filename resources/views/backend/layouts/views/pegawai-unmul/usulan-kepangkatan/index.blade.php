@extends('backend.layouts.roles.pegawai-unmul.app')

@section('title', 'Usulan Kepangkatan Saya')

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
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    Usulan Kepangkatan Saya
                </h1>
                <p class="mt-2 text-gray-600">
                    Pantau status dan riwayat usulan Kepangkatan yang telah Anda ajukan.
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
                                        <a href="{{ route('pegawai-unmul.usulan-kepangkatan.show', $existingUsulan->id) }}"
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
                                        @if(in_array($existingUsulan->status_usulan, ['Draft Usulan', 'Usulan Dikirim ke Kepegawaian Universitas', 'Usulan Perbaikan Dari Pegawai Ke Kepegawaian Universitas']) || is_null($existingUsulan->status_usulan))
                                        <button type="button"
                                                data-usulan-id="{{ $existingUsulan->id }}"
                                                onclick="confirmDelete(this.dataset.usulanId)"
                                                class="btn-animate inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 hover:text-red-700">
                                            <i data-lucide="trash-2" class="w-3 h-3 mr-1"></i>
                                            Hapus
                                        </button>
                                        @endif
                                    </div>
                                @else
                                    {{-- Jika belum ada usulan, tampilkan tombol Membuat Usulan --}}
                                    <button type="button"
                                            onclick="showCreateModal({{ $periode->id }})"
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

{{-- Create Usulan Modal --}}
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full hidden z-50" style="left: 280px; right: 0; padding: 0 20px;">
    <div class="relative top-10 mx-auto p-0 border w-11/12 max-w-2xl shadow-2xl rounded-lg bg-white" style="margin-left: auto; margin-right: auto; max-width: calc(100vw - 320px); min-width: 400px;">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-t-lg">
            <h3 class="text-xl font-bold text-white flex items-center">
                <i data-lucide="plus-circle" class="w-6 h-6 mr-3"></i>
                Pilih Jenis Usulan Kepangkatan
            </h3>
            <button type="button" onclick="closeCreateModal()" class="text-white hover:text-gray-200 transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        {{-- Modal Content --}}
        <div id="createModalContent" class="p-6">
            <form id="createUsulanForm" method="POST" action="{{ route('pegawai-unmul.usulan-kepangkatan.store') }}" onsubmit="return validateForm()">
                @csrf
                <input type="hidden" id="periode_id" name="periode_id">
                
                <div class="mb-6">
                    <label class="block text-lg font-semibold text-gray-800 mb-4">Pilih Jenis Usulan Pangkat:</label>
                    <div class="space-y-4">
                        @if($statusKepegawaian === 'Dosen PNS')
                            {{-- Opsi untuk Dosen PNS --}}
                            <div class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
                                <input type="radio" id="jenis_dosen_pns" name="jenis_usulan" value="Dosen PNS" class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300" checked>
                                <label for="jenis_dosen_pns" class="ml-4 block text-base font-medium text-gray-700 cursor-pointer">
                                    <div class="flex items-center">
                                        <i data-lucide="graduation-cap" class="w-5 h-5 text-blue-600 mr-3"></i>
                                        <div>
                                            <div class="font-semibold">Dosen PNS</div>
                                            <div class="text-sm text-gray-500">Pengajuan kenaikan pangkat untuk Dosen PNS</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        @elseif($statusKepegawaian === 'Tenaga Kependidikan PNS')
                            {{-- Opsi untuk Tenaga Kependidikan PNS --}}
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <div class="mb-3">
                                    <h4 class="text-sm font-semibold text-gray-700 flex items-center">
                                        <i data-lucide="users" class="w-4 h-4 text-orange-600 mr-2"></i>
                                        Tenaga Kependidikan PNS
                                    </h4>
                                    <p class="text-xs text-gray-500">Pilih salah satu jenis jabatan:</p>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-white transition-colors cursor-pointer bg-white">
                                        <input type="radio" id="jenis_jabatan_administrasi" name="jenis_usulan" value="Jabatan Administrasi" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                        <label for="jenis_jabatan_administrasi" class="ml-3 block text-sm font-medium text-gray-700 cursor-pointer">
                                            <div class="flex items-center">
                                                <i data-lucide="briefcase" class="w-4 h-4 text-green-600 mr-2"></i>
                                                <div>
                                                    <div class="font-semibold">a. Jabatan Administrasi</div>
                                                    <div class="text-xs text-gray-500">Pengajuan kenaikan pangkat untuk jabatan administrasi</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-white transition-colors cursor-pointer bg-white">
                                        <input type="radio" id="jenis_jabatan_fungsional_tertentu" name="jenis_usulan" value="Jabatan Fungsional Tertentu" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                        <label for="jenis_jabatan_fungsional_tertentu" class="ml-3 block text-sm font-medium text-gray-700 cursor-pointer">
                                            <div class="flex items-center">
                                                <i data-lucide="award" class="w-4 h-4 text-purple-600 mr-2"></i>
                                                <div>
                                                    <div class="font-semibold">b. Jabatan Fungsional Tertentu</div>
                                                    <div class="text-xs text-gray-500">Pengajuan kenaikan pangkat untuk jabatan fungsional tertentu</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-white transition-colors cursor-pointer bg-white">
                                        <input type="radio" id="jenis_jabatan_struktural" name="jenis_usulan" value="Jabatan Struktural" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                        <label for="jenis_jabatan_struktural" class="ml-3 block text-sm font-medium text-gray-700 cursor-pointer">
                                            <div class="flex items-center">
                                                <i data-lucide="building" class="w-4 h-4 text-red-600 mr-2"></i>
                                                <div>
                                                    <div class="font-semibold">c. Jabatan Struktural</div>
                                                    <div class="text-xs text-gray-500">Pengajuan kenaikan pangkat untuk jabatan struktural</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Fallback jika status kepegawaian tidak dikenali --}}
                            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-center">
                                    <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600 mr-3"></i>
                                    <div>
                                        <h4 class="text-sm font-medium text-yellow-800">Status Kepegawaian Tidak Dikenali</h4>
                                        <p class="text-sm text-yellow-700 mt-1">Silakan hubungi administrator untuk mengatur status kepegawaian Anda.</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                
                {{-- Modal Footer --}}
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeCreateModal()" class="px-6 py-3 text-base font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                        <i data-lucide="x" class="w-4 h-4 mr-2 inline"></i>
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-3 text-base font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300 transform hover:scale-105 shadow-lg" id="submitBtn">
                        <i data-lucide="arrow-right" class="w-4 h-4 mr-2 inline"></i>
                        <span id="submitText">Lanjutkan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Mobile Modal Overlay --}}
<div id="createModalMobile" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 lg:hidden">
    <div class="relative top-10 mx-auto p-0 border w-11/12 max-w-md shadow-2xl rounded-lg bg-white">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-t-lg">
            <h3 class="text-lg font-bold text-white flex items-center">
                <i data-lucide="plus-circle" class="w-5 h-5 mr-2"></i>
                Pilih Jenis Usulan Kepangkatan
            </h3>
            <button type="button" onclick="closeCreateModal()" class="text-white hover:text-gray-200 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        {{-- Modal Content --}}
        <div class="p-4">
            <form method="POST" action="{{ route('pegawai-unmul.usulan-kepangkatan.store') }}" onsubmit="return validateForm()">
                @csrf
                <input type="hidden" id="periode_id_mobile" name="periode_id">
                
                <div class="mb-4">
                    <label class="block text-base font-semibold text-gray-800 mb-3">Pilih Jenis Usulan Pangkat:</label>
                    <div class="space-y-3">
                        @if($statusKepegawaian === 'Dosen PNS')
                            {{-- Opsi untuk Dosen PNS --}}
                            <div class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
                                <input type="radio" name="jenis_usulan" value="Dosen PNS" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300" checked>
                                <label class="ml-3 block text-sm font-medium text-gray-700 cursor-pointer">
                                    <div class="flex items-center">
                                        <i data-lucide="graduation-cap" class="w-4 h-4 text-blue-600 mr-2"></i>
                                        <div>
                                            <div class="font-semibold">Dosen PNS</div>
                                            <div class="text-xs text-gray-500">Pengajuan kenaikan pangkat untuk Dosen PNS</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        @elseif($statusKepegawaian === 'Tenaga Kependidikan PNS')
                            {{-- Opsi untuk Tenaga Kependidikan PNS --}}
                            <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                                <div class="mb-2">
                                    <h4 class="text-xs font-semibold text-gray-700 flex items-center">
                                        <i data-lucide="users" class="w-3 h-3 text-orange-600 mr-1"></i>
                                        Tenaga Kependidikan PNS
                                    </h4>
                                    <p class="text-xs text-gray-500">Pilih salah satu jenis jabatan:</p>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center p-2 border border-gray-200 rounded-lg hover:bg-white transition-colors cursor-pointer bg-white">
                                        <input type="radio" name="jenis_usulan" value="Jabatan Administrasi" class="h-3 w-3 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                        <label class="ml-2 block text-xs font-medium text-gray-700 cursor-pointer">
                                            <div class="flex items-center">
                                                <i data-lucide="briefcase" class="w-3 h-3 text-green-600 mr-1"></i>
                                                <div>
                                                    <div class="font-semibold">a. Jabatan Administrasi</div>
                                                    <div class="text-xs text-gray-500">Jabatan administrasi</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="flex items-center p-2 border border-gray-200 rounded-lg hover:bg-white transition-colors cursor-pointer bg-white">
                                        <input type="radio" name="jenis_usulan" value="Jabatan Fungsional Tertentu" class="h-3 w-3 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                        <label class="ml-2 block text-xs font-medium text-gray-700 cursor-pointer">
                                            <div class="flex items-center">
                                                <i data-lucide="award" class="w-3 h-3 text-purple-600 mr-1"></i>
                                                <div>
                                                    <div class="font-semibold">b. Jabatan Fungsional Tertentu</div>
                                                    <div class="text-xs text-gray-500">Jabatan fungsional tertentu</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="flex items-center p-2 border border-gray-200 rounded-lg hover:bg-white transition-colors cursor-pointer bg-white">
                                        <input type="radio" name="jenis_usulan" value="Jabatan Struktural" class="h-3 w-3 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                        <label class="ml-2 block text-xs font-medium text-gray-700 cursor-pointer">
                                            <div class="flex items-center">
                                                <i data-lucide="building" class="w-3 h-3 text-red-600 mr-1"></i>
                                                <div>
                                                    <div class="font-semibold">c. Jabatan Struktural</div>
                                                    <div class="text-xs text-gray-500">Jabatan struktural</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Fallback jika status kepegawaian tidak dikenali --}}
                            <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-center">
                                    <i data-lucide="alert-triangle" class="w-4 h-4 text-yellow-600 mr-2"></i>
                                    <div>
                                        <h4 class="text-xs font-medium text-yellow-800">Status Kepegawaian Tidak Dikenali</h4>
                                        <p class="text-xs text-yellow-700 mt-1">Silakan hubungi administrator.</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                
                {{-- Modal Footer --}}
                <div class="flex justify-end space-x-2 pt-3 border-t border-gray-200">
                    <button type="button" onclick="closeCreateModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                        <i data-lucide="x" class="w-3 h-3 mr-1 inline"></i>
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i data-lucide="arrow-right" class="w-3 h-3 mr-1 inline"></i>
                        Lanjutkan
                    </button>
                </div>
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
    fetch(`/pegawai-unmul/usulan-kepangkatan/${usulanId}/logs`)
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

function showCreateModal(periodeId) {
    const modal = document.getElementById('createModal');
    const modalMobile = document.getElementById('createModalMobile');
    const periodeIdInput = document.getElementById('periode_id');
    const periodeIdInputMobile = document.getElementById('periode_id_mobile');
    
    // Set periode ID for both modals
    periodeIdInput.value = periodeId;
    periodeIdInputMobile.value = periodeId;
    
    // Show appropriate modal based on screen size
    if (window.innerWidth >= 1024) { // lg breakpoint
        modal.classList.remove('hidden');
    } else {
        modalMobile.classList.remove('hidden');
    }
    
    // Add click handlers for radio button containers
    const radioContainers = document.querySelectorAll('#createModal .flex.items-center.p-4, #createModal .flex.items-center.p-3, #createModalMobile .flex.items-center.p-3, #createModalMobile .flex.items-center.p-2');
    radioContainers.forEach(container => {
        container.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                
                // Remove active class from all containers
                radioContainers.forEach(c => c.classList.remove('bg-indigo-50', 'border-indigo-300'));
                
                // Add active class to clicked container
                this.classList.add('bg-indigo-50', 'border-indigo-300');
            }
        });
    });
}

function closeCreateModal() {
    const modal = document.getElementById('createModal');
    const modalMobile = document.getElementById('createModalMobile');
    
    // Hide both modals
    modal.classList.add('hidden');
    modalMobile.classList.add('hidden');
    
    // Reset forms
    document.getElementById('createUsulanForm').reset();
    document.querySelector('#createModalMobile form').reset();
    
    // Remove active classes
    const radioContainers = document.querySelectorAll('#createModal .flex.items-center.p-4, #createModalMobile .flex.items-center.p-3');
    radioContainers.forEach(container => {
        container.classList.remove('bg-indigo-50', 'border-indigo-300');
    });
}

function validateForm() {
    const selectedOption = document.querySelector('input[name="jenis_usulan"]:checked');
    
    if (!selectedOption) {
        alert('Silakan pilih jenis usulan pangkat terlebih dahulu.');
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

// Handle window resize for modal switching
window.addEventListener('resize', function() {
    const modal = document.getElementById('createModal');
    const modalMobile = document.getElementById('createModalMobile');
    
    if (window.innerWidth >= 1024) {
        // Desktop view - hide mobile modal if it's open
        if (!modalMobile.classList.contains('hidden')) {
            modalMobile.classList.add('hidden');
            modal.classList.remove('hidden');
        }
    } else {
        // Mobile view - hide desktop modal if it's open
        if (!modal.classList.contains('hidden')) {
            modal.classList.add('hidden');
            modalMobile.classList.remove('hidden');
        }
    }
});

function confirmDelete(usulanId) {
    if (confirm('Apakah Anda yakin ingin menghapus usulan ini? Tindakan ini tidak dapat dibatalkan.')) {
        // Create form untuk DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/pegawai-unmul/usulan-kepangkatan/${usulanId}`;
        
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
