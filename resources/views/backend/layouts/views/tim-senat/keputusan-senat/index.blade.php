@extends('backend.layouts.roles.tim-senat.app')

@section('title', 'Keputusan Senat - Tim Senat')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-emerald-50/30">
    {{-- Header Section --}}
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6 flex flex-wrap gap-4 justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Keputusan Senat
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Riwayat dan dokumentasi keputusan senat
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <button class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i data-lucide="download" class="w-4 h-4 inline mr-1"></i>
                        Export Laporan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-lg">
                        <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Disetujui</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $usulans->where('status_usulan', 'Disetujui')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="bg-red-100 p-3 rounded-lg">
                        <i data-lucide="x-circle" class="w-6 h-6 text-red-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Ditolak</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $usulans->where('status_usulan', 'Ditolak')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <i data-lucide="file-text" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Keputusan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $usulans->whereIn('status_usulan', ['Disetujui', 'Ditolak'])->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <i data-lucide="calendar" class="w-6 h-6 text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Bulan Ini</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $usulans->whereIn('status_usulan', ['Disetujui', 'Ditolak'])->where('updated_at', '>=', now()->startOfMonth())->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mb-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Filter Keputusan</h3>
                    <p class="text-sm text-gray-500">Filter berdasarkan periode dan status keputusan</p>
                </div>
                <div class="flex items-center space-x-4">
                    <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Semua Status</option>
                        <option value="Disetujui">Disetujui</option>
                        <option value="Ditolak">Ditolak</option>
                    </select>
                    <input type="month" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" value="{{ now()->format('Y-m') }}">
                    <button class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i data-lucide="filter" class="w-4 h-4 inline mr-1"></i>
                        Filter
                    </button>
                </div>
            </div>
        </div>

        {{-- Decisions Table --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-600 to-green-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="gavel" class="w-6 h-6 mr-3"></i>
                    Riwayat Keputusan Senat
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
                                Keputusan
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal Keputusan
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Alasan
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($usulans->whereIn('status_usulan', ['Disetujui', 'Ditolak'])->sortByDesc('updated_at') as $usulan)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center">
                                                <i data-lucide="user" class="w-5 h-5 text-emerald-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $usulan->pegawai->nama_lengkap ?? 'N/A' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $usulan->pegawai->nip ?? 'N/A' }}
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                {{ $usulan->pegawai->unitKerja->nama ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $usulan->jenis_usulan }}</div>
                                    @if($usulan->jabatanLama && $usulan->jabatanTujuan)
                                        <div class="text-xs text-gray-500">
                                            {{ $usulan->jabatanLama->jabatan }} → {{ $usulan->jabatanTujuan->jabatan }}
                                        </div>
                                    @endif
                                    @php
                                        $periodeInfo = $usulan->getPeriodeInfo('Tim Senat');
                                    @endphp
                                    @if($periodeInfo['status'] === 'accessible')
                                        <div class="text-xs text-gray-400">
                                            Periode: {{ $periodeInfo['nama_periode'] }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($usulan->status_usulan === 'Disetujui')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                            Disetujui
                                        </span>
                                    @elseif($usulan->status_usulan === 'Ditolak')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                            Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $usulan->updated_at ? $usulan->updated_at->format('d/m/Y H:i') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        @if($usulan->status_usulan === 'Disetujui')
                                            Usulan memenuhi semua persyaratan dan direkomendasikan untuk disetujui
                                        @elseif($usulan->status_usulan === 'Ditolak')
                                            Usulan tidak memenuhi persyaratan yang ditetapkan
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('tim-senat.usulan.show', $usulan->id) }}"
                                           class="text-emerald-600 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 px-3 py-1 rounded-lg transition-colors">
                                            <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>
                                            Detail
                                        </a>
                                        <button class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-lg transition-colors">
                                            <i data-lucide="download" class="w-4 h-4 inline mr-1"></i>
                                            PDF
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <i data-lucide="gavel" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
                                        <p class="text-lg font-medium">Belum ada keputusan</p>
                                        <p class="text-sm">Tim Senat belum memberikan keputusan apapun.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($usulans->whereIn('status_usulan', ['Disetujui', 'Ditolak'])->count() > 10)
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $usulans->links() }}
                </div>
            @endif
        </div>

        {{-- Summary Report --}}
        <div class="mt-8 bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Ringkasan Keputusan</h3>
                <i data-lucide="bar-chart-3" class="w-5 h-5 text-emerald-600"></i>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $usulans->where('status_usulan', 'Disetujui')->count() }}</div>
                    <div class="text-sm text-gray-600">Total Disetujui</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-600">{{ $usulans->where('status_usulan', 'Ditolak')->count() }}</div>
                    <div class="text-sm text-gray-600">Total Ditolak</div>
                </div>
                <div class="text-center">
                    @php
                        $total = $usulans->whereIn('status_usulan', ['Disetujui', 'Ditolak'])->count();
                        $approved = $usulans->where('status_usulan', 'Disetujui')->count();
                        $percentage = $total > 0 ? round(($approved / $total) * 100, 1) : 0;
                    @endphp
                    <div class="text-2xl font-bold text-blue-600">{{ $percentage }}%</div>
                    <div class="text-sm text-gray-600">Tingkat Persetujuan</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
