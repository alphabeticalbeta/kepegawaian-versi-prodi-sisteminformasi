@extends('backend.layouts.roles.admin-universitas.app')

@section('title', 'Dasar Hukum')

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

    /* Rich Text Editor Styles - Minimal custom CSS for editor functionality */
    #editor {
        min-height: 200px;
        font-family: 'Inter', sans-serif;
        line-height: 1.6;
        color: #374151;
    }

    #editor:focus {
        outline: none;
    }

    #editor p {
        margin-bottom: 1rem;
    }

    #editor ul, #editor ol {
        margin: 1rem 0;
        padding-left: 2rem;
    }

    #editor ul {
        list-style-type: disc;
    }

    #editor ol {
        list-style-type: decimal;
    }

    #editor li {
        margin-bottom: 0.5rem;
    }

    #editor strong {
        font-weight: 600;
    }

    #editor em {
        font-style: italic;
    }

    #editor u {
        text-decoration: underline;
    }

    #editor h1, #editor h2, #editor h3 {
        font-weight: 600;
        margin: 1.5rem 0 1rem 0;
    }

    #editor h1 {
        font-size: 1.875rem;
    }

    #editor h2 {
        font-size: 1.5rem;
    }

    #editor h3 {
        font-size: 1.25rem;
    }

    #editor blockquote {
        border-left: 4px solid #E5E7EB;
        padding-left: 1rem;
        margin: 1rem 0;
        font-style: italic;
        color: #6B7280;
    }

    #editor::-webkit-scrollbar {
        width: 6px;
    }

    #editor::-webkit-scrollbar-track {
        background: #F3F4F6;
        border-radius: 3px;
    }

    #editor::-webkit-scrollbar-thumb {
        background: #D1D5DB;
        border-radius: 3px;
    }

    #editor::-webkit-scrollbar-thumb:hover {
        background: #9CA3AF;
    }

    /* Line clamp utility */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Hide pagination by default */
    #paginationContainer {
        display: none;
    }

    /* Show pagination when needed */
    #paginationContainer.show {
        display: block;
    }

    /* Show pagination info even when pagination controls are hidden */
    #paginationContainer:not(.show) {
        display: block;
    }

    /* Hide pagination controls when not needed */
    #paginationContainer:not(.show) .flex.items-center.space-x-2 {
        display: none;
    }

    /* Table transition animations */
    #dasarHukumTableBody {
        transition: opacity 0.3s ease-in-out;
    }

    .table-loading {
        opacity: 0.5;
        pointer-events: none;
    }

    .table-loaded {
        opacity: 1;
    }

    /* Pagination button animations */
    #paginationContainer button {
        transition: all 0.2s ease-in-out;
    }

    #paginationContainer button:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    #paginationContainer button:active:not(:disabled) {
        transform: translateY(0);
    }

    /* Page number button animations */
    #pageNumbers button {
        transition: all 0.2s ease-in-out;
    }

    #pageNumbers button:hover {
        transform: scale(1.05);
    }

    #pageNumbers button:active {
        transform: scale(0.95);
    }

    /* Loading spinner animation */
    .loading-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #6366f1;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
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
                    Dasar Hukum
                </h1>
                <p class="text-base text-black sm:text-lg">
                    Kelola dokumen dasar hukum Universitas Mulawarman
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="relative z-10 -mt-8 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-full pt-4 mt-4 animate-fade-in">
            <!-- Filter and Search -->
            <div class="mb-4 bg-white rounded-2xl shadow-xl p-4 transition-all duration-300 hover:shadow-2xl">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis</label>
                        <select id="filterJenis" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400">
                            <option value="">Semua</option>
                            <option value="keputusan">Keputusan</option>
                            <option value="pedoman">Pedoman</option>
                            <option value="peraturan">Peraturan</option>
                            <option value="surat_edaran">Surat Edaran</option>
                            <option value="surat_kementerian">Surat Kementerian</option>
                            <option value="surat_rektor">Surat Rektor</option>
                            <option value="undang_undang">Undang-Undang</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                        <select id="filterStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400">
                            <option value="">Semua</option>
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                        <input type="text" id="searchInput" placeholder="Cari judul/nomor dokumen..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400">
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl">
                <!-- Table Header with Add Button -->
                <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Dasar Hukum</h3>
                    <button onclick="openModal()"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:from-indigo-700 hover:to-purple-700 focus:from-indigo-700 focus:to-purple-700 active:from-indigo-900 active:to-purple-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md hover:shadow-lg">
                        <i data-lucide="plus" class="h-4 w-4 mr-2"></i>
                        Tambah Dasar Hukum
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black font-bold uppercase tracking-wider w-16 text-center">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black font-bold uppercase tracking-wider w-24 text-center">Jenis</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black font-bold uppercase tracking-wider w-48 text-center">Judul</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black font-bold uppercase tracking-wider w-28 text-center">Nomor Dokumen</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black font-bold uppercase tracking-wider w-28 text-center">Instansi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black font-bold uppercase tracking-wider w-20 text-center">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black font-bold uppercase tracking-wider w-24 text-center">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black font-bold uppercase tracking-wider w-24 text-center">Dokumen</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black font-bold uppercase tracking-wider w-28 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="dasarHukumTableBody" class="bg-white divide-y divide-gray-200">
                            <!-- Data will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div id="paginationContainer" class="bg-white rounded-2xl shadow-xl overflow-hidden mt-6">
        <div class="px-4 py-4 flex items-center justify-between">
            <div class="flex items-center text-sm text-gray-700">
                <span id="paginationInfo">Menampilkan 0 dari 0 data</span>
            </div>
            <div class="flex items-center space-x-2">
                <button id="prevPageBtn" onclick="changePage(currentPage - 1)"
                        class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                        disabled>
                    <i data-lucide="chevron-left" class="h-4 w-4"></i>
                </button>
                <div id="pageNumbers" class="flex space-x-1">
                    <!-- Page numbers will be generated here -->
                </div>
                <button id="nextPageBtn" onclick="changePage(currentPage + 1)"
                        class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                        disabled>
                    <i data-lucide="chevron-right" class="h-4 w-4"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="dasarHukumModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 transition-opacity duration-300">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 xl:w-2/5 shadow-2xl rounded-2xl bg-white transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-6 border-b border-gray-200">
                <h3 id="modalTitle" class="text-xl font-bold text-gray-900">Tambah Dasar Hukum</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="x" class="h-6 w-6"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="dasarHukumForm" class="mt-6" enctype="multipart/form-data">
                <input type="hidden" id="editId" name="id">

                <div class="space-y-6">
                    <!-- Jenis Dasar Hukum dan Sub Jenis -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="jenis_dasar_hukum" class="block text-sm font-semibold text-gray-700 mb-3">
                                Jenis Dasar Hukum <span class="text-red-500">*</span>
                            </label>
                            <select id="jenis_dasar_hukum" name="jenis_dasar_hukum" required onchange="toggleSubJenis()"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400">
                                <option value="">Pilih Jenis Dasar Hukum</option>
                                <option value="keputusan">Keputusan</option>
                                <option value="pedoman">Pedoman</option>
                                <option value="peraturan">Peraturan</option>
                                <option value="surat_edaran">Surat Edaran</option>
                                <option value="surat_kementerian">Surat Kementerian</option>
                                <option value="surat_rektor">Surat Rektor Universitas Mulawarman</option>
                                <option value="undang_undang">Undang-Undang</option>
                            </select>
                        </div>

                        <!-- Sub Jenis - Conditional -->
                        <div id="subJenisField" class="hidden">
                            <label for="sub_jenis" class="block text-sm font-semibold text-gray-700 mb-3">
                                Sub Jenis <span class="text-red-500">*</span>
                            </label>
                            <select id="sub_jenis" name="sub_jenis"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400">
                                <option value="">Pilih Sub Jenis</option>
                                <option value="peraturan">Peraturan</option>
                                <option value="surat_keputusan">Surat Keputusan</option>
                                <option value="sk_non_pns">Surat Keputusan (SK) Non PNS</option>
                            </select>
                        </div>
                    </div>

                    <!-- Nomor Dokumen dan Tanggal Dokumen -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nomor_dokumen" class="block text-sm font-semibold text-gray-700 mb-3">
                                Nomor Dokumen <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <input type="text" id="nomor_dokumen" name="nomor_dokumen" required
                                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400"
                                       placeholder="001/UNMUL/REKTOR/2024">
                                <button type="button" onclick="generateNomorDokumen()"
                                        class="px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label for="tanggal_dokumen" class="block text-sm font-semibold text-gray-700 mb-3">
                                Tanggal Dokumen <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="tanggal_dokumen" name="tanggal_dokumen" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400">
                        </div>
                    </div>

                    <!-- Nama Instansi -->
                    <div class="mb-6">
                        <div>
                            <label for="nama_instansi" class="block text-sm font-semibold text-gray-700 mb-3">
                                Nama Instansi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nama_instansi" name="nama_instansi" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400"
                                   placeholder="Universitas Mulawarman">
                        </div>

                    <!-- Judul -->
                    <div class="mt-6">
                        <label for="judul" class="block text-sm font-semibold text-gray-700 mb-3">
                            Judul <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="judul" name="judul" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400"
                               placeholder="Masukkan judul dokumen dasar hukum">
                    </div>

                    <!-- Konten -->
                    <div class="mb-6 mt-6">
                        <label for="konten" class="block text-sm font-semibold text-gray-700 mb-3">
                            Konten <span class="text-red-500">*</span>
                        </label>
                        <!-- Rich Text Editor Toolbar -->
                        <div class="border border-gray-300 rounded-t-lg bg-gray-50 p-2">
                            <div class="flex flex-wrap gap-1">
                                <button type="button" class="ql-bold p-2 hover:bg-gray-200 rounded" title="Bold">
                                    <i data-lucide="bold" class="h-4 w-4"></i>
                                </button>
                                <button type="button" class="ql-italic p-2 hover:bg-gray-200 rounded" title="Italic">
                                    <i data-lucide="italic" class="h-4 w-4"></i>
                                </button>
                                <button type="button" class="ql-underline p-2 hover:bg-gray-200 rounded" title="Underline">
                                    <i data-lucide="underline" class="h-4 w-4"></i>
                                </button>
                                <div class="w-px h-6 bg-gray-300 mx-1"></div>
                                <button type="button" class="ql-list p-2 hover:bg-gray-200 rounded" value="ordered" title="Numbered List">
                                    <i data-lucide="list-ordered" class="h-4 w-4"></i>
                                </button>
                                <button type="button" class="ql-list p-2 hover:bg-gray-200 rounded" value="bullet" title="Bullet List">
                                    <i data-lucide="list" class="h-4 w-4"></i>
                                </button>
                                <div class="w-px h-6 bg-gray-300 mx-1"></div>
                                <button type="button" class="ql-align p-2 hover:bg-gray-200 rounded" value="" title="Align Left">
                                    <i data-lucide="align-left" class="h-4 w-4"></i>
                                </button>
                                <button type="button" class="ql-align p-2 hover:bg-gray-200 rounded" value="center" title="Align Center">
                                    <i data-lucide="align-center" class="h-4 w-4"></i>
                                </button>
                                <button type="button" class="ql-align p-2 hover:bg-gray-200 rounded" value="right" title="Align Right">
                                    <i data-lucide="align-right" class="h-4 w-4"></i>
                                </button>
                                <div class="w-px h-6 bg-gray-300 mx-1"></div>
                                <button type="button" class="ql-indent p-2 hover:bg-gray-200 rounded" value="-1" title="Decrease Indent">
                                    <i data-lucide="indent-decrease" class="h-4 w-4"></i>
                                </button>
                                <button type="button" class="ql-indent p-2 hover:bg-gray-200 rounded" value="+1" title="Increase Indent">
                                    <i data-lucide="indent-increase" class="h-4 w-4"></i>
                                </button>
                            </div>
                        </div>
                        <div id="editor" contenteditable="true" class="w-full px-4 py-3 border border-gray-300 border-t-0 rounded-b-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400"
                             placeholder="Masukkan konten dokumen dasar hukum..."></div>
                        <input type="hidden" id="konten" name="konten">
                    </div>

                    <!-- Penulis dan Tags -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="penulis" class="block text-sm font-semibold text-gray-700 mb-3">
                                Penulis
                            </label>
                            <input type="text" id="penulis" name="penulis" readonly
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm bg-gray-100 text-gray-600 cursor-not-allowed"
                                   placeholder="Nama penulis"
                                   @if(auth()->guard('pegawai')->check())
                                       value="{{ auth()->guard('pegawai')->user()->nama_lengkap ?? auth()->guard('pegawai')->user()->name ?? 'Pegawai' }}"
                                   @else
                                       value="{{ auth()->user()->name ?? 'Administrator' }}"
                                   @endif>
                            <p class="text-xs text-gray-500 mt-1">
                                <i data-lucide="lock" class="w-3 h-3 inline mr-1"></i>
                                Penulis otomatis dari pegawai yang login
                            </p>
                        </div>
                        <div>
                            <label for="tags" class="block text-sm font-semibold text-gray-700 mb-3">
                                Tags
                            </label>
                            <input type="text" id="tags" name="tags"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400"
                                   placeholder="kepegawaian, pendidikan, umum (pisahkan dengan koma)">
                        </div>
                    </div>

                    <!-- Thumbnail dan Lampiran -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="thumbnail" class="block text-sm font-semibold text-gray-700 mb-3">
                                Thumbnail
                            </label>
                            <input type="file" id="thumbnail" name="thumbnail" accept="image/*"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400">
                        <p class="text-xs text-gray-500 mt-1">
                            Format: JPEG, PNG, JPG, GIF. Max: 2MB
                        </p>
                        <!-- Current Thumbnail Display -->
                        <div id="currentThumbnail" class="mt-3 hidden">
                            <p class="text-sm font-medium text-gray-700 mb-2">File saat ini:</p>
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border">
                                <div class="flex-shrink-0">
                                    <img id="thumbnailPreview" src="" alt="Thumbnail" class="h-12 w-12 object-cover rounded">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p id="thumbnailName" class="text-sm font-medium text-gray-900 truncate"></p>
                                    <p class="text-xs text-gray-500">Klik untuk download</p>
                                </div>
                                <a id="thumbnailDownload" href="#" target="_blank"
                                   class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i data-lucide="download" class="h-3 w-3 mr-1"></i>
                                    Download
                                </a>
                            </div>
                        </div>
                        </div>
                        <div>
                            <label for="lampiran" class="block text-sm font-semibold text-gray-700 mb-3">
                                Lampiran <span class="text-red-500" id="lampiranRequired">*</span>
                            </label>
                            <input type="file" id="lampiran" name="lampiran[]" multiple accept=".pdf"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400">
                            <p class="text-xs text-gray-500 mt-1">
                                Format: PDF saja. Max: 10MB per file
                            </p>
                            <!-- Current Attachments Display -->
                            <div id="currentAttachments" class="mt-3 hidden">
                                <p class="text-sm font-medium text-gray-700 mb-2">File saat ini:</p>
                                <div id="attachmentsList" class="space-y-2">
                                    <!-- Attachments will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tanggal Publish dan Masa Berlaku -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="tanggal_publish" class="block text-sm font-semibold text-gray-700 mb-3">
                                Tanggal Publish
                            </label>
                            <input type="datetime-local" id="tanggal_publish" name="tanggal_publish"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400">
                        </div>
                        <div>
                            <label for="masa_berlaku" class="block text-sm font-semibold text-gray-700 mb-3">
                                Masa Berlaku
                            </label>
                            <input type="date" id="masa_berlaku" name="masa_berlaku"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400">
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-3">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>

                    <!-- Options -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div class="flex items-center justify-center">
                            <input type="checkbox" id="is_featured" name="is_featured" value="1"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="is_featured" class="ml-2 block text-sm text-gray-700">
                                Featured (Tampil di halaman utama)
                            </label>
                        </div>
                        <div class="flex items-center justify-center">
                            <input type="checkbox" id="is_pinned" name="is_pinned" value="1"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="is_pinned" class="ml-2 block text-sm text-gray-700">
                                Pinned (Selalu tampil di atas)
                            </label>
                        </div>
                    </div>
                </div>

                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeModal()"
                            class="px-6 py-3 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-md hover:shadow-lg">
                        Batal
                    </button>
                    <button type="submit" id="submitBtn"
                            class="inline-flex items-center px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <span id="submitText">Simpan</span>
                        <div id="submitLoader" class="hidden ml-2">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Escape HTML function for security
