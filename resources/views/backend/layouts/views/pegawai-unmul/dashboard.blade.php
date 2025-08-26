@extends('backend.layouts.roles.pegawai-unmul.app')

@section('title', 'Dashboard Pegawai')

@section('content')

    @php
    // Cek apakah ada usulan yang perlu diperbaiki
        $usulanPerluPerbaikan = $usulans->firstWhere('status_usulan', 'Perbaikan Usulan');
    @endphp

    @if($usulanPerluPerbaikan)
        <div class="bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-4 rounded-lg mb-6 shadow-md" role="alert">
            <div class="flex">
                <div class="py-1"><svg class="fill-current h-6 w-6 text-orange-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 5v6h2V5H9zm0 8h2v2H9v-2z"/></svg></div>
                <div>
                    <p class="font-bold">Aksi Diperlukan</p>
                    <p class="text-sm">Satu atau lebih usulan Anda telah dikembalikan dan memerlukan perbaikan. Silakan periksa detailnya di bawah.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Notifikasi sudah ditangani oleh component flash di layout base --}}

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                Riwayat Usulan Saya
            </h1>
            <p class="mt-2 text-gray-600">
                Pantau status dan riwayat semua usulan yang telah Anda ajukan.
            </p>
        </div>

        <div class="bg-white shadow-xl rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">
                        Daftar Usulan
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Berikut adalah semua usulan yang pernah Anda buat.
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-4">Jenis Usulan</th>
                            <th scope="col" class="px-6 py-4">Periode</th>
                            <th scope="col" class="px-6 py-4">Tanggal Pengajuan</th>
                            <th scope="col" class="px-6 py-4 text-center">Status</th>
                            <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($usulans as $usulan)
                            <tr class="bg-white border-b hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap capitalize">{{ $usulan->jenis_usulan }}</td>
                                <td class="px-6 py-4">{{ $usulan->periodeUsulan->nama_periode }}</td>
                                <td class="px-6 py-4">{{ $usulan->created_at->isoFormat('D MMMM YYYY') }}</td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        // Function to get display status based on current status and role
                                        function getDisplayStatus($usulan, $currentRole) {
                                            $status = $usulan->status_usulan;
                                            
                                            // Mapping status berdasarkan alur kerja yang diminta
                                            switch ($status) {
                                                // Status untuk Pegawai
                                                case 'Diajukan':
                                                    return 'Usulan Dikirim ke Admin Fakultas';
                                                
                                                case 'Perbaikan Usulan':
                                                    if ($currentRole === 'Pegawai') {
                                                        return 'Usulan Perbaikan dari Admin Fakultas';
                                                    }
                                                    break;
                                                
                                                case 'Perbaikan dari Kepegawaian Universitas':
                                                    if ($currentRole === 'Pegawai') {
                                                        return 'Usulan Perbaikan dari Kepegawaian Universitas';
                                                    }
                                                    break;
                                                
                                                case 'Perbaikan dari Penilai Universitas':
                                                    if ($currentRole === 'Pegawai') {
                                                        return 'Usulan Perbaikan dari Penilai Universitas';
                                                    }
                                                    break;
                                                
                                                case 'Perbaikan dari Tim Sister':
                                                    return 'Permintaan Perbaikan Usulan dari Tim Sister';
                                                
                                                case 'Dikirim ke Sister':
                                                    return 'Usulan Sudah Dikirim ke Sister';
                                                
                                                default:
                                                    return $status;
                                            }
                                            
                                            return $status;
                                        }
                                        
                                        // Get display status
                                        $displayStatus = getDisplayStatus($usulan, 'Pegawai');
                                        
                                        // Status colors mapping
                                        $statusColors = [
                                            // Status lama (fallback)
                                            'Draft' => 'bg-gray-100 text-gray-800',
                                            'Diajukan' => 'bg-blue-100 text-blue-800',
                                            'Sedang Direview' => 'bg-yellow-100 text-yellow-800',
                                            'Perbaikan Usulan' => 'bg-orange-100 text-orange-800',
                                            'Dikembalikan' => 'bg-red-100 text-red-800',
                                            'Disetujui' => 'bg-green-100 text-green-800',
                                            'Direkomendasikan' => 'bg-purple-100 text-purple-800',
                                            'Ditolak' => 'bg-red-100 text-red-800',
                                            
                                            // Status baru
                                            'Usulan Dikirim ke Admin Fakultas' => 'bg-blue-100 text-blue-800',
                                            'Usulan Perbaikan dari Admin Fakultas' => 'bg-amber-100 text-amber-800',
                                            'Usulan Perbaikan dari Kepegawaian Universitas' => 'bg-red-100 text-red-800',
                                            'Usulan Perbaikan dari Penilai Universitas' => 'bg-orange-100 text-orange-800',
                                            'Permintaan Perbaikan Usulan dari Tim Sister' => 'bg-red-100 text-red-800',
                                            'Usulan Sudah Dikirim ke Sister' => 'bg-blue-100 text-blue-800',
                                        ];
                                        
                                        $statusColor = $statusColors[$displayStatus] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                        {{ $displayStatus }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center space-x-2">
                                        @php
                                            // Tentukan route berdasarkan jenis usulan
                                            $routeName = match($usulan->jenis_usulan) {
                                                'Usulan Jabatan' => 'pegawai-unmul.usulan-jabatan',
                                                'Usulan Kepangkatan' => 'pegawai-unmul.usulan-kepangkatan',
                                                'Usulan ID SINTA ke SISTER' => 'pegawai-unmul.usulan-id-sinta-sister',
                                                'Usulan Laporan LKD' => 'pegawai-unmul.usulan-laporan-lkd',
                                                'Usulan Laporan SERDOS' => 'pegawai-unmul.usulan-laporan-serdos',
                                                'Usulan NUPTK' => 'pegawai-unmul.usulan-nuptk',
                                                'Usulan Pencantuman Gelar' => 'pegawai-unmul.usulan-pencantuman-gelar',
                                                'Usulan Pengaktifan Kembali' => 'pegawai-unmul.usulan-pengaktifan-kembali',
                                                'Usulan Pensiun' => 'pegawai-unmul.usulan-pensiun',
                                                'Usulan Penyesuaian Masa Kerja' => 'pegawai-unmul.usulan-penyesuaian-masa-kerja',
                                                'Usulan Presensi' => 'pegawai-unmul.usulan-presensi',
                                                'Usulan Satyalancana' => 'pegawai-unmul.usulan-satyalancana',
                                                'Usulan Tugas Belajar' => 'pegawai-unmul.usulan-tugas-belajar',
                                                'Usulan Ujian Dinas & Ijazah' => 'pegawai-unmul.usulan-ujian-dinas-ijazah',
                                                default => 'pegawai-unmul.usulan-jabatan'
                                            };

                                            // Tentukan apakah bisa edit atau hanya lihat detail
                                            $canEdit = in_array($usulan->status_usulan, ['Draft', 'Perbaikan Usulan', 'Dikembalikan ke Pegawai']);
                                            $actionRoute = $canEdit ? $routeName . '.edit' : $routeName . '.show';
                                        @endphp

                                        @if($usulan->status_usulan == 'Perbaikan Usulan')
                                            <a href="{{ route($actionRoute, $usulan) }}"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-orange-600 bg-orange-50 border border-orange-200 rounded-lg hover:bg-orange-100 hover:text-orange-700 transition-colors duration-200">
                                                <i data-lucide="edit" class="w-3 h-3 mr-1"></i>
                                                Perbaiki Usulan
                                            </a>
                                        @else
                                            <a href="{{ route($actionRoute, $usulan) }}"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 hover:text-indigo-700 transition-colors duration-200">
                                                <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                                Detail
                                            </a>
                                        @endif
                                        <a href="{{ route($routeName . '.logs', $usulan) }}"
                                           target="_blank"
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-gray-700 transition-colors duration-200">
                                            <i data-lucide="history" class="w-3 h-3 mr-1"></i>
                                            Log
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-white border-b">
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i data-lucide="file-x" class="w-12 h-12 text-gray-300 mb-4"></i>
                                        <p class="text-lg font-medium text-gray-400">Belum ada usulan</p>
                                        <p class="text-sm text-gray-400">Anda belum pernah membuat usulan apapun.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($usulans->hasPages())
                <div class="p-4 border-t border-gray-200">
                {{ $usulans->links() }}
                </div>
            @endif
        </div>


    </div>
@endsection




