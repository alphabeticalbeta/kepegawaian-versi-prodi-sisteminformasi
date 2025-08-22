@extends('backend.layouts.roles.tim-penilai.app')

@section('title', 'Daftar Usulan - Tim Penilai')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/30">
    {{-- Header Section --}}
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6 flex flex-wrap gap-4 justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Daftar Usulan Tim Penilai
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Kelola usulan yang sedang dalam proses penilaian
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <i data-lucide="clock" class="w-6 h-6 text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Menunggu Penilaian</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $usulans->where('status_usulan', 'Sedang Direview')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <i data-lucide="thumbs-up" class="w-6 h-6 text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Direkomendasikan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $usulans->where('status_usulan', 'Direkomendasikan')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="bg-red-100 p-3 rounded-lg">
                        <i data-lucide="x-circle" class="w-6 h-6 text-red-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Dikembalikan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $usulans->where('status_usulan', 'Perbaikan Usulan')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Usulan Table --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="list" class="w-6 h-6 mr-3"></i>
                    Daftar Usulan
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pegawai
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jenis Usulan
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Periode
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($usulans as $usulan)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                                <i data-lucide="user" class="w-5 h-5 text-purple-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $usulan->pegawai->nama_lengkap ?? 'N/A' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $usulan->pegawai->nip ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $usulan->jenis_usulan }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $periodeInfo = $usulan->getPeriodeInfo('Tim Penilai');
                                    @endphp
                                    @if($periodeInfo['status'] === 'accessible')
                                        <div class="text-sm text-gray-900">{{ $periodeInfo['nama_periode'] }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $periodeInfo['tanggal_mulai'] ? \Carbon\Carbon::parse($periodeInfo['tanggal_mulai'])->format('d/m/Y') : 'N/A' }} - 
                                            {{ $periodeInfo['tanggal_selesai'] ? \Carbon\Carbon::parse($periodeInfo['tanggal_selesai'])->format('d/m/Y') : 'N/A' }}
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-400">{{ $periodeInfo['nama_periode'] }}</div>
                                        <div class="text-xs text-gray-300">{{ $periodeInfo['message'] }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'Diajukan' => 'bg-blue-100 text-blue-800',
                                            'Diusulkan ke Universitas' => 'bg-indigo-100 text-indigo-800',
                                            'Sedang Direview' => 'bg-yellow-100 text-yellow-800',
                                            'Direkomendasikan' => 'bg-purple-100 text-purple-800',
                                            'Disetujui' => 'bg-green-100 text-green-800',
                                            'Ditolak' => 'bg-red-100 text-red-800',
                                            'Perbaikan Usulan' => 'bg-orange-100 text-orange-800',
                                        ];
                                        $statusColor = $statusColors[$usulan->status_usulan] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                        {{ $usulan->status_usulan }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $usulan->created_at ? $usulan->created_at->format('d/m/Y H:i') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('tim-penilai.usulan.show', $usulan->id) }}"
                                       class="text-purple-600 hover:text-purple-900 bg-purple-50 hover:bg-purple-100 px-3 py-1 rounded-lg transition-colors">
                                        <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
                                        <p class="text-lg font-medium">Belum ada usulan</p>
                                        <p class="text-sm">Tidak ada usulan yang perlu dinilai saat ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($usulans->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $usulans->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
