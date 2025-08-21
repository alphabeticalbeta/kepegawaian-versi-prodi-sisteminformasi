{{-- Action Buttons Component --}}
@php
    // Determine if usulan can be edited
    $canEdit = $canEdit ?? in_array($usulan->status_usulan, ['Diajukan', 'Sedang Direview']);

    $isCompleted = in_array($usulan->status_usulan, [
        'Perlu Perbaikan',
        'Dikembalikan',
        'Diteruskan Ke Universitas',
        'Disetujui',
        'Direkomendasikan',
        'Ditolak'
    ]);
@endphp

<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Hasil Validasi</h3>
            <p class="text-sm text-gray-600">
                @if($canEdit)
                    Pilih aksi yang akan dilakukan setelah validasi selesai.
                @else
                    Status usulan: {{ $usulan->status_usulan }}
                @endif
            </p>
        </div>

        {{-- Action Buttons: Admin Universitas --}}
        <div class="bg-white shadow-md rounded-lg p-6 mt-6">
        <div class="flex flex-col md:flex-row md:items-center gap-3">

            {{-- Simpan saja --}}
            <button type="submit" name="action_type" value="save_only"
                    class="px-6 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">
            Simpan Validasi
            </button>

            {{-- Kembalikan ke Pegawai (wajib catatan_umum) --}}
            <button type="submit" name="action_type" value="return_to_pegawai"
                    onclick="return confirm('Kembalikan usulan ke pegawai untuk perbaikan?')"
                    class="px-6 py-2 rounded-md bg-yellow-600 text-white hover:bg-yellow-700">
            Kembalikan ke Pegawai
            </button>

            {{-- Tolak --}}
            <button type="submit" name="action_type" value="reject_proposal"
                    onclick="return confirm('Tolak usulan ini? Tindakan ini menghentikan proses.')"
                    class="px-6 py-2 rounded-md bg-red-600 text-white hover:bg-red-700">
            Tolak
            </button>

            {{-- Setujui (langsung set Direkomendasikan) --}}
            <button type="submit" name="action_type" value="approve_proposal"
                    onclick="return confirm('Setujui & rekomendasikan usulan ini?')"
                    class="px-6 py-2 rounded-md bg-emerald-600 text-white hover:bg-emerald-700">
            Setujui (Direkomendasikan)
            </button>

            {{-- Rekomendasikan dgn cek syarat (Penilai & Senat) --}}
            <button type="submit" name="action_type" value="recommend_proposal"
                    title="Hanya lanjut jika syarat rekomendasi terpenuhi (Penilai & Senat)"
                    class="px-6 py-2 rounded-md bg-green-600 text-white hover:bg-green-700">
            Rekomendasikan (Cek Syarat)
            </button>

        </div>

        {{-- Catatan umum (opsional / required_if:return_to_pegawai) --}}
        <div class="mt-4">
            <label for="catatan_umum" class="block text-sm font-medium text-gray-700">
            Catatan Umum (wajib diisi jika mengembalikan ke pegawai)
            </label>
            <textarea id="catatan_umum" name="catatan_umum" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                    placeholder="Tulis catatan untuk pegawai/jejak proses...">{{ old('catatan_umum') }}</textarea>
        </div>
        </div>

    </div>
</div>
