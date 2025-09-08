{{-- Component Daftar Pengusul Tugas Belajar - Hanya untuk jenis usulan Tugas Belajar --}}
@if(isset($filter) && $filter === 'jenis_tugas_belajar' && isset($filterValue))
<div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-slate-800">
            Daftar Pengusul: {{ $filterValue === 'dosen' ? 'Dosen' : 'Tenaga Kependidikan' }}
        </h3>
        <span class="text-sm text-slate-500">
            Total: {{ $usulans->count() }} pengusul
        </span>
    </div>

    @if($usulans->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-slate-600">
            <thead class="text-xs text-slate-700 uppercase bg-slate-100">
                <tr>
                    <th scope="col" class="px-6 py-3 text-center">No</th>
                    <th scope="col" class="px-6 py-3">Nama dan NIP Pegawai</th>
                    <th scope="col" class="px-6 py-3">Fakultas dan Prodi</th>
                    <th scope="col" class="px-6 py-3">Jenis Tugas Belajar</th>
                    <th scope="col" class="px-6 py-3 text-center">Status</th>
                    <th scope="col" class="px-6 py-3 text-center">Tanggal Pengajuan</th>
                    <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usulans as $index => $usulan)
                <tr class="bg-white border-b hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 text-center font-medium">{{ $index + 1 }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div>
                                <div class="font-medium text-slate-900">{{ $usulan->pegawai->nama_lengkap ?? 'N/A' }}</div>
                                <div class="font-medium text-slate-900">NIP: {{ $usulan->pegawai->nip ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($usulan->pegawai && $usulan->pegawai->unitKerja)
                            <div class="text-sm">
                                <div class="font-medium">{{ $usulan->pegawai->unitKerja->subUnitKerja->unitKerja->nama ?? 'N/A' }}</div>
                                <div class="text-xs text-slate-500">{{ $usulan->pegawai->unitKerja->nama ?? 'N/A' }}</div>
                            </div>
                        @else
                            <span class="text-slate-400">N/A</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $jenisTubel = $usulan->data_usulan['jenis_tubel'] ?? 'N/A';
                            $jenisTubelColor = match($jenisTubel) {
                                'Tugas Belajar' => 'bg-blue-100 text-blue-800',
                                'Perpanjangan Tugas Belajar' => 'bg-purple-100 text-purple-800',
                                default => 'bg-slate-100 text-slate-800'
                            };
                        @endphp
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $jenisTubelColor }}">
                            {{ $jenisTubel }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $statusColor = match($usulan->status_usulan) {
                                \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS => 'bg-red-100 text-red-800',
                                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_KEPEGAWAIAN_UNIVERSITAS => 'bg-indigo-100 text-indigo-800',
                                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS => 'bg-cyan-100 text-cyan-800',
                                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_SUDAH_DIKIRIM_KE_KEMENTERIAN => 'bg-purple-100 text-purple-800',
                                \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEMENTERIAN => 'bg-orange-100 text-orange-800',
                                \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEMENTERIAN => 'bg-amber-100 text-amber-800',
                                \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEMENTERIAN => 'bg-red-100 text-red-800',
                                default => 'bg-slate-100 text-slate-800'
                            };
                        @endphp
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                            {{ $usulan->status_usulan ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center text-sm">
                        {{ $usulan->created_at ? \Carbon\Carbon::parse($usulan->created_at)->format('d M Y') : 'N/A' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('backend.kepegawaian-universitas.usulan.validasi-tubel', $usulan->id) }}"
                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-cyan-700 transition-colors">
                            <i class="fas fa-eye mr-1"></i>
                            Detail
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-center py-8">
        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-book-open text-slate-400 text-xl"></i>
        </div>
        <p class="text-slate-500 text-lg font-medium">Belum ada pengusul</p>
        <p class="text-slate-400 text-sm">Untuk jenis usulan Tugas Belajar: {{ $filterValue === 'dosen' ? 'Dosen' : 'Tenaga Kependidikan' }}</p>
    </div>
    @endif
</div>
@endif
