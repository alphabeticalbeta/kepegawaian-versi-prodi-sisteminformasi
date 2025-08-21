<aside id="sidebar" class="sidebar w-64 bg-white shadow-sm fixed top-0 left-0 h-full z-30 group">
    {{-- Header Sidebar --}}
    <div class="flex items-center justify-center p-4 h-16 shadow">
        <div class="flex items-center gap-3 justify-center">
            <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo" class="h-10 flex-shrink-0">
            <span class="font-bold text-lg sidebar-text text-center">Penilai Universitas</span>
        </div>
    </div>

    {{-- Navigasi Menu --}}
    <nav class="py-4 overflow-y-auto h-[calc(100vh-128px)]"> {{-- Dibuat bisa di-scroll jika menu terlalu panjang --}}
        <div class="space-y-1">

            <div class="px-4 relative">
                <a href="{{ route('penilai-universitas.dashboard') }}" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                    <i data-lucide="home" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="font-medium sidebar-text">Dashboard</span>
                </a>
            </div>

            <div class="px-4 relative">
                <a href="{{ route('penilai-universitas.pusat-usulan.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                    <i data-lucide="file-user" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="font-medium sidebar-text">Usulan Jabatan</span>
                </a>
            </div>
        </div>
    </nav>
</aside>