function escapeHtml(text) {
    if (text === null || text === undefined) {
        return '';
    }
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
}

// Global variables
let currentPage = 1;
let totalPages = 1;
let currentSearch = '';
let currentFilters = {
    jenis: '',
    status: ''
};
let currentEditId = null;
let dasarHukumData = []; // Store loaded data for edit
let totalData = 0;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadDasarHukum();
    initializeEditor();
    initializeFilters();
});

// Load dasar hukum data
async function loadDasarHukum(page = 1) {
    try {
        // Show loading animation
        showTableLoading();

        const params = new URLSearchParams({
            page: page,
            per_page: 10,
            search: currentSearch,
            jenis: currentFilters.jenis,
            status: currentFilters.status
        });

        const response = await fetch(`/admin-universitas/dasar-hukum/data?${params}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        });

        if (response.ok) {
            const data = await response.json();
            // API Response received

            if (data.success) {
                // Store data globally for edit functionality
                dasarHukumData = data.data;
                window.currentDasarHukumData = data.data;
                // Data loaded successfully

                // Check if pagination should be shown

                // Add delay for smooth animation
                setTimeout(() => {
                    renderTable(data.data);
                    updatePagination(data);
                    hideTableLoading();
                }, 200);
            } else {
                hideTableLoading();
                showAlert('Error!', 'Terjadi kesalahan saat memuat data', 'error');
            }
        } else {
            hideTableLoading();
            showAlert('Error!', 'Gagal memuat data dasar hukum', 'error');
        }
    } catch (error) {
        hideTableLoading();
        // Error loading data
        showAlert('Error!', 'Terjadi kesalahan saat memuat data', 'error');
    }
}

// Render table
function renderTable(dasarHukum) {
    const tbody = document.getElementById('dasarHukumTableBody');

    if (!tbody) {
        // Table body not found
        return;
    }

    // Add fade-in animation class
    tbody.classList.add('fade-in');

    if (dasarHukum.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                    <div class="flex flex-col items-center">
                        <i data-lucide="file-x" class="h-12 w-12 text-gray-300 mb-2"></i>
                        <p class="text-lg font-medium">Tidak ada data dasar hukum</p>
                        <p class="text-sm text-gray-400">Klik "Tambah Dasar Hukum" untuk menambah data baru</p>
                    </div>
                </td>
            </tr>
        `;
        lucide.createIcons();
        return;
    }

    tbody.innerHTML = dasarHukum.map((item, index) => `
        <tr class="hover:bg-gray-50 transition-all duration-200 animate-fade-in" style="animation-delay: ${index * 50}ms">
            <td class="px-4 py-3 text-sm text-gray-900 text-center">${(currentPage - 1) * 10 + index + 1}</td>
            <td class="px-4 py-3 text-center">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    ${getJenisLabel(item.jenis_dasar_hukum)}
                </span>
                ${item.sub_jenis ? `<div class="text-xs text-gray-500 mt-1">${getSubJenisLabel(item.sub_jenis)}</div>` : ''}
            </td>
            <td class="px-4 py-3">
                <div class="text-sm font-medium text-gray-900 line-clamp-2" title="${escapeHtml(item.judul)}">${escapeHtml(item.judul)}</div>
                <div class="text-sm text-gray-500">${escapeHtml(item.penulis)}</div>
            </td>
            <td class="px-4 py-3 text-sm text-gray-900 text-center">${escapeHtml(item.nomor_dokumen)}</td>
            <td class="px-4 py-3 text-sm text-gray-900 text-center">${escapeHtml(item.nama_instansi)}</td>
            <td class="px-4 py-3 text-center">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusClass(item.status)}">
                    ${getStatusLabel(item.status)}
                </span>
            </td>
            <td class="px-4 py-3 text-sm text-gray-900 text-center">${formatDate(item.tanggal_dokumen)}</td>
            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-center">
                ${item.lampiran && item.lampiran.length > 0 ?
                    item.lampiran.map((file, index) =>
                        `<a href="/dasar-hukum-document/${escapeHtml(file.path)}" target="_blank" class="inline-flex items-center px-4 py-2 text-xs font-medium text-white bg-gradient-to-r from-purple-600 to-indigo-600 rounded hover:from-purple-700 hover:to-indigo-700 transition-all duration-200 mr-1" title="Buka ${escapeHtml(file.name)}"><i data-lucide="paperclip" class="h-3 w-3 mr-1"></i>Lihat Dokumen${item.lampiran.length > 1 ? ' ' + (index + 1) : ''}</a>`
                    ).join('')
                    : '<span class="text-gray-400 text-xs">-</span>'}
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-center">
                <button onclick="editDasarHukum(${item.id})"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-cyan-600 rounded-lg hover:from-blue-700 hover:to-cyan-700 transition-all duration-200 shadow-md hover:shadow-lg mr-2"
                        title="Edit">
                    <i data-lucide="edit" class="h-4 w-4 mr-2"></i>
                    Edit
                </button>
                <button onclick="deleteDasarHukum(${item.id})"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-red-600 to-pink-600 rounded-lg hover:from-red-700 hover:to-pink-700 transition-all duration-200 shadow-md hover:shadow-lg"
                        title="Hapus">
                    <i data-lucide="trash-2" class="h-4 w-4 mr-2"></i>
                    Hapus
                </button>
            </td>
        </tr>
    `).join('');

    // Re-initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

// Helper functions
function getJenisLabel(jenis) {
    const labels = {
        'keputusan': 'Keputusan',
        'pedoman': 'Pedoman',
        'peraturan': 'Peraturan',
        'surat_edaran': 'Surat Edaran',
        'surat_kementerian': 'Surat Kementerian',
        'surat_rektor': 'Surat Rektor',
        'undang_undang': 'Undang-Undang'
    };
    return labels[jenis] || jenis;
}

function getSubJenisLabel(subJenis) {
    const labels = {
        'peraturan': 'Peraturan',
        'surat_keputusan': 'Surat Keputusan',
        'sk_non_pns': 'SK Non PNS'
    };
    return labels[subJenis] || subJenis;
}

function getStatusLabel(status) {
    const labels = {
        'draft': 'Draft',
        'published': 'Published',
        'archived': 'Archived'
    };
    return labels[status] || status;
}

function getStatusClass(status) {
    const classes = {
        'draft': 'bg-yellow-100 text-yellow-800',
        'published': 'bg-green-100 text-green-800',
        'archived': 'bg-gray-100 text-gray-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
}


function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

// Modal functions
function openModal() {
    currentEditId = null;
    document.getElementById('modalTitle').textContent = 'Tambah Dasar Hukum';
    document.getElementById('dasarHukumForm').reset();
    document.getElementById('editId').value = '';
    document.getElementById('submitText').textContent = 'Simpan';
    document.getElementById('editor').innerHTML = '';
    document.getElementById('subJenisField').classList.add('hidden');

    // Set lampiran as required for new entries
    document.getElementById('lampiran').required = true;
    document.getElementById('lampiranRequired').style.display = 'inline';

    // Restore penulis value after reset (since it's read-only)
    setTimeout(() => {
        @if(auth()->guard('pegawai')->check())
            document.getElementById('penulis').value = "{{ auth()->guard('pegawai')->user()->nama_lengkap ?? auth()->guard('pegawai')->user()->name ?? 'Pegawai' }}";
        @else
            document.getElementById('penulis').value = "{{ auth()->user()->name ?? 'Administrator' }}";
        @endif
    }, 10);

    // Hide current files
    document.getElementById('currentThumbnail').classList.add('hidden');
    document.getElementById('currentAttachments').classList.add('hidden');

    const modal = document.getElementById('dasarHukumModal');
    const modalContent = document.getElementById('modalContent');

    modal.classList.remove('hidden');

    // Trigger animation
    setTimeout(() => {
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeModal() {
    const modal = document.getElementById('dasarHukumModal');
    const modalContent = document.getElementById('modalContent');

    // Trigger animation
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
        currentEditId = null;
        resetForm();
    }, 300);
}

function resetForm() {
    document.getElementById('dasarHukumForm').reset();
    document.getElementById('editId').value = '';
    document.getElementById('modalTitle').textContent = 'Tambah Dasar Hukum';
    document.getElementById('submitText').textContent = 'Simpan';

    // Clear rich text editor
    if (document.getElementById('editor')) {
        document.getElementById('editor').innerHTML = '';
    }

    // Hide current file displays
    document.getElementById('currentThumbnail').classList.add('hidden');
    document.getElementById('currentAttachments').classList.add('hidden');
}

// Toggle sub jenis field
function toggleSubJenis() {
    const jenis = document.getElementById('jenis_dasar_hukum').value;
    const subJenisField = document.getElementById('subJenisField');
    const subJenisSelect = document.getElementById('sub_jenis');

    if (jenis === 'surat_rektor') {
        subJenisField.classList.remove('hidden');
        subJenisSelect.required = true;
    } else {
        subJenisField.classList.add('hidden');
        subJenisSelect.required = false;
        subJenisSelect.value = '';
    }
}

// Display current files
function displayCurrentFiles(item) {
    // Display current thumbnail
    const currentThumbnail = document.getElementById('currentThumbnail');
    if (item.thumbnail) {
        const thumbnailPreview = document.getElementById('thumbnailPreview');
        const thumbnailName = document.getElementById('thumbnailName');
        const thumbnailDownload = document.getElementById('thumbnailDownload');

        // Extract filename from path
        const filename = item.thumbnail.split('/').pop();

        // Use the same route as lampiran for thumbnail access
        const thumbnailUrl = `/dasar-hukum-document/${filename}`;

        thumbnailPreview.src = thumbnailUrl;
        thumbnailName.textContent = filename;
        thumbnailDownload.href = thumbnailUrl;

        currentThumbnail.classList.remove('hidden');
    } else {
        currentThumbnail.classList.add('hidden');
    }

    // Display current attachments
    const currentAttachments = document.getElementById('currentAttachments');
    const attachmentsList = document.getElementById('attachmentsList');

    if (item.lampiran && item.lampiran.length > 0) {
        attachmentsList.innerHTML = '';
        item.lampiran.forEach(file => {
            // Handle both old format (string) and new format (object)
            const fileName = typeof file === 'string' ? file.split('/').pop() : file.name;
            const filePath = typeof file === 'string' ? file.split('/').pop() : file.path;
            const fileSize = typeof file === 'object' ? file.size : null;

            const attachmentDiv = document.createElement('div');
            attachmentDiv.className = 'flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border';
            attachmentDiv.innerHTML = `
                <div class="flex-shrink-0">
                    <i data-lucide="file" class="h-8 w-8 text-gray-400"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">${escapeHtml(fileName)}</p>
                    <p class="text-xs text-gray-500">${fileSize ? `Size: ${(fileSize / 1024).toFixed(1)} KB` : 'Klik untuk download'}</p>
                </div>
                <a href="/dasar-hukum-document/${escapeHtml(filePath)}" target="_blank"
                   class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i data-lucide="download" class="h-3 w-3 mr-1"></i>
                    Download
                </a>
            `;
            attachmentsList.appendChild(attachmentDiv);
        });

        currentAttachments.classList.remove('hidden');
        lucide.createIcons();
    } else {
        currentAttachments.classList.add('hidden');
    }
}

// Generate nomor dokumen
function generateNomorDokumen() {
    const jenis = document.getElementById('jenis_dasar_hukum').value;
    const tahun = new Date().getFullYear();
    const instansi = document.getElementById('nama_instansi').value || 'UNMUL';

    let prefix = '';
    switch(jenis) {
        case 'keputusan': prefix = 'KEP'; break;
        case 'peraturan': prefix = 'PER'; break;
        case 'surat_edaran': prefix = 'SE'; break;
        case 'surat_kementerian': prefix = 'SKM'; break;
        case 'surat_rektor': prefix = 'SKR'; break;
        case 'undang_undang': prefix = 'UU'; break;
        default: prefix = 'DH'; // Dasar Hukum
    }

    const nomor = `001/${instansi}/${prefix}/${tahun}`;
    document.getElementById('nomor_dokumen').value = nomor;
}

// Initialize rich text editor
function initializeEditor() {
    const editor = document.getElementById('editor');
    const kontenInput = document.getElementById('konten');

    // Update hidden input when editor content changes
    editor.addEventListener('input', function() {
        kontenInput.value = editor.innerHTML;
    });

    // Handle toolbar buttons
    document.querySelectorAll('.ql-bold').forEach(btn => {
        btn.addEventListener('click', () => {
            document.execCommand('bold');
            editor.focus();
        });
    });

    document.querySelectorAll('.ql-italic').forEach(btn => {
        btn.addEventListener('click', () => {
            document.execCommand('italic');
            editor.focus();
        });
    });

    document.querySelectorAll('.ql-underline').forEach(btn => {
        btn.addEventListener('click', () => {
            document.execCommand('underline');
            editor.focus();
        });
    });

    document.querySelectorAll('.ql-list').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const value = e.target.getAttribute('value');
            if (value === 'ordered') {
                document.execCommand('insertOrderedList');
            } else if (value === 'bullet') {
                document.execCommand('insertUnorderedList');
            }
            editor.focus();
        });
    });

    document.querySelectorAll('.ql-align').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const value = e.target.getAttribute('value');
            document.execCommand('justify' + (value || 'Left'));
            editor.focus();
        });
    });

    document.querySelectorAll('.ql-indent').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const value = e.target.getAttribute('value');
            if (value === '+1') {
                document.execCommand('indent');
            } else if (value === '-1') {
                document.execCommand('outdent');
            }
            editor.focus();
        });
    });
}

