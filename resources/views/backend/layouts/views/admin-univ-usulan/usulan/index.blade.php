@extends('backend.layouts.roles.admin-univ-usulan.app')
@section('title', $namaUsulan . ' - Admin Universitas Usulan')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 p-4">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-6 rounded-2xl mb-6 shadow-2xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold mb-2">{{ $namaUsulan }}</h1>
                <p class="text-blue-100">Kelola usulan untuk periode {{ $periode->nama_periode }}</p>
                <div class="flex items-center mt-2 space-x-4 text-blue-200 text-sm">
                    <span>📅 {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d M Y') }}</span>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                        {{ $periode->status === 'Buka' ? 'bg-green-500/20 text-green-100' : 'bg-red-500/20 text-red-100' }}">
                        {{ $periode->status }}
                    </span>
                </div>
            </div>
            <div class="hidden md:block">
                <div class="text-right">
                    <p class="text-blue-100 text-sm">{{ now()->format('l, d F Y') }}</p>
                    <p class="text-blue-200 text-xs">{{ now()->format('H:i') }} WIB</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white/90 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-white/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-600">Total Usulan</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['total_usulan'] }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-xl">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-white/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-600">Disetujui</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['usulan_disetujui'] }}</p>
                    @if($stats['total_usulan'] > 0)
                        <p class="text-xs text-slate-500">{{ number_format(($stats['usulan_disetujui'] / $stats['total_usulan']) * 100, 1) }}%</p>
                    @endif
                </div>
                <div class="p-3 bg-green-100 rounded-xl">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-white/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-600">Ditolak</p>
                    <p class="text-3xl font-bold text-red-600">{{ $stats['usulan_ditolak'] }}</p>
                    @if($stats['total_usulan'] > 0)
                        <p class="text-xs text-slate-500">{{ number_format(($stats['usulan_ditolak'] / $stats['total_usulan']) * 100, 1) }}%</p>
                    @endif
                </div>
                <div class="p-3 bg-red-100 rounded-xl">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-white/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-600">Pending</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $stats['usulan_pending'] }}</p>
                    @if($stats['total_usulan'] > 0)
                        <p class="text-xs text-slate-500">{{ number_format(($stats['usulan_pending'] / $stats['total_usulan']) * 100, 1) }}%</p>
                    @endif
                </div>
                <div class="p-3 bg-yellow-100 rounded-xl">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-wrap gap-4 mb-6">
        <a href="{{ route('backend.admin-univ-usulan.usulan.create', ['jenis' => $jenisUsulan]) }}"
           class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors duration-200 font-medium flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Buat Usulan Baru
        </a>

        <form action="{{ route('backend.admin-univ-usulan.usulan.toggle-periode') }}" method="POST" class="inline">
            @csrf
            <input type="hidden" name="jenis" value="{{ $jenisUsulan }}">
            <button type="submit"
                    class="px-6 py-3 rounded-xl font-medium flex items-center transition-colors duration-200
                    {{ $periode->status === 'Buka' ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-green-600 text-white hover:bg-green-700' }}">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if($periode->status === 'Buka')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                    @endif
                </svg>
                {{ $periode->status === 'Buka' ? 'Tutup Periode' : 'Buka Periode' }}
            </button>
        </form>

        <button class="bg-emerald-600 text-white px-6 py-3 rounded-xl hover:bg-emerald-700 transition-colors duration-200 font-medium flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Export Data
        </button>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Data Table -->
    <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold text-slate-800">Daftar {{ $namaUsulan }}</h3>
                    <p class="text-slate-600 mt-1">Periode: {{ $periode->nama_periode }}</p>
                </div>
                <div class="flex gap-2">
                    <div class="relative">
                        <input type="text"
                               placeholder="Cari usulan..."
                               class="px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <svg class="w-4 h-4 absolute right-3 top-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Pegawai</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Jenis Pegawai</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal Usulan</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $usulan->pegawai->jenis_pegawai }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $usulan->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $usulan->status_usulan === 'Disetujui' ? 'bg-green-100 text-green-800' :
                                       ($usulan->status_usulan === 'Ditolak' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ $usulan->status_usulan }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    <a href="{{ route('backend.admin-univ-usulan.usulan.show', $usulan) }}"
                                       class="text-blue-600 hover:text-blue-900 px-3 py-1 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                        Detail
                                    </a>
                                    <a href="#"
                                       class="text-indigo-600 hover:text-indigo-900 px-3 py-1 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-slate-500">
                                    <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-slate-900">Belum ada usulan</h3>
                                    <p class="mt-1 text-sm text-slate-500">Belum ada usulan untuk {{ $namaUsulan }} pada periode ini.</p>
                                    <div class="mt-6">
                                        <a href="{{ route('backend.admin-univ-usulan.usulan.create', ['jenis' => $jenisUsulan]) }}"
                                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Buat Usulan Pertama
                                        </a>
                                    </div>
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
