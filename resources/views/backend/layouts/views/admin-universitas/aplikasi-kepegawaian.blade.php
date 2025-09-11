@extends('backend.layouts.roles.admin-universitas.app')

@section('title', 'Aplikasi Kepegawaian')

@push('styles')
<style>
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
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
                    Aplikasi Kepegawaian
                </h1>
                <p class="text-lg text-black sm:text-xl">
                    Kelola daftar aplikasi kepegawaian yang tersedia
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="relative z-10 -mt-8 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-full pt-4 mt-4">
            <!-- Add Button -->
            <div class="mb-6 flex justify-end">
                <button onclick="openModal()"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:from-indigo-700 hover:to-purple-700 focus:from-indigo-700 focus:to-purple-700 active:from-indigo-900 active:to-purple-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg hover:shadow-xl">
                    <i data-lucide="plus" class="h-5 w-5 mr-2"></i>
                    Tambah Aplikasi
                </button>
            </div>

            <!-- Data Table -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden ">
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Aplikasi</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sumber</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Link</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="aplikasiTableBody" class="bg-white divide-y divide-gray-200">
                            <!-- Data will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="aplikasiModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-2xl rounded-2xl bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-6 border-b border-gray-200">
                <h3 id="modalTitle" class="text-xl font-bold text-gray-900">Tambah Aplikasi</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="x" class="h-6 w-6"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="aplikasiForm" class="mt-6">
                <input type="hidden" id="editId" name="id">

                <div class="space-y-6">
                    <!-- Nama Aplikasi -->
                    <div>
                        <label for="nama_aplikasi" class="block text-sm font-semibold text-gray-700 mb-3">
                            Nama Aplikasi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama_aplikasi" name="nama_aplikasi" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                               placeholder="Masukkan nama aplikasi">
                    </div>

                    <!-- Sumber -->
                    <div>
                        <label for="sumber" class="block text-sm font-semibold text-gray-700 mb-3">
                            Sumber <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="sumber" name="sumber" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                               placeholder="Masukkan sumber aplikasi">
                    </div>

                    <!-- Keterangan -->
                    <div>
                        <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-3">
                            Keterangan <span class="text-red-500">*</span>
                        </label>
                        <textarea id="keterangan" name="keterangan" rows="3" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                                  placeholder="Masukkan keterangan aplikasi"></textarea>
                    </div>

                    <!-- Link -->
                    <div>
                        <label for="link" class="block text-sm font-semibold text-gray-700 mb-3">
                            Link Aplikasi <span class="text-red-500">*</span>
                        </label>
                        <input type="url" id="link" name="link" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                               placeholder="https://example.com">
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-3">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
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
@endsection

@push('scripts')
<script>
// Global variables
let currentEditId = null;
let aplikasiData = [];

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

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('aplikasiTableBody');
    if (tbody) {
        loadAplikasiDataFromServer();
    }
    initializeForm();
});

