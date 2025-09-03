{{-- Action Buttons untuk Pegawai --}}
@if($usulan->exists)
    {{-- Check if usulan is in view-only status --}}
    @php
        $viewOnlyStatuses = [
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_SUDAH_DIKIRIM_KE_BKN,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_BKN,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS
        ];
        
        // Status yang dapat diedit (tidak view-only) - hanya status draft dan permintaan perbaikan
        $editableStatuses = [
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_DRAFT_USULAN,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN
        ];
        
        if (in_array($usulan->status_usulan, $editableStatuses)) {
            $isViewOnly = false;  // Dapat diedit
        } else {
            $isViewOnly = true;  // View-only untuk semua status lainnya
        }
    @endphp

    @if(!$isViewOnly)
        {{-- Action buttons hanya muncul jika bukan view-only --}}
        <div class="flex flex-wrap gap-4 mb-6">
            {{-- Simpan Usulan (Selalu Aktif jika Edit) --}}
            <button type="submit" name="action" value="simpan" 
                    class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
                <i data-lucide="save" class="w-4 h-4"></i>
                Simpan Usulan
            </button>

            {{-- Kirim Usulan Ke Kepegawaian Universitas --}}
            @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_DRAFT_USULAN || is_null($usulan->status_usulan))
                <button type="button" onclick="submitAction('kirim_ke_kepegawaian')"
                        class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    Kirim Usulan Ke Kepegawaian Universitas
                </button>
            @endif

            {{-- Kirim Usulan Perbaikan Ke Kepegawaian Universitas --}}
            @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS)
                <button type="button" onclick="submitAction('kirim_perbaikan_ke_kepegawaian')"
                        class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    Kirim Usulan Perbaikan Ke Kepegawaian Universitas
                </button>
            @endif

            {{-- Kirim Usulan Perbaikan Dari BKN ke Kepegawaian Universitas --}}
            @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN)
                <button type="button" onclick="submitAction('kirim_perbaikan_bkn_ke_kepegawaian')"
                        class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    Kirim Usulan Perbaikan Dari BKN ke Kepegawaian Universitas
                </button>
            @endif

            {{-- Kirim Usulan Perbaikan Ke BKN --}}
            @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN)
                <button type="button" onclick="submitAction('kirim_perbaikan_ke_bkn')"
                        class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    Kirim Usulan Perbaikan Ke BKN
                </button>
            @endif
        </div>
    @else
        {{-- View-only mode - tidak ada button yang ditampilkan --}}
    @endif

@else
    {{-- Usulan belum tersimpan di database --}}
    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
        <div class="flex items-start">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600 mr-3 mt-0.5"></i>
            <div>
                <h4 class="text-sm font-medium text-yellow-800">Usulan Belum Tersimpan</h4>
                <p class="text-sm text-yellow-700 mt-1">
                    Usulan ini belum tersimpan di database. Silakan simpan usulan terlebih dahulu sebelum melanjutkan.
                </p>
            </div>
        </div>
    </div>
@endif

<script>


// Action button configurations
const actionConfigs = {
    'kirim_ke_kepegawaian': {
        title: 'Kirim Usulan ke Kepegawaian Universitas',
        message: 'Apakah Anda yakin ingin mengirim usulan ini ke Kepegawaian Universitas? Usulan akan diverifikasi oleh tim kepegawaian.',
        icon: 'question',
        confirmColor: '#3b82f6',
        loadingText: 'Mengirim usulan ke Kepegawaian Universitas...'
    },
    'kirim_perbaikan_ke_kepegawaian': {
        title: 'Kirim Usulan Perbaikan',
        message: 'Apakah Anda yakin ingin mengirim usulan perbaikan ini ke Kepegawaian Universitas? Pastikan semua field telah diperbaiki.',
        icon: 'question',
        confirmColor: '#f59e0b',
        loadingText: 'Mengirim usulan perbaikan ke Kepegawaian Universitas...'
    },
    'kirim_perbaikan_bkn_ke_kepegawaian': {
        title: 'Kirim Usulan Perbaikan dari BKN',
        message: 'Apakah Anda yakin ingin mengirim usulan perbaikan dari BKN ini ke Kepegawaian Universitas? Pastikan semua field telah diperbaiki.',
        icon: 'question',
        confirmColor: '#8b5cf6',
        loadingText: 'Mengirim usulan perbaikan dari BKN ke Kepegawaian Universitas...'
    },
    'kirim_perbaikan_ke_bkn': {
        title: 'Kirim Usulan Perbaikan ke BKN',
        message: 'Apakah Anda yakin ingin mengirim usulan perbaikan ini ke BKN? Usulan akan diproses oleh tim BKN.',
        icon: 'question',
        confirmColor: '#3b82f6',
        loadingText: 'Mengirim usulan perbaikan ke BKN...'
    }
};

