{{-- Header Info Card - Informasi Dasar Usulan --}}
<div class="bg-white shadow-md rounded-lg mb-6">
    <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
        <h3 class="text-lg font-semibold text-gray-800">Informasi Usulan</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Nama Lengkap --}}
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Nama Lengkap</p>
                <p class="text-base font-semibold text-gray-900">
                    {{ $usulan->pegawai->gelar_depan ?? '' }}
                    {{ $usulan->pegawai->nama_lengkap ?? 'N/A' }}
                    {{ $usulan->pegawai->gelar_belakang ?? '' }}
                </p>
            </div>

            {{-- NIP --}}
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">NIP</p>
                <p class="text-base font-mono text-gray-800">
                    {{ $usulan->pegawai->nip ?? 'N/A' }}
                </p>
            </div>

            {{-- Jenis Usulan --}}
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Jenis Usulan</p>
                <p class="text-base text-gray-800 capitalize">
                    {{ str_replace(['usulan-', '-'], [' ', ' '], $usulan->jenis_usulan) }}
                </p>
            </div>

            {{-- Status Usulan --}}
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Status Saat Ini</p>
                @php
                    $statusColors = [
                        'Draft' => 'bg-gray-100 text-gray-800',
                        'Diajukan' => 'bg-blue-100 text-blue-800',
                        'Sedang Direview' => 'bg-yellow-100 text-yellow-800',
                        'Perlu Perbaikan' => 'bg-orange-100 text-orange-800',
                        'Dikembalikan' => 'bg-red-100 text-red-800',
                        'Diteruskan Ke Universitas' => 'bg-purple-100 text-purple-800',
                        'Disetujui' => 'bg-green-100 text-green-800',
                        'Direkomendasikan' => 'bg-emerald-100 text-emerald-800',
                        'Ditolak' => 'bg-red-100 text-red-800'
                    ];
                    $statusClass = $statusColors[$usulan->status_usulan] ?? 'bg-gray-100 text-gray-800';
                @endphp
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $statusClass }}">
                    {{ $usulan->status_usulan }}
                </span>
            </div>
        </div>

        {{-- Additional Info Row --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6 pt-6 border-t border-gray-200">
            {{-- Unit Kerja --}}
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Unit Kerja</p>
                <p class="text-base text-gray-800">
                    {{ $usulan->pegawai->unitKerja->nama ?? 'N/A' }}
                </p>
            </div>

            {{-- Jabatan Lama (if applicable) --}}
            @if(isset($usulan->jabatanLama))
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Jabatan Saat Ini</p>
                <p class="text-base text-gray-800">
                    {{ $usulan->jabatanLama->jabatan ?? 'Tidak Ada' }}
                </p>
            </div>
            @endif

            {{-- Jabatan Tujuan (if applicable) --}}
            @if(isset($usulan->jabatanTujuan))
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Jabatan Tujuan</p>
                <p class="text-base font-semibold text-indigo-600">
                    {{ $usulan->jabatanTujuan->jabatan ?? 'Tidak Ada' }}
                </p>
            </div>
            @endif

            {{-- Tanggal Pengajuan --}}
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Tanggal Pengajuan</p>
                <p class="text-base text-gray-800">
                    {{ $usulan->created_at->isoFormat('D MMMM YYYY') }}
                </p>
            </div>
        </div>

        {{-- Periode Info --}}
        @if(isset($usulan->periodeUsulan))
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-blue-900">
                            Periode: {{ $usulan->periodeUsulan->nama_periode }}
                        </p>
                        <p class="text-sm text-blue-700 mt-1">
                            Berlaku: {{ \Carbon\Carbon::parse($usulan->periodeUsulan->tanggal_mulai)->isoFormat('D MMM') }} -
                            {{ \Carbon\Carbon::parse($usulan->periodeUsulan->tanggal_selesai)->isoFormat('D MMM YYYY') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    @php
        $counts = $usulan->getSenateDecisionCounts();
        $minSetuju = $usulan->getSenateMinSetuju();
        $isReviewerRecommended = $usulan->isRecommendedByReviewer();
    @endphp

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4 p-5">
        {{-- Status Rekomendasi Tim Penilai --}}
        <div class="rounded-lg border border-gray-200 p-4 bg-white">
            <p class="text-sm text-gray-500 mb-1">Status Tim Penilai</p>
            @if($isReviewerRecommended)
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-sm font-semibold">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                    Direkomendasikan
                </div>
            @else
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-sm font-semibold">
                    <span class="inline-block w-2 h-2 rounded-full bg-amber-500"></span>
                    Belum Direkomendasikan
                </div>
            @endif
            <p class="mt-2 text-xs text-gray-500">Sumber: tabel <code>usulan_penilai</code></p>
        </div>

        {{-- Ringkasan Keputusan Tim Senat --}}
        <div class="rounded-lg border border-gray-200 p-4 bg-white">
            <p class="text-sm text-gray-500 mb-1">Keputusan Tim Senat</p>
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-2 py-1 rounded bg-emerald-50 text-emerald-700 text-sm">Setuju: <b>{{ $counts['setuju'] }}</b></span>
                <span class="px-2 py-1 rounded bg-rose-50 text-rose-700 text-sm">Menolak: <b>{{ $counts['tolak'] }}</b></span>
                <span class="px-2 py-1 rounded bg-gray-100 text-gray-700 text-sm">Total: <b>{{ $counts['total'] }}</b></span>
            </div>
            <p class="mt-2 text-xs text-gray-500">Threshold minimal setuju: <b>{{ $minSetuju }}</b></p>
        </div>

        {{-- Status Kelayakan Direkomendasikan (Admin Univ) --}}
        <div class="rounded-lg border border-gray-200 p-4 bg-white">
            <p class="text-sm text-gray-500 mb-1">Kelayakan Direkomendasikan</p>
            @php
                $senatePass = $counts['total'] > 0 && $counts['setuju'] >= $minSetuju;
            @endphp
            @if($isReviewerRecommended && $senatePass)
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-sm font-semibold">
                    <span class="inline-block w-2 h-2 rounded-full bg-indigo-500"></span>
                    Siap Direkomendasikan
                </div>
            @else
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-sm font-semibold">
                    <span class="inline-block w-2 h-2 rounded-full bg-gray-500"></span>
                    Belum Memenuhi Syarat
                </div>
            @endif
            <ul class="mt-2 text-xs text-gray-500 list-disc list-inside space-y-0.5">
                <li>Penilai direkomendasikan: <b>{{ $isReviewerRecommended ? 'Ya' : 'Tidak' }}</b></li>
                <li>Senat setuju: <b>{{ $counts['setuju'] }}</b> / minimal <b>{{ $minSetuju }}</b></li>
            </ul>
        </div>
    </div>
</div>
