@extends('backend.layouts.roles.kepegawaian-universitas.app')

@section('title', 'Master Data Jabatan')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-slate-800">Master Data Jabatan</h1>
                        <p class="text-slate-600">Kelola data jabatan dengan hirarki yang tepat untuk sistem usulan</p>
                    </div>
                </div>
                <a href="{{ route('backend.kepegawaian-universitas.jabatan.create') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Jabatan
                </a>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-green-800 font-medium">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <span class="text-red-800 font-medium">{{ session('error') }}</span>
            </div>
        </div>
        @endif

        <!-- Filter Section -->
        <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/30 overflow-hidden mb-6">
            <div class="p-6 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-blue-50/30">
                <h2 class="text-xl font-bold text-slate-800">Filter & Pencarian</h2>
                <p class="text-slate-600">Filter data jabatan berdasarkan kriteria tertentu</p>
            </div>
            <div class="p-6">
                <form method="GET" action="{{ route('backend.kepegawaian-universitas.jabatan.index') }}" id="filterForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                        <!-- Filter Jenis Pegawai -->
                        <div>
                            <label for="jenis_pegawai" class="block text-sm font-medium text-slate-700 mb-2">Jenis Pegawai</label>
                            <select name="jenis_pegawai" id="jenis_pegawai"
                                    class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="">Semua Jenis</option>
                                @foreach($filterData['jenis_pegawai_options'] as $option)
                                    <option value="{{ $option }}" {{ request('jenis_pegawai') == $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Jenis Jabatan -->
                        <div>
                            <label for="jenis_jabatan" class="block text-sm font-medium text-slate-700 mb-2">Jenis Jabatan</label>
                            <select name="jenis_jabatan" id="jenis_jabatan"
                                    class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="">Semua Jenis</option>
                                @foreach($filterData['jenis_jabatan_options'] as $option)
                                    <option value="{{ $option }}" {{ request('jenis_jabatan') == $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Search Nama Jabatan -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-slate-700 mb-2">Nama Jabatan</label>
                            <input type="text" name="search" id="search"
                                value="{{ request('search') }}"
                                placeholder="Cari nama jabatan..."
                                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        </div>

                        <!-- Filter Hirarki -->
                        <div>
                            <label for="has_hierarchy" class="block text-sm font-medium text-slate-700 mb-2">Status Hirarki</label>
                            <select name="has_hierarchy" id="has_hierarchy"
                                    class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="">Semua</option>
                                <option value="yes" {{ request('has_hierarchy') == 'yes' ? 'selected' : '' }}>Dengan Hirarki</option>
                                <option value="no" {{ request('has_hierarchy') == 'no' ? 'selected' : '' }}>Tanpa Hirarki</option>
                            </select>
                        </div>

                        <!-- Filter Eligible Usulan -->
                        <div>
                            <label for="eligible_usulan" class="block text-sm font-medium text-slate-700 mb-2">Dapat Usulan</label>
                            <select name="eligible_usulan" id="eligible_usulan"
                                    class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="">Semua</option>
                                <option value="yes" {{ request('eligible_usulan') == 'yes' ? 'selected' : '' }}>Ya</option>
                                <option value="no" {{ request('eligible_usulan') == 'no' ? 'selected' : '' }}>Tidak</option>
                            </select>
                        </div>
                    </div>

                    <!-- Filter Action Buttons -->
                    <div class="mt-6 flex justify-between items-center">
                        <div class="flex space-x-3">
                            <button type="submit"
                                    class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Cari
                            </button>
                            <a href="{{ route('backend.kepegawaian-universitas.jabatan.index') }}"
                               class="px-6 py-3 text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition-colors duration-200">
                                Reset
                            </a>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('backend.kepegawaian-universitas.jabatan.export') }}?{{ http_build_query(request()->query()) }}"
                               class="px-6 py-3 text-green-600 bg-green-100 rounded-xl hover:bg-green-200 transition-colors duration-200">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Export
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/30 overflow-hidden">
            <div class="p-6 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-blue-50/30">
                <h2 class="text-xl font-bold text-slate-800">Data Jabatan</h2>
                <p class="text-slate-600">Jabatan diurutkan berdasarkan hirarki dari terendah ke tertinggi</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Jenis Pegawai</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Jenis Jabatan</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nama Jabatan</th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Level</th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Usulan</th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse($jabatans as $index => $jabatan)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                {{ ($jabatans->currentPage() - 1) * $jabatans->perPage() + $index + 1 }}
                            </td>

                            <!-- Jenis Pegawai -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($jabatan->jenis_pegawai == 'Dosen')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        Dosen
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        Tenaga Kependidikan
                                    </span>
                                @endif
                            </td>

                            <!-- Jenis Jabatan -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                {{ $jabatan->jenis_jabatan }}
                            </td>

                            <!-- Nama Jabatan -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($jabatan->hierarchy_level)
                                        <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-bold mr-3">
                                            {{ $jabatan->hierarchy_level }}
                                        </div>
                                    @else
                                        <div class="flex-shrink-0 w-8 h-8 bg-slate-200 text-slate-600 rounded-full flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-slate-900">{{ $jabatan->jabatan }}</div>
                                        <div class="text-xs text-slate-500">
                                            {{ $jabatan->hierarchy_level ? 'Dengan Hirarki' : 'Tanpa Hirarki' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Level -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($jabatan->hierarchy_level)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                        </svg>
                                        Level {{ $jabatan->hierarchy_level }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                        </svg>
                                        Non-Hierarki
                                    </span>
                                @endif
                            </td>

                            <!-- Usulan -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($jabatan->isEligibleForUsulan())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Dapat
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Tidak
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center items-center gap-2">
                                    <a href="{{ route('backend.kepegawaian-universitas.jabatan.edit', $jabatan) }}"
                                       class="p-2 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <button type="button"
                                            onclick="deleteJabatan({{ $jabatan->id }}, '{{ $jabatan->jabatan }}')"
                                            class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-slate-900">Tidak ada data jabatan</h3>
                                    <p class="text-slate-500 mt-1">Tambah data jabatan untuk memulai</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($jabatans->hasPages())
            <div class="bg-white px-6 py-4 border-t border-slate-200">
                {{ $jabatans->links() }}
            </div>
            @endif
        </div>

        <!-- Summary Statistics -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-slate-200 p-4 text-center shadow-sm">
                <div class="text-2xl font-bold text-slate-900">{{ $stats['total'] }}</div>
                <div class="text-sm text-slate-600">Total Jabatan</div>
            </div>

            <div class="bg-blue-50 rounded-xl border border-blue-200 p-4 text-center shadow-sm">
                <div class="text-2xl font-bold text-blue-700">{{ $stats['dengan_hirarki'] }}</div>
                <div class="text-sm text-blue-600">Dengan Hirarki</div>
            </div>

            <div class="bg-orange-50 rounded-xl border border-orange-200 p-4 text-center shadow-sm">
                <div class="text-2xl font-bold text-orange-700">{{ $stats['tanpa_hirarki'] }}</div>
                <div class="text-sm text-orange-600">Tanpa Hirarki</div>
            </div>

            <div class="bg-green-50 rounded-xl border border-green-200 p-4 text-center shadow-sm">
                <div class="text-2xl font-bold text-green-700">{{ $stats['dapat_usulan'] }}</div>
                <div class="text-sm text-green-600">Dapat Usulan</div>
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
    const notificationContainer = document.getElementById('notificationContainer');

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

    // Delete jabatan function
    window.deleteJabatan = function(id, jabatanName) {
        if (confirm(`Apakah Anda yakin ingin menghapus jabatan "${escapeHtml(jabatanName)}"?`)) {
            // Show loading notification
            showNotification('Menghapus jabatan...', 'info');

            fetch(`/kepegawaian-universitas/jabatan/${id}`, {
                method: 'DELETE',
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
                    showNotification(data.message || 'Jabatan berhasil dihapus!', 'success');

                    // Reload page after 2 seconds
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showNotification(data.message || 'Gagal menghapus jabatan.', 'error');
                }
            })
            .catch(error => {
                showNotification('Terjadi kesalahan pada server. Silakan coba lagi.', 'error');
            });
        }
    };

    // Auto-hide flash messages after 5 seconds
    const flashMessages = document.querySelectorAll('.mb-6.p-4');
    flashMessages.forEach(message => {
        setTimeout(() => {
            message.style.opacity = '0';
            message.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                if (message.parentNode) {
                    message.parentNode.removeChild(message);
                }
            }, 300);
        }, 5000);
    });
});
</script>
@endsection
