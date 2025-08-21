@extends('backend.layouts.roles.admin-keuangan.app')
@section('title', 'SK Pangkat - Admin Keuangan')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50">
    <!-- Header Section -->
    <div class="admin-keuangan-bg text-white p-6 rounded-2xl mb-6 shadow-2xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold mb-2">SK Pangkat</h1>
                <p class="text-amber-100">Kelola pembayaran untuk Surat Keputusan Pangkat</p>
            </div>
            <div class="hidden md:block">
                <div class="text-right">
                    <p class="text-amber-100 text-sm">{{ now()->format('l, d F Y') }}</p>
                    <p class="text-amber-200 text-xs">{{ now()->format('H:i') }} WIB</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="periode" class="block text-sm font-medium text-slate-700 mb-2">Periode</label>
                <select name="periode" id="periode"
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                    <option value="">Semua Periode</option>
                    @foreach($periods as $year)
                        <option value="{{ $year }}" {{ request('periode') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status_pembayaran" class="block text-sm font-medium text-slate-700 mb-2">Status Pembayaran</label>
                <select name="status_pembayaran" id="status_pembayaran"
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                    <option value="">Semua Status</option>
                    @foreach($statusPembayaranOptions as $key => $value)
                        <option value="{{ $key }}" {{ request('status_pembayaran') == $key ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="unit_kerja" class="block text-sm font-medium text-slate-700 mb-2">Unit Kerja</label>
                <select name="unit_kerja" id="unit_kerja"
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                    <option value="">Semua Unit Kerja</option>
                    @foreach($unitKerjas as $unitKerjaName => $subSubUnits)
                        <optgroup label="{{ $unitKerjaName }}">
                            @foreach($subSubUnits as $subSubUnit)
                                <option value="{{ $subSubUnit->id }}" {{ request('unit_kerja') == $subSubUnit->id ? 'selected' : '' }}>
                                    {{ $subSubUnit->nama }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit"
                        class="w-full bg-amber-600 text-white px-4 py-3 rounded-xl hover:bg-amber-700 transition-colors duration-200 font-medium flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold text-slate-800">Daftar SK Pangkat</h3>
                    <p class="text-slate-600 mt-1">{{ $usulans->total() }} usulan SK Pangkat yang disetujui</p>
                </div>
                <div class="flex gap-2">
                    <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Excel
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Pegawai</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Pangkat Baru</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal Usulan</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status Pembayaran</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($usulans as $index => $usulan)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                {{ $usulans->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-slate-900">{{ $usulan->pegawai->nama_lengkap }}</div>
                                    <div class="text-sm text-slate-500">NIP: {{ $usulan->pegawai->nip }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-slate-900">{{ $usulan->pangkatTujuan->pangkat ?? 'Tidak tersedia' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $usulan->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusPembayaran = $usulan->status_pembayaran ?? 'Belum Dibayar';
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $statusPembayaran === 'Sudah Dibayar' ? 'bg-green-100 text-green-800' :
                                       ($statusPembayaran === 'Sedang Diproses' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $statusPembayaran }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    <button class="text-amber-600 hover:text-amber-900 px-3 py-1 bg-amber-50 rounded-lg hover:bg-amber-100 transition-colors">
                                        Detail
                                    </button>
                                    <button class="text-green-600 hover:text-green-900 px-3 py-1 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                                        Proses
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-slate-500">
                                    <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-slate-900">Tidak ada data</h3>
                                    <p class="mt-1 text-sm text-slate-500">Belum ada usulan SK Pangkat yang disetujui.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($usulans->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $usulans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
