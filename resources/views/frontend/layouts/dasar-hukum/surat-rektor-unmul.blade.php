@extends('frontend.layouts.app')

@section('title', 'Surat Rektor Universitas Mulawarman - Universitas Mulawarman')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="relative overflow-hidden shadow-2xl">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative px-6 py-5 sm:px-8 sm:py-20">
            <div class="mx-auto max-w-4xl text-center">
                <div class="mb-6 flex justify-center">
                    <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo UNMUL" class="h-32 w-auto object-contain">
                </div>
                <h1 class="text-4xl font-bold tracking-tight text-black sm:text-5xl mb-4">
                    Surat Rektor Universitas Mulawarman
                </h1>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="w-full px-2 sm:px-4 lg:px-6 py-6">
        <!-- Tab Navigation -->
        <div class="mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="flex flex-wrap">
                    <button id="tab-all" onclick="switchTab('all')"
                            class="tab-button flex-1 px-4 py-3 text-sm font-medium text-center border-b-2 border-transparent hover:text-violet-600 hover:border-violet-300 transition-all duration-200 bg-violet-50 text-violet-700 border-violet-500">
                        <span class="flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Semua
                        </span>
                    </button>
                    <button id="tab-peraturan" onclick="switchTab('peraturan')"
                            class="tab-button flex-1 px-4 py-3 text-sm font-medium text-center border-b-2 border-transparent hover:text-violet-600 hover:border-violet-300 transition-all duration-200">
                        <span class="flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Peraturan
                        </span>
                    </button>
                    <button id="tab-surat_keputusan" onclick="switchTab('surat_keputusan')"
                            class="tab-button flex-1 px-4 py-3 text-sm font-medium text-center border-b-2 border-transparent hover:text-violet-600 hover:border-violet-300 transition-all duration-200">
                        <span class="flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Surat Keputusan
                        </span>
                    </button>
                    <button id="tab-sk_non_pns" onclick="switchTab('sk_non_pns')"
                            class="tab-button flex-1 px-4 py-3 text-sm font-medium text-center border-b-2 border-transparent hover:text-violet-600 hover:border-violet-300 transition-all duration-200">
                        <span class="flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            SK Non PNS
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Search Section -->
        <div class="mb-4 flex justify-end">
            <div class="relative w-96">
                <input type="text" id="searchInput" placeholder="Cari surat rektor  .  .  .  .  .  .  .  . "
                       class="w-full px-2 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base transition-all duration-300 hover:border-violet-400 focus:border-violet-500 focus:ring-violet-500">
                <div id="searchLoading" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-violet-600"></div>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div id="loadingState" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-violet-600"></div>
            <p class="mt-4 text-gray-600">Memuat surat rektor...</p>
        </div>

        <!-- Error State -->
        <div id="errorState" class="hidden text-center py-12">
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 max-w-md mx-auto">
                <svg class="mx-auto h-12 w-12 text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-lg font-medium text-red-800 mb-2">Gagal Memuat Surat Rektor</h3>
                <p class="text-red-600 mb-4">Terjadi kesalahan saat memuat data surat rektor.</p>
                <button onclick="loadSuratRektorUnmul()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                    Coba Lagi
                </button>
            </div>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="hidden text-center py-12">
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 max-w-md mx-auto">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-800 mb-2">Tidak Ada Surat Rektor</h3>
                <p class="text-gray-600">Belum ada surat rektor yang tersedia saat ini.</p>
            </div>
        </div>

        <!-- Surat Rektor Grid -->
        <div id="suratRektorUnmulGrid" class="hidden grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 transition-all duration-500">
            <!-- Surat Rektor cards will be populated here -->
        </div>

        <!-- Pagination -->
        <div id="paginationContainer" class="hidden mt-6">
            <nav class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <button id="prevPage" class="px-3 py-2 text-sm font-medium text-black bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>

                    <div id="pageNumbers" class="flex items-center space-x-1">
                        <!-- Page numbers will be populated here -->
                    </div>

                    <button id="nextPage" class="px-3 py-2 text-sm font-medium text-black bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>

                <div class="text-sm text-gray-700">
                    Menampilkan <span id="showingFrom">0</span> - <span id="showingTo">0</span> dari <span id="totalItems">0</span> surat rektor
                </div>
            </nav>
        </div>
    </div>
