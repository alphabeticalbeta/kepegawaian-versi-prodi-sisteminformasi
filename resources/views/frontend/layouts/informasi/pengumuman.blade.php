@extends('frontend.layouts.app')

@section('title', 'Pengumuman - Universitas Mulawarman')

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
                    Pengumuman Terkini
                </h1>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="w-full px-2 sm:px-4 lg:px-6 py-6">
        <!-- Search Section -->
        <div class="mb-4 flex justify-end">
            <div class="relative w-96">
                <input type="text" id="searchInput" placeholder="Cari pengumuman  .  .  .  .  .  .  .  . "
                       class="w-full px-2 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base transition-all duration-300 hover:border-green-400 focus:border-green-500 focus:ring-green-500">
            </div>
        </div>


        <!-- Error State -->
        <div id="errorState" class="hidden text-center py-12">
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 max-w-md mx-auto">
                <svg class="mx-auto h-12 w-12 text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-lg font-medium text-red-800 mb-2">Gagal Memuat Pengumuman</h3>
                <p class="text-red-600 mb-4">Terjadi kesalahan saat memuat data pengumuman.</p>
                <button onclick="loadPengumuman()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                    Coba Lagi
                </button>
            </div>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="hidden text-center py-12">
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 max-w-md mx-auto">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-800 mb-2">Tidak Ada Pengumuman</h3>
                <p class="text-gray-600">Belum ada pengumuman yang tersedia saat ini.</p>
            </div>
        </div>

        <!-- Pengumuman Grid -->
        <div id="pengumumanGrid" class="hidden grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 transition-all duration-500">
            <!-- Pengumuman cards will be populated here -->
        </div>

        <!-- Pagination -->
        <div id="paginationContainer" class="hidden mt-6">
            <nav class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <button id="prevPage" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>

                    <div id="pageNumbers" class="flex items-center space-x-1">
                        <!-- Page numbers will be populated here -->
                    </div>

                    <button id="nextPage" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>

                <div class="text-sm text-gray-700">
                    Menampilkan <span id="showingFrom">0</span> - <span id="showingTo">0</span> dari <span id="totalItems">0</span> pengumuman
                </div>
            </nav>
        </div>
    </div>
</div>

<!-- Pengumuman Detail Modal -->
<div id="pengumumanModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b">
                <h3 id="modalTitle" class="text-xl font-semibold text-gray-900">Detail Pengumuman</h3>
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
let abortController = null; // For request cancellation

// Load pengumuman data
async function loadPengumuman(page = 1, isSearch = false, isPagination = false) {
    try {
        // Cancel previous request if it exists
        if (abortController) {
            abortController.abort();
        }

        // Create new abort controller for this request
        abortController = new AbortController();


        const params = new URLSearchParams({
            page: page,
            per_page: 8,
            sort: 'latest',
            search: currentSearch,
            jenis: 'pengumuman'
        });

        const response = await fetch(`/informasi/data?${params}`, {
            signal: abortController.signal,
            cache: 'no-cache' // Ensure fresh data for search
        });
        const data = await response.json();

        if (data.success) {

            // For search and pagination, update content immediately without delay
            if (isSearch || isPagination) {
                displayPengumuman(data.data);
                updatePagination(data);

                // Show elements immediately for smooth experience
                const gridElement = document.getElementById('pengumumanGrid');
                const paginationElement = document.getElementById('paginationContainer');

                if (gridElement) {
                    gridElement.classList.remove('hidden');
                }

                if (paginationElement) {
                    paginationElement.classList.remove('hidden');
                }
            } else {
                // For initial load, use setTimeout for smooth transition
                displayPengumuman(data.data);
                updatePagination(data);

                // Show other elements
                const gridElement = document.getElementById('pengumumanGrid');
                const paginationElement = document.getElementById('paginationContainer');

                if (gridElement) {
                    gridElement.classList.remove('hidden');
                }

                if (paginationElement) {
                    paginationElement.classList.remove('hidden');
                }
            }
        } else {
            console.error('API returned success: false');
            showError();
        }
    } catch (error) {
        // Don't show error for aborted requests (user typed new search)
        if (error.name === 'AbortError') {
            return;
        }

        console.error('Error loading pengumuman:', error);
        showError();
    }
}

// Display pengumuman cards
function displayPengumuman(pengumuman) {
    const grid = document.getElementById('pengumumanGrid');
    const emptyState = document.getElementById('emptyState');

    if (pengumuman.length === 0) {
        grid.classList.add('hidden');
        emptyState.classList.remove('hidden');
        return;
    }

    emptyState.classList.add('hidden');
    grid.classList.remove('hidden');

    grid.innerHTML = pengumuman.map((item, index) => `
        <article class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer transform hover:scale-105 animate-fade-in" style="animation-delay: ${index * 100}ms" onclick="showPengumumanDetail(${item.id})">
            <div class="relative">
                ${item.thumbnail ? `
                    <img src="/lampiran/${item.thumbnail.split('/').pop()}"
                         alt="${item.judul}"
                         class="w-full h-48 object-cover"
                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjNmNGY2Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzljYTNhZiIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPk5vIEltYWdlPC90ZXh0Pjwvc3ZnPg=='">
                ` : `
                    <div class="w-full h-32 bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center transition-transform duration-300 hover:scale-110">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
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
                    ${formatDate(item.tanggal_publish)}
                </div>

                <h3 class="text-sm font-semibold text-gray-900 mb-2 line-clamp-2 hover:text-green-600 transition-colors duration-300">
                    ${item.judul}
                </h3>

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

                    <div class="text-green-600 font-medium text-xs transition-colors duration-300 hover:text-green-800">
                        Klik untuk melihat →
                    </div>
                </div>

                ${item.tags && item.tags.length > 0 ? `
                    <div class="mt-4 flex flex-wrap gap-2">
                        ${item.tags.map(tag => `
                            <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full hover:bg-green-100 hover:text-green-700 transition-colors duration-300">
                                ${tag}
                            </span>
                        `).join('')}
                    </div>
                ` : ''}
            </div>
        </article>
    `).join('');
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

// Show pengumuman detail modal
async function showPengumumanDetail(id) {
    try {
        const response = await fetch(`/informasi/${id}`);
        const data = await response.json();

        if (data.success) {
            const item = data.data;

            document.getElementById('modalTitle').textContent = item.judul;
            
            // Create modal content safely
            const modalContent = document.getElementById('modalContent');
            modalContent.innerHTML = '';
            
            // Create container
            const container = document.createElement('div');
            container.className = 'space-y-6';
            
            // Date and author info
            const infoDiv = document.createElement('div');
            infoDiv.className = 'flex items-center text-sm text-gray-500';
            infoDiv.innerHTML = `
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                ${formatDate(item.tanggal_publish)}
                <span class="mx-2">•</span>
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                ${escapeHtml(item.penulis || 'Admin')}
            `;
            container.appendChild(infoDiv);
            
            // Thumbnail
            if (item.thumbnail) {
                const imgDiv = document.createElement('div');
                const img = document.createElement('img');
                img.src = `/lampiran/${item.thumbnail.split('/').pop()}`;
                img.alt = escapeHtml(item.judul);
                img.className = 'w-full h-60 object-cover rounded-lg';
                img.onerror = function() {
                    this.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjNmNGY2Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzljYTNhZiIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPk5vIEltYWdlPC90ZXh0Pjwvc3ZnPg==';
                };
                imgDiv.appendChild(img);
                container.appendChild(imgDiv);
            }
            
            // Content - safely handle HTML content
            const contentDiv = document.createElement('div');
            contentDiv.className = 'prose max-w-none';
            // Use textContent for safety, or implement proper HTML sanitization
            contentDiv.textContent = item.konten;
            container.appendChild(contentDiv);
            
            // Attachments
            if (item.lampiran && item.lampiran.length > 0) {
                const attachmentDiv = document.createElement('div');
                attachmentDiv.className = 'border-t pt-6';
                
                const title = document.createElement('h4');
                title.className = 'text-lg font-semibold text-gray-900 mb-4';
                title.textContent = 'Lampiran';
                attachmentDiv.appendChild(title);
                
                const filesDiv = document.createElement('div');
                filesDiv.className = 'space-y-2';
                
                item.lampiran.forEach(file => {
                    const fileLink = document.createElement('a');
                    fileLink.href = `/lampiran/${file.path}`;
                    fileLink.target = '_blank';
                    fileLink.className = 'flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors';
                    
                    fileLink.innerHTML = `
                        <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="text-gray-700">${escapeHtml(file.name)}</span>
                        <span class="ml-auto text-sm text-gray-500">${formatFileSize(file.size)}</span>
                    `;
                    
                    filesDiv.appendChild(fileLink);
                });
                
                attachmentDiv.appendChild(filesDiv);
                container.appendChild(attachmentDiv);
            }
            
            modalContent.appendChild(container);

            document.getElementById('pengumumanModal').classList.remove('hidden');
        }
    } catch (error) {
        // Handle error silently for production
        document.getElementById('modalContent').innerHTML = '<div class="text-center text-red-500">Gagal memuat detail pengumuman</div>';
    }
}

// Close modal
function closeModal() {
    document.getElementById('pengumumanModal').classList.add('hidden');
}

// Update pagination
function updatePagination(response) {
    const container = document.getElementById('paginationContainer');
    const pageNumbers = document.getElementById('pageNumbers');
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');

    currentPage = response.current_page;
    totalPages = response.last_page;

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
                ? 'bg-green-600 text-white'
                : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-50'
        }`;
        pageBtn.onclick = () => loadPengumuman(i, false, true);
        pageNumbers.appendChild(pageBtn);
    }

    // Update prev/next buttons
    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled = currentPage === totalPages;
    prevBtn.onclick = () => currentPage > 1 && loadPengumuman(currentPage - 1, false, true);
    nextBtn.onclick = () => currentPage < totalPages && loadPengumuman(currentPage + 1, false, true);

    // Update showing info
    document.getElementById('showingFrom').textContent = response.from || 0;
    document.getElementById('showingTo').textContent = response.to || 0;
    document.getElementById('totalItems').textContent = response.total || 0;
}


// Show error state
function showError() {
    document.getElementById('pengumumanGrid').classList.add('hidden');
    document.getElementById('emptyState').classList.add('hidden');
    document.getElementById('errorState').classList.remove('hidden');
    document.getElementById('paginationContainer').classList.add('hidden');
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


    // If search is empty, load all pengumuman immediately
    if (currentSearch === '') {
        loadPengumuman(1, true);
    } else {
        // Only search if there's actual content
        loadPengumuman(1, true);
    }
}, 300)); // Reduced from 500ms to 300ms for faster response

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

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    loadPengumuman();
    
    // Check for highlight parameter
    const urlParams = new URLSearchParams(window.location.search);
    const highlightId = urlParams.get('highlight');
    
    if (highlightId) {
        // Wait for pengumuman to load, then open modal directly
        setTimeout(() => {
            showPengumumanDetail(highlightId);
        }, 1500);
    }
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
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

/* Grid transition */
#pengumumanGrid {
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
    border-left: 4px solid #10b981;
    padding-left: 1rem;
    margin: 1.5rem 0;
    font-style: italic;
    color: #6b7280;
}

.prose a {
    color: #10b981;
    text-decoration: underline;
}

.prose a:hover {
    color: #059669;
}
</style>
@endpush
@endsection
