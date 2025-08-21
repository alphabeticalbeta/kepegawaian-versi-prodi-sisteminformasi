@extends('backend.layouts.roles.admin-univ-usulan.app')

@section('title', isset($item) ? 'Edit Unit Kerja' : 'Tambah Unit Kerja')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800">
                        {{ isset($item) ? 'Edit Unit Kerja' : 'Tambah Unit Kerja' }}
                    </h1>
                    <p class="text-slate-600">Kelola data unit kerja Universitas Mulawarman</p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/30 overflow-hidden">
            <div class="p-6 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-blue-50/30">
                <h2 class="text-xl font-bold text-slate-800">Form Unit Kerja</h2>
                <p class="text-slate-600">Isi data unit kerja dengan lengkap dan benar</p>
            </div>

            <div class="p-6">
                <form action="{{ isset($item) ? route('backend.admin-univ-usulan.unitkerja.update', ['type' => $type, 'id' => $item->id]) : route('backend.admin-univ-usulan.unitkerja.store') }}"
                      method="POST"
                      class="space-y-6">
                    @csrf
                    @if(isset($item))
                        @method('PUT')
                    @endif

                    <!-- Unit Kerja Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Unit Kerja -->
                        <div>
                            <label for="unit_kerja_nama" class="block text-sm font-medium text-slate-700 mb-2">
                                Unit Kerja
                            </label>
                            <input type="text"
                                   id="unit_kerja_nama"
                                   name="unit_kerja_nama"
                                   value="{{ isset($item) ? ($item->unitKerja->nama ?? '') : old('unit_kerja_nama') }}"
                                   class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                   placeholder="Masukkan nama unit kerja">
                            @error('unit_kerja_nama')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Sub Unit Kerja -->
                        <div>
                            <label for="sub_unit_kerja_nama" class="block text-sm font-medium text-slate-700 mb-2">
                                Sub Unit Kerja
                            </label>
                            <input type="text"
                                   id="sub_unit_kerja_nama"
                                   name="sub_unit_kerja_nama"
                                   value="{{ isset($item) ? ($item->subUnitKerja->nama ?? '') : old('sub_unit_kerja_nama') }}"
                                   class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                   placeholder="Masukkan nama sub unit kerja">
                            @error('sub_unit_kerja_nama')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nama -->
                        <div>
                            <label for="nama" class="block text-sm font-medium text-slate-700 mb-2">
                                Nama
                            </label>
                            <input type="text"
                                   id="nama"
                                   name="nama"
                                   value="{{ isset($item) ? $item->nama : old('nama') }}"
                                   class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                   placeholder="Masukkan nama"
                                   required>
                            @error('nama')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-200">
                        <a href="{{ route('backend.admin-univ-usulan.unitkerja.index') }}"
                           class="px-6 py-3 text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition-colors duration-200">
                            Batal
                        </a>
                        <button type="submit"
                                class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            {{ isset($item) ? 'Update' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