function submitAction(action) {
    try {
        // Check if SweetAlert2 is available
        if (typeof Swal === 'undefined') {
            if (confirm('Apakah Anda yakin ingin melanjutkan aksi ini?')) {
                processAction(action);
            }
            return;
        }
        
        const config = actionConfigs[action];
        if (!config) {
            // Use global showError if available, otherwise use fallback
            if (typeof window.showError === 'function') {
                window.showError('Aksi tidak dikenal');
            } else {
                alert('Aksi tidak dikenal');
            }
            return;
        }

        // Show confirmation dialog
        Swal.fire({
            title: config.title,
            text: config.message,
            icon: config.icon,
            showCancelButton: true,
            confirmButtonColor: config.confirmColor,
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: config.loadingText,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Process the action
                processAction(action);
            }
        }).catch((error) => {
            // Fallback to processAction directly
            processAction(action);
        });
        
    } catch (error) {
        // Fallback to processAction directly
        processAction(action);
    }
}

function processAction(action) {
    try {
        // Gunakan form utama yang sudah ada
        let actionForm = document.querySelector('form[action*="usulan-kepangkatan"]');
        let actionInput = document.getElementById('formAction');
        
        if (actionForm && actionInput) {
            // Set action dan submit form utama
            actionInput.value = action;
            
            // Close loading SweetAlert2 jika ada
            if (typeof Swal !== 'undefined') {
                Swal.close();
            }
            
            // Submit form utama
            actionForm.submit();
        } else {
            // Fallback: buat form dinamis jika form utama tidak ditemukan
            console.warn('Form utama tidak ditemukan, menggunakan fallback form dinamis');
            createDynamicForm(action);
        }
        
    } catch (error) {
        console.error('Error in processAction:', error);
        
        // Close loading if SweetAlert2 is available
        if (typeof Swal !== 'undefined') {
            Swal.close();
        }
        
        // Show error message
        if (typeof window.showError === 'function') {
            window.showError('Terjadi kesalahan saat memproses aksi. Silakan coba lagi.');
        } else {
            alert('Terjadi kesalahan saat memproses aksi. Silakan coba lagi.');
        }
    }
}

// Fallback function untuk membuat form dinamis jika diperlukan
function createDynamicForm(action) {
    try {
        // Remove existing form if any
        const existingForm = document.getElementById('actionForm');
        if (existingForm) {
            existingForm.remove();
        }
        
        // Create new form
        const actionForm = document.createElement('form');
        actionForm.id = 'actionForm';
        actionForm.action = '{{ route("pegawai-unmul.usulan-kepangkatan.update", $usulan) }}';
        actionForm.method = 'POST';
        actionForm.enctype = 'multipart/form-data';
        actionForm.style.display = 'none';
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        actionForm.appendChild(csrfInput);
        
        // Add method override
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        actionForm.appendChild(methodInput);
        
        // Add action input
        const actionValue = document.createElement('input');
        actionValue.type = 'hidden';
        actionValue.name = 'action';
        actionValue.value = action;
        actionForm.appendChild(actionValue);
        
        // Add pangkat_tujuan_id
        const pangkatInput = document.createElement('input');
        pangkatInput.type = 'hidden';
        pangkatInput.name = 'pangkat_tujuan_id';
        pangkatInput.value = '{{ $usulan->pangkat_tujuan_id ?? "" }}';
        actionForm.appendChild(pangkatInput);
        
        // Add form to body and submit
        document.body.appendChild(actionForm);
        actionForm.submit();
        
    } catch (error) {
        console.error('Error creating dynamic form:', error);
        alert('Terjadi kesalahan saat membuat form. Silakan coba lagi.');
    }
}

// Using global SweetAlert2 functions from show.blade.php
// showSuccess and showError are defined globally
</script>