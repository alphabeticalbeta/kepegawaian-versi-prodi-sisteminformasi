@extends('backend.layouts.roles.kepegawaian-universitas.app')
@section('title', 'Dashboard ' . $namaUsulan . ' - Kepegawaian Universitas')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 p-4">
    <!-- Flash Messages -->
    @include('backend.components.usulan._alert-messages')

    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-6 rounded-2xl mb-6 shadow-2xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold mb-2">Dashboard {{ $namaUsulan }}</h1>
                <p class="text-blue-100">Kelola periode dan statistik untuk {{ $namaUsulan }}</p>
            </div>
            <div class="hidden md:block">
                <div class="text-right">
                    <p class="text-blue-100 text-sm">{{ now()->format('l, d F Y') }}</p>
                    <p class="text-blue-200 text-xs">{{ now()->format('H:i') }} WIB</p>
                </div>
            </div>
        </div>
    </div>



    <!-- Quick Actions -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-slate-800">Aksi Cepat</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.create') }}?jenis={{ $jenisUsulan }}"
               class="bg-white/90 backdrop-blur-xl p-6 rounded-xl shadow-lg border border-white/30 hover:shadow-xl transition-all duration-300 flex items-center group">
                <div class="p-3 bg-blue-100 rounded-lg mr-4 group-hover:bg-blue-200 transition-colors">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800">Buat Periode Usulan</h3>
                    <p class="text-sm text-slate-600">Tambah periode baru untuk {{ $namaUsulan }}</p>
                </div>
            </a>

            <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.index') }}"
               class="bg-white/90 backdrop-blur-xl p-6 rounded-xl shadow-lg border border-white/30 hover:shadow-xl transition-all duration-300 flex items-center group">
                <div class="p-3 bg-indigo-100 rounded-lg mr-4 group-hover:bg-indigo-200 transition-colors">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800">Kelola Semua Periode</h3>
                    <p class="text-sm text-slate-600">Edit dan kelola periode usulan</p>
                </div>
            </a>

            <a href="#"
               class="bg-white/90 backdrop-blur-xl p-6 rounded-xl shadow-lg border border-white/30 hover:shadow-xl transition-all duration-300 flex items-center group">
                <div class="p-3 bg-green-100 rounded-lg mr-4 group-hover:bg-green-200 transition-colors">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800">Laporan {{ $namaUsulan }}</h3>
                    <p class="text-sm text-slate-600">Export dan analisis data</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Periods Table -->
    <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30">
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold text-slate-800">Tabel Periode {{ $namaUsulan }}</h3>
                    <p class="text-slate-600 mt-1">Kelola periode usulan dengan mudah</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- Search and Filter -->
            <div class="mb-6 flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text"
                               id="searchPeriode"
                               placeholder="Cari periode..."
                               class="w-full pl-10 p-5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <div class="flex gap-2">
                    <select id="filterStatus" class="px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Status</option>
                        <option value="Buka">Buka</option>
                        <option value="Tutup">Tutup</option>
                    </select>
                    <button id="resetFilter" class="px-4 py-3 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition-colors">
                        Reset
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                @if($periodes->count() > 0)
                    <div class="mb-4 text-sm text-slate-600">
                        Menampilkan <span class="font-semibold">{{ $periodes->count() }}</span> periode usulan
                    </div>
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nama Periode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Jenis Usulan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Periode Usulan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Periode Perbaikan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Jumlah Pelamar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @foreach($periodes as $index => $periode)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 font-medium">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-slate-900">{{ $periode->nama_periode }}</div>
                                        <div class="text-sm font-medium text-slate-900">Tahun {{ $periode->tahun_periode }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if($periode->jenis_usulan == 'jabatan-dosen-regular') bg-indigo-100 text-indigo-800
                                        @elseif($periode->jenis_usulan == 'jabatan-dosen-pengangkatan') bg-cyan-100 text-cyan-800
                                        @elseif($periode->jenis_usulan == 'nuptk') bg-green-100 text-green-800
                                        @elseif($periode->jenis_usulan == 'laporan-lkd') bg-blue-100 text-blue-800
                                        @elseif($periode->jenis_usulan == 'presensi') bg-pink-100 text-pink-800
                                        @elseif($periode->jenis_usulan == 'id-sinta-sister') bg-teal-100 text-teal-800
                                        @elseif($periode->jenis_usulan == 'satyalancana') bg-orange-100 text-orange-800
                                        @elseif($periode->jenis_usulan == 'tugas-belajar') bg-cyan-100 text-cyan-800
                                        @elseif($periode->jenis_usulan == 'pengaktifan-kembali') bg-emerald-100 text-emerald-800
                                        @elseif($periode->jenis_usulan == 'penyesuaian-masa-kerja') bg-amber-100 text-amber-800
                                        @elseif($periode->jenis_usulan == 'ujian-dinas-ijazah') bg-lime-100 text-lime-800
                                        @elseif($periode->jenis_usulan == 'laporan-serdos') bg-rose-100 text-rose-800
                                        @elseif($periode->jenis_usulan == 'pensiun') bg-slate-100 text-slate-800
                                        @elseif($periode->jenis_usulan == 'kepangkatan') bg-violet-100 text-violet-800
                                        @elseif($periode->jenis_usulan == 'pencantuman-gelar') bg-fuchsia-100 text-fuchsia-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        @if($periode->jenis_usulan == 'jabatan-dosen-regular')
                                            Usulan Jabatan Dosen Reguler
                                        @elseif($periode->jenis_usulan == 'jabatan-dosen-pengangkatan')
                                            Usulan Jabatan Dosen Pengangkatan Pertama
                                        @elseif($periode->jenis_usulan == 'nuptk')
                                            Usulan NUPTK
                                        @elseif($periode->jenis_usulan == 'laporan-lkd')
                                            Usulan Laporan LKD
                                        @elseif($periode->jenis_usulan == 'presensi')
                                            Usulan Presensi
                                        @elseif($periode->jenis_usulan == 'id-sinta-sister')
                                            Usulan ID SINTA ke SISTER
                                        @elseif($periode->jenis_usulan == 'satyalancana')
                                            Usulan Satyalancana
                                        @elseif($periode->jenis_usulan == 'tugas-belajar')
                                            Usulan Tugas Belajar
                                        @elseif($periode->jenis_usulan == 'pengaktifan-kembali')
                                            Usulan Pengaktifan Kembali
                                        @elseif($periode->jenis_usulan == 'penyesuaian-masa-kerja')
                                            Usulan Penyesuaian Masa Kerja
                                        @elseif($periode->jenis_usulan == 'ujian-dinas-ijazah')
                                            Usulan Ujian Dinas Ijazah
                                        @elseif($periode->jenis_usulan == 'laporan-serdos')
                                            Usulan Laporan SERDOS
                                        @elseif($periode->jenis_usulan == 'pensiun')
                                            Usulan Pensiun
                                        @elseif($periode->jenis_usulan == 'kepangkatan')
                                            Usulan Kepangkatan
                                        @elseif($periode->jenis_usulan == 'pencantuman-gelar')
                                            Usulan Pencantuman Gelar
                                        @else
                                            {{ ucwords(str_replace('-', ' ', $periode->jenis_usulan)) }}
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                    <div class="flex flex-col">
                                        <div class="font-medium text-slate-800">
                                            {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d M Y') }}
                                        </div>
                                        <div class="font-medium text-slate-800">
                                            s/d {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d M Y') }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                    @if($periode->tanggal_mulai_perbaikan && $periode->tanggal_selesai_perbaikan)
                                        <div class="flex flex-col">
                                            <div class="font-medium text-slate-800">
                                                {{ \Carbon\Carbon::parse($periode->tanggal_mulai_perbaikan)->format('d M Y') }}
                                            </div>
                                            <div class="font-medium text-slate-800">
                                                s/d {{ \Carbon\Carbon::parse($periode->tanggal_selesai_perbaikan)->format('d M Y') }}
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                    <div class="flex items-center">
                                        <span class="font-semibold text-blue-600">{{ $periode->usulans_count }}</span>
                                        <span class="text-slate-500 ml-1">pelamar</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $periode->status === 'Buka' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $periode->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-1 sm:space-x-2">
                                        <!-- Lihat Data Pengusul -->
                                        <a href="{{ route('backend.kepegawaian-universitas.dashboard-periode.show', $periode) }}"
                                           class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-2 py-2 sm:px-3 sm:py-2 rounded-lg transition-colors duration-200 flex items-center"
                                           title="Lihat Data Pengusul">
                                            <svg class="w-4 h-4 sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            <span class="text-xs font-medium hidden sm:inline">Lihat</span>
                                        </a>

                                        <!-- Edit -->
                                        <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.edit', $periode) }}"
                                           class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-2 py-2 sm:px-3 sm:py-2 rounded-lg transition-colors duration-200 flex items-center"
                                           title="Edit Periode">
                                            <svg class="w-4 h-4 sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            <span class="text-xs font-medium hidden sm:inline">Edit</span>
                                        </a>

                                        <!-- Delete -->
                                        @if($periode->usulans_count == 0)
                                            <form action="{{ route('backend.kepegawaian-universitas.periode-usulan.destroy', $periode) }}"
                                                  method="POST"
                                                  class="inline-block"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus periode ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-2 py-2 sm:px-3 sm:py-2 rounded-lg transition-colors duration-200 flex items-center"
                                                        title="Hapus Periode">
                                                    <svg class="w-4 h-4 sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    <span class="text-xs font-medium hidden sm:inline">Hapus</span>
                                                </button>
                                            </form>
                                        @else
                                            <button disabled
                                                    class="text-slate-400 bg-slate-100 px-2 py-2 sm:px-3 sm:py-2 rounded-lg cursor-not-allowed flex items-center"
                                                    title="Tidak dapat dihapus karena sudah ada pelamar">
                                                <svg class="w-4 h-4 sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                <span class="text-xs font-medium hidden sm:inline">Hapus</span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-slate-900">Belum ada periode</h3>
                    <p class="mt-1 text-sm text-slate-500">Belum ada periode untuk {{ $namaUsulan }}. Mulai dengan membuat periode pertama.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchPeriode');
    const statusFilter = document.getElementById('filterStatus');
    const resetButton = document.getElementById('resetFilter');
    const tableRows = document.querySelectorAll('tbody tr');
    const resultCount = document.querySelector('.mb-4.text-sm.text-slate-600 span');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusTerm = statusFilter.value;
        let visibleIndex = 1;
        let visibleCount = 0;

        tableRows.forEach((row) => {
            const namaPeriode = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const status = row.querySelector('td:nth-child(7)').textContent.trim();

            const matchesSearch = namaPeriode.includes(searchTerm);
            const matchesStatus = !statusTerm || status === statusTerm;

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
                // Update nomor urut untuk baris yang terlihat
                row.querySelector('td:first-child').textContent = visibleIndex++;
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update jumlah hasil
        if (resultCount) {
            resultCount.textContent = visibleCount;
        }
    }

    // Event listeners
    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);

    resetButton.addEventListener('click', function() {
        searchInput.value = '';
        statusFilter.value = '';
        tableRows.forEach((row, index) => {
            row.style.display = '';
            row.querySelector('td:first-child').textContent = index + 1;
        });
        // Reset jumlah hasil
        if (resultCount) {
            resultCount.textContent = tableRows.length;
        }
    });
});
</script>
@endsection
