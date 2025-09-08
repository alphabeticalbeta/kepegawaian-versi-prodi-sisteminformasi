@extends('backend.layouts.roles.admin-universitas.app')

@section('title', 'Berita & Pengumuman')

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

    /* Hide pagination by default */
    #paginationContainer {
        display: none;
    }

    /* Show pagination when needed */
    #paginationContainer.show {
        display: block;
    }

    /* Table transition animations */
    #informasiTableBody {
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
                    Berita & Pengumuman
                </h1>
                <p class="text-base text-black sm:text-lg">
                    Kelola berita dan pengumuman Universitas Mulawarman
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="relative z-10 -mt-8 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-full pt-4 mt-4 animate-fade-in">
            <!-- Filter and Search -->
            <div class="mb-4 bg-white rounded-2xl shadow-xl p-4 transition-all duration-300 hover:shadow-2xl">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis</label>
                        <select id="filterJenis" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400">
                            <option value="">Semua</option>
                            <option value="berita">Berita</option>
                            <option value="pengumuman">Pengumuman</option>
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
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Featured/Pinned</label>
                        <select id="filterSpecial" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400">
                            <option value="">Semua</option>
                            <option value="featured">Featured</option>
                            <option value="pinned">Pinned</option>
                            <option value="both">Featured & Pinned</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                        <input type="text" id="searchInput" placeholder="Cari judul/konten..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400">
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl">
                <!-- Table Header with Add Button -->
                <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Berita & Pengumuman</h3>
                    <button onclick="openModal()"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:from-indigo-700 hover:to-purple-700 focus:from-indigo-700 focus:to-purple-700 active:from-indigo-900 active:to-purple-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md hover:shadow-lg">
                        <i data-lucide="plus" class="h-4 w-4 mr-2"></i>
                        Tambah Informasi
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black font-bold text-center uppercase tracking-wider w-16">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black font-bold text-center uppercase tracking-wider w-24">Jenis</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black font-bold text-center uppercase tracking-wider">Judul</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black font-bold text-center uppercase tracking-wider w-32">Nomor Surat</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black font-bold text-center uppercase tracking-wider w-24">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black font-bold text-center uppercase tracking-wider w-32">Featured/Pinned</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black font-bold text-center uppercase tracking-wider w-28">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black font-bold text-center uppercase tracking-wider w-24">Dokumen</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black font-bold text-center tracking-wider w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="informasiTableBody" class="bg-white divide-y divide-gray-200">
                            <!-- Data will be loaded here -->
                        </tbody>
                    </table>
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
    </div>
</div>

