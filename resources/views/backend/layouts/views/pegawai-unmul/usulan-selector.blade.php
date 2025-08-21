@extends('backend.layouts.roles.pegawai-unmul.app')

@section('title', 'Pilih Jenis Usulan')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Notifikasi sudah ditangani oleh component flash di layout base --}}

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            Pilih Jenis Usulan
        </h1>
        <p class="mt-2 text-gray-600">
            Silakan pilih jenis usulan yang ingin Anda ajukan.
        </p>
    </div>

    {{-- Usulan Options Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($jenisUsulanOptions as $key => $option)
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-lg bg-{{ $option['color'] }}-100 flex items-center justify-center">
                                <i data-lucide="{{ $option['icon'] }}" class="w-6 h-6 text-{{ $option['color'] }}-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ $option['title'] }}
                            </h3>
                        </div>
                    </div>

                    <p class="text-gray-600 text-sm mb-4">
                        {{ $option['description'] }}
                    </p>

                    @if ($option['available'])
                        <a href="{{ $option['route'] }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Buat Usulan
                        </a>
                    @else
                        <div class="flex items-center text-gray-500 text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Belum Tersedia
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Back to Dashboard --}}
    <div class="mt-8 text-center">
        <a href="{{ route('pegawai-unmul.usulan-pegawai.dashboard') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