// Load data from server using database
async function loadAplikasiDataFromServer() {
    try {
        const response = await fetch('/admin-universitas/aplikasi-kepegawaian/data', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (response.ok) {
            const result = await response.json();
            if (result.success && result.data) {
                aplikasiData = result.data;
            } else {
                // No data available
                aplikasiData = [];
            }
        } else {
            // Server error
            aplikasiData = [];
        }
    } catch (error) {
        // Error loading data
        aplikasiData = [];
    }

    // Load data to table
    loadAplikasiData();
}

// Load data to table
function loadAplikasiData() {
    const tbody = document.getElementById('aplikasiTableBody');
    if (!tbody) {
        return;
    }

    if (aplikasiData.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                    <div class="flex flex-col items-center">
                        <i data-lucide="file-text" class="h-12 w-12 text-gray-300 mb-4"></i>
                        <p class="text-lg font-medium">Belum ada data</p>
                        <p class="text-sm">Data aplikasi kepegawaian akan ditampilkan di sini</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = aplikasiData.map((item, index) => `
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${index + 1}
            </td>
            <td class="px-6 py-4">
                <div class="text-sm font-medium text-gray-900 break-words max-w-xs">${escapeHtml(item.nama_aplikasi)}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${escapeHtml(item.sumber)}</div>
            </td>
            <td class="px-6 py-4">
                <div class="text-sm text-gray-900 break-words max-w-xs" title="${escapeHtml(item.keterangan)}">${escapeHtml(item.keterangan)}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <a href="${escapeHtml(item.link)}" target="_blank"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-md hover:shadow-lg">
                    <i data-lucide="external-link" class="h-4 w-4 mr-2"></i>
                    Buka Aplikasi
                </a>
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
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button onclick="editAplikasi(${item.id})"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-cyan-600 rounded-lg hover:from-blue-700 hover:to-cyan-700 transition-all duration-200 shadow-md hover:shadow-lg mr-2"
                        title="Edit">
                    <i data-lucide="edit" class="h-4 w-4 mr-2"></i>
                    Edit
                </button>
                <button onclick="deleteAplikasi(${item.id})"
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

// Initialize form
function initializeForm() {
    const form = document.getElementById('aplikasiForm');
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    }
}

// Handle form submission
function handleFormSubmit(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData.entries());

    const isEdit = currentEditId !== null;
    const url = isEdit ? `/admin-universitas/aplikasi-kepegawaian/${currentEditId}` : '/admin-universitas/aplikasi-kepegawaian';
    const method = isEdit ? 'PUT' : 'POST';

    if (isEdit) {
        data._method = 'PUT';
    }

    showSubmitLoading(true);

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data),
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            if (currentEditId) {
                // Update existing data
                const index = aplikasiData.findIndex(item => item.id === currentEditId);
                if (index !== -1) {
                    aplikasiData[index] = {
                        ...aplikasiData[index],
                        nama_aplikasi: data.nama_aplikasi,
                        sumber: data.sumber,
                        keterangan: data.keterangan,
                        link: data.link,
                        status: data.status
                    };
                }
            } else {
                // Add new data
                aplikasiData.push(result.data);
            }

            // Reload data from server to get fresh data
            loadAplikasiDataFromServer().then(() => {
                closeModal();
                showSuccess(result.message);
            });
        } else {
            showError(result.message);
        }
    })
    .catch(error => {
        // Error handling
        showError('Terjadi kesalahan saat menyimpan data');
    })
    .finally(() => {
        showSubmitLoading(false);
    });
}

// Open modal
function openModal() {
    currentEditId = null;
    document.getElementById('modalTitle').textContent = 'Tambah Aplikasi';
    document.getElementById('aplikasiForm').reset();
    document.getElementById('editId').value = '';
    document.getElementById('aplikasiModal').classList.remove('hidden');
}

// Close modal
function closeModal() {
    document.getElementById('aplikasiModal').classList.add('hidden');
    currentEditId = null;
}

// Edit aplikasi
function editAplikasi(id) {
    const item = aplikasiData.find(item => item.id === id);
    if (!item) return;

    currentEditId = id;
    document.getElementById('modalTitle').textContent = 'Edit Aplikasi';
    document.getElementById('editId').value = item.id;
    document.getElementById('nama_aplikasi').value = item.nama_aplikasi;
    document.getElementById('sumber').value = item.sumber;
    document.getElementById('keterangan').value = item.keterangan;
    document.getElementById('link').value = item.link;
    document.getElementById('status').value = item.status;

    document.getElementById('aplikasiModal').classList.remove('hidden');
}

// Delete aplikasi
function deleteAplikasi(id) {
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
            fetch(`/admin-universitas/aplikasi-kepegawaian/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ _method: 'DELETE' }),
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    // Remove from local data
                    aplikasiData = aplikasiData.filter(item => item.id !== id);
                    loadAplikasiData();
                    showSuccess(result.message);
                } else {
                    showError(result.message);
                }
            })
            .catch(error => {
                // Error handling
                showError('Terjadi kesalahan saat menghapus data');
            });
        }
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
function showSuccess(message) {
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
function showError(message) {
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
</script>
@endpush
