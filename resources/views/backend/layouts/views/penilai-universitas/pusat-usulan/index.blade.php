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
                    Daftar Usulan yang Ditugaskan
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Berikut adalah daftar usulan yang telah ditugaskan kepada Anda untuk direview, termasuk history review yang telah selesai.
                </p>
                <div class="flex gap-4 mt-2 text-xs">
                    <div class="flex items-center gap-1">
                        <span class="w-3 h-3 bg-yellow-100 border border-yellow-300 rounded-full"></span>
                        <span class="text-gray-600">Sedang Direview</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-3 h-3 bg-purple-100 border border-purple-300 rounded-full"></span>
                        <span class="text-gray-600">Menunggu Review Admin</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4">Nama Pengusul</th>
                        <th scope="col" class="px-6 py-4">NIP</th>
                        <th scope="col" class="px-6 py-4">Jenis Usulan</th>
                        <th scope="col" class="px-6 py-4">Periode</th>
                        <th scope="col" class="px-6 py-4 text-center">Status</th>
                        <th scope="col" class="px-6 py-4 text-center">Tanggal Ditugaskan</th>
                        <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($usulans as $usulan)
                        <tr class="bg-white border-b hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap">{{ $usulan->pegawai->nama_lengkap ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $usulan->pegawai->nip ?? 'N/A' }}</td>
                            <td class="px-6 py-4 capitalize">{{ $usulan->jenis_usulan }}</td>
                            <td class="px-6 py-4">{{ $usulan->periodeUsulan->nama_periode ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($usulan->status_usulan == 'Sedang Direview')
                                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                        Sedang Direview
                                    </span>
                                @elseif($usulan->status_usulan == 'Menunggu Review Admin Univ')
                                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                        Menunggu Review Admin
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $usulan->status_usulan }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-500">
                                @if($usulan->status_usulan == 'Menunggu Review Admin Univ')
                                    @php
                                        $penilaiReview = $usulan->validasi_data['tim_penilai'] ?? [];
                                        $reviewDate = null;
                                        if (isset($penilaiReview['perbaikan_usulan']['tanggal_return'])) {
                                            $reviewDate = $penilaiReview['perbaikan_usulan']['tanggal_return'];
                                        } elseif (isset($penilaiReview['tanggal_rekomendasi'])) {
                                            $reviewDate = $penilaiReview['tanggal_rekomendasi'];
                                        }
                                    @endphp
                                    @if($reviewDate)
                                        <div class="text-xs">
                                            <div class="font-medium text-purple-600">Review Selesai</div>
                                            <div>{{ \Carbon\Carbon::parse($reviewDate)->isoFormat('D MMM Y, HH:mm') }}</div>
                                        </div>
                                    @else
                                        {{ $usulan->created_at ? \Carbon\Carbon::parse($usulan->created_at)->isoFormat('D MMM Y, HH:mm') : 'N/A' }}
                                    @endif
                                @else
                                    {{ $usulan->created_at ? \Carbon\Carbon::parse($usulan->created_at)->isoFormat('D MMM Y, HH:mm') : 'N/A' }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    @if($usulan->status_usulan == 'Menunggu Review Admin Univ')
                                        @php
                                            $penilaiReview = $usulan->validasi_data['tim_penilai'] ?? [];
                                            $reviewType = '';
                                            if (isset($penilaiReview['perbaikan_usulan'])) {
                                                $reviewType = 'Perbaikan Usulan';
                                            } elseif (isset($penilaiReview['recommendation']) && $penilaiReview['recommendation'] === 'direkomendasikan') {
                                                $reviewType = 'Direkomendasikan';
                                            }
                                        @endphp
                                        @if($reviewType)
                                            <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-800">
                                                {{ $reviewType }}
                                            </span>
                                        @endif
                                    @endif
                                    <a href="{{ route('penilai-universitas.pusat-usulan.show', $usulan->id) }}" class="font-medium text-blue-600 hover:text-blue-900" title="Review Usulan">
                                        @if($usulan->status_usulan == 'Menunggu Review Admin Univ')
                                            Lihat Detail
                                        @else
                                            Review
                                        @endif
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="bg-white border-b">
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    <p>Belum ada usulan yang ditugaskan kepada Anda.</p>
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
