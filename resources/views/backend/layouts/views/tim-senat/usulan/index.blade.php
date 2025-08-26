@extends('backend.layouts.roles.tim-senat.app')

@section('title', 'Daftar Usulan - Tim Senat')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-emerald-50/30">
    {{-- Header Section --}}
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6 flex flex-wrap gap-4 justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Daftar Usulan Tim Senat
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Kelola usulan yang direkomendasikan untuk keputusan final senat
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="bg-emerald-100 px-4 py-2 rounded-lg">
                        <span class="text-emerald-800 text-sm font-medium">
                            <i data-lucide="shield-check" class="w-4 h-4 inline mr-1"></i>
                            Tim Senat
                        </span>
                    </div>
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
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <i data-lucide="clock" class="w-6 h-6 text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Menunggu Keputusan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $usulans->where('status_usulan', 'Direkomendasikan')->count() }}</p>
                    </div>
                </div>
            </div>

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
                        <p class="text-sm font-medium text-gray-600">Total Usulan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $usulans->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mb-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Filter Usulan</h3>
                    <p class="text-sm text-gray-500">Filter usulan berdasarkan status dan periode</p>
                </div>
                <div class="flex items-center space-x-4">
                    <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Semua Status</option>
                        <option value="Direkomendasikan">Menunggu Keputusan</option>
                        <option value="Disetujui">Disetujui</option>
                        <option value="Ditolak">Ditolak</option>
                    </select>
                    <button class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i data-lucide="filter" class="w-4 h-4 inline mr-1"></i>
                        Filter
                    </button>
                </div>
            </div>
        </div>

        {{-- Usulan Table --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-600 to-green-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="gavel" class="w-6 h-6 mr-3"></i>
                    Daftar Usulan untuk Keputusan Senat
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
                                Tanggal Direkomendasikan
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($usulans as $usulan)
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
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $periodeInfo = $usulan->getPeriodeInfo('Tim Senat');
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
                                        // Function to get display status based on current status and role
                                        function getDisplayStatus($usulan, $currentRole) {
                                            $status = $usulan->status_usulan;
                                            
                                            // Mapping status berdasarkan alur kerja yang diminta
                                            switch ($status) {
                                                // Status untuk Tim Senat
                                                case 'Direkomendasikan':
                                                    if ($currentRole === 'Tim Senat') {
                                                        return 'Usulan Direkomendasikan oleh Tim Senat';
                                                    }
                                                    break;
                                                
                                                case 'Dikirim ke Sister':
                                                    return 'Usulan Sudah Dikirim ke Sister';
                                                
                                                case 'Perbaikan dari Tim Sister':
                                                    return 'Permintaan Perbaikan Usulan dari Tim Sister';
                                                
                                                default:
                                                    return $status;
                                            }
                                            
                                            return $status;
                                        }
                                        
                                        // Get display status
                                        $displayStatus = getDisplayStatus($usulan, 'Tim Senat');
                                        
                                        // Status colors mapping
                                        $statusColors = [
                                            // Status lama (fallback)
                                            'Diajukan' => 'bg-blue-100 text-blue-800',
                                            'Diusulkan ke Universitas' => 'bg-indigo-100 text-indigo-800',
                                            'Sedang Direview' => 'bg-yellow-100 text-yellow-800',
                                            'Direkomendasikan' => 'bg-purple-100 text-purple-800',
                                            'Disetujui' => 'bg-green-100 text-green-800',
                                            'Ditolak' => 'bg-red-100 text-red-800',
                                            'Perbaikan Usulan' => 'bg-orange-100 text-orange-800',
                                            'Diusulkan ke Sister' => 'bg-purple-100 text-purple-800',
                                            'Perbaikan dari Tim Sister' => 'bg-orange-100 text-orange-800',
                                            
                                            // Status baru
                                            'Usulan Direkomendasikan oleh Tim Senat' => 'bg-purple-100 text-purple-800',
                                            'Usulan Sudah Dikirim ke Sister' => 'bg-blue-100 text-blue-800',
                                            'Permintaan Perbaikan Usulan dari Tim Sister' => 'bg-red-100 text-red-800',
                                        ];
                                        $statusColor = $statusColors[$displayStatus] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                        @if($usulan->status_usulan === 'Direkomendasikan')
                                            <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                        @elseif($usulan->status_usulan === 'Disetujui')
                                            <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                        @elseif($usulan->status_usulan === 'Ditolak')
                                            <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                        @endif
                                        {{ $displayStatus }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @php
                                        $recommendationLog = $usulan->logs()->where('status_baru', 'Direkomendasikan')->first();
                                    @endphp
                                    {{ $recommendationLog ? $recommendationLog->created_at->format('d/m/Y H:i') : ($usulan->created_at ? $usulan->created_at->format('d/m/Y H:i') : 'N/A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('tim-senat.usulan.show', $usulan->id) }}"
                                           class="text-emerald-600 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 px-3 py-1 rounded-lg transition-colors">
                                            <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>
                                            Detail
                                        </a>
                                        @if($usulan->status_usulan === 'Direkomendasikan')
                                            <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded">
                                                Menunggu Keputusan
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <i data-lucide="gavel" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
                                        <p class="text-lg font-medium">Belum ada usulan</p>
                                        <p class="text-sm">Tidak ada usulan yang perlu diputuskan oleh Tim Senat saat ini.</p>
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

        {{-- Information Panel --}}
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i data-lucide="info" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Informasi Tim Senat</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Tim Senat berwenang memberikan keputusan final atas usulan yang telah direkomendasikan oleh Tim Penilai</li>
                            <li>Keputusan dapat berupa "Disetujui" atau "Ditolak" dengan alasan yang jelas</li>
                            <li>Usulan yang disetujui akan diteruskan ke tahap berikutnya sesuai prosedur</li>
                            <li>Usulan yang ditolak akan dikembalikan untuk perbaikan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
