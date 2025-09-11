@extends('backend.layouts.roles.kepegawaian-universitas.app')

@section('title', 'Master Data Pegawai')

@push('styles')
<style>
    /* Hide scrollbar for table container */
    .overflow-x-auto::-webkit-scrollbar {
        display: none;
    }

    .overflow-x-auto {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* Table transition animations */
    #pegawaiTableBody {
        transition: opacity 0.3s ease-in-out;
    }

    .table-loading {
        opacity: 0.5;
        pointer-events: none;
    }

    /* Ensure import and export buttons are clickable */
    #importBtn, #exportBtn {
        pointer-events: auto !important;
        cursor: pointer !important;
        z-index: 10 !important;
    }

    .table-loaded {
        opacity: 1;
    }

    /* Force scrollbar to always show */
    .force-scrollbar {
        overflow: auto !important;
        scrollbar-width: auto !important;
        -webkit-overflow-scrolling: touch !important;
    }

    .force-scrollbar::-webkit-scrollbar {
        width: 12px !important;
        height: 12px !important;
    }

    .force-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1 !important;
        border-radius: 6px !important;
    }

    .force-scrollbar::-webkit-scrollbar-thumb {
        background: #c1c1c1 !important;
        border-radius: 6px !important;
    }

    .force-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8 !important;
    }

    /* Fade in animation for new content */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <!-- Header Section -->
    <div class="relative overflow-hidden shadow-2xl">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative px-4 py-8 sm:px-6 sm:py-10">
            <div class="mx-auto max-w-full text-center">
                <div class="mb-4 flex justify-center">
                    <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo UNMUL" class="h-20 w-auto object-contain">
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-black sm:text-3xl mb-2">
                    Master Data Pegawai
                </h1>
                <p class="text-base text-black sm:text-lg">
                    Kelola data pegawai Universitas Mulawarman
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="relative z-10 -mt-8 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-full pt-4 mt-4 animate-fade-in">
            <!-- Filter and Search -->
            <div class="mb-4 bg-white rounded-2xl shadow-xl p-3 sm:p-4 transition-all duration-300 hover:shadow-2xl">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">Search</label>
                        <input type="text" id="searchInput" placeholder="Cari nama, NIP, unit kerja, atau status..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400 text-xs sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">Jenis Pegawai</label>
                        <select id="filterJenisPegawai"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400 text-xs sm:text-sm">
                            <option value="">Semua Jenis</option>
                            <option value="Dosen">Dosen</option>
                            <option value="Tenaga Kependidikan">Tenaga Kependidikan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">Status Kepegawaian</label>
                        <select id="filterStatusKepegawaian"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400 text-xs sm:text-sm">
                            <option value="">Semua Status</option>
                            @php
                                $uniqueStatus = $pegawais->pluck('status_kepegawaian')->filter()->unique()->sort();
                            @endphp
                            @foreach($uniqueStatus as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">Unit Kerja</label>
                        <select id="filterUnitKerja"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400 text-xs sm:text-sm">
                            <option value="">Semua Unit Kerja</option>
                            @php
                                $uniqueUnitKerjas = $pegawais->pluck('unitKerja.unitKerja.nama')->filter()->unique()->sort();
                            @endphp
                            @foreach($uniqueUnitKerjas as $unitKerja)
                                <option value="{{ $unitKerja }}">{{ $unitKerja }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl">
                <!-- Table Header with Action Buttons -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-3 sm:p-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 gap-3">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">Daftar Data Pegawai</h3>
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <!-- Export Button -->
                        <button id="exportBtn" onclick="exportData()"
                                class="inline-flex items-center px-3 sm:px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 border border-transparent rounded-lg font-semibold text-xs sm:text-sm text-white hover:from-green-700 hover:to-emerald-700 focus:from-green-700 focus:to-emerald-700 active:from-green-900 active:to-emerald-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md hover:shadow-lg">
                            <i data-lucide="download" class="h-4 w-4 mr-1 sm:mr-2"></i>
                            <span class="hidden sm:inline">Export Excel</span>
                            <span class="sm:hidden">Export</span>
                        </button>

                        <!-- Import Button -->
                        <button id="importBtn" type="button" onclick="openImportModal()"
                                class="inline-flex items-center px-3 sm:px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 border border-transparent rounded-lg font-semibold text-xs sm:text-sm text-white hover:from-blue-700 hover:to-cyan-700 focus:from-blue-700 focus:to-cyan-700 active:from-blue-900 active:to-cyan-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md hover:shadow-lg"
                                style="pointer-events: auto; cursor: pointer; z-index: 10;">
                            <i data-lucide="upload" class="h-4 w-4 mr-1 sm:mr-2"></i>
                            <span class="hidden sm:inline">Import Excel</span>
                            <span class="sm:hidden">Import</span>
                        </button>

                        <!-- Template Button -->
                        <a href="{{ route('backend.kepegawaian-universitas.data-pegawai.template') }}"
                           class="inline-flex items-center px-3 sm:px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 border border-transparent rounded-lg font-semibold text-xs sm:text-sm text-white hover:from-purple-700 hover:to-pink-700 focus:from-purple-700 focus:to-pink-700 active:from-purple-900 active:to-pink-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md hover:shadow-lg">
                            <i data-lucide="file-text" class="h-4 w-4 mr-1 sm:mr-2"></i>
                            <span class="hidden sm:inline">Template</span>
                            <span class="sm:hidden">Template</span>
                        </a>

                        <!-- Bulk Actions -->
                        <div id="bulkActions" class="hidden items-center gap-2 overflow-x-auto" style="display: none;">
                            <button onclick="bulkDelete()" id="bulkDeleteBtn"
                                    class="inline-flex items-center px-3 sm:px-4 py-2 bg-gradient-to-r from-red-600 to-rose-600 border border-transparent rounded-lg font-semibold text-xs sm:text-sm text-white hover:from-red-700 hover:to-rose-700 focus:from-red-700 focus:to-rose-700 active:from-red-900 active:to-rose-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md hover:shadow-lg">
                                <i data-lucide="trash-2" class="h-4 w-4 mr-1 sm:mr-2"></i>
                                <span class="hidden sm:inline">Hapus Terpilih</span>
                                <span class="sm:hidden">Hapus</span>
                            </button>
                            <button onclick="bulkUpdate()" id="bulkUpdateBtn"
                                    class="inline-flex items-center px-3 sm:px-4 py-2 bg-gradient-to-r from-orange-600 to-amber-600 border border-transparent rounded-lg font-semibold text-xs sm:text-sm text-white hover:from-orange-700 hover:to-amber-700 focus:from-orange-700 focus:to-amber-700 active:from-orange-900 active:to-amber-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md hover:shadow-lg">
                                <i data-lucide="edit" class="h-4 w-4 mr-1 sm:mr-2"></i>
                                <span class="hidden sm:inline">Update Terpilih</span>
                                <span class="sm:hidden">Update</span>
                            </button>
                        </div>

                        <!-- Add Button -->
                        <a href="{{ route('backend.kepegawaian-universitas.data-pegawai.create') }}"
                           class="inline-flex items-center px-3 sm:px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg font-semibold text-xs sm:text-sm text-white hover:from-indigo-700 hover:to-purple-700 focus:from-indigo-700 focus:to-purple-700 active:from-indigo-900 active:to-purple-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md hover:shadow-lg">
                            <i data-lucide="plus" class="h-4 w-4 mr-1 sm:mr-2"></i>
                            <span class="hidden sm:inline">Tambah Pegawai</span>
                            <span class="sm:hidden">Tambah</span>
                        </a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th class="px-2 sm:px-4 py-3 text-xs font-bold text-black text-center uppercase tracking-wider w-12">
                                    <input type="checkbox" id="selectAll" class="h-3 w-3 sm:h-4 sm:w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                </th>
                                <th class="px-2 sm:px-4 py-3 text-xs font-bold text-black text-center uppercase tracking-wider w-16">No</th>
                                <th class="px-2 sm:px-4 py-3 text-xs font-bold text-black text-center uppercase tracking-wider w-48">Nama & NIP</th>
                                <th class="px-2 sm:px-4 py-3 text-xs font-bold text-black text-center uppercase tracking-wider w-32">Status Kepegawaian</th>
                                <th class="px-2 sm:px-4 py-3 text-xs font-bold text-black text-center uppercase tracking-wider w-32">Pangkat</th>
                                <th class="px-2 sm:px-4 py-3 text-xs font-bold text-black text-center uppercase tracking-wider w-32">Jabatan</th>
                                <th class="px-2 sm:px-4 py-3 text-xs font-bold text-black text-center uppercase tracking-wider w-40">Unit Kerja</th>
                                <th class="px-2 sm:px-4 py-3 text-xs font-bold text-black text-center tracking-wider w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="pegawaiTableBody" class="bg-white divide-y divide-gray-200">
                            @forelse($pegawais as $index => $pegawai)
                                <tr class="hover:bg-gray-50 transition-all duration-200 animate-fade-in" style="animation-delay: {{ $index * 50 }}ms">
                                    <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-center">
                                        <input type="checkbox" name="selected_pegawai[]" value="{{ $pegawai->id }}" class="pegawai-checkbox h-3 w-3 sm:h-4 sm:w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    </td>
                                    <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-center">
                                        {{ $index + $pegawais->firstItem() }}
                                    </td>
                                    <td class="px-2 sm:px-4 py-3">
                                        <div class="flex items-center">
                                            @if($pegawai->foto && file_exists(public_path('storage/' . $pegawai->foto)))
                                                <img class="h-8 w-8 sm:h-10 sm:w-10 rounded-full object-cover mr-2 sm:mr-4 border border-gray-200"
                                                     src="{{ route('backend.kepegawaian-universitas.data-pegawai.show-document', ['pegawai' => $pegawai->id, 'field' => 'foto']) }}"
                                                     alt="Foto {{ $pegawai->nama_lengkap }}"
                                                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($pegawai->nama_lengkap) }}&background=random'"
                                                     loading="lazy">
                                            @else
                                                <img class="h-8 w-8 sm:h-10 sm:w-10 rounded-full object-cover mr-2 sm:mr-4 border border-gray-200"
                                                     src="https://ui-avatars.com/api/?name={{ urlencode($pegawai->nama_lengkap) }}&background=random"
                                                     alt="Avatar {{ $pegawai->nama_lengkap }}"
                                                     loading="lazy">
                                            @endif
                                            <div class="flex flex-col">
                                                <div class="text-xs sm:text-sm font-medium text-gray-900">{{ $pegawai->nama_lengkap }}</div>
                                                <div class="text-xs sm:text-sm font-medium text-gray-900">{{ $pegawai->nip }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{
                                            $pegawai->status_kepegawaian == 'PNS' ? 'bg-green-100 text-green-800' :
                                            ($pegawai->status_kepegawaian == 'CPNS' ? 'bg-yellow-100 text-yellow-800' :
                                            ($pegawai->status_kepegawaian == 'Non PNS' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800'))
                                        }}">
                                            {{ $pegawai->status_kepegawaian ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-center">
                                        <div class="text-xs sm:text-sm text-gray-900" title="{{ $pegawai->pangkat?->pangkat ?? 'Belum ditentukan' }}">
                                            {{ $pegawai->pangkat?->pangkat ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-center">
                                        <div class="text-xs sm:text-sm text-gray-900" title="{{ $pegawai->jabatan?->jabatan ?? 'Belum ditentukan' }}">
                                            {{ $pegawai->jabatan?->jabatan ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-center">
                                        <div class="text-xs sm:text-sm text-gray-900" title="{{ $pegawai->unitKerja?->unitKerja?->nama ?? 'Belum ditentukan' }}">
                                            {{ $pegawai->unitKerja?->unitKerja?->nama ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-sm font-medium text-center">
                                        <div class="flex justify-center items-center gap-1 sm:gap-2">
                                            <a href="{{ route('backend.kepegawaian-universitas.data-pegawai.edit', $pegawai) }}"
                                               class="inline-flex items-center px-2 sm:px-3 py-1.5 text-xs sm:text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-cyan-600 rounded-lg hover:from-blue-700 hover:to-cyan-700 transition-all duration-200 shadow-md hover:shadow-lg"
                                               title="Edit Data">
                                                <i data-lucide="edit" class="h-3 w-3 sm:h-4 sm:w-4 mr-1"></i>
                                                <span class="hidden sm:inline">Edit</span>
                                            </a>
                                            <form action="{{ route('backend.kepegawaian-universitas.data-pegawai.destroy', $pegawai) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pegawai ini?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center px-2 sm:px-3 py-1.5 text-xs sm:text-sm font-medium text-white bg-gradient-to-r from-red-600 to-pink-600 rounded-lg hover:from-red-700 hover:to-pink-700 transition-all duration-200 shadow-md hover:shadow-lg"
                                                        title="Hapus Data">
                                                    <i data-lucide="trash-2" class="h-3 w-3 sm:h-4 sm:w-4 mr-1"></i>
                                                    <span class="hidden sm:inline">Hapus</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-2 sm:px-4 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <i data-lucide="users" class="h-10 w-10 text-gray-300 mb-3"></i>
                                            <p class="text-base font-medium">Belum ada data</p>
                                            <p class="text-sm">Data pegawai akan ditampilkan di sini</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden mt-6">
                <div class="px-4 py-4 flex items-center justify-between">
                    <div class="flex items-center text-sm text-gray-700">
                            <span>Menampilkan {{ $pegawais->firstItem() ?? 0 }} - {{ $pegawais->lastItem() ?? 0 }} dari {{ $pegawais->total() }} data</span>
                        </div>
                    <div class="flex items-center space-x-2">
                        @if($pegawais->onFirstPage())
                            <span class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md cursor-not-allowed">
                                <i data-lucide="chevron-left" class="h-4 w-4"></i>
                            </span>
                        @else
                            <a href="{{ $pegawais->previousPageUrl() }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-200">
                                <i data-lucide="chevron-left" class="h-4 w-4"></i>
                            </a>
                        @endif
                        
                        <div class="flex space-x-1">
                            @foreach($pegawais->getUrlRange(1, $pegawais->lastPage()) as $page => $url)
                                @if($page == $pegawais->currentPage())
                                    <span class="px-3 py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg shadow-md">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:shadow-sm transition-all duration-200">{{ $page }}</a>
                                @endif
                            @endforeach
                        </div>
                        
                        @if($pegawais->hasMorePages())
                            <a href="{{ $pegawais->nextPageUrl() }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-200">
                                <i data-lucide="chevron-right" class="h-4 w-4"></i>
                            </a>
                        @else
                            <span class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md cursor-not-allowed">
                                <i data-lucide="chevron-right" class="h-4 w-4"></i>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Global variables
let currentSearch = '';
let currentFilterJenisPegawai = '';
let currentFilterUnitKerja = '';

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

// Global export function
function exportData() {
    // Export data function
    // Get current filters
    const jenisPegawai = document.getElementById('filterJenisPegawai').value;
    const statusKepegawaian = document.getElementById('filterStatusKepegawaian').value;
    const unitKerja = document.getElementById('filterUnitKerja').value;

    // Build export URL with filters
    let exportUrl = '{{ route("backend.kepegawaian-universitas.data-pegawai.export") }}';
    const params = new URLSearchParams();

    if (jenisPegawai) params.append('jenis_pegawai', jenisPegawai);
    if (statusKepegawaian) params.append('status_kepegawaian', statusKepegawaian);
    if (unitKerja) params.append('unit_kerja', unitKerja);

    if (params.toString()) {
        exportUrl += '?' + params.toString();
    }

    // Show loading
    const exportBtn = document.getElementById('exportBtn');
    const originalText = exportBtn.innerHTML;
    exportBtn.innerHTML = '<i data-lucide="loader-2" class="h-4 w-4 mr-1 sm:mr-2 animate-spin"></i><span class="hidden sm:inline">Exporting...</span><span class="sm:hidden">Export...</span>';
    exportBtn.disabled = true;

    // Download file
    window.location.href = exportUrl;

    // Reset button after delay
    setTimeout(() => {
        exportBtn.innerHTML = originalText;
        exportBtn.disabled = false;
    }, 2000);
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
        // Page initialization

    // Add event listeners for buttons
    const exportBtn = document.getElementById('exportBtn');
    const importBtn = document.getElementById('importBtn');

    if (exportBtn) {
        exportBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            exportData();
        });
    }

    if (importBtn) {
        importBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            // Inline function to avoid scope issues
            const modal = document.getElementById('importModal');
            if (modal) {
                modal.classList.remove('hidden');
            }
        });
    }

    // Add event listeners for modal buttons
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const previewBtn = document.getElementById('previewBtn');

    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', function() {
            document.getElementById('importModal').classList.add('hidden');
            document.getElementById('importForm').reset();
            document.getElementById('previewContainer').innerHTML = '';
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            document.getElementById('importModal').classList.add('hidden');
            document.getElementById('importForm').reset();
            document.getElementById('previewContainer').innerHTML = '';
        });
    }

    if (previewBtn) {
        previewBtn.addEventListener('click', function() {

            // Inline previewImport function to avoid scope issues
            const fileInput = document.getElementById('importFile');
            const importMode = document.getElementById('importMode').value;

            if (!fileInput.files[0]) {
                alert('Pilih file Excel terlebih dahulu');
                return;
            }

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            formData.append('import_mode', importMode);

            // Show loading
            const originalText = previewBtn.innerHTML;
            previewBtn.innerHTML = '<i data-lucide="loader-2" class="h-4 w-4 mr-2 animate-spin"></i>Loading...';
            previewBtn.disabled = true;

            fetch('{{ route("backend.kepegawaian-universitas.data-pegawai.preview-import") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Inline displayPreview function to avoid scope issues
                    const container = document.getElementById('previewContainer');
                    const previewData = data.preview_data || data.data;
                    const totalRows = data.total_rows || previewData.length;
                    const headers = data.headers || Object.keys(previewData[0] || {});
                    const validation = data.validation || {};

                    let html = `
                        <div class="mb-4 p-3 sm:p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <h4 class="font-semibold text-blue-900 mb-2 text-sm sm:text-base">Preview Data Import</h4>
                            <p class="text-xs sm:text-sm text-blue-700">Total baris: ${totalRows} | Menampilkan 10 baris pertama</p>
                        </div>
                    `;

                    // Show validation warnings/errors
                    if (validation.has_errors) {
                        html += `
                            <div class="mb-4 p-3 sm:p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <h4 class="font-semibold text-yellow-900 mb-2 text-sm sm:text-base flex items-center">
                                    <i data-lucide="alert-triangle" class="w-4 h-4 mr-2"></i>
                                    Peringatan Validasi
                                </h4>
                        `;

                        if (validation.missing_headers && validation.missing_headers.length > 0) {
                            html += `
                                <div class="mb-2">
                                    <p class="text-yellow-800 text-xs sm:text-sm font-medium">Header yang hilang:</p>
                                    <p class="text-yellow-700 text-xs">${validation.missing_headers.join(', ')}</p>
                                </div>
                            `;
                        }

                        if (validation.extra_headers && validation.extra_headers.length > 0) {
                            html += `
                                <div class="mb-2">
                                    <p class="text-yellow-800 text-xs sm:text-sm font-medium">Header tambahan:</p>
                                    <p class="text-yellow-700 text-xs">${validation.extra_headers.join(', ')}</p>
                                </div>
                            `;
                        }

                        if (validation.errors && Object.keys(validation.errors).length > 0) {
                            html += `
                                <div class="mb-2">
                                    <p class="text-yellow-800 text-xs sm:text-sm font-medium">Error pada data:</p>
                                    <div class="text-yellow-700 text-xs max-h-32 overflow-y-auto">
                            `;
                            Object.entries(validation.errors).forEach(([row, errors]) => {
                                html += `<div class="mb-1"><strong>${row}:</strong> ${errors.join(', ')}</div>`;
                            });
                            html += `</div></div>`;
                        }

                        html += `</div>`;
                    }

                    // Show success message if no errors
                    if (!validation.has_errors) {
                        html += `
                            <div class="mb-4 p-3 sm:p-4 bg-green-50 border border-green-200 rounded-lg">
                                <h4 class="font-semibold text-green-900 mb-2 text-sm sm:text-base flex items-center">
                                    <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>
                                    Data Valid
                                </h4>
                                <p class="text-green-700 text-xs sm:text-sm">Data siap untuk diimport</p>
                            </div>
                        `;
                    }

                    html += `
                        <div class="force-scrollbar" style="min-width: 100%; max-width: 100%; max-height: 400px;">
                            <table class="text-xs sm:text-sm border border-gray-200" style="min-width: 800px; width: max-content; table-layout: auto;">
                                <thead class="bg-gray-50">
                                    <tr>
                                        ${headers.map(header => `<th class="px-2 sm:px-3 py-2 border border-gray-200 text-left font-semibold">${header}</th>`).join('')}
                                    </tr>
                                </thead>
                                <tbody>
                                    ${previewData.slice(0, 10).map((row, index) => {
                                        const rowNumber = index + 2; // +2 because header is row 1, and we start from row 2
                                        const hasError = validation.errors && validation.errors['Baris ' + rowNumber];
                                        const rowClass = hasError ? 'bg-red-50' : 'hover:bg-gray-50';
                                        return `
                                            <tr class="${rowClass}">
                                            ${headers.map(header => `<td class="px-2 sm:px-3 py-2 border border-gray-200">${row[header] || ''}</td>`).join('')}
                                        </tr>
                                        `;
                                    }).join('')}
                                </tbody>
                            </table>
                        </div>
                    `;

                    container.innerHTML = html;
                } else {
                    const container = document.getElementById('previewContainer');
                    container.innerHTML = `
                        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                            <h4 class="font-semibold text-red-900 mb-2 flex items-center">
                                <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i>
                                Error Preview
                            </h4>
                            <p class="text-red-700 text-sm">${data.message}</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                // Error occurred during preview
                alert('Terjadi kesalahan saat memproses file');
            })
            .finally(() => {
                previewBtn.innerHTML = originalText;
                previewBtn.disabled = false;
            });
        });
    }

    initializeSearchAndFilter();
    
    // Handle import success modal
    initializeImportSuccessModal();
});

// Initialize import success modal
function initializeImportSuccessModal() {
    // Check if there are import details in session
    @if(session('import_details'))
        const importDetails = @json(session('import_details'));
        showImportSuccessModal(importDetails);
    @endif

    // Close success modal handlers
    document.getElementById('closeSuccessModalBtn')?.addEventListener('click', closeImportSuccessModal);
    document.getElementById('closeSuccessModalBtn2')?.addEventListener('click', closeImportSuccessModal);
    
    // Download error log handler
    document.getElementById('downloadErrorLogBtn')?.addEventListener('click', downloadErrorLog);
}

// Show import success modal
function showImportSuccessModal(details) {
    const modal = document.getElementById('importSuccessModal');
    const content = document.getElementById('importSuccessContent');
    const downloadBtn = document.getElementById('downloadErrorLogBtn');
    
    // Build content
    let html = `
        <div class="space-y-4">
            <!-- Summary Card -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center mb-3">
                    <i data-lucide="file-check" class="w-5 h-5 text-green-600 mr-2"></i>
                    <h4 class="font-semibold text-green-900">File: ${details.file_name}</h4>
                </div>
                <p class="text-sm text-green-700">Mode: ${getImportModeText(details.import_mode)}</p>
                <p class="text-sm text-green-700">Waktu: ${details.import_timestamp}</p>
            </div>

            <!-- Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-center">
                    <div class="text-2xl font-bold text-blue-600">${details.import_stats.created}</div>
                    <div class="text-sm text-blue-700">Data Baru</div>
                </div>
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 text-center">
                    <div class="text-2xl font-bold text-purple-600">${details.import_stats.updated}</div>
                    <div class="text-sm text-purple-700">Data Diupdate</div>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-center">
                    <div class="text-2xl font-bold text-yellow-600">${details.import_stats.errors}</div>
                    <div class="text-sm text-yellow-700">Error</div>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-center">
                    <div class="text-2xl font-bold text-red-600">${details.import_stats.failures}</div>
                    <div class="text-sm text-red-700">Gagal</div>
                </div>
            </div>
    `;

    // Show errors if any
    if (details.import_errors && details.import_errors.length > 0) {
        html += `
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h4 class="font-semibold text-yellow-900 mb-3 flex items-center">
                    <i data-lucide="alert-triangle" class="w-4 h-4 mr-2"></i>
                    Error Details
                </h4>
                <div class="max-h-40 overflow-y-auto">
                    <ul class="text-sm text-yellow-800 space-y-1">
        `;
        
        details.import_errors.forEach(error => {
            html += `<li>• ${error}</li>`;
        });
        
        html += `
                    </ul>
                </div>
            </div>
        `;
        
        // Show download button
        downloadBtn.classList.remove('hidden');
    }

    html += `</div>`;
    
    content.innerHTML = html;
    modal.classList.remove('hidden');
}

// Close import success modal
function closeImportSuccessModal() {
    document.getElementById('importSuccessModal').classList.add('hidden');
}

// Get import mode text
function getImportModeText(mode) {
    const modes = {
        'create_only': 'Tambah Data Baru',
        'update_only': 'Update Data Existing',
        'create_update': 'Tambah & Update Data'
    };
    return modes[mode] || mode;
}

// Download error log
function downloadErrorLog() {
    @if(session('import_details'))
        const importDetails = @json(session('import_details'));
        
        if (importDetails.import_errors && importDetails.import_errors.length > 0) {
            const errorLog = importDetails.import_errors.join('\n');
            const blob = new Blob([errorLog], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `import_error_log_${new Date().toISOString().slice(0, 10)}.txt`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }
    @endif
}

// Initialize search and filter functionality
function initializeSearchAndFilter() {
    // Initialize search input with real-time filtering
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function(e) {
            currentSearch = e.target.value.trim();
            performSearch();
        }, 300));
    }

    // Initialize filter dropdowns with real-time filtering
    const filterJenisPegawai = document.getElementById('filterJenisPegawai');
    if (filterJenisPegawai) {
        filterJenisPegawai.addEventListener('change', function() {
            currentFilterJenisPegawai = this.value;
            performSearch();
        });
    }

    const filterUnitKerja = document.getElementById('filterUnitKerja');
    if (filterUnitKerja) {
        filterUnitKerja.addEventListener('change', function() {
            currentFilterUnitKerja = this.value;
            performSearch();
        });
    }
}

// Perform search and filter
function performSearch() {
    const tbody = document.getElementById('pegawaiTableBody');
    if (!tbody) return;

    const rows = tbody.querySelectorAll('tr');
    let visibleCount = 0;

    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length === 0) return; // Skip empty rows

        const namaCell = cells[1]; // Nama column
        const nipCell = cells[2]; // NIP column
        const jenisPegawaiCell = cells[3]; // Jenis Pegawai column

        if (!namaCell || !nipCell || !jenisPegawaiCell) return;

        const nama = namaCell.textContent.toLowerCase();
        const nip = nipCell.textContent.toLowerCase();
        const jenisPegawai = jenisPegawaiCell.textContent.toLowerCase();

        let showRow = true;

        // Search filter - improved substring search
        if (currentSearch) {
            const searchTerm = currentSearch.toLowerCase().trim();
            // Search in nama and nip with better matching
            const namaMatch = nama.includes(searchTerm);
            const nipMatch = nip.includes(searchTerm);

            // Also search in unit kerja and status if available
            const unitKerjaCell = cells[4]; // Unit Kerja column
            const statusCell = cells[5]; // Status Kepegawaian column

            let unitKerjaMatch = false;
            let statusMatch = false;

            if (unitKerjaCell) {
                const unitKerja = unitKerjaCell.textContent.toLowerCase();
                unitKerjaMatch = unitKerja.includes(searchTerm);
            }

            if (statusCell) {
                const status = statusCell.textContent.toLowerCase();
                statusMatch = status.includes(searchTerm);
            }

            showRow = namaMatch || nipMatch || unitKerjaMatch || statusMatch;
        }

        // Jenis Pegawai filter
        if (currentFilterJenisPegawai && showRow) {
            const filterTerm = currentFilterJenisPegawai.toLowerCase();
            showRow = jenisPegawai.includes(filterTerm);
        }

        // Unit Kerja filter
        if (currentFilterUnitKerja && showRow) {
            const unitKerjaCell = cells[4]; // Unit Kerja column
            if (unitKerjaCell) {
                const unitKerja = unitKerjaCell.textContent.toLowerCase();
                const filterTerm = currentFilterUnitKerja.toLowerCase();
                showRow = unitKerja.includes(filterTerm);
            }
        }

        if (showRow) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    // Update pagination info if needed
    updatePaginationInfo(visibleCount);
}

// Update pagination info
function updatePaginationInfo(visibleCount) {
    const paginationInfo = document.querySelector('.flex.items-center.text-sm.text-gray-700 span');
    if (paginationInfo) {
        if (currentSearch || currentFilterJenisPegawai || currentFilterUnitKerja) {
            paginationInfo.textContent = `Menampilkan ${visibleCount} data (hasil filter)`;
        } else {
            // Reset to original pagination info
            const totalData = {{ $pegawais->total() }};
            const firstItem = {{ $pegawais->firstItem() ?? 0 }};
            const lastItem = {{ $pegawais->lastItem() ?? 0 }};
            paginationInfo.textContent = `Menampilkan ${firstItem} - ${lastItem} dari ${totalData} data`;
        }
    }


    // Import modal functions
    function openImportModal() {
        // Open import modal
        const modal = document.getElementById('importModal');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
        document.getElementById('importForm').reset();
        document.getElementById('previewContainer').innerHTML = '';
    }

    // Preview import function
    function previewImport() {
        const fileInput = document.getElementById('importFile');
        const importMode = document.getElementById('importMode').value;

        if (!fileInput.files[0]) {
            alert('Pilih file Excel terlebih dahulu');
            return;
        }

        const formData = new FormData();
        formData.append('file', fileInput.files[0]);
        formData.append('import_mode', importMode);

        // Show loading
        const previewBtn = document.getElementById('previewBtn');
        const originalText = previewBtn.innerHTML;
        previewBtn.innerHTML = '<i data-lucide="loader-2" class="h-4 w-4 mr-2 animate-spin"></i>Loading...';
        previewBtn.disabled = true;

        fetch('{{ route("backend.kepegawaian-universitas.data-pegawai.preview-import") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayPreview(data.preview_data, data.total_rows, data.headers);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            // Error occurred during import
            alert('Terjadi kesalahan saat preview data');
        })
        .finally(() => {
            previewBtn.innerHTML = originalText;
            previewBtn.disabled = false;
        });
    }

    // Display preview data
    function displayPreview(data, totalRows, headers) {
        const container = document.getElementById('previewContainer');

        let html = `
            <div class="mb-4 p-3 sm:p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h4 class="font-semibold text-blue-900 mb-2 text-sm sm:text-base">Preview Data Import</h4>
                <p class="text-xs sm:text-sm text-blue-700">Total baris: ${totalRows} | Menampilkan 10 baris pertama</p>
            </div>
            <div class="force-scrollbar" style="min-width: 100%; max-width: 100%; max-height: 400px;">
                <table class="text-xs sm:text-sm border border-gray-200" style="min-width: 800px; width: max-content; table-layout: auto;">
                    <thead class="bg-gray-50">
                        <tr>
                            ${headers.map(header => `<th class="px-2 sm:px-3 py-2 border border-gray-200 text-left font-semibold">${header}</th>`).join('')}
                        </tr>
                    </thead>
                    <tbody>
                        ${data.map(row => `
                            <tr class="hover:bg-gray-50">
                                ${headers.map(header => `<td class="px-2 sm:px-3 py-2 border border-gray-200">${row[header] || ''}</td>`).join('')}
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;

        container.innerHTML = html;
    }
}
</script>

<!-- Import Success Modal -->
<div id="importSuccessModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 sm:top-20 mx-auto p-4 sm:p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white max-h-[90vh] force-scrollbar">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base sm:text-lg font-semibold text-green-900 flex items-center">
                    <i data-lucide="check-circle" class="w-5 h-5 mr-2 text-green-600"></i>
                    Import Berhasil
                </h3>
                <button id="closeSuccessModalBtn" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5 sm:h-6 sm:w-6"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div id="importSuccessContent">
                <!-- Content will be populated by JavaScript -->
            </div>

            <!-- Modal Footer -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-2 sm:gap-3 mt-6">
                <button id="downloadErrorLogBtn" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition duration-200 text-sm sm:text-base hidden">
                    <i data-lucide="download" class="h-4 w-4 mr-2 inline"></i>
                    Download Error Log
                </button>
                <button id="closeSuccessModalBtn2" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition duration-200 text-sm sm:text-base">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 sm:top-20 mx-auto p-4 sm:p-5 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 shadow-lg rounded-md bg-white max-h-[90vh] force-scrollbar">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900">Import Data Pegawai</h3>
                <button id="closeModalBtn" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5 sm:h-6 sm:w-6"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="importForm" action="{{ route('backend.kepegawaian-universitas.data-pegawai.import') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Import Mode -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mode Import</label>
                    <select id="importMode" name="import_mode" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="create_update">Tambah & Update Data</option>
                        <option value="create_only">Hanya Tambah Data Baru</option>
                        <option value="update_only">Hanya Update Data Existing</option>
                    </select>
                </div>

                <!-- File Upload -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">File Excel</label>
                    <input type="file" id="importFile" name="file" accept=".xlsx,.xls"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                           required>
                    <p class="text-xs text-gray-500 mt-1">Format: .xlsx atau .xls (Max: 10MB)</p>
                </div>

                <!-- Preview Container -->
                <div id="previewContainer" class="mb-4"></div>

                <!-- Modal Footer -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-2 sm:gap-3">
                    <button type="button" id="cancelBtn"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-200 text-sm sm:text-base">
                        Batal
                    </button>
                    <button type="button" id="previewBtn"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 text-sm sm:text-base">
                        <i data-lucide="eye" class="h-4 w-4 mr-2 inline"></i>
                        Preview
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200 text-sm sm:text-base">
                        <i data-lucide="upload" class="h-4 w-4 mr-2 inline"></i>
                        Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Update Modal -->
<div id="bulkUpdateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white overflow-x-auto">
        <!-- Modal Header -->
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900">Update Data Terpilih</h3>
            <button onclick="closeBulkUpdateModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="h-5 w-5 sm:h-6 sm:w-6"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="bulkUpdateForm">
            @csrf
            <input type="hidden" id="selectedIds" name="selected_ids">

            <!-- Update Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Kepegawaian</label>
                    <select name="status_kepegawaian" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">-- Pilih Status --</option>
                        <option value="Dosen PNS">Dosen PNS</option>
                        <option value="Dosen PPPK">Dosen PPPK</option>
                        <option value="Dosen Non ASN">Dosen Non ASN</option>
                        <option value="Tenaga Kependidikan PNS">Tenaga Kependidikan PNS</option>
                        <option value="Tenaga Kependidikan PPPK">Tenaga Kependidikan PPPK</option>
                        <option value="Tenaga Kependidikan Non ASN">Tenaga Kependidikan Non ASN</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Pegawai</label>
                    <select name="jenis_pegawai" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">-- Pilih Jenis --</option>
                        <option value="Dosen">Dosen</option>
                        <option value="Tenaga Kependidikan">Tenaga Kependidikan</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pangkat Terakhir</label>
                <select name="pangkat_terakhir_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">-- Pilih Pangkat --</option>
                    @foreach(\App\Models\KepegawaianUniversitas\Pangkat::all() as $pangkat)
                        <option value="{{ $pangkat->id }}">{{ $pangkat->pangkat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan Terakhir</label>
                <select name="jabatan_terakhir_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">-- Pilih Jabatan --</option>
                    @foreach(\App\Models\KepegawaianUniversitas\Jabatan::all() as $jabatan)
                        <option value="{{ $jabatan->id }}">{{ $jabatan->jabatan }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Unit Kerja</label>
                <select name="unit_kerja_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">-- Pilih Unit Kerja --</option>
                    @foreach(\App\Models\KepegawaianUniversitas\SubSubUnitKerja::with('subUnitKerja.unitKerja')->get() as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->subUnitKerja->unitKerja->nama }} - {{ $unit->sub_unit_kerja }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        <!-- Modal Footer -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-2 sm:gap-3">
            <button type="button" onclick="closeBulkUpdateModal()"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-200 text-sm sm:text-base">
                Batal
            </button>
            <button type="button" onclick="submitBulkUpdate()"
                    class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition duration-200 text-sm sm:text-base">
                <i data-lucide="save" class="h-4 w-4 mr-2 inline"></i>
                Update Data
            </button>
        </div>
    </div>
</div>

<script>
    // Bulk operations functions
    document.addEventListener('DOMContentLoaded', function() {
        // Select all checkbox functionality
        const selectAllCheckbox = document.getElementById('selectAll');
        const pegawaiCheckboxes = document.querySelectorAll('.pegawai-checkbox');
        const bulkActions = document.getElementById('bulkActions');

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                pegawaiCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkActionsVisibility();
            });
        }

        // Individual checkbox functionality
        pegawaiCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateBulkActionsVisibility();
                updateSelectAllCheckbox();
            });
        });

        function updateBulkActionsVisibility() {
            const checkedBoxes = document.querySelectorAll('.pegawai-checkbox:checked');
            if (checkedBoxes.length > 0) {
                bulkActions.style.display = 'flex';
            } else {
                bulkActions.style.display = 'none';
            }
        }

        function updateSelectAllCheckbox() {
            const checkedBoxes = document.querySelectorAll('.pegawai-checkbox:checked');
            const totalBoxes = pegawaiCheckboxes.length;

            if (checkedBoxes.length === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            } else if (checkedBoxes.length === totalBoxes) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = true;
            } else {
                selectAllCheckbox.indeterminate = true;
            }
        }
    });

    // Bulk delete function
    function bulkDelete() {
        const selectedIds = getSelectedIds();
        if (selectedIds.length === 0) {
            alert('Pilih minimal satu pegawai untuk dihapus.');
            return;
        }

        if (confirm(`Apakah Anda yakin ingin menghapus ${selectedIds.length} pegawai terpilih?`)) {
            // Show loading
            const deleteBtn = document.getElementById('bulkDeleteBtn');
            const originalText = deleteBtn.innerHTML;
            deleteBtn.innerHTML = '<i data-lucide="loader-2" class="h-4 w-4 mr-1 sm:mr-2 animate-spin"></i><span class="hidden sm:inline">Menghapus...</span><span class="sm:hidden">Hapus...</span>';
            deleteBtn.disabled = true;

            // Submit form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("backend.kepegawaian-universitas.data-pegawai.bulk-delete") }}';

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_ids[]';
                input.value = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }
    }

    // Bulk update function
    function bulkUpdate() {
        const selectedIds = getSelectedIds();
        if (selectedIds.length === 0) {
            alert('Pilih minimal satu pegawai untuk diupdate.');
            return;
        }

        // Set selected IDs in hidden input
        document.getElementById('selectedIds').value = selectedIds.join(',');

        // Show modal
        document.getElementById('bulkUpdateModal').classList.remove('hidden');
    }

    // Close bulk update modal
    function closeBulkUpdateModal() {
        document.getElementById('bulkUpdateModal').classList.add('hidden');
        document.getElementById('bulkUpdateForm').reset();
    }

    // Submit bulk update
    function submitBulkUpdate() {
        const form = document.getElementById('bulkUpdateForm');
        const formData = new FormData(form);

        // Show loading
        const updateBtn = document.querySelector('button[onclick="submitBulkUpdate()"]');
        const originalText = updateBtn.innerHTML;
        updateBtn.innerHTML = '<i data-lucide="loader-2" class="h-4 w-4 mr-2 animate-spin"></i>Updating...';
        updateBtn.disabled = true;

        // Submit form
        const submitForm = document.createElement('form');
        submitForm.method = 'POST';
        submitForm.action = '{{ route("backend.kepegawaian-universitas.data-pegawai.bulk-update") }}';

        // Add form data
        for (let [key, value] of formData.entries()) {
            if (value) { // Only add non-empty values
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                submitForm.appendChild(input);
            }
        }

        document.body.appendChild(submitForm);
        submitForm.submit();
    }

    // Get selected IDs
    function getSelectedIds() {
        const checkedBoxes = document.querySelectorAll('.pegawai-checkbox:checked');
        return Array.from(checkedBoxes).map(checkbox => checkbox.value);
    }

</script>

@endpush

@endsection
