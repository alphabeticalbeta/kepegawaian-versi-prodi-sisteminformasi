{{-- Action Buttons Component --}}
@php
    $canEdit = $canEdit ?? in_array($usulan->status_usulan, [
        'Diusulkan ke Universitas',
        'Sedang Direview Universitas',
    ]);
    // Baca syarat dari model
    $minSetuju = $usulan->getSenateMinSetuju();
    $isReviewerRecommended = $usulan->isRecommendedByReviewer();
    $senatePass = $usulan->isSenateApproved($minSetuju);

    // Tombol Direkomendasikan hanya aktif jika dua-duanya terpenuhi
    $canRecommend = $isReviewerRecommended && $senatePass;

    // Determine if usulan can be edited
     $canEdit = $canEdit ?? in_array($usulan->status_usulan, [
        'Diusulkan ke Universitas',
        'Sedang Direview Universitas',
    ]);

    $isCompleted = in_array($usulan->status_usulan, [
                    'Perbaikan Usulan',
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

        <div class="flex gap-4">
            @if($canEdit)
                {{-- Editable Actions --}}
                <button type="button"
                        onclick="showReturnForm()"
                        class="px-6 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"></path></svg>
                    Kembalikan untuk Revisi
                </button>

                <button type="button"
                        onclick="showNotRecommendedForm()"
                        class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Belum Direkomendasikan
                </button>

                <button type="button"
                        onclick="showSendToAssessorForm()"
                        class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8">
                        </path>
                    </svg>
                    Kirim Usulan ke Tim Penilai
                </button>

                <button type="button"
                        onclick="showSendToSenateForm()"
                        class="px-6 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 flex items-center gap-2 transition-colors
                               {{ $usulan->isRecommendedByReviewer() ? '' : 'opacity-50 cursor-not-allowed' }}"
                        {{ $usulan->isRecommendedByReviewer() ? '' : 'disabled' }}
                        title="{{ $usulan->isRecommendedByReviewer() ? 'Kirim ke Tim Senat' : 'Menunggu rekomendasi dari Tim Penilai' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                    Kirim Usulan ke Tim Senat
                </button>

                @if(!$usulan->isRecommendedByReviewer())
                    <p class="text-xs text-gray-500 mt-2">
                        Syarat untuk Kirim ke Tim Senat: Tim Penilai belum memberikan rekomendasi.
                    </p>
                @endif

            @else
                {{-- Read-only Status Indicators --}}
                @if($usulan->status_usulan === 'Perbaikan Usulan')
                    <div class="flex items-center gap-2 px-4 py-2 bg-orange-100 text-orange-800 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                            </path>
                        </svg>
                        <span class="font-medium">Usulan sudah dikembalikan ke pegawai untuk perbaikan</span>
                    </div>

                @elseif($usulan->status_usulan === 'Diteruskan Ke Universitas')
                    <div class="flex items-center gap-2 px-4 py-2 bg-purple-100 text-purple-800 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8">
                            </path>
                        </svg>
                        <span class="font-medium">Usulan sudah diteruskan ke tingkat universitas</span>
                    </div>

                @elseif($usulan->status_usulan === 'Direkomendasikan')
                    <div class="flex items-center gap-2 px-4 py-2 bg-emerald-100 text-emerald-800 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <span class="font-medium">Usulan telah selesai diproses - Direkomendasikan</span>
                    </div>

                @elseif($usulan->status_usulan === 'Ditolak')
                    <div class="flex items-center gap-2 px-4 py-2 bg-red-100 text-red-800 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <span class="font-medium">Usulan telah selesai diproses - Ditolak</span>
                    </div>

                @else
                    <div class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-800 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <span class="font-medium">Status: {{ $usulan->status_usulan }}</span>
                    </div>
                @endif

                {{-- Back to List Button --}}
                <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.pendaftar', $usulan->periode_usulan_id) }}"
                   class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700
                          flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z">
                        </path>
                    </svg>
                    Kembali ke Daftar
                </a>
            @endif
        </div>
    </div>
</div>
