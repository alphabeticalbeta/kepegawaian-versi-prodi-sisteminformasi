@extends('backend.layouts.roles.pegawai-unmul.app')

@section('title', 'Detail Usulan Presensi')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/30">
    {{-- Header Section --}}
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6 flex flex-wrap gap-4 justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Detail Usulan Presensi
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Informasi lengkap Usulan Presensi
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('pegawai-unmul.dashboard-pegawai-unmul') }}"
                       class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Status Badge --}}
        <div class="mb-6">
            @php
                $statusColors = [
                    'Draft' => 'bg-gray-100 text-gray-800 border-gray-300',
                    'Diajukan' => 'bg-blue-100 text-blue-800 border-blue-300',
                    'Sedang Direview' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                    'Disetujui' => 'bg-green-100 text-green-800 border-green-300',
                    'Direkomendasikan' => 'bg-purple-100 text-purple-800 border-purple-300',
                    'Ditolak' => 'bg-red-100 text-red-800 border-red-300',
                    'Dikembalikan ke Pegawai' => 'bg-orange-100 text-orange-800 border-orange-300',
                    'Perlu Perbaikan' => 'bg-amber-100 text-amber-800 border-amber-300',
                ];
                $statusColor = $statusColors[$usulan->status_usulan] ?? 'bg-gray-100 text-gray-800 border-gray-300';
            @endphp
            <div class="inline-flex items-center px-4 py-2 rounded-full border {{ $statusColor }}">
                <span class="text-sm font-medium">Status: {{ $usulan->status_usulan }}</span>
            </div>
        </div>

        {{-- Informasi Periode Usulan --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="calendar-clock" class="w-6 h-6 mr-3"></i>
                    Informasi Periode Usulan
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Periode</label>
                        <p class="text-xs text-gray-600 mb-2">Periode usulan yang sedang berlangsung</p>
                        <input type="text" value="{{ $usulan->periodeUsulan->nama_periode ?? 'Tidak ada periode aktif' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Masa Berlaku</label>
                        <p class="text-xs text-gray-600 mb-2">Rentang waktu periode usulan</p>
                        <input type="text" value="{{ $usulan->periodeUsulan ? \Carbon\Carbon::parse($usulan->periodeUsulan->tanggal_mulai)->isoFormat('D MMM YYYY') . ' - ' . \Carbon\Carbon::parse($usulan->periodeUsulan->tanggal_selesai)->isoFormat('D MMM YYYY') : '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                </div>
            </div>
        </div>

        {{-- Informasi Pegawai --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="user" class="w-6 h-6 mr-3"></i>
                    Informasi Pegawai
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Nama Lengkap</label>
                        <p class="text-xs text-gray-600 mb-2">Nama lengkap pegawai</p>
                        <input type="text" value="{{ $usulan->pegawai->nama_lengkap ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">NIP</label>
                        <p class="text-xs text-gray-600 mb-2">Nomor Induk Pegawai</p>
                        <input type="text" value="{{ $usulan->pegawai->nip ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                </div>
            </div>
        </div>

        {{-- Data Usulan --}}
        @if(isset($usulan->data_usulan) && !empty($usulan->data_usulan))
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="file-text" class="w-6 h-6 mr-3"></i>
                    Data Usulan
                </h2>
            </div>
            <div class="p-6">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <pre class="text-sm text-gray-800 whitespace-pre-wrap">{{ json_encode($usulan->data_usulan, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
        </div>
        @endif

        {{-- Dokumen Usulan --}}
        @if(isset($usulan->data_usulan['dokumen_usulan']) && !empty($usulan->data_usulan['dokumen_usulan']))
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-orange-600 to-red-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="file-text" class="w-6 h-6 mr-3"></i>
                    Dokumen Usulan
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($usulan->data_usulan['dokumen_usulan'] as $docType => $docData)
                        @if(isset($docData['path']))
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-semibold text-gray-800 capitalize">{{ str_replace('_', ' ', $docType) }}</h4>
                                    <p class="text-sm text-gray-600">{{ $docData['original_name'] ?? 'Dokumen' }}</p>
                                </div>
                                <a href="{{ route('pegawai-unmul.usulan-presensi.show-document', ['usulan-presensi' => $usulan->id, 'field' => $docType]) }}"
                                   target="_blank"
                                   class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100">
                                    <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                    Lihat
                                </a>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Catatan Pengusul --}}
        @if(isset($usulan->data_usulan['catatan_pengusul']) && $usulan->data_usulan['catatan_pengusul'])
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-teal-600 to-cyan-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="message-square" class="w-6 h-6 mr-3"></i>
                    Catatan Pengusul
                </h2>
            </div>
            <div class="p-6">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <p class="text-gray-800">{{ $usulan->data_usulan['catatan_pengusul'] }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Catatan Verifikator --}}
        @if($usulan->catatan_verifikator)
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-amber-600 to-orange-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="clipboard-check" class="w-6 h-6 mr-3"></i>
                    Catatan Verifikator
                </h2>
            </div>
            <div class="p-6">
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <p class="text-amber-800">{{ $usulan->catatan_verifikator }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Metadata --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="info" class="w-6 h-6 mr-3"></i>
                    Informasi Sistem
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Tanggal Dibuat</label>
                        <p class="text-xs text-gray-600 mb-2">Waktu usulan pertama kali dibuat</p>
                        <input type="text" value="{{ $usulan->created_at ? \Carbon\Carbon::parse($usulan->created_at)->isoFormat('D MMMM YYYY HH:mm') : '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Terakhir Diupdate</label>
                        <p class="text-xs text-gray-600 mb-2">Waktu terakhir usulan diperbarui</p>
                        <input type="text" value="{{ $usulan->updated_at ? \Carbon\Carbon::parse($usulan->updated_at)->isoFormat('D MMMM YYYY HH:mm') : '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection