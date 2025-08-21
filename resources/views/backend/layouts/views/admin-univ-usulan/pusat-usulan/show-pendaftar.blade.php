@extends('backend.layouts.roles.admin-univ-usulan.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-8">
        <div class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
            <a href="{{ route('backend.admin-univ-usulan.pusat-usulan.index') }}" class="hover:text-gray-700 transition-colors">
                Manajemen Usulan
            </a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-gray-900 font-medium">
                Daftar Pendaftar
            </span>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">
            {{ $periode->nama_periode }}
        </h1>
        <p class="mt-2 text-gray-600">
            Daftar pegawai yang telah mengajukan usulan <span class="font-semibold capitalize">{{ $periode->jenis_usulan }}</span> pada periode ini.
        </p>
    </div>

    <div class="bg-white shadow-xl rounded-2xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">
                Daftar Pendaftar ({{ $usulans->total() }})
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4">Nama Pegawai</th>
                        <th scope="col" class="px-6 py-4">NIP</th>
                        <th scope="col" class="px-6 py-4">Jabatan Saat Ini</th>
                        <th scope="col" class="px-6 py-4">Jabatan Tujuan</th>
                        <th scope="col" class="px-6 py-4 text-center">Tgl. Usulan</th>
                        <th scope="col" class="px-6 py-4 text-center">Status</th>
                        <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($usulans as $usulan)
                        <tr class="bg-white border-b hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap">{{ $usulan->pegawai->nama_lengkap }}</td>
                            <td class="px-6 py-4">{{ $usulan->pegawai->nip }}</td>
                            <td class="px-6 py-4">{{ $usulan->jabatanLama?->jabatan ?? 'Tidak Ada' }}</td>
                            <td class="px-6 py-4">{{ $usulan->jabatanTujuan->jabatan ?? 'Tidak Ada' }}</td>
                            <td class="px-6 py-4 text-center">{{ $usulan->created_at->isoFormat('D MMM YYYY') }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $usulan->status_usulan }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('backend.admin-univ-usulan.pusat-usulan.show', $usulan->id) }}" class="font-medium text-indigo-600 hover:text-indigo-900">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr class="bg-white border-b">
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                Belum ada pendaftar untuk periode ini.
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