</div>

<!-- Surat Rektor Detail Modal -->
<div id="suratRektorUnmulModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b">
                <h3 id="modalTitle" class="text-xl font-semibold text-gray-900">Detail Surat Rektor Universitas Mulawarman</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                <div id="modalContent">
                    <!-- Modal content will be populated here -->
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentPage = 1;
let totalPages = 1;
let currentSearch = '';
let currentTab = 'all';
let abortController = null; // For request cancellation

// Per-tab state management
let tabStates = {
    all: { search: '', page: 1, totalPages: 1 },
    peraturan: { search: '', page: 1, totalPages: 1 },
    surat_keputusan: { search: '', page: 1, totalPages: 1 },
    sk_non_pns: { search: '', page: 1, totalPages: 1 }
};

// Load surat rektor unmul data
async function loadSuratRektorUnmul(page = 1, isSearch = false, isPagination = false, isTabSwitch = false) {
    try {
        // Cancel previous request if it exists
        if (abortController) {
            abortController.abort();
        }

        // Create new abort controller for this request
        abortController = new AbortController();

        // Only show loading for initial load, not for search, pagination, or tab switching
        if (!isSearch && !isPagination && !isTabSwitch) {
            showLoading();
        }

        const params = new URLSearchParams({
            page: page,
            per_page: 8,
            sort: 'latest',
            search: currentSearch,
            jenis: 'surat_rektor'
        });

        // Add sub_jenis filter if not 'all'
        if (currentTab !== 'all') {
            params.append('sub_jenis', currentTab);
        }

        const response = await fetch(`/api/dasar-hukum?${params}`, {
            signal: abortController.signal,
            cache: 'no-cache' // Ensure fresh data for search
        });
        const data = await response.json();

        if (data.success) {
            // Hide search loading animation
            hideSearchLoading();

            // For search and pagination, update content immediately without delay
            if (isSearch || isPagination) {
                displaySuratRektorUnmul(data.data);
                updatePagination(data.pagination);

                // Show elements immediately for smooth experience
                const gridElement = document.getElementById('suratRektorUnmulGrid');
                const paginationElement = document.getElementById('paginationContainer');

                if (gridElement) {
                    gridElement.classList.remove('hidden');
                }

                if (paginationElement) {
                    paginationElement.classList.remove('hidden');
                }
            } else {
                // For initial load, use setTimeout for smooth transition
                displaySuratRektorUnmul(data.data);
                updatePagination(data.pagination);

                setTimeout(() => {
                    const loadingElement = document.getElementById('loadingState');
                    if (loadingElement) {
                        loadingElement.remove();
                    }

                    // Show other elements
                    const gridElement = document.getElementById('suratRektorUnmulGrid');
                    const paginationElement = document.getElementById('paginationContainer');

                    if (gridElement) {
                        gridElement.classList.remove('hidden');
                    }

                    if (paginationElement) {
                        paginationElement.classList.remove('hidden');
                    }
                }, 100);
            }
        } else {
            console.error('API returned success: false');
            if (!isSearch && !isPagination) {
                const loadingElement = document.getElementById('loadingState');
                if (loadingElement) {
                    loadingElement.remove();
                }
            }
            showError();
        }
    } catch (error) {
        // Don't show error for aborted requests (user typed new search)
        if (error.name === 'AbortError') {
            return;
        }

        console.error('Error loading surat rektor unmul:', error);
        if (!isSearch && !isPagination) {
            const loadingElement = document.getElementById('loadingState');
            if (loadingElement) {
                loadingElement.remove();
            }
        }
        showError();
    }
}

// Display surat rektor unmul cards
function displaySuratRektorUnmul(suratRektorUnmul) {
    const grid = document.getElementById('suratRektorUnmulGrid');
    const emptyState = document.getElementById('emptyState');

    if (suratRektorUnmul.length === 0) {
        grid.classList.add('hidden');
        emptyState.classList.remove('hidden');
        return;
    }

    emptyState.classList.add('hidden');
    grid.classList.remove('hidden');

    grid.innerHTML = suratRektorUnmul.map((item, index) => `
        <article class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer transform hover:scale-105 animate-fade-in" style="animation-delay: ${index * 100}ms" onclick="showSuratRektorUnmulDetail(${item.id})">
            <div class="relative">
                ${item.thumbnail ? `
                    <img src="/admin-universitas/dasar-hukum-document/${item.thumbnail.split('/').pop()}"
                         alt="${item.judul}"
                         class="w-full h-48 object-cover"
                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjNmNGY2Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzljYTNhZiIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPk5vIEltYWdlPC90ZXh0Pjwvc3ZnPg=='">
                ` : `
                    <div class="w-full h-32 bg-gradient-to-br from-violet-400 to-purple-500 flex items-center justify-center transition-transform duration-300 hover:scale-110">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                `}

                ${item.is_featured ? `
                    <div class="absolute top-4 left-4">
                        <span class="bg-yellow-500 text-white text-xs font-semibold px-2 py-1 rounded-full shadow-lg">
                            Featured
                        </span>
                    </div>
                ` : ''}

                ${item.is_pinned ? `
                    <div class="absolute top-4 right-4">
                        <span class="bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded-full shadow-lg">
                            Pinned
                        </span>
                    </div>
                ` : ''}

            </div>

            <div class="p-3">
                <div class="flex items-center text-xs text-gray-500 mb-2">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    ${formatDate(item.tanggal_dokumen)}
                </div>

                <h3 class="text-sm font-semibold text-gray-900 mb-2 line-clamp-2 hover:text-violet-600 transition-colors duration-300">
                    ${item.judul}
                </h3>

                ${item.sub_jenis ? `
                    <div class="mb-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-violet-100 text-violet-800">
                            ${getSubJenisLabel(item.sub_jenis)}
                        </span>
                    </div>
                ` : ''}

                <p class="text-xs text-gray-600 mb-3 line-clamp-2">
                    ${stripHtml(item.konten)}
                </p>

                <div class="flex items-center justify-between">
                    <div class="flex items-center text-xs text-gray-500">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        ${item.penulis || 'Admin'}
                    </div>

                    <div class="text-violet-600 font-medium text-xs transition-colors duration-300 hover:text-violet-800">
                        Klik untuk melihat →
                    </div>
                </div>

                ${item.tags && item.tags.length > 0 ? `
                    <div class="mt-4 flex flex-wrap gap-2">
                        ${item.tags.map(tag => `
                            <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full hover:bg-violet-100 hover:text-violet-700 transition-colors duration-300">
                                ${tag}
                            </span>
                        `).join('')}
                    </div>
                ` : ''}
            </div>
        </article>
    `).join('');
}

// Show surat rektor unmul detail modal
async function showSuratRektorUnmulDetail(id) {
    try {
        const response = await fetch(`/api/dasar-hukum/${id}`);
        const data = await response.json();

        if (data.success) {
            const item = data.data;

            document.getElementById('modalTitle').textContent = item.judul;
            document.getElementById('modalContent').innerHTML = `
                <div class="space-y-6">
                    <div class="flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        ${formatDate(item.tanggal_dokumen)}
                        <span class="mx-2">•</span>
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        ${item.penulis || 'Admin'}
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-semibold text-gray-700">Nomor Dokumen:</span>
                            <p class="text-gray-600">${item.nomor_dokumen || '-'}</p>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700">Instansi:</span>
                            <p class="text-gray-600">${item.nama_instansi || '-'}</p>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700">Jenis:</span>
                            <p class="text-gray-600">${item.jenis_label || 'Surat Rektor Universitas Mulawarman'}</p>
                            ${item.sub_jenis ? `
                                <div class="mt-1">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-violet-100 text-violet-800">
                                        ${getSubJenisLabel(item.sub_jenis)}
                                    </span>
                                </div>
                            ` : ''}
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700">Status:</span>
                            <p class="text-gray-600">${item.status_label || '-'}</p>
                        </div>
                    </div>

                    ${item.thumbnail ? `
                        <img src="/admin-universitas/dasar-hukum-document/${item.thumbnail.split('/').pop()}"
                             alt="${item.judul}"
                             class="w-full h-60 object-cover rounded-lg"
                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjNmNGY2Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzljYTNhZiIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPk5vIEltYWdlPC90ZXh0Pjwvc3ZnPg=='">
                    ` : ''}

                    <div class="prose max-w-none">
                        ${item.konten}
                    </div>

                    ${item.lampiran && item.lampiran.length > 0 ? `
                        <div class="border-t pt-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Lampiran</h4>
                            <div class="space-y-2">
                                ${item.lampiran.map(file => `
                                    <a href="/admin-universitas/dasar-hukum-document/${file.split('/').pop()}" target="_blank"
                                       class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                        <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="text-gray-700">${file.split('/').pop()}</span>
                                    </a>
                                `).join('')}
                            </div>
                        </div>
                    ` : ''}
                </div>
            `;

            document.getElementById('suratRektorUnmulModal').classList.remove('hidden');
        }
    } catch (error) {
        console.error('Error loading surat rektor unmul detail:', error);
    }
}

