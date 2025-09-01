@extends('backend.layouts.roles.kepegawaian-universitas.app')
@section('title', 'Dashboard Periode: ' . $periode->nama_periode . ' - Kepegawaian Universitas')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 p-4">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-6 rounded-2xl mb-6 shadow-2xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold mb-2">Dashboard: {{ $periode->nama_periode }}</h1>
                <p class="text-blue-100">{{ $periode->jenis_usulan }} • Tahun {{ $periode->tahun_periode }}</p>
                <div class="flex items-center mt-2 space-x-4 text-blue-200 text-sm">
                    <span>📅 {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d M Y') }}</span>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                        {{ $periode->status === 'Buka' ? 'bg-green-500/20 text-green-100' : 'bg-red-500/20 text-red-100' }}">
                        {{ $periode->status }}
                    </span>
                </div>
            </div>
            <div class="hidden md:block">
                <a href="{{ route('backend.kepegawaian-universitas.dashboard-periode.index', ['jenis' => request()->get('jenis', 'jabatan')]) }}"
                   class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>



    <!-- Charts and Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Status Distribution Chart -->
        <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Distribusi Status Usulan</h3>
            <div class="space-y-4">
                @foreach($usulanByStatus as $status => $count)
                    @php
                        $percentage = $stats['total_usulan'] > 0 ? ($count / $stats['total_usulan']) * 100 : 0;
                        $colorClass = match($status) {
                            'Disetujui' => 'bg-green-500',
                            'Ditolak' => 'bg-red-500',
                            'Menunggu Verifikasi' => 'bg-yellow-500',
                            'Dalam Proses' => 'bg-blue-500',
                            default => 'bg-gray-500'
                        };
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-slate-600">{{ $status }}</span>
                            <span class="font-semibold">{{ $count }} ({{ number_format($percentage, 1) }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="{{ $colorClass }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Employee Type Distribution -->
        <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Usulan per Jenis Pegawai</h3>
            <div class="space-y-4">
                @forelse($usulanByJenisPegawai as $jenis => $count)
                    @php
                        $percentage = $stats['total_usulan'] > 0 ? ($count / $stats['total_usulan']) * 100 : 0;
                        $colorClass = match($jenis) {
                            'Dosen' => 'bg-indigo-500',
                            'Tenaga Kependidikan' => 'bg-purple-500',
                            default => 'bg-gray-500'
                        };
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-slate-600">{{ $jenis }}</span>
                            <span class="font-semibold">{{ $count }} ({{ number_format($percentage, 1) }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="{{ $colorClass }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500 text-center">Belum ada data usulan</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-wrap gap-4 mb-6">
        <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.edit', $periode) }}"
           class="bg-indigo-600 text-white px-6 py-3 rounded-xl hover:bg-indigo-700 transition-colors duration-200 font-medium flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit Periode
        </a>

        <button class="bg-emerald-600 text-white px-6 py-3 rounded-xl hover:bg-emerald-700 transition-colors duration-200 font-medium flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Export Data
        </button>

        <form id="togglePeriodeForm" action="{{ route('backend.kepegawaian-universitas.usulan.toggle-periode') }}" method="POST" class="inline">
            @csrf
            <input type="hidden" name="periode_id" value="{{ $periode->id }}">
            <button type="submit" id="togglePeriodeBtn"
                    class="px-6 py-3 rounded-xl font-medium flex items-center transition-colors duration-200
                    {{ $periode->status === 'Buka' ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-green-600 text-white hover:bg-green-700' }}">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if($periode->status === 'Buka')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                    @endif
                </svg>
                <span id="togglePeriodeText">{{ $periode->status === 'Buka' ? 'Tutup Periode' : 'Buka Periode' }}</span>
            </button>
        </form>
    </div>

    <!-- Recent Usulans -->
    <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30">
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-800">Usulan Terbaru</h3>
                                <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.pendaftar', $periode) }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                    Lihat Semua
                </a>
            </div>
        </div>

        <div class="table-container">
            <table class="table-full">
                <thead>
                    <tr>
                        <th>Pegawai</th>
                        <th>Jenis Pegawai</th>
                        <th>Unit Kerja</th>
                        <th>Sub-Sub Unit Kerja</th>
                        <th>Tujuan Usulan</th>
                        <th>Tanggal Usulan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentUsulans as $usulan)
                        <tr>
                            <td>
                                <div>
                                    <div class="text-sm font-medium text-slate-900">{{ $usulan->pegawai->nama_lengkap }}</div>
                                    <div class="text-sm text-slate-500">NIP: {{ $usulan->pegawai->nip }}</div>
                                </div>
                            </td>
                            <td class="text-sm text-slate-500">
                                {{ $usulan->pegawai->jenis_pegawai }}
                            </td>
                            <td class="text-sm text-slate-500">
                                @php
                                    $unitKerja = $usulan->pegawai->unitKerja;
                                    $subUnitKerja = $unitKerja ? $unitKerja->subUnitKerja : null;
                                    $parentUnitKerja = $subUnitKerja ? $subUnitKerja->unitKerja : null;
                                @endphp
                                {{ $parentUnitKerja ? $parentUnitKerja->nama : 'N/A' }}
                            </td>
                            <td class="text-sm text-slate-500">
                                {{ $unitKerja ? $unitKerja->nama : 'N/A' }}
                            </td>
                            <td class="text-sm text-slate-500">
                                {{ $usulan->jabatanTujuan->jabatan ?? 'N/A' }}
                            </td>
                            <td class="text-sm text-slate-500">
                                {{ $usulan->created_at->format('d M Y H:i') }}
                            </td>
                            <td>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $usulan->status_usulan === 'Disetujui' ? 'bg-green-100 text-green-800' :
                                       ($usulan->status_usulan === 'Ditolak' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ $usulan->status_usulan }}
                                </span>
                            </td>
                            <td class="text-sm text-slate-500">
                                <a href="{{ route('backend.kepegawaian-universitas.usulan.show', $usulan->id) }}"
                                   class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-slate-500 py-8">
                                Belum ada usulan untuk periode ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table-container {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .table-full {
        width: 100%;
        height: 100%;
        table-layout: fixed;
        border-collapse: collapse;
    }

    .table-full th,
    .table-full td {
        border: 1px solid #e2e8f0;
        padding: 12px;
        text-align: left;
        vertical-align: middle;
    }

    .table-full th {
        background-color: #f8fafc;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }

    .table-full tbody tr:hover {
        background-color: #f1f5f9;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleForm = document.getElementById('togglePeriodeForm');
    const toggleBtn = document.getElementById('togglePeriodeBtn');
    const toggleText = document.getElementById('togglePeriodeText');

    if (toggleForm) {
        toggleForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Disable button to prevent double submission
            toggleBtn.disabled = true;
            toggleBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Memproses...';

            fetch(toggleForm.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    periode_id: document.querySelector('input[name="periode_id"]').value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Update button appearance
                    if (data.new_status === 'Buka') {
                        toggleBtn.className = 'px-6 py-3 rounded-xl font-medium flex items-center transition-colors duration-200 bg-red-600 text-white hover:bg-red-700';
                        toggleBtn.innerHTML = `
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Tutup Periode</span>
                        `;
                    } else {
                        toggleBtn.className = 'px-6 py-3 rounded-xl font-medium flex items-center transition-colors duration-200 bg-green-600 text-white hover:bg-green-700';
                        toggleBtn.innerHTML = `
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                            </svg>
                            <span>Buka Periode</span>
                        `;
                    }
                } else {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan saat mengubah status periode.'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan saat mengubah status periode.'
                });
            })
            .finally(() => {
                // Re-enable button
                toggleBtn.disabled = false;
            });
        });
    }
});
</script>
@endpush
@endsection

