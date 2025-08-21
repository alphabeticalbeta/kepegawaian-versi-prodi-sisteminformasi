@extends('backend.layouts.roles.admin-univ-usulan.app')

@section('title', 'Master Data Pegawai')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- PESAN SUKSES --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-800 p-4 mb-6 rounded-lg shadow-md flex items-center">
            <i data-lucide="check-circle-2" class="w-5 h-5 mr-3 text-green-600"></i>
            <p class="font-semibold">{{ session('success') }}</p>
        </div>
    @endif

    {{-- KARTU HEADER & FILTER --}}
    <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 mb-8">
        {{-- Bagian Atas: Judul dan Tombol Tambah --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 border-b border-gray-200">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-800">Master Data Pegawai</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola, filter, dan tambahkan data pegawai baru.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('backend.admin-univ-usulan.data-pegawai.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-bold rounded-lg shadow-lg shadow-indigo-500/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:scale-105">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    <span>Tambah Pegawai</span>
                </a>
            </div>
        </div>

        {{-- Bagian Bawah: Form Filter --}}
        <div class="pt-5">
            <form method="GET" action="{{ route('backend.admin-univ-usulan.data-pegawai.index') }}">
                <div class="flex flex-col sm:flex-row items-start sm:items-end gap-4">
                    <div class="w-full sm:w-auto flex-grow">
                        <label for="search" class="block text-sm font-semibold text-slate-600 mb-1">Cari Nama atau NIP</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Ketik nama atau NIP..." class="block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors">
                    </div>
                    <div class="w-full sm:w-auto">
                        <label for="filter_jenis_pegawai" class="block text-sm font-semibold text-slate-600 mb-1">Jenis Pegawai</label>
                        <select id="filter_jenis_pegawai" name="filter_jenis_pegawai" class="block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors">
                            <option value="">Semua Jenis</option>
                            <option value="Dosen" {{ request('filter_jenis_pegawai') == 'Dosen' ? 'selected' : '' }}>Dosen</option>
                            <option value="Tenaga Kependidikan" {{ request('filter_jenis_pegawai') == 'Tenaga Kependidikan' ? 'selected' : '' }}>Tenaga Kependidikan</option>
                        </select>
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-4 py-2 bg-slate-700 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition-colors shadow-sm">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        <span>Cari</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- KARTU TABEL DATA --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-xs text-slate-500 uppercase bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-3 font-semibold text-center tracking-wider">No</th>
                    <th scope="col" class="px-6 py-3 font-semibold text-left tracking-wider">Nama</th>
                    <th scope="col" class="px-6 py-3 font-semibold text-center tracking-wider">NIP</th>
                    <th scope="col" class="px-6 py-3 font-semibold text-center tracking-wider">Jenis Pegawai</th>
                    <th scope="col" class="px-6 py-3 font-semibold text-center tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($pegawais as $index => $pegawai)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 text-center text-slate-600 align-middle">
                            {{ $index + $pegawais->firstItem() }}
                        </td>
                        <td class="px-6 py-4 align-middle">
                            <div class="flex items-center">
                                <img class="h-10 w-10 rounded-full object-cover mr-4 border"
                                 src="{{ $pegawai->foto ? route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai->id, 'field' => 'foto']) : 'https://ui-avatars.com/api/?name=' . urlencode($pegawai->nama_lengkap) . '&background=random' }}"
                                 alt="Foto"
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($pegawai->nama_lengkap) }}&background=random'">
                                <div class="font-medium text-slate-800">{{ $pegawai->nama_lengkap }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center text-slate-600 align-middle">{{ $pegawai->nip }}</td>
                        <td class="px-6 py-4 text-center align-middle">
                            @if($pegawai->jenis_pegawai == 'Dosen')
                                <span class="px-2.5 py-1 text-xs font-semibold text-sky-800 bg-sky-100 rounded-full">{{ $pegawai->jenis_pegawai }}</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-semibold text-amber-800 bg-amber-100 rounded-full">{{ $pegawai->jenis_pegawai }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center align-middle">
                            <div class="flex justify-center items-center gap-2">
                                {{-- Tombol Lihat (Read) --}}
                                <a href="{{ route('backend.admin-univ-usulan.data-pegawai.show', $pegawai) }}" class="p-2 text-slate-500 hover:text-green-600 hover:bg-green-100 rounded-full transition-colors" title="Lihat Detail">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                {{-- Tombol Edit (Update) --}}
                                <a href="{{ route('backend.admin-univ-usulan.data-pegawai.edit', $pegawai) }}" class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-indigo-100 rounded-full transition-colors" title="Edit Data">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                                {{-- Tombol Hapus (Delete) --}}
                                <form action="{{ route('backend.admin-univ-usulan.data-pegawai.destroy', $pegawai) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pegawai ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-500 hover:text-red-600 hover:bg-red-100 rounded-full transition-colors" title="Hapus Data">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-500">
                                <i data-lucide="database-x" class="w-12 h-12 text-slate-300 mb-3"></i>
                                <h3 class="text-lg font-semibold">Data Tidak Ditemukan</h3>
                                <p class="text-sm">Tidak ada data pegawai yang cocok dengan filter Anda.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{-- PAGINATION --}}
        <div class="p-4 border-t border-gray-200">
            {{ $pegawais->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
