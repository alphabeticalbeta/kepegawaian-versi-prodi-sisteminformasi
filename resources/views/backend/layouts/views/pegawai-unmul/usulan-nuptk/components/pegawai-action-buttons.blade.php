{{-- Action Buttons untuk Pegawai NUPTK --}}
@if($usulan->exists)
    {{-- Check if usulan is in view-only status --}}
    @php
        $viewOnlyStatuses = [
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_SUDAH_DIKIRIM_KE_SISTER,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_SISTER,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER
        ];
        
        // Status yang dapat diedit (tidak view-only) - hanya status draft dan permintaan perbaikan
        $editableStatuses = [
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_DRAFT_USULAN,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_TIM_SISTER
        ];
        
        if (in_array($usulan->status_usulan, $editableStatuses)) {
            $isViewOnly = false;  // Dapat diedit
        } else {
            $isViewOnly = true;  // View-only untuk semua status lainnya
        }
    @endphp

    @if(!$isViewOnly)
        {{-- Action buttons hanya muncul jika bukan view-only --}}
        <div class="flex flex-wrap gap-3 justify-start items-center">
            {{-- Simpan Usulan (Selalu Aktif jika Edit) --}}
            <button type="submit" name="action" value="simpan" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105 whitespace-nowrap">
                <i data-lucide="save" class="w-4 h-4"></i>
                Simpan Usulan
            </button>

            {{-- Kirim Usulan Ke Kepegawaian Universitas --}}
            @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_DRAFT_USULAN || is_null($usulan->status_usulan))
                <button type="button" 
                        onclick="submitAction('kirim_ke_kepegawaian')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105 whitespace-nowrap">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    Kirim ke Kepegawaian
                </button>
            @endif

            {{-- Kirim Usulan Perbaikan Ke Kepegawaian Universitas --}}
            @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS)
                <button type="button" onclick="submitAction('kirim_perbaikan_ke_kepegawaian')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105 whitespace-nowrap">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    Kirim Perbaikan
                </button>
            @endif

            {{-- Kirim Usulan Perbaikan Dari Tim Sister ke Kepegawaian Universitas --}}
            @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_TIM_SISTER)
                <button type="button" onclick="submitAction('kirim_perbaikan_tim_sister_ke_kepegawaian')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105 whitespace-nowrap">
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    Kirim dari Sister
                </button>
            @endif

            {{-- Kirim Usulan Perbaikan Ke Tim Sister --}}
            @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_TIM_SISTER)
                <button type="button" onclick="submitAction('kirim_perbaikan_ke_tim_sister')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105 whitespace-nowrap">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    Kirim ke Sister
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
                <h4 class="text-sm font-medium text-yellow-800">Usulan NUPTK Belum Tersimpan</h4>
                <p class="text-sm text-yellow-700 mt-1">
                    Usulan NUPTK ini belum tersimpan di database. Silakan simpan usulan terlebih dahulu sebelum melanjutkan.
                </p>
            </div>
        </div>
    </div>
@endif

<script>
// Action button configurations untuk NUPTK
const actionConfigs = {
    'kirim_ke_kepegawaian': {
        title: 'Kirim Usulan NUPTK ke Kepegawaian Universitas',
        message: 'Apakah Anda yakin ingin mengirim usulan NUPTK ini ke Kepegawaian Universitas? Usulan akan diverifikasi oleh tim kepegawaian.',
        icon: 'question',
        confirmColor: '#3b82f6',
        loadingText: 'Mengirim usulan NUPTK ke Kepegawaian Universitas...'
    },
    'kirim_perbaikan_ke_kepegawaian': {
        title: 'Kirim Usulan Perbaikan NUPTK',
        message: 'Apakah Anda yakin ingin mengirim usulan perbaikan NUPTK ini ke Kepegawaian Universitas? Pastikan semua field telah diperbaiki.',
        icon: 'question',
        confirmColor: '#f59e0b',
        loadingText: 'Mengirim usulan perbaikan NUPTK ke Kepegawaian Universitas...'
    },
    'kirim_perbaikan_tim_sister_ke_kepegawaian': {
        title: 'Kirim Usulan Perbaikan dari Tim Sister',
        message: 'Apakah Anda yakin ingin mengirim usulan perbaikan dari Tim Sister ini ke Kepegawaian Universitas? Pastikan semua field telah diperbaiki.',
        icon: 'question',
        confirmColor: '#8b5cf6',
        loadingText: 'Mengirim usulan perbaikan dari Tim Sister ke Kepegawaian Universitas...'
    },
    'kirim_perbaikan_ke_tim_sister': {
        title: 'Kirim Usulan Perbaikan ke Tim Sister',
        message: 'Apakah Anda yakin ingin mengirim usulan perbaikan NUPTK ini ke Tim Sister? Usulan akan diproses oleh tim Sister.',
        icon: 'question',
        confirmColor: '#3b82f6',
        loadingText: 'Mengirim usulan perbaikan NUPTK ke Tim Sister...'
    }
};