<!-- Modal -->
<div id="informasiModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 transition-opacity duration-300">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 xl:w-2/5 shadow-2xl rounded-2xl bg-white transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-6 border-b border-gray-200">
                <h3 id="modalTitle" class="text-xl font-bold text-gray-900">Tambah Informasi</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="x" class="h-6 w-6"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="informasiForm" class="mt-6" enctype="multipart/form-data">
                <input type="hidden" id="editId" name="id">

                <div class="space-y-6">
                    <!-- Jenis -->
                    <div>
                        <label for="jenis" class="block text-sm font-semibold text-gray-700 mb-3">
                            Jenis <span class="text-red-500">*</span>
                        </label>
                        <select id="jenis" name="jenis" required onchange="togglePengumumanFields()"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400">
                            <option value="">Pilih Jenis</option>
                            <option value="berita">Berita</option>
                            <option value="pengumuman">Pengumuman</option>
                        </select>
                    </div>

                    <!-- Fields khusus untuk Pengumuman -->
                    <div id="pengumumanFields" class="hidden grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nomor Surat -->
                        <div>
                            <label for="nomor_surat" class="block text-sm font-semibold text-gray-700 mb-3">
                                Nomor Surat <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <input type="text" id="nomor_surat" name="nomor_surat"
                                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400"
                                       placeholder="001/UNMUL/KEU/2024">
                                <button type="button" onclick="generateNomorSurat()"
                                        class="px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Format: [No]/[Instansi]/[Unit]/[Tahun]
                            </p>
                        </div>

                        <!-- Tanggal Surat -->
                        <div>
                            <label for="tanggal_surat" class="block text-sm font-semibold text-gray-700 mb-3">
                                Tanggal Surat <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="tanggal_surat" name="tanggal_surat"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400">
                        </div>
                    </div>

                    <!-- Judul -->
                    <div>
                        <label for="judul" class="block text-sm font-semibold text-gray-700 mb-3">
                            Judul <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="judul" name="judul" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400"
                               placeholder="Masukkan judul berita/pengumuman">
                    </div>

                    <!-- Konten -->
                    <div>
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
                             placeholder="Masukkan konten berita/pengumuman..."></div>
                        <input type="hidden" id="konten" name="konten">
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

                    <!-- Penulis dan Tags -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="penulis" class="block text-sm font-semibold text-gray-700 mb-3">
                                Penulis <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="penulis" name="penulis" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400 disabled:bg-gray-100 disabled:text-gray-600 disabled:cursor-not-allowed"
                                   placeholder="Nama penulis">
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

                    <!-- Thumbnail -->
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

                    <!-- Lampiran -->
                    <div>
                        <label for="lampiran" class="block text-sm font-semibold text-gray-700 mb-3">
                            Lampiran <span id="lampiranRequired" class="text-red-500">*</span>
                        </label>
                        <input type="file" id="lampiran" name="lampiran[]" multiple accept=".pdf"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400">
                        <p class="text-xs text-gray-500 mt-1">
                            Format: PDF saja. Max: 10MB per file
                            <span id="lampiranNote" class="text-blue-600 font-medium">(Wajib untuk Pengumuman)</span>
                        </p>
                        <!-- Current Attachments Display -->
                        <div id="currentAttachments" class="mt-3 hidden">
                            <p class="text-sm font-medium text-gray-700 mb-2">File saat ini:</p>
                            <div id="attachmentsList" class="space-y-2">
                                <!-- Attachments will be populated here -->
                            </div>
                        </div>
                    </div>

                    <!-- Tanggal Publish dan Berakhir -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="tanggal_publish" class="block text-sm font-semibold text-gray-700 mb-3">
                                Tanggal Publish
                            </label>
                            <input type="datetime-local" id="tanggal_publish" name="tanggal_publish"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400">
                        </div>
                        <div>
                            <label for="tanggal_berakhir" class="block text-sm font-semibold text-gray-700 mb-3">
                                Tanggal Berakhir
                            </label>
                            <input type="datetime-local" id="tanggal_berakhir" name="tanggal_berakhir"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400">
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-center">
                            <input type="checkbox" id="is_featured" name="is_featured" value="1"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="is_featured" class="ml-2 block text-sm text-gray-700">
                                Featured (Tampil di halaman utama)
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="is_pinned" name="is_pinned" value="1"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="is_pinned" class="ml-2 block text-sm text-gray-700">
                                Pinned (Selalu tampil di atas)
                            </label>
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
// Global variables
let currentEditId = null;
let editor = null;
let informasiData = [];
let currentPage = 1;
let totalPages = 1;
let totalData = 0;
let currentSearch = '';

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM Content Loaded - Starting initialization');

    initializeForm();
    initializeRichTextEditor();
    loadInformasiData();
    console.log('✅ Initialization completed');
});

// Initialize form
function initializeForm() {
    const form = document.getElementById('informasiForm');
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
        console.log('✅ Form event listener added');
    }

    // Initialize search input with real-time filtering
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function(e) {
            currentSearch = e.target.value.trim();

            // If search is empty, load all data immediately
            if (currentSearch === '') {
                loadInformasiData(1);
            } else {
                // Only search if there's actual content
                loadInformasiData(1);
            }
        }, 300));
        console.log('✅ Search input event listener added');
    }

    // Initialize filter dropdowns with real-time filtering
    const filterJenis = document.getElementById('filterJenis');
    const filterStatus = document.getElementById('filterStatus');
    const filterSpecial = document.getElementById('filterSpecial');

    if (filterJenis) {
        filterJenis.addEventListener('change', function() {
            loadInformasiData(1); // Reset to page 1 when filtering
        });
        console.log('✅ Filter Jenis event listener added');
    }

    if (filterStatus) {
        filterStatus.addEventListener('change', function() {
            loadInformasiData(1); // Reset to page 1 when filtering
        });
        console.log('✅ Filter Status event listener added');
    }

    if (filterSpecial) {
        filterSpecial.addEventListener('change', function() {
            loadInformasiData(1); // Reset to page 1 when filtering
        });
        console.log('✅ Filter Special event listener added');
    }
}

// Initialize rich text editor
function initializeRichTextEditor() {
    const editorElement = document.getElementById('editor');
    if (editorElement) {
        // Initialize toolbar buttons
        const toolbarButtons = document.querySelectorAll('.ql-bold, .ql-italic, .ql-underline, .ql-list, .ql-align, .ql-indent');

        toolbarButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const command = this.classList[0].replace('ql-', '');
                const value = this.getAttribute('value') || null;

                if (command === 'list') {
                    document.execCommand('insertOrderedList', false, null);
                } else if (command === 'align') {
                    document.execCommand('justify' + (value || 'Left'), false, null);
                } else if (command === 'indent') {
                    if (value === '+1') {
                        document.execCommand('indent', false, null);
                    } else {
                        document.execCommand('outdent', false, null);
                    }
                } else {
                    document.execCommand(command, false, null);
                }

                // Update hidden input
                updateHiddenContent();
            });
        });

        // Update hidden input when content changes
        editorElement.addEventListener('input', updateHiddenContent);
        editorElement.addEventListener('paste', function(e) {
            setTimeout(updateHiddenContent, 100);
        });

        console.log('✅ Rich text editor initialized');
    }
}

// Update hidden content input
function updateHiddenContent() {
    const editor = document.getElementById('editor');
    const hiddenInput = document.getElementById('konten');
    if (editor && hiddenInput) {
        hiddenInput.value = editor.innerHTML;
    }
}

// Toggle pengumuman fields
function togglePengumumanFields() {
    const jenis = document.getElementById('jenis').value;
    const pengumumanFields = document.getElementById('pengumumanFields');
    const nomorSurat = document.getElementById('nomor_surat');
    const tanggalSurat = document.getElementById('tanggal_surat');
    const penulisField = document.getElementById('penulis');
    const penulisLabel = document.querySelector('label[for="penulis"]');
    const lampiranField = document.getElementById('lampiran');
    const lampiranRequired = document.getElementById('lampiranRequired');
    const lampiranNote = document.getElementById('lampiranNote');

    if (jenis === 'pengumuman') {
        // Show pengumuman fields
        pengumumanFields.classList.remove('hidden');
        nomorSurat.required = true;
        tanggalSurat.required = true;

        // Lampiran wajib untuk pengumuman
        lampiranField.required = true;
        lampiranRequired.style.display = 'inline';
        lampiranNote.textContent = '(Wajib untuk Pengumuman)';
        lampiranNote.className = 'text-red-600 font-medium';

        // Disable penulis field and set to current user
        penulisField.disabled = true;
        @if(auth()->guard('pegawai')->check())
            penulisField.value = '{{ auth()->guard("pegawai")->user()->nama_lengkap ?? auth()->guard("pegawai")->user()->name ?? "Pegawai" }}';
        @else
            penulisField.value = '{{ auth()->user()->name ?? "Administrator" }}';
        @endif
        penulisField.classList.add('bg-gray-100', 'text-gray-600');
        penulisLabel.innerHTML = 'Penulis <span class="text-gray-500">(Otomatis dari pegawai login)</span>';
    } else {
        // Hide pengumuman fields
        pengumumanFields.classList.add('hidden');
        nomorSurat.required = false;
        tanggalSurat.required = false;
        nomorSurat.value = '';
        tanggalSurat.value = '';

        // Lampiran tidak wajib untuk berita
        lampiranField.required = false;
        lampiranRequired.style.display = 'none';
        lampiranNote.textContent = '(Opsional untuk Berita)';
        lampiranNote.className = 'text-blue-600 font-medium';

        // Enable penulis field for berita
        penulisField.disabled = false;
        penulisField.value = '';
        penulisField.classList.remove('bg-gray-100', 'text-gray-600');
        penulisLabel.innerHTML = 'Penulis <span class="text-red-500">*</span>';
    }
}

// Generate nomor surat
async function generateNomorSurat() {
    try {
        const response = await fetch('/admin-universitas/informasi/generate-nomor-surat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                unit: 'KEU',
                tahun: new Date().getFullYear()
            })
        });

        const result = await response.json();
        if (result.success) {
            document.getElementById('nomor_surat').value = result.nomor_surat;
        } else {
            showInformasiError('Gagal generate nomor surat: ' + result.message);
        }
    } catch (error) {
        console.error('Error generating nomor surat:', error);
        showInformasiError('Terjadi kesalahan saat generate nomor surat');
    }
}

