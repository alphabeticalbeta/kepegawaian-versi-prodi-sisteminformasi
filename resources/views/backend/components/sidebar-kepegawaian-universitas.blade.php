<aside id="sidebar" class="sidebar w-64 bg-white shadow-sm fixed top-0 left-0 h-full z-30 flex flex-col">
    {{-- Header Sidebar --}}
    <div class="flex items-center justify-center p-4 h-16 shadow-sm flex-shrink-0">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo" class="h-10 flex-shrink-0">
            <span class="font-bold text-lg text-center sidebar-text">Admin Usulan Kepegawaian</span>
        </div>
    </div>

    {{-- Navigasi Menu --}}
    <nav class="flex-1 overflow-y-auto py-2">

        {{-- Master Data Dropdown --}}
        @php
            $masterMenus = [
                ['route' => 'backend.kepegawaian-universitas.data-pegawai.index', 'icon' => 'users', 'label' => 'Data Pegawai', 'pattern' => 'backend.kepegawaian-universitas.data-pegawai.*'],
                ['route' => 'backend.kepegawaian-universitas.role-pegawai.index', 'icon' => 'user-cog', 'label' => 'Role Pegawai', 'pattern' => 'backend.kepegawaian-universitas.role-pegawai.*'],
                ['route' => 'backend.kepegawaian-universitas.unitkerja.index', 'icon' => 'building-2', 'label' => 'Unit Kerja', 'pattern' => 'backend.kepegawaian-universitas.unitkerja.*'],
                ['route' => 'backend.kepegawaian-universitas.pangkat.index', 'icon' => 'award', 'label' => 'Pangkat', 'pattern' => 'backend.kepegawaian-universitas.pangkat.*'],
                ['route' => 'backend.kepegawaian-universitas.jabatan.index', 'icon' => 'briefcase', 'label' => 'Jabatan', 'pattern' => 'backend.kepegawaian-universitas.jabatan.*'],
            ];
            $isMasterActive = collect($masterMenus)->contains(fn($menu) => request()->routeIs($menu['pattern']));
        @endphp

        <div class="mb-2 px-2">
            <button type="button"
                    class="flex items-center justify-between w-full px-4 py-3 rounded-lg group transition {{ $isMasterActive ? 'bg-gray-100 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
                    aria-controls="dropdown-master"
                    data-collapse-toggle="dropdown-master"
                    aria-expanded="{{ $isMasterActive ? 'true' : 'false' }}">
                <div class="flex items-center">
                    <i data-lucide="database" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="font-medium sidebar-text">Master Data</span>
                </div>
                {{-- PERBAIKAN: Kelas .sidebar-text dihapus dari ikon ini --}}
                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform {{ $isMasterActive ? 'rotate-180' : '' }}"></i>
            </button>
            <div id="dropdown-master" class="dropdown-menu {{ $isMasterActive ? '' : 'hidden' }} space-y-1 pl-4 mt-1">
                @foreach($masterMenus as $menu)
                <div class="relative">
                    <a href="{{ route($menu['route']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg transition {{ request()->routeIs($menu['pattern']) ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                        <i data-lucide="{{ $menu['icon'] }}" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">{{ $menu['label'] }}</span>
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Usulan Dropdown --}}
        @php $isUsulanActive = request()->is('*/usulan/*') || request()->routeIs('backend.kepegawaian-universitas.usulan.*') || request()->routeIs('backend.kepegawaian-universitas.dashboard-periode.*'); @endphp
        <div class="mb-2 px-2">
            <button type="button"
                    class="flex items-center justify-between w-full px-4 py-3 rounded-lg group transition {{ $isUsulanActive ? 'bg-gray-100 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
                    aria-controls="dropdown-usulan"
                    data-collapse-toggle="dropdown-usulan"
                    aria-expanded="{{ $isUsulanActive ? 'true' : 'false' }}">
                <div class="flex items-center">
                    <i data-lucide="file-text" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="font-medium sidebar-text">Usulan</span>
                </div>
                {{-- PERBAIKAN: Kelas .sidebar-text dihapus dari ikon ini --}}
                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform {{ $isUsulanActive ? 'rotate-180' : '' }}"></i>
            </button>
                        <div id="dropdown-usulan" class="dropdown-menu {{ $isUsulanActive ? '' : 'hidden' }} space-y-1 pl-4 mt-1">
                <div class="relative">
                    <a href="{{ route('backend.kepegawaian-universitas.dashboard-periode.index', ['jenis' => 'nuptk']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->get('jenis') == 'nuptk' ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                        <i data-lucide="user-check" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan NUPTK</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="{{ route('backend.kepegawaian-universitas.dashboard-periode.index', ['jenis' => 'laporan-lkd']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->get('jenis') == 'laporan-lkd' ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                        <i data-lucide="file-bar-chart-2" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Laporan LKD</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="{{ route('backend.kepegawaian-universitas.dashboard-periode.index', ['jenis' => 'presensi']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->get('jenis') == 'presensi' ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                        <i data-lucide="clipboard-check" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Presensi</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="{{ route('backend.kepegawaian-universitas.dashboard-periode.index', ['jenis' => 'penyesuaian-masa-kerja']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->get('jenis') == 'penyesuaian-masa-kerja' ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                        <i data-lucide="clock" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Penyesuaian Masa Kerja</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="{{ route('backend.kepegawaian-universitas.dashboard-periode.index', ['jenis' => 'ujian-dinas-ijazah']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->get('jenis') == 'ujian-dinas-ijazah' ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                        <i data-lucide="book-marked" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Ujian Dinas & Ijazah</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="{{ route('backend.kepegawaian-universitas.dashboard-periode.index', ['jenis' => 'jabatan']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->get('jenis') == 'jabatan' || (!request()->has('jenis') && request()->routeIs('backend.kepegawaian-universitas.dashboard-periode.*')) ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                        <i data-lucide="file-user" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Jabatan</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="{{ route('backend.kepegawaian-universitas.dashboard-periode.index', ['jenis' => 'laporan-serdos']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->get('jenis') == 'laporan-serdos' ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                        <i data-lucide="file-check-2" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Laporan Serdos</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="{{ route('backend.kepegawaian-universitas.dashboard-periode.index', ['jenis' => 'pensiun']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->get('jenis') == 'pensiun' ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                        <i data-lucide="user-minus" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Pensiun</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="{{ route('backend.kepegawaian-universitas.dashboard-periode.index', ['jenis' => 'kepangkatan']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->get('jenis') == 'kepangkatan' ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                        <i data-lucide="trending-up" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Kepangkatan</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="{{ route('backend.kepegawaian-universitas.dashboard-periode.index', ['jenis' => 'pencantuman-gelar']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->get('jenis') == 'pencantuman-gelar' ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                        <i data-lucide="graduation-cap" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Pencantuman Gelar</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="{{ route('backend.kepegawaian-universitas.dashboard-periode.index', ['jenis' => 'id-sinta-sister']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->get('jenis') == 'id-sinta-sister' ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                        <i data-lucide="link" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan ID SINTA ke SISTER</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="{{ route('backend.kepegawaian-universitas.dashboard-periode.index', ['jenis' => 'satyalancana']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->get('jenis') == 'satyalancana' ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                        <i data-lucide="medal" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Satyalancana</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="{{ route('backend.kepegawaian-universitas.dashboard-periode.index', ['jenis' => 'tugas-belajar']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->get('jenis') == 'tugas-belajar' ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                        <i data-lucide="book-open" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Tugas Belajar</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="{{ route('backend.kepegawaian-universitas.dashboard-periode.index', ['jenis' => 'pengaktifan-kembali']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->get('jenis') == 'pengaktifan-kembali' ? 'bg-blue-100 text-blue-700 font-semibold' : '' }}">
                        <i data-lucide="user-plus" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Pengaktifan Kembali</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
</aside>
