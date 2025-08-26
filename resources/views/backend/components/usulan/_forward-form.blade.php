{{-- Forward Form Component --}}
<div id="forward-form-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="forward-form-title">
                    Forward Usulan
                </h3>
                <button type="button" 
                        onclick="closeForwardForm()"
                        class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <form id="forward-form" method="POST" action="{{ route((isset($config['routePrefix']) ? $config['routePrefix'] : 'admin-fakultas') . '.save-validation', $usulan->id) }}">
                @csrf
                <input type="hidden" name="action_type" id="forward-action-type" value="">
                
                <div class="mb-4">
                    <label for="forward-catatan" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan (opsional):
                    </label>
                    <textarea id="forward-catatan" 
                              name="catatan_umum" 
                              rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                </div>
                
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" 
                            onclick="closeForwardForm()"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                        Kirim
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showForwardForm(actionType, title) {
    document.getElementById('forward-action-type').value = actionType;
    document.getElementById('forward-form-title').textContent = title;
    document.getElementById('forward-form-modal').classList.remove('hidden');
}

function closeForwardForm() {
    document.getElementById('forward-form-modal').classList.add('hidden');
    document.getElementById('forward-catatan').value = '';
}

// Close modal when clicking outside
document.getElementById('forward-form-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeForwardForm();
    }
});

// Handle form submission
document.getElementById('forward-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const actionType = formData.get('action_type');
    const catatan = formData.get('catatan_umum');
    
    // Show loading state
    Swal.fire({
        title: 'Memproses...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch(this.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Berhasil!',
                text: data.message || 'Usulan berhasil diteruskan',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                title: 'Gagal!',
                text: data.message || 'Terjadi kesalahan',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Terjadi kesalahan saat memproses aksi',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
    
    closeForwardForm();
});
</script>
