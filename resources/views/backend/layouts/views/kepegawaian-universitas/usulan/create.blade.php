@extends('backend.layouts.roles.kepegawaian-universitas.app')
@section('title', 'Buat ' . $namaUsulan . ' - Kepegawaian Universitas')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 p-4">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-6 rounded-2xl mb-6 shadow-2xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold mb-2">Buat {{ $namaUsulan }}</h1>
                <p class="text-blue-100">Periode: {{ $periode->nama_periode }}</p>
                <div class="flex items-center mt-2 space-x-4 text-blue-200 text-sm">
                    <span>📅 {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d M Y') }}</span>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                        {{ $periode->status === 'Buka' ? 'bg-green-500/20 text-green-100' : 'bg-red-500/20 text-red-100' }}">
                        {{ $periode->status }}
                    </span>
                </div>
            </div>
            <div class="hidden md:block">
                <a href="{{ route('backend.kepegawaian-universitas.usulan.index', ['jenis' => $jenisUsulan]) }}"
                   class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Period Status Alert -->
    @if($periode->status === 'Tutup')
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <strong>Peringatan!</strong> Periode {{ $namaUsulan }} sedang ditutup. Usulan tidak dapat dibuat saat ini.
            </div>
        </div>
    @endif

    <!-- Form Section -->
    <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 p-8">
        @if($periode->status === 'Buka')
            <form action="#" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="periode_usulan_id" value="{{ $periode->id }}">
                <input type="hidden" name="jenis_usulan" value="{{ $namaUsulan }}">

                <!-- Form content will be customized based on usulan type -->
                @switch($jenisUsulan)
                    @case('jabatan')
                        @include('backend.layouts.views.kepegawaian-universitas.usulan.forms.jabatan')
                        @break
                    @case('kepangkatan')
                        @include('backend.layouts.views.kepegawaian-universitas.usulan.forms.kepangkatan')
                        @break
                    @case('tugas-belajar')
                        @include('backend.layouts.views.kepegawaian-universitas.usulan.forms.tugas-belajar')
                        @break
                    @default
                        @include('backend.layouts.views.kepegawaian-universitas.usulan.forms.default')
                @endswitch

                <!-- Submit Buttons -->
                <div class="flex gap-4 pt-6 border-t border-slate-200">
                    <button type="submit"
                            class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors duration-200 font-medium flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Usulan
                    </button>

                    <a href="{{ route('backend.kepegawaian-universitas.usulan.index', ['jenis' => $jenisUsulan]) }}"
                       class="bg-slate-200 text-slate-700 px-6 py-3 rounded-xl hover:bg-slate-300 transition-colors duration-200 font-medium">
                        Batal
                    </a>
                </div>
            </form>
        @else
            <!-- Disabled state when period is closed -->
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-slate-900">Periode Ditutup</h3>
                <p class="mt-2 text-sm text-slate-500">
                    Periode untuk {{ $namaUsulan }} sedang ditutup.<br>
                    Hubungi administrator untuk membuka periode.
                </p>
                <div class="mt-6">
                    <a href="{{ route('backend.kepegawaian-universitas.usulan.index', ['jenis' => $jenisUsulan]) }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Daftar
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
