@extends('backend.layouts.admin-fakultas.app')

@section('title', 'Dashboard Admin Fakultas')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Halaman -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Pusat Usulan Fakultas</h1>
            <p class="mt-1 text-sm text-gray-600">
                Selamat datang, <span class="font-medium">{{ Auth::user()->nama_lengkap ?? 'Admin' }}</span>.
                @if($unitKerja)
                    Anda mengelola usulan untuk <span class="font-semibold text-indigo-600">{{ $unitKerja->nama }}</span>.
                @else
                    <span class="text-red-600 font-medium flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Unit kerja tidak ditemukan.
                    </span>
                @endif
            </p>
        </div>

        <!-- Alert & Info Panel -->
        @if(!$unitKerja)
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L10 9.414l1.293-1.293a1 1 0 111.414 1.414L11.414 10l1.293 1.293a1 1 0 01-1.414 1.414L10 10.586l-1.293 1.293a1 1 0 01-1.414-1.414L9.586 10 8.293 8.707z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Konfigurasi Unit Kerja Bermasalah</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>Akun Anda belum dikaitkan dengan unit kerja fakultas. Silakan hubungi Administrator untuk:</p>
                            <ul class="list-disc pl-5 mt-1">
                                <li>Mengatur field Unit Kerja pada akun Anda</li>
                                <li>Memastikan unit kerja tersedia di master data</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Info Panel Sukses -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>{{ $unitKerja->nama }}</strong> |
                            Total periode: {{ $periodeUsulans->total() }} |
                            Total usulan menunggu review: {{ $periodeUsulans->sum('jumlah_pengusul') }} |
                            Total usulan: {{ $periodeUsulans->sum('total_usulan') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Card Utama -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Daftar Periode Usulan</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Angka pada kolom "Review" menunjukkan jumlah usulan yang menunggu verifikasi. Klik tombol aksi untuk melihat semua usulan periode tersebut.
                </p>
            </div>

            <!-- Tabel Data -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Periode</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal Usulan</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Review</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($periodeUsulans as $periode)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $periode->nama_periode }}</div>
                                    <div class="text-sm text-gray-500">Tahun {{ $periode->tahun_periode }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $periode->jenis_usulan }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    {{ $periode->tanggal_mulai->isoFormat('D MMM') }} - {{ $periode->tanggal_selesai->isoFormat('D MMM Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($periode->status == 'Buka')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Buka
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Tutup
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($periode->jumlah_pengusul > 0)
                                        <div class="relative group">
                                            <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full cursor-help">
                                                {{ $periode->jumlah_pengusul }}
                                            </span>
                                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-800 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                                                {{ $periode->jumlah_pengusul }} usulan menunggu review
                                            </div>
                                        </div>
                                    @else
                                        <div class="relative group">
                                            <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-gray-100 bg-gray-600 rounded-full cursor-help">
                                                0
                                            </span>
                                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-800 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                                                Tidak ada usulan
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('admin-fakultas.periode.pendaftar', $periode->id) }}"
                                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                        <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.022 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                        </svg>
                                        @if($periode->jumlah_pengusul > 0)
                                            Review ({{ $periode->jumlah_pengusul }})
                                        @else
                                            Lihat Semua
                                        @endif
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2z" />
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak Ada Periode Usulan</h3>
                                        @if(!$unitKerja)
                                            <p class="mt-1 text-sm text-red-600">Unit kerja tidak ditemukan. Periksa pengaturan akun Anda.</p>
                                        @else
                                            <p class="mt-1 text-sm text-gray-500">
                                                Saat ini tidak ada periode usulan untuk fakultas <strong>{{ $unitKerja->nama }}</strong>.
                                            </p>
                                            <p class="mt-1 text-xs text-gray-400">
                                                Total periode ditemukan: {{ $periodeUsulans->total() }}
                                            </p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($periodeUsulans->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $periodeUsulans->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection


