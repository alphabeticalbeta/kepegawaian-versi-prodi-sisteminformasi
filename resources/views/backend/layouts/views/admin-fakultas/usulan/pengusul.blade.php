@extends('backend.layouts.roles.admin-fakultas.app')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Header Section --}}
        <div class="mb-8">
            <a href="{{ route('admin-fakultas.dashboard') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 inline-flex items-center mb-4">
                <svg class="h-5 w-5 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Kembali ke Dashboard
            </a>

            <h1 class="text-3xl font-bold text-gray-900">
                Daftar Pengusul
            </h1>
            <div class="mt-2 flex items-center gap-4">
                <p class="text-gray-600">Periode: <span class="font-semibold">{{ $periode->nama_periode }}</span></p>
                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $periode->status == 'Buka' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $periode->status }}
                </span>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6" role="alert">
                <div class="flex">
                    <div class="py-1">
                        <svg class="fill-current h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold">Sukses!</p>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Summary Card --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Pengusul</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $usulans->total() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Usulan Dikirim ke Admin Fakultas</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $usulans->where('status_usulan', 'Diajukan')->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Sedang Direview</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $usulans->where('status_usulan', 'Sedang Direview')->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Perbaikan</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $usulans->whereIn('status_usulan', ['Perbaikan Usulan', 'Dikembalikan', 'Perbaikan dari Kepegawaian Universitas', 'Perbaikan dari Penilai Universitas'])->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Selesai</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $usulans->whereIn('status_usulan', ['Diusulkan ke Universitas', 'Direkomendasikan', 'Disetujui'])->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Table --}}
        <div class="bg-white shadow-xl rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800">
                    Daftar Pengusul Jabatan
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Kelola dan validasi usulan jabatan dari pegawai fakultas Anda.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-4">Pegawai</th>
                            <th scope="col" class="px-6 py-4">Jabatan Saat Ini</th>
                            <th scope="col" class="px-6 py-4">Jabatan Tujuan</th>
                            <th scope="col" class="px-6 py-4">Tanggal Usulan</th>
                            <th scope="col" class="px-6 py-4 text-center">Status</th>
                            <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usulans as $usulan)
                            <tr class="bg-white border-b hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700">
                                                    {{ substr($usulan->pegawai->nama_lengkap, 0, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $usulan->pegawai->nama_lengkap }}</div>
                                            <div class="text-sm text-gray-500">{{ $usulan->pegawai->nip }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $usulan->jabatanLama->jabatan ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $usulan->jabatanTujuan->jabatan ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $usulan->created_at->isoFormat('D MMM YYYY') }}</div>
                                    <div class="text-sm text-gray-500">{{ $usulan->created_at->isoFormat('HH:mm') }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        // Function to get display status based on current status and role
                                        function getDisplayStatus($usulan, $currentRole) {
                                            $status = $usulan->status_usulan;
                                            
                                            // Mapping status berdasarkan alur kerja yang diminta
                                            switch ($status) {
                                                // Status untuk Admin Fakultas
                                                case 'Diajukan':
                                                    return 'Usulan Dikirim ke Admin Fakultas';
                                                
                                                case 'Perbaikan Usulan':
                                                    if ($currentRole === 'Admin Fakultas') {
                                                        return 'Permintaan Perbaikan dari Admin Fakultas';
                                                    }
                                                    break;
                                                
                                                case 'Diusulkan ke Universitas':
                                                    if ($currentRole === 'Admin Fakultas') {
                                                        return 'Usulan Disetujui Admin Fakultas';
                                                    }
                                                    break;
                                                
                                                case 'Perbaikan dari Kepegawaian Universitas':
                                                    if ($currentRole === 'Admin Fakultas') {
                                                        return 'Usulan Perbaikan dari Kepegawaian Universitas';
                                                    }
                                                    break;
                                                
                                                case 'Perbaikan dari Penilai Universitas':
                                                    if ($currentRole === 'Admin Fakultas') {
                                                        return 'Usulan Perbaikan dari Penilai Universitas';
                                                    }
                                                    break;
                                                
                                                default:
                                                    return $status;
                                            }
                                            
                                            return $status;
                                        }
                                        
                                        // Get display status
                                        $displayStatus = getDisplayStatus($usulan, 'Admin Fakultas');
                                        
                                        // Status colors mapping
                                        $statusColors = [
                                            // Status lama (fallback)
                                            'Diajukan' => 'bg-yellow-100 text-yellow-800',
                                            'Sedang Direview' => 'bg-blue-100 text-blue-800',
                                            'Perbaikan Usulan' => 'bg-orange-100 text-orange-800',
                                            'Diusulkan ke Universitas' => 'bg-purple-100 text-purple-800',
                                            'Direkomendasikan' => 'bg-green-100 text-green-800',
                                            'Ditolak' => 'bg-red-100 text-red-800',
                                            
                                            // Status baru
                                            'Usulan Dikirim ke Admin Fakultas' => 'bg-blue-100 text-blue-800',
                                            'Permintaan Perbaikan dari Admin Fakultas' => 'bg-amber-100 text-amber-800',
                                            'Usulan Disetujui Admin Fakultas' => 'bg-green-100 text-green-800',
                                            'Usulan Perbaikan dari Kepegawaian Universitas' => 'bg-red-100 text-red-800',
                                            'Usulan Perbaikan dari Penilai Universitas' => 'bg-orange-100 text-orange-800',
                                        ];
                                        
                                        $statusColor = $statusColors[$displayStatus] ?? 'bg-gray-100 text-gray-800';
                                    @endphp

                                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                        {{ $displayStatus }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('admin-fakultas.usulan.show', $usulan->id) }}"
                                       class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-md hover:bg-indigo-700 transition-colors duration-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-white border-b">
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Pengusul</h3>
                                        <p class="text-gray-500">Belum ada pengusul jabatan untuk periode ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($usulans->hasPages())
                <div class="p-4 border-t border-gray-200">
                    {{ $usulans->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
