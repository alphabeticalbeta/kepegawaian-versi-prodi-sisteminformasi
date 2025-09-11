@extends('backend.layouts.roles.admin-universitas.app')

@section('title', 'Visi dan Misi - Admin Universitas')

@push('styles')
<style>
/* Rich Text Editor Styles */
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

#editor[contenteditable="true"]:empty:before {
    content: "Masukkan konten visi atau misi...";
    color: #9CA3AF;
    font-style: italic;
}

/* Toolbar Button Styles */
#toolbar button {
    transition: all 0.2s ease;
}

#toolbar button:hover {
    background-color: #E5E7EB;
}

#toolbar button.active {
    background-color: #3B82F6;
    color: white;
}

/* Custom scrollbar for editor */
#editor::-webkit-scrollbar {
    width: 6px;
}

#editor::-webkit-scrollbar-track {
    background: #F3F4F6;
}

#editor::-webkit-scrollbar-thumb {
    background: #D1D5DB;
    border-radius: 3px;
}

#editor::-webkit-scrollbar-thumb:hover {
    background: #9CA3AF;
}
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <!-- Header Section -->
    <div class="relative overflow-hidden shadow-2xl">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative px-6 py-12 sm:px-8 sm:py-16">
            <div class="mx-auto max-w-4xl text-center">
                <div class="mb-6 flex justify-center">
                    <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo UNMUL" class="h-32 w-auto object-contain">
                </div>
                <h1 class="text-3xl font-bold tracking-tight text-black sm:text-4xl mb-4">
                    Visi dan Misi
                </h1>
                <p class="text-lg text-black sm:text-xl">
                    Kelola Visi dan Misi Universitas Mulawarman
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="relative z-10 -mt-8 px-6 sm:px-8">
        <div class="mx-auto max-w-7xl pt-5 mt-5">
            <!-- Data Table -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Table Header -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-900">Data Visi dan Misi</h2>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jenis
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Konten
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal Dibuat
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="visiMisiTableBody">
                            <!-- Data will be loaded here -->
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i data-lucide="file-text" class="h-12 w-12 text-gray-300 mb-4"></i>
                                        <p class="text-lg font-medium">Belum ada data</p>
                                        <p class="text-sm">Data visi dan misi akan ditampilkan di sini</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="visiMisiModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-2xl bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900" id="modalTitle">
                    Edit Visi dan Misi
                </h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="x" class="h-6 w-6"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="visiMisiForm" class="mt-6">
                <input type="hidden" id="editId" name="id">

                <div class="space-y-6">
                    <!-- Jenis -->
                    <div>
                        <label for="jenis" class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis <span class="text-red-500">*</span>
                        </label>
                        <!-- Select dropdown for create mode -->
                        <select id="jenis" name="jenis" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">Pilih Jenis</option>
                            <option value="visi">Visi</option>
                            <option value="misi">Misi</option>
                        </select>
                        <!-- Read-only display for edit mode -->
                        <div id="jenisDisplay" class="hidden w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg">
                            <span id="jenisText" class="text-gray-700 font-medium"></span>
                        </div>
                    </div>

                    <!-- Konten -->
                    <div>
                        <label for="konten" class="block text-sm font-medium text-gray-700 mb-2">
                            Konten <span class="text-red-500">*</span>
                        </label>
                        <div class="border border-gray-300 rounded-lg overflow-hidden">
                            <!-- Rich Text Editor Toolbar -->
                            <div id="toolbar" class="bg-gray-50 border-b border-gray-300 p-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <!-- Text Formatting -->
                                    <div class="flex items-center gap-1 border-r border-gray-300 pr-2">
                                        <button type="button" class="ql-bold p-1 hover:bg-gray-200 rounded" title="Bold">
                                            <i data-lucide="bold" class="h-4 w-4"></i>
                                        </button>
                                        <button type="button" class="ql-italic p-1 hover:bg-gray-200 rounded" title="Italic">
                                            <i data-lucide="italic" class="h-4 w-4"></i>
                                        </button>
                                        <button type="button" class="ql-underline p-1 hover:bg-gray-200 rounded" title="Underline">
                                            <i data-lucide="underline" class="h-4 w-4"></i>
                                        </button>
                                    </div>

                                    <!-- Lists -->
                                    <div class="flex items-center gap-1 border-r border-gray-300 pr-2">
                                        <button type="button" class="ql-list p-1 hover:bg-gray-200 rounded" value="ordered" title="Numbered List">
                                            <i data-lucide="list-ordered" class="h-4 w-4"></i>
                                        </button>
                                        <button type="button" class="ql-list p-1 hover:bg-gray-200 rounded" value="bullet" title="Bullet List">
                                            <i data-lucide="list" class="h-4 w-4"></i>
                                        </button>
                                    </div>

                                    <!-- Alignment -->
                                    <div class="flex items-center gap-1 border-r border-gray-300 pr-2">
                                        <button type="button" class="ql-align p-1 hover:bg-gray-200 rounded" value="" title="Align Left">
                                            <i data-lucide="align-left" class="h-4 w-4"></i>
                                        </button>
                                        <button type="button" class="ql-align p-1 hover:bg-gray-200 rounded" value="center" title="Align Center">
                                            <i data-lucide="align-center" class="h-4 w-4"></i>
                                        </button>
                                        <button type="button" class="ql-align p-1 hover:bg-gray-200 rounded" value="right" title="Align Right">
                                            <i data-lucide="align-right" class="h-4 w-4"></i>
                                        </button>
                                    </div>

                                    <!-- Indent -->
                                    <div class="flex items-center gap-1">
                                        <button type="button" class="ql-indent p-1 hover:bg-gray-200 rounded" value="-1" title="Decrease Indent">
                                            <i data-lucide="indent-decrease" class="h-4 w-4"></i>
                                        </button>
                                        <button type="button" class="ql-indent p-1 hover:bg-gray-200 rounded" value="+1" title="Increase Indent">
                                            <i data-lucide="indent-increase" class="h-4 w-4"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Editor Container -->
                            <div id="editor" class="min-h-[200px] p-4 focus:outline-none"
                                 style="font-family: 'Inter', sans-serif; line-height: 1.6;">
                            </div>
                        </div>

                        <!-- Hidden textarea for form submission -->
                        <textarea id="konten" name="konten" style="display: none;" required></textarea>

                        <!-- Help text -->
                        <p class="text-sm text-gray-500 mt-2">
                            <i data-lucide="info" class="h-4 w-4 inline mr-1"></i>
                            Gunakan toolbar di atas untuk memformat teks, membuat daftar bernomor, dan mengatur paragraf
                        </p>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">Pilih Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="tidak_aktif">Tidak Aktif</option>
                        </select>
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
                        <span id="submitBtnText">Simpan</span>
                        <i data-lucide="loader-2" class="h-4 w-4 animate-spin hidden ml-2" id="submitBtnLoader"></i>
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