// Form submission
document.getElementById('dasarHukumForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitLoader = document.getElementById('submitLoader');

    // Show loading
    showSubmitLoading(true);

    try {
        const formData = new FormData(this);
        const isEdit = currentEditId !== null;
        const url = isEdit ? `/admin-universitas/dasar-hukum/${currentEditId}` : '/admin-universitas/dasar-hukum';

        if (isEdit) {
            formData.append('_method', 'PUT');
        }

        const response = await fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        });

        const data = await response.json();

        if (data.success) {
            showAlert('Berhasil!', 'Data berhasil disimpan', 'success');
            closeModal();
            loadDasarHukum(currentPage);
        } else {
            // Handle validation errors
            if (data.errors) {
                let errorMessage = 'Validasi gagal:\n';
                for (const field in data.errors) {
                    errorMessage += `• ${escapeHtml(data.errors[field][0])}\n`;
                }
                showAlert('Error!', errorMessage, 'error');
            } else {
                showAlert('Error!', 'Terjadi kesalahan saat menyimpan data', 'error');
            }
        }
    } catch (error) {
        // Error submitting form
        showAlert('Error!', 'Terjadi kesalahan saat menyimpan data', 'error');
    } finally {
        // Hide loading
        showSubmitLoading(false);
    }
});

