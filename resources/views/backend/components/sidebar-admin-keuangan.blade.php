<aside id="sidebar" class="sidebar w-64 bg-white shadow-lg fixed top-0 left-0 h-full z-30 flex flex-col transition-all duration-300">
    {{-- Header Sidebar --}}
    <div class="flex items-center justify-center p-4 h-16 border-b border-gray-200 flex-shrink-0">
        <div class="flex items-center gap-3 overflow-hidden">
            <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo" class="h-10 flex-shrink-0">
            <span class="font-bold text-lg text-amber-700 sidebar-text">Admin Keuangan</span>
        </div>
    </div>

    {{-- Navigasi Menu --}}
    <nav class="flex-1 overflow-y-auto py-4">
        {{-- Dashboard --}}
        <div class="px-4 mb-4">
            <a href="{{ route('admin-keuangan.dashboard') }}"
               class="flex items-center px-3 py-2.5 rounded-lg font-semibold {{ request()->routeIs('admin-keuangan.dashboard') ? 'bg-amber-50 text-amber-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>
        </div>

        {{-- Laporan Section --}}
        <div class="px-4 mb-2 mt-6">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 py-2">
                Laporan Keuangan
            </div>
        </div>

        <div class="px-4 mb-4">
            <a href="{{ route('admin-keuangan.laporan-keuangan.index') }}"
               class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin-keuangan.laporan-keuangan.*') ? 'bg-amber-50 text-amber-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="bar-chart-3" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span class="sidebar-text">Laporan Keuangan</span>
            </a>
        </div>

        <div class="px-4 mb-6">
            <a href="{{ route('admin-keuangan.verifikasi-dokumen.index') }}"
               class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin-keuangan.verifikasi-dokumen.*') ? 'bg-amber-50 text-amber-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="file-check" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span class="sidebar-text">Verifikasi Dokumen</span>
            </a>
        </div>

        {{-- SK Documents Section --}}
        <div class="px-4 mb-2">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 py-2">
                Surat Keputusan
            </div>
        </div>

        {{-- SK Pangkat --}}
        <div class="px-4 mb-1">
            <a href="{{ route('admin-keuangan.sk-pangkat.index') }}"
               class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin-keuangan.sk-pangkat.*') ? 'bg-amber-50 text-amber-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="award" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span class="sidebar-text">SK Pangkat</span>
            </a>
        </div>

        {{-- SK Jabatan --}}
        <div class="px-4 mb-1">
            <a href="{{ route('admin-keuangan.sk-jabatan.index') }}"
               class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin-keuangan.sk-jabatan.*') ? 'bg-amber-50 text-amber-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="briefcase" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span class="sidebar-text">SK Jabatan</span>
            </a>
        </div>

        {{-- SK Berkala --}}
        <div class="px-4 mb-1">
            <a href="{{ route('admin-keuangan.sk-berkala.index') }}"
               class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin-keuangan.sk-berkala.*') ? 'bg-amber-50 text-amber-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="calendar-days" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span class="sidebar-text">SK Berkala</span>
            </a>
        </div>

        {{-- Model D --}}
        <div class="px-4 mb-1">
            <a href="{{ route('admin-keuangan.model-d.index') }}"
               class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin-keuangan.model-d.*') ? 'bg-amber-50 text-amber-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="file-text" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span class="sidebar-text">Model D</span>
            </a>
        </div>

        {{-- SK CPNS (80 %) --}}
        <div class="px-4 mb-1">
            <a href="{{ route('admin-keuangan.sk-cpns.index') }}"
               class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin-keuangan.sk-cpns.*') ? 'bg-amber-50 text-amber-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="user-plus" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span class="sidebar-text">SK CPNS (80%)</span>
            </a>
        </div>

        {{-- SK PNS (100 %) --}}
        <div class="px-4 mb-1">
            <a href="{{ route('admin-keuangan.sk-pns.index') }}"
               class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin-keuangan.sk-pns.*') ? 'bg-amber-50 text-amber-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="user-check" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span class="sidebar-text">SK PNS (100%)</span>
            </a>
        </div>

        {{-- SK PPPK --}}
        <div class="px-4 mb-1">
            <a href="{{ route('admin-keuangan.sk-pppk.index') }}"
               class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin-keuangan.sk-pppk.*') ? 'bg-amber-50 text-amber-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="user-cog" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span class="sidebar-text">SK PPPK</span>
            </a>
        </div>

        {{-- SK Mutasi --}}
        <div class="px-4 mb-1">
            <a href="{{ route('admin-keuangan.sk-mutasi.index') }}"
               class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin-keuangan.sk-mutasi.*') ? 'bg-amber-50 text-amber-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="move" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span class="sidebar-text">SK Mutasi</span>
            </a>
        </div>

        {{-- SK Pensiun --}}
        <div class="px-4 mb-1">
            <a href="{{ route('admin-keuangan.sk-pensiun.index') }}"
               class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin-keuangan.sk-pensiun.*') ? 'bg-amber-50 text-amber-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="user-x" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span class="sidebar-text">SK Pensiun</span>
            </a>
        </div>

        {{-- SK Pembayaran Tunjangan Sertifikasi Dosen --}}
        <div class="px-4 mb-1">
            <a href="{{ route('admin-keuangan.sk-tunjangan-sertifikasi.index') }}"
               class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin-keuangan.sk-tunjangan-sertifikasi.*') ? 'bg-amber-50 text-amber-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="banknote" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span class="sidebar-text text-sm">SK Tunjangan Sertifikasi</span>
            </a>
        </div>

        {{-- SKPP --}}
        <div class="px-4 mb-1">
            <a href="{{ route('admin-keuangan.skpp.index') }}"
               class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin-keuangan.skpp.*') ? 'bg-amber-50 text-amber-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="shield" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span class="sidebar-text">SKPP</span>
            </a>
        </div>

        {{-- SK Pemberhentian (Meninggal Dunia) --}}
        <div class="px-4 mb-1">
            <a href="{{ route('admin-keuangan.sk-pemberhentian-meninggal.index') }}"
               class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin-keuangan.sk-pemberhentian-meninggal.*') ? 'bg-amber-50 text-amber-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="user-minus" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span class="sidebar-text text-sm">SK Pemberhentian (Meninggal)</span>
            </a>
        </div>

        {{-- SK Pengaktifan Kembali --}}
        <div class="px-4 mb-1">
            <a href="{{ route('admin-keuangan.sk-pengaktifan-kembali.index') }}"
               class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin-keuangan.sk-pengaktifan-kembali.*') ? 'bg-amber-50 text-amber-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="rotate-ccw" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span class="sidebar-text text-sm">SK Pengaktifan Kembali</span>
            </a>
        </div>

        {{-- SK Tugas Belajar --}}
        <div class="px-4 mb-1">
            <a href="{{ route('admin-keuangan.sk-tugas-belajar.index') }}"
               class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin-keuangan.sk-tugas-belajar.*') ? 'bg-amber-50 text-amber-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="graduation-cap" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span class="sidebar-text">SK Tugas Belajar</span>
            </a>
        </div>

        {{-- SK Pemberhentian Sementara --}}
        <div class="px-4 mb-1">
            <a href="{{ route('admin-keuangan.sk-pemberhentian-sementara.index') }}"
               class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin-keuangan.sk-pemberhentian-sementara.*') ? 'bg-amber-50 text-amber-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="pause" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span class="sidebar-text text-sm">SK Pemberhentian Sementara</span>
            </a>
        </div>

        {{-- SK Penyesuaian Masa Kerja --}}
        <div class="px-4 mb-4">
            <a href="{{ route('admin-keuangan.sk-penyesuaian-masa-kerja.index') }}"
               class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin-keuangan.sk-penyesuaian-masa-kerja.*') ? 'bg-amber-50 text-amber-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="clock" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span class="sidebar-text text-sm">SK Penyesuaian Masa Kerja</span>
            </a>
        </div>
    </nav>
</aside>
