@extends('backend.layouts.roles.tim-senat.app')
@section('title', 'Dashboard Tim Senat')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-red-50 to-amber-50">
    <!-- Header Section -->
    <div class="tim-senat-bg text-white p-8 rounded-2xl mb-8 shadow-2xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Dashboard Tim Senat</h1>
                <p class="text-orange-100">Selamat datang, {{ $user->nama_lengkap }}</p>
                <p class="text-orange-200 text-sm">{{ $user->email }}</p>
            </div>
            <div class="hidden md:block">
                <div class="text-right">
                    <p class="text-orange-100 text-sm">{{ now()->format('l, d F Y') }}</p>
                    <p class="text-orange-200 text-xs">{{ now()->format('H:i') }} WIB</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-600">Total Usulan Dosen</p>
                    <p class="text-3xl font-bold text-orange-600">{{ number_format($stats['total_usulan_dosen']) }}</p>
                </div>
                <div class="p-3 bg-orange-100 rounded-xl">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 01-3 0m3 0V5.292a1 1 0 00-.293-.707l-1.414-1.414A1 1 0 0018 3M21 4H7.414a1 1 0 00-.707.293L5.293 5.707A1 1 0 005 6.414V21"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-600">Pending Review</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ number_format($stats['usulan_pending_review']) }}</p>
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
                    <p class="text-sm text-slate-600">Sudah Direview</p>
                    <p class="text-3xl font-bold text-green-600">{{ number_format($stats['usulan_reviewed']) }}</p>
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
                    <p class="text-sm text-slate-600">Total Dosen</p>
                    <p class="text-3xl font-bold text-blue-600">{{ number_format($stats['total_dosen']) }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-xl">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
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
                    <h3 class="text-xl font-semibold text-slate-800">Usulan Dosen Terbaru</h3>
                    <p class="text-slate-600 mt-1">10 usulan dosen terbaru yang masuk</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($recentUsulans as $usulan)
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors">
                                <div class="flex-1">
                                    <h4 class="font-medium text-slate-900">{{ $usulan->pegawai->nama_lengkap }}</h4>
                                    <p class="text-sm text-slate-600">NIP: {{ $usulan->pegawai->nip }}</p>
                                    <p class="text-sm text-orange-600">{{ $usulan->jabatanTujuan->jabatan ?? 'Jabatan tidak diketahui' }}</p>
                                    <p class="text-xs text-slate-500">{{ $usulan->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-block px-3 py-1 text-xs font-medium rounded-full
                                        {{ $usulan->status_usulan === 'Disetujui' ? 'bg-green-100 text-green-800' :
                                           ($usulan->status_usulan === 'Ditolak' ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-800') }}">
                                        {{ $usulan->status_usulan }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-slate-500 py-8">Belum ada usulan dosen</p>
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
                    <a href="{{ route('tim-senat.rapat-senat.index') }}"
                       class="flex items-center gap-3 p-3 bg-orange-50 hover:bg-orange-100 rounded-xl transition-colors group">
                        <div class="p-2 bg-orange-100 group-hover:bg-orange-200 rounded-lg transition-colors">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <span class="font-medium text-slate-700">Rapat Senat</span>
                    </a>

                    <a href="{{ route('tim-senat.keputusan-senat.index') }}"
                       class="flex items-center gap-3 p-3 bg-red-50 hover:bg-red-100 rounded-xl transition-colors group">
                        <div class="p-2 bg-red-100 group-hover:bg-red-200 rounded-lg transition-colors">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <span class="font-medium text-slate-700">Keputusan Senat</span>
                    </a>
                </div>
            </div>

            <!-- System Info -->
            <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 p-6">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">Informasi Sistem</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">Role Aktif</span>
                        <span class="text-sm font-medium text-orange-600">Tim Senat</span>
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
