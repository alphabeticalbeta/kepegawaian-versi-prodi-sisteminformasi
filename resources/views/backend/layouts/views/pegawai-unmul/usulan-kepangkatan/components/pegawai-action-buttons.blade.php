{{-- Action Buttons untuk Pegawai --}}
@if($usulan->exists)
    <div class="flex flex-wrap gap-4 mb-6">
        {{-- Simpan Usulan (Selalu Aktif jika Edit) --}}
        <button type="submit" name="action" value="simpan" 
                class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
            <i data-lucide="save" class="w-4 h-4"></i>
            Simpan Usulan
        </button>

        {{-- Kirim Usulan Ke Kepegawaian Universitas --}}
        @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_DRAFT_USULAN || is_null($usulan->status_usulan))
            <button type="submit" name="action" value="kirim_ke_kepegawaian" 
                    class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
                <i data-lucide="send" class="w-4 h-4"></i>
                Kirim Usulan Ke Kepegawaian Universitas
            </button>
        @endif

        {{-- Kirim Usulan Perbaikan Ke Kepegawaian Universitas --}}
        @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS)
            <button type="submit" name="action" value="kirim_perbaikan_ke_kepegawaian" 
                    class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                Kirim Usulan Perbaikan Ke Kepegawaian Universitas
            </button>
        @endif

        {{-- Kirim Usulan Perbaikan Dari BKN ke Kepegawaian Universitas --}}
        @if($usulan->status_usulan === \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN)
            <button type="submit" name="action" value="kirim_perbaikan_bkn_ke_kepegawaian" 
                    class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                Kirim Usulan Perbaikan Dari BKN ke Kepegawaian Universitas
            </button>
        @endif
    </div>

    {{-- Info Status --}}
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-start">
            <i data-lucide="info" class="w-5 h-5 text-blue-600 mr-3 mt-0.5"></i>
            <div>
                <h4 class="text-sm font-medium text-blue-800">Status Usulan Saat Ini</h4>
                <p class="text-sm text-blue-700 mt-1">
                    <strong>{{ $usulan->status_usulan ?: 'Belum disimpan' }}</strong>
                </p>
                <p class="text-xs text-blue-600 mt-2">
                    Pilih aksi yang sesuai dengan status usulan Anda. Tombol "Simpan Usulan" selalu tersedia untuk menyimpan perubahan.
                </p>
            </div>
        </div>
    </div>
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
