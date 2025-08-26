{{-- Info History Perbaikan Section --}}
@if($currentRole === 'Admin Fakultas')
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mb-6">
        <div class="flex items-center mb-4">
            <i data-lucide="info" class="w-5 h-5 text-blue-600 mr-2"></i>
            <h3 class="text-lg font-semibold text-gray-900">ⓘ Info History Perbaikan</h3>
        </div>
        @if(!empty($existingValidation))
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600 mr-3 mt-0.5"></i>
                    <div>
                        <h4 class="font-medium text-yellow-800">Perbaikan dari Admin Universitas</h4>
                        <p class="text-sm text-yellow-700 mt-1">
                            Usulan ini telah dikembalikan untuk perbaikan. Silakan periksa catatan perbaikan di bawah ini.
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i data-lucide="info" class="w-5 h-5 text-gray-600 mr-3 mt-0.5"></i>
                    <div>
                        <h4 class="font-medium text-gray-800">Tidak Ada Data Perbaikan</h4>
                        <p class="text-sm text-gray-700 mt-1">
                            Belum ada data perbaikan yang dikirim ke Pegawai. History akan muncul setelah Admin Fakultas mengirim perbaikan.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endif
