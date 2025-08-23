@extends('backend.layouts.roles.kepegawaian-universitas.app')

@section('title', isset($subUnitKerja) ? 'Edit Sub Unit Kerja' : 'Tambah Sub Unit Kerja')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('backend.kepegawaian-universitas.sub-unitkerja.index') }}" 
                   class="p-2 bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
                    <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div class="p-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800">
                        {{ isset($subUnitKerja) ? 'Edit Sub Unit Kerja' : 'Tambah Sub Unit Kerja' }}
                    </h1>
                    <p class="text-slate-600">{{ isset($subUnitKerja) ? 'Perbarui informasi' : 'Tambahkan' }} Sub Unit Kerja</p>
                </div>
            </div>
        </div>

        <!-- Form Section -->
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-slate-800 to-slate-900 px-8 py-6">
                    <h2 class="text-xl font-semibold text-white">Informasi Sub Unit Kerja</h2>
                </div>
                
                <form action="{{ isset($subUnitKerja) ? route('backend.kepegawaian-universitas.sub-unitkerja.update', $subUnitKerja) : route('backend.kepegawaian-universitas.sub-unitkerja.store') }}" 
                      method="POST" class="p-8 space-y-6">
                    @csrf
                    @if(isset($subUnitKerja))
                        @method('PUT')
                    @endif

                    <!-- Unit Kerja -->
                    <div>
                        <label for="unit_kerja_id" class="block text-sm font-medium text-slate-700 mb-2">
                            Unit Kerja <span class="text-red-500">*</span>
                        </label>
                        <select name="unit_kerja_id" id="unit_kerja_id" 
                                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('unit_kerja_id') border-red-500 @enderror">
                            <option value="">Pilih Unit Kerja</option>
                            @foreach($unitKerjas as $unitKerja)
                                <option value="{{ $unitKerja->id }}" 
                                        {{ (isset($subUnitKerja) && $subUnitKerja->unit_kerja_id == $unitKerja->id) || old('unit_kerja_id') == $unitKerja->id ? 'selected' : '' }}>
                                    {{ $unitKerja->nama_unit_kerja }}
                                </option>
                            @endforeach
                        </select>
                        @error('unit_kerja_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama Sub Unit Kerja -->
                    <div>
                        <label for="nama_sub_unit_kerja" class="block text-sm font-medium text-slate-700 mb-2">
                            Nama Sub Unit Kerja <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="nama_sub_unit_kerja" 
                               id="nama_sub_unit_kerja" 
                               value="{{ isset($subUnitKerja) ? $subUnitKerja->nama_sub_unit_kerja : old('nama_sub_unit_kerja') }}"
                               class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('nama_sub_unit_kerja') border-red-500 @enderror"
                               placeholder="Masukkan nama sub unit kerja">
                        @error('nama_sub_unit_kerja')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-200">
                        <a href="{{ route('backend.kepegawaian-universitas.sub-unitkerja.index') }}" 
                           class="px-6 py-3 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl font-medium transition-colors duration-200">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white rounded-xl font-semibold shadow-lg transition-all duration-300 transform hover:scale-105">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ isset($subUnitKerja) ? 'Perbarui' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
