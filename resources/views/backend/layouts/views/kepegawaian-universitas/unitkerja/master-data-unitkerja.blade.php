@extends('backend.layouts.roles.kepegawaian-universitas.app')

@section('title', 'Master Data Unit Kerja')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-slate-800">Master Data Unit Kerja</h1>
                        <p class="text-slate-600">Kelola hierarki Unit Kerja, Sub Unit Kerja, dan Sub-sub Unit Kerja</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('backend.kepegawaian-universitas.unitkerja.create') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Unit Kerja
                    </a>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-green-800 font-medium">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <span class="text-red-800 font-medium">{{ session('error') }}</span>
            </div>
        </div>
        @endif

        <!-- Hierarchical Data Display -->
        <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/30 overflow-hidden">
            <div class="p-6 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-blue-50/30">
                <h2 class="text-xl font-bold text-slate-800">Hierarki Unit Kerja</h2>
                <p class="text-slate-600">Struktur organisasi Unit Kerja Universitas Mulawarman</p>
            </div>

            <div class="p-6">
                @if($unitKerjas->count() > 0)
                    <div class="space-y-6">
                        @foreach($unitKerjas as $unitKerja)
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border border-blue-200/50 overflow-hidden">
                            <!-- Unit Kerja Header -->
                            <div class="p-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <h3 class="text-lg font-bold">{{ $unitKerja->nama }}</h3>
                                        <span class="px-2 py-1 bg-white/20 rounded-lg text-xs font-medium">Unit Kerja</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('backend.kepegawaian-universitas.unitkerja.edit', ['type' => 'unit_kerja', 'id' => $unitKerja->id]) }}"
                                           class="p-2 bg-white/20 rounded-lg hover:bg-white/30 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('backend.kepegawaian-universitas.unitkerja.destroy', ['type' => 'unit_kerja', 'id' => $unitKerja->id]) }}"
                                              method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus Unit Kerja ini?')"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-white/20 rounded-lg hover:bg-white/30 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Sub Unit Kerja -->
                            @if($unitKerja->subUnitKerjas->count() > 0)
                                <div class="p-4 space-y-3">
                                    @foreach($unitKerja->subUnitKerjas as $subUnitKerja)
                                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                                        <div class="p-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-3">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                    </svg>
                                                    <h4 class="font-semibold">{{ $subUnitKerja->nama }}</h4>
                                                    <span class="px-2 py-1 bg-white/20 rounded-lg text-xs font-medium">Sub Unit Kerja</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('backend.kepegawaian-universitas.unitkerja.edit', ['type' => 'sub_unit_kerja', 'id' => $subUnitKerja->id]) }}"
                                                       class="p-1.5 bg-white/20 rounded-lg hover:bg-white/30 transition-colors">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('backend.kepegawaian-universitas.unitkerja.destroy', ['type' => 'sub_unit_kerja', 'id' => $subUnitKerja->id]) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus Sub Unit Kerja ini?')"
                                                          class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1.5 bg-white/20 rounded-lg hover:bg-white/30 transition-colors">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Sub-sub Unit Kerja -->
                                        @if($subUnitKerja->subSubUnitKerjas->count() > 0)
                                            <div class="p-3 space-y-2">
                                                @foreach($subUnitKerja->subSubUnitKerjas as $subSubUnitKerja)
                                                <div class="flex items-center justify-between p-2 bg-slate-50 rounded-lg">
                                                    <div class="flex items-center gap-3">
                                                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                        </svg>
                                                        <span class="font-medium text-slate-700">{{ $subSubUnitKerja->nama }}</span>
                                                        <span class="px-2 py-1 bg-slate-200 text-slate-600 rounded-lg text-xs font-medium">Sub-sub Unit Kerja</span>
                                                    </div>
                                                    <div class="flex items-center gap-1">
                                                        <a href="{{ route('backend.kepegawaian-universitas.unitkerja.edit', ['type' => 'sub_sub_unit_kerja', 'id' => $subSubUnitKerja->id]) }}"
                                                           class="p-1 bg-slate-200 rounded hover:bg-slate-300 transition-colors">
                                                            <svg class="w-3 h-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                        </a>
                                                        <form action="{{ route('backend.kepegawaian-universitas.unitkerja.destroy', ['type' => 'sub_sub_unit_kerja', 'id' => $subSubUnitKerja->id]) }}"
                                                              method="POST"
                                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus Sub-sub Unit Kerja ini?')"
                                                              class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="p-1 bg-slate-200 rounded hover:bg-slate-300 transition-colors">
                                                                <svg class="w-3 h-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-16 w-16 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-slate-900">Belum Ada Unit Kerja</h3>
                        <p class="mt-2 text-slate-500">Mulai dengan menambahkan unit kerja pertama Anda.</p>
                        <div class="mt-6">
                            <a href="{{ route('backend.kepegawaian-universitas.unitkerja.create') }}"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Unit Kerja Pertama
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
