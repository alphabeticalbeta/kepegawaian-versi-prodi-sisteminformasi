<aside id="sidebar" class="sidebar w-64 bg-white shadow-lg fixed top-0 left-0 h-full z-30 flex flex-col transition-all duration-300">
    {{-- Header Sidebar --}}
    <div class="flex items-center justify-center p-4 h-16 border-b border-gray-200 flex-shrink-0">
        <div class="flex items-center gap-3 overflow-hidden">
            <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo" class="h-10 flex-shrink-0">
            <span class="font-bold text-lg text-orange-700 sidebar-text">Tim Senat</span>
        </div>
    </div>

    {{-- Navigasi Menu --}}
    <nav class="flex-1 overflow-y-auto py-4">
        {{-- Dashboard --}}
        <div class="px-4 mb-4">
            <a href="{{ route('tim-senat.dashboard') }}"
               class="flex items-center px-3 py-2.5 rounded-lg font-semibold {{ request()->routeIs('tim-senat.dashboard') ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>
        </div>

        {{-- Senat Section --}}
        <div class="px-4 mb-2 mt-6">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 py-2">
                Manajemen Senat
            </div>
        </div>

        <div class="px-4 mb-4">
            <a href="{{ route('tim-senat.rapat-senat.index') }}"
               class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('tim-senat.rapat-senat.*') ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="users" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span class="sidebar-text">Rapat Senat</span>
            </a>
        </div>

        <div class="px-4 mb-6">
            <a href="{{ route('tim-senat.keputusan-senat.index') }}"
               class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('tim-senat.keputusan-senat.*') ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="file-text" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span class="sidebar-text">Keputusan Senat</span>
            </a>
        </div>

        {{-- Usulan Section --}}
        <div class="px-4 mb-2">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 py-2">
                Review Usulan
            </div>
        </div>

        <div class="px-4 mb-4">
            <a href="{{ route('tim-senat.usulan.index') }}"
               class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('tim-senat.usulan.*') ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <i data-lucide="graduation-cap" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span class="sidebar-text">Usulan</span>
            </a>
        </div>
    </nav>
</aside>
