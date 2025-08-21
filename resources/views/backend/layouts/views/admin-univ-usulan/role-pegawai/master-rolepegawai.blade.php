@extends('backend.layouts.roles.admin-univ-usulan.app')

@section('title', 'Manajemen Role Pegawai')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-3xl lg:text-4xl font-bold text-slate-800 mb-2">
                        Manajemen Role Pegawai
                    </h1>
                    <p class="text-slate-600 text-lg">
                        Kelola role dan permission untuk setiap pegawai
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="bg-white/80 backdrop-blur-sm px-4 py-2 rounded-xl border border-white/30 shadow-sm">
                        <span class="text-sm text-slate-600">Total Pegawai:</span>
                        <span class="font-semibold text-slate-800 ml-1">{{ $pegawais->total() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search & Filter Section -->
        <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/30 p-6 mb-8">
            <form method="GET" action="{{ route('backend.admin-univ-usulan.role-pegawai.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-slate-700 mb-2">Cari Pegawai</label>
                    <input type="text"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Nama atau NIP..."
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                </div>
                <div>
                    <label for="jenis_pegawai" class="block text-sm font-medium text-slate-700 mb-2">Jenis Pegawai</label>
                    <select id="jenis_pegawai"
                            name="jenis_pegawai"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                        <option value="">Semua Jenis</option>
                        <option value="Dosen" {{ request('jenis_pegawai') == 'Dosen' ? 'selected' : '' }}>Dosen</option>
                        <option value="Tenaga Kependidikan" {{ request('jenis_pegawai') == 'Tenaga Kependidikan' ? 'selected' : '' }}>Tenaga Kependidikan</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="flex-1 bg-indigo-600 text-white px-4 py-3 rounded-xl hover:bg-indigo-700 transition-colors duration-200 font-medium flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span>Cari</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Role Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-7 gap-4 mb-8">
            <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-600">Admin Univ</p>
                        <p class="text-lg font-bold text-indigo-600">{{ $pegawais->where('roles.0.name', 'Admin Universitas Usulan')->count() }}</p>
                    </div>
                    <div class="p-2 bg-indigo-100 rounded-xl">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-600">Admin Fakultas</p>
                        <p class="text-lg font-bold text-green-600">{{ $pegawais->where('roles.0.name', 'Admin Fakultas')->count() }}</p>
                    </div>
                    <div class="p-2 bg-green-100 rounded-xl">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-600">Admin Keuangan</p>
                        <p class="text-lg font-bold text-yellow-600">{{ $pegawais->where('roles.0.name', 'Admin Keuangan')->count() }}</p>
                    </div>
                    <div class="p-2 bg-yellow-100 rounded-xl">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-600">Tim Senat</p>
                        <p class="text-lg font-bold text-orange-600">{{ $pegawais->where('roles.0.name', 'Tim Senat')->count() }}</p>
                    </div>
                    <div class="p-2 bg-orange-100 rounded-xl">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-600">Penilai</p>
                        <p class="text-lg font-bold text-purple-600">{{ $pegawais->where('roles.0.name', 'Penilai Universitas')->count() }}</p>
                    </div>
                    <div class="p-2 bg-purple-100 rounded-xl">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-600">Pegawai</p>
                        <p class="text-lg font-bold text-blue-600">{{ $pegawais->where('roles.0.name', 'Pegawai Unmul')->count() }}</p>
                    </div>
                    <div class="p-2 bg-blue-100 rounded-xl">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-600">Tanpa Role</p>
                        <p class="text-lg font-bold text-gray-600">{{ $pegawais->where('roles', '[]')->count() }}</p>
                    </div>
                    <div class="p-2 bg-gray-100 rounded-xl">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pegawai List -->
        <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/30 overflow-hidden">
            <div class="p-6 border-b border-slate-200">
                <h2 class="text-xl font-semibold text-slate-800">Daftar Pegawai</h2>
            </div>

            @if($pegawais->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Pegawai</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">NIP</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Jenis</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($pegawais as $pegawai)
                                <tr class="hover:bg-slate-50/50 transition-colors duration-200">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if($pegawai->foto)
                                                                                <img class="h-10 w-10 rounded-full object-cover"
                                 src="{{ $pegawai->foto ? route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai->id, 'field' => 'foto']) : 'https://ui-avatars.com/api/?name=' . urlencode($pegawai->nama_lengkap) . '&background=random' }}"
                                 alt="{{ $pegawai->nama_lengkap }}"
                                 onerror="this.parentElement.innerHTML='<div class=\'h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm\'>{{ substr($pegawai->nama_lengkap, 0, 2) }}</div>'">
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm">
                                                        {{ substr($pegawai->nama_lengkap, 0, 2) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-slate-900">{{ $pegawai->nama_lengkap }}</div>
                                                <div class="text-sm text-slate-500">{{ $pegawai->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-slate-900 font-mono">{{ $pegawai->nip }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $pegawai->jenis_pegawai == 'Dosen' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $pegawai->jenis_pegawai }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($pegawai->roles as $role)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $role->name == 'Admin Universitas Usulan' ? 'bg-red-100 text-red-800' :
                                                       ($role->name == 'Admin Fakultas' ? 'bg-green-100 text-green-800' :
                                                       ($role->name == 'Penilai Universitas' ? 'bg-purple-100 text-purple-800' :
                                                       'bg-blue-100 text-blue-800')) }}">
                                                    {{ $role->name }}
                                                </span>
                                            @empty
                                                <span class="text-sm text-slate-500">Tidak ada role</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                            {{ $pegawai->status_kepegawaian }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('backend.admin-univ-usulan.role-pegawai.edit', $pegawai) }}"
                                           class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit Role
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-slate-200">
                    {{ $pegawais->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="mx-auto w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-slate-900 mb-2">Tidak ada pegawai ditemukan</h3>
                    <p class="text-slate-500">Coba ubah filter pencarian Anda atau tambahkan pegawai baru.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-submit form when filter changes
    document.getElementById('jenis_pegawai').addEventListener('change', function() {
        this.form.submit();
    });

    // Search with debounce
    let searchTimeout;
    document.getElementById('search').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            this.form.submit();
        }, 500);
    });
</script>
@endpush