function submitAction(action) {
    try {
        console.log('submitAction called with action:', action);
        
        // Check if SweetAlert2 is available
        if (typeof Swal === 'undefined') {
            console.log('SweetAlert2 not available, using confirm dialog');
            if (confirm('Apakah Anda yakin ingin melanjutkan aksi ini?')) {
                processAction(action);
            }
            return;
        }
        
        const config = actionConfigs[action];
        console.log('Action config:', config);
        
        if (!config) {
            console.error('Action config not found for:', action);
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
            console.log('SweetAlert result:', result);
            if (result.isConfirmed) {
                console.log('User confirmed, showing loading...');
                // Show loading
                Swal.fire({
                    title: config.loadingText,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Process the action
                console.log('Processing action...');
                processAction(action);
            } else {
                console.log('User cancelled action');
            }
        }).catch((error) => {
            console.error('SweetAlert error:', error);
            // Fallback to processAction directly
            if (confirm(config.message)) {
                processAction(action);
            }
        });
        
    } catch (error) {
        // Fallback to processAction directly
        processAction(action);
    }
}

function processAction(action) {
    try {
        
        // Gunakan form utama yang sudah ada untuk NUPTK (form dengan id="formAction")
        let actionForm = document.querySelector('form input[id="formAction"]')?.closest('form');
        let actionInput = document.getElementById('formAction');
        
        // Fallback: jika tidak ditemukan, cari form yang mengandung button action
        if (!actionForm || !actionInput) {
            actionForm = document.querySelector('form[action*="usulan-nuptk"]');
            actionInput = actionForm?.querySelector('input[name="action"]');
        }
        
        
        if (actionForm && actionInput) {
            // Set global variable untuk tracking
            window.pendingAction = action;
            
            // Set action dan submit form utama
            actionInput.value = action;
            
            // Close loading SweetAlert2 jika ada
            if (typeof Swal !== 'undefined') {
                Swal.close();
            }
            
            
            // Submit form utama tanpa mencegah redirect
            actionForm.submit();
        } else {
            // Fallback: buat form dinamis jika form utama tidak ditemukan
            createDynamicForm(action);
        }
        
    } catch (error) {
        
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
        
        // Create new form untuk NUPTK
        const actionForm = document.createElement('form');
        actionForm.id = 'actionForm';
        actionForm.action = '{{ route("pegawai-unmul.usulan-nuptk.update", $usulan) }}';
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
        
        // Add jenis_nuptk untuk NUPTK
        const jenisNuptkInput = document.createElement('input');
        jenisNuptkInput.type = 'hidden';
        jenisNuptkInput.name = 'jenis_nuptk';
        jenisNuptkInput.value = '{{ $usulan->jenis_nuptk ?? "" }}';
        actionForm.appendChild(jenisNuptkInput);
        
        // Add form to body and submit
        document.body.appendChild(actionForm);
        actionForm.submit();
        
    } catch (error) {
        alert('Terjadi kesalahan saat membuat form. Silakan coba lagi.');
    }
}

// Using global SweetAlert2 functions from show.blade.php
// showSuccess and showError are defined globally

// Form submission handler
document.addEventListener('DOMContentLoaded', function() {
    // Hanya tangani form utama (yang memiliki input dengan id="formAction")
    const mainForm = document.querySelector('form input[id="formAction"]')?.closest('form');
    
    if (mainForm) {
        mainForm.addEventListener('submit', function(e) {
            const actionInput = this.querySelector('input[name="action"]');
            
            // If there's a pending action, use it
            if (window.pendingAction && actionInput) {
                actionInput.value = window.pendingAction;
                window.pendingAction = null; // Reset
            }
        });
    }
    
    // Add global variable to track the action
    window.pendingAction = null;
});
</script>
