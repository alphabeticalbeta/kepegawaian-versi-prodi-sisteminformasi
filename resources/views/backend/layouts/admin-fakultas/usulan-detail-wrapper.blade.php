@extends('backend.layouts.admin-fakultas.app')

@section('title', 'Validasi Detail Usulan Jabatan')

@section('content')
<div class="container mx-auto p-5">

    {{-- Alert Messages - Enhanced --}}
    @include('backend.components.usulan._alert-messages')

    {{-- Form Container --}}
    <form action="{{ $formAction }}" method="POST" id="validationForm" enctype="multipart/form-data" class="mt-8 space-y-8">
        @csrf

        {{-- Header Navigation --}}
        <div class="mb-8">
            <a href="{{ route('admin-fakultas.periode.pendaftar', $usulan->periode_usulan_id) }}" 
               class="text-sm font-medium text-indigo-600 hover:text-indigo-500 inline-flex items-center">
                <svg class="h-5 w-5 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Kembali ke Daftar Pengusul
            </a>
            <h1 class="text-2xl font-bold text-gray-800 mt-2">
                Validasi Usulan: {{ $usulan->pegawai->nama_lengkap ?? 'N/A' }}
            </h1>
            <p class="text-sm text-gray-500">
                Lakukan validasi terhadap setiap item data usulan pegawai dan lihat dokumen pendukung.
            </p>
        </div>

        {{-- Header Info Card - Reuse existing --}}
        @include('backend.components.usulan._header', [
            'usulan' => $usulan
        ])

        {{-- ENHANCED: Dokumen Section - Tampilkan dulu untuk kemudahan review --}}
        @if(isset($dokumenData))
            @include('backend.components.usulan._dokumen', [
                'usulan' => $usulan,
                'dokumenData' => $dokumenData,
                'bkdLabels' => $bkdLabels ?? []
            ])
        @endif

        {{-- Validation Sections - Reuse existing --}}
        @php
            // Use processed validation fields if available
            $validationFieldsForForm = isset($validationFields) && is_array($validationFields) 
                ? array_map(fn($categoryData) => array_keys($categoryData), $validationFields)
                : \App\Models\BackendUnivUsulan\Usulan::getValidationFieldsWithDynamicBkd($usulan);
        @endphp

        @if(isset($validationFieldsForForm) && count($validationFieldsForForm) > 0)
            @foreach($validationFieldsForForm as $category => $fields)
                @include('backend.components.usulan._validation-section', [
                    'category' => $category,
                    'fields'   => $fields,
                    'usulan'   => $usulan,
                    'canEdit'  => in_array($usulan->status_usulan, ['Diajukan', 'Sedang Direview']),
                    'existingValidation' => $existingValidation ?? [],
                    'bkdLabels' => $bkdLabels ?? []
                ])
            @endforeach
        @endif

        {{-- Admin Fakultas Specific Action Buttons --}}
        @include('backend.layouts.admin-fakultas.partials._action-buttons', [
            'usulan' => $usulan
        ])

        {{-- ADDED: Form Input Dokumen Fakultas --}}
        @include('backend.layouts.admin-fakultas.partials._dokumen-fakultas-form', [
            'usulan' => $usulan
        ])

        {{-- Return Form with Enhanced Validation Summary --}}
        <div id="returnForm" class="hidden mt-6 bg-white shadow-md rounded-lg overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-600 to-orange-600 px-6 py-4">
                <h3 class="text-lg font-semibold text-white">Kembalikan Usulan untuk Perbaikan</h3>
                <p class="text-yellow-100 text-sm mt-1">Berikan catatan yang jelas untuk pegawai</p>
            </div>
            <div class="p-6">
                {{-- Enhanced Validation Issue Summary --}}
                <div id="validationIssueSummary" class="hidden mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <h4 class="font-medium text-red-900 mb-2">📋 Item yang tidak sesuai:</h4>
                    <ul id="issueList" class="text-sm text-red-800 space-y-1 list-disc pl-5"></ul>
                </div>

                {{-- Document Status Summary --}}
                @if(isset($dokumenData))
                    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h4 class="font-medium text-blue-900 mb-2">📄 Ringkasan Status Dokumen:</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                            @if(isset($dokumenData['dokumen_profil']))
                                @php
                                    $profilAvailable = collect($dokumenData['dokumen_profil'])->where('status', 'available')->count();
                                    $profilTotal = count($dokumenData['dokumen_profil']);
                                @endphp
                                <div class="text-center">
                                    <div class="font-semibold text-blue-900">Profil Pegawai</div>
                                    <div class="text-blue-700">{{ $profilAvailable }}/{{ $profilTotal }}</div>
                                </div>
                            @endif
                            
                            @if(isset($dokumenData['dokumen_usulan']))
                                @php
                                    $usulanAvailable = collect($dokumenData['dokumen_usulan'])->where('status', 'available')->count();
                                    $usulanTotal = count($dokumenData['dokumen_usulan']);
                                @endphp
                                <div class="text-center">
                                    <div class="font-semibold text-blue-900">Dokumen Usulan</div>
                                    <div class="text-blue-700">{{ $usulanAvailable }}/{{ $usulanTotal }}</div>
                                </div>
                            @endif
                            
                            @if(isset($dokumenData['dokumen_bkd']))
                                @php
                                    $bkdAvailable = collect($dokumenData['dokumen_bkd'])->where('status', 'available')->count();
                                    $bkdTotal = count($dokumenData['dokumen_bkd']);
                                @endphp
                                <div class="text-center">
                                    <div class="font-semibold text-blue-900">Dokumen BKD</div>
                                    <div class="text-blue-700">{{ $bkdAvailable }}/{{ $bkdTotal }}</div>
                                </div>
                            @endif
                            
                            @if(isset($dokumenData['dokumen_pendukung']))
                                @php
                                    $pendukungCompleted = collect($dokumenData['dokumen_pendukung'])->whereIn('status', ['available', 'filled'])->count();
                                    $pendukungTotal = count($dokumenData['dokumen_pendukung']);
                                @endphp
                                <div class="text-center">
                                    <div class="font-semibold text-blue-900">Dok. Fakultas</div>
                                    <div class="text-blue-700">{{ $pendukungCompleted }}/{{ $pendukungTotal }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="mb-4">
                    <label for="catatan_umum_return" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan untuk Pegawai <span class="text-red-500">*</span>
                    </label>
                    <textarea id="catatan_umum_return" name="catatan_umum" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                              placeholder="Jelaskan secara detail item mana yang perlu diperbaiki dan bagaimana cara memperbaikinya..."
                              required></textarea>
                    <p class="text-xs text-gray-500 mt-1">Minimum 10 karakter</p>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="hideReturnForm()" 
                            class="px-4 py-2 text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="button" onclick="submitReturnForm()" 
                            class="px-6 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition-colors">
                        Kembalikan ke Pegawai
                    </button>
                </div>
            </div>
        </div>

        {{-- Riwayat Log - Reuse existing --}}
        @include('backend.components.usulan._riwayat_log', ['usulan' => $usulan])
    </form>
</div>

{{-- Enhanced Summary for Desktop --}}
@if(isset($dokumenData))
    <div class="hidden lg:block fixed top-20 right-6 w-72 bg-white shadow-lg rounded-lg border border-gray-200 p-4 z-40">
        <h4 class="font-semibold text-gray-800 mb-3 border-b pb-2">📊 Ringkasan Dokumen</h4>
        <div class="space-y-3 text-sm">
            @if(isset($dokumenData['dokumen_profil']))
                @php
                    $profilAvailable = collect($dokumenData['dokumen_profil'])->where('status', 'available')->count();
                    $profilTotal = count($dokumenData['dokumen_profil']);
                    $profilPct = $profilTotal ? round(($profilAvailable/$profilTotal)*100) : 0;
                @endphp
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">📄 Profil Pegawai</span>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-800 font-medium">{{ $profilAvailable }}/{{ $profilTotal }}</span>
                        <div class="w-12 h-2 bg-gray-200 rounded-full">
                            <div class="h-2 bg-green-500 rounded-full" style="width: {{ $profilPct }}%"></div>
                        </div>
                    </div>
                </div>
            @endif
            
            @if(isset($dokumenData['dokumen_usulan']))
                @php
                    $usulanAvailable = collect($dokumenData['dokumen_usulan'])->where('status', 'available')->count();
                    $usulanTotal = count($dokumenData['dokumen_usulan']);
                    $usulanPct = $usulanTotal ? round(($usulanAvailable/$usulanTotal)*100) : 0;
                @endphp
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">📋 Dokumen Usulan</span>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-800 font-medium">{{ $usulanAvailable }}/{{ $usulanTotal }}</span>
                        <div class="w-12 h-2 bg-gray-200 rounded-full">
                            <div class="h-2 bg-blue-500 rounded-full" style="width: {{ $usulanPct }}%"></div>
                        </div>
                    </div>
                </div>
            @endif
            
            @if(isset($dokumenData['dokumen_bkd']))
                @php
                    $bkdAvailable = collect($dokumenData['dokumen_bkd'])->where('status', 'available')->count();
                    $bkdTotal = count($dokumenData['dokumen_bkd']);
                    $bkdPct = $bkdTotal ? round(($bkdAvailable/$bkdTotal)*100) : 0;
                @endphp
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">📊 Dokumen BKD</span>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-800 font-medium">{{ $bkdAvailable }}/{{ $bkdTotal }}</span>
                        <div class="w-12 h-2 bg-gray-200 rounded-full">
                            <div class="h-2 bg-purple-500 rounded-full" style="width: {{ $bkdPct }}%"></div>
                        </div>
                    </div>
                </div>
            @endif
            
            @if(isset($dokumenData['dokumen_pendukung']))
                @php
                    $pendukungCompleted = collect($dokumenData['dokumen_pendukung'])->whereIn('status', ['available', 'filled'])->count();
                    $pendukungTotal = count($dokumenData['dokumen_pendukung']);
                    $pendukungPct = $pendukungTotal ? round(($pendukungCompleted/$pendukungTotal)*100) : 0;
                @endphp
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">🏛️ Dokumen Fakultas</span>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-800 font-medium">{{ $pendukungCompleted }}/{{ $pendukungTotal }}</span>
                        <div class="w-12 h-2 bg-gray-200 rounded-full">
                            <div class="h-2 bg-orange-500 rounded-full" style="width: {{ $pendukungPct }}%"></div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="mt-4 pt-3 border-t border-gray-200">
            <div class="text-xs text-gray-500">
                💡 Scroll ke atas untuk melihat detail dokumen lengkap
            </div>
        </div>
    </div>
@endif
@endsection

{{-- FIXED: Include validation scripts dari component yang benar --}}
@include('backend.components.usulan._validation-scripts')