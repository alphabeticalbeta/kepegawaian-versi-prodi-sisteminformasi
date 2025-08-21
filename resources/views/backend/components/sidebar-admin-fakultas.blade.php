<aside id="sidebar" class="sidebar w-64 bg-white shadow-lg fixed top-0 left-0 h-full z-30 flex flex-col transition-all duration-300">
    {{-- Header Sidebar --}}
    <div class="flex items-center justify-center p-4 h-16 border-b border-gray-200 flex-shrink-0">
        <div class="flex items-center gap-3 overflow-hidden">
            <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo" class="h-10 flex-shrink-0">
            <span class="font-bold text-lg text-slate-700 sidebar-text">Admin Fakultas</span>
        </div>
    </div>

    {{-- Navigasi Menu --}}
    <nav class="flex-1 overflow-y-auto py-4">
        <div class="px-4 mb-4">
            <a href="{{ route('admin-fakultas.dashboard') }}"
            class="flex items-center px-3 py-2.5 rounded-lg font-semibold {{ request()->routeIs('admin-fakultas.dashboard') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>
        </div>

        {{-- Dropdown untuk semua menu usulan --}}
        @php
            $usulanMenus = [
                ['route' => route('admin-fakultas.dashboard-jabatan'), 'icon' => 'file-user', 'label' => 'Usulan Jabatan', 'pattern' => 'admin-fakultas.dashboard-jabatan'],
                ['route' => route('admin-fakultas.dashboard-pangkat'), 'icon' => 'trending-up', 'label' => 'Usulan Kepangkatan', 'pattern' => 'admin-fakultas.dashboard-pangkat'],
                ['route' => '#', 'icon' => 'user-check', 'label' => 'Usulan NUPTK', 'pattern' => 'admin-fakultas.usulan-nuptk.*'],
                ['route' => '#', 'icon' => 'file-bar-chart-2', 'label' => 'Usulan Laporan LKD', 'pattern' => 'admin-fakultas.usulan-laporan-lkd.*'],
                ['route' => '#', 'icon' => 'clipboard-check', 'label' => 'Usulan Presensi', 'pattern' => 'admin-fakultas.usulan-presensi.*'],
                ['route' => '#', 'icon' => 'clock', 'label' => 'Usulan Penyesuaian Masa Kerja', 'pattern' => 'admin-fakultas.usulan-penyesuaian-masa-kerja.*'],
                ['route' => '#', 'icon' => 'book-marked', 'label' => 'Usulan Ujian Dinas & Ijazah', 'pattern' => 'admin-fakultas.usulan-ujian-dinas-ijazah.*'],
                ['route' => '#', 'icon' => 'file-check-2', 'label' => 'Usulan Laporan Serdos', 'pattern' => 'admin-fakultas.usulan-laporan-serdos.*'],
                ['route' => '#', 'icon' => 'user-minus', 'label' => 'Usulan Pensiun', 'pattern' => 'admin-fakultas.usulan-pensiun.*'],
                ['route' => '#', 'icon' => 'graduation-cap', 'label' => 'Usulan Pencantuman Gelar', 'pattern' => 'admin-fakultas.usulan-pencantuman-gelar.*'],
                ['route' => '#', 'icon' => 'link', 'label' => 'Usulan ID SINTA ke SISTER', 'pattern' => 'admin-fakultas.usulan-id-sinta-sister.*'],
                ['route' => '#', 'icon' => 'medal', 'label' => 'Usulan Satyalancana', 'pattern' => 'admin-fakultas.usulan-satyalancana.*'],
                ['route' => '#', 'icon' => 'book-open', 'label' => 'Usulan Tugas Belajar', 'pattern' => 'admin-fakultas.usulan-tugas-belajar.*'],
                ['route' => '#', 'icon' => 'user-plus', 'label' => 'Usulan Pengaktifan Kembali', 'pattern' => 'admin-fakultas.usulan-pengaktifan-kembali.*'],
            ];
            $isUsulanActive = collect($usulanMenus)->contains(fn($menu) => request()->routeIs($menu['pattern']));
        @endphp

        <div class="px-4 mb-2" x-data="{ open: {{ $isUsulanActive ? 'true' : 'false' }} }">
            {{-- Tombol Dropdown --}}
            <button id="usulan-dropdown-button" @click="open = !open" class="flex items-center justify-between w-full px-3 py-2.5 rounded-lg {{ $isUsulanActive ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <span class="flex items-center font-semibold">
                    <i data-lucide="file-plus-2" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="sidebar-text">Usulan</span>
                </span>
                <i data-lucide="chevron-down" class="w-5 h-5 transition-transform sidebar-text" :class="{ 'rotate-180': open }"></i>
            </button>

            {{-- Konten Dropdown --}}
            <div x-show="open" x-transition class="mt-2 space-y-1 pl-1">
                @foreach ($usulanMenus as $menu)
                    <a href="{{ $menu['route'] }}"
                       class="flex items-center px-3 py-2 rounded-lg font-semibold text-sm {{ request()->routeIs($menu['pattern']) ? 'bg-slate-100 text-indigo-700 font-semibold' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                        <i data-lucide="{{ $menu['icon'] }}" class="w-4 h-4 mr-3 flex-shrink-0"></i>
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
