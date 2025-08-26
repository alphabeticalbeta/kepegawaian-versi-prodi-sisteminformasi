@extends('backend.layouts.roles.admin-fakultas.app')

@section('title', 'Dashboard Usulan Jabatan')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Halaman -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Usulan Jabatan</h1>
            <p class="mt-1 text-sm text-gray-600">
                Selamat datang, <span class="font-medium">{{ Auth::user()->nama_lengkap ?? 'Admin' }}</span>.
                @if($unitKerja)
                    Anda mengelola usulan jabatan untuk <span class="font-semibold text-indigo-600">{{ $unitKerja->nama }}</span>.
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
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Periode -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i data-lucide="calendar" class="w-6 h-6"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Periode</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $statistics['total_periode'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Menunggu Validasi -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <i data-lucide="clock" class="w-6 h-6"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Menunggu Validasi</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $statistics['menunggu_validasi'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Dikirim ke Universitas -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                            <i data-lucide="send" class="w-6 h-6"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Dikirim ke Universitas</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $statistics['dikirim_universitas'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Usulan -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i data-lucide="file-text" class="w-6 h-6"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Usulan</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $statistics['total_usulan'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Perbaikan -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                            <i data-lucide="edit-3" class="w-6 h-6"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Perbaikan</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $statistics['perbaikan'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Disetujui -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i data-lucide="check-circle" class="w-6 h-6"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Disetujui</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $statistics['disetujui'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Ditolak -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-600">
                            <i data-lucide="x-circle" class="w-6 h-6"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Ditolak</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $statistics['ditolak'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

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
                            Total periode jabatan: {{ $statistics['total_periode'] }} |
                            Menunggu validasi: {{ $statistics['menunggu_validasi'] }} |
                            Total usulan: {{ $statistics['total_usulan'] }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Card Utama -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Daftar Periode Usulan Jabatan</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Berikut adalah daftar periode usulan jabatan yang tersedia. Periode yang sudah tutup tetap ditampilkan jika ada usulan dari fakultas Anda (sebagai history). Klik tombol aksi untuk melihat detail usulan periode tersebut.
                </p>
            </div>

            <!-- Tabel Data -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Periode</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal Usulan</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Menunggu Validasi</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Dikirim ke Universitas</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Perbaikan</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Usulan</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($periodeUsulans as $periode)
                            @php
                                // Cek apakah periode sudah tutup
                                $isPeriodClosed = $periode->status !== 'Buka' || now()->gt($periode->tanggal_selesai);
                                // Cek apakah periode memiliki usulan dari fakultas ini (untuk history)
                                $hasUsulan = $periode->total_usulan > 0;
                            @endphp
                            <tr class="{{ $isPeriodClosed && !$hasUsulan ? 'opacity-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $periode->nama_periode }}</div>
                                    <div class="text-sm text-gray-500">Tahun {{ $periode->tahun_periode }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d/m/Y') }} -
                                        {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d/m/Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($isPeriodClosed)
                                        @if($hasUsulan)
                                            <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                                Periode Tutup (History)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 border border-gray-200">
                                                <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                                Periode Tutup
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 border border-green-200">
                                            <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                            Periode Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($periode->menunggu_validasi > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                            {{ $periode->menunggu_validasi }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400">0</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($periode->dikirim_universitas > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 border border-indigo-200">
                                            {{ $periode->dikirim_universitas }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400">0</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($periode->perbaikan > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">
                                            {{ $periode->perbaikan }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400">0</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($periode->total_usulan > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                            {{ $periode->total_usulan }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400">0</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    @if($hasUsulan)
                                        {{-- Ada usulan, tampilkan tombol Lihat Usulan --}}
                                        <a href="{{ route('admin-fakultas.periode.pendaftar', $periode->id) }}"
                                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 shadow-sm">
                                            <i data-lucide="eye" class="w-4 h-4 mr-2"></i>
                                            Lihat Usulan
                                        </a>
                                    @else
                                        @if(!$isPeriodClosed)
                                            {{-- Periode masih aktif tapi belum ada usulan --}}
                                            <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 border border-gray-200 rounded-lg">
                                                <i data-lucide="clock" class="w-4 h-4 mr-2"></i>
                                                Belum Ada Usulan
                                            </span>
                                        @else
                                            {{-- Periode sudah tutup dan tidak ada usulan --}}
                                            <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-50 border border-gray-100 rounded-lg">
                                                <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i>
                                                Tidak Ada Usulan
                                            </span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    <div class="flex flex-col items-center py-8">
                                        <i data-lucide="file-x" class="w-12 h-12 text-gray-300 mb-4"></i>
                                        <p class="text-lg font-medium text-gray-900 mb-2">Tidak ada periode usulan jabatan</p>
                                        <p class="text-sm text-gray-500">Belum ada periode usulan jabatan yang tersedia saat ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($periodeUsulans->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $periodeUsulans->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