// Load informasi data (legacy function - now uses pagination)
async function loadInformasiData(page = 1) {
    try {
        // Show loading animation
        showTableLoading();

        const params = new URLSearchParams({
            page: page,
            per_page: 10,
            search: currentSearch,
            jenis: document.getElementById('filterJenis')?.value || '',
            status: document.getElementById('filterStatus')?.value || '',
            special: document.getElementById('filterSpecial')?.value || ''
        });

        const response = await fetch(`/admin-universitas/informasi/data?${params}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (response.ok) {
            const result = await response.json();
            console.log('API Response:', result);

            if (result.success) {
                // Store data globally for edit functionality
                informasiData = result.data;
                window.currentInformasiData = result.data;
                console.log('Data loaded:', result.data.length, 'items');
                console.log('Pagination info:', {
                    current_page: result.current_page,
                    last_page: result.last_page,
                    total: result.total
                });

                // Check if pagination should be shown
                if (result.last_page > 1) {
                    console.log('Should show pagination - last_page > 1');
                } else {
                    console.log('Should hide pagination - last_page <= 1');
                }

                // Add delay for smooth animation
                setTimeout(() => {
                    displayInformasiData();
                    updatePagination(result);
                    hideTableLoading();
                }, 200);
            } else {
                hideTableLoading();
                showInformasiError('Gagal memuat data: ' + result.message);
            }
        } else {
            hideTableLoading();
            showInformasiError('Gagal memuat data informasi');
        }
    } catch (error) {
        hideTableLoading();
        console.error('Error loading informasi data:', error);
        showInformasiError('Terjadi kesalahan saat memuat data');
    }
}

// Display informasi data
function displayInformasiData() {
    const tbody = document.getElementById('informasiTableBody');

    if (!tbody) {
        console.error('❌ Table body not found!');
        return;
    }

    // Add fade-in animation class
    tbody.classList.add('fade-in');

    if (informasiData.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                    <div class="flex flex-col items-center">
                        <i data-lucide="file-text" class="h-10 w-10 text-gray-300 mb-3"></i>
                        <p class="text-base font-medium">Belum ada data</p>
                        <p class="text-sm">Data berita dan pengumuman akan ditampilkan di sini</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = informasiData.map((item, index) => `
        <tr class="hover:bg-gray-50 transition-all duration-200 animate-fade-in" style="animation-delay: ${index * 50}ms">
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-center">
                ${index + 1}
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-center">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${
                    item.jenis === 'berita' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'
                }">
                    ${item.jenis === 'berita' ? 'Berita' : 'Pengumuman'}
                </span>
            </td>
            <td class="px-4 py-3">
                <div class="text-sm font-medium text-gray-900" title="${item.judul}">
                    ${item.judul}
                </div>
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-center">
                <div class="text-sm text-gray-900">
                    ${item.nomor_surat || '-'}
                </div>
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${
                    item.status === 'published' ? 'bg-green-100 text-green-800' :
                    item.status === 'draft' ? 'bg-yellow-100 text-yellow-800' :
                    'bg-red-100 text-red-800'
                }">
                    ${item.status === 'published' ? 'Published' :
                      item.status === 'draft' ? 'Draft' : 'Archived'}
                </span>
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-center">
                <div class="flex space-x-1">
                    ${item.is_featured ? '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Featured</span>' : ''}
                    ${item.is_pinned ? '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Pinned</span>' : ''}
                    ${!item.is_featured && !item.is_pinned ? '<span class="text-gray-400 text-xs">-</span>' : ''}
                </div>
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                ${formatDate(item.created_at)}
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-center">
                ${item.lampiran && item.lampiran.length > 0 ?
                    item.lampiran.map((file, index) =>
                        `<a href="/lampiran/${file.path}" target="_blank" class="inline-flex items-center px-4 py-2 text-xs font-medium text-white bg-gradient-to-r from-purple-600 to-indigo-600 rounded hover:from-purple-700 hover:to-indigo-700 transition-all duration-200 mr-1" title="Buka ${file.name}"><i data-lucide="paperclip" class="h-3 w-3 mr-1"></i>Lihat Dokumen${item.lampiran.length > 1 ? ' ' + (index + 1) : ''}</a>`
                    ).join('')
                    : '<span class="text-gray-400 text-xs">-</span>'}
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-center">
                <button onclick="editInformasi(${item.id})"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-cyan-600 rounded-lg hover:from-blue-700 hover:to-cyan-700 transition-all duration-200 shadow-md hover:shadow-lg mr-2"
                        title="Edit">
                    <i data-lucide="edit" class="h-4 w-4 mr-2"></i>
                    Edit
                </button>
                <button onclick="deleteInformasi(${item.id})"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-red-600 to-pink-600 rounded-lg hover:from-red-700 hover:to-pink-700 transition-all duration-200 shadow-md hover:shadow-lg"
                        title="Hapus">
                    <i data-lucide="trash-2" class="h-4 w-4 mr-2"></i>
                    Hapus
                </button>
            </td>
        </tr>
    `).join('');

    // Reinitialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}


// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Open modal
function openModal() {
    currentEditId = null;
    document.getElementById('modalTitle').textContent = 'Tambah Informasi';
    document.getElementById('informasiForm').reset();
    document.getElementById('editId').value = '';
    document.getElementById('editor').innerHTML = '';
    document.getElementById('konten').value = '';
    document.getElementById('pengumumanFields').classList.add('hidden');

    // Reset pengumuman fields
    const nomorSuratField = document.getElementById('nomor_surat');
    const tanggalSuratField = document.getElementById('tanggal_surat');
    if (nomorSuratField) nomorSuratField.value = '';
    if (tanggalSuratField) tanggalSuratField.value = '';

    // Hide current file displays
    document.getElementById('currentThumbnail').classList.add('hidden');
    document.getElementById('currentAttachments').classList.add('hidden');

    const modal = document.getElementById('informasiModal');
    const modalContent = document.getElementById('modalContent');

    modal.classList.remove('hidden');

    // Trigger animation
    setTimeout(() => {
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
}

// Close modal
function closeModal() {
    const modal = document.getElementById('informasiModal');
    const modalContent = document.getElementById('modalContent');

    // Trigger close animation
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
        currentEditId = null;
        resetForm();
    }, 300);
}

function resetForm() {
    document.getElementById('informasiForm').reset();
    document.getElementById('editId').value = '';
    document.getElementById('modalTitle').textContent = 'Tambah Informasi';
    document.getElementById('submitText').textContent = 'Simpan';

    // Clear rich text editor
    if (document.getElementById('editor')) {
        document.getElementById('editor').innerHTML = '';
    }

    // Hide current file displays
    document.getElementById('currentThumbnail').classList.add('hidden');
    document.getElementById('currentAttachments').classList.add('hidden');

    // Reset pengumuman fields
    const nomorSuratField = document.getElementById('nomor_surat');
    const tanggalSuratField = document.getElementById('tanggal_surat');
    if (nomorSuratField) nomorSuratField.value = '';
    if (tanggalSuratField) tanggalSuratField.value = '';

    // Reset toggle fields
    togglePengumumanFields();
}

// Display current files in edit mode
function displayCurrentFiles(item) {
    // Display thumbnail
    if (item.thumbnail) {
        const thumbnailDiv = document.getElementById('currentThumbnail');
        const thumbnailPreview = document.getElementById('thumbnailPreview');
        const thumbnailName = document.getElementById('thumbnailName');
        const thumbnailDownload = document.getElementById('thumbnailDownload');

        // Extract filename from path
        const filename = item.thumbnail.split('/').pop();

        // Use the same route as lampiran for thumbnail access
        const thumbnailUrl = `/lampiran/${filename}`;

        thumbnailPreview.src = thumbnailUrl;
        thumbnailName.textContent = filename;
        thumbnailDownload.href = thumbnailUrl;

        thumbnailDiv.classList.remove('hidden');
    } else {
        document.getElementById('currentThumbnail').classList.add('hidden');
    }

    // Display attachments
    if (item.lampiran && item.lampiran.length > 0) {
        const attachmentsDiv = document.getElementById('currentAttachments');
        const attachmentsList = document.getElementById('attachmentsList');

        attachmentsList.innerHTML = '';

        item.lampiran.forEach((file, index) => {
            const attachmentItem = document.createElement('div');
            attachmentItem.className = 'flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border';

            // Get file extension for icon
            const fileExt = file.name.split('.').pop().toLowerCase();
            let iconClass = 'file';
            if (fileExt === 'pdf') iconClass = 'file-text';
            else if (['doc', 'docx'].includes(fileExt)) iconClass = 'file-text';

            attachmentItem.innerHTML = `
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 bg-gray-200 rounded-lg flex items-center justify-center">
                        <i data-lucide="${iconClass}" class="h-5 w-5 text-gray-600"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">${file.name}</p>
                    <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
                </div>
                <a href="/lampiran/${file.path}" target="_blank"
                   class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i data-lucide="download" class="h-3 w-3 mr-1"></i>
                    Download
                </a>
            `;

            attachmentsList.appendChild(attachmentItem);
        });

        attachmentsDiv.classList.remove('hidden');

        // Re-initialize Lucide icons for new elements
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    } else {
        document.getElementById('currentAttachments').classList.add('hidden');
    }
}

// Format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Edit informasi
function editInformasi(id) {
    const item = informasiData.find(item => item.id === id);
    if (!item) return;

    // Debug: Log item data
    console.log('Edit item data:', item);
    console.log('Penulis:', item.penulis);
    console.log('Lampiran:', item.lampiran);
    console.log('Thumbnail:', item.thumbnail);
    console.log('Tanggal Publish:', item.tanggal_publish);
    console.log('Tanggal Surat:', item.tanggal_surat);
    console.log('Nomor Surat:', item.nomor_surat);
    console.log('Jenis:', item.jenis);

    currentEditId = id;
    document.getElementById('modalTitle').textContent = 'Edit Informasi';
    document.getElementById('editId').value = item.id;
    document.getElementById('jenis').value = item.jenis;
    document.getElementById('judul').value = item.judul;
    document.getElementById('editor').innerHTML = item.konten;
    document.getElementById('konten').value = item.konten;
    document.getElementById('status').value = item.status;

    // Apply toggle first to show/hide pengumuman fields
    togglePengumumanFields();

    // Add small delay to ensure modal is fully rendered
    setTimeout(() => {
    // Check if elements exist before setting values
    const penulisField = document.getElementById('penulis');
    const tagsField = document.getElementById('tags');
    const tanggalField = document.getElementById('tanggal_publish');
    const tanggalBerakhirField = document.getElementById('tanggal_berakhir');

    console.log('Penulis field found:', penulisField);
    console.log('Tags field found:', tagsField);
    console.log('Tanggal field found:', tanggalField);
    console.log('Tanggal Berakhir field found:', tanggalBerakhirField);

    if (penulisField) {
        penulisField.value = item.penulis || '';
        console.log('Penulis value set to:', penulisField.value);
    }

    if (tagsField) {
        tagsField.value = item.tags ? item.tags.join(', ') : '';
        console.log('Tags value set to:', tagsField.value);
    }

    // Handle tanggal_publish format
    if (tanggalField && item.tanggal_publish) {
        // Convert from "2025-09-07T17:41:00.000000Z" to "2025-09-07T17:41"
        const dateStr = item.tanggal_publish.replace('T', 'T').substring(0, 16);
        tanggalField.value = dateStr;
        console.log('Tanggal value set to:', tanggalField.value);
    } else if (tanggalField) {
        tanggalField.value = '';
    }

    // Handle tanggal_berakhir format
    if (tanggalBerakhirField && item.tanggal_berakhir) {
        // Convert from "2025-09-07T17:41:00.000000Z" to "2025-09-07T17:41"
        const dateStr = item.tanggal_berakhir.replace('T', 'T').substring(0, 16);
        tanggalBerakhirField.value = dateStr;
        console.log('Tanggal Berakhir value set to:', tanggalBerakhirField.value);
    } else if (tanggalBerakhirField) {
        tanggalBerakhirField.value = '';
    }

    document.getElementById('is_featured').checked = item.is_featured;
    document.getElementById('is_pinned').checked = item.is_pinned;

    // Handle pengumuman fields
    if (item.jenis === 'pengumuman') {
        console.log('Processing pengumuman fields...');
        const nomorSuratField = document.getElementById('nomor_surat');
        const tanggalSuratField = document.getElementById('tanggal_surat');

        console.log('Nomor Surat field found:', nomorSuratField);
        console.log('Tanggal Surat field found:', tanggalSuratField);

        if (nomorSuratField) {
            nomorSuratField.value = item.nomor_surat || '';
            console.log('Nomor Surat value set to:', nomorSuratField.value);
        }

        if (tanggalSuratField) {
            console.log('Tanggal Surat raw data:', item.tanggal_surat);
            // Handle tanggal_surat format
            if (item.tanggal_surat) {
                // Convert from "2025-09-07T00:00:00.000000Z" to "2025-09-07" (date format)
                const dateStr = item.tanggal_surat.substring(0, 10);
                tanggalSuratField.value = dateStr;
                console.log('Tanggal Surat value set to:', tanggalSuratField.value);
            } else {
                tanggalSuratField.value = '';
                console.log('Tanggal Surat is empty, field cleared');
            }
        }
    } else {
        console.log('Not a pengumuman, skipping pengumuman fields');
    }

    // Display current files
    displayCurrentFiles(item);
    }, 100); // End setTimeout

    const modal = document.getElementById('informasiModal');
    const modalContent = document.getElementById('modalContent');

    modal.classList.remove('hidden');

    // Trigger animation
    setTimeout(() => {
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
}

// Delete informasi
function deleteInformasi(id) {
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
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin-universitas/informasi/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ _method: 'DELETE' })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    loadInformasiData();
                    showInformasiSuccess(result.message);
                } else {
                    showInformasiError(result.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showInformasiError('Terjadi kesalahan saat menghapus data');
            });
        }
    });
}

// Handle form submission
function handleFormSubmit(event) {
    event.preventDefault();

    // Update hidden content before submit
    updateHiddenContent();

    const formData = new FormData(event.target);
    const isEdit = currentEditId !== null;
    const url = isEdit ? `/admin-universitas/informasi/${currentEditId}` : '/admin-universitas/informasi';

    if (isEdit) {
        formData.append('_method', 'PUT');
    }

    showSubmitLoading(true);

    fetch(url, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(result => {
        if (result.success) {
            closeModal();
            loadInformasiData();
            showInformasiSuccess(result.message);
        } else {
            // Handle validation errors
            if (result.errors) {
                let errorMessage = 'Validasi gagal:\n';
                for (const field in result.errors) {
                    errorMessage += `• ${result.errors[field][0]}\n`;
                }
                showInformasiError(errorMessage);
            } else {
                showInformasiError(result.message);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showInformasiError('Terjadi kesalahan saat menyimpan data');
    })
    .finally(() => {
        showSubmitLoading(false);
    });
}

// Show submit loading
function showSubmitLoading(show) {
    const text = document.getElementById('submitText');
    const loader = document.getElementById('submitLoader');

    if (show) {
        text.textContent = 'Menyimpan...';
        loader.classList.remove('hidden');
    } else {
        text.textContent = 'Simpan';
        loader.classList.add('hidden');
    }
}

// Show success message
function showInformasiSuccess(message) {
    Swal.fire({
        title: 'Berhasil!',
        text: message,
        icon: 'success',
        confirmButtonText: 'OK',
        customClass: {
            popup: 'bg-gray-800 text-white',
            title: 'text-white',
            content: 'text-gray-300',
            confirmButton: 'bg-green-600 hover:bg-green-700 text-white'
        }
    });
}

// Show error message
function showInformasiError(message) {
    Swal.fire({
        title: 'Error!',
        text: message,
        icon: 'error',
        confirmButtonText: 'OK',
        customClass: {
            popup: 'bg-gray-800 text-white',
            title: 'text-white',
            content: 'text-gray-300',
            confirmButton: 'bg-red-600 hover:bg-red-700 text-white'
        }
    });
}

// Loading animation functions
function showTableLoading() {
    const tbody = document.getElementById('informasiTableBody');
    if (tbody) {
        tbody.classList.add('table-loading');
        tbody.classList.remove('table-loaded');
    }
}

function hideTableLoading() {
    const tbody = document.getElementById('informasiTableBody');
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

    console.log('updatePagination called with:', {
        currentPage,
        totalPages,
        totalData
    });

    const container = document.getElementById('paginationContainer');
    const pageNumbers = document.getElementById('pageNumbers');
    const prevBtn = document.getElementById('prevPageBtn');
    const nextBtn = document.getElementById('nextPageBtn');

    console.log('Pagination elements found:', {
        container: !!container,
        pageNumbers: !!pageNumbers,
        prevBtn: !!prevBtn,
        nextBtn: !!nextBtn
    });

    // Show/hide pagination
    if (totalPages > 1) {
        console.log('Showing pagination - totalPages > 1');
        container.classList.add('show');
    } else {
        console.log('Hiding pagination - totalPages <= 1');
        container.classList.remove('show');
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
    loadInformasiData(page);
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

</script>
@endpush
@endsection
