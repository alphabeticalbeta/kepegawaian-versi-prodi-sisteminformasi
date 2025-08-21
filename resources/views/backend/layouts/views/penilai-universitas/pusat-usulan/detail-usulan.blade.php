@extends('backend.layouts.roles.penilai-universitas.app')

@section('title', 'Detail Usulan')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header Section --}}
    <div class="mb-8">
        <div class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
            <a href="{{ route('penilai-universitas.dashboard') }}" class="hover:text-gray-700 transition-colors">
                Dashboard
            </a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <a href="{{ route('penilai-universitas.pusat-usulan.index') }}" class="hover:text-gray-700 transition-colors">
                Penilaian Usulan
            </a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <a href="{{ route('penilai-universitas.pusat-usulan.show-pendaftar', $usulan->periodeUsulan->id) }}" class="hover:text-gray-700 transition-colors">
                Daftar Pendaftar
            </a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-gray-900 font-medium">Detail Usulan</span>
        </div>

        <h1 class="text-3xl font-bold text-gray-900">
            Detail Usulan: {{ $usulan->pegawai->nama_lengkap ?? 'N/A' }}
        </h1>
        <p class="mt-2 text-gray-600">
            Review detail usulan {{ strtolower(str_replace(['usulan-', '-'], [' ', ' '], $usulan->jenis_usulan)) }} yang diajukan.
        </p>
    </div>

    {{-- Use Shared Usulan Detail Component --}}
    @include('backend.layouts.views.shared.usulan-detail', [
        'usulan' => $usulan,
        'role' => 'Tim Penilai',
        'existingValidation' => $existingValidation ?? [],
        'validationFields' => $validationFields ?? [],
        'bkdLabels' => $bkdLabels ?? [],
        'canEdit' => $canEdit ?? false,
        'penilais' => collect() // Empty collection since this is for penilai view
    ])
</div>
@endsection


