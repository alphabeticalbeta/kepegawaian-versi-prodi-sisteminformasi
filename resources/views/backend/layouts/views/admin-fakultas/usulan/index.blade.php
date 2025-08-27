@extends('backend.layouts.roles.admin-fakultas.app')

@section('content')
    {{-- Auto-resolve unit kerja jika tidak ada dari controller --}}
    @include('backend.components.usulan._unit-kerja-resolver')

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                Pusat Manajemen Usulan Jabatan
            </h1>
            <p class="mt-2 text-gray-600">
                @if(isset($unitKerja) && $unitKerja)
                    Kelola dan pantau usulan jabatan dari <span class="font-semibold text-indigo-600">{{ $unitKerja->nama }}</span>.
                @else
                    <span class="text-red-600 font-medium">⚠️ Unit kerja tidak ditemukan.</span>
                @endif
            </p>
        </div>

        {{-- Alert Panel untuk Unit Kerja --}}
        @if(!isset($unitKerja) || !$unitKerja)
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6" role="alert">
                <div class="flex">
                    <div class="py-1">
                        <svg class="h-5 w-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L10 9.414l1.293-1.293a1 1 0 111.414 1.414L11.414 10l1.293 1.293a1 1 0 01-1.414 1.414L10 10.586l-1.293 1.293a1 1 0 01-1.414-1.414L9.586 10 8.293 8.707z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-red-800">Konfigurasi Unit Kerja Bermasalah</p>
                        <p class="text-sm text-red-700">Akun Anda belum dikaitkan dengan unit kerja fakultas. Data usulan tidak akan muncul.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Notifikasi Sukses --}}
        @if (session('success'))
            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6" role="alert">
                <div class="flex">
                    <div class="py-1">
                        <svg class="h-5 w-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-green-800">Berhasil</p>
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Info Card Fakultas --}}
        @if($unitKerja)
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6 mb-6">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0H3m4 0v-4a1 1 0 011-1h2a1 1 0 011 1v4m-6 0v-6a1 1 0 011-1h2a1 1 0 011 1v6"/>
                        </svg>
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-lg font-semibold text-blue-900">{{ $unitKerja->nama }}</h3>
                        <p class="text-sm text-blue-700">Unit kerja yang Anda kelola</p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-blue-900">{{ $periodeUsulans->sum('jumlah_pengusul') }}</div>
                        <div class="text-xs text-blue-600">Total Pengusul</div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-gradient-to-r from-red-50 to-orange-50 border border-red-200 rounded-xl p-6 mb-6">
                <div class="flex items-center gap-3">
                    <div class="bg-red-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-red-900">Unit Kerja Tidak Ditemukan</h3>
                        <p class="text-sm text-red-700">Silakan hubungi administrator untuk mengatur unit kerja Anda</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white shadow-xl rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">
                        Daftar Periode Usulan Jabatan
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Periode usulan jabatan yang tersedia untuk fakultas Anda.
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 flex items-center gap-3">
                    {{-- Info Badge --}}
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Admin Fakultas
                    </span>
                </div>
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
                            <th scope="col" class="px-6 py-4 text-center">Pengusul</th>
                            <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($periodeUsulans as $periode)
                            <tr class="bg-white border-b hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap">
                                    {{ $periode->nama_periode }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ \App\Helpers\UsulanHelper::getJenisUsulanBadgeClass($periode->jenis_usulan) }}">
                                        <i data-lucide="{{ \App\Helpers\UsulanHelper::getJenisUsulanIcon($periode->jenis_usulan) }}" class="w-3 h-3 mr-1"></i>
                                        {{ \App\Helpers\UsulanHelper::formatJenisUsulan($periode->jenis_usulan) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->isoFormat('D MMM') }} -
                                    {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->isoFormat('D MMM YYYY') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($periode->tanggal_mulai_perbaikan)
                                        {{ \Carbon\Carbon::parse($periode->tanggal_mulai_perbaikan)->isoFormat('D MMM') }} -
                                        {{ \Carbon\Carbon::parse($periode->tanggal_selesai_perbaikan)->isoFormat('D MMM YYYY') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($periode->status == 'Buka')
                                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            <svg class="w-3 h-3 mr-1 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                                            </svg>
                                            Tutup
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center">
                                        <span class="font-bold text-lg text-gray-800">{{ $periode->jumlah_pengusul }}</span>
                                        <span class="text-xs text-gray-500 ml-1">orang</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center items-center gap-3">
                                        @if($periode->jumlah_pengusul > 0)
                                            <a href="{{ route('admin-fakultas.periode.pendaftar', $periode->id) }}"
                                               class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-md hover:bg-blue-700 transition-colors duration-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                Kelola
                                            </a>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-500 text-xs font-medium rounded-md">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                                </svg>
                                                Kosong
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-white border-b">
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                        @if(!isset($unitKerja) || !$unitKerja)
                                            <h3 class="text-lg font-medium text-red-900 mb-2">Unit Kerja Tidak Dikonfigurasi</h3>
                                            <p class="text-red-600 mb-4">Tidak dapat menampilkan data karena unit kerja tidak ditemukan.</p>
                                            <p class="text-xs text-red-400">Hubungi administrator untuk mengatur field unit_kerja_id pada akun Anda.</p>
                                        @else
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Periode Usulan</h3>
                                            <p class="text-gray-500 mb-4">
                                                Belum ada periode usulan jabatan untuk <strong>{{ $unitKerja->nama }}</strong>.
                                            </p>
                                            <div class="text-xs text-gray-400 space-y-1">
                                                <p>• Hubungi Admin Universitas untuk membuka periode usulan baru</p>
                                                <p>• Atau tunggu hingga periode usulan dibuka</p>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if (method_exists($periodeUsulans, 'hasPages') && $periodeUsulans->hasPages())
                <div class="p-4 border-t border-gray-200">
                    {{ $periodeUsulans->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
