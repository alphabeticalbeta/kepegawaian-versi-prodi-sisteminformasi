@extends('backend.layouts.roles.penilai-universitas.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            Pusat Manajemen Usulan
        </h1>
        <p class="mt-2 text-gray-600">
            Kelola periode, lihat pendaftar, dan pantau semua jenis usulan dari satu tempat.
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
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-between items-start sm:items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">
                    Daftar Periode Usulan
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Berikut adalah semua periode usulan yang telah dibuat.
                </p>
            </div>
            <a href="{{ route('backend.admin-univ-usulan.periode-usulan.create', ['jenis' => 'jabatan']) }}" class="mt-4 sm:mt-0 inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-indigo-700 hover:to-purple-700 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Periode
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4">Nama Periode</th>
                        <th scope="col" class="px-6 py-4">Jenis Usulan</th>
                        <th scope="col" class="px-6 py-4">Tanggal Usulan</th>
                        <th scope="col" class="px-6 py-4">Tanggal Perbaikan</th>
                        <th scope="col" class="px-6 py-4 text-center">Status</th>
                        <th scope="col" class="px-6 py-4 text-center">Pendaftar</th>
                        <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($periodeUsulans as $periode)
                        <tr class="bg-white border-b hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap">{{ $periode->nama_periode }}</td>
                            <td class="px-6 py-4 capitalize">{{ $periode->jenis_usulan }}</td>
                            <td class="px-6 py-4">{{ \Carbon\Carbon::parse($periode->tanggal_mulai)->isoFormat('D MMM') }} - {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->isoFormat('D MMM YYYY') }}</td>
                            <td class="px-6 py-4">
                                @if($periode->tanggal_mulai_perbaikan)
                                    {{ \Carbon\Carbon::parse($periode->tanggal_mulai_perbaikan)->isoFormat('D MMM') }} - {{ \Carbon\Carbon::parse($periode->tanggal_selesai_perbaikan)->isoFormat('D MMM YYYY') }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($periode->status == 'Buka')
                                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Buka</span>
                                @else
                                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Tutup</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-gray-800">{{ $periode->usulans_count }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center items-center gap-4">
                                    <a href="{{ route('backend.admin-univ-usulan.periode-usulan.pendaftar', $periode->id) }}" class="font-medium text-blue-600 hover:text-blue-900" title="Lihat Pendaftar" class="font-medium text-blue-600 hover:text-blue-900" title="Lihat Pendaftar">Lihat</a>
                                    <a href="{{ route('backend.admin-univ-usulan.periode-usulan.edit', $periode->id) }}" class="font-medium text-indigo-600 hover:text-indigo-900" title="Edit Periode">Edit</a>

                                    @if($periode->usulans_count > 0)
                                        <span class="font-medium text-gray-400 cursor-not-allowed" title="Tidak dapat dihapus karena sudah ada pendaftar">Hapus</span>
                                    @else
                                        <form action="{{ route('backend.admin-univ-usulan.periode-usulan.destroy', $periode->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus data ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-medium text-red-600 hover:text-red-900" title="Hapus Periode">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="bg-white border-b">
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    <p>Belum ada data periode usulan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($periodeUsulans->hasPages())
            <div class="p-4 border-t border-gray-200">
               {{ $periodeUsulans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
