<aside id="sidebar" class="sidebar w-64 bg-white shadow-lg fixed top-0 left-0 h-full z-30 flex flex-col transition-all duration-300">
    {{-- Header Sidebar --}}
    <div class="flex items-center justify-center p-4 h-16 border-b border-gray-200 flex-shrink-0">
        <div class="flex items-center gap-3 overflow-hidden">
            <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo" class="h-10 flex-shrink-0">
            <span class="font-bold text-lg text-slate-700 sidebar-text">Pegawai Unmul</span>
        </div>
    </div>

    {{-- Navigasi Menu --}}
    <nav class="flex-1 overflow-y-auto py-4">
        <div class="px-4 mb-4">
            <a href="{{ route('pegawai-unmul.usulan-pegawai.dashboard') }}"
            class="flex items-center px-3 py-2.5 rounded-lg font-semibold {{ request()->routeIs('pegawai-unmul.usulan-pegawai.dashboard') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="file-text" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span class="sidebar-text">Usulan Saya</span>
            </a>
        </div>

        {{-- Dropdown untuk semua menu usulan --}}
        @php
            $usulanMenus = [
                ['route' => route('pegawai-unmul.usulan-jabatan.index'), 'icon' => 'file-user', 'label' => 'Usulan Jabatan', 'pattern' => 'pegawai-unmul.usulan-jabatan.*'],
                ['route' => route('pegawai-unmul.usulan-kepangkatan.index'), 'icon' => 'trending-up', 'label' => 'Usulan Kepangkatan', 'pattern' => 'pegawai-unmul.usulan-kepangkatan.*'],
                ['route' => route('pegawai-unmul.usulan-nuptk.index'), 'icon' => 'user-check', 'label' => 'Usulan NUPTK', 'pattern' => 'pegawai-unmul.usulan-nuptk.*'],
                ['route' => route('pegawai-unmul.usulan-tugas-belajar.index'), 'icon' => 'book-open', 'label' => 'Usulan Tugas Belajar', 'pattern' => 'pegawai-unmul.usulan-tugas-belajar.*'],
                ['route' => route('pegawai-unmul.usulan-laporan-lkd.index'), 'icon' => 'file-bar-chart-2', 'label' => 'Usulan Laporan LKD', 'pattern' => 'pegawai-unmul.usulan-laporan-lkd.*'],
                ['route' => route('pegawai-unmul.usulan-presensi.index'), 'icon' => 'clipboard-check', 'label' => 'Usulan Presensi', 'pattern' => 'pegawai-unmul.usulan-presensi.*'],
                ['route' => route('pegawai-unmul.usulan-penyesuaian-masa-kerja.index'), 'icon' => 'clock', 'label' => 'Usulan Penyesuaian Masa Kerja', 'pattern' => 'pegawai-unmul.usulan-penyesuaian-masa-kerja.*'],
                ['route' => route('pegawai-unmul.usulan-ujian-dinas-ijazah.index'), 'icon' => 'book-marked', 'label' => 'Usulan Ujian Dinas & Ijazah', 'pattern' => 'pegawai-unmul.usulan-ujian-dinas-ijazah.*'],
                ['route' => route('pegawai-unmul.usulan-laporan-serdos.index'), 'icon' => 'file-check-2', 'label' => 'Usulan Laporan Serdos', 'pattern' => 'pegawai-unmul.usulan-laporan-serdos.*'],
                ['route' => route('pegawai-unmul.usulan-pensiun.index'), 'icon' => 'user-minus', 'label' => 'Usulan Pensiun', 'pattern' => 'pegawai-unmul.usulan-pensiun.*'],

                ['route' => route('pegawai-unmul.usulan-pencantuman-gelar.index'), 'icon' => 'graduation-cap', 'label' => 'Usulan Pencantuman Gelar', 'pattern' => 'pegawai-unmul.usulan-pencantuman-gelar.*'],
                ['route' => route('pegawai-unmul.usulan-id-sinta-sister.index'), 'icon' => 'link', 'label' => 'Usulan ID SINTA ke SISTER', 'pattern' => 'pegawai-unmul.usulan-id-sinta-sister.*'],
                ['route' => route('pegawai-unmul.usulan-satyalancana.index'), 'icon' => 'medal', 'label' => 'Usulan Satyalancana', 'pattern' => 'pegawai-unmul.usulan-satyalancana.*'],
                ['route' => route('pegawai-unmul.usulan-pengaktifan-kembali.index'), 'icon' => 'user-plus', 'label' => 'Usulan Pengaktifan Kembali', 'pattern' => 'pegawai-unmul.usulan-pengaktifan-kembali.*'],
            ];
            $isUsulanActive = collect($usulanMenus)->contains(fn($menu) => request()->routeIs($menu['pattern']));
        @endphp

        <div class="px-4 mb-2" x-data="{ open: {{ $isUsulanActive ? 'true' : 'false' }} }">
            {{-- Tombol Dropdown --}}
            <button id="usulan-dropdown-button" @click="open = !open" class="flex items-center justify-between w-full px-3 py-2.5 rounded-lg {{ $isUsulanActive ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <span class="flex items-center font-semibold">
                    <i data-lucide="file-plus-2" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="sidebar-text">Layanan Usulan</span>
                </span>
                {{-- PERBAIKAN: Menambahkan kelas sidebar-text agar panah juga hilang --}}
                <i data-lucide="chevron-down" class="w-5 h-5 transition-transform sidebar-text" :class="{ 'rotate-180': open }"></i>
            </button>

            {{-- Konten Dropdown --}}
            {{-- PERBAIKAN: Menghapus kelas sidebar-text dari div ini --}}
            <div x-show="open" x-transition class="mt-2 space-y-1 pl-1">
                @foreach ($usulanMenus as $menu)
                    <a href="{{ $menu['route'] }}"
                       class="flex items-center px-3 py-2 rounded-lg font-semibold text-sm {{ request()->routeIs($menu['pattern']) ? 'bg-slate-100 text-indigo-700 font-semibold' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                        <i data-lucide="{{ $menu['icon'] }}" class="w-4 h-4 mr-3 flex-shrink-0"></i>
                        {{-- PERBAIKAN: Menambahkan kelas sidebar-text pada label sub-menu --}}
                        <span class="sidebar-text">{{ $menu['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </nav>

    {{-- Footer Sidebar untuk Logout --}}
    <div class="p-4 border-t border-gray-200 flex-shrink-0">
        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="flex items-center px-3 py-2.5 rounded-lg font-semibold text-red-600 bg-red-50 hover:bg-red-100">
            <i data-lucide="log-out" class="w-5 h-5 mr-3 flex-shrink-0"></i>
            <span class="sidebar-text">Logout</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
</aside>
