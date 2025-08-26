@extends('backend.layouts.roles.penilai-universitas.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            Pusat Manajemen Usulan
        </h1>
        <p class="mt-2 text-gray-600">
            Daftar usulan yang ditugaskan kepada Anda untuk dinilai.
        </p>
    </div>

    {{-- Notifikasi Sukses --}}
    @if (session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6" role="alert">
            <div class="flex">
                <div class="py-1"><svg class="h-5 w-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg></div>
                <div>
                    <p class="font-bold text-green-800">Berhasil</p>
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white shadow-xl rounded-2xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">
                    Daftar Usulan yang Ditugaskan
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Usulan kepegawaian yang ditugaskan kepada Anda untuk penilaian.
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Pegawai</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Jenis Pegawai</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nama Sub-Sub Unit Kerja</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Unit Kerja</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Jabatan yang Dituju</th>
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
                                    <div class="text-sm font-medium text-slate-900">{{ $usulan->pegawai->nama_lengkap ?? 'N/A' }}</div>
                                    <div class="text-sm text-slate-500">NIP: {{ $usulan->pegawai->nip ?? 'N/A' }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $usulan->pegawai->jenis_pegawai ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $usulan->pegawai->unitKerja->subUnitKerja->nama ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $usulan->pegawai->unitKerja->subUnitKerja->unitKerja->nama ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $usulan->jabatanTujuan->jabatan ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    // Function to get display status based on current status and role
                                    function getDisplayStatus($usulan, $currentRole) {
                                        $status = $usulan->status_usulan;
                                        
                                        // Mapping status berdasarkan alur kerja yang diminta
                                        switch ($status) {
                                            // Status untuk Penilai Universitas
                                            case 'Menunggu Hasil Penilaian Tim Penilai':
                                                if ($currentRole === 'Penilai Universitas') {
                                                    return 'Usulan Perbaikan dari Penilai Universitas';
                                                }
                                                break;
                                            
                                            case 'Perbaikan Dari Tim Penilai':
                                                if ($currentRole === 'Penilai Universitas') {
                                                    return 'Usulan Perbaikan dari Penilai Universitas';
                                                }
                                                break;
                                            
                                            case 'Usulan Direkomendasi Tim Penilai':
                                                if ($currentRole === 'Penilai Universitas') {
                                                    return 'Usulan Direkomendasi Penilai Universitas';
                                                }
                                                break;
                                            
                                            default:
                                                return $status;
                                        }
                                        
                                        return $status;
                                    }
                                    
                                    // Get display status
                                    $displayStatus = getDisplayStatus($usulan, 'Penilai Universitas');
                                    
                                    // Status colors mapping
                                    $statusColors = [
                                        // Status lama (fallback)
                                        'Disetujui' => 'bg-green-100 text-green-800',
                                        'Ditolak' => 'bg-red-100 text-red-800',
                                        'Menunggu Hasil Penilaian Tim Penilai' => 'bg-yellow-100 text-yellow-800',
                                        
                                        // Status baru
                                        'Usulan Perbaikan dari Penilai Universitas' => 'bg-orange-100 text-orange-800',
                                        'Usulan Direkomendasi Penilai Universitas' => 'bg-purple-100 text-purple-800',
                                    ];
                                    
                                    $statusColor = $statusColors[$displayStatus] ?? 'bg-yellow-100 text-yellow-800';
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                                    {{ $displayStatus }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    <a href="{{ route('penilai-universitas.pusat-usulan.show', $usulan) }}"
                                       class="text-blue-600 hover:text-blue-900 px-3 py-1 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                        Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="text-slate-500">
                                    <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-slate-900">Belum ada usulan</h3>
                                    <p class="mt-1 text-sm text-slate-500">Belum ada usulan yang ditugaskan kepada Anda untuk dinilai.</p>
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
