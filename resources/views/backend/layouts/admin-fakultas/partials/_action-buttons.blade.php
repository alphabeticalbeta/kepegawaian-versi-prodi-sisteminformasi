{{-- resources/views/backend/layouts/admin-fakultas/partials/_action-buttons.blade.php --}}
{{-- Action buttons khusus untuk Admin Fakultas --}}

<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Hasil Validasi</h3>
            <p class="text-sm text-gray-600">Pilih aksi yang akan dilakukan setelah validasi selesai.</p>
        </div>

        @php
            // Tentukan apakah usulan bisa diedit berdasarkan status
            $canEdit = in_array($usulan->status_usulan, ['Diajukan', 'Sedang Direview']);
            $isCompleted = in_array($usulan->status_usulan, [
                'Perlu Perbaikan',
                'Dikembalikan',
                'Diteruskan Ke Universitas',
                'Disetujui',
                'Direkomendasikan',
                'Ditolak'
            ]);
        @endphp

        <div class="flex gap-4">
            @if($canEdit)
                {{-- Tombol Simpan Validasi --}}
                <button type="submit"
                        onclick="submitValidation(event)"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    Simpan Validasi
                </button>

                {{-- Tombol Perbaikan Usulan (Ke Pegawai) --}}
                <button type="button" onclick="showReturnForm()"
                        class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"></path>
                    </svg>
                    Perbaikan Usulan (Ke Pegawai)
                </button>

                {{-- Tombol Belum Direkomendasikan (Ke Pegawai) --}}
                <button type="button" onclick="showRejectForm()"
                        class="px-6 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Belum Direkomendasikan (Ke Pegawai)
                </button>

                {{-- Tombol Direkomendasikan (Ke Admin Universitas) --}}
                <button type="button" onclick="showForwardForm()"
                        class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Direkomendasikan (Ke Admin Universitas)
                </button>

            @else
                {{-- Status indicator jika tidak bisa diedit --}}
                <div class="flex items-center gap-4">
                    @include('backend.layouts.admin-fakultas.partials._status-indicators', ['usulan' => $usulan])

                    {{-- Tombol kembali ke daftar --}}
                    <a href="{{ route('admin-fakultas.periode.pendaftar', $usulan->periode_usulan_id) }}"
                       class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"></path>
                        </svg>
                        Kembali ke Daftar
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Hidden Forms untuk berbagai aksi --}}
    @include('backend.layouts.admin-fakultas.partials._hidden-forms', ['usulan' => $usulan])
</div>