// Data array (will be loaded from API)
let visiMisiData = [];

// Escape HTML function for XSS protection
function escapeHtml(text) {
    if (text === null || text === undefined) {
        return '';
    }
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('visiMisiTableBody');

    if (tbody) {
        loadVisiMisiDataFromServer();
    }

    initializeForm();
    initializeRichTextEditor();
});

// Load data from server using API
async function loadVisiMisiDataFromServer() {
    try {
        const response = await fetch('/admin-universitas/visi-misi/data', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            credentials: 'same-origin'
        });

        if (response.ok) {
            const result = await response.json();

            if (result.success && result.data) {
                visiMisiData = result.data;
            } else {
                visiMisiData = [];
            }
        } else {
            visiMisiData = [];
        }
    } catch (error) {
        visiMisiData = [];
    }

    // Load data to table
    loadVisiMisiData();
}

// Load data to table
function loadVisiMisiData() {
    const tbody = document.getElementById('visiMisiTableBody');

    if (!tbody) {
        return;
    }

    if (visiMisiData.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                    <div class="flex flex-col items-center">
                        <i data-lucide="file-text" class="h-12 w-12 text-gray-300 mb-4"></i>
                        <p class="text-lg font-medium">Belum ada data</p>
                        <p class="text-sm">Data visi dan misi akan ditampilkan di sini</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = visiMisiData.map((item, index) => `
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${index + 1}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${
                    item.jenis === 'visi'
                        ? 'bg-blue-100 text-blue-800'
                        : 'bg-green-100 text-green-800'
                }">
                    ${item.jenis === 'visi' ? 'Visi' : 'Misi'}
                </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-900 max-w-md">
                <div class="truncate" title="${escapeHtml(stripHtml(item.konten))}">
                    ${escapeHtml(stripHtml(item.konten).length > 100 ? stripHtml(item.konten).substring(0, 100) + '...' : stripHtml(item.konten))}
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${
                    item.status === 'aktif'
                        ? 'bg-green-100 text-green-800'
                        : 'bg-red-100 text-red-800'
                }">
                    ${item.status === 'aktif' ? 'Aktif' : 'Tidak Aktif'}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${formatDate(item.created_at)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button onclick="editVisiMisi(${escapeHtml(item.id)})"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-cyan-600 rounded-lg hover:from-blue-700 hover:to-cyan-700 transition-all duration-200 shadow-md hover:shadow-lg"
                        title="Edit">
                    <i data-lucide="edit" class="h-4 w-4 mr-2"></i>
                    Edit
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
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Strip HTML tags from content
function stripHtml(html) {
    const tmp = document.createElement('div');
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || '';
}

// Initialize form
function initializeForm() {
    const form = document.getElementById('visiMisiForm');
    form.addEventListener('submit', handleFormSubmit);
}

// Initialize Rich Text Editor
function initializeRichTextEditor() {
    const editorElement = document.getElementById('editor');
    const hiddenTextarea = document.getElementById('konten');

    // Make editor contenteditable
    editorElement.setAttribute('contenteditable', 'true');

    // Initialize toolbar buttons
    initializeToolbar();

    // Handle editor content changes
    editorElement.addEventListener('input', function() {
        // Update hidden textarea with HTML content
        hiddenTextarea.value = editorElement.innerHTML;

        // Update toolbar button states
        updateToolbarStates();
    });

    // Handle editor focus
    editorElement.addEventListener('focus', function() {
        updateToolbarStates();
    });

    // Handle editor blur
    editorElement.addEventListener('blur', function() {
        hiddenTextarea.value = editorElement.innerHTML;
    });
}

// Initialize toolbar buttons
function initializeToolbar() {
    const toolbar = document.getElementById('toolbar');

    // Bold button
    toolbar.querySelector('.ql-bold').addEventListener('click', function() {
        document.execCommand('bold');
        updateToolbarStates();
    });

    // Italic button
    toolbar.querySelector('.ql-italic').addEventListener('click', function() {
        document.execCommand('italic');
        updateToolbarStates();
    });

    // Underline button
    toolbar.querySelector('.ql-underline').addEventListener('click', function() {
        document.execCommand('underline');
        updateToolbarStates();
    });

    // Ordered list button
    toolbar.querySelector('.ql-list[value="ordered"]').addEventListener('click', function() {
        document.execCommand('insertOrderedList');
        updateToolbarStates();
    });

    // Bullet list button
    toolbar.querySelector('.ql-list[value="bullet"]').addEventListener('click', function() {
        document.execCommand('insertUnorderedList');
        updateToolbarStates();
    });

    // Alignment buttons
    toolbar.querySelector('.ql-align[value=""]').addEventListener('click', function() {
        document.execCommand('justifyLeft');
        updateToolbarStates();
    });

    toolbar.querySelector('.ql-align[value="center"]').addEventListener('click', function() {
        document.execCommand('justifyCenter');
        updateToolbarStates();
    });

    toolbar.querySelector('.ql-align[value="right"]').addEventListener('click', function() {
        document.execCommand('justifyRight');
        updateToolbarStates();
    });

    // Indent buttons
    toolbar.querySelector('.ql-indent[value="-1"]').addEventListener('click', function() {
        document.execCommand('outdent');
        updateToolbarStates();
    });

    toolbar.querySelector('.ql-indent[value="+1"]').addEventListener('click', function() {
        document.execCommand('indent');
        updateToolbarStates();
    });
}

// Update toolbar button states
function updateToolbarStates() {
    const toolbar = document.getElementById('toolbar');

    // Update bold button
    const boldBtn = toolbar.querySelector('.ql-bold');
    if (document.queryCommandState('bold')) {
        boldBtn.classList.add('active');
    } else {
        boldBtn.classList.remove('active');
    }

    // Update italic button
    const italicBtn = toolbar.querySelector('.ql-italic');
    if (document.queryCommandState('italic')) {
        italicBtn.classList.add('active');
    } else {
        italicBtn.classList.remove('active');
    }

    // Update underline button
    const underlineBtn = toolbar.querySelector('.ql-underline');
    if (document.queryCommandState('underline')) {
        underlineBtn.classList.add('active');
    } else {
        underlineBtn.classList.remove('active');
    }
}

// Handle form submit
function handleFormSubmit(e) {
    e.preventDefault();

    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);

    // Validate form data
    if (!data.jenis || !data.konten || !data.status) {
        showError('Semua field harus diisi!');
        return;
    }

    // Show SweetAlert when submit button is clicked
    showSubmitConfirmation(data);
}

// Process form submission after confirmation
async function processFormSubmission(data) {
    // Show loading
    showSubmitLoading(true);

    try {
        // Determine API endpoint and method
        let url, method;
        if (currentEditId) {
            url = `/admin-universitas/visi-misi/${currentEditId}`;
            method = 'PUT';
        } else {
            url = '/admin-universitas/visi-misi';
            method = 'POST';
        }

        // Add CSRF token
        data._token = '{{ csrf_token() }}';
        if (method === 'PUT') {
            data._method = 'PUT';
        }

        // Make API call
        const response = await fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: new URLSearchParams(data)
        });

        const result = await response.json();

        if (result.success) {
            // Reload data from API to get fresh data
            await loadVisiMisiDataFromServer();
            closeModal();
            showSuccess('Data berhasil disimpan');
        } else {
            showError('Gagal menyimpan data');
        }
    } catch (error) {
        showError('Terjadi kesalahan saat menyimpan data');
    } finally {
        showSubmitLoading(false);
    }
}

// Open create modal
function openCreateModal() {
    currentEditId = null;
    document.getElementById('modalTitle').textContent = 'Tambah Visi dan Misi';
    document.getElementById('submitBtnText').textContent = 'Simpan';

    // Reset form
    document.getElementById('visiMisiForm').reset();
    document.getElementById('editId').value = '';

    // Reset jenis field display to show select dropdown
    const jenisSelect = document.getElementById('jenis');
    const jenisDisplay = document.getElementById('jenisDisplay');

    jenisSelect.classList.remove('hidden');
    jenisDisplay.classList.add('hidden');

    // Reset editor
    const editorElement = document.getElementById('editor');
    const hiddenTextarea = document.getElementById('konten');
    editorElement.innerHTML = '';
    hiddenTextarea.value = '';

    // Open modal
    document.getElementById('visiMisiModal').classList.remove('hidden');

    // Focus jenis select
    setTimeout(() => {
        document.getElementById('jenis').focus();
    }, 100);
}

// Edit visi misi
function editVisiMisi(id) {
    const item = visiMisiData.find(item => item.id === id);

    if (!item) {
        return;
    }

    currentEditId = id;

    document.getElementById('modalTitle').textContent = 'Edit Visi dan Misi';
    document.getElementById('submitBtnText').textContent = 'Perbarui';
    document.getElementById('editId').value = item.id;
    document.getElementById('status').value = item.status;

    // Hide select dropdown and show read-only display for jenis
    const jenisSelect = document.getElementById('jenis');
    const jenisDisplay = document.getElementById('jenisDisplay');
    const jenisText = document.getElementById('jenisText');

    jenisSelect.classList.add('hidden');
    jenisDisplay.classList.remove('hidden');
    jenisText.textContent = item.jenis === 'visi' ? 'Visi' : 'Misi';

    // Set hidden value for form submission
    jenisSelect.value = item.jenis;

    // Set editor content
    const editorElement = document.getElementById('editor');
    const hiddenTextarea = document.getElementById('konten');

    // Convert plain text to HTML if needed
    let htmlContent = item.konten;
    if (!htmlContent.includes('<')) {
        // Convert plain text to HTML with line breaks
        htmlContent = htmlContent.replace(/\n/g, '<br>');
    }

    editorElement.innerHTML = htmlContent;
    hiddenTextarea.value = htmlContent;

    // Open modal
    document.getElementById('visiMisiModal').classList.remove('hidden');

    // Focus editor
    setTimeout(() => {
        editorElement.focus();
    }, 100);
}

// Delete visi misi
function deleteVisiMisi(id) {
    const item = visiMisiData.find(item => item.id === id);
    if (!item) return;

    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus ${escapeHtml(item.jenis === 'visi' ? 'Visi' : 'Misi')} ini?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
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
            // Remove item from data
            visiMisiData = visiMisiData.filter(item => item.id !== id);

            // Reload table
            loadVisiMisiData();

            showSuccess('Data berhasil dihapus!');
        }
    });
}


// Close modal
function closeModal() {
    document.getElementById('visiMisiModal').classList.add('hidden');
    currentEditId = null;

    // Reset editor
    const editorElement = document.getElementById('editor');
    const hiddenTextarea = document.getElementById('konten');
    editorElement.innerHTML = '';
    hiddenTextarea.value = '';

    // Reset jenis field display
    const jenisSelect = document.getElementById('jenis');
    const jenisDisplay = document.getElementById('jenisDisplay');

    jenisSelect.classList.remove('hidden');
    jenisDisplay.classList.add('hidden');

    // Reset form
    document.getElementById('visiMisiForm').reset();
}

// Show submit loading
function showSubmitLoading(show) {
    const btn = document.getElementById('submitBtn');
    const text = document.getElementById('submitBtnText');
    const loader = document.getElementById('submitBtnLoader');

    if (show) {
        btn.disabled = true;
        text.textContent = 'Menyimpan...';
        loader.classList.remove('hidden');
    } else {
        btn.disabled = false;
        text.textContent = 'Simpan';
        loader.classList.add('hidden');
    }
}

// Show submit confirmation
function showSubmitConfirmation(data) {
    const action = currentEditId ? 'memperbarui' : 'menyimpan';
    const jenis = data.jenis === 'visi' ? 'Visi' : 'Misi';

    Swal.fire({
        title: 'Konfirmasi',
        text: `Apakah Anda yakin ingin ${action} data ${escapeHtml(jenis)} ini?`,
        icon: 'question',
        confirmButtonText: 'Ya, Simpan',
        showCancelButton: true,
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'bg-gradient-to-br from-green-50 to-emerald-100',
            title: 'text-green-800',
            content: 'text-green-600',
            confirmButton: 'bg-green-600 hover:bg-green-700 text-white',
            cancelButton: 'bg-gray-500 hover:bg-gray-600 text-white'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Continue with form submission
            processFormSubmission(data);
        } else {
            // Hide loading and don't submit
            showSubmitLoading(false);
        }
    });
}

// Show success message
function showSuccess(message) {
    Swal.fire({
        title: 'Berhasil!',
        text: escapeHtml(message),
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
function showError(message) {
    Swal.fire({
        title: 'Error!',
        text: escapeHtml(message),
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
</script>
@endpush
@endsection
