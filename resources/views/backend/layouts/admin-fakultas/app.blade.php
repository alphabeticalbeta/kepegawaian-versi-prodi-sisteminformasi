<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Pegawai') - Kepegawaian UNMUL</title>

    {{-- Memuat CSS dan JS dari Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- CSRF Token untuk AJAX requests --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- External Libraries --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Alpine.js for reactive components --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-100">

    <div class="flex h-screen">

        {{-- Sidebar dipanggil di sini --}}
        @include('backend.components.sidebar-admin-fakultas')

        {{-- Kontainer untuk Header dan Konten Utama --}}
        <div id="main-content" class="flex-1 flex flex-col transition-all duration-300 ml-64">

            {{-- Header dipanggil di sini --}}
            @include('backend.components.header')

            {{-- Konten Dinamis dengan scroll internal --}}
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- ====================================================================== --}}
    {{-- =================== BLOK SCRIPT YANG DIPERBAIKI ====================== --}}
    {{-- ====================================================================== --}}
    <script>

        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();

            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');

            let isSidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';


            const applySidebarState = (collapsed) => {
                // Menargetkan HANYA elemen span dengan kelas .sidebar-text
                const sidebarTexts = sidebar.querySelectorAll('span.sidebar-text');

                // Tambahkan kelas penanda ke body untuk styling via CSS jika perlu
                document.body.classList.toggle('sidebar-collapsed', collapsed);

                if (collapsed) {
                    sidebar.classList.remove('w-64');
                    sidebar.classList.add('w-20');
                    mainContent.classList.remove('ml-64');
                    mainContent.classList.add('ml-20');

                    sidebarTexts.forEach(text => text.classList.add('hidden'));
                } else {
                    sidebar.classList.remove('w-20');
                    sidebar.classList.add('w-64');
                    mainContent.classList.remove('ml-20');
                    mainContent.classList.add('ml-64');

                    sidebarTexts.forEach(text => text.classList.remove('hidden'));
                }
                localStorage.setItem('sidebarCollapsed', collapsed);
            };

            applySidebarState(isSidebarCollapsed);

            window.toggleSidebar = function() {
                isSidebarCollapsed = !isSidebarCollapsed;
                applySidebarState(isSidebarCollapsed);
            }
        });
    </script>

    {{-- Additional scripts from child templates --}}
    @stack('scripts')

</body>
</html>
