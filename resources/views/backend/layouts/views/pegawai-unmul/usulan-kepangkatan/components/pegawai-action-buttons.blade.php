{{-- Action Buttons untuk Pegawai --}}
@if($usulan->exists)
    {{-- Check if usulan is in view-only status --}}
    @php
        $viewOnlyStatuses = [
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_SUDAH_DIKIRIM_KE_BKN,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN_BKN,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_DIREKOMENDASIKAN,
            \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN
        ];
        
        $isViewOnly = in_array($usulan->status_usulan, $viewOnlyStatuses);
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
function submitAction(action) {
    try {
        // Try to find existing form
        let actionForm = document.getElementById('actionForm');
        let actionValue = document.getElementById('actionValue');
        
        // If form not found, create it dynamically
        if (!actionForm || !actionValue) {
            // Remove existing form if any
            const existingForm = document.getElementById('actionForm');
            if (existingForm) {
                existingForm.remove();
            }
            
            // Create new form
            actionForm = document.createElement('form');
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
            actionValue = document.createElement('input');
            actionValue.type = 'hidden';
            actionValue.name = 'action';
            actionValue.id = 'actionValue';
            actionForm.appendChild(actionValue);
            
            // Add pangkat_tujuan_id
            const pangkatInput = document.createElement('input');
            pangkatInput.type = 'hidden';
            pangkatInput.name = 'pangkat_tujuan_id';
            pangkatInput.value = '{{ $usulan->pangkat_tujuan_id ?? "" }}';
            actionForm.appendChild(pangkatInput);
            
            // Add form to body
            document.body.appendChild(actionForm);
        }
        
        // Set action value and submit
        actionValue.value = action;
        actionForm.submit();
        
    } catch (error) {
        console.error('Error in submitAction:', error);
        alert('Terjadi kesalahan. Silakan coba lagi.');
    }
}
</script>