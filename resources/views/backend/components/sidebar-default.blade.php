{{--
    Default Sidebar Component
    Menentukan sidebar mana yang akan ditampilkan berdasarkan role user
--}}

@auth('pegawai')
    @php
        $user = Auth::guard('pegawai')->user();
        $roles = $user->roles->pluck('name');
    @endphp

    {{-- Admin Keuangan Sidebar --}}
    @if($roles->contains('Admin Keuangan'))
        @include('backend.components.sidebar-admin-keuangan')

    {{-- Tim Senat Sidebar --}}
    @elseif($roles->contains('Tim Senat'))
        @include('backend.components.sidebar-tim-senat')

    {{-- Kepegawaian Universitas Sidebar --}}
    @elseif($roles->contains('Kepegawaian Universitas'))
        @include('backend.components.sidebar-kepegawaian-universitas')

    {{-- Admin Universitas Sidebar --}}
    @elseif($roles->contains('Admin Universitas'))
        @include('backend.components.sidebar-admin-universitas')

    {{-- Admin Fakultas Sidebar --}}
    @elseif($roles->contains('Admin Fakultas'))
        @include('backend.components.sidebar-admin-fakultas')

    {{-- Penilai Universitas Sidebar --}}
    @elseif($roles->contains('Penilai Universitas'))
        @include('backend.components.sidebar-penilai-universitas')

    {{-- Pegawai Unmul Sidebar (Default) --}}
    @else
        @include('backend.components.sidebar-pegawai-unmul')
    @endif

@else
    {{-- Default fallback sidebar for unauthenticated users --}}
    <aside id="sidebar" class="sidebar w-64 bg-white dark:bg-gray-800 shadow-lg fixed top-0 left-0 h-full z-30 flex flex-col transition-colors">
        <div class="flex items-center justify-center p-4 h-16 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo" class="h-10 flex-shrink-0">
                <span class="font-bold text-lg text-slate-700 dark:text-gray-200 sidebar-text">Kepegawaian Unmul</span>
            </div>
        </div>
        <nav class="flex-1 overflow-y-auto py-4">
            <div class="px-4">
                <div class="text-center text-slate-500 dark:text-gray-400 py-8">
                    Silakan login untuk mengakses menu
                </div>
            </div>
        </nav>
    </aside>
@endauth
