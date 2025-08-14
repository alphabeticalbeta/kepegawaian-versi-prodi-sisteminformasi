<aside id="sidebar" class="w-64 bg-white shadow-sm fixed top-0 left-0 h-full z-30 group">
   {{-- Header Sidebar --}}
   <div class="flex items-center justify-center p-4 h-16 shadow">
       <div class="flex items-center gap-3 justify-center">
           <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo" class="h-10 flex-shrink-0">
           <span class="font-bold text-lg sidebar-text">Admin Fakultas</span>
       </div>
   </div>

    {{-- Navigasi Menu --}}
    <nav class="py-4 overflow-y-auto h-[calc(100vh-128px)]"> {{-- Dibuat bisa di-scroll jika menu terlalu panjang --}}
        <div class="space-y-1">

            <div class="px-4 mb-4">
                {{-- [PERBAIKAN] Menggunakan nama rute yang benar --}}
                <a href="{{ route('admin-fakultas.dashboard') }}"
                class="flex items-center px-3 py-2.5 rounded-lg font-semibold {{ request()->routeIs('admin-fakultas.dashboard') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-100' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            </div>

            <!-- Usulan Section -->
            <div class="px-4 mb-2 mt-4">
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 py-2">
                    Usulan
                </div>
            </div>

            <div class="px-4 relative">
                <a href="{{ route('admin-fakultas.usulan.jabatan') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin-fakultas.usulan.jabatan') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i data-lucide="briefcase" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="font-medium sidebar-text">Jabatan</span>
                </a>
            </div>

            <div class="px-4 relative">
                <a href="{{ route('admin-fakultas.usulan.pangkat') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('admin-fakultas.usulan.pangkat') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i data-lucide="award" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="font-medium sidebar-text">Pangkat</span>
                </a>
            </div>
        </div>
    </nav>
</aside>