// Close modal
function closeModal() {
    document.getElementById('suratRektorUnmulModal').classList.add('hidden');
}

// Update pagination
function updatePagination(pagination) {
    const container = document.getElementById('paginationContainer');
    const pageNumbers = document.getElementById('pageNumbers');
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');

    currentPage = pagination.current_page;
    totalPages = pagination.last_page;

    // Show/hide pagination
    if (totalPages > 1) {
        container.classList.remove('hidden');
    } else {
        container.classList.add('hidden');
    }

    // Update page numbers
    pageNumbers.innerHTML = '';
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);

    for (let i = startPage; i <= endPage; i++) {
        const pageBtn = document.createElement('button');
        pageBtn.textContent = i;
        pageBtn.className = `px-3 py-2 text-sm font-medium rounded-lg ${
            i === currentPage
                ? 'bg-violet-600 text-white border border-violet-600'
                : 'text-black bg-white border border-gray-300 hover:bg-gray-50 hover:text-black'
        }`;
        pageBtn.onclick = () => loadSuratRektorUnmul(i, false, true);
        pageNumbers.appendChild(pageBtn);
    }

    // Update prev/next buttons
    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled = currentPage === totalPages;
    prevBtn.onclick = () => currentPage > 1 && loadSuratRektorUnmul(currentPage - 1, false, true);
    nextBtn.onclick = () => currentPage < totalPages && loadSuratRektorUnmul(currentPage + 1, false, true);

    // Update showing info
    document.getElementById('showingFrom').textContent = pagination.from || 0;
    document.getElementById('showingTo').textContent = pagination.to || 0;
    document.getElementById('totalItems').textContent = pagination.total || 0;

    // Save current tab state
    saveTabState(currentTab);
}

// Show loading state
function showLoading() {
    const loadingElement = document.getElementById('loadingState');
    if (loadingElement) {
        loadingElement.style.display = 'block';
        loadingElement.classList.remove('hidden');
    }
    document.getElementById('suratRektorUnmulGrid').classList.add('hidden');
    document.getElementById('emptyState').classList.add('hidden');
    document.getElementById('errorState').classList.add('hidden');
    document.getElementById('paginationContainer').classList.add('hidden');
}

// Show error state
function showError() {
    document.getElementById('loadingState').classList.add('hidden');
    document.getElementById('suratRektorUnmulGrid').classList.add('hidden');
    document.getElementById('emptyState').classList.add('hidden');
    document.getElementById('errorState').classList.remove('hidden');
    document.getElementById('paginationContainer').classList.add('hidden');
}

// Show search loading animation
function showSearchLoading() {
    const searchLoading = document.getElementById('searchLoading');
    if (searchLoading) {
        searchLoading.classList.remove('hidden');
    }
}

// Hide search loading animation
function hideSearchLoading() {
    const searchLoading = document.getElementById('searchLoading');
    if (searchLoading) {
        searchLoading.classList.add('hidden');
    }
}

