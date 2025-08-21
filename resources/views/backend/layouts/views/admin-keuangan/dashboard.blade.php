@extends('backend.layouts.roles.admin-keuangan.app')
@section('title', 'Dashboard Admin Keuangan')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50">
    <!-- Header Section -->
    <div class="admin-keuangan-bg text-white p-8 rounded-2xl mb-8 shadow-2xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Dashboard Admin Keuangan</h1>
                <p class="text-amber-100">Selamat datang, {{ $user->nama_lengkap }}</p>
                <p class="text-amber-200 text-sm">{{ $user->email }}</p>
            </div>
            <div class="hidden md:block">
                <div class="text-right">
                    <p class="text-amber-100 text-sm">{{ now()->format('l, d F Y') }}</p>
                    <p class="text-amber-200 text-xs">{{ now()->format('H:i') }} WIB</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-600">Total Usulan</p>
                    <p class="text-3xl font-bold text-amber-600">{{ number_format($stats['total_usulan']) }}</p>
                </div>
                <div class="p-3 bg-amber-100 rounded-xl">
                    <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-600">Menunggu Verifikasi</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ number_format($stats['usulan_pending']) }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-xl">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-600">Disetujui</p>
                    <p class="text-3xl font-bold text-green-600">{{ number_format($stats['usulan_approved']) }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-xl">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-600">Ditolak</p>
                    <p class="text-3xl font-bold text-red-600">{{ number_format($stats['usulan_rejected']) }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-xl">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2">
            <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 overflow-hidden">
                <div class="p-6 border-b border-slate-200">
                    <h3 class="text-xl font-semibold text-slate-800">Usulan Terbaru</h3>
                    <p class="text-slate-600 mt-1">10 usulan terbaru yang masuk</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($recentUsulans as $usulan)
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors">
                                <div class="flex-1">
                                    <h4 class="font-medium text-slate-900">{{ $usulan->pegawai->nama_lengkap }}</h4>
                                    <p class="text-sm text-slate-600">NIP: {{ $usulan->pegawai->nip }}</p>
                                    <p class="text-xs text-slate-500">{{ $usulan->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-block px-3 py-1 text-xs font-medium rounded-full
                                        {{ $usulan->status_usulan === 'Disetujui' ? 'bg-green-100 text-green-800' :
                                           ($usulan->status_usulan === 'Ditolak' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ $usulan->status_usulan }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-slate-500 py-8">Belum ada usulan</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 p-6">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">Aksi Cepat</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin-keuangan.laporan-keuangan.index') }}"
                       class="flex items-center gap-3 p-3 bg-amber-50 hover:bg-amber-100 rounded-xl transition-colors group">
                        <div class="p-2 bg-amber-100 group-hover:bg-amber-200 rounded-lg transition-colors">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <span class="font-medium text-slate-700">Laporan Keuangan</span>
                    </a>

                    <a href="{{ route('admin-keuangan.verifikasi-dokumen.index') }}"
                       class="flex items-center gap-3 p-3 bg-orange-50 hover:bg-orange-100 rounded-xl transition-colors group">
                        <div class="p-2 bg-orange-100 group-hover:bg-orange-200 rounded-lg transition-colors">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="font-medium text-slate-700">Verifikasi Dokumen</span>
                    </a>
                </div>
            </div>

            <!-- System Info -->
            <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 p-6">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">Informasi Sistem</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">Role Aktif</span>
                        <span class="text-sm font-medium text-amber-600">Admin Keuangan</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">Status</span>
                        <span class="inline-block w-2 h-2 bg-green-500 rounded-full"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">Last Login</span>
                        <span class="text-sm text-slate-700">{{ now()->format('H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