// Show submit loading
function showSubmitLoading(show) {
    const submitBtn = document.getElementById('submitBtn');
    const text = document.getElementById('submitText');
    const loader = document.getElementById('submitLoader');

    if (show) {
        submitBtn.disabled = true;
        text.textContent = 'Menyimpan...';
        loader.classList.remove('hidden');
    } else {
        submitBtn.disabled = false;
        // Restore original text based on edit mode
        text.textContent = currentEditId ? 'Update' : 'Simpan';
        loader.classList.add('hidden');
    }
}

// Edit function
function editDasarHukum(id) {
    const item = dasarHukumData.find(item => item.id === id);
    if (!item) return;

    // Edit item data

    currentEditId = id;
    document.getElementById('modalTitle').textContent = 'Edit Dasar Hukum';
    document.getElementById('editId').value = item.id;
    document.getElementById('jenis_dasar_hukum').value = item.jenis_dasar_hukum;
    document.getElementById('judul').value = item.judul;
    document.getElementById('editor').innerHTML = escapeHtml(item.konten);
    document.getElementById('konten').value = item.konten;
    document.getElementById('status').value = item.status;

    // Apply toggle first to show/hide sub jenis fields
    toggleSubJenis();

    // Add small delay to ensure modal is fully rendered
    setTimeout(() => {
        // Check if elements exist before setting values
        const subJenisField = document.getElementById('sub_jenis');
        const nomorDokumenField = document.getElementById('nomor_dokumen');
        const tanggalDokumenField = document.getElementById('tanggal_dokumen');
        const namaInstansiField = document.getElementById('nama_instansi');
        const penulisField = document.getElementById('penulis');
        const tagsField = document.getElementById('tags');
        const tanggalField = document.getElementById('tanggal_publish');
        const masaBerlakuField = document.getElementById('masa_berlaku');

        // Form fields found

        if (subJenisField) {
            subJenisField.value = item.sub_jenis || '';
            // Sub Jenis value set
        }

        if (nomorDokumenField) {
            nomorDokumenField.value = item.nomor_dokumen || '';
            // Nomor Dokumen value set
        }

        if (tanggalDokumenField) {
            // Handle tanggal_dokumen format
            // Handle tanggal_dokumen format
            if (item.tanggal_dokumen) {
                // Convert from "2025-09-07T00:00:00.000000Z" to "2025-09-07" (date format)
                const dateStr = item.tanggal_dokumen.substring(0, 10);
                tanggalDokumenField.value = dateStr;
                // Tanggal Dokumen value set
            } else {
                tanggalDokumenField.value = '';
                // Tanggal Dokumen is empty, field cleared
            }
        }

        if (namaInstansiField) {
            namaInstansiField.value = item.nama_instansi || '';
            // Nama Instansi value set
        }

        // Ensure penulis field is always filled with current user (read-only)
        if (penulisField) {
            @if(auth()->guard('pegawai')->check())
                penulisField.value = "{{ auth()->guard('pegawai')->user()->nama_lengkap ?? auth()->guard('pegawai')->user()->name ?? 'Pegawai' }}";
            @else
                penulisField.value = "{{ auth()->user()->name ?? 'Administrator' }}";
            @endif
        }

        if (tagsField) {
            tagsField.value = item.tags ? item.tags.join(', ') : '';
            // Tags value set
        }

        // Handle tanggal_publish format
        if (tanggalField && item.tanggal_publish) {
            // Convert from "2025-09-07T17:41:00.000000Z" to "2025-09-07T17:41"
            const dateStr = item.tanggal_publish.replace('T', 'T').substring(0, 16);
            tanggalField.value = dateStr;
            // Tanggal value set
        } else if (tanggalField) {
            tanggalField.value = '';
        }

        if (masaBerlakuField) {
            masaBerlakuField.value = item.masa_berlaku || '';
            // Masa Berlaku value set
        }

        document.getElementById('is_featured').checked = item.is_featured;
        document.getElementById('is_pinned').checked = item.is_pinned;

        // Display current files
        displayCurrentFiles(item);

        // Set lampiran as optional for edit (since existing files are preserved)
        document.getElementById('lampiran').required = false;
        document.getElementById('lampiranRequired').style.display = 'none';
    }, 100); // End setTimeout

    const modal = document.getElementById('dasarHukumModal');
    const modalContent = document.getElementById('modalContent');

    modal.classList.remove('hidden');

    // Trigger animation
    setTimeout(() => {
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
}

// Delete function
async function deleteDasarHukum(id) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Data yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'bg-gray-800 text-white',
                title: 'text-white',
                content: 'text-gray-300',
                confirmButton: 'bg-red-600 hover:bg-red-700 text-white',
                cancelButton: 'bg-gray-600 hover:bg-gray-700 text-white'
            }
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/admin-universitas/dasar-hukum/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        credentials: 'same-origin'
                    });

                    const data = await response.json();

                    if (data.success) {
                        showAlert('Berhasil!', 'Data berhasil dihapus', 'success');
                        loadDasarHukum(currentPage);
                    } else {
                        showAlert('Error!', 'Terjadi kesalahan saat menghapus data', 'error');
                    }
                } catch (error) {
                    // Error deleting data
                    showAlert('Error!', 'Terjadi kesalahan saat menghapus data', 'error');
                }
            }
        });
    } else {
        if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            try {
                const response = await fetch(`/admin-universitas/dasar-hukum/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                });

                const data = await response.json();

                if (data.success) {
                    showAlert('Success', 'Data berhasil dihapus', 'success');
                    loadDasarHukum(currentPage);
                } else {
                    showAlert('Error', 'Terjadi kesalahan saat menghapus data', 'error');
                }
            } catch (error) {
                // Error deleting data
                showAlert('Error', 'Gagal menghapus data', 'error');
            }
        }
    }
}

// Filter functions
function initializeFilters() {
    // Search input with real-time filtering
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function(e) {
            currentSearch = e.target.value.trim();

            // If search is empty, load all data immediately
            if (currentSearch === '') {
                loadDasarHukum(1);
            } else {
                // Only search if there's actual content
                loadDasarHukum(1);
            }
        }, 300));
    }

    // Real-time filtering for jenis dropdown
    const filterJenis = document.getElementById('filterJenis');
    if (filterJenis) {
        filterJenis.addEventListener('change', function() {
            currentFilters.jenis = this.value;
            loadDasarHukum(1);
        });
    }

    // Real-time filtering for status dropdown
    const filterStatus = document.getElementById('filterStatus');
    if (filterStatus) {
        filterStatus.addEventListener('change', function() {
            currentFilters.status = this.value;
            loadDasarHukum(1);
        });
    }
}

function applyFilters() {
    // This function is kept for backward compatibility but is no longer used
    currentFilters.jenis = document.getElementById('filterJenis').value;
    currentFilters.status = document.getElementById('filterStatus').value;
    currentSearch = document.getElementById('searchInput').value;

    loadDasarHukum(1);
}

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

// Alert function
function showAlert(title, message, type) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: title,
            text: message,
            icon: type,
            confirmButtonText: 'OK',
            customClass: {
                popup: 'bg-gray-800 text-white',
                title: 'text-white',
                content: 'text-gray-300',
                confirmButton: type === 'success' ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-red-600 hover:bg-red-700 text-white'
            }
        });
    } else {
        alert(`${escapeHtml(title)}: ${escapeHtml(message)}`);
    }
}

// Loading animation functions
function showTableLoading() {
    const tbody = document.getElementById('dasarHukumTableBody');
    if (tbody) {
        tbody.classList.add('table-loading');
        tbody.classList.remove('table-loaded');
    }
}

function hideTableLoading() {
    const tbody = document.getElementById('dasarHukumTableBody');
    if (tbody) {
        tbody.classList.remove('table-loading');
        tbody.classList.add('table-loaded');
    }
}

// Pagination functions
function updatePagination(data) {
    currentPage = data.current_page;
    totalPages = data.last_page;
    totalData = data.total;

    // Update pagination

    const container = document.getElementById('paginationContainer');
    const pageNumbers = document.getElementById('pageNumbers');
    const prevBtn = document.getElementById('prevPageBtn');
    const nextBtn = document.getElementById('nextPageBtn');

    // Pagination elements found

    // Show/hide pagination
    if (totalPages > 1) {
        container.classList.add('show');
    } else {
        container.classList.remove('show');
    }

    // Always show pagination container if there's data
    if (totalData > 0) {
        container.style.display = 'block';
    } else {
        container.style.display = 'none';
    }

    // Update pagination info
    const startItem = (currentPage - 1) * 10 + 1;
    const endItem = Math.min(currentPage * 10, totalData);
    document.getElementById('paginationInfo').textContent =
        `Menampilkan ${startItem}-${endItem} dari ${totalData} data`;

    // Update page numbers
    pageNumbers.innerHTML = '';
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);

    for (let i = startPage; i <= endPage; i++) {
        const pageBtn = document.createElement('button');
        pageBtn.textContent = i;
        pageBtn.className = `px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 ${
            i === currentPage
                ? 'bg-indigo-600 text-white shadow-md'
                : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-50 hover:shadow-sm'
        }`;
        pageBtn.onclick = (e) => {
            e.target.style.transform = 'scale(0.95)';
            setTimeout(() => {
                e.target.style.transform = '';
            }, 150);
            changePage(i);
        };
        pageNumbers.appendChild(pageBtn);
    }

    // Update prev/next buttons
    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled = currentPage === totalPages;
    prevBtn.onclick = (e) => {
        if (currentPage > 1) {
            e.target.style.transform = 'scale(0.95)';
            setTimeout(() => {
                e.target.style.transform = '';
            }, 150);
            changePage(currentPage - 1);
        }
    };
    nextBtn.onclick = (e) => {
        if (currentPage < totalPages) {
            e.target.style.transform = 'scale(0.95)';
            setTimeout(() => {
                e.target.style.transform = '';
            }, 150);
            changePage(currentPage + 1);
        }
    };
}

function changePage(page) {
    if (page < 1 || page > totalPages || page === currentPage) {
        return;
    }

    // Add click animation to the clicked button
    const clickedButton = event.target.closest('button');
    if (clickedButton) {
        clickedButton.style.transform = 'scale(0.95)';
        setTimeout(() => {
            clickedButton.style.transform = '';
        }, 150);
    }

    currentPage = page;
    loadDasarHukum(page);
}

// Initialize Lucide icons
lucide.createIcons();
</script>
@endpush
@endsection