// Utility functions
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function getSubJenisLabel(subJenis) {
    const labels = {
        'peraturan': 'Peraturan',
        'surat_keputusan': 'Surat Keputusan',
        'sk_non_pns': 'SK Non PNS'
    };
    return labels[subJenis] || subJenis;
}

function stripHtml(html) {
    const tmp = document.createElement('div');
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || '';
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Event listeners
document.getElementById('searchInput').addEventListener('input', debounce((e) => {
    currentSearch = e.target.value.trim();

    // Save current tab state
    saveTabState(currentTab);

    // Show search loading animation
    showSearchLoading();

    // If search is empty, load all surat rektor unmul immediately
    if (currentSearch === '') {
        loadSuratRektorUnmul(1, true);
    } else {
        // Only search if there's actual content
        loadSuratRektorUnmul(1, true);
    }
}, 300)); // 300ms debounce for faster response

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Tab switching functionality
function switchTab(tab) {
    // Update current tab
    currentTab = tab;

    // Update URL parameter
    const url = new URL(window.location);
    if (tab === 'all') {
        url.searchParams.delete('tab');
    } else {
        url.searchParams.set('tab', tab);
    }
    window.history.pushState({}, '', url);

    // Update tab UI
    updateTabUI(tab);

    // Load tab state
    loadTabState(tab);

    // Load data for the tab without showing spinner
    loadSuratRektorUnmul(1, false, false, true);
}

// Update tab UI
function updateTabUI(activeTab) {
    // Remove active class from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('bg-violet-50', 'text-violet-700', 'border-violet-500');
        button.classList.add('text-gray-500', 'border-transparent');
    });

    // Add active class to current tab
    const activeButton = document.getElementById(`tab-${activeTab}`);
    if (activeButton) {
        activeButton.classList.remove('text-gray-500', 'border-transparent');
        activeButton.classList.add('bg-violet-50', 'text-violet-700', 'border-violet-500');
    }
}

// Load tab state
function loadTabState(tab) {
    const state = tabStates[tab];
    currentSearch = state.search;
    currentPage = state.page;
    totalPages = state.totalPages;

    // Update search input
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.value = currentSearch;
    }
}

// Save tab state
function saveTabState(tab) {
    tabStates[tab] = {
        search: currentSearch,
        page: currentPage,
        totalPages: totalPages
    };
}

// Initialize tab from URL parameter
function initializeTabFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');

    if (tabParam && ['peraturan', 'surat_keputusan', 'sk_non_pns'].includes(tabParam)) {
        currentTab = tabParam;
    } else {
        currentTab = 'all';
    }

    updateTabUI(currentTab);
    loadTabState(currentTab);
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    initializeTabFromURL();
    loadSuratRektorUnmul();
});
</script>
@endpush

@push('styles')
<style>
/* Search animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.6s ease-out forwards;
    opacity: 0;
}

/* Search input focus animation */
#searchInput:focus {
    transform: scale(1.02);
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

/* Grid transition */
#suratRektorUnmulGrid {
    transition: opacity 0.3s ease-in-out;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.prose {
    color: #374151;
    line-height: 1.75;
}

.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
    color: #111827;
    font-weight: 600;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.prose p {
    margin-bottom: 1.5rem;
}

.prose img {
    border-radius: 0.5rem;
    margin: 1.5rem 0;
}

.prose ul, .prose ol {
    margin: 1.5rem 0;
    padding-left: 1.5rem;
}

.prose li {
    margin: 0.5rem 0;
}

.prose blockquote {
    border-left: 4px solid #8b5cf6;
    padding-left: 1rem;
    margin: 1.5rem 0;
    font-style: italic;
    color: #6b7280;
}

.prose a {
    color: #8b5cf6;
    text-decoration: underline;
}

.prose a:hover {
    color: #7c3aed;
}

/* Pagination button styles */
#pageNumbers button {
    color: #000000 !important;
    font-weight: 500;
}

#pageNumbers button.bg-violet-600 {
    color: #ffffff !important;
}

#prevPage, #nextPage {
    color: #000000 !important;
}
</style>
@endpush
@endsection